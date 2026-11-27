<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartnerPayoutRequest;
use App\Http\Requests\PartnerRequest;
use App\Http\Requests\PartnerShareRequest;
use App\Models\AuditLog;
use App\Models\Firm;
use App\Models\Partner;
use App\Models\PartnerPayout;
use App\Models\PartnerShare;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Services\PartnerProfitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerController extends Controller
{
    public function __construct(private PartnerProfitService $profitService)
    {
    }

    private function ensureAdmin(): void
    {
        if (session('login_type') === 'firm' || session()->has('firm_id')) {
            abort(403, 'Access Denied. Partners & Profit Sharing is restricted to Administrators only.');
        }

        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403, 'Access Denied. Administrators only.');
        }
    }

    private function scopeDropdowns(): array
    {
        return [
            'firms'           => Firm::where('status', 'active')->orderBy('firm_name')->get(),
            'projects'        => Project::with('firm')->orderBy('project_name')->get(),
            'propertyMasters' => PropertyMaster::with('firm')->orderBy('property_name')->get(),
            'properties'      => Property::naturalSort(Property::with(['firm', 'project', 'propertyMaster'])->orderBy('property_name')->get()),
            'paymentModes'    => PaymentMode::where('status', 'active')->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = Partner::with([
            'firm',
            'shares.firm',
            'shares.project',
            'shares.propertyMaster',
            'shares.property',
            'payouts',
        ]);

        if ($request->filled('firm_id')) {
            $fid = (int) $request->firm_id;
            $query->where(function ($q) use ($fid) {
                $q->where('firm_id', $fid)
                  ->orWhereHas('shares', fn ($sq) => $sq->where('firm_id', $fid));
            });
        }

        if ($request->filled('partner_type')) {
            if ($request->partner_type === 'silent') {
                $query->where('is_silent', true);
            } elseif ($request->partner_type === 'on_paper') {
                $query->where('is_silent', false);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%");
            });
        }

        $partners = $query->latest('id')->paginate(15)->withQueryString();

        // Compute summary totals across displayed/all active shares
        $allShares = PartnerShare::with(['firm', 'project', 'propertyMaster', 'property'])
            ->where('status', 'active')
            ->get();
        $shareMetrics = $this->profitService->calculateForShares($allShares);

        $partnerSummaries = [];
        foreach ($partners as $partner) {
            $entitled = 0.0;
            $bookedEntitled = 0.0;
            foreach ($partner->shares->where('status', 'active') as $share) {
                $m = $shareMetrics[$share->id] ?? $this->profitService->calculateForShare($share);
                $entitled += $m['realized_entitled'];
                $bookedEntitled += $m['booked_entitled'];
            }
            $paid = (float) $partner->payouts->sum('amount');
            $partnerSummaries[$partner->id] = [
                'realized_entitled' => round($entitled, 2),
                'booked_entitled'   => round($bookedEntitled, 2),
                'paid_amount'       => round($paid, 2),
                'balance_due'       => round(max(0.0, $entitled - $paid), 2),
                'overdrawn_amount'  => round(max(0.0, $paid - $entitled), 2),
            ];
        }

        $totalPartners      = Partner::count();
        $silentPartners     = Partner::where('is_silent', true)->count();
        $totalEntitled      = round(array_sum(array_column($shareMetrics, 'realized_entitled')), 2);
        $totalPaidOut       = round((float) PartnerPayout::sum('amount'), 2);
        $totalBalanceDue    = round(max(0.0, $totalEntitled - $totalPaidOut), 2);

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.partners.index', compact(
            'partners',
            'partnerSummaries',
            'firms',
            'totalPartners',
            'silentPartners',
            'totalEntitled',
            'totalPaidOut',
            'totalBalanceDue'
        ));
    }

    public function create()
    {
        $this->ensureAdmin();

        return view('admin.partners.create', $this->scopeDropdowns());
    }

    public function store(PartnerRequest $request)
    {
        $this->ensureAdmin();

        $validated = $request->validated();

        // Validate optional initial share if percentage was provided
        $hasInitialShare = $request->filled('initial_share_percentage') && $request->filled('initial_scope_type');
        if ($hasInitialShare) {
            [$resolvedFirmId, $projId, $pmId, $propId] = $this->resolveShareTarget(
                $request->initial_scope_type,
                $request->initial_firm_id ?: ($request->firm_id ?: null),
                $request->initial_project_id,
                $request->initial_property_master_id,
                $request->initial_property_id
            );

            if (!$resolvedFirmId) {
                return back()->withInput()->with('error', 'Please select a Firm or target record for the initial profit share.');
            }

            $existingPct = $this->profitService->allocatedPercentageForTarget(
                $request->initial_scope_type,
                $resolvedFirmId,
                $projId,
                $pmId,
                $propId
            );
            $newPct = (float) $request->initial_share_percentage;
            if ($existingPct + $newPct > 100.001) {
                return back()->withInput()->with(
                    'error',
                    "Total share percentage for this target cannot exceed 100%. Already allocated: {$existingPct}%."
                );
            }
        }

        $partner = DB::transaction(function () use ($request, $validated, $hasInitialShare) {
            $partner = Partner::create([
                'firm_id'    => $validated['firm_id'] ?? null,
                'name'       => $validated['name'],
                'mobile'     => $validated['mobile'] ?? null,
                'email'      => $validated['email'] ?? null,
                'is_silent'  => $request->boolean('is_silent', true),
                'status'     => $validated['status'],
                'notes'      => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            if ($hasInitialShare) {
                [$resolvedFirmId, $projId, $pmId, $propId] = $this->resolveShareTarget(
                    $request->initial_scope_type,
                    $request->initial_firm_id ?: ($request->firm_id ?: null),
                    $request->initial_project_id,
                    $request->initial_property_master_id,
                    $request->initial_property_id
                );

                PartnerShare::create([
                    'firm_id'            => $resolvedFirmId,
                    'partner_id'         => $partner->id,
                    'scope_type'         => $request->initial_scope_type,
                    'project_id'         => $projId,
                    'property_master_id' => $pmId,
                    'property_id'        => $propId,
                    'share_percentage'   => (float) $request->initial_share_percentage,
                    'effective_from'     => $request->initial_effective_from ?: null,
                    'status'             => 'active',
                    'notes'              => $request->initial_share_notes ?: null,
                    'created_by'         => Auth::id(),
                ]);
            }

            return $partner;
        });

        AuditLog::log('Partner Profit Sharing', 'Create Record', "Created partner '{$partner->name}'" . ($partner->is_silent ? ' (Silent Partner)' : ''));

        return redirect()->route('partners.show', $partner->id)
            ->with('success', "Partner '{$partner->name}' created successfully.");
    }

    public function show(Partner $partner)
    {
        $this->ensureAdmin();

        $partner->load([
            'firm',
            'shares.firm',
            'shares.project',
            'shares.propertyMaster',
            'shares.property',
            'payouts.share.firm',
            'payouts.share.project',
            'payouts.share.propertyMaster',
            'payouts.share.property',
            'payouts.paymentMode',
            'payouts.creator',
        ]);

        $shareMetrics = $this->profitService->calculateForShares($partner->shares);

        $totalRealizedEntitled = round(array_sum(array_column($shareMetrics, 'realized_entitled')), 2);
        $totalBookedEntitled   = round(array_sum(array_column($shareMetrics, 'booked_entitled')), 2);
        $totalPaid             = round((float) $partner->payouts->sum('amount'), 2);
        $totalBalanceDue       = round(max(0.0, $totalRealizedEntitled - $totalPaid), 2);
        $totalOverdrawn        = round(max(0.0, $totalPaid - $totalRealizedEntitled), 2);

        // Unique pool totals across this partner's active share targets (e.g. Inflow 2 Cr - All Partners Paid 60L = 1.40 Cr Pending)
        $seenTargets = [];
        $totalPoolAvailable = 0.0;
        $totalPoolPaidAll   = 0.0;
        foreach ($partner->shares->where('status', 'active') as $sh) {
            $tKey = implode(':', [$sh->firm_id, $sh->scope_type, $sh->project_id, $sh->property_master_id, $sh->property_id]);
            if (isset($seenTargets[$tKey])) {
                continue;
            }
            $seenTargets[$tKey] = true;
            $m = $shareMetrics[$sh->id] ?? [];
            $totalPoolAvailable += (float) ($m['realized_profit'] ?? 0);
            $totalPoolPaidAll   += (float) ($m['pool_paid_all'] ?? 0);
        }
        $totalPoolPending = round($totalPoolAvailable - $totalPoolPaidAll, 2);

        return view('admin.partners.show', array_merge(
            compact(
                'partner',
                'shareMetrics',
                'totalRealizedEntitled',
                'totalBookedEntitled',
                'totalPaid',
                'totalBalanceDue',
                'totalOverdrawn',
                'totalPoolAvailable',
                'totalPoolPaidAll',
                'totalPoolPending'
            ),
            $this->scopeDropdowns()
        ));
    }

    public function edit(Partner $partner)
    {
        $this->ensureAdmin();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        return view('admin.partners.edit', compact('partner', 'firms'));
    }

    public function update(PartnerRequest $request, Partner $partner)
    {
        $this->ensureAdmin();

        $validated = $request->validated();
        $partner->update([
            'firm_id'   => $validated['firm_id'] ?? null,
            'name'      => $validated['name'],
            'mobile'    => $validated['mobile'] ?? null,
            'email'     => $validated['email'] ?? null,
            'is_silent' => $request->boolean('is_silent'),
            'status'    => $validated['status'],
            'notes'     => $validated['notes'] ?? null,
        ]);

        AuditLog::log('Partner Profit Sharing', 'Update Record', "Updated partner '{$partner->name}' (ID: {$partner->id})");

        return redirect()->route('partners.show', $partner->id)
            ->with('success', 'Partner details updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $this->ensureAdmin();

        $name = $partner->name;
        $partner->delete();

        AuditLog::log('Partner Profit Sharing', 'Delete Record', "Deleted partner '{$name}'");

        return redirect()->route('partners.index')
            ->with('success', "Partner '{$name}' deleted successfully.");
    }

    public function storeShare(PartnerShareRequest $request, Partner $partner)
    {
        $this->ensureAdmin();

        [$firmId, $projId, $pmId, $propId] = $this->resolveShareTarget(
            $request->scope_type,
            (int) $request->firm_id,
            $request->project_id ? (int) $request->project_id : null,
            $request->property_master_id ? (int) $request->property_master_id : null,
            $request->property_id ? (int) $request->property_id : null
        );

        $status = $request->input('status', 'active');
        $newPct = (float) $request->share_percentage;

        if ($status === 'active') {
            $allocated = $this->profitService->allocatedPercentageForTarget(
                $request->scope_type,
                $firmId,
                $projId,
                $pmId,
                $propId
            );
            if ($allocated + $newPct > 100.001) {
                return back()->withInput()->with(
                    'error',
                    "Cannot assign {$newPct}%. Already allocated {$allocated}% on this target (max 100%)."
                );
            }
        }

        $share = PartnerShare::create([
            'firm_id'            => $firmId,
            'partner_id'         => $partner->id,
            'scope_type'         => $request->scope_type,
            'project_id'         => $projId,
            'property_master_id' => $pmId,
            'property_id'        => $propId,
            'share_percentage'   => $newPct,
            'effective_from'     => $request->effective_from ?: null,
            'status'             => $status,
            'notes'              => $request->notes ?: null,
            'created_by'         => Auth::id(),
        ]);

        AuditLog::log('Partner Profit Sharing', 'Create Record', "Assigned {$newPct}% ({$share->scope_label}: {$share->target_name}) to '{$partner->name}'");

        return redirect()->route('partners.show', $partner->id)
            ->with('success', "Profit share of {$newPct}% added successfully.");
    }

    public function updateShare(PartnerShareRequest $request, Partner $partner, PartnerShare $share)
    {
        $this->ensureAdmin();
        if ($share->partner_id !== $partner->id) {
            abort(404);
        }

        [$firmId, $projId, $pmId, $propId] = $this->resolveShareTarget(
            $request->scope_type,
            (int) $request->firm_id,
            $request->project_id ? (int) $request->project_id : null,
            $request->property_master_id ? (int) $request->property_master_id : null,
            $request->property_id ? (int) $request->property_id : null
        );

        $status = $request->input('status', $share->status);
        $newPct = (float) $request->share_percentage;

        if ($status === 'active') {
            $allocated = $this->profitService->allocatedPercentageForTarget(
                $request->scope_type,
                $firmId,
                $projId,
                $pmId,
                $propId,
                $share->id
            );
            if ($allocated + $newPct > 100.001) {
                return back()->withInput()->with(
                    'error',
                    "Cannot update to {$newPct}%. Other partners already hold {$allocated}% on this target."
                );
            }
        }

        $share->update([
            'firm_id'            => $firmId,
            'scope_type'         => $request->scope_type,
            'project_id'         => $projId,
            'property_master_id' => $pmId,
            'property_id'        => $propId,
            'share_percentage'   => $newPct,
            'effective_from'     => $request->effective_from ?: null,
            'status'             => $status,
            'notes'              => $request->notes ?: null,
        ]);

        AuditLog::log('Partner Profit Sharing', 'Update Record', "Updated share #{$share->id} to {$newPct}% for '{$partner->name}'");

        return redirect()->route('partners.show', $partner->id)
            ->with('success', 'Profit share rule updated successfully.');
    }

    public function destroyShare(Partner $partner, PartnerShare $share)
    {
        $this->ensureAdmin();
        if ($share->partner_id !== $partner->id) {
            abort(404);
        }

        $share->delete();
        AuditLog::log('Partner Profit Sharing', 'Delete Record', "Removed profit share #{$share->id} for '{$partner->name}'");

        return redirect()->route('partners.show', $partner->id)
            ->with('success', 'Profit share allocation deleted.');
    }

    public function storePayout(PartnerPayoutRequest $request, Partner $partner)
    {
        $this->ensureAdmin();

        $share = PartnerShare::where('id', $request->partner_share_id)
            ->where('partner_id', $partner->id)
            ->firstOrFail();

        $modeName = $request->payment_mode;
        if ($request->filled('payment_mode_id')) {
            $pm = PaymentMode::find($request->payment_mode_id);
            if ($pm) {
                $modeName = $pm->name;
            }
        }

        $payout = PartnerPayout::create([
            'firm_id'          => $share->firm_id,
            'partner_id'       => $partner->id,
            'partner_share_id' => $share->id,
            'payout_date'      => $request->payout_date,
            'amount'           => (float) $request->amount,
            'payment_mode_id'  => $request->payment_mode_id ?: null,
            'payment_mode'     => $modeName ?: 'Cash',
            'reference_no'     => $request->reference_no ?: null,
            'remarks'          => $request->remarks ?: null,
            'created_by'       => Auth::id(),
        ]);

        AuditLog::log(
            'Partner Profit Sharing',
            'Payout',
            "Paid ₹" . number_format($payout->amount, 2) . " profit share to '{$partner->name}' ({$share->scope_label}: {$share->target_name})"
        );

        return redirect()->route('partners.show', $partner->id)
            ->with('success', 'Profit payout of ₹' . number_format($payout->amount, 2) . ' recorded successfully.');
    }

    public function destroyPayout(Partner $partner, PartnerPayout $payout)
    {
        $this->ensureAdmin();
        if ($payout->partner_id !== $partner->id) {
            abort(404);
        }

        $amt = $payout->amount;
        $payout->delete();

        AuditLog::log('Partner Profit Sharing', 'Delete Record', "Deleted profit payout of ₹" . number_format($amt, 2) . " for '{$partner->name}'");

        return redirect()->route('partners.show', $partner->id)
            ->with('success', 'Profit payout deleted successfully.');
    }

    public function payoutsIndex(Request $request)
    {
        $this->ensureAdmin();

        $query = PartnerPayout::with([
            'firm',
            'partner',
            'share.project',
            'share.propertyMaster',
            'share.property',
            'paymentMode',
        ]);

        if ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('payout_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payout_date', '<=', $request->to_date);
        }

        $totalPayoutAmount = (float) (clone $query)->sum('amount');
        $payouts = $query->latest('payout_date')->latest('id')->paginate(20)->withQueryString();

        $firms    = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $partners = Partner::orderBy('name')->get();

        return view('admin.partners.payouts', compact('payouts', 'totalPayoutAmount', 'firms', 'partners'));
    }

    public function downloadPdf(Partner $partner)
    {
        $this->ensureAdmin();

        $partner->load([
            'firm',
            'shares.firm',
            'shares.project',
            'shares.propertyMaster',
            'shares.property',
            'payouts.share.firm',
            'payouts.share.project',
            'payouts.share.propertyMaster',
            'payouts.share.property',
        ]);

        $shareMetrics = $this->profitService->calculateForShares($partner->shares);
        $totalRealizedEntitled = round(array_sum(array_column($shareMetrics, 'realized_entitled')), 2);
        $totalBookedEntitled   = round(array_sum(array_column($shareMetrics, 'booked_entitled')), 2);
        $totalPaid             = round((float) $partner->payouts->sum('amount'), 2);
        $totalBalanceDue       = round(max(0.0, $totalRealizedEntitled - $totalPaid), 2);

        return view('admin.partners.show-pdf', compact(
            'partner',
            'shareMetrics',
            'totalRealizedEntitled',
            'totalBookedEntitled',
            'totalPaid',
            'totalBalanceDue'
        ));
    }

    public function payoutReceiptPdf(Partner $partner, PartnerPayout $payout)
    {
        $this->ensureAdmin();
        if ($payout->partner_id !== $partner->id) {
            abort(404);
        }

        $payout->load(['firm', 'partner', 'share.project', 'share.propertyMaster', 'share.property', 'paymentMode']);
        $metrics = $payout->share ? $this->profitService->calculateForShare($payout->share) : null;

        return view('admin.partners.payout-receipt-pdf', compact('partner', 'payout', 'metrics'));
    }

    private function resolveShareTarget(
        string $scopeType,
        ?int $firmId,
        ?int $projectId,
        ?int $propertyMasterId,
        ?int $propertyId
    ): array {
        if ($scopeType === PartnerShare::SCOPE_PROJECT && $projectId) {
            $project = Project::find($projectId);
            return [$project?->firm_id ?? $firmId, $projectId, null, null];
        }
        if ($scopeType === PartnerShare::SCOPE_PROPERTY_MASTER && $propertyMasterId) {
            $pm = PropertyMaster::find($propertyMasterId);
            return [$pm?->firm_id ?? $firmId, null, $propertyMasterId, null];
        }
        if ($scopeType === PartnerShare::SCOPE_PROPERTY && $propertyId) {
            $prop = Property::find($propertyId);
            return [$prop?->firm_id ?? $firmId, null, null, $propertyId];
        }

        return [$firmId, null, null, null];
    }
}
