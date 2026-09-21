@extends('admin.layouts.app')
@section('title', 'Agriculture Financial & Operations Reports')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass Report System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-print {
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.20) !important;
    cursor: pointer; transition: all .25s ease;
}
.btn-print:hover { background: rgba(255, 255, 255, 0.18) !important; transform: translateY(-2px); }

/* Report Tab Navigation */
.report-tabs-nav {
    display: flex; gap: 8px; margin-bottom: 24px; overflow-x: auto; padding-bottom: 6px;
}
.tab-btn {
    padding: 12px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 700;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 8px;
    background: rgba(20, 27, 41, 0.60); border: 1px solid rgba(255, 255, 255, 0.10);
    color: #CBD5E1 !important; transition: all .25s ease; white-space: nowrap;
}
.tab-btn:hover { background: rgba(255, 255, 255, 0.10); color: #FFFFFF !important; }
.tab-btn.active {
    background: #2563EB !important; color: #FFFFFF !important; border-color: #3B82F6 !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.40);
}

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
.filter-control {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    min-width: 140px;
}
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    white-space: nowrap !important; height: 42px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); }
.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; white-space: nowrap !important; height: 42px; display: inline-flex; align-items: center; }

.table-container { width: 100%; max-width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); box-sizing: border-box; }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
.premium-table th {
    padding: 12px 14px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 10.5px;
    text-transform: uppercase; letter-spacing: 0.7px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 12px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
}
.premium-table tr:hover { background: rgba(255, 255, 255, 0.04); }

.grid-2col { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px; width: 100%; max-width: 100%; box-sizing: border-box; }
@media (max-width: 1000px) { .grid-2col { grid-template-columns: minmax(0, 1fr); } }

.breakdown-card {
    background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 16px; padding: 20px;
}
.breakdown-title { font-size: 13px; font-weight: 800; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.breakdown-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.05); font-size: 13.5px; }
.breakdown-item:last-child { border-bottom: none; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2><i class="fa-solid fa-chart-pie" style="color: #60A5FA;"></i> Agriculture Financial Reports</h2>
        <p>Comprehensive Income, Expense, Labour wages, and Farm Profit / Loss statements.</p>
    </div>
    <a href="{{ route('agriculture.reports.print', request()->query()) }}" target="_blank" class="btn-print">
        <i class="fa-solid fa-print"></i> Print Report / Sheet
    </a>
</div>

<!-- Tabs Navigation -->
<div class="report-tabs-nav">
    <a href="{{ route('agriculture.reports.index', array_merge(request()->except('page'), ['report_type' => 'profit_loss'])) }}" class="tab-btn {{ $reportType === 'profit_loss' ? 'active' : '' }}">
        <i class="fa-solid fa-scale-balanced"></i> Profit & Loss Statement
    </a>
    <a href="{{ route('agriculture.reports.index', array_merge(request()->except('page'), ['report_type' => 'income'])) }}" class="tab-btn {{ $reportType === 'income' ? 'active' : '' }}">
        <i class="fa-solid fa-wheat-awn"></i> Income / Sales Report
    </a>
    <a href="{{ route('agriculture.reports.index', array_merge(request()->except('page'), ['report_type' => 'expense'])) }}" class="tab-btn {{ $reportType === 'expense' ? 'active' : '' }}">
        <i class="fa-solid fa-file-invoice-dollar"></i> Expense Report
    </a>
    <a href="{{ route('agriculture.reports.index', array_merge(request()->except('page'), ['report_type' => 'labour'])) }}" class="tab-btn {{ $reportType === 'labour' ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i> Labour & Wages Report
    </a>
    <a href="{{ route('agriculture.reports.index', array_merge(request()->except('page'), ['report_type' => 'farm'])) }}" class="tab-btn {{ $reportType === 'farm' ? 'active' : '' }}">
        <i class="fa-solid fa-tractor"></i> Farm-Wise Summary
    </a>
</div>

<!-- Filter Bar -->
<div class="card-box">
    <form method="GET" action="{{ route('agriculture.reports.index') }}" class="filter-bar">
        <input type="hidden" name="report_type" value="{{ $reportType }}">

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
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" {{ request('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->farm_name }}</option>
                @endforeach
            </select>
        </div>

        @if($reportType === 'income')
        <div class="filter-group">
            <span class="filter-label">Income Type</span>
            <select name="income_type" class="filter-control">
                <option value="">All Types</option>
                @foreach($incomeTypes as $it)
                    <option value="{{ $it }}" {{ request('income_type') == $it ? 'selected' : '' }}>{{ $it }}</option>
                @endforeach
            </select>
        </div>
        @elseif($reportType === 'expense')
        <div class="filter-group">
            <span class="filter-label">Category</span>
            <select name="category" class="filter-control">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        @elseif($reportType === 'labour')
        <div class="filter-group">
            <span class="filter-label">Labour Type</span>
            <select name="labour_type" class="filter-control">
                <option value="">All Types</option>
                @foreach($labourTypes as $lt)
                    <option value="{{ $lt }}" {{ request('labour_type') == $lt ? 'selected' : '' }}>{{ $lt }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-control">
        </div>

        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-control">
        </div>

        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Generate</button>
        <a href="{{ route('agriculture.reports.index', ['report_type' => $reportType]) }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
    </form>

    {{-- REPORT CONTENT BASED ON ACTIVE TAB --}}
    @if($reportType === 'profit_loss')
        <!-- Profit & Loss View -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(34, 197, 94, 0.15); color: #34D399;">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                </div>
                <div class="kpi-info">
                    <h4>Total Agri Revenue</h4>
                    <div class="kpi-val" style="color:#34D399;">₹{{ number_format($reportData['totalIncome'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(239, 68, 68, 0.15); color: #F87171;">
                    <i class="fa-solid fa-arrow-trend-down"></i>
                </div>
                <div class="kpi-info">
                    <h4>Total Expenses</h4>
                    <div class="kpi-val" style="color:#F87171;">₹{{ number_format($reportData['totalExpense'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA;">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="kpi-info">
                    <h4>Labour Wages Total</h4>
                    <div class="kpi-val" style="color:#60A5FA;">₹{{ number_format($reportData['labourExpense'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                @php $pVal = $reportData['netProfit'] ?? 0; @endphp
                <div class="kpi-icon" style="background: {{ $pVal >= 0 ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }}; color: {{ $pVal >= 0 ? '#34D399' : '#F87171' }};">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <div class="kpi-info">
                    <h4>Net Profit / Loss</h4>
                    <div class="kpi-val" style="color: {{ $pVal >= 0 ? '#34D399' : '#F87171' }};">{{ $pVal >= 0 ? '+' : '' }}₹{{ number_format($pVal, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="grid-2col" style="margin-top: 24px;">
            <div class="breakdown-card">
                <div class="breakdown-title"><i class="fa-solid fa-receipt"></i> Category-Wise Expenses</div>
                @forelse($reportData['categoryBreakdown'] ?? [] as $cat => $sum)
                    <div class="breakdown-item">
                        <span style="color:#CBD5E1;">{{ $cat }}</span>
                        <strong style="color:#F87171;">₹{{ number_format($sum, 2) }}</strong>
                    </div>
                @empty
                    <div style="color:#94A3B8;text-align:center;padding:20px;">No expense records in selected period.</div>
                @endforelse
            </div>

            <div class="breakdown-card">
                <div class="breakdown-title"><i class="fa-solid fa-wheat-awn"></i> Income By Revenue Stream</div>
                @forelse($reportData['incomeBreakdown'] ?? [] as $iType => $sum)
                    <div class="breakdown-item">
                        <span style="color:#CBD5E1;">{{ $iType }}</span>
                        <strong style="color:#34D399;">₹{{ number_format($sum, 2) }}</strong>
                    </div>
                @empty
                    <div style="color:#94A3B8;text-align:center;padding:20px;">No income records in selected period.</div>
                @endforelse
            </div>
        </div>

    @elseif($reportType === 'income')
        <!-- Income Report Table -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Billed</h4>
                    <div class="kpi-val" style="color:#34D399;">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Payment Received</h4>
                    <div class="kpi-val" style="color:#60A5FA;">₹{{ number_format($reportData['totalReceived'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Pending Receivables</h4>
                    <div class="kpi-val" style="color:#F87171;">₹{{ number_format($reportData['totalPending'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Farm</th>
                        <th>Crop / Produce</th>
                        <th>Buyer</th>
                        <th>Quantity & Rate</th>
                        <th>Total Amount</th>
                        <th>Received</th>
                        <th>Pending</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['incomes'] ?? [] as $i => $inc)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</td>
                        <td>{{ $inc->farm?->farm_name ?: '—' }}</td>
                        <td><strong>{{ $inc->crop_product }}</strong> ({{ $inc->income_type }})</td>
                        <td>{{ $inc->customer?->name ?: ($inc->buyer_name ?: 'Direct') }}</td>
                        <td>{{ number_format($inc->quantity, 2) }} {{ $inc->unit }} @ ₹{{ number_format($inc->rate, 2) }}</td>
                        <td><strong style="color:#34D399;">₹{{ number_format($inc->total_amount, 2) }}</strong></td>
                        <td><span style="color:#60A5FA;">₹{{ number_format($inc->payment_received, 2) }}</span></td>
                        <td><span style="color:#F87171;">₹{{ number_format($inc->pending_amount, 2) }}</span></td>
                        <td>{{ $inc->payment_status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:30px;color:#94A3B8;">No income records match filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($reportType === 'expense')
        <!-- Expense Report Table -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Filtered Expenses</h4>
                    <div class="kpi-val" style="color:#F87171;">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Farm</th>
                        <th>Category</th>
                        <th>Expense Type</th>
                        <th>Paid To</th>
                        <th>Bill #</th>
                        <th>Amount</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['expenses'] ?? [] as $i => $exp)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</td>
                        <td>{{ $exp->farm?->farm_name ?: '—' }}</td>
                        <td><span style="color:#60A5FA;font-weight:700;">{{ $exp->category }}</span></td>
                        <td>{{ $exp->expense_type }}</td>
                        <td>{{ $exp->vendor?->name ?: ($exp->contractor?->name ?: ($exp->labour?->name ?: '—')) }}</td>
                        <td>{{ $exp->bill_no ?: ($exp->invoice_no ?: '—') }}</td>
                        <td><strong style="color:#F87171;">₹{{ number_format($exp->amount, 2) }}</strong></td>
                        <td>{{ $exp->payment_method ?: 'Cash' }}</td>
                        <td>{{ $exp->payment_status }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" style="text-align:center;padding:30px;color:#94A3B8;">No expense records match filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($reportType === 'labour')
        <!-- Labour Wage Ledger Report -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Paid to Labours</h4>
                    <div class="kpi-val" style="color:#34D399;">₹{{ number_format($reportData['totalPaid'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Outstanding Advances</h4>
                    <div class="kpi-val" style="color:#F87171;">₹{{ number_format($reportData['totalAdvanceBalance'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Pending Wages</h4>
                    <div class="kpi-val" style="color:#FBBF24;">₹{{ number_format($reportData['totalPending'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Labour Name</th>
                        <th>Type</th>
                        <th>Farm</th>
                        <th>Total Earned</th>
                        <th>Total Advances Given</th>
                        <th>Total Paid</th>
                        <th>Advance Balance</th>
                        <th>Pending Wage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['labours'] ?? [] as $i => $lab)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong style="color:#FFFFFF;">{{ $lab->name }}</strong></td>
                        <td>{{ $lab->labour_type }}</td>
                        <td>{{ $lab->farm?->farm_name ?: 'General' }}</td>
                        <td>₹{{ number_format($lab->total_earned, 2) }}</td>
                        <td>₹{{ number_format($lab->total_advance, 2) }}</td>
                        <td><strong style="color:#34D399;">₹{{ number_format($lab->total_paid, 2) }}</strong></td>
                        <td><span style="color:#F87171;font-weight:700;">₹{{ number_format($lab->advance_balance, 2) }}</span></td>
                        <td><span style="color:#FBBF24;font-weight:700;">₹{{ number_format($lab->pending_amount, 2) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:30px;color:#94A3B8;">No labour records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @elseif($reportType === 'farm')
        <!-- Farm Wise Summary Table -->
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Farm Area</h4>
                    <div class="kpi-val" style="color:#FBBF24;">{{ number_format($reportData['totalArea'] ?? 0, 2) }} Acres</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Farm Revenue</h4>
                    <div class="kpi-val" style="color:#34D399;">₹{{ number_format($reportData['totalIncome'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Total Farm Expenses</h4>
                    <div class="kpi-val" style="color:#F87171;">₹{{ number_format($reportData['totalExpense'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-info">
                    <h4>Net Farm Profit</h4>
                    <div class="kpi-val" style="color: {{ ($reportData['netProfit'] ?? 0) >= 0 ? '#34D399' : '#F87171' }};">₹{{ number_format($reportData['netProfit'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Farm Name</th>
                        <th>Location</th>
                        <th>Survey No</th>
                        <th>Land Area</th>
                        <th>Current Crop</th>
                        <th>Total Income</th>
                        <th>Total Expense</th>
                        <th>Net Profit / Loss</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData['farmSummaries'] ?? [] as $i => $fs)
                    @php $f = $fs['farm']; @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><strong style="color:#FFFFFF;">{{ $f->farm_name }}</strong></td>
                        <td>{{ $f->village ?: '—' }}</td>
                        <td>{{ $f->survey_no ?: '—' }}</td>
                        <td>{{ $fs['land_area'] }}</td>
                        <td><span style="color:#34D399;font-weight:700;">{{ $fs['crop'] }}</span></td>
                        <td><strong style="color:#34D399;">₹{{ number_format($fs['total_income'], 2) }}</strong></td>
                        <td><strong style="color:#F87171;">₹{{ number_format($fs['total_expense'], 2) }}</strong></td>
                        <td>
                            <strong style="color: {{ $fs['net_profit'] >= 0 ? '#34D399' : '#F87171' }};">
                                {{ $fs['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($fs['net_profit'], 2) }}
                            </strong>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" style="text-align:center;padding:30px;color:#94A3B8;">No farm records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
