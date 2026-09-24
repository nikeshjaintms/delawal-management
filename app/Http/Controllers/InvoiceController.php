<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Customer;
use App\Models\Firm;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\InvoiceSetting;
use App\Models\PaymentMode;
use App\Models\Project;
use App\Models\PropertySale;
use App\Models\PurchaseOrder;
use App\Models\Rental;
use App\Models\Tenant;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Get active firm ID and admin flag for current request/session
     */
    protected function getAuthContext()
    {
        $user = Auth::user();
        $isFirm = session('login_type') === 'firm' && session('firm_id');
        $isAdmin = ($user && $user->isAdmin()) || $isFirm;
        $firmId = $isFirm ? session('firm_id') : ($user ? $user->firm_id : session('firm_id'));

        return [$isAdmin, $firmId, $user];
    }

    /**
     * Display a listing of invoices (System-wide or filtered).
     */
    public function index(Request $request)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        $query = Invoice::with(['firm', 'project', 'customer', 'tenant', 'contractor', 'vendor', 'creator']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('invoice_no', 'like', "%{$s}%")
                    ->orWhere('recipient_name', 'like', "%{$s}%")
                    ->orWhere('recipient_phone', 'like', "%{$s}%")
                    ->orWhere('recipient_email', 'like', "%{$s}%")
                    ->orWhere('recipient_gstin', 'like', "%{$s}%")
                    ->orWhereHas('project', fn($pq) => $pq->where('project_name', 'like', "%{$s}%"));
            });
        }

        // Summary Stats before pagination
        $statsQuery = clone $query;
        $totalInvoicesCount = $statsQuery->count();
        $totalInvoicedAmount = (float) $statsQuery->sum('total_amount');
        $totalPaidAmount = (float) $statsQuery->sum('paid_amount');
        $totalBalanceAmount = (float) $statsQuery->sum('balance_amount');
        $paidCount = (clone $query)->where('payment_status', 'paid')->count();
        $unpaidCount = (clone $query)->where('payment_status', 'unpaid')->count();
        $partialCount = (clone $query)->where('payment_status', 'partially_paid')->count();

        $invoices = $query->latest('invoice_date')->latest('id')->paginate(15)->withQueryString();

        // Filter dropdown options
        $projectsQuery = Project::orderBy('project_name');
        if (!$isAdmin) {
            $projectsQuery->where('firm_id', $firmId);
        }
        $projects = $projectsQuery->get();
        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();

        return view('admin.invoices.index', compact(
            'invoices',
            'projects',
            'firms',
            'totalInvoicesCount',
            'totalInvoicedAmount',
            'totalPaidAmount',
            'totalBalanceAmount',
            'paidCount',
            'unpaidCount',
            'partialCount'
        ));
    }

    /**
     * Show form to create a new invoice.
     */
    public function create(Request $request)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        
        $projectsQuery = Project::where('status', 'active')->orderBy('project_name');
        $customersQuery = Customer::where('status', 'active')->orderBy('name');
        $tenantsQuery = Tenant::where('status', 'active')->orderBy('name');
        $contractorsQuery = Contractor::where('status', 'active')->orderBy('contractor_name');
        $vendorsQuery = Vendor::where('status', 'active')->orderBy('name');

        if (!$isAdmin && $firmId) {
            $projectsQuery->where('firm_id', $firmId);
            $customersQuery->where('firm_id', $firmId);
            $tenantsQuery->where('firm_id', $firmId);
            $contractorsQuery->where('firm_id', $firmId);
            $vendorsQuery->where('firm_id', $firmId);
        }

        $projects = $projectsQuery->get();
        $customers = $customersQuery->get();
        $tenants = $tenantsQuery->get();
        $contractors = $contractorsQuery->get();
        $vendors = $vendorsQuery->get();
        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        // Selected / Pre-filled contextual records
        $selectedProjectId = $request->input('project_id');
        $selectedProject = $selectedProjectId ? Project::find($selectedProjectId) : null;
        $selectedType = $request->input('type', 'custom');

        // Suggest Invoice Number
        $activeSetting = InvoiceSetting::activeSetting();
        $suggestedNo = $activeSetting ? $activeSetting->generateNumber($selectedType === 'sale' ? 'sales' : ($selectedType === 'rental' ? 'rental' : 'payment')) : 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Preload firm bank details if applicable
        $defaultFirm = $selectedProject ? $selectedProject->firm : ($firmId ? Firm::find($firmId) : $firms->first());

        return view('admin.invoices.create', compact(
            'firms',
            'projects',
            'customers',
            'tenants',
            'contractors',
            'vendors',
            'paymentModes',
            'selectedProject',
            'selectedType',
            'suggestedNo',
            'defaultFirm'
        ));
    }

    /**
     * Store a newly created invoice and its line items.
     */
    public function store(Request $request)
    {
        [$isAdmin, $currentFirmId, $user] = $this->getAuthContext();

        $request->validate([
            'firm_id'          => 'required|exists:firms,id',
            'invoice_no'       => 'required|string|max:100|unique:invoices,invoice_no',
            'invoice_type'     => 'required|in:sale,rental,contractor,material_purchase,custom',
            'invoice_date'     => 'required|date',
            'due_date'         => 'nullable|date|after_or_equal:invoice_date',
            'recipient_name'   => 'required|string|max:255',
            'recipient_phone'  => 'nullable|string|max:50',
            'recipient_email'  => 'nullable|email|max:150',
            'recipient_address'=> 'nullable|string',
            'recipient_gstin'  => 'nullable|string|max:50',
            'project_id'       => 'nullable|exists:projects,id',
            'items'            => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:500',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.unit_price'       => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineDiscount = (float) ($item['discount_amount'] ?? 0);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);

            $basePrice = ($qty * $unitPrice) - $lineDiscount;
            $lineTax = ($basePrice * $taxPercent) / 100;
            $lineTotal = $basePrice + $lineTax;

            $subtotal += $basePrice;

            $itemsData[] = [
                'item_type'        => $item['item_type'] ?? 'custom',
                'item_description' => $item['item_description'],
                'hsn_sac_code'     => $item['hsn_sac_code'] ?? null,
                'quantity'         => $qty,
                'unit'             => $item['unit'] ?? 'Nos',
                'unit_price'       => $unitPrice,
                'discount_amount'  => $lineDiscount,
                'tax_percent'      => $taxPercent,
                'tax_amount'       => $lineTax,
                'total_price'      => $lineTotal,
            ];
        }

        // Global Discount calculation
        $discountType = $request->discount_type ?? 'fixed';
        $discountValue = (float) ($request->discount_value ?? 0);
        $globalDiscount = $discountType === 'percentage'
            ? ($subtotal * $discountValue) / 100
            : $discountValue;

        $taxableAmount = max(0, $subtotal - $globalDiscount);

        // Overall Tax Calculation
        $taxType = $request->tax_type ?? 'gst_intra';
        $taxPercent = (float) ($request->tax_percent ?? 0);
        $totalTax = ($taxableAmount * $taxPercent) / 100;

        $cgst = 0;
        $sgst = 0;
        $igst = 0;

        if ($taxType === 'gst_intra') {
            $cgst = $totalTax / 2;
            $sgst = $totalTax / 2;
        } elseif ($taxType === 'gst_inter') {
            $igst = $totalTax;
        }

        $roundOff = (float) ($request->round_off ?? 0);
        $grandTotal = $taxableAmount + $totalTax + $roundOff;

        // Initial Payment if given
        $initialPayment = (float) ($request->initial_payment_amount ?? 0);
        $paidAmount = min($grandTotal, max(0, $initialPayment));
        $balanceAmount = max(0, $grandTotal - $paidAmount);

        $paymentStatus = 'unpaid';
        if ($balanceAmount <= 0 && $grandTotal > 0) {
            $paymentStatus = 'paid';
        } elseif ($paidAmount > 0) {
            $paymentStatus = 'partially_paid';
        }

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'firm_id'          => $request->firm_id,
                'project_id'       => $request->project_id,
                'invoice_no'       => $request->invoice_no,
                'invoice_type'     => $request->invoice_type,
                'invoice_date'     => $request->invoice_date,
                'due_date'         => $request->due_date,
                'customer_id'      => $request->customer_id,
                'tenant_id'        => $request->tenant_id,
                'contractor_id'    => $request->contractor_id,
                'vendor_id'        => $request->vendor_id,
                'recipient_name'   => $request->recipient_name,
                'recipient_phone'  => $request->recipient_phone,
                'recipient_email'  => $request->recipient_email,
                'recipient_address'=> $request->recipient_address,
                'recipient_gstin'  => $request->recipient_gstin,
                'property_sale_id' => $request->property_sale_id,
                'rental_id'        => $request->rental_id,
                'purchase_order_id'=> $request->purchase_order_id,
                'subtotal'         => $subtotal,
                'discount_type'    => $discountType,
                'discount_value'   => $discountValue,
                'discount_amount'  => $globalDiscount,
                'tax_type'         => $taxType,
                'tax_percent'      => $taxPercent,
                'cgst_amount'      => $cgst,
                'sgst_amount'      => $sgst,
                'igst_amount'      => $igst,
                'tax_amount'       => $totalTax,
                'round_off'        => $roundOff,
                'total_amount'     => $grandTotal,
                'paid_amount'      => $paidAmount,
                'balance_amount'   => $balanceAmount,
                'payment_status'   => $paymentStatus,
                'status'           => $request->status ?? 'issued',
                'bank_name'        => $request->bank_name,
                'bank_account_no'  => $request->bank_account_no,
                'bank_ifsc'        => $request->bank_ifsc,
                'bank_branch'      => $request->bank_branch,
                'terms_conditions' => $request->terms_conditions,
                'notes'            => $request->notes,
                'created_by'       => $user ? $user->id : null,
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            if ($paidAmount > 0) {
                $invoice->payments()->create([
                    'payment_date'          => $request->initial_payment_date ?? $request->invoice_date,
                    'amount'                => $paidAmount,
                    'payment_mode_id'       => $request->initial_payment_mode_id,
                    'payment_mode'          => $request->initial_payment_mode ?? 'Cash',
                    'transaction_reference' => $request->initial_payment_ref,
                    'notes'                 => 'Initial payment upon invoice issuance',
                    'received_by'           => $user ? $user->id : null,
                ]);
            }

            // Increment Invoice Setting if used
            $activeSetting = InvoiceSetting::activeSetting();
            if ($activeSetting) {
                $activeSetting->increment('current_number');
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice ' . $invoice->invoice_no . ' generated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display detailed view of the invoice.
     */
    public function show(Invoice $invoice)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $invoice->load([
            'firm',
            'project',
            'customer',
            'tenant',
            'contractor',
            'vendor',
            'propertySale',
            'rental',
            'purchaseOrder',
            'items',
            'payments.paymentMode',
            'payments.receiver',
            'creator'
        ]);

        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.invoices.show', compact('invoice', 'paymentModes'));
    }

    /**
     * Show form to edit an existing invoice.
     */
    public function edit(Invoice $invoice)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $invoice->load(['items', 'payments', 'firm', 'project']);

        $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        $projects = Project::where('status', 'active')->orderBy('project_name')->get();
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();
        $contractors = Contractor::where('status', 'active')->orderBy('contractor_name')->get();
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.invoices.edit', compact(
            'invoice',
            'firms',
            'projects',
            'customers',
            'tenants',
            'contractors',
            'vendors',
            'paymentModes'
        ));
    }

    /**
     * Update an existing invoice and its line items.
     */
    public function update(Request $request, Invoice $invoice)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $request->validate([
            'firm_id'          => 'required|exists:firms,id',
            'invoice_no'       => 'required|string|max:100|unique:invoices,invoice_no,' . $invoice->id,
            'invoice_type'     => 'required|in:sale,rental,contractor,material_purchase,custom',
            'invoice_date'     => 'required|date',
            'due_date'         => 'nullable|date|after_or_equal:invoice_date',
            'recipient_name'   => 'required|string|max:255',
            'recipient_phone'  => 'nullable|string|max:50',
            'recipient_email'  => 'nullable|email|max:150',
            'recipient_address'=> 'nullable|string',
            'recipient_gstin'  => 'nullable|string|max:50',
            'project_id'       => 'nullable|exists:projects,id',
            'items'            => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:500',
            'items.*.quantity'         => 'required|numeric|min:0.01',
            'items.*.unit_price'       => 'required|numeric|min:0',
        ]);

        $subtotal = 0;
        $itemsData = [];

        foreach ($request->items as $item) {
            $qty = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineDiscount = (float) ($item['discount_amount'] ?? 0);
            $taxPercent = (float) ($item['tax_percent'] ?? 0);

            $basePrice = ($qty * $unitPrice) - $lineDiscount;
            $lineTax = ($basePrice * $taxPercent) / 100;
            $lineTotal = $basePrice + $lineTax;

            $subtotal += $basePrice;

            $itemsData[] = [
                'item_type'        => $item['item_type'] ?? 'custom',
                'item_description' => $item['item_description'],
                'hsn_sac_code'     => $item['hsn_sac_code'] ?? null,
                'quantity'         => $qty,
                'unit'             => $item['unit'] ?? 'Nos',
                'unit_price'       => $unitPrice,
                'discount_amount'  => $lineDiscount,
                'tax_percent'      => $taxPercent,
                'tax_amount'       => $lineTax,
                'total_price'      => $lineTotal,
            ];
        }

        // Global Discount calculation
        $discountType = $request->discount_type ?? 'fixed';
        $discountValue = (float) ($request->discount_value ?? 0);
        $globalDiscount = $discountType === 'percentage'
            ? ($subtotal * $discountValue) / 100
            : $discountValue;

        $taxableAmount = max(0, $subtotal - $globalDiscount);

        // Tax Calculation
        $taxType = $request->tax_type ?? 'gst_intra';
        $taxPercent = (float) ($request->tax_percent ?? 0);
        $totalTax = ($taxableAmount * $taxPercent) / 100;

        $cgst = 0;
        $sgst = 0;
        $igst = 0;

        if ($taxType === 'gst_intra') {
            $cgst = $totalTax / 2;
            $sgst = $totalTax / 2;
        } elseif ($taxType === 'gst_inter') {
            $igst = $totalTax;
        }

        $roundOff = (float) ($request->round_off ?? 0);
        $grandTotal = $taxableAmount + $totalTax + $roundOff;

        DB::beginTransaction();
        try {
            $invoice->update([
                'firm_id'          => $request->firm_id,
                'project_id'       => $request->project_id,
                'invoice_no'       => $request->invoice_no,
                'invoice_type'     => $request->invoice_type,
                'invoice_date'     => $request->invoice_date,
                'due_date'         => $request->due_date,
                'customer_id'      => $request->customer_id,
                'tenant_id'        => $request->tenant_id,
                'contractor_id'    => $request->contractor_id,
                'vendor_id'        => $request->vendor_id,
                'recipient_name'   => $request->recipient_name,
                'recipient_phone'  => $request->recipient_phone,
                'recipient_email'  => $request->recipient_email,
                'recipient_address'=> $request->recipient_address,
                'recipient_gstin'  => $request->recipient_gstin,
                'property_sale_id' => $request->property_sale_id,
                'rental_id'        => $request->rental_id,
                'purchase_order_id'=> $request->purchase_order_id,
                'subtotal'         => $subtotal,
                'discount_type'    => $discountType,
                'discount_value'   => $discountValue,
                'discount_amount'  => $globalDiscount,
                'tax_type'         => $taxType,
                'tax_percent'      => $taxPercent,
                'cgst_amount'      => $cgst,
                'sgst_amount'      => $sgst,
                'igst_amount'      => $igst,
                'tax_amount'       => $totalTax,
                'round_off'        => $roundOff,
                'total_amount'     => $grandTotal,
                'status'           => $request->status ?? $invoice->status,
                'bank_name'        => $request->bank_name,
                'bank_account_no'  => $request->bank_account_no,
                'bank_ifsc'        => $request->bank_ifsc,
                'bank_branch'      => $request->bank_branch,
                'terms_conditions' => $request->terms_conditions,
                'notes'            => $request->notes,
            ]);

            // Sync items
            $invoice->items()->delete();
            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            // Recalculate payment balances
            $invoice->recalculateBalances();

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Delete an invoice.
     */
    public function destroy(Invoice $invoice)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $no = $invoice->invoice_no;
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', "Invoice {$no} deleted successfully.");
    }

    /**
     * Record payment against an invoice.
     */
    public function recordPayment(Request $request, Invoice $invoice)
    {
        [$isAdmin, $firmId, $user] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $request->validate([
            'payment_date'          => 'required|date',
            'amount'                => 'required|numeric|min:0.01',
            'payment_mode_id'       => 'nullable|exists:payment_modes,id',
            'payment_mode'          => 'nullable|string|max:50',
            'transaction_reference' => 'nullable|string|max:100',
            'notes'                 => 'nullable|string',
        ]);

        $modeName = $request->payment_mode;
        if ($request->payment_mode_id && empty($modeName)) {
            $mode = PaymentMode::find($request->payment_mode_id);
            $modeName = $mode ? $mode->name : 'Cash';
        }

        $invoice->payments()->create([
            'payment_date'          => $request->payment_date,
            'amount'                => $request->amount,
            'payment_mode_id'       => $request->payment_mode_id,
            'payment_mode'          => $modeName ?: 'Cash',
            'transaction_reference' => $request->transaction_reference,
            'notes'                 => $request->notes,
            'received_by'           => $user ? $user->id : null,
        ]);

        $invoice->recalculateBalances();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment of ₹' . number_format($request->amount, 2) . ' recorded successfully!');
    }

    /**
     * Delete a payment recorded on an invoice.
     */
    public function destroyPayment(Invoice $invoice, InvoicePayment $payment)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }

        $payment->delete();
        $invoice->recalculateBalances();

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Payment entry deleted.');
    }

    /**
     * Print / PDF View for a single invoice.
     */
    public function print(Invoice $invoice)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        if (!$isAdmin && $invoice->firm_id != $firmId) {
            abort(403);
        }

        $invoice->load([
            'firm',
            'project',
            'customer',
            'tenant',
            'contractor',
            'vendor',
            'items',
            'payments.paymentMode',
            'creator'
        ]);

        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * Export invoice listings as PDF.
     */
    public function exportPdf(Request $request)
    {
        [$isAdmin, $firmId] = $this->getAuthContext();

        $query = Invoice::with(['firm', 'project', 'customer']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('invoice_date')->get();

        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->sum('paid_amount');
        $totalBalance = $invoices->sum('balance_amount');

        return view('admin.invoices.pdf-list', compact('invoices', 'totalInvoiced', 'totalPaid', 'totalBalance'));
    }

    /**
     * AJAX Endpoint to fetch details for Customer / Tenant / Contractor / Vendor / Project
     */
    public function ajaxData(Request $request)
    {
        $type = $request->type;
        $id = $request->id;

        if (!$type || !$id) {
            return response()->json(['error' => 'Invalid parameters'], 400);
        }

        switch ($type) {
            case 'customer':
                $entity = Customer::find($id);
                return response()->json([
                    'name'    => $entity->name ?? '',
                    'phone'   => $entity->mobile ?? '',
                    'email'   => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin'   => '',
                ]);

            case 'tenant':
                $entity = Tenant::find($id);
                return response()->json([
                    'name'    => $entity->name ?? '',
                    'phone'   => $entity->mobile ?? '',
                    'email'   => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin'   => '',
                ]);

            case 'contractor':
                $entity = Contractor::find($id);
                return response()->json([
                    'name'    => $entity->name ?? ($entity->contractor_name ?? ''),
                    'phone'   => $entity->mobile ?? ($entity->phone ?? ''),
                    'email'   => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin'   => $entity->gstin ?? ($entity->gst_number ?? ''),
                ]);

            case 'vendor':
                $entity = Vendor::find($id);
                return response()->json([
                    'name'    => $entity->vendor_name ?? ($entity->name ?? ''),
                    'phone'   => $entity->phone ?? ($entity->mobile ?? ''),
                    'email'   => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin'   => $entity->gst_number ?? ($entity->gstin ?? ''),
                ]);

            case 'project':
                $project = Project::with('firm')->find($id);
                return response()->json([
                    'firm_id'          => $project->firm_id ?? '',
                    'firm_name'        => $project->firm->firm_name ?? '',
                    'bank_name'        => $project->firm->bank_name ?? '',
                    'bank_account_no'  => $project->firm->bank_account_no ?? '',
                    'bank_ifsc'        => $project->firm->bank_ifsc ?? '',
                    'bank_branch'      => $project->firm->bank_branch ?? '',
                ]);

            default:
                return response()->json(['error' => 'Unknown type'], 404);
        }
    }
}
