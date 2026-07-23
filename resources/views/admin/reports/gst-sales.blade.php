@extends('admin.layouts.app')
@section('title','GST Sales Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Header ── */
.rpt-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.rpt-title-block h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.rpt-title-block p{font-size:13.5px;color:#64748B;}
.rpt-action-btns{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
/* Buttons */
.btn-pdf{padding:9px 16px;border:1px solid #EF4444;border-radius:8px;font-size:13px;font-weight:600;
    display:inline-flex;align-items:center;gap:7px;color:#EF4444;background:rgba(239,68,68,0.05);
    text-decoration:none;transition:all .2s ease;}
.btn-pdf:hover{background:rgba(239,68,68,0.12);transform:translateY(-1px);}
.btn-excel{padding:9px 16px;border:1px solid #16803D;border-radius:8px;font-size:13px;font-weight:600;
    display:inline-flex;align-items:center;gap:7px;color:#16803D;background:rgba(34,197,94,0.05);
    text-decoration:none;transition:all .2s ease;}
.btn-excel:hover{background:rgba(34,197,94,0.12);transform:translateY(-1px);}
.btn-print{padding:9px 16px;border:1px solid #6366F1;border-radius:8px;font-size:13px;font-weight:600;
    display:inline-flex;align-items:center;gap:7px;color:#6366F1;background:rgba(99,102,241,0.05);
    cursor:pointer;font-family:inherit;transition:all .2s ease;}
.btn-print:hover{background:rgba(99,102,241,0.12);transform:translateY(-1px);}
/* Summary cards */
.gst-stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px;margin-bottom:22px;}
.gst-stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:16px 18px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .2s ease,box-shadow .2s ease;}
.gst-stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.gst-stat-card .sc-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}
.gst-stat-card .sc-value{font-size:18px;font-weight:800;color:#0F172A;}
.gst-stat-card .sc-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;
    justify-content:center;font-size:15px;margin-bottom:10px;}
.sc-blue  {background:rgba(59,130,246,0.1); color:#3B82F6;}
.sc-green {background:rgba(16,185,129,0.1); color:#10B981;}
.sc-amber {background:rgba(245,158,11,0.1); color:#F59E0B;}
.sc-purple{background:rgba(139,92,246,0.1); color:#8B5CF6;}
.sc-red   {background:rgba(239,68,68,0.1);  color:#EF4444;}
.sc-sky   {background:rgba(14,165,233,0.1); color:#0EA5E9;}
.sc-teal  {background:rgba(20,184,166,0.1); color:#14B8A6;}
/* Filter */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;
    font-family:inherit;outline:none;background:#fff;transition:border-color .18s ease;min-width:150px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.15);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 16px;border-radius:8px;border:none;
    font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;
    display:inline-flex;align-items:center;gap:6px;transition:background .18s ease;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;
    display:inline-flex;align-items:center;gap:5px;transition:color .18s ease;}
.btn-reset:hover{color:#0F172A;}
/* Table */
.table-wrap{width:100%;overflow-x:auto;}
.gst-table{width:100%;border-collapse:collapse;font-size:13px;}
.gst-table thead th{padding:11px 13px;background:#F8FAFC;color:#475569;font-weight:700;
    border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;
    white-space:nowrap;}
.gst-table tbody td{padding:12px 13px;border-bottom:1px solid #F1F5F9;color:#0F172A;vertical-align:middle;}
.gst-table tbody tr:last-child td{border-bottom:none;}
.gst-table tbody tr{transition:background .14s ease;}
.gst-table tbody tr:hover{background:#F0F9FF;}
.gst-table tfoot td{padding:11px 13px;background:#F8FAFC;font-weight:800;border-top:2px solid #E2E8F0;}
/* Badges */
.pay-badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.pb-paid{background:rgba(16,185,129,0.1);color:#065F46;}
.pb-pending{background:rgba(245,158,11,0.1);color:#92400E;}
.pb-partial{background:rgba(59,130,246,0.1);color:#1E40AF;}
.pb-cancelled{background:rgba(239,68,68,0.1);color:#991B1B;}
/* Empty state */
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
.empty-state p{font-size:14px;}
/* Amount cells */
.amt{text-align:right;font-variant-numeric:tabular-nums;}
.amt-gst{color:#8B5CF6;font-weight:700;}
.amt-total{color:#0F172A;font-weight:800;}
.amt-grand{color:#059669;font-weight:800;font-size:14px;}
/* Footer meta */
.rpt-footer-bar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;
    gap:10px;margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;}
.rpt-footer-bar span{font-size:12px;color:#64748B;}
/* Print */
@media print{
    .sidebar,.topbar,.rpt-action-btns,.filter-bar,.btn-filter,.btn-reset,.card-box:has(.filter-bar){display:none!important;}
    .main-content{margin-left:0!important;}
    .content-body{padding:0!important;}
    body{background:#fff!important;}
    .gst-stat-card{box-shadow:none!important;border:1px solid #E2E8F0!important;}
}
</style>

{{-- Header --}}
<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-file-invoice-dollar" style="color:#F59E0B;margin-right:9px;"></i>GST Sales Report</h2>
        <p>Sales invoice GST summary with taxable amount, CGST, SGST, IGST breakup and grand total.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('reports.gst-sales.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.gst-sales.excel', request()->query()) }}" class="btn-excel">
            <i class="fa-solid fa-file-csv"></i> Export Excel
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="gst-stat-grid">
    <div class="gst-stat-card">
        <div class="sc-icon sc-blue"><i class="fa-solid fa-receipt"></i></div>
        <div class="sc-label">Total Invoices</div>
        <div class="sc-value">{{ $totalInvoices }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="sc-label">Taxable Amount</div>
        <div class="sc-value" style="color:#D97706;">₹{{ number_format($totalTaxable,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-sky"><i class="fa-solid fa-c"></i></div>
        <div class="sc-label">Total CGST</div>
        <div class="sc-value" style="color:#0EA5E9;">₹{{ number_format($totalCgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-teal"><i class="fa-solid fa-s"></i></div>
        <div class="sc-label">Total SGST</div>
        <div class="sc-value" style="color:#14B8A6;">₹{{ number_format($totalSgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-i"></i></div>
        <div class="sc-label">Total IGST</div>
        <div class="sc-value" style="color:#8B5CF6;">₹{{ number_format($totalIgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-percent"></i></div>
        <div class="sc-label">Total GST</div>
        <div class="sc-value" style="color:#EF4444;">₹{{ number_format($totalGst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Grand Total</div>
        <div class="sc-value" style="color:#059669;">₹{{ number_format($grandTotal,2) }}</div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.gst-sales') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer</span>
            <select name="filter_customer" class="filter-ctrl @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer')==$c->id?'selected':'' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['paid','pending','partial','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
        </button>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_status']))
            <a href="{{ route('reports.gst-sales') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- Data Table --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:13.5px;font-weight:700;color:#0F172A;">
            <i class="fa-solid fa-table-list" style="color:#F59E0B;margin-right:7px;"></i>
            GST Invoice Records
            <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">
                {{ $totalInvoices }} record{{ $totalInvoices!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_customer','filter_status']))
        <span style="font-size:12px;color:#64748B;display:flex;align-items:center;gap:5px;">
            <i class="fa-solid fa-filter" style="color:#F59E0B;"></i> Filtered results
        </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="gst-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Property</th>
                    <th>HSN Code</th>
                    <th class="amt">Taxable Amt</th>
                    <th class="amt">CGST</th>
                    <th class="amt">SGST</th>
                    <th class="amt">IGST</th>
                    <th class="amt">Total GST</th>
                    <th class="amt">Grand Total</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $i => $sale)
                @php
                    $badge = match(strtolower($sale->payment_status ?? 'pending')) {
                        'paid'      => 'pb-paid',
                        'partial'   => 'pb-partial',
                        'cancelled' => 'pb-cancelled',
                        default     => 'pb-pending',
                    };
                @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ $i + 1 }}</td>
                    <td>
                        <span style="font-weight:600;font-size:13px;">
                            {{ $sale->invoice_no ?? '—' }}
                        </span>
                    </td>
                    <td style="white-space:nowrap;font-size:13px;">
                        {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '—' }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $sale->customer?->name ?? '—' }}</div>
                        @if($sale->customer?->mobile)
                            <div style="font-size:11px;color:#64748B;">{{ $sale->customer->mobile }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $sale->property?->property_name ?? '—' }}</td>
                    <td>
                        @if($sale->hsn_code)
                            <span style="background:#F1F5F9;padding:2px 8px;border-radius:5px;font-size:12px;font-weight:600;color:#475569;">
                                {{ $sale->hsn_code }}
                            </span>
                        @else
                            <span style="color:#CBD5E1;">—</span>
                        @endif
                    </td>
                    <td class="amt">₹{{ number_format($sale->computed_taxable,2) }}</td>
                    <td class="amt amt-gst" style="color:#0EA5E9;">
                        ₹{{ number_format($sale->computed_cgst,2) }}
                        @if($sale->cgst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $sale->cgst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt amt-gst" style="color:#14B8A6;">
                        ₹{{ number_format($sale->computed_sgst,2) }}
                        @if($sale->sgst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $sale->sgst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt amt-gst" style="color:#8B5CF6;">
                        ₹{{ number_format($sale->computed_igst,2) }}
                        @if($sale->igst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $sale->igst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt" style="color:#EF4444;font-weight:700;">
                        ₹{{ number_format($sale->computed_total_gst,2) }}
                    </td>
                    <td class="amt amt-grand">
                        ₹{{ number_format($sale->computed_grand_total,2) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="pay-badge {{ $badge }}">
                            {{ ucfirst($sale->payment_status ?? 'Pending') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('property-sales.show', $sale->id) }}"
                           style="color:#3B82F6;font-size:13px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;transition:color .15s ease;"
                           title="View Sale">
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <p>No GST sales records found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_customer','filter_status']))
                                <a href="{{ route('reports.gst-sales') }}"
                                   style="color:#3B82F6;font-size:13px;margin-top:8px;display:inline-block;">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($sales->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="font-size:13px;color:#0F172A;">
                        <i class="fa-solid fa-sigma" style="color:#F59E0B;margin-right:6px;"></i>
                        Total ({{ $totalInvoices }} invoice{{ $totalInvoices!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#D97706;font-size:14px;">₹{{ number_format($totalTaxable,2) }}</td>
                    <td class="amt" style="color:#0EA5E9;font-size:14px;">₹{{ number_format($totalCgst,2) }}</td>
                    <td class="amt" style="color:#14B8A6;font-size:14px;">₹{{ number_format($totalSgst,2) }}</td>
                    <td class="amt" style="color:#8B5CF6;font-size:14px;">₹{{ number_format($totalIgst,2) }}</td>
                    <td class="amt" style="color:#EF4444;font-size:14px;">₹{{ number_format($totalGst,2) }}</td>
                    <td class="amt amt-grand" style="font-size:15px;">₹{{ number_format($grandTotal,2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($sales->count() > 0)
    <div class="rpt-footer-bar">
        <span>
            <strong>{{ $totalInvoices }}</strong> record{{ $totalInvoices!=1?'s':'' }}
            &nbsp;·&nbsp; Grand Total: <strong style="color:#059669;">₹{{ number_format($grandTotal,2) }}</strong>
            &nbsp;·&nbsp; Total GST: <strong style="color:#EF4444;">₹{{ number_format($totalGst,2) }}</strong>
        </span>
        <span><i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
    @endif
</div>
@endsection
