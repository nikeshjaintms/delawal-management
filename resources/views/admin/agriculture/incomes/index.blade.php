@extends('admin.layouts.app')
@section('title', 'Agriculture Income & Sales')
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

.type-chip { background: rgba(34, 197, 94, 0.15) !important; color: #34D399 !important; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(34, 197, 94, 0.30); }

.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.st-paid    { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-partial { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.st-pending { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-wheat-awn" style="color: #34D399;"></i> Agriculture Income & Crop Sales</h2>
        <p>Record crop harvest sales, APMC market sales, by-products, leasing income, and government subsidies.</p>
    </div>
    <a href="{{ route('agriculture.incomes.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Record Sale / Income</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<!-- KPI Cards -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(34, 197, 94, 0.15); color: #34D399;">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
        <div class="kpi-info">
            <h4>Total Agri Revenue</h4>
            <div class="kpi-val" style="color:#34D399;">₹{{ number_format($totalIncomeAmount, 2) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">
            <i class="fa-solid fa-hand-holding-dollar"></i>
        </div>
        <div class="kpi-info">
            <h4>Payment Received</h4>
            <div class="kpi-val" style="color:#60A5FA;">₹{{ number_format($totalReceived, 2) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24;">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="kpi-info">
            <h4>Pending Receivables</h4>
            <div class="kpi-val" style="color:#FBBF24;">₹{{ number_format($totalPending, 2) }}</div>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('agriculture.incomes.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Crop, Buyer, Invoice #, Notes...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Income Type</span>
            <select name="filter_type" class="filter-control">
                <option value="">All Types</option>
                @foreach($incomeTypes as $it)
                    <option value="{{ $it }}" {{ request('filter_type') == $it ? 'selected' : '' }}>{{ $it }}</option>
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
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                <option value="Paid" {{ request('filter_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                <option value="Partial" {{ request('filter_status') == 'Partial' ? 'selected' : '' }}>Partial</option>
                <option value="Pending" {{ request('filter_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
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
        @if(request()->hasAny(['search', 'filter_type', 'filter_farm', 'filter_status', 'from_date', 'to_date', 'firm_id']))
            <a href="{{ route('agriculture.incomes.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Farm</th>
                    <th>Crop / Product</th>
                    <th>Buyer / Customer</th>
                    <th>Qty & Rate</th>
                    <th>Total Billed (₹)</th>
                    <th>Received (₹)</th>
                    <th>Pending (₹)</th>
                    <th>Invoice & Mode</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $key => $inc)
                @php
                    $stClass = 'st-pending';
                    if ($inc->payment_status === 'Paid') $stClass = 'st-paid';
                    elseif ($inc->payment_status === 'Partial') $stClass = 'st-partial';
                @endphp
                <tr>
                    <td>{{ method_exists($incomes, 'firstItem') ? ($incomes->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color:#FFFFFF;">{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</strong>
                    </td>
                    <td>
                        @if($inc->farm)
                            <a href="{{ route('agriculture.farms.show', $inc->farm_id) }}" style="color:#60A5FA;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-tractor" style="font-size:11px;"></i> {{ $inc->farm->farm_name }}
                            </a>
                        @else
                            <span style="color:#94A3B8;">General Farm</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:#FFFFFF;font-size:14px;">{{ $inc->crop_product }}</strong>
                        <div><span class="type-chip">{{ $inc->income_type }}</span></div>
                    </td>
                    <td>
                        @if($inc->customer)
                            <div style="color:#FFFFFF;font-weight:600;"><i class="fa-solid fa-user-tie" style="color:#60A5FA;font-size:11px;"></i> {{ $inc->customer->name }}</div>
                        @elseif($inc->buyer_name)
                            <div style="color:#CBD5E1;font-weight:600;">{{ $inc->buyer_name }}</div>
                        @else
                            <span style="color:#94A3B8;">Direct Buyer</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:#FBBF24;">{{ number_format($inc->quantity, 2) }}</strong> {{ $inc->unit }}
                        <div style="font-size:11.5px;color:#94A3B8;">@ ₹{{ number_format($inc->rate, 2) }}</div>
                    </td>
                    <td>
                        <strong style="color:#34D399;font-size:14.5px;">₹{{ number_format($inc->total_amount, 2) }}</strong>
                    </td>
                    <td>
                        <span style="color:#60A5FA;font-weight:700;">₹{{ number_format($inc->payment_received, 2) }}</span>
                    </td>
                    <td>
                        @if($inc->pending_amount > 0)
                            <strong style="color:#F87171;">₹{{ number_format($inc->pending_amount, 2) }}</strong>
                        @else
                            <span style="color:#34D399;font-size:12px;">₹0.00</span>
                        @endif
                    </td>
                    <td>
                        <div style="color:#FFFFFF;font-weight:600;">{{ $inc->payment_method ?: 'Cash' }}</div>
                        @if($inc->invoice_no)
                            <div style="font-size:11px;color:#94A3B8;">Inv: {{ $inc->invoice_no }}</div>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $stClass }}">{{ $inc->payment_status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('agriculture.incomes.show', $inc->id) }}" class="btn-view" title="View Sale Details"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('agriculture.incomes.edit', $inc->id) }}" class="btn-edit" title="Edit Sale"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('agriculture.incomes.destroy', $inc->id) }}" method="POST" style="display:inline;" id="del-inc-{{ $inc->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Income Record" onclick="confirmDeleteIncome({{ $inc->id }}, '{{ number_format($inc->total_amount, 2) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-wheat-awn" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No agriculture crop sales or income records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($incomes, 'links'))
        <div class="pagination-wrapper">{{ $incomes->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteIncome(id, amount) {
    Swal.fire({
        title: 'Delete Income Record?',
        html: 'Are you sure you want to delete crop sale of <strong>₹' + amount + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-inc-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
