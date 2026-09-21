@extends('admin.layouts.app')
@section('title', 'Labour Management')
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
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-green {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #34D399 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(16,185,129,0.35);
}
.btn-green:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.stat-cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 20px; }
.stat-card-custom {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important; padding: 14px 18px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
}
.stat-icon-wrap { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; }
.stat-icon-wrap.purple { background: rgba(168, 85, 247, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(168, 85, 247, 0.35) !important; }
.stat-icon-wrap.gold   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.stat-icon-wrap.green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.stat-icon-wrap.red    { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.stat-body-custom { min-width: 0; flex: 1; }
.stat-body-custom .sc-label { font-size: 10.5px; font-weight: 700; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; white-space: nowrap; }
.stat-body-custom .sc-value { font-size: 16.5px; font-weight: 700; color: #FFFFFF !important; white-space: nowrap; }

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
    min-width: 135px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.search-input { min-width: 220px; }

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

.type-chip {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important;
    padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important;
}
.type-fixed   { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; border-color: rgba(59, 130, 246, 0.35) !important; }
.type-normal  { background: rgba(16, 185, 129, 0.15) !important; color: #34D399 !important; border-color: rgba(16, 185, 129, 0.35) !important; }
.type-advance { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; border-color: rgba(245, 158, 11, 0.35) !important; }

.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
.st-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-inact  { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-people-carry-box" style="color: #C084FC;"></i> Labour Management</h2>
        <p>Manage fixed salary, daily wage (normal), and advance-based farm labours and track advance balances.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('agriculture.labours.create') }}" class="btn-gold"><i class="fa-solid fa-user-plus"></i> Add New Labour</a>
        <a href="{{ route('agriculture.labour-payments.create') }}" class="btn-green"><i class="fa-solid fa-money-bill-wave"></i> Record Labour Payment</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

{{-- Summary Cards --}}
<div class="stat-cards-grid">
    <div class="stat-card-custom">
        <div class="stat-icon-wrap purple"><i class="fa-solid fa-users"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Labours</div>
            <div class="sc-value">{{ $labours->total() }}</div>
        </div>
    </div>
    <div class="stat-card-custom">
        <div class="stat-icon-wrap gold"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Advance Given</div>
            <div class="sc-value" style="color:#FBBF24 !important;">₹{{ number_format($totalAdvance, 2) }}</div>
        </div>
    </div>
    <div class="stat-card-custom">
        <div class="stat-icon-wrap green"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Paid</div>
            <div class="sc-value" style="color:#34D399 !important;">₹{{ number_format($totalPaid, 2) }}</div>
        </div>
    </div>
    <div class="stat-card-custom">
        <div class="stat-icon-wrap red"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Pending Wage Payments</div>
            <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalPending, 2) }}</div>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('agriculture.labours.index') }}" class="filter-bar">
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
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Name, mobile, field/crop...">
        </div>

        <div class="filter-group">
            <span class="filter-label">Labour Type</span>
            <select name="filter_type" class="filter-control">
                <option value="">All Types</option>
                @foreach(['Fixed Labour', 'Normal Labour', 'Advance Labour'] as $t)
                    <option value="{{ $t }}" {{ request('filter_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Farm</span>
            <select name="filter_farm" class="filter-control">
                <option value="">All Farms</option>
                @foreach($farms as $fm)
                    <option value="{{ $fm->id }}" {{ request('filter_farm') == $fm->id ? 'selected' : '' }}>{{ $fm->farm_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                @foreach(['Active', 'Inactive'] as $st)
                    <option value="{{ $st }}" {{ request('filter_status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'filter_type', 'filter_farm', 'filter_status', 'firm_id']))
            <a href="{{ route('agriculture.labours.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Labour Name</th>
                    <th>Mobile</th>
                    <th>Labour Type</th>
                    <th>Assigned Farm</th>
                    <th>Field / Crop</th>
                    <th style="text-align:right;">Daily Wage / Salary</th>
                    <th style="text-align:right;">Advance Balance</th>
                    <th style="text-align:right;">Pending Amount</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($labours as $key => $labour)
                @php
                    $chipCls = 'type-normal';
                    if ($labour->labour_type === 'Fixed Labour') $chipCls = 'type-fixed';
                    elseif ($labour->labour_type === 'Advance Labour') $chipCls = 'type-advance';
                @endphp
                <tr>
                    <td>{{ method_exists($labours, 'firstItem') ? ($labours->firstItem() + $key) : ($key + 1) }}</td>
                    <td>
                        <strong style="color:#FFFFFF;font-size:14px;">{{ $labour->name }}</strong>
                        <div style="font-size:11.5px;color:#94A3B8;">{{ $labour->firm_names }}</div>
                    </td>
                    <td><span style="color:#CBD5E1;">{{ $labour->mobile_number ?: '—' }}</span></td>
                    <td><span class="type-chip {{ $chipCls }}">{{ $labour->labour_type }}</span></td>
                    <td>
                        @if($labour->farm)
                            <a href="{{ route('agriculture.farms.show', $labour->farm_id) }}" style="color:#60A5FA;font-weight:600;text-decoration:none;">
                                {{ $labour->farm->farm_name }}
                            </a>
                        @else
                            <span style="color:#94A3B8;">General Labour</span>
                        @endif
                    </td>
                    <td>{{ $labour->field_crop ?: 'General' }}</td>
                    <td style="text-align:right;font-weight:700;color:#FFFFFF;">
                        @if($labour->labour_type === 'Normal Labour')
                            ₹{{ number_format($labour->daily_wage, 2) }} <span style="font-size:11px;color:#94A3B8;font-weight:400;">/ day</span>
                        @else
                            ₹{{ number_format($labour->fixed_salary, 2) }} <span style="font-size:11px;color:#94A3B8;font-weight:400;">/ mo</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:700;color:#FBBF24;">₹{{ number_format($labour->advance_balance, 2) }}</td>
                    <td style="text-align:right;font-weight:700;color: {{ $labour->pending_amount > 0 ? '#F87171' : '#34D399' }};">
                        ₹{{ number_format($labour->pending_amount, 2) }}
                    </td>
                    <td style="text-align:center;">
                        <span class="status-badge {{ $labour->status === 'Active' ? 'st-active' : 'st-inact' }}">{{ $labour->status }}</span>
                    </td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('agriculture.labours.show', $labour->id) }}" class="btn-view" title="View Ledger"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('agriculture.labours.edit', $labour->id) }}" class="btn-edit" title="Edit Labour"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('agriculture.labours.destroy', $labour->id) }}" method="POST" style="display:inline;" id="del-labour-{{ $labour->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Labour" onclick="confirmDeleteLabour({{ $labour->id }}, '{{ addslashes($labour->name) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="padding:40px;text-align:center;color:#CBD5E1;">
                        <i class="fa-solid fa-people-carry-box" style="font-size:32px;opacity:0.3;display:block;margin-bottom:10px;"></i>
                        No labour records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($labours, 'links'))
        <div class="pagination-wrapper">{{ $labours->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDeleteLabour(id, name) {
    Swal.fire({
        title: 'Delete Labour?',
        html: 'Are you sure you want to delete <strong>' + name + '</strong>?<br><small style="color:#64748B;">Labour payment history will remain recorded in expenses.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-labour-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
