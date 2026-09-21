@extends('admin.layouts.app')
@section('title', 'Agriculture Dashboard')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 22px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; display: flex; align-items: center; gap: 10px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.50); }

.btn-green {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #34D399 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(16,185,129,0.35);
}
.btn-green:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(16,185,129,0.50); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 22px !important;
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.30) !important; margin-bottom: 22px;
}

/* KPI Summary Cards Grid */
.stat-cards-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 14px;
}
.stat-cards-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 22px;
}
@media (max-width: 1200px) {
    .stat-cards-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .stat-cards-grid-3 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .stat-cards-grid-4, .stat-cards-grid-3 { grid-template-columns: 1fr; }
}

.stat-card-custom {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important; padding: 16px 18px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: 0 10px 26px rgba(0, 0, 0, 0.25); transition: all .25s ease;
    min-width: 0;
}
.stat-card-custom:hover { transform: translateY(-2px); border-color: rgba(59, 130, 246, 0.45) !important; box-shadow: 0 14px 32px rgba(0, 0, 0, 0.35); }

.stat-icon-wrap { width: 44px; height: 44px; border-radius: 11px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.stat-icon-wrap.green  { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.stat-icon-wrap.blue   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.stat-icon-wrap.gold   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.stat-icon-wrap.red    { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.stat-icon-wrap.purple { background: rgba(168, 85, 247, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(168, 85, 247, 0.35) !important; }

.stat-body-custom { min-width: 0; flex: 1; overflow: hidden; }
.stat-body-custom .sc-label { font-size: 10.5px; font-weight: 700; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-body-custom .sc-value { font-size: 17px; font-weight: 700; color: #FFFFFF !important; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.stat-body-custom .sc-sub { font-size: 11.5px; color: #CBD5E1 !important; margin-top: 2px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Filter Bar */
.filter-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 16px 20px !important;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25) !important; margin-bottom: 22px;
}
.filter-bar-grid {
    display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;
}
.filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1 1 150px; min-width: 130px; }
.filter-label { font-size: 10.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.7px; }
.filter-control {
    width: 100%; padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box; height: 42px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.btn-filter-submit {
    background: #2563EB !important; color: #FFFFFF !important; padding: 0 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; height: 42px; display: inline-flex; align-items: center; gap: 7px;
}
.btn-filter-submit:hover { background: #1D4ED8 !important; transform: translateY(-2px); }
.btn-reset {
    color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 0 14px;
    white-space: nowrap !important; height: 42px; display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 10px;
    transition: all .2s ease;
}
.btn-reset:hover { background: rgba(255, 255, 255, 0.10); color: #FFFFFF !important; }

/* Dashboard Two-Column Layout */
.dashboard-grid-2 {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 16px;
    margin-bottom: 22px;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}
@media (max-width: 1150px) {
    .dashboard-grid-2 {
        grid-template-columns: minmax(0, 1fr);
    }
}

.table-container {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    display: block;
    box-sizing: border-box;
}
.premium-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 12.5px;
}
.premium-table th {
    padding: 10px 12px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 10px 12px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 12.5px;
    color: #E2E8F0 !important;
    font-weight: 500;
    vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.section-head-card {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;
    flex-wrap: wrap; gap: 8px;
}
.section-head-card h3 { font-size: 14.5px; font-weight: 800; color: #FFFFFF !important; margin: 0; display: flex; align-items: center; gap: 8px; }

.badge-tag {
    display: inline-flex; align-items: center; padding: 3px 8px; border-radius: 6px;
    font-size: 10.5px; font-weight: 700; text-transform: uppercase;
}
.badge-green { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-red   { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-blue  { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-gold  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-wheat-awn" style="color: #34D399;"></i> Agriculture Dashboard</h2>
        <p>Comprehensive overview of farm properties, crop revenues, expenses, labour wages, and net profit.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('agriculture.farms.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Farm</a>
        <a href="{{ route('agriculture.incomes.create') }}" class="btn-green"><i class="fa-solid fa-circle-dollar-to-slot"></i> Record Income</a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-card">
    <form method="GET" action="{{ route('agriculture.dashboard') }}" class="filter-bar-grid">
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
            <span class="filter-label">Farm / Land</span>
            <select name="farm_id" class="filter-control">
                <option value="">All Farms</option>
                @foreach($allFarms as $fm)
                    <option value="{{ $fm->id }}" {{ request('farm_id') == $fm->id ? 'selected' : '' }}>{{ $fm->farm_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-group">
            <span class="filter-label">Project</span>
            <select name="project_id" class="filter-control">
                <option value="">All Projects</option>
                @foreach($allProjects as $pj)
                    <option value="{{ $pj->id }}" {{ request('project_id') == $pj->id ? 'selected' : '' }}>{{ $pj->project_name }}</option>
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

        <div style="display: flex; gap: 8px; align-items: flex-end;">
            <button type="submit" class="btn-filter-submit"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
            @if(request()->hasAny(['firm_id', 'farm_id', 'project_id', 'from_date', 'to_date']))
                <a href="{{ route('agriculture.dashboard') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Top Row: Financial & Profit KPIs --}}
<div class="stat-cards-grid-4">
    <div class="stat-card-custom">
        <div class="stat-icon-wrap green"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Revenue</div>
            <div class="sc-value" style="color:#34D399 !important;">₹{{ number_format($totalIncome, 2) }}</div>
            <div class="sc-sub">Rec: ₹{{ number_format($incomeReceived, 2) }} &bull; <span style="color:#F87171;">Pend: ₹{{ number_format($incomePending, 2) }}</span></div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon-wrap red"><i class="fa-solid fa-receipt"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Expenses</div>
            <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($totalExpense, 2) }}</div>
            <div class="sc-sub">Labour: ₹{{ number_format($labourExpense, 2) }} ({{ $totalExpense > 0 ? round(($labourExpense / $totalExpense) * 100) : 0 }}%)</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon-wrap {{ $netProfit >= 0 ? 'green' : 'red' }}">
            <i class="fa-solid {{ $netProfit >= 0 ? 'fa-scale-balanced' : 'fa-triangle-exclamation' }}"></i>
        </div>
        <div class="stat-body-custom">
            <div class="sc-label">Net Profit / Loss</div>
            <div class="sc-value" style="color: {{ $netProfit >= 0 ? '#34D399' : '#F87171' }} !important;">
                {{ $netProfit >= 0 ? '+' : '' }}₹{{ number_format($netProfit, 2) }}
            </div>
            <div class="sc-sub">Total Income − Total Expense</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon-wrap blue"><i class="fa-solid fa-tractor"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Farms & Land</div>
            <div class="sc-value">{{ $totalFarms }} <span style="font-size:13px;color:#94A3B8;font-weight:600;">Parcels</span></div>
            <div class="sc-sub">{{ number_format($totalLandArea, 2) }} Total Land Area &bull; {{ $activeFarmsCount }} Active</div>
        </div>
    </div>
</div>

{{-- Second Row: Labour & Advance KPIs --}}
<div class="stat-cards-grid-3">
    <div class="stat-card-custom">
        <div class="stat-icon-wrap purple"><i class="fa-solid fa-people-carry-box"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Active Labour Force</div>
            <div class="sc-value">{{ $totalLabours }} <span style="font-size:13px;color:#94A3B8;font-weight:600;">Workers</span></div>
            <div class="sc-sub">{{ $activeLabours }} Active on Duty</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon-wrap gold"><i class="fa-solid fa-hand-holding-dollar"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Total Advance Given</div>
            <div class="sc-value" style="color:#FBBF24 !important;">₹{{ number_format($totalAdvanceGiven, 2) }}</div>
            <div class="sc-sub">Outstanding Balance: ₹{{ number_format($totalAdvanceBalance, 2) }}</div>
        </div>
    </div>

    <div class="stat-card-custom">
        <div class="stat-icon-wrap red"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <div class="stat-body-custom">
            <div class="sc-label">Pending Wages</div>
            <div class="sc-value" style="color:#F87171 !important;">₹{{ number_format($pendingLabourPay, 2) }}</div>
            <div class="sc-sub">Unpaid wages & salary balances</div>
        </div>
    </div>
</div>

{{-- Two-column section: Farm Profit Breakdown & Monthly Comparison --}}
<div class="dashboard-grid-2">
    {{-- Farm-wise Financial Performance --}}
    <div class="card-box" style="display: flex; flex-direction: column;">
        <div class="section-head-card">
            <h3><i class="fa-solid fa-tractor" style="color:#60A5FA;"></i> Farm Financial Performance</h3>
            <a href="{{ route('agriculture.farms.index') }}" style="color:#60A5FA;font-size:12.5px;font-weight:700;text-decoration:none;">View All Farms &rarr;</a>
        </div>
        <div class="table-container" style="flex: 1;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Farm Name</th>
                        <th>Crop / Area</th>
                        <th style="text-align:right;">Income</th>
                        <th style="text-align:right;">Expense</th>
                        <th style="text-align:right;">Net Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmsList->take(6) as $farm)
                    @php
                        $fInc = $farm->total_income;
                        $fExp = $farm->total_expense;
                        $fProfit = $fInc - $fExp;
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('agriculture.farms.show', $farm->id) }}" style="color:#FFFFFF;font-weight:700;text-decoration:none;">
                                {{ $farm->farm_name }}
                            </a>
                            <div style="font-size:11px;color:#94A3B8;">{{ $farm->village ?: 'Farm Parcel' }}</div>
                        </td>
                        <td>
                            <span class="badge-tag badge-blue">{{ $farm->crop_activity ?: 'General' }}</span>
                            <div style="font-size:11px;color:#CBD5E1;margin-top:2px;">{{ $farm->land_area }} {{ $farm->area_unit }}</div>
                        </td>
                        <td style="text-align:right;color:#34D399;font-weight:700;">₹{{ number_format($fInc, 2) }}</td>
                        <td style="text-align:right;color:#F87171;font-weight:700;">₹{{ number_format($fExp, 2) }}</td>
                        <td style="text-align:right;font-weight:800;color: {{ $fProfit >= 0 ? '#34D399' : '#F87171' }};">
                            {{ $fProfit >= 0 ? '+' : '' }}₹{{ number_format($fProfit, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:#94A3B8;">No farm records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Monthly Income vs Expense Trend --}}
    <div class="card-box" style="display: flex; flex-direction: column;">
        <div class="section-head-card">
            <h3><i class="fa-solid fa-chart-column" style="color:#34D399;"></i> Monthly Financial Trend (6 Months)</h3>
            <a href="{{ route('agriculture.reports.index') }}" style="color:#34D399;font-size:12.5px;font-weight:700;text-decoration:none;">Full Reports &rarr;</a>
        </div>
        <div class="table-container" style="flex: 1;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th style="text-align:right;">Income</th>
                        <th style="text-align:right;">Expense</th>
                        <th style="text-align:right;">Net Profit / Loss</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyTrends as $trend)
                    <tr>
                        <td style="color:#FFFFFF;font-weight:700;">{{ $trend['month'] }}</td>
                        <td style="text-align:right;color:#34D399;font-weight:700;">₹{{ number_format($trend['income'], 2) }}</td>
                        <td style="text-align:right;color:#F87171;font-weight:700;">₹{{ number_format($trend['expense'], 2) }}</td>
                        <td style="text-align:right;font-weight:800;color: {{ $trend['profit'] >= 0 ? '#34D399' : '#F87171' }};">
                            {{ $trend['profit'] >= 0 ? '+' : '' }}₹{{ number_format($trend['profit'], 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Recent Incomes & Expenses --}}
<div class="dashboard-grid-2">
    {{-- Recent Incomes --}}
    <div class="card-box" style="display: flex; flex-direction: column;">
        <div class="section-head-card">
            <h3><i class="fa-solid fa-arrow-trend-up" style="color:#34D399;"></i> Recent Crop Incomes</h3>
            <a href="{{ route('agriculture.incomes.index') }}" style="color:#34D399;font-size:12.5px;font-weight:700;text-decoration:none;">View All Incomes &rarr;</a>
        </div>
        <div class="table-container" style="flex: 1;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Crop / Produce</th>
                        <th>Buyer</th>
                        <th style="text-align:right;">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentIncomes as $inc)
                    <tr>
                        <td style="color:#CBD5E1;">{{ \Carbon\Carbon::parse($inc->income_date)->format('d M Y') }}</td>
                        <td>
                            <div style="font-weight:700;color:#FFFFFF;font-size:13.5px;">{{ $inc->crop_product }}</div>
                            <div style="font-size:11.5px;color:#94A3B8;margin-top:2px;">
                                <i class="fa-solid fa-tractor" style="font-size:10px;color:#60A5FA;"></i> {{ $inc->farm?->farm_name ?? '—' }} &bull; {{ number_format($inc->quantity, 2) }} {{ $inc->unit }}
                            </div>
                        </td>
                        <td style="color:#CBD5E1;">{{ $inc->buyer_display_name }}</td>
                        <td style="text-align:right;color:#34D399;font-weight:800;">₹{{ number_format($inc->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:#94A3B8;">No income recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Expenses --}}
    <div class="card-box" style="display: flex; flex-direction: column;">
        <div class="section-head-card">
            <h3><i class="fa-solid fa-receipt" style="color:#F87171;"></i> Recent Agriculture Expenses</h3>
            <a href="{{ route('agriculture.expenses.index') }}" style="color:#F87171;font-size:12.5px;font-weight:700;text-decoration:none;">View All Expenses &rarr;</a>
        </div>
        <div class="table-container" style="flex: 1;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category / Farm</th>
                        <th>Paid To</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentExpenses as $exp)
                    <tr>
                        <td style="color:#CBD5E1;">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                        <td>
                            <span class="badge-tag badge-gold">{{ $exp->category }}</span>
                            <div style="font-size:11.5px;color:#94A3B8;margin-top:3px;">
                                <i class="fa-solid fa-tractor" style="font-size:10px;color:#60A5FA;"></i> {{ $exp->farm?->farm_name ?? 'General Farm' }}
                            </div>
                        </td>
                        <td style="color:#CBD5E1;">
                            {{ $exp->labour?->name ?? ($exp->vendor?->name ?? ($exp->contractor?->contractor_name ?? 'Direct')) }}
                        </td>
                        <td style="text-align:right;color:#F87171;font-weight:800;">₹{{ number_format($exp->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:#94A3B8;">No expenses recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
