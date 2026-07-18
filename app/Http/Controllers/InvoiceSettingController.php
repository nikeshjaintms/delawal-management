<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceSettingRequest;

use App\Models\InvoiceSetting;
use App\Models\FinancialYear;
use Illuminate\Http\Request;

class InvoiceSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceSetting::with('financialYear');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $settings = $query->latest()->paginate(10)->withQueryString();

        return view('admin.firm-management.invoice-settings.index', compact('settings'));
    }

    public function create()
    {
        $financialYears = FinancialYear::where('status', 'active')->latest()->get();
        return view('admin.firm-management.invoice-settings.create', compact('financialYears'));
    }

    public function store(InvoiceSettingRequest $request)
    {
        $validated = $request->validated();
        InvoiceSetting::create($validated);

        return redirect()->route('invoice-settings.index')
            ->with('success', 'Invoice settings created successfully!');
    }

    public function show(InvoiceSetting $invoiceSetting)
    {
        $invoiceSetting->load('financialYear');
        return view('admin.firm-management.invoice-settings.show', compact('invoiceSetting'));
    }

    public function edit(InvoiceSetting $invoiceSetting)
    {
        $financialYears = FinancialYear::where('status', 'active')->latest()->get();
        return view('admin.firm-management.invoice-settings.edit', compact('invoiceSetting', 'financialYears'));
    }

    public function update(InvoiceSettingRequest $request, InvoiceSetting $invoiceSetting)
    {
        $validated = $request->validated();
        $invoiceSetting->update($validated);

        return redirect()->route('invoice-settings.index')
            ->with('success', 'Invoice settings updated successfully!');
    }

    public function destroy(InvoiceSetting $invoiceSetting)
    {
        $invoiceSetting->delete();

        return redirect()->route('invoice-settings.index')
            ->with('success', 'Invoice settings deleted successfully!');
    }

    /** Preview current invoice number formats */
    public function preview(InvoiceSetting $invoiceSetting)
    {
        $invoiceSetting->load('financialYear');
        $year = $invoiceSetting->financialYear
            ? substr($invoiceSetting->financialYear->year_name, 0, 4)
            : date('Y');

        $num = str_pad($invoiceSetting->current_number, 4, '0', STR_PAD_LEFT);

        $previews = [
            'Sales'    => "{$invoiceSetting->sales_prefix}-{$year}-{$num}",
            'Purchase' => "{$invoiceSetting->purchase_prefix}-{$year}-{$num}",
            'Booking'  => "{$invoiceSetting->booking_prefix}-{$year}-{$num}",
            'Rental'   => "{$invoiceSetting->rental_prefix}-{$year}-{$num}",
            'Payment'  => "{$invoiceSetting->payment_prefix}-{$year}-{$num}",
            'Receipt'  => "{$invoiceSetting->receipt_prefix}-{$year}-{$num}",
            'Expense'  => "{$invoiceSetting->expense_prefix}-{$year}-{$num}",
            'Income'   => "{$invoiceSetting->income_prefix}-{$year}-{$num}",
            'Loan'     => "{$invoiceSetting->loan_prefix}-{$year}-{$num}",
        ];

        return response()->json($previews);
    }
}
