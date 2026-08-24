@extends('admin.layouts.app')
@section('title','Invoice Settings — Details')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.header-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.detail-card, .card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 26px 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1px; color: #60A5FA !important; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; align-items: center; gap: 10px;
}
.section-heading i { color: #3B82F6; font-size: 14px; }

.detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.detail-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 768px) { .detail-grid, .detail-grid-2 { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .detail-grid, .detail-grid-2 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important; border-radius: 14px;
    transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.35) !important; background: rgba(16, 22, 34, 0.85) !important; }

.detail-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.8px; color: #94A3B8 !important; margin-bottom: 6px;
    display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.sample-chip {
    font-family: monospace; font-size: 12.5px; font-weight: 700;
    color: #60A5FA !important; background: rgba(59, 130, 246, 0.15);
    padding: 3px 8px; border-radius: 6px; border: 1px solid rgba(59, 130, 246, 0.30);
    display: inline-block; margin-top: 4px;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Invoice Number Series</h2>
        <p>Configuration details and sample invoice numbers.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('invoice-settings.edit', $invoiceSetting) }}" class="btn-primary-custom"><i class="fa fa-edit"></i> Edit</a>
        <a href="{{ route('invoice-settings.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

{{-- General --}}
<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-gear"></i> General Settings</div>
    <div class="detail-grid-2">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Assigned Firms</div>
            <div class="detail-value">{{ $invoiceSetting->firm_names }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Financial Year</div>
            <div class="detail-value">{{ $invoiceSetting->financialYear->year_name ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-signal"></i> Status</div>
            <div class="detail-value"><span class="badge badge-{{ $invoiceSetting->status }}">{{ ucfirst($invoiceSetting->status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-play"></i> Starting Number</div>
            <div class="detail-value">{{ $invoiceSetting->starting_number }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-arrow-up-1-9"></i> Current Number</div>
            <div class="detail-value">{{ $invoiceSetting->current_number }}</div>
        </div>
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
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-tag"></i> {{ $label }}</div>
            <div class="detail-value" style="font-family:monospace;font-size:16px">{{ $prefix }}</div>
            <div style="font-size:12px;color:#CBD5E1;margin-top:6px">
                Sample: <span class="sample-chip">{{ $prefix }}-{{ $year }}-{{ $num }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
