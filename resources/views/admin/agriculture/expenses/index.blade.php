@extends('admin.layouts.app')
@section('title', 'Agriculture Expenses')
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

.badge-cat { padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
.cat-seeds      { background: rgba(34, 197, 94, 0.15); color: #34D399; border: 1px solid rgba(34, 197, 94, 0.30); }
.cat-fertilizer { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.30); }
.cat-labour     { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); }
.cat-equipment  { background: rgba(168, 85, 247, 0.15); color: #C084FC; border: 1px solid rgba(168, 85, 247, 0.30); }
.cat-pesticides { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.30); }
.cat-fuel       { background: rgba(234, 88, 12, 0.15); color: #FB923C; border: 1px solid rgba(234, 88, 12, 0.30); }
.cat-water      { background: rgba(6, 182, 212, 0.15); color: #22D3EE; border: 1px solid rgba(6, 182, 212, 0.30); }
.cat-other      { background: rgba(148, 163, 184, 0.15); color: #CBD5E1; border: 1px solid rgba(148, 163, 184, 0.30); }

.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.st-paid    { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-file-invoice-dollar" style="color: #F87171;"></i> Agriculture Expenses</h2>
        <p>Track seeds, fertilizer, labour, diesel/fuel, equipment, tractor, irrigation, and farm overhead costs.</p>
    </div>
    <a href="{{ route('agriculture.expenses.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Record Expense</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<!-- KPI Cards -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #F87171;">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="kpi-info">
            <h4>Total Agri Expenses</h4>
            <div class="kpi-val" style="color:#F87171;">₹{{ number_format($totalAmount, 2) }}</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">
            <i class="fa-solid fa-list-check"></i>
        </div>
        <div class="kpi-info">
            <h4>Expense Entries</h4>
            <div class="kpi-val">{{ method_exists($expenses, 'total') ? $expenses->total() : count($expenses) }}</div>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('agriculture.expenses.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Description, Bill/Inv #, Vendor...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="filter_category" class="filter-control">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('filter_category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
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
        @if(request()->hasAny(['search', 'filter_category', 'filter_farm', 'filter_status', 'from_date', 'to_date', 'firm_id']))
            <a href="{{ route('agriculture.expenses.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Farm</th>
                    <th>Category & Type</th>
                    <th>Paid To / Entity</th>
                    <th>Amount (₹)</th>
                    <th>Payment Mode</th>
                    <th>Bill / Inv #</th>
                    <th>Attachment</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $key => $exp)
                @php
                    $catClass = 'cat-other';
                    if ($exp->category === 'Seeds') $catClass = 'cat-seeds';
                    elseif ($exp->category === 'Fertilizer') $catClass = 'cat-fertilizer';
                    elseif ($exp->category === 'Labour') $catClass = 'cat-labour';
                    elseif ($exp->category === 'Equipment' || $exp->category === 'Tractor / Machinery') $catClass = 'cat-equipment';
                    elseif ($exp->category === 'Pesticides') $catClass = 'cat-pesticides';
                    elseif ($exp->category === 'Diesel / Fuel') $catClass = 'cat-fuel';
                    elseif ($exp->category === 'Water / Irrigation') $catClass = 'cat-water';

                    $stClass = $exp->payment_status === 'Paid' ? 'st-paid' : 'st-pending';
                @endphp
                <tr>
                    <td>{{ method_exists($expenses, 'firstItem') ? ($expenses->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color:#FFFFFF;">{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</strong>
                    </td>
                    <td>
                        @if($exp->farm)
                            <a href="{{ route('agriculture.farms.show', $exp->farm_id) }}" style="color:#60A5FA;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-tractor" style="font-size:11px;"></i> {{ $exp->farm->farm_name }}
                            </a>
                        @else
                            <span style="color:#94A3B8;">General Farm</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-cat {{ $catClass }}">{{ $exp->category }}</span>
                        <div style="font-size:11.5px;color:#CBD5E1;margin-top:3px;">{{ $exp->expense_type }}</div>
                    </td>
                    <td>
                        @if($exp->vendor)
                            <div style="color:#FFFFFF;font-weight:600;"><i class="fa-solid fa-shop" style="color:#FBBF24;font-size:11px;"></i> {{ $exp->vendor->name }}</div>
                            <span style="font-size:11px;color:#94A3B8;">Vendor</span>
                        @elseif($exp->contractor)
                            <div style="color:#FFFFFF;font-weight:600;"><i class="fa-solid fa-user-gear" style="color:#60A5FA;font-size:11px;"></i> {{ $exp->contractor->name }}</div>
                            <span style="font-size:11px;color:#94A3B8;">Contractor</span>
                        @elseif($exp->labour)
                            <div style="color:#FFFFFF;font-weight:600;"><i class="fa-solid fa-user" style="color:#34D399;font-size:11px;"></i> {{ $exp->labour->name }}</div>
                            <span style="font-size:11px;color:#94A3B8;">Labour Worker</span>
                        @else
                            <span style="color:#CBD5E1;">{{ $exp->description ?: '—' }}</span>
                        @endif
                    </td>
                    <td>
                        <strong style="color:#F87171;font-size:14.5px;">₹{{ number_format($exp->amount, 2) }}</strong>
                    </td>
                    <td>
                        <div style="color:#FFFFFF;font-weight:600;">{{ $exp->payment_method ?: 'Cash' }}</div>
                        @if($exp->paymentMode)
                            <div style="font-size:11px;color:#94A3B8;">{{ $exp->paymentMode->name }}</div>
                        @endif
                    </td>
                    <td>
                        <span style="color:#CBD5E1;font-size:12px;">{{ $exp->bill_no ?: ($exp->invoice_no ?: '—') }}</span>
                    </td>
                    <td>
                        @if($exp->attachment)
                            <a href="{{ asset('storage/' . $exp->attachment) }}" target="_blank" style="color:#60A5FA;font-size:12px;display:inline-flex;align-items:center;gap:4px;text-decoration:none;">
                                <i class="fa-solid fa-paperclip"></i> View Doc
                            </a>
                        @else
                            <span style="color:#94A3B8;font-size:11px;">No File</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $stClass }}">{{ $exp->payment_status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('agriculture.expenses.show', $exp->id) }}" class="btn-view" title="View Expense"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('agriculture.expenses.edit', $exp->id) }}" class="btn-edit" title="Edit Expense"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('agriculture.expenses.destroy', $exp->id) }}" method="POST" style="display:inline;" id="del-exp-{{ $exp->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Expense" onclick="confirmDeleteExpense({{ $exp->id }}, '{{ number_format($exp->amount, 2) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-file-invoice-dollar" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No agriculture expense records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($expenses, 'links'))
        <div class="pagination-wrapper">{{ $expenses->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteExpense(id, amount) {
    Swal.fire({
        title: 'Delete Expense?',
        html: 'Are you sure you want to delete expense record of <strong>₹' + amount + '</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-exp-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
