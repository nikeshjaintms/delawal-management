@extends('admin.layouts.app')
@section('title','Debit Note Report')
@section('page-title','Reports')
@section('content')
<style>
.rpt-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:14px;}
.rpt-title-block h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.rpt-title-block p{font-size:13.5px;color:#64748B;}
.rpt-action-btns{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
.btn-pdf{padding:9px 16px;border:1px solid #EF4444;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#EF4444;background:rgba(239,68,68,0.05);text-decoration:none;transition:all .2s ease;}
.btn-pdf:hover{background:rgba(239,68,68,0.12);transform:translateY(-1px);}
.btn-excel{padding:9px 16px;border:1px solid #16803D;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#16803D;background:rgba(34,197,94,0.05);text-decoration:none;transition:all .2s ease;}
.btn-excel:hover{background:rgba(34,197,94,0.12);transform:translateY(-1px);}
.btn-print{padding:9px 16px;border:1px solid #6366F1;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#6366F1;background:rgba(99,102,241,0.05);cursor:pointer;font-family:inherit;transition:all .2s ease;}
.btn-print:hover{background:rgba(99,102,241,0.12);transform:translateY(-1px);}
.btn-manage{padding:9px 16px;border:1px solid #E2E8F0;border-radius:8px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:7px;color:#64748B;background:#fff;text-decoration:none;transition:all .2s ease;}
.btn-manage:hover{border-color:#EF4444;color:#EF4444;}
/* Stat cards */
.gst-stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(165px,1fr));gap:14px;margin-bottom:22px;}
.gst-stat-card{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:16px 18px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);
    transition:transform .2s ease,box-shadow .2s ease;}
.gst-stat-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.07),0 16px 36px rgba(0,0,0,0.09);}
.gst-stat-card .sc-label{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}
.gst-stat-card .sc-value{font-size:18px;font-weight:800;color:#0F172A;}
.gst-stat-card .sc-icon{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;margin-bottom:10px;}
.sc-blue  {background:rgba(59,130,246,0.1); color:#3B82F6;}
.sc-red   {background:rgba(239,68,68,0.1);  color:#EF4444;}
.sc-amber {background:rgba(245,158,11,0.1); color:#F59E0B;}
.sc-purple{background:rgba(139,92,246,0.1); color:#8B5CF6;}
.sc-sky   {background:rgba(14,165,233,0.1); color:#0EA5E9;}
.sc-teal  {background:rgba(20,184,166,0.1); color:#14B8A6;}
.sc-crimson{background:rgba(220,38,38,0.1); color:#DC2626;}
/* Filter */
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:20px 22px;
    box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.04);margin-bottom:18px;}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:150px;}
.filter-ctrl:focus{border-color:#EF4444;box-shadow:0 0 0 3px rgba(239,68,68,0.12);}
.btn-filter{background:#0F172A;color:#fff;padding:9px 16px;border-radius:8px;border:none;font-size:13px;
    font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;
    display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-filter:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;
    display:inline-flex;align-items:center;gap:5px;}
.btn-reset:hover{color:#0F172A;}
/* Table */
.table-wrap{width:100%;overflow-x:auto;}
.gst-table{width:100%;border-collapse:collapse;font-size:13px;}
.gst-table thead th{padding:11px 13px;background:#F8FAFC;color:#475569;font-weight:700;
    border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;}
.gst-table tbody td{padding:12px 13px;border-bottom:1px solid #F1F5F9;color:#0F172A;vertical-align:middle;}
.gst-table tbody tr:last-child td{border-bottom:none;}
.gst-table tbody tr{transition:background .14s ease;}
.gst-table tbody tr:hover{background:#FFF5F5;}
.gst-table tfoot td{padding:11px 13px;background:#F8FAFC;font-weight:800;border-top:2px solid #E2E8F0;}
.amt{text-align:right;font-variant-numeric:tabular-nums;}
/* Badges */
.dn-badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.dn-approved{background:rgba(16,185,129,0.1);color:#065F46;}
.dn-pending{background:rgba(245,158,11,0.1);color:#92400E;}
.dn-rejected{background:rgba(239,68,68,0.1);color:#991B1B;}
.empty-state{text-align:center;padding:52px 20px;color:#94A3B8;}
.empty-state i{font-size:40px;margin-bottom:14px;display:block;opacity:.3;}
.empty-state p{font-size:14px;}
.rpt-footer-bar{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;
    gap:10px;margin-top:16px;padding-top:14px;border-top:1px solid #F1F5F9;}
.rpt-footer-bar span{font-size:12px;color:#64748B;}
@media print{
    .sidebar,.topbar,.rpt-action-btns,.filter-bar,.btn-filter,.btn-reset,.btn-manage{display:none!important;}
    .main-content{margin-left:0!important;}
    .content-body{padding:0!important;}
    body{background:#fff!important;}
}
</style>

<div class="rpt-header">
    <div class="rpt-title-block">
        <h2><i class="fa-solid fa-circle-minus" style="color:#EF4444;margin-right:9px;"></i>Debit Note Report</h2>
        <p>Vendor debit adjustment and payable deduction summary with GST breakup.</p>
    </div>
    <div class="rpt-action-btns">
        <a href="{{ route('debit-notes.index') }}" class="btn-manage">
            <i class="fa-solid fa-list"></i> Manage Notes
        </a>
        <a href="{{ route('reports.debit-note.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.debit-note.excel', request()->query()) }}" class="btn-excel">
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
        <div class="sc-icon sc-blue"><i class="fa-solid fa-file-lines"></i></div>
        <div class="sc-label">Total Notes</div>
        <div class="sc-value">{{ $totalNotes }}</div>
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
        <div class="sc-icon sc-crimson"><i class="fa-solid fa-circle-minus"></i></div>
        <div class="sc-label">Total Debit Amt</div>
        <div class="sc-value" style="color:#DC2626;">₹{{ number_format($totalDebit,2) }}</div>
    </div>
</div>

{{-- Filters --}}
<div class="card-box">
    <form method="GET" action="{{ route('reports.debit-note') }}" class="filter-bar">
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
                    <option value="{{ $v->id }}" {{ request('filter_vendor')==$v->id?'selected':'' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-filter">
            <i class="fa-solid fa-magnifying-glass"></i> Apply
        </button>
        @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_status']))
            <a href="{{ route('reports.debit-note') }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif
    </form>
</div>

{{-- Data Table --}}
<div class="card-box">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
        <div style="font-size:13.5px;font-weight:700;color:#0F172A;">
            <i class="fa-solid fa-table-list" style="color:#EF4444;margin-right:7px;"></i>
            Debit Note Records
            <span style="font-size:12px;font-weight:500;color:#64748B;margin-left:8px;">
                {{ $totalNotes }} record{{ $totalNotes!=1?'s':'' }}
            </span>
        </div>
        @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_status']))
            <span style="font-size:12px;color:#64748B;display:flex;align-items:center;gap:5px;">
                <i class="fa-solid fa-filter" style="color:#EF4444;"></i> Filtered
            </span>
        @endif
    </div>

    <div class="table-wrap">
        <table class="gst-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Debit Note No</th>
                    <th>Date</th>
                    <th>Vendor / Supplier</th>
                    <th>Related Bill</th>
                    <th>Reason</th>
                    <th class="amt">Taxable</th>
                    <th class="amt">CGST</th>
                    <th class="amt">SGST</th>
                    <th class="amt">IGST</th>
                    <th class="amt">Total GST</th>
                    <th class="amt">Debit Amt</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;">View</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notes as $i => $dn)
                @php $badge = match($dn->status){'Approved'=>'dn-approved','Rejected'=>'dn-rejected',default=>'dn-pending'}; @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ $i + 1 }}</td>
                    <td style="font-weight:700;font-size:13px;">{{ $dn->debit_note_no ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;">
                        {{ \Carbon\Carbon::parse($dn->debit_note_date)->format('d M Y') }}
                    </td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $dn->vendor?->name ?? '—' }}</div>
                        @if($dn->vendor?->mobile)
                            <div style="font-size:11px;color:#64748B;">{{ $dn->vendor->mobile }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#64748B;">{{ $dn->related_bill_no ?? '—' }}</td>
                    <td style="font-size:13px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $dn->reason ? \Illuminate\Support\Str::limit($dn->reason,35) : '—' }}
                    </td>
                    <td class="amt">₹{{ number_format($dn->taxable_amount,2) }}</td>
                    <td class="amt" style="color:#0EA5E9;font-weight:700;">
                        ₹{{ number_format($dn->cgst_amount,2) }}
                        @if($dn->cgst_rate)<div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $dn->cgst_rate }}%)</div>@endif
                    </td>
                    <td class="amt" style="color:#14B8A6;font-weight:700;">
                        ₹{{ number_format($dn->sgst_amount,2) }}
                        @if($dn->sgst_rate)<div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $dn->sgst_rate }}%)</div>@endif
                    </td>
                    <td class="amt" style="color:#8B5CF6;font-weight:700;">
                        ₹{{ number_format($dn->igst_amount,2) }}
                        @if($dn->igst_rate)<div style="font-size:11px;color:#94A3B8;font-weight:400;">({{ $dn->igst_rate }}%)</div>@endif
                    </td>
                    <td class="amt" style="color:#EF4444;font-weight:700;">₹{{ number_format($dn->total_gst,2) }}</td>
                    <td class="amt" style="color:#DC2626;font-weight:800;font-size:14px;">₹{{ number_format($dn->debit_amount,2) }}</td>
                    <td style="text-align:center;"><span class="dn-badge {{ $badge }}">{{ $dn->status }}</span></td>
                    <td style="text-align:center;">
                        <a href="{{ route('debit-notes.show', $dn->id) }}"
                           style="color:#3B82F6;font-size:13px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;transition:color .15s;"
                           title="View">
                            <i class="fa-regular fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="14">
                        <div class="empty-state">
                            <i class="fa-solid fa-circle-minus"></i>
                            <p>No debit notes found for the selected filters.</p>
                            @if(request()->hasAny(['from_date','to_date','filter_vendor','filter_status']))
                                <a href="{{ route('reports.debit-note') }}" style="color:#3B82F6;font-size:13px;margin-top:8px;display:inline-block;">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($notes->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" style="font-size:13px;color:#0F172A;">
                        <i class="fa-solid fa-sigma" style="color:#EF4444;margin-right:6px;"></i>
                        Total ({{ $totalNotes }} note{{ $totalNotes!=1?'s':'' }})
                    </td>
                    <td class="amt" style="color:#D97706;font-size:14px;">₹{{ number_format($totalTaxable,2) }}</td>
                    <td class="amt" style="color:#0EA5E9;font-size:14px;">₹{{ number_format($totalCgst,2) }}</td>
                    <td class="amt" style="color:#14B8A6;font-size:14px;">₹{{ number_format($totalSgst,2) }}</td>
                    <td class="amt" style="color:#8B5CF6;font-size:14px;">₹{{ number_format($totalIgst,2) }}</td>
                    <td class="amt" style="color:#EF4444;font-size:14px;">₹{{ number_format($totalGst,2) }}</td>
                    <td class="amt" style="color:#DC2626;font-size:15px;font-weight:800;">₹{{ number_format($totalDebit,2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    @if($notes->count() > 0)
    <div class="rpt-footer-bar">
        <span>
            <strong>{{ $totalNotes }}</strong> record{{ $totalNotes!=1?'s':'' }}
            &nbsp;·&nbsp; Total Debit: <strong style="color:#DC2626;">₹{{ number_format($totalDebit,2) }}</strong>
            &nbsp;·&nbsp; Total GST: <strong style="color:#EF4444;">₹{{ number_format($totalGst,2) }}</strong>
        </span>
        <span><i class="fa-regular fa-clock"></i> Generated: {{ now()->format('d M Y, h:i A') }}</span>
    </div>
    @endif
</div>
@endsection
