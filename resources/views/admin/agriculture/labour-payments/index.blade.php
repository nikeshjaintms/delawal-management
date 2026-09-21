@extends('admin.layouts.app')
@section('title', 'Labour Payments & Wage Ledger')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

/* KPI Row */
.kpi-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
.kpi-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important; padding: 14px 18px !important;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25) !important;
    display: flex; align-items: center; gap: 14px;
}
.kpi-icon {
    width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}
.kpi-info { min-width: 0; flex: 1; }
.kpi-info h4 { font-size: 10.5px; font-weight: 700; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.6px; margin: 0 0 3px 0; white-space: nowrap; }
.kpi-info .kpi-val { font-size: 16.5px; font-weight: 700; color: #FFFFFF !important; margin: 0; line-height: 1.2; white-space: nowrap; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: flex-end !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }
.filter-control, .search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    min-width: 130px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 180px; }
.search-input::placeholder { color: #94A3B8 !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; height: 42px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); }
.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; white-space: nowrap !important; height: 42px; display: inline-flex; align-items: center; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge-wage   { background: rgba(34, 197, 94, 0.15) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.30); padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }
.badge-salary { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.30); padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }
.badge-adv-g  { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.30); padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }
.badge-adv-d  { background: rgba(168, 85, 247, 0.15) !important; color: #C084FC !important; border: 1px solid rgba(168, 85, 247, 0.30); padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }
.badge-bonus  { background: rgba(236, 72, 153, 0.15) !important; color: #F472B6 !important; border: 1px solid rgba(236, 72, 153, 0.30); padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; }

.badge-synced { background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.25); font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-money-check-dollar" style="color: #60A5FA;"></i> Labour Payments & Wage Ledger</h2>
        <p>Record daily wage payouts, monthly salaries, advances given, and advance deductions.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('agriculture.labours.index') }}" class="btn-reset" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px;"><i class="fa-solid fa-users"></i> All Labours</a>
        <a href="{{ route('agriculture.labour-payments.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Record Payment</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<!-- KPI Cards -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(34, 197, 94, 0.15); color: #34D399;">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div class="kpi-info">
            <h4>Total Paid Out</h4>
            <div class="kpi-val" style="color:#34D399;">₹{{ number_format($totalAmountPaid, 2) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(168, 85, 247, 0.15); color: #C084FC;">
            <i class="fa-solid fa-percent"></i>
        </div>
        <div class="kpi-info">
            <h4>Advance Deductions</h4>
            <div class="kpi-val" style="color:#C084FC;">₹{{ number_format($totalAdvanceDeducted, 2) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="kpi-info">
            <h4>Total Records</h4>
            <div class="kpi-val">{{ method_exists($payments, 'total') ? $payments->total() : count($payments) }}</div>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('agriculture.labour-payments.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-control" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Labour, Farm, Ref No, Notes...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Payment Type</span>
            <select name="filter_type" class="filter-control">
                <option value="">All Types</option>
                @foreach($paymentTypes as $pt)
                    <option value="{{ $pt }}" {{ request('filter_type') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Labour</span>
            <select name="filter_labour" class="filter-control">
                <option value="">All Labours</option>
                @foreach($labours as $l)
                    <option value="{{ $l->id }}" {{ request('filter_labour') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Farm</span>
            <select name="filter_farm" class="filter-control">
                <option value="">All Farms</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" {{ request('filter_farm') == $farm->id ? 'selected' : '' }}>{{ $farm->farm_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-control">
        </div>

        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-control">
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'filter_type', 'filter_labour', 'filter_farm', 'from_date', 'to_date', 'firm_id']))
            <a href="{{ route('agriculture.labour-payments.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Labour Name</th>
                    <th>Farm</th>
                    <th>Type</th>
                    <th>Work Days / Rate</th>
                    <th>Gross</th>
                    <th>Adv Deducted</th>
                    <th>Net Paid (₹)</th>
                    <th>Mode & Ref</th>
                    <th>Notes</th>
                    <th style="text-align:center;">Exp Synced</th>
                    <th style="width:70px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $key => $p)
                @php
                    $tBadge = 'badge-wage';
                    if ($p->payment_type === 'Salary') $tBadge = 'badge-salary';
                    elseif ($p->payment_type === 'Advance Given') $tBadge = 'badge-adv-g';
                    elseif ($p->payment_type === 'Advance Deduction') $tBadge = 'badge-adv-d';
                    elseif ($p->payment_type === 'Bonus') $tBadge = 'badge-bonus';
                @endphp
                <tr>
                    <td>{{ method_exists($payments, 'firstItem') ? ($payments->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color:#FFFFFF;">{{ $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d M Y') : '—' }}</strong>
                    </td>
                    <td>
                        @if($p->labour)
                            <a href="{{ route('agriculture.labours.show', $p->labour_id) }}" style="color:#60A5FA;font-weight:700;text-decoration:none;">
                                {{ $p->labour->name }}
                            </a>
                            <div style="font-size:11px;color:#94A3B8;">{{ $p->labour->labour_type }} &bull; {{ $p->labour->phone ?: 'No phone' }}</div>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->farm)
                            <span style="color:#CBD5E1;font-weight:600;"><i class="fa-solid fa-tractor" style="color:#60A5FA;font-size:11px;"></i> {{ $p->farm->farm_name }}</span>
                        @else
                            <span style="color:#94A3B8;">General / Multiple</span>
                        @endif
                    </td>
                    <td><span class="{{ $tBadge }}">{{ $p->payment_type }}</span></td>
                    <td>
                        @if($p->working_days > 0)
                            <span style="color:#FFFFFF;font-weight:600;">{{ (float)$p->working_days }} days</span>
                            @if($p->daily_wage_rate > 0)
                                <div style="font-size:11px;color:#94A3B8;">@ ₹{{ number_format($p->daily_wage_rate, 2) }}</div>
                            @endif
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->gross_amount > 0)
                            <span style="color:#CBD5E1;">₹{{ number_format($p->gross_amount, 2) }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($p->advance_deducted > 0)
                            <span style="color:#F87171;font-weight:600;">-₹{{ number_format($p->advance_deducted, 2) }}</span>
                        @else
                            <span style="color:#94A3B8;">—</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:{{ $p->payment_type === 'Advance Deduction' ? '#C084FC' : '#34D399' }};font-size:14.5px;">
                            ₹{{ number_format($p->amount, 2) }}
                        </strong>
                    </td>
                    <td>
                        <div style="color:#FFFFFF;font-weight:600;">{{ $p->payment_mode ?: 'Cash' }}</div>
                        @if($p->reference_no)
                            <div style="font-size:11px;color:#94A3B8;">Ref: {{ $p->reference_no }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="color:#94A3B8;font-size:12px;" title="{{ $p->notes }}">
                            {{ Str::limit($p->notes ?: '—', 22) }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        @if($p->sync_to_expense)
                            <span class="badge-synced"><i class="fa-solid fa-check"></i> Yes</span>
                        @else
                            <span style="color:#94A3B8;font-size:11px;">No</span>
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('agriculture.labour-payments.destroy', $p->id) }}" method="POST" id="del-p-{{ $p->id }}">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-delete" title="Delete Payment Record" onclick="confirmDeletePayment({{ $p->id }}, '{{ number_format($p->amount, 2) }}')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-receipt" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No labour payment transactions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($payments, 'links'))
        <div class="pagination-wrapper">{{ $payments->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeletePayment(id, amount) {
    Swal.fire({
        title: 'Delete Payment Record?',
        html: 'Are you sure you want to delete payment of <strong>₹' + amount + '</strong>?<br><small style="color:#64748B;">This will also remove any auto-synced Agri Expense and recalculate the labour balances.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-p-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
