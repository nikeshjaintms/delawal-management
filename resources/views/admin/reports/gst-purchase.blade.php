@extends('admin.layouts.app')
@section('title','GST Purchase Report')
@section('page-title','Reports')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.rpt-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 14px; }
.rpt-title-block h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.rpt-title-block p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }
.rpt-action-btns { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-pdf {
    padding: 10px 18px; border: 1px solid #EF4444 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #DC2626 !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40);
}
.btn-pdf:hover { background: #B91C1C !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.60); }

.btn-excel {
    padding: 10px 18px; border: 1px solid #10B981 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #059669 !important; text-decoration: none !important;
    transition: all .2s ease; box-shadow: 0 4px 14px rgba(5, 150, 105, 0.40);
}
.btn-excel:hover { background: #047857 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5, 150, 105, 0.60); }

.btn-print {
    padding: 10px 18px; border: 1px solid #6366F1 !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 7px;
    color: #FFFFFF !important; background: #4F46E5 !important; cursor: pointer;
    font-family: inherit; transition: all .2s ease; box-shadow: 0 4px 14px rgba(79, 70, 229, 0.40);
}
.btn-print:hover { background: #4338CA !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.60); }

/* Summary cards */
.gst-stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(175px, 1fr)); gap: 14px; margin-bottom: 24px; }
.gst-stat-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 18px 20px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30);
    transition: transform .25s ease, box-shadow .25s ease;
}
.gst-stat-card:hover { transform: translateY(-3px); border-color: rgba(59, 130, 246, 0.40) !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45); }
.gst-stat-card .sc-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; }
.gst-stat-card .sc-value { font-size: 22px; font-weight: 800; color: #FFFFFF !important; }
.gst-stat-card .sc-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 12px; }
.sc-orange { background: rgba(249, 115, 22, 0.18) !important; color: #FB923C !important; border: 1px solid rgba(249, 115, 22, 0.35); }
.sc-amber  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.sc-sky    { background: rgba(56, 189, 248, 0.18) !important; color: #38BDF8 !important; border: 1px solid rgba(56, 189, 248, 0.35); }
.sc-teal   { background: rgba(45, 212, 191, 0.18) !important; color: #2DD4BF !important; border: 1px solid rgba(45, 212, 191, 0.35); }
.sc-purple { background: rgba(167, 139, 250, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(167, 139, 250, 0.35); }
.sc-red    { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }
.sc-green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }

/* Filter */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.filter-bar { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }
.filter-ctrl {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease; min-width: 150px;
    box-sizing: border-box;
}
select.filter-ctrl option { background: #101622 !important; color: #FFFFFF !important; }
.filter-ctrl:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-filter {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; font-family: inherit; align-self: flex-end; display: inline-flex; align-items: center;
    gap: 6px; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); height: 42px; white-space: nowrap !important;
}
.btn-filter:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 10px 12px;
    align-self: flex-end; display: inline-flex; align-items: center; gap: 5px; transition: color .15s; height: 42px; white-space: nowrap !important;
}
.btn-reset:hover { color: #FFFFFF !important; }

/* Table */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); background: rgba(16, 22, 34, 0.70); }
.gst-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.gst-table thead th {
    padding: 13px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.gst-table tbody td {
    padding: 13px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #FFFFFF !important; font-weight: 600; vertical-align: middle; white-space: nowrap !important;
}
.gst-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.gst-table tfoot td {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.08) !important;
    font-weight: 800; border-top: 2px solid rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important; white-space: nowrap !important;
}

/* Badges */
.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .3px; }
.sb-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35); }
.sb-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35); }
.sb-rejected { background: rgba(239, 68, 68, 0.18) !important;  color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35); }

/* Amount cells */
.amt { text-align: right; font-variant-numeric: tabular-nums; font-family: monospace; }
.amt-gst { font-weight: 700; }
.amt-grand { color: #34D399 !important; font-weight: 800; font-size: 14.5px; }

/* Empty state */
.empty-state { text-align: center; padding: 52px 20px; color: #94A3B8; }
.empty-state i { font-size: 40px; margin-bottom: 14px; display: block; opacity: .4; color: #FB923C; }
.empty-state p { font-size: 14.5px; color: #CBD5E1; }

.rpt-footer-bar {
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
    gap: 12px; margin-top: 18px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.12);
}

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
        <h2><i class="fa-solid fa-cart-flatbed" style="color:#FB923C;margin-right:9px;"></i>GST Purchase Report</h2>
        <p>Purchase bill GST summary with taxable amount, CGST, SGST, IGST breakup and grand total.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('reports.gst-purchase.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.gst-purchase.excel', request()->query()) }}" class="btn-excel">
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
        <div class="sc-icon sc-orange"><i class="fa-solid fa-file-invoice"></i></div>
        <div class="sc-label">Total Bills</div>
        <div class="sc-value" style="color:#FFFFFF !important;">{{ $totalBills }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-amber"><i class="fa-solid fa-indian-rupee-sign"></i></div>
        <div class="sc-label">Taxable Amount</div>
        <div class="sc-value" style="color:#FBBF24 !important;">₹{{ number_format($totalTaxable,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-sky"><i class="fa-solid fa-coins"></i></div>
        <div class="sc-label">Total CGST</div>
        <div class="sc-value" style="color:#38BDF8 !important;">₹{{ number_format($totalCgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-teal"><i class="fa-solid fa-landmark"></i></div>
        <div class="sc-label">Total SGST</div>
        <div class="sc-value" style="color:#2DD4BF !important;">₹{{ number_format($totalSgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-purple"><i class="fa-solid fa-globe"></i></div>
        <div class="sc-label">Total IGST</div>
        <div class="sc-value" style="color:#C084FC !important;">₹{{ number_format($totalIgst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-red"><i class="fa-solid fa-percent"></i></div>
        <div class="sc-label">Total GST</div>
        <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalGst,2) }}</div>
    </div>
    <div class="gst-stat-card">
        <div class="sc-icon sc-green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-label">Grand Total</div>
        <div class="sc-value" style="color:#34D399 !important;">₹{{ number_format($grandTotal,2) }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.gst-purchase') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Vendor / Supplier</span>
            <select name="filter_vendor" class="filter-ctrl @error('filter_vendor') is-invalid @enderror">
                <option value="">All Vendors</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ request('filter_vendor')==$v->id?'selected':'' }}>
                        {{ $v->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-ctrl @error('filter_category') is-invalid @enderror">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('filter_category')==$cat->id?'selected':'' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Approval Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
        </button>
        @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_category','filter_status']))
            <a href="{{ route('reports.gst-purchase') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- Data Table --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:16px;font-weight:800;color:#FFFFFF !important;">
            <i class="fa-solid fa-table-list" style="color:#FB923C;margin-right:8px;"></i>
            GST Purchase Records
            <span style="font-size:13px;font-weight:700;color:#94A3B8;margin-left:8px;">
                {{ $totalBills }} record{{ $totalBills!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_category','filter_status']))
            <span style="font-size:13px;font-weight:700;color:#FB923C;display:flex;align-items:center;gap:5px;">
                <i class="fa-solid fa-filter" style="color:#FB923C;"></i> Filtered results
            </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="gst-table">
            <thead>
                <tr>
                    <th style="width:36px;">#</th>
                    <th>Bill No</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Vendor / Supplier</th>
                    <th>Expense Title</th>
                    <th>HSN Code</th>
                    <th class="amt">Taxable Amt</th>
                    <th class="amt">CGST</th>
                    <th class="amt">SGST</th>
                    <th class="amt">IGST</th>
                    <th class="amt">Total GST</th>
                    <th class="amt">Grand Total</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $i => $exp)
                @php
                    $badge = match(strtolower($exp->approval_status ?? 'pending')) {
                        'approved' => 'sb-approved',
                        'rejected' => 'sb-rejected',
                        default    => 'sb-pending',
                    };
                    $supplierName = $exp->vendor?->name ?? ($exp->paid_to ?? '—');
                @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12.5px;font-weight:700;">{{ $i + 1 }}</td>
                    <td style="font-weight:700;font-size:13px;color:#FFFFFF;">{{ $exp->bill_no ?? '—' }}</td>
                    <td style="font-size:13px;color:#60A5FA;font-weight:600;">{{ $exp->invoice_no ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;color:#CBD5E1;font-weight:600;">
                        {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">{{ $supplierName }}</div>
                        @if($exp->vendor?->mobile)
                            <div style="font-size:11.5px;color:#94A3B8;font-weight:500;">{{ $exp->vendor->mobile }}</div>
                        @endif
                    </td>
                    <td>
                        <div style="font-size:13.5px;font-weight:600;color:#CBD5E1;">{{ $exp->expense_title }}</div>
                        @if($exp->expense_category)
                            <div style="font-size:11.5px;color:#94A3B8;font-weight:500;">{{ $exp->expense_category }}</div>
                        @endif
                    </td>
                    <td>
                        @if($exp->hsn_code)
                            <span style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);padding:3px 8px;border-radius:6px;font-size:12px;font-weight:700;color:#CBD5E1;">
                                {{ $exp->hsn_code }}
                            </span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td class="amt" style="color:#FBBF24 !important;font-weight:700;">₹{{ number_format($exp->computed_taxable,2) }}</td>
                    <td class="amt amt-gst" style="color:#38BDF8 !important;">
                        ₹{{ number_format($exp->computed_cgst,2) }}
                        @if($exp->cgst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:500;">({{ $exp->cgst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt amt-gst" style="color:#2DD4BF !important;">
                        ₹{{ number_format($exp->computed_sgst,2) }}
                        @if($exp->sgst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:500;">({{ $exp->sgst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt amt-gst" style="color:#C084FC !important;">
                        ₹{{ number_format($exp->computed_igst,2) }}
                        @if($exp->igst_rate)
                            <div style="font-size:11px;color:#94A3B8;font-weight:500;">({{ $exp->igst_rate }}%)</div>
                        @endif
                    </td>
                    <td class="amt" style="color:#F87171 !important;font-weight:800;">
                        ₹{{ number_format($exp->computed_total_gst,2) }}
                    </td>
                    <td class="amt amt-grand">
                        ₹{{ number_format($exp->computed_grand_total,2) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $badge }}">
                            {{ ucfirst($exp->approval_status ?? 'Pending') }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <a href="{{ route('expenses.show', $exp->id) }}"
                           style="color:#60A5FA;font-size:14px;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:rgba(59,130,246,0.15);border:1px solid rgba(59,130,246,0.30);text-decoration:none;transition:all .2s ease;"
                           title="View Expense">
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15">
                        <div class="empty-state">
                            <i class="fa-solid fa-cart-flatbed"></i>
                            <p>No GST purchase records found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_category','filter_status']))
                                <a href="{{ route('reports.gst-purchase') }}"
                                   style="color:#60A5FA;font-weight:700;font-size:13px;margin-top:8px;display:inline-block;text-decoration:none;">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($expenses->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="7" style="font-size:13.5px;color:#FFFFFF !important;font-weight:800;">
                        <i class="fa-solid fa-calculator" style="color:#FB923C;margin-right:6px;"></i>
                        Total ({{ $totalBills }} bill{{ $totalBills!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#FBBF24 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalTaxable,2) }}</td>
                    <td class="amt" style="color:#38BDF8 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalCgst,2) }}</td>
                    <td class="amt" style="color:#2DD4BF !important;font-size:14px;font-weight:800;">₹{{ number_format($totalSgst,2) }}</td>
                    <td class="amt" style="color:#C084FC !important;font-size:14px;font-weight:800;">₹{{ number_format($totalIgst,2) }}</td>
                    <td class="amt" style="color:#F87171 !important;font-size:14px;font-weight:800;">₹{{ number_format($totalGst,2) }}</td>
                    <td class="amt amt-grand" style="font-size:15px;color:#34D399 !important;font-weight:800;">₹{{ number_format($grandTotal,2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($expenses->count() > 0)
    <div class="rpt-footer-bar">
        <span style="font-size:13.5px;color:#FFFFFF !important;font-weight:700 !important;">
            <strong>{{ $totalBills }}</strong> record{{ $totalBills!=1?'s':'' }}
            &nbsp;·&nbsp; Grand Total: <strong style="color:#34D399 !important;">₹{{ number_format($grandTotal,2) }}</strong>
            &nbsp;·&nbsp; Total GST: <strong style="color:#F87171 !important;">₹{{ number_format($totalGst,2) }}</strong>
        </span>
        <span style="font-size:13px;color:#FFFFFF !important;font-weight:700 !important;"><i class="fa-regular fa-clock" style="color:#FB923C;"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
    @endif
</div>
@endsection
