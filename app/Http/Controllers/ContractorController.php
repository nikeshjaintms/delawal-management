<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContractorRequest;
use App\Models\Contractor;
use App\Models\ContractorPayment;
use App\Models\Firm;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ContractorController extends Controller
{
    private function authorise(Contractor $contractor): void
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $contractor->firm_id != $firmId) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::with(['project', 'projects', 'properties', 'firm', 'firms']);

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('project_id')) {
            $pId = $request->project_id;
            $query->where(function ($q) use ($pId) {
                $q->where('project_id', $pId)
                  ->orWhereHas('projects', function ($pq) use ($pId) {
                      $pq->where('projects.id', $pId);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('contractor_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('aadhar_no', 'like', "%{$s}%")
                  ->orWhere('pan_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhereHas('projects', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('properties', function ($pq) use ($s) {
                      $pq->where('property_name', 'like', "%{$s}%")
                         ->orWhere('property_code', 'like', "%{$s}%")
                         ->orWhere('unit_no', 'like', "%{$s}%");
                  });
            });
        }

        $totalContract = (float) (clone $query)->sum('contract_amount');
        $totalPaid = (float) (clone $query)->sum('paid_amount');
        $totalDue = (float) (clone $query)->sum('due_amount');
        $totalContractors = (clone $query)->count();

        $contractors = $query->with('payments.paymentMode')->latest()->paginate(15)->withQueryString();

        $projectsQuery = Project::orderBy('project_name');
        if (!$isAdmin) {
            $projectsQuery->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $projectsQuery->where('firm_id', $request->firm_id);
        }
        $projects = $projectsQuery->get();

        $firms = $isAdmin ? Firm::where('status', 'active')->orderBy('firm_name')->get() : collect();

        return view('admin.contractors.index', compact(
            'contractors',
            'projects',
            'firms',
            'totalContract',
            'totalPaid',
            'totalDue',
            'totalContractors'
        ));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if ($isAdmin) {
            $projects = Project::with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$firmId])->with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = collect();
        }

        $selectedProjectId = $request->project_id;

        return view('admin.contractors.create', compact('projects', 'firms', 'selectedProjectId'));
    }

    public function store(ContractorRequest $request)
    {
        $user = Auth::user();
        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $primaryFirmId = reset($firmIds);

        $projectIds = (array) $request->project_ids;
        if (empty($projectIds) && $request->filled('project_id')) {
            $projectIds = [(int)$request->project_id];
        }
        $primaryProjectId = reset($projectIds) ?: null;

        $contractAmount = $request->filled('contract_amount') ? floatval($request->contract_amount) : 0.00;

        $contractor = Contractor::create([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $primaryProjectId,
            'contractor_name' => $request->contractor_name,
            'mobile'          => $request->mobile,
            'aadhar_no'       => $request->aadhar_no,
            'pan_no'          => $request->pan_no,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'ifsc_code'       => $request->ifsc_code,
            'branch_name'     => $request->branch_name,
            'address'         => $request->address,
            'contract_amount' => $contractAmount,
            'paid_amount'     => 0.00,
            'due_amount'      => $contractAmount,
            'payment_status'  => 'unpaid',
            'work_type'       => $request->work_type,
            'contract_date'   => $request->contract_date,
            'contract_notes'  => $request->contract_notes,
            'status'          => $request->status,
            'created_by'      => Auth::id(),
            'updated_by'      => Auth::id(),
        ]);

        $contractor->syncFirms($firmIds);
        $contractor->syncProjects($projectIds);

        $propertyIds = (array) $request->property_ids;
        $contractor->syncProperties($propertyIds);

        // Record initial payment / advance if entered during creation
        if ($request->filled('initial_payment_amount') && floatval($request->initial_payment_amount) > 0) {
            $initialAmt = floatval($request->initial_payment_amount);
            $payMode = $request->initial_payment_mode ?: 'Cash';
            $pmId = PaymentMode::where('name', $payMode)->value('id');

            ContractorPayment::create([
                'contractor_id'   => $contractor->id,
                'firm_id'         => $primaryFirmId,
                'project_id'      => $primaryProjectId,
                'property_id'     => !empty($propertyIds) ? $propertyIds[0] : null,
                'payment_mode_id' => $pmId,
                'amount'          => $initialAmt,
                'payment_date'    => $request->contract_date ?: date('Y-m-d'),
                'payment_mode'    => $payMode,
                'reference_no'    => $request->initial_reference_no,
                'payment_type'    => 'Advance',
                'remarks'         => 'Initial payment / advance recorded during contractor creation',
                'created_by'      => Auth::id(),
                'updated_by'      => Auth::id(),
            ]);

            $contractor->recalculatePaymentStatus();
        }

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Create',
            "Created new contractor '{$contractor->contractor_name}' with " . count($projectIds) . " assigned project(s) and " . count($propertyIds) . " assigned plot(s)/unit(s)"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$contractor->contractor_name}' added successfully.");
    }

    public function show(Contractor $contractor)
    {
        $this->authorise($contractor);
        $contractor->load([
            'project.propertyMaster',
            'projects.propertyMaster',
            'properties.project',
            'firm',
            'firms',
            'creator',
            'updater',
            'payments.creator',
            'payments.project',
            'payments.property',
            'payments.paymentMode'
        ]);

        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.contractors.show', compact('contractor', 'paymentModes'));
    }

    public function edit(Contractor $contractor)
    {
        $this->authorise($contractor);
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $contractor->load(['projects', 'properties', 'firms']);

        if ($isAdmin) {
            $projects = Project::with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = Project::forFirms([$contractor->firm_id])->with(['properties', 'propertyMaster', 'firm', 'firms'])->orderBy('project_name')->get();
            $firms = collect();
        }

        return view('admin.contractors.edit', compact('contractor', 'projects', 'firms'));
    }

    public function update(ContractorRequest $request, Contractor $contractor)
    {
        $this->authorise($contractor);
        $user = Auth::user();

        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $primaryFirmId = reset($firmIds);

        $projectIds = (array) $request->project_ids;
        if (empty($projectIds) && $request->filled('project_id')) {
            $projectIds = [(int)$request->project_id];
        }
        $primaryProjectId = reset($projectIds) ?: null;

        $contractAmount = $request->filled('contract_amount') ? floatval($request->contract_amount) : 0.00;

        $contractor->update([
            'firm_id'         => $primaryFirmId,
            'project_id'      => $primaryProjectId,
            'contractor_name' => $request->contractor_name,
            'mobile'          => $request->mobile,
            'aadhar_no'       => $request->aadhar_no,
            'pan_no'          => $request->pan_no,
            'bank_name'       => $request->bank_name,
            'account_number'  => $request->account_number,
            'ifsc_code'       => $request->ifsc_code,
            'branch_name'     => $request->branch_name,
            'address'         => $request->address,
            'contract_amount' => $contractAmount,
            'work_type'       => $request->work_type,
            'contract_date'   => $request->contract_date,
            'contract_notes'  => $request->contract_notes,
            'status'          => $request->status,
            'updated_by'      => Auth::id(),
        ]);

        $contractor->syncFirms($firmIds);
        $contractor->syncProjects($projectIds);

        $propertyIds = (array) $request->property_ids;
        $contractor->syncProperties($propertyIds);

        $contractor->recalculatePaymentStatus();

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Update',
            "Updated contractor '{$contractor->contractor_name}' (ID: {$contractor->id}) with " . count($projectIds) . " assigned project(s) and " . count($propertyIds) . " assigned plot(s)/unit(s)"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$contractor->contractor_name}' updated successfully.");
    }

    public function storePayment(Request $request, Contractor $contractor)
    {
        $this->authorise($contractor);

        $validated = $request->validate([
            'amount'        => 'required|numeric|min:0.01',
            'payment_date'  => 'required|date',
            'payment_mode'  => 'required|string|max:100',
            'reference_no'  => 'nullable|string|max:150',
            'bank_name'     => 'nullable|string|max:150',
            'bill_no'       => 'nullable|string|max:150',
            'project_id'    => 'nullable|exists:projects,id',
            'property_id'   => 'nullable|exists:properties,id',
            'payment_type'  => 'nullable|string|max:50',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remarks'       => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $firmId = $contractor->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        $docPath = null;
        if ($request->hasFile('document_file')) {
            $docPath = $request->file('document_file')->store('contractors/payments', 'public');
        }

        $pmId = PaymentMode::where('name', $validated['payment_mode'])->value('id');

        ContractorPayment::create([
            'contractor_id'   => $contractor->id,
            'firm_id'         => $firmId,
            'project_id'      => $validated['project_id'] ?? $contractor->project_id,
            'property_id'     => $validated['property_id'] ?? null,
            'payment_mode_id' => $pmId,
            'amount'          => (float)$validated['amount'],
            'payment_date'    => $validated['payment_date'],
            'payment_mode'    => $validated['payment_mode'],
            'reference_no'    => $validated['reference_no'] ?? null,
            'bank_name'       => $validated['bank_name'] ?? null,
            'bill_no'         => $validated['bill_no'] ?? null,
            'document_file'   => $docPath,
            'payment_type'    => $validated['payment_type'] ?? 'Part Payment',
            'remarks'         => $validated['remarks'] ?? null,
            'created_by'      => $user ? $user->id : null,
            'updated_by'      => $user ? $user->id : null,
        ]);

        $contractor->recalculatePaymentStatus();

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Payment',
            "Recorded payment installment of ₹" . number_format($validated['amount'], 2) . " for contractor '{$contractor->contractor_name}'"
        );

        return redirect()->route('contractors.show', $contractor->id)
            ->with('success', 'Payment installment of ₹' . number_format($validated['amount'], 2) . ' recorded successfully.');
    }

    public function destroyPayment(Contractor $contractor, ContractorPayment $payment)
    {
        $this->authorise($contractor);

        if ($payment->contractor_id != $contractor->id) {
            abort(404);
        }

        $amount = $payment->amount;
        if ($payment->document_file) {
            Storage::disk('public')->delete($payment->document_file);
        }

        $payment->delete();
        $contractor->recalculatePaymentStatus();

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Delete Payment',
            "Deleted payment record of ₹" . number_format($amount, 2) . " for contractor '{$contractor->contractor_name}'"
        );

        return redirect()->route('contractors.show', $contractor->id)
            ->with('success', 'Payment record of ₹' . number_format($amount, 2) . ' removed successfully.');
    }

    public function destroy(Contractor $contractor)
    {
        $this->authorise($contractor);
        $name = $contractor->contractor_name;
        $contractor->delete();

        \App\Models\AuditLog::log(
            'Contractor Management',
            'Delete',
            "Deleted contractor '{$name}'"
        );

        return redirect()->route('contractors.index')
            ->with('success', "Contractor '{$name}' deleted successfully.");
    }

    public function getByProject($projectId)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::where('status', 'active')
            ->where(function ($q) use ($projectId) {
                $q->where('project_id', $projectId)
                  ->orWhereHas('projects', function ($pq) use ($projectId) {
                      $pq->where('projects.id', $projectId);
                  });
            });

        if (!$isAdmin && $firmId) {
            $query->forFirms([$firmId]);
        }

        $contractors = $query->orderBy('contractor_name')->get(['id', 'contractor_name', 'mobile', 'project_id']);
        return response()->json($contractors);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Contractor::with(['project.propertyMaster', 'projects.propertyMaster', 'properties', 'firm', 'firms']);

        if (!$isAdmin) {
            $query->forFirms([$firmId]);
        } elseif ($request->filled('firm_id')) {
            $query->forFirms([$request->firm_id]);
        }

        if ($request->filled('project_id')) {
            $pId = $request->project_id;
            $query->where(function ($q) use ($pId) {
                $q->where('project_id', $pId)
                  ->orWhereHas('projects', function ($pq) use ($pId) {
                      $pq->where('projects.id', $pId);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('contractor_name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('aadhar_no', 'like', "%{$s}%")
                  ->orWhere('pan_no', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhereHas('projects', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('project_name', 'like', "%{$s}%");
                  })
                  ->orWhereHas('properties', function ($pq) use ($s) {
                      $pq->where('property_name', 'like', "%{$s}%")
                         ->orWhere('property_code', 'like', "%{$s}%")
                         ->orWhere('unit_no', 'like', "%{$s}%");
                  });
            });
        }

        $contractors = $query->latest()->get();
        $totalContractors = $contractors->count();
        $activeContractors = $contractors->where('status', 'active')->count();

        return view('admin.contractors.pdf', compact('contractors', 'totalContractors', 'activeContractors'));
    }

    public function downloadPdf(Contractor $contractor)
    {
        $this->authorise($contractor);
        $contractor->load(['project.propertyMaster', 'projects.propertyMaster', 'properties.project', 'firm', 'firms', 'creator', 'updater']);

        return view('admin.contractors.show-pdf', compact('contractor'));
    }
}
