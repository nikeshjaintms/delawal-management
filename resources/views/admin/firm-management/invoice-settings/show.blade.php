@extends('admin.layouts.app')
@section('title','Invoice Settings — Details')
@section('page-title','Firm Management')

@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.header-actions{display:flex;gap:10px;flex-wrap:wrap}
.detail-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.detail-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.detail-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
@media(max-width:768px){.detail-grid,.detail-grid-2{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.detail-grid,.detail-grid-2{grid-template-columns:1fr}}
.detail-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:5px}
.detail-value{font-size:15px;font-weight:600;color:var(--text-primary)}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.invoice-sample{display:flex;flex-direction:column;gap:4px}
.inv-row{display:flex;align-items:center;gap:10px}
.inv-label{font-size:12px;color:var(--text-secondary);width:100px;font-weight:600}
.inv-val{font-family:monospace;font-size:13px;font-weight:700;background:var(--blue-light);color:var(--blue);border-radius:6px;padding:3px 10px}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Invoice Number Series</h2>
        <p>Configuration details and sample invoice numbers.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('invoice-settings.edit', $invoiceSetting) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
        <a href="{{ route('invoice-settings.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>

{{-- General --}}
<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-gear"></i> General Settings</div>
    <div class="detail-grid-2">
        <div><div class="detail-label">Financial Year</div><div class="detail-value">{{ $invoiceSetting->financialYear->year_name ?? '—' }}</div></div>
        <div><div class="detail-label">Status</div><div class="detail-value"><span class="badge badge-{{ $invoiceSetting->status }}">{{ ucfirst($invoiceSetting->status) }}</span></div></div>
        <div><div class="detail-label">Starting Number</div><div class="detail-value">{{ $invoiceSetting->starting_number }}</div></div>
        <div><div class="detail-label">Current Number</div><div class="detail-value">{{ $invoiceSetting->current_number }}</div></div>
    </div>
</div>

{{-- Prefix & Sample --}}
<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-hashtag"></i> Invoice Prefixes &amp; Samples</div>
    @php
        $fy = $invoiceSetting->financialYear;
        $year = $fy ? substr($fy->year_name, 0, 4) : date('Y');
        $num = str_pad($invoiceSetting->current_number, 4, '0', STR_PAD_LEFT);
        $prefixes = [
            'Sales'    => $invoiceSetting->sales_prefix,
            'Purchase' => $invoiceSetting->purchase_prefix,
            'Booking'  => $invoiceSetting->booking_prefix,
            'Rental'   => $invoiceSetting->rental_prefix,
            'Payment'  => $invoiceSetting->payment_prefix,
            'Receipt'  => $invoiceSetting->receipt_prefix,
            'Expense'  => $invoiceSetting->expense_prefix,
            'Income'   => $invoiceSetting->income_prefix,
            'Loan'     => $invoiceSetting->loan_prefix,
        ];
    @endphp
    <div class="detail-grid">
        @foreach($prefixes as $label => $prefix)
        <div>
            <div class="detail-label">{{ $label }}</div>
            <div class="detail-value" style="font-family:monospace">{{ $prefix }}</div>
            <div style="font-size:11.5px;color:var(--text-secondary);margin-top:3px">
                Sample: <strong style="color:var(--blue)">{{ $prefix }}-{{ $year }}-{{ $num }}</strong>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
