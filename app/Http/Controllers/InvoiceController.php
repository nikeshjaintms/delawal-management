<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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
                $q
                    ->where('invoice_no', 'like', "%{$s}%")
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

        $propertySalesQuery = PropertySale::with(['customer', 'property.project'])->latest();
        $bookingsQuery = Booking::with(['customer', 'property.project'])->latest();
        $rentalsQuery = Rental::with(['tenant', 'property.project'])->latest();

        if (!$isAdmin && $firmId) {
            $projectsQuery->where('firm_id', $firmId);
            $customersQuery->where('firm_id', $firmId);
            $tenantsQuery->where('firm_id', $firmId);
            $contractorsQuery->where('firm_id', $firmId);
            $vendorsQuery->where('firm_id', $firmId);
            $propertySalesQuery->where('firm_id', $firmId);
            $bookingsQuery->where('firm_id', $firmId);
            $rentalsQuery->where('firm_id', $firmId);
        }

        $projects = $projectsQuery->get();
        $customers = $customersQuery->get();
        $tenants = $tenantsQuery->get();
        $contractors = $contractorsQuery->get();
        $vendors = $vendorsQuery->get();
        $propertySales = $propertySalesQuery->take(40)->get();
        $bookings = $bookingsQuery->take(40)->get();
        $rentals = $rentalsQuery->take(40)->get();
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
            'propertySales',
            'bookings',
            'rentals',
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
            'firm_id' => 'required|exists:firms,id',
            'invoice_no' => 'required|string|max:100|unique:invoices,invoice_no',
            'invoice_type' => 'required|in:sale,rental,contractor,material_purchase,custom',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:50',
            'recipient_email' => 'nullable|email|max:150',
            'recipient_address' => 'nullable|string',
            'recipient_gstin' => 'nullable|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
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
                'item_type' => $item['item_type'] ?? 'custom',
                'item_description' => $item['item_description'],
                'hsn_sac_code' => $item['hsn_sac_code'] ?? null,
                'quantity' => $qty,
                'unit' => $item['unit'] ?? 'Nos',
                'unit_price' => $unitPrice,
                'discount_amount' => $lineDiscount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $lineTax,
                'total_price' => $lineTotal,
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
                'firm_id' => $request->firm_id,
                'project_id' => $request->project_id,
                'invoice_no' => $request->invoice_no,
                'invoice_type' => $request->invoice_type,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'customer_id' => $request->customer_id,
                'tenant_id' => $request->tenant_id,
                'contractor_id' => $request->contractor_id,
                'vendor_id' => $request->vendor_id,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_email' => $request->recipient_email,
                'recipient_address' => $request->recipient_address,
                'recipient_gstin' => $request->recipient_gstin,
                'property_sale_id' => $request->property_sale_id,
                'rental_id' => $request->rental_id,
                'purchase_order_id' => $request->purchase_order_id,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $globalDiscount,
                'tax_type' => $taxType,
                'tax_percent' => $taxPercent,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_amount' => $totalTax,
                'round_off' => $roundOff,
                'total_amount' => $grandTotal,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $paymentStatus,
                'status' => $request->status ?? 'issued',
                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'bank_ifsc' => $request->bank_ifsc,
                'bank_branch' => $request->bank_branch,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
                'created_by' => $user ? $user->id : null,
            ]);

            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            if ($paidAmount > 0) {
                $invoice->payments()->create([
                    'payment_date' => $request->initial_payment_date ?? $request->invoice_date,
                    'amount' => $paidAmount,
                    'payment_mode_id' => $request->initial_payment_mode_id,
                    'payment_mode' => $request->initial_payment_mode ?? 'Cash',
                    'transaction_reference' => $request->initial_payment_ref,
                    'notes' => 'Initial payment upon invoice issuance',
                    'received_by' => $user ? $user->id : null,
                ]);
            }

            // Increment Invoice Setting if used
            $activeSetting = InvoiceSetting::activeSetting();
            if ($activeSetting) {
                $activeSetting->increment('current_number');
            }

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice->id)
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
            'firm_id' => 'required|exists:firms,id',
            'invoice_no' => 'required|string|max:100|unique:invoices,invoice_no,' . $invoice->id,
            'invoice_type' => 'required|in:sale,rental,contractor,material_purchase,custom',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:50',
            'recipient_email' => 'nullable|email|max:150',
            'recipient_address' => 'nullable|string',
            'recipient_gstin' => 'nullable|string|max:50',
            'project_id' => 'nullable|exists:projects,id',
            'items' => 'required|array|min:1',
            'items.*.item_description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
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
                'item_type' => $item['item_type'] ?? 'custom',
                'item_description' => $item['item_description'],
                'hsn_sac_code' => $item['hsn_sac_code'] ?? null,
                'quantity' => $qty,
                'unit' => $item['unit'] ?? 'Nos',
                'unit_price' => $unitPrice,
                'discount_amount' => $lineDiscount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $lineTax,
                'total_price' => $lineTotal,
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
                'firm_id' => $request->firm_id,
                'project_id' => $request->project_id,
                'invoice_no' => $request->invoice_no,
                'invoice_type' => $request->invoice_type,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'customer_id' => $request->customer_id,
                'tenant_id' => $request->tenant_id,
                'contractor_id' => $request->contractor_id,
                'vendor_id' => $request->vendor_id,
                'recipient_name' => $request->recipient_name,
                'recipient_phone' => $request->recipient_phone,
                'recipient_email' => $request->recipient_email,
                'recipient_address' => $request->recipient_address,
                'recipient_gstin' => $request->recipient_gstin,
                'property_sale_id' => $request->property_sale_id,
                'rental_id' => $request->rental_id,
                'purchase_order_id' => $request->purchase_order_id,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $globalDiscount,
                'tax_type' => $taxType,
                'tax_percent' => $taxPercent,
                'cgst_amount' => $cgst,
                'sgst_amount' => $sgst,
                'igst_amount' => $igst,
                'tax_amount' => $totalTax,
                'round_off' => $roundOff,
                'total_amount' => $grandTotal,
                'status' => $request->status ?? $invoice->status,
                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'bank_ifsc' => $request->bank_ifsc,
                'bank_branch' => $request->bank_branch,
                'terms_conditions' => $request->terms_conditions,
                'notes' => $request->notes,
            ]);

            // Sync items
            $invoice->items()->delete();
            foreach ($itemsData as $item) {
                $invoice->items()->create($item);
            }

            // Recalculate payment balances
            $invoice->recalculateBalances();

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice->id)
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

        return redirect()
            ->route('invoices.index')
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
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode_id' => 'nullable|exists:payment_modes,id',
            'payment_mode' => 'nullable|string|max:50',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $modeName = $request->payment_mode;
        if ($request->payment_mode_id && empty($modeName)) {
            $mode = PaymentMode::find($request->payment_mode_id);
            $modeName = $mode ? $mode->name : 'Cash';
        }

        $invoice->payments()->create([
            'payment_date' => $request->payment_date,
            'amount' => $request->amount,
            'payment_mode_id' => $request->payment_mode_id,
            'payment_mode' => $modeName ?: 'Cash',
            'transaction_reference' => $request->transaction_reference,
            'notes' => $request->notes,
            'received_by' => $user ? $user->id : null,
        ]);

        $invoice->recalculateBalances();

        return redirect()
            ->route('invoices.show', $invoice->id)
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

        return redirect()
            ->route('invoices.show', $invoice->id)
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
     * Automatically Generate & Issue a Tax Invoice from a Property Sale, Booking, or Rental Agreement.
     */
    public function autoGenerate(Request $request)
    {
        [$isAdmin, $firmId, $user] = $this->getAuthContext();
        $sourceType = $request->input('source_type');  // 'sale', 'booking', 'rental'
        $sourceId = $request->input('source_id');

        if (!$sourceType || !$sourceId) {
            return back()->with('error', 'Invalid source for automatic invoice generation.');
        }

        try {
            DB::beginTransaction();

            if ($sourceType === 'sale') {
                $sale = PropertySale::with(['customer', 'property.project', 'firm'])->findOrFail($sourceId);

                // Check if invoice already exists
                $existing = Invoice::where('property_sale_id', $sale->id)->first();
                if ($existing) {
                    DB::rollBack();
                    return redirect()
                        ->route('invoices.show', $existing->id)
                        ->with('info', "Tax Invoice #{$existing->invoice_no} already exists for this Property Sale.");
                }

                $activeSetting = InvoiceSetting::activeSetting();
                $invoiceNo = $activeSetting ? $activeSetting->generateNumber('sales') : 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $propNames = $sale->property_names ?: ($sale->property->property_name ?? 'Property Unit');
                $projName = $sale->property?->project?->project_name ?? 'Delawala Master Project';
                $subtotal = (float) $sale->sale_amount;

                $taxType = 'gst_intra';
                $taxPercent = 18.0;
                $totalTax = ($subtotal * $taxPercent) / 100;
                $cgst = $totalTax / 2;
                $sgst = $totalTax / 2;
                $grandTotal = $subtotal + $totalTax;
                $paidAmt = min((float) $sale->booking_amount, $grandTotal);
                $balAmt = max(0, $grandTotal - $paidAmt);

                $activeFirm = $sale->firm ?: ($firmId ? Firm::find($firmId) : Firm::first());

                $invoice = Invoice::create([
                    'firm_id' => $activeFirm ? $activeFirm->id : 3,
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'invoice_date' => $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date) : now(),
                    'due_date' => now()->addDays(15),
                    'recipient_name' => $sale->customer->name ?? 'Valued Customer',
                    'recipient_phone' => $sale->customer->mobile ?? null,
                    'recipient_email' => $sale->customer->email ?? null,
                    'recipient_address' => $sale->customer->address ?? ($sale->customer->city ?? 'Dahegam, Bharuch'),
                    'recipient_gstin' => $sale->customer->gst_no ?? null,
                    'customer_id' => $sale->customer_id,
                    'project_id' => $sale->property?->project_id,
                    'property_sale_id' => $sale->id,
                    'subtotal' => $subtotal,
                    'discount_type' => 'fixed',
                    'discount_value' => 0,
                    'discount_amount' => 0,
                    'tax_type' => $taxType,
                    'tax_percent' => $taxPercent,
                    'cgst_amount' => $cgst,
                    'sgst_amount' => $sgst,
                    'igst_amount' => 0,
                    'tax_amount' => $totalTax,
                    'round_off' => 0,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $paidAmt,
                    'balance_amount' => $balAmt,
                    'payment_status' => ($paidAmt >= $grandTotal) ? 'paid' : (($paidAmt > 0) ? 'partially_paid' : 'unpaid'),
                    'status' => 'active',
                    'created_by' => $user ? $user->id : null,
                    'bank_name' => $activeFirm->bank_name ?? 'Bank of Baroda',
                    'bank_account_no' => $activeFirm->bank_account_no ?? '',
                    'bank_ifsc' => $activeFirm->bank_ifsc ?? '',
                    'bank_branch' => $activeFirm->bank_branch ?? 'Dahegam Branch',
                    'terms_conditions' => "1. Official Tax Invoice issued by Delawala Infra Co. (GSTIN: 24CUBPD0770R1ZI).\n2. Subject to Dahegam / Bharuch Jurisdiction.",
                    'notes' => 'Auto-generated from Property Sale Agreement: ' . ($sale->agreement_no ?: ('SALE-' . $sale->id)),
                ]);

                // Create Item
                $invoice->items()->create([
                    'item_type' => 'property_sale',
                    'item_description' => "Property Sale Consideration for {$propNames} ({$projName})",
                    'hsn_sac_code' => '9954',
                    'quantity' => 1,
                    'unit' => 'Unit',
                    'unit_price' => $subtotal,
                    'discount_amount' => 0,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $totalTax,
                    'total_price' => $grandTotal,
                ]);

                // Record Advance Token Payment if paid
                if ($paidAmt > 0) {
                    $invoice->payments()->create([
                        'payment_date' => $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date) : now(),
                        'amount' => $paidAmt,
                        'payment_mode' => 'Bank Transfer / Cheque / Cash',
                        'transaction_reference' => 'Booking Token for ' . ($sale->agreement_no ?: ('SALE-' . $sale->id)),
                        'notes' => 'Auto-recorded token advance received from Sale Agreement',
                        'created_by' => $user ? $user->id : null,
                    ]);
                }

                DB::commit();
                return redirect()
                    ->route('invoices.show', $invoice->id)
                    ->with('success', "Tax Invoice #{$invoice->invoice_no} generated automatically with official GST details!");
            }

            if ($sourceType === 'booking') {
                $booking = Booking::with(['customer', 'property.project', 'firm'])->findOrFail($sourceId);

                $activeSetting = InvoiceSetting::activeSetting();
                $invoiceNo = $activeSetting ? $activeSetting->generateNumber('sales') : 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $propName = $booking->property->property_name ?? 'Property Unit';
                $projName = $booking->property?->project?->project_name ?? 'Delawala Project';
                $subtotal = (float) $booking->final_amount;

                $taxType = 'gst_intra';
                $taxPercent = 18.0;
                $totalTax = ($subtotal * $taxPercent) / 100;
                $cgst = $totalTax / 2;
                $sgst = $totalTax / 2;
                $grandTotal = $subtotal + $totalTax;
                $paidAmt = min((float) $booking->booking_amount, $grandTotal);
                $balAmt = max(0, $grandTotal - $paidAmt);

                $activeFirm = $booking->firm ?: ($firmId ? Firm::find($firmId) : Firm::first());

                $invoice = Invoice::create([
                    'firm_id' => $activeFirm ? $activeFirm->id : 3,
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'invoice_date' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date) : now(),
                    'due_date' => now()->addDays(15),
                    'recipient_name' => $booking->customer->name ?? 'Valued Customer',
                    'recipient_phone' => $booking->customer->mobile ?? null,
                    'recipient_email' => $booking->customer->email ?? null,
                    'recipient_address' => $booking->customer->address ?? ($booking->customer->city ?? 'Dahegam, Bharuch'),
                    'recipient_gstin' => $booking->customer->gst_no ?? null,
                    'customer_id' => $booking->customer_id,
                    'project_id' => $booking->property?->project_id,
                    'subtotal' => $subtotal,
                    'tax_type' => $taxType,
                    'tax_percent' => $taxPercent,
                    'cgst_amount' => $cgst,
                    'sgst_amount' => $sgst,
                    'igst_amount' => 0,
                    'tax_amount' => $totalTax,
                    'round_off' => 0,
                    'total_amount' => $grandTotal,
                    'paid_amount' => $paidAmt,
                    'balance_amount' => $balAmt,
                    'payment_status' => ($paidAmt >= $grandTotal) ? 'paid' : (($paidAmt > 0) ? 'partially_paid' : 'unpaid'),
                    'status' => 'active',
                    'created_by' => $user ? $user->id : null,
                    'bank_name' => $activeFirm->bank_name ?? 'Bank of Baroda',
                    'bank_account_no' => $activeFirm->bank_account_no ?? '',
                    'bank_ifsc' => $activeFirm->bank_ifsc ?? '',
                    'bank_branch' => $activeFirm->bank_branch ?? 'Dahegam Branch',
                    'terms_conditions' => "1. Official Tax Invoice issued by Delawala Infra Co. (GSTIN: 24CUBPD0770R1ZI).\n2. Subject to Dahegam / Bharuch Jurisdiction.",
                    'notes' => 'Auto-generated from Booking Code: ' . ($booking->booking_code ?: ('BK-' . $booking->id)),
                ]);

                $invoice->items()->create([
                    'item_type' => 'booking',
                    'item_description' => "Property Booking & Reservation for {$propName} ({$projName})",
                    'hsn_sac_code' => '9954',
                    'quantity' => 1,
                    'unit' => 'Unit',
                    'unit_price' => $subtotal,
                    'discount_amount' => 0,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $totalTax,
                    'total_price' => $grandTotal,
                ]);

                if ($paidAmt > 0) {
                    $invoice->payments()->create([
                        'payment_date' => $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date) : now(),
                        'amount' => $paidAmt,
                        'payment_mode' => 'Cash / Bank',
                        'transaction_reference' => 'Booking Token Advance for ' . ($booking->booking_code ?: 'BK-' . $booking->id),
                        'notes' => 'Auto-recorded token advance received from Booking',
                        'created_by' => $user ? $user->id : null,
                    ]);
                }

                DB::commit();
                return redirect()
                    ->route('invoices.show', $invoice->id)
                    ->with('success', "Tax Invoice #{$invoice->invoice_no} generated automatically from Booking!");
            }

            if ($sourceType === 'rental') {
                $rental = Rental::with(['tenant', 'property.project', 'firm'])->findOrFail($sourceId);

                $activeSetting = InvoiceSetting::activeSetting();
                $invoiceNo = $activeSetting ? $activeSetting->generateNumber('rental') : 'INV-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

                $propName = $rental->property->property_name ?? 'Rental Unit';
                $subtotal = (float) $rental->monthly_rent;

                $taxType = 'gst_intra';
                $taxPercent = 18.0;
                $totalTax = ($subtotal * $taxPercent) / 100;
                $cgst = $totalTax / 2;
                $sgst = $totalTax / 2;
                $grandTotal = $subtotal + $totalTax;

                $activeFirm = $rental->firm ?: ($firmId ? Firm::find($firmId) : Firm::first());

                $invoice = Invoice::create([
                    'firm_id' => $activeFirm ? $activeFirm->id : 3,
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'rental',
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(10),
                    'recipient_name' => $rental->tenant_name ?? ($rental->tenant->name ?? 'Tenant'),
                    'recipient_phone' => $rental->tenant_mobile ?? ($rental->tenant->mobile ?? null),
                    'recipient_email' => $rental->tenant->email ?? null,
                    'recipient_address' => $rental->tenant->address ?? 'Dahegam, Bharuch',
                    'recipient_gstin' => $rental->tenant->gst_no ?? null,
                    'tenant_id' => $rental->tenant_id,
                    'rental_id' => $rental->id,
                    'project_id' => $rental->property?->project_id,
                    'subtotal' => $subtotal,
                    'tax_type' => $taxType,
                    'tax_percent' => $taxPercent,
                    'cgst_amount' => $cgst,
                    'sgst_amount' => $sgst,
                    'igst_amount' => 0,
                    'tax_amount' => $totalTax,
                    'round_off' => 0,
                    'total_amount' => $grandTotal,
                    'paid_amount' => 0,
                    'balance_amount' => $grandTotal,
                    'payment_status' => 'unpaid',
                    'status' => 'active',
                    'created_by' => $user ? $user->id : null,
                    'bank_name' => $activeFirm->bank_name ?? 'Bank of Baroda',
                    'bank_account_no' => $activeFirm->bank_account_no ?? '',
                    'bank_ifsc' => $activeFirm->bank_ifsc ?? '',
                    'bank_branch' => $activeFirm->bank_branch ?? 'Dahegam Branch',
                    'terms_conditions' => "1. Official Rent Invoice issued by Delawala Infra Co. (GSTIN: 24CUBPD0770R1ZI).\n2. Subject to Dahegam / Bharuch Jurisdiction.",
                    'notes' => 'Auto-generated from Rental Agreement: ' . ($rental->agreement_no ?: ('RA-' . $rental->id)),
                ]);

                $invoice->items()->create([
                    'item_type' => 'rental',
                    'item_description' => "Monthly Rent for {$propName} (Agreement: " . ($rental->agreement_no ?: ('RA-' . $rental->id)) . ')',
                    'hsn_sac_code' => '9972',
                    'quantity' => 1,
                    'unit' => 'Month',
                    'unit_price' => $subtotal,
                    'discount_amount' => 0,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $totalTax,
                    'total_price' => $grandTotal,
                ]);

                DB::commit();
                return redirect()
                    ->route('invoices.show', $invoice->id)
                    ->with('success', "Rental Tax Invoice #{$invoice->invoice_no} generated automatically!");
            }

            DB::rollBack();
            return back()->with('error', 'Unsupported source type for invoice generation.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error auto-generating invoice: ' . $e->getMessage());
        }
    }

    /**
     * AJAX Endpoint to fetch details for Customer / Tenant / Contractor / Vendor / Project / Sale
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
                    'name' => $entity->name ?? '',
                    'phone' => $entity->mobile ?? '',
                    'email' => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin' => $entity->gst_no ?? '',
                ]);

            case 'tenant':
                $entity = Tenant::find($id);
                return response()->json([
                    'name' => $entity->name ?? '',
                    'phone' => $entity->mobile ?? '',
                    'email' => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin' => $entity->gst_no ?? '',
                ]);

            case 'contractor':
                $entity = Contractor::find($id);
                return response()->json([
                    'name' => $entity->name ?? ($entity->contractor_name ?? ''),
                    'phone' => $entity->mobile ?? ($entity->phone ?? ''),
                    'email' => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin' => $entity->gstin ?? ($entity->gst_number ?? ''),
                ]);

            case 'vendor':
                $entity = Vendor::find($id);
                return response()->json([
                    'name' => $entity->vendor_name ?? ($entity->name ?? ''),
                    'phone' => $entity->phone ?? ($entity->mobile ?? ''),
                    'email' => $entity->email ?? '',
                    'address' => $entity->address ?? '',
                    'gstin' => $entity->gst_number ?? ($entity->gstin ?? ($entity->gst_no ?? '')),
                ]);

            case 'project':
                $project = Project::with('firm')->find($id);
                return response()->json([
                    'firm_id' => $project->firm_id ?? '',
                    'firm_name' => $project->firm->firm_name ?? '',
                    'bank_name' => $project->firm->bank_name ?? '',
                    'bank_account_no' => $project->firm->bank_account_no ?? '',
                    'bank_ifsc' => $project->firm->bank_ifsc ?? '',
                    'bank_branch' => $project->firm->bank_branch ?? '',
                ]);

            case 'property_sale':
                $sale = PropertySale::with(['customer', 'property.project', 'firm'])->find($id);
                if (!$sale)
                    return response()->json(['error' => 'Sale not found'], 404);
                return response()->json([
                    'recipient_name' => $sale->customer->name ?? '',
                    'recipient_phone' => $sale->customer->mobile ?? '',
                    'recipient_email' => $sale->customer->email ?? '',
                    'recipient_address' => $sale->customer->address ?? ($sale->customer->city ?? ''),
                    'recipient_gstin' => $sale->customer->gst_no ?? '',
                    'project_id' => $sale->property?->project_id ?? '',
                    'firm_id' => $sale->firm_id ?? '',
                    'item_description' => 'Property Sale Consideration for ' . ($sale->property_names ?: ($sale->property->property_name ?? 'Unit')),
                    'hsn_sac_code' => '9954',
                    'unit_price' => (float) $sale->sale_amount,
                    'paid_amount' => (float) $sale->booking_amount,
                    'property_sale_id' => $sale->id,
                ]);

            case 'booking':
                $booking = Booking::with(['customer', 'property.project', 'firm'])->find($id);
                if (!$booking)
                    return response()->json(['error' => 'Booking not found'], 404);
                $propName = $booking->property->property_name ?? 'Property Unit';
                $projName = $booking->property?->project?->project_name ?? 'Delawala Project';
                return response()->json([
                    'recipient_name' => $booking->customer->name ?? '',
                    'recipient_phone' => $booking->customer->mobile ?? '',
                    'recipient_email' => $booking->customer->email ?? '',
                    'recipient_address' => $booking->customer->address ?? ($booking->customer->city ?? ''),
                    'recipient_gstin' => $booking->customer->gst_no ?? '',
                    'project_id' => $booking->property?->project_id ?? '',
                    'firm_id' => $booking->firm_id ?? '',
                    'item_description' => "Property Booking & Reservation for {$propName} ({$projName})",
                    'hsn_sac_code' => '9954',
                    'unit_price' => (float) $booking->final_amount,
                    'paid_amount' => (float) $booking->booking_amount,
                ]);

            case 'rental':
                $rental = Rental::with(['tenant', 'property.project', 'firm'])->find($id);
                if (!$rental)
                    return response()->json(['error' => 'Rental not found'], 404);
                $propName = $rental->property->property_name ?? 'Rental Unit';
                return response()->json([
                    'recipient_name' => $rental->tenant_name ?? ($rental->tenant->name ?? ''),
                    'recipient_phone' => $rental->tenant_mobile ?? ($rental->tenant->mobile ?? ''),
                    'recipient_email' => $rental->tenant->email ?? '',
                    'recipient_address' => $rental->tenant->address ?? '',
                    'recipient_gstin' => $rental->tenant->gst_no ?? '',
                    'project_id' => $rental->property?->project_id ?? '',
                    'firm_id' => $rental->firm_id ?? '',
                    'item_description' => "Monthly Rent for {$propName} (Agreement: " . ($rental->agreement_no ?: ('RA-' . $rental->id)) . ')',
                    'hsn_sac_code' => '9972',
                    'unit_price' => (float) $rental->monthly_rent,
                    'paid_amount' => 0,
                    'rental_id' => $rental->id,
                ]);

            default:
                return response()->json(['error' => 'Unknown type'], 404);
        }
    }
}
