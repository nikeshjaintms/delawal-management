@extends('admin.layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_no . ' - Delawala Management')
@section('page-title', 'Invoice Details')

@section('content')
<style>
.inv-show-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 30px;
}
@media (max-width: 992px) {
    .inv-show-grid { grid-template-columns: 1fr; }
}

.inv-detail-card {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35);
}

.inv-brand-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 20px;
}
.inv-firm-title {
    font-size: 24px;
    font-weight: 800;
    color: #FFFFFF;
    letter-spacing: -0.3px;
    margin-bottom: 6px;
}
.inv-no-badge {
    font-size: 20px;
    font-weight: 800;
    color: #60A5FA;
    letter-spacing: 0.5px;
}

.inv-address-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 28px;
}
@media (max-width: 600px) {
    .inv-address-grid { grid-template-columns: 1fr; }
}
.addr-block-title {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.addr-name { font-size: 16px; font-weight: 700; color: #FFFFFF; margin-bottom: 4px; }
.addr-text { font-size: 13.5px; color: #CBD5E1; line-height: 1.5; }

/* Table in Show */
.show-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 24px;
}
.show-table th {
    background: rgba(15, 23, 42, 0.90);
    color: #94A3B8;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.show-table td {
    padding: 14px;
    font-size: 13.5px;
    color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    vertical-align: middle;
}

.summary-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.summary-table td {
    padding: 8px 12px;
    font-size: 13.5px;
    color: #CBD5E1;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.summary-table tr.grand td {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF;
    border-top: 2px solid rgba(255, 255, 255, 0.18);
    border-bottom: none;
    padding-top: 14px;
}
.summary-table tr.grand td.val { color: #60A5FA; }

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

.btn-print {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 20px !important;
    min-height: 42px !important;
    background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%) !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(129, 140, 248, 0.50) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(99, 102, 241, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.btn-print:hover {
    background: linear-gradient(135deg, #4F46E5 0%, #4338CA 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.55) !important;
    color: #FFFFFF !important;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}
.modal-content-box {
    background: #141B29;
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-radius: 20px;
    padding: 28px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.60);
}
</style>

{{-- Breadcrumbs & Header --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-file-invoice-dollar" style="color: #60A5FA; margin-right: 6px;"></i>Invoices</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    <a href="{{ route('invoices.index') }}">All Invoices</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">{{ $invoice->invoice_no }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $invoice->invoice_no }}</h2>
        <p>Issued on {{ $invoice->invoice_date->format('d M, Y') }} &bull; {{ $invoice->type_label }}</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="btn-print">
            <i class="fa-solid fa-print"></i> Print / PDF Invoice
        </a>
        <button type="button" class="btn-gold" onclick="openPaymentModal()">
            <i class="fa-solid fa-money-bill-wave"></i> Record Payment
        </button>
        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-primary-custom">
            <i class="fa-solid fa-pen-to-square"></i> Edit
        </a>
        <a href="{{ route('invoices.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="inv-show-grid">
    <!-- LEFT: INVOICE MAIN DOSSIER -->
    <div class="inv-detail-card">
        <div class="inv-brand-header">
            <div>
                <div class="inv-firm-title">{{ $invoice->firm->firm_name ?? 'Delawala Management' }}</div>
                <div style="font-size: 13px; color: #94A3B8; max-width: 380px;">
                    {{ $invoice->firm->address ?? '' }} {{ $invoice->firm->city ? ', ' . $invoice->firm->city : '' }}
                </div>
                @if($invoice->firm && $invoice->firm->gst_number)
                    <div style="font-size: 12px; color: #60A5FA; font-weight: 700; margin-top: 4px;">
                        GSTIN: {{ $invoice->firm->gst_number }}
                    </div>
                @endif
            </div>

            <div style="text-align: right;">
                <div class="addr-block-title">TAX INVOICE</div>
                <div class="inv-no-badge">{{ $invoice->invoice_no }}</div>
                <div style="font-size: 13px; color: #CBD5E1; margin-top: 4px;">
                    Date: <strong>{{ $invoice->invoice_date->format('d M, Y') }}</strong>
                </div>
                @if($invoice->due_date)
                    <div style="font-size: 12.5px; color: #94A3B8;">
                        Due Date: {{ $invoice->due_date->format('d M, Y') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Project & Recipient details -->
        <div class="inv-address-grid">
            <div>
                <div class="addr-block-title"><i class="fa-solid fa-user-check" style="color: #60A5FA;"></i> Bill To:</div>
                <div class="addr-name">{{ $invoice->recipient_name }}</div>
                <div class="addr-text">
                    @if($invoice->recipient_phone)
                        <div><i class="fa-solid fa-phone" style="font-size: 11px;"></i> {{ $invoice->recipient_phone }}</div>
                    @endif
                    @if($invoice->recipient_email)
                        <div><i class="fa-solid fa-envelope" style="font-size: 11px;"></i> {{ $invoice->recipient_email }}</div>
                    @endif
                    @if($invoice->recipient_address)
                        <div><i class="fa-solid fa-location-dot" style="font-size: 11px;"></i> {{ $invoice->recipient_address }}</div>
                    @endif
                    @if($invoice->recipient_gstin)
                        <div style="color: #93C5FD; font-weight: 700; margin-top: 2px;">GSTIN: {{ $invoice->recipient_gstin }}</div>
                    @endif
                </div>
            </div>

            <div>
                <div class="addr-block-title"><i class="fa-solid fa-city" style="color: #34D399;"></i> Project / Subject:</div>
                @if($invoice->project)
                    <div class="addr-name">
                        <a href="{{ route('projects.show', $invoice->project_id) }}" style="color: #60A5FA; text-decoration: none;">
                            {{ $invoice->project->project_name }}
                        </a>
                    </div>
                    <div class="addr-text">
                        <div>Code: <code class="code-chip">{{ $invoice->project->project_code }}</code></div>
                        <div>Type: {{ ucfirst($invoice->project->project_type ?: 'Plotted Development') }}</div>
                        <div>{{ $invoice->project->display_address }}</div>
                    </div>
                @else
                    <div class="addr-name" style="color: #CBD5E1;">Standalone / General Invoice</div>
                    <div class="addr-text">No specific project linked</div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <div style="overflow-x: auto;">
            <table class="show-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Item Description</th>
                        <th>HSN/SAC</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: right;">Rate</th>
                        <th style="text-align: right;">Discount</th>
                        <th style="text-align: right;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $i => $item)
                        <tr>
                            <td style="color: #64748B; font-weight: 700;">{{ $i + 1 }}</td>
                            <td>
                                <div style="font-weight: 700; color: #FFFFFF;">{{ $item->item_description }}</div>
                                <span class="badge-inv badge-general" style="font-size: 10px; padding: 2px 6px; margin-top: 3px;">
                                    {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 12px;">{{ $item->hsn_sac_code ?: '—' }}</td>
                            <td style="text-align: right; font-weight: 600;">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                            <td style="text-align: right; font-weight: 600;">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align: right; color: #FBBF24;">{{ $item->discount_amount > 0 ? '₹' . number_format($item->discount_amount, 2) : '—' }}</td>
                            <td style="text-align: right; font-weight: 800; color: #FFFFFF;">₹{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Bank Details & Notes -->
        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.08); display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <div class="addr-block-title"><i class="fa-solid fa-building-columns"></i> Bank Account Details</div>
                <div style="font-size: 13px; color: #CBD5E1; line-height: 1.6;">
                    <div>Bank: <strong>{{ $invoice->bank_name ?: ($invoice->firm->bank_name ?? '—') }}</strong></div>
                    <div>A/C No: <strong>{{ $invoice->bank_account_no ?: ($invoice->firm->bank_account_no ?? '—') }}</strong></div>
                    <div>IFSC: <strong>{{ $invoice->bank_ifsc ?: ($invoice->firm->bank_ifsc ?? '—') }}</strong></div>
                    @if($invoice->bank_branch || ($invoice->firm && $invoice->firm->bank_branch))
                        <div>Branch: <strong>{{ $invoice->bank_branch ?: $invoice->firm->bank_branch }}</strong></div>
                    @endif
                </div>
            </div>

            <div>
                <div class="addr-block-title"><i class="fa-solid fa-file-lines"></i> Terms &amp; Notes</div>
                <div style="font-size: 12.5px; color: #94A3B8; white-space: pre-line;">
                    {{ $invoice->terms_conditions ?: 'Payment due as per agreed milestone schedule.' }}
                </div>
                @if($invoice->notes)
                    <div style="margin-top: 8px; font-size: 12.5px; color: #60A5FA;">
                        <strong>Note:</strong> {{ $invoice->notes }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: FINANCIAL BREAKDOWN & PAYMENT TRACKER -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Financial Summary -->
        <div class="inv-detail-card" style="padding: 22px;">
            <div style="font-size: 15px; font-weight: 800; color: #FFFFFF; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: #60A5FA;"></i> Financial Summary
            </div>

            <table class="summary-table">
                <tr>
                    <td>Subtotal</td>
                    <td style="text-align: right; font-weight: 700; color: #FFFFFF;">₹{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td>Discount ({{ $invoice->discount_type == 'percentage' ? $invoice->discount_value . '%' : 'Fixed' }})</td>
                        <td style="text-align: right; color: #FBBF24;">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->tax_type === 'gst_intra' && $invoice->tax_amount > 0)
                    <tr>
                        <td>CGST ({{ $invoice->tax_percent / 2 }}%)</td>
                        <td style="text-align: right; color: #CBD5E1;">+₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST ({{ $invoice->tax_percent / 2 }}%)</td>
                        <td style="text-align: right; color: #CBD5E1;">+₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                    </tr>
                @elseif($invoice->tax_type === 'gst_inter' && $invoice->tax_amount > 0)
                    <tr>
                        <td>IGST ({{ $invoice->tax_percent }}%)</td>
                        <td style="text-align: right; color: #CBD5E1;">+₹{{ number_format($invoice->igst_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->round_off != 0)
                    <tr>
                        <td>Round Off</td>
                        <td style="text-align: right; color: #94A3B8;">{{ $invoice->round_off > 0 ? '+' : '' }}₹{{ number_format($invoice->round_off, 2) }}</td>
                    </tr>
                @endif
                <tr class="grand">
                    <td>Grand Total</td>
                    <td class="val" style="text-align: right;">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #34D399; font-weight: 700;">Paid Amount</td>
                    <td style="text-align: right; color: #34D399; font-weight: 800;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: {{ $invoice->balance_amount > 0 ? '#F87171' : '#94A3B8' }}; font-weight: 700;">Balance Due</td>
                    <td style="text-align: right; color: {{ $invoice->balance_amount > 0 ? '#F87171' : '#94A3B8' }}; font-weight: 800; font-size: 16px;">
                        ₹{{ number_format($invoice->balance_amount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td>Payment Status</td>
                    <td style="text-align: right;">
                        <span class="badge-inv {{ $invoice->payment_badge_class }}">
                            {{ str_replace('_', ' ', $invoice->payment_status) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Payments Ledger -->
        <div class="inv-detail-card" style="padding: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div style="font-size: 15px; font-weight: 800; color: #FFFFFF; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-money-bill-transfer" style="color: #34D399;"></i> Payment History
                </div>
                <button type="button" class="btn-gold" style="font-size: 12px; padding: 6px 12px;" onclick="openPaymentModal()">
                    <i class="fa-solid fa-plus"></i> Add
                </button>
            </div>

            @if($invoice->payments->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($invoice->payments as $pmt)
                        <div style="background: rgba(15, 23, 42, 0.60); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 12px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-size: 14px; font-weight: 800; color: #34D399;">
                                    ₹{{ number_format($pmt->amount, 2) }}
                                </div>
                                <div style="font-size: 11.5px; color: #94A3B8;">
                                    {{ $pmt->payment_date->format('d M, Y') }} &bull; {{ $pmt->payment_mode }}
                                    @if($pmt->transaction_reference)
                                        &bull; Ref: {{ $pmt->transaction_reference }}
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('invoices.payments.destroy', [$invoice->id, $pmt->id]) }}" method="POST" onsubmit="return confirm('Delete this payment entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-act btn-act-del" style="width: 28px; height: 28px; font-size: 11px;" title="Delete Payment">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 20px 10px; color: #64748B; font-size: 13px;">
                    <i class="fa-solid fa-clock-rotate-left" style="font-size: 24px; margin-bottom: 6px; display: block;"></i>
                    No payments recorded yet.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- RECORD PAYMENT MODAL -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal-content-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);">
            <div style="font-size: 18px; font-weight: 800; color: #FFFFFF;">
                <i class="fa-solid fa-money-bill-wave" style="color: #34D399; margin-right: 6px;"></i> Record Payment
            </div>
            <button type="button" onclick="closePaymentModal()" style="background: none; border: none; color: #94A3B8; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('invoices.payments.store', $invoice->id) }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Payment Amount (₹) <span class="req">*</span></label>
                <input type="number" step="0.01" min="0.01" max="{{ $invoice->balance_amount > 0 ? $invoice->balance_amount : 99999999 }}" name="amount" value="{{ $invoice->balance_amount }}" class="f-control" required>
                <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">Remaining Balance: ₹{{ number_format($invoice->balance_amount, 2) }}</div>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Date <span class="req">*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="f-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode_id" class="f-control">
                    @foreach($paymentModes as $pm)
                        <option value="{{ $pm->id }}" {{ strtolower($pm->name) == 'cash' ? 'selected' : '' }}>
                            {{ $pm->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Transaction / Cheque Reference</label>
                <input type="text" name="transaction_reference" class="f-control" placeholder="e.g. UTR12345678 or Chq #00123">
            </div>

            <div class="form-group">
                <label class="form-label">Notes / Remarks</label>
                <textarea name="notes" class="f-control" rows="2" placeholder="Optional notes..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px;">
                <button type="button" class="btn-secondary-custom" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-check"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}
function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}
</script>
@endsection
