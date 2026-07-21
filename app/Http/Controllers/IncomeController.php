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
            $pmQuery->where('firm_id', $firmId);
        }

        return [
            'firms'        => $firms,
            'paymentModes' => $pmQuery->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Income::with(['firms', 'firm', 'paymentMode']);

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

        $totalAmount = (clone $query)->sum('amount');
        $incomes     = $query->orderBy('income_date', 'desc')->paginate(15)->withQueryString();
        $firms       = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.incomes.index', compact('incomes', 'firms', 'totalAmount'));
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
        $income->load(['firms', 'firm', 'paymentMode']);
        $this->authorise($income);
        return view('admin.incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $income->load(['firms', 'firm']);
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
