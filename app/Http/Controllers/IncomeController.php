<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRequest;
use App\Models\Income;
use App\Models\PaymentMode;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    const INCOME_TYPES = ['Rent', 'Property Sale', 'Commission', 'Interest', 'Other'];

    private function authorise(Income $income): void
    {
        $user = Auth::user();
        if ($user && !$user->isAdmin()) {
            $userFirmId = $user->firm_id;
            if ($income->firm_id != $userFirmId && !$income->firms->contains($userFirmId)) {
                abort(403);
            }
        }
    }

    private function dropdowns($selectedFirmId = null): array
    {
        $user   = Auth::user();
        $firmId = $selectedFirmId ?? ($user ? $user->firm_id : session('firm_id'));

        $firms   = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $pmQuery = PaymentMode::where('status', 'active')->orderBy('name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $pmQuery->whereHas('firms', function($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            });
        }

        $propQuery = \App\Models\Property::with('propertyType')->orderBy('property_name');
        if ($firmId && (!$user || !$user->isAdmin())) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        return [
            'firms'        => $firms,
            'paymentModes' => $pmQuery->get(),
            'properties'   => $properties,
        ];
    }

    public function index(Request $request)
    {
        $query = Income::with(['firms', 'firm', 'paymentMode', 'property.propertyType']);

        if (!Auth::user()->isAdmin()) {
            $query->forFirms([Auth::user()->firm_id]);
        } elseif ($request->filled('firm_ids') || $request->filled('firm_id')) {
            $firmIds = $request->input('firm_ids', (array)$request->firm_id);
            $query->forFirms($firmIds);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('income_type', 'like', "%{$s}%")
                  ->orWhere('received_from', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhereHas('property', fn($p) => $p->where('property_name', 'like', "%{$s}%"))
                  ->orWhereHas('property.propertyType', fn($pt) => $pt->where('name', 'like', "%{$s}%"))
                  ->orWhereHas('firms', fn($f) => $f->where('firm_name', 'like', "%{$s}%"))
                  ->orWhereHas('firm', fn($f) => $f->where('firm_name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('income_type', $request->filter_type);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('income_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('income_date', '<=', $request->to_date);
        }

        if ($request->filled('filter_property')) {
            $query->where('property_id', $request->filter_property);
        }

        if ($request->filled('filter_property_type')) {
            $query->whereHas('property', fn($q) => $q->where('property_type_id', $request->filter_property_type));
        }

        $totalAmount = (clone $query)->sum('amount');

        if ($request->filled('export')) {
            $exportType = $request->export;
            $allRecords = $query->orderBy('income_date', 'desc')->get();
            if ($exportType === 'csv' || $exportType === 'excel') {
                return $this->exportCsv($allRecords);
            }
            if ($exportType === 'pdf') {
                return view('admin.incomes.pdf', compact('allRecords', 'totalAmount'));
            }
        }

        if ($request->get('print') === 'true') {
            $allRecords = $query->orderBy('income_date', 'desc')->get();
            return view('admin.incomes.pdf', compact('allRecords', 'totalAmount')); // PDF styled print view
        }

        $incomes     = $query->orderBy('income_date', 'desc')->paginate(15)->withQueryString();
        
        $user = Auth::user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $firms       = Firm::where('status', 'active')->orderBy('firm_name')->get();

        $propQuery = \App\Models\Property::orderBy('property_name');
        if (!Auth::user()->isAdmin()) {
            $propQuery->where('firm_id', $firmId);
        }
        $properties = $propQuery->get();

        $ptQuery = \App\Models\PropertyType::orderBy('name');
        if (!Auth::user()->isAdmin()) {
            $ptQuery->whereHas('firms', fn($q) => $q->where('firms.id', $firmId));
        }
        $propertyTypes = $ptQuery->get();

        return view('admin.incomes.index', compact('incomes', 'firms', 'totalAmount', 'properties', 'propertyTypes'));
    }

    private function exportCsv($records)
    {
        $filename = 'income-report-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        $callback = function () use ($records) {
            $h = fopen('php://output', 'w');
            fprintf($h, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($h, ['Delawala Properties & Management - Income Report']);
            fputcsv($h, ['Generated on', date('d M Y, h:i A')]);
            fputcsv($h, []);
            fputcsv($h, ['Sr', 'Firm', 'Property Name', 'Property Type', 'Date', 'Income Type', 'Received From', 'Amount', 'Payment Mode', 'Status']);
            foreach ($records as $i => $r) {
                fputcsv($h, [
                    $i + 1,
                    $r->firm_names ?? $r->firm->firm_name ?? '—',
                    $r->property->property_name ?? '—',
                    $r->property->propertyType->name ?? '—',
                    \Carbon\Carbon::parse($r->income_date)->format('d M Y'),
                    $r->income_type ?? '—',
                    $r->received_from ?? '—',
                    number_format($r->amount, 2),
                    $r->paymentMode->name ?? '—',
                    ucfirst($r->status),
                ]);
            }
            fclose($h);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function create()
    {
        return view('admin.incomes.create', $this->dropdowns());
    }

    public function store(IncomeRequest $request)
    {
        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: Auth::user()->firm_id;

        $income = Income::create([
            'firm_id'         => $primaryFirmId,
            'property_id'     => $request->property_id,
            'income_date'     => $request->income_date,
            'income_type'     => $request->income_type,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'received_from'   => $request->received_from,
            'reference_no'    => $request->reference_no,
            'description'     => $request->description,
            'status'          => $request->status,
        ]);

        $income->syncFirms($firmIds);

        return redirect()->route('incomes.index')->with('success', 'Income record added successfully.');
    }

    public function show(Income $income)
    {
        $income->load(['firms', 'firm', 'paymentMode', 'property.propertyType']);
        $this->authorise($income);
        return view('admin.incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $income->load(['firms', 'firm', 'property.propertyType']);
        $this->authorise($income);
        return view('admin.incomes.edit', array_merge(['income' => $income], $this->dropdowns($income->firm_id)));
    }

    public function update(IncomeRequest $request, Income $income)
    {
        $income->load(['firms', 'firm']);
        $this->authorise($income);

        $firmIds = $request->input('firm_ids', (array)($request->firm_id ?? $income->firm_id ?? Auth::user()->firm_id));
        $primaryFirmId = reset($firmIds) ?: $income->firm_id;

        $income->update([
            'firm_id'         => $primaryFirmId,
            'property_id'     => $request->property_id,
            'income_date'     => $request->income_date,
            'income_type'     => $request->income_type,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'received_from'   => $request->received_from,
            'reference_no'    => $request->reference_no,
            'description'     => $request->description,
            'status'          => $request->status,
        ]);

        $income->syncFirms($firmIds);

        return redirect()->route('incomes.index')->with('success', 'Income record updated successfully.');
    }

    public function destroy(Income $income)
    {
        $this->authorise($income);
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'Income record deleted successfully.');
    }
}
