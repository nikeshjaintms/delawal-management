@extends('admin.layouts.app')

@section('title', 'Edit Invoice ' . $invoice->invoice_no . ' - Delawala Management')
@section('page-title', 'Edit Invoice')

@section('content')
<style>
.form-section-card {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30);
}
.section-head {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 800;
    color: #FFFFFF;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.section-head i { color: #60A5FA; font-size: 18px; }
.form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
.form-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

@media (max-width: 992px) { .form-grid-3, .form-grid-4 { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .form-grid-2, .form-grid-3, .form-grid-4 { grid-template-columns: 1fr; } }

.form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
.form-label { font-size: 12.5px; font-weight: 700; color: #CBD5E1; text-transform: uppercase; letter-spacing: 0.5px; }
.form-label .req { color: #EF4444; }

.f-control {
    background: rgba(15, 23, 42, 0.85) !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    color: #FFFFFF !important;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13.5px;
    outline: none;
    transition: all 0.2s ease;
}
.f-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.20); }
.f-control option { background: #0F172A; color: #FFFFFF; }

.items-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 16px; }
.items-table th {
    background: rgba(15, 23, 42, 0.90);
    color: #94A3B8;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 12px 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.items-table td { padding: 10px 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); vertical-align: middle; }
.btn-add-item {
    background: rgba(59, 130, 246, 0.15);
    color: #60A5FA;
    border: 1px dashed rgba(59, 130, 246, 0.40);
    border-radius: 10px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-remove-row {
    background: rgba(239, 68, 68, 0.15);
    color: #F87171;
    border: 1px solid rgba(239, 68, 68, 0.30);
    border-radius: 8px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.calc-summary-card {
    background: rgba(15, 23, 42, 0.75);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    padding: 20px;
    margin-left: auto;
    max-width: 480px;
}
.calc-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 13.5px;
    color: #CBD5E1;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.calc-row.grand-total {
    border-top: 2px solid rgba(255, 255, 255, 0.18);
    border-bottom: none;
    padding-top: 14px;
    margin-top: 8px;
    font-size: 18px;
    font-weight: 800;
    color: #FFFFFF;
}
.calc-row.grand-total .val { color: #60A5FA; }

.breadcrumb-nav {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(20, 27, 41, 0.60);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 12px;
    font-size: 13px;
    color: #94A3B8;
    font-weight: 600;
    margin-bottom: 20px;
}
.breadcrumb-nav a { color: #60A5FA; text-decoration: none; font-weight: 700; transition: color 0.15s ease; }
.breadcrumb-nav a:hover { color: #93C5FD; }
.breadcrumb-nav .separator { font-size: 10px; color: #64748B; }
.breadcrumb-nav .active { color: #FFFFFF; font-weight: 700; }

.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.crud-title h2 {
    font-size: 26px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}
.crud-title p {
    font-size: 13.5px;
    color: #CBD5E1 !important;
    font-weight: 500;
    margin: 0;
}

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom, .btn-gold {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 22px !important;
    min-height: 42px !important;
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(96, 165, 250, 0.50) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.btn-primary-custom:hover, .btn-gold:hover {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border-color: #60A5FA !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.55) !important;
    color: #FFFFFF !important;
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom, .btn-outline, .btn-cancel, a.btn-cancel {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 20px !important;
    min-height: 42px !important;
    background: rgba(30, 41, 59, 0.85) !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(255, 255, 255, 0.20) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    white-space: nowrap !important;
}
.btn-secondary-custom:hover, .btn-outline:hover, .btn-cancel:hover {
    background: rgba(51, 65, 85, 0.95) !important;
    border-color: rgba(255, 255, 255, 0.38) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35) !important;
    color: #FFFFFF !important;
}
</style>

{{-- Breadcrumbs & Header --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-file-invoice-dollar" style="color: #60A5FA; margin-right: 6px;"></i>Invoices</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    <a href="{{ route('invoices.index') }}">All Invoices</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <a href="{{ route('invoices.show', $invoice->id) }}">{{ $invoice->invoice_no }}</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">Edit</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Invoice: {{ $invoice->invoice_no }}</h2>
        <p>Modify line items, discounts, taxes and billing information</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('invoices.index') }}" class="btn-cancel btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> All Invoices
        </a>
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-secondary-custom">
            <i class="fa-solid fa-eye"></i> View Invoice
        </a>
    </div>
</div>

<form action="{{ route('invoices.update', $invoice->id) }}" method="POST" id="invoiceEditForm">
    @csrf
    @method('PUT')

    <!-- SECTION 1: INVOICE CONFIG -->
    <div class="form-section-card">
        <div class="section-head">
            <i class="fa-solid fa-layer-group"></i> 1. Invoice Type &amp; Project Association
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Firm <span class="req">*</span></label>
                <select name="firm_id" class="f-control" required>
                    @foreach($firms as $f)
                        <option value="{{ $f->id }}" {{ old('firm_id', $invoice->firm_id) == $f->id ? 'selected' : '' }}>
                            {{ $f->firm_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Project</label>
                <select name="project_id" class="f-control">
                    <option value="">-- No Specific Project (General) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $invoice->project_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->project_name }} ({{ $p->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Invoice Type <span class="req">*</span></label>
                <select name="invoice_type" class="f-control" required>
                    <option value="sale" {{ old('invoice_type', $invoice->invoice_type) == 'sale' ? 'selected' : '' }}>Property / Plot Sale</option>
                    <option value="rental" {{ old('invoice_type', $invoice->invoice_type) == 'rental' ? 'selected' : '' }}>Rental / Lease</option>
                    <option value="contractor" {{ old('invoice_type', $invoice->invoice_type) == 'contractor' ? 'selected' : '' }}>Contractor / Labour</option>
                    <option value="material_purchase" {{ old('invoice_type', $invoice->invoice_type) == 'material_purchase' ? 'selected' : '' }}>Material / Purchase</option>
                    <option value="custom" {{ old('invoice_type', $invoice->invoice_type) == 'custom' ? 'selected' : '' }}>General Custom</option>
                </select>
            </div>
        </div>

        <div class="form-grid-4">
            <div class="form-group">
                <label class="form-label">Invoice Number <span class="req">*</span></label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no', $invoice->invoice_no) }}" class="f-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Invoice Date <span class="req">*</span></label>
                <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" class="f-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Due Date</label>
                <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}" class="f-control">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="f-control">
                    <option value="issued" {{ old('status', $invoice->status) == 'issued' ? 'selected' : '' }}>Issued (Final)</option>
                    <option value="draft" {{ old('status', $invoice->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ old('status', $invoice->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SECTION 2: RECIPIENT -->
    <div class="form-section-card">
        <div class="section-head">
            <i class="fa-solid fa-user-check"></i> 2. Bill To / Recipient Details
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Recipient Name <span class="req">*</span></label>
                <input type="text" name="recipient_name" value="{{ old('recipient_name', $invoice->recipient_name) }}" class="f-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone / Mobile</label>
                <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $invoice->recipient_phone) }}" class="f-control">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="recipient_email" value="{{ old('recipient_email', $invoice->recipient_email) }}" class="f-control">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Billing Address</label>
                <textarea name="recipient_address" class="f-control" rows="2">{{ old('recipient_address', $invoice->recipient_address) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Recipient GSTIN</label>
                <input type="text" name="recipient_gstin" value="{{ old('recipient_gstin', $invoice->recipient_gstin) }}" class="f-control">
            </div>
        </div>
    </div>

    <!-- SECTION 3: LINE ITEMS -->
    <div class="form-section-card">
        <div class="section-head" style="justify-content: space-between;">
            <div><i class="fa-solid fa-list-check"></i> 3. Line Items &amp; Charges</div>
            <button type="button" class="btn-add-item" onclick="addItemRow()">
                <i class="fa-solid fa-plus"></i> Add Item Row
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="items-table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 140px;">Item Type</th>
                        <th>Item Description &amp; Specifications</th>
                        <th style="width: 100px;">HSN/SAC</th>
                        <th style="width: 90px;">Qty</th>
                        <th style="width: 100px;">Unit</th>
                        <th style="width: 120px;">Unit Price (₹)</th>
                        <th style="width: 100px;">Discount (₹)</th>
                        <th style="width: 130px; text-align: right;">Total (₹)</th>
                        <th style="width: 50px; text-align: center;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    @foreach($invoice->items as $idx => $item)
                        <tr class="item-row" data-index="{{ $idx }}">
                            <td>
                                <select name="items[{{ $idx }}][item_type]" class="f-control item-type">
                                    <option value="plot_unit" {{ $item->item_type == 'plot_unit' ? 'selected' : '' }}>Plot / Unit</option>
                                    <option value="rent_month" {{ $item->item_type == 'rent_month' ? 'selected' : '' }}>Rent / Lease</option>
                                    <option value="labour_work" {{ $item->item_type == 'labour_work' ? 'selected' : '' }}>Labour Work</option>
                                    <option value="material" {{ $item->item_type == 'material' ? 'selected' : '' }}>Material</option>
                                    <option value="service" {{ $item->item_type == 'service' ? 'selected' : '' }}>Service Charge</option>
                                    <option value="custom" {{ $item->item_type == 'custom' ? 'selected' : '' }}>Custom Item</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $idx }}][item_description]" value="{{ $item->item_description }}" class="f-control item-desc" required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $idx }}][hsn_sac_code]" value="{{ $item->hsn_sac_code }}" class="f-control item-hsn">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][quantity]" class="f-control item-qty" value="{{ $item->quantity }}" oninput="calculateTotals()" required>
                            </td>
                            <td>
                                <input type="text" name="items[{{ $idx }}][unit]" class="f-control item-unit" value="{{ $item->unit }}">
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][unit_price]" class="f-control item-price" value="{{ $item->unit_price }}" oninput="calculateTotals()" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0" name="items[{{ $idx }}][discount_amount]" class="f-control item-discount" value="{{ $item->discount_amount }}" oninput="calculateTotals()">
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #FFFFFF; font-size: 14px;">
                                <span class="row-total-display">₹{{ number_format($item->total_price, 2) }}</span>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Remove Item">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn-add-item" onclick="addItemRow()">
            <i class="fa-solid fa-plus"></i> Add Item Row
        </button>

        <!-- Calculations -->
        <div style="margin-top: 24px; display: flex; justify-content: flex-end;">
            <div class="calc-summary-card">
                <div class="calc-row">
                    <span>Subtotal</span>
                    <strong id="subtotalDisplay">₹{{ number_format($invoice->subtotal, 2) }}</strong>
                </div>

                <div class="calc-row">
                    <span>Global Discount</span>
                    <div style="display: flex; gap: 6px; align-items: center;">
                        <select name="discount_type" id="discountType" class="f-control" style="padding: 4px 8px; width: 80px;" onchange="calculateTotals()">
                            <option value="fixed" {{ $invoice->discount_type == 'fixed' ? 'selected' : '' }}>₹</option>
                            <option value="percentage" {{ $invoice->discount_type == 'percentage' ? 'selected' : '' }}>%</option>
                        </select>
                        <input type="number" step="0.01" min="0" name="discount_value" id="discountValue" value="{{ $invoice->discount_value }}" class="f-control" style="width: 100px; padding: 4px 8px;" oninput="calculateTotals()">
                    </div>
                </div>

                <div class="calc-row">
                    <span>Tax Treatment (GST)</span>
                    <select name="tax_type" id="taxType" class="f-control" style="padding: 4px 8px; width: 170px;" onchange="calculateTotals()">
                        <option value="none" {{ $invoice->tax_type == 'none' ? 'selected' : '' }}>No Tax (0%)</option>
                        <option value="gst_intra" {{ $invoice->tax_type == 'gst_intra' ? 'selected' : '' }}>Intra-State (CGST + SGST)</option>
                        <option value="gst_inter" {{ $invoice->tax_type == 'gst_inter' ? 'selected' : '' }}>Inter-State (IGST)</option>
                    </select>
                </div>

                <div class="calc-row" id="taxPercentRow">
                    <span>GST Rate (%)</span>
                    <select name="tax_percent" id="taxPercent" class="f-control" style="padding: 4px 8px; width: 100px;" onchange="calculateTotals()">
                        <option value="0" {{ (int)$invoice->tax_percent == 0 ? 'selected' : '' }}>0%</option>
                        <option value="5" {{ (int)$invoice->tax_percent == 5 ? 'selected' : '' }}>5%</option>
                        <option value="12" {{ (int)$invoice->tax_percent == 12 ? 'selected' : '' }}>12%</option>
                        <option value="18" {{ (int)$invoice->tax_percent == 18 ? 'selected' : '' }}>18%</option>
                        <option value="28" {{ (int)$invoice->tax_percent == 28 ? 'selected' : '' }}>28%</option>
                    </select>
                </div>

                <div class="calc-row" id="cgstRow" style="font-size: 12.5px; color: #94A3B8;">
                    <span>CGST</span>
                    <span id="cgstDisplay">₹{{ number_format($invoice->cgst_amount, 2) }}</span>
                </div>
                <div class="calc-row" id="sgstRow" style="font-size: 12.5px; color: #94A3B8;">
                    <span>SGST</span>
                    <span id="sgstDisplay">₹{{ number_format($invoice->sgst_amount, 2) }}</span>
                </div>
                <div class="calc-row" id="igstRow" style="display: none; font-size: 12.5px; color: #94A3B8;">
                    <span>IGST</span>
                    <span id="igstDisplay">₹{{ number_format($invoice->igst_amount, 2) }}</span>
                </div>

                <div class="calc-row">
                    <span>Round Off</span>
                    <input type="number" step="0.01" name="round_off" id="roundOffInput" value="{{ $invoice->round_off }}" class="f-control" style="width: 100px; padding: 4px 8px;" oninput="calculateTotals()">
                </div>

                <div class="calc-row grand-total">
                    <span>Grand Total</span>
                    <span class="val" id="grandTotalDisplay">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 4: BANK DETAILS & TERMS -->
    <div class="form-section-card">
        <div class="section-head">
            <i class="fa-solid fa-building-columns"></i> 4. Bank Account &amp; Terms
        </div>

        <div class="form-grid-4">
            <div class="form-group">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $invoice->bank_name) }}" class="f-control">
            </div>
            <div class="form-group">
                <label class="form-label">Account Number</label>
                <input type="text" name="bank_account_no" value="{{ old('bank_account_no', $invoice->bank_account_no) }}" class="f-control">
            </div>
            <div class="form-group">
                <label class="form-label">IFSC Code</label>
                <input type="text" name="bank_ifsc" value="{{ old('bank_ifsc', $invoice->bank_ifsc) }}" class="f-control">
            </div>
            <div class="form-group">
                <label class="form-label">Branch</label>
                <input type="text" name="bank_branch" value="{{ old('bank_branch', $invoice->bank_branch) }}" class="f-control">
            </div>
        </div>

        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Terms &amp; Conditions</label>
                <textarea name="terms_conditions" class="f-control" rows="3">{{ old('terms_conditions', $invoice->terms_conditions) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Notes / Remarks</label>
                <textarea name="notes" class="f-control" rows="3">{{ old('notes', $invoice->notes) }}</textarea>
            </div>
        </div>
    </div>

    <!-- ACTIONS -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; margin-bottom: 40px;">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-secondary-custom">Cancel</a>
        <button type="submit" class="btn-gold" style="font-size: 14px; padding: 12px 28px;">
            <i class="fa-solid fa-floppy-disk"></i> Update Invoice
        </button>
    </div>
</form>

<script>
let rowIndex = {{ $invoice->items->count() }};

function addItemRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.index = rowIndex;

    tr.innerHTML = `
        <td>
            <select name="items[${rowIndex}][item_type]" class="f-control item-type">
                <option value="plot_unit">Plot / Unit</option>
                <option value="rent_month">Rent / Lease</option>
                <option value="labour_work">Labour Work</option>
                <option value="material">Material</option>
                <option value="service">Service Charge</option>
                <option value="custom" selected>Custom Item</option>
            </select>
        </td>
        <td>
            <input type="text" name="items[${rowIndex}][item_description]" class="f-control item-desc" placeholder="Description of property, material or service" required>
        </td>
        <td>
            <input type="text" name="items[${rowIndex}][hsn_sac_code]" class="f-control item-hsn" placeholder="997212">
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="f-control item-qty" value="1" oninput="calculateTotals()" required>
        </td>
        <td>
            <input type="text" name="items[${rowIndex}][unit]" class="f-control item-unit" value="Nos" placeholder="Sq.Ft, Nos, Month">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" class="f-control item-price" value="0.00" oninput="calculateTotals()" required>
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${rowIndex}][discount_amount]" class="f-control item-discount" value="0.00" oninput="calculateTotals()">
        </td>
        <td style="text-align: right; font-weight: 700; color: #FFFFFF; font-size: 14px;">
            <span class="row-total-display">₹0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" class="btn-remove-row" onclick="removeRow(this)" title="Remove Item">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    rowIndex++;
    calculateTotals();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) {
        alert('At least one item is required in the invoice.');
        return;
    }
    btn.closest('tr').remove();
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const discount = parseFloat(row.querySelector('.item-discount').value) || 0;

        const rowTotal = Math.max(0, (qty * price) - discount);
        row.querySelector('.row-total-display').textContent = '₹' + rowTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        subtotal += rowTotal;
    });

    document.getElementById('subtotalDisplay').textContent = '₹' + subtotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const discType = document.getElementById('discountType').value;
    const discVal = parseFloat(document.getElementById('discountValue').value) || 0;
    let totalDiscount = discType === 'percentage' ? (subtotal * discVal) / 100 : discVal;

    const taxable = Math.max(0, subtotal - totalDiscount);

    const taxType = document.getElementById('taxType').value;
    const taxPercent = parseFloat(document.getElementById('taxPercent').value) || 0;

    let cgst = 0, sgst = 0, igst = 0, totalTax = 0;

    if (taxType === 'gst_intra' && taxPercent > 0) {
        totalTax = (taxable * taxPercent) / 100;
        cgst = totalTax / 2;
        sgst = totalTax / 2;

        document.getElementById('taxPercentRow').style.display = 'flex';
        document.getElementById('cgstRow').style.display = 'flex';
        document.getElementById('sgstRow').style.display = 'flex';
        document.getElementById('igstRow').style.display = 'none';

        document.getElementById('cgstRow').querySelector('span:first-child').textContent = `CGST (${(taxPercent/2).toFixed(1)}%)`;
        document.getElementById('sgstRow').querySelector('span:first-child').textContent = `SGST (${(taxPercent/2).toFixed(1)}%)`;
        document.getElementById('cgstDisplay').textContent = '₹' + cgst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('sgstDisplay').textContent = '₹' + sgst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else if (taxType === 'gst_inter' && taxPercent > 0) {
        totalTax = (taxable * taxPercent) / 100;
        igst = totalTax;

        document.getElementById('taxPercentRow').style.display = 'flex';
        document.getElementById('cgstRow').style.display = 'none';
        document.getElementById('sgstRow').style.display = 'none';
        document.getElementById('igstRow').style.display = 'flex';

        document.getElementById('igstRow').querySelector('span:first-child').textContent = `IGST (${taxPercent.toFixed(1)}%)`;
        document.getElementById('igstDisplay').textContent = '₹' + igst.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    } else {
        document.getElementById('taxPercentRow').style.display = 'none';
        document.getElementById('cgstRow').style.display = 'none';
        document.getElementById('sgstRow').style.display = 'none';
        document.getElementById('igstRow').style.display = 'none';
    }

    const roundOff = parseFloat(document.getElementById('roundOffInput').value) || 0;
    const grandTotal = Math.max(0, taxable + totalTax + roundOff);

    document.getElementById('grandTotalDisplay').textContent = '₹' + grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>
@endsection
