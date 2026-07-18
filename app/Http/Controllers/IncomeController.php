<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRequest;

use App\Models\Income;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    const INCOME_TYPES = ['Rent', 'Property Sale', 'Commission', 'Interest', 'Other'];

    private function dropdowns(): array
    {
        return [
            'paymentModes' => PaymentMode::where('firm_id', Auth::user()->firm_id)
                ->where('status', 'active')->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $query = Income::with('paymentMode')->where('firm_id', Auth::user()->firm_id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('income_type', 'like', "%{$s}%")
                  ->orWhere('received_from', 'like', "%{$s}%")
                  ->orWhere('reference_no', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('filter_type')) {
            $query->where('income_type', $request->filter_type);
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $totalAmount = (clone $query)->sum('amount');
        $incomes     = $query->orderBy('income_date', 'desc')->paginate(15);

        return view('admin.incomes.index', compact('incomes', 'totalAmount'));
    }

    public function create()
    {
        return view('admin.incomes.create', $this->dropdowns());
    }

    public function store(IncomeRequest $request)
    {
        

        Income::create([
            'firm_id'         => Auth::user()->firm_id,
            'income_date'     => $request->income_date,
            'income_type'     => $request->income_type,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'received_from'   => $request->received_from,
            'reference_no'    => $request->reference_no,
            'description'     => $request->description,
            'status'          => $request->status,
        ]);

        return redirect()->route('incomes.index')->with('success', 'Income record added successfully.');
    }

    public function show(Income $income)
    {
        if ($income->firm_id != Auth::user()->firm_id) abort(403);
        $income->load('paymentMode');
        return view('admin.incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        if ($income->firm_id != Auth::user()->firm_id) abort(403);
        return view('admin.incomes.edit', array_merge(['income' => $income], $this->dropdowns()));
    }

    public function update(IncomeRequest $request, Income $income)
    {
        if ($income->firm_id != Auth::user()->firm_id) abort(403);

        

        $income->update([
            'income_date'     => $request->income_date,
            'income_type'     => $request->income_type,
            'amount'          => $request->amount,
            'payment_mode_id' => $request->payment_mode_id ?: null,
            'received_from'   => $request->received_from,
            'reference_no'    => $request->reference_no,
            'description'     => $request->description,
            'status'          => $request->status,
        ]);

        return redirect()->route('incomes.index')->with('success', 'Income record updated successfully.');
    }

    public function destroy(Income $income)
    {
        if ($income->firm_id != Auth::user()->firm_id) abort(403);
        $income->delete();
        return redirect()->route('incomes.index')->with('success', 'Income record deleted successfully.');
    }
}
