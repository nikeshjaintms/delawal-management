@extends('admin.layouts.app')
@section('title', 'Farm Details - ' . $farm->farm_name)
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.farm-hero {
    display: flex; align-items: flex-start; gap: 22px; padding-bottom: 24px;
    margin-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.farm-icon-box {
    width: 68px; height: 68px; border-radius: 18px; background: rgba(59, 130, 246, 0.18) !important;
    border: 2px solid rgba(59, 130, 246, 0.40) !important; display: flex; align-items: center;
    justify-content: center; font-size: 28px; color: #60A5FA !important; flex-shrink: 0;
}
.farm-hero-info h3 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; }
.farm-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 10px; }
.hero-badges-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.amount-hero { font-size: 22px; font-weight: 800; color: #34D399 !important; }
.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; }
.st-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-prep   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.st-harvest{ background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.st-fallow { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.st-inact  { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.type-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.10); }

/* KPI Row */
.stat-mini-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-mini-card {
    padding: 18px 20px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px;
}
.stat-mini-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.stat-mini-val { font-size: 20px; font-weight: 800; color: #FFFFFF !important; }

.section-title {
    font-size: 12.5px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 24px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px !important;
}
.detail-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); margin-bottom: 24px; }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px; text-transform: uppercase;
    letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
}
.premium-table td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-green {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #34D399 !important;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 22px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important; font-size: 13.5px;
    font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px;
    text-decoration: none !important;
}
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Farm Overview: {{ $farm->farm_name }}</h2>
        <p>Complete records of agricultural land, crops, labour forces, revenues, and expenditures.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('agriculture.farms.edit', $farm->id) }}" class="btn-gold"><i class="fa fa-edit"></i> Edit Farm</a>
        <a href="{{ route('agriculture.incomes.create') }}" class="btn-green"><i class="fa fa-plus"></i> Add Income</a>
        <a href="{{ route('agriculture.farms.index') }}" class="btn-outline"><i class="fa fa-arrow-left"></i> Back to Farms</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    {{-- Hero Section --}}
    <div class="farm-hero">
        <div class="farm-icon-box"><i class="fa-solid fa-tractor"></i></div>
        <div class="farm-hero-info">
            <h3>{{ $farm->farm_name }}</h3>
            <p>
                <i class="fa-solid fa-location-dot" style="color:#60A5FA;"></i> {{ $farm->village ? $farm->village . ', ' : '' }}{{ $farm->taluka ? $farm->taluka . ', ' : '' }}{{ $farm->district ?: 'Gujarat' }}
                &nbsp;·&nbsp; Firm: <strong style="color:#FFFFFF;">{{ $farm->firm_names }}</strong>
            </p>
            <div class="hero-badges-row">
                <span class="status-badge st-{{ strtolower(str_replace(' ', '', $farm->status)) }}">{{ $farm->status }}</span>
                <span class="type-chip">{{ $farm->farm_type }}</span>
                <span style="color:#FBBF24;font-weight:700;font-size:14px;"><i class="fa-solid fa-ruler-combined"></i> {{ $farm->land_area }} {{ $farm->area_unit }}</span>
                <span style="color:#34D399;font-weight:700;font-size:14px;"><i class="fa-solid fa-seedling"></i> {{ $farm->crop_activity ?: 'General Farming' }}</span>
            </div>
        </div>
    </div>

    {{-- Financial KPI Cards for this Farm --}}
    <div class="stat-mini-grid">
        <div class="stat-mini-card">
            <div class="stat-mini-label">Total Farm Income</div>
            <div class="stat-mini-val" style="color:#34D399;">₹{{ number_format($totalIncome, 2) }}</div>
            <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;">Received: ₹{{ number_format($incomeReceived, 2) }}</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Total Farm Expense</div>
            <div class="stat-mini-val" style="color:#F87171;">₹{{ number_format($totalExpense, 2) }}</div>
            <div style="font-size:11.5px;color:#94A3B8;margin-top:4px;">Labour: ₹{{ number_format($labourExpense, 2) }}</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Net Farm Profit / Loss</div>
            <div class="stat-mini-val" style="color: {{ $netProfit >= 0 ? '#34D399' : '#F87171' }};">
                ₹{{ number_format($netProfit, 2) }}
            </div>
            <div style="font-size:11.5px;color:#CBD5E1;margin-top:4px;">Income − Expense</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Labour Workforce</div>
            <div class="stat-mini-val" style="color:#60A5FA;">{{ $farm->labours->count() }} <span style="font-size:14px;color:#94A3B8;">Labours</span></div>
            <div style="font-size:11.5px;color:#CBD5E1;margin-top:4px;">{{ $farm->labours->where('status', 'Active')->count() }} Active</div>
        </div>
    </div>

    {{-- Section 1: Property & Land Specifics --}}
    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Farm & Property Information</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label">Survey / Khasra No</div>
            <div class="detail-value">{{ $farm->survey_no ?: '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Owner / Seller</div>
            <div class="detail-value">{{ $farm->owner_seller_name ?: '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Start / Possession Date</div>
            <div class="detail-value">{{ $farm->start_date ? \Carbon\Carbon::parse($farm->start_date)->format('d M Y') : '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Linked Project</div>
            <div class="detail-value">{{ $farm->project?->project_name ?? 'None (Direct Farm)' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Linked Property</div>
            <div class="detail-value">{{ $farm->property?->property_name ?? 'None' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Area & Units</div>
            <div class="detail-value">{{ $farm->land_area }} {{ $farm->area_unit }}</div>
        </div>
    </div>

    @if($farm->notes)
        <div class="detail-item" style="margin-bottom:24px;">
            <div class="detail-label">Notes & Remarks</div>
            <div class="detail-value" style="font-weight:400;line-height:1.6;">{{ $farm->notes }}</div>
        </div>
    @endif

    {{-- Section 2: Associated Labours --}}
    <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span><i class="fa-solid fa-people-carry-box"></i> Associated Labours ({{ $farm->labours->count() }})</span>
        <a href="{{ route('agriculture.labours.create') }}" style="color:#60A5FA;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa-solid fa-plus"></i> Add Labour</a>
    </div>
    @if($farm->labours->count() > 0)
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Labour Name</th>
                        <th>Type</th>
                        <th>Mobile</th>
                        <th>Field / Work</th>
                        <th style="text-align:right;">Wage / Salary</th>
                        <th style="text-align:right;">Advance Balance</th>
                        <th style="text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farm->labours as $lab)
                    <tr>
                        <td>
                            <a href="{{ route('agriculture.labours.show', $lab->id) }}" style="color:#FFFFFF;font-weight:700;text-decoration:none;">
                                {{ $lab->name }}
                            </a>
                        </td>
                        <td><span class="type-chip">{{ $lab->labour_type }}</span></td>
                        <td>{{ $lab->mobile_number ?: '—' }}</td>
                        <td>{{ $lab->field_crop ?: 'General' }}</td>
                        <td style="text-align:right;font-weight:700;color:#FFFFFF;">
                            @if($lab->labour_type === 'Normal Labour')
                                ₹{{ number_format($lab->daily_wage, 2) }} / day
                            @else
                                ₹{{ number_format($lab->fixed_salary, 2) }} / mo
                            @endif
                        </td>
                        <td style="text-align:right;font-weight:700;color:#FBBF24;">₹{{ number_format($lab->advance_balance, 2) }}</td>
                        <td style="text-align:center;">
                            <a href="{{ route('agriculture.labours.show', $lab->id) }}" class="btn-view" style="font-size:11.5px;padding:4px 8px;"><i class="fa fa-eye"></i> View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="detail-item" style="text-align:center;padding:24px;color:#94A3B8;margin-bottom:24px;">No labours assigned directly to this farm yet.</div>
    @endif

    {{-- Section 3: Incomes from this Farm --}}
    <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span><i class="fa-solid fa-arrow-trend-up"></i> Crop & Farm Incomes ({{ $farm->incomes->count() }})</span>
        <a href="{{ route('agriculture.incomes.create') }}" style="color:#34D399;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa-solid fa-plus"></i> Record Income</a>
    </div>
    @if($farm->incomes->count() > 0)
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Crop / Product</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Buyer</th>
                        <th style="text-align:right;">Total Amount</th>
                        <th style="text-align:right;">Pending</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farm->incomes->sortByDesc('income_date')->take(10) as $inc)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($inc->income_date)->format('d M Y') }}</td>
                        <td><strong style="color:#FFFFFF;">{{ $inc->crop_product }}</strong></td>
                        <td>{{ $inc->quantity }} {{ $inc->unit }}</td>
                        <td>₹{{ number_format($inc->rate, 2) }}</td>
                        <td>{{ $inc->buyer_display_name }}</td>
                        <td style="text-align:right;color:#34D399;font-weight:800;">₹{{ number_format($inc->total_amount, 2) }}</td>
                        <td style="text-align:right;color:#F87171;font-weight:700;">₹{{ number_format($inc->pending_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="detail-item" style="text-align:center;padding:24px;color:#94A3B8;margin-bottom:24px;">No income recorded for this farm yet.</div>
    @endif

    {{-- Section 4: Expenses for this Farm --}}
    <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span><i class="fa-solid fa-receipt"></i> Farm Expenses ({{ $farm->expenses->count() }})</span>
        <a href="{{ route('agriculture.expenses.create') }}" style="color:#F87171;font-size:12px;font-weight:700;text-decoration:none;"><i class="fa-solid fa-plus"></i> Add Expense</a>
    </div>
    @if($farm->expenses->count() > 0)
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Paid To / Description</th>
                        <th>Payment Mode</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($farm->expenses->sortByDesc('expense_date')->take(10) as $exp)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                        <td><span class="type-chip">{{ $exp->category }}</span></td>
                        <td>
                            <strong style="color:#FFFFFF;">{{ $exp->labour?->name ?? ($exp->vendor?->name ?? ($exp->contractor?->name ?? 'Direct')) }}</strong>
                            <div style="font-size:11.5px;color:#94A3B8;">{{ $exp->description ?: 'Farm Expense' }}</div>
                        </td>
                        <td>{{ $exp->payment_method ?: 'Cash' }}</td>
                        <td style="text-align:right;color:#F87171;font-weight:800;">₹{{ number_format($exp->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="detail-item" style="text-align:center;padding:24px;color:#94A3B8;">No expenses recorded for this farm yet.</div>
    @endif
</div>
@endsection
