@extends('admin.layouts.app')
@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')
@section('content')
    <style>
    /* --- Welcome Banner --- */
    .dash-welcome {
        position: relative;
        background: linear-gradient(135deg, #000000 0%, #000000 45%, #C5A87E 100%);
        border-radius: 22px; padding: 34px 38px; margin-bottom: 28px; overflow: hidden;
        box-shadow: 0 4px 24px rgba(197, 168, 126, 0.22), 0 1px 4px rgba(0, 0, 0, 0.10);
        transition: box-shadow 0.3s ease;
    }
    .dash-welcome:hover { box-shadow: 0 8px 36px rgba(197, 168, 126, 0.32), 0 2px 8px rgba(0, 0, 0, 0.12); }
    .dash-welcome-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 22px; }
    .dash-welcome-tag {
        display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.10);
        border: 1px solid rgba(255,255,255,0.18); color: #C7D7FF; font-size: 11px; font-weight: 700;
        letter-spacing: 1.3px; text-transform: uppercase; padding: 5px 13px; border-radius: 20px;
        margin-bottom: 11px; backdrop-filter: blur(4px);
    }
    .dash-welcome-title { font-size: 27px; font-weight: 800; color: #F0F6FF; line-height: 1.2; margin-bottom: 7px; text-shadow: 0 2px 12px rgba(0, 0, 0, 0.15); }
    .dash-welcome-sub { font-size: 14px; color: #A5B8D8; font-weight: 400; line-height: 1.5; }
    .dash-quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .dqa-btn {
        display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.11);
        border: 1px solid rgba(255,255,255,0.18); color: #E8EEFF; padding: 9px 17px; border-radius: 11px;
        font-size: 13px; font-weight: 600; text-decoration: none;
        transition: all 0.22s cubic-bezier(0.4,0,0.2,1); white-space: nowrap;
        font-family: inherit; cursor: pointer; backdrop-filter: blur(4px);
    }
    .dqa-btn i { font-size: 13px; transition: transform 0.2s ease; }
    .dqa-btn:hover { background: rgba(197, 168, 126, 0.20); border-color: rgba(197, 168, 126, 0.40); color: #fff; transform: translateY(-2px); box-shadow: 0 4px 16px rgba(197, 168, 126, 0.25); }
    .dqa-btn:hover i { transform: scale(1.15); }

    /* --- KPI Section Header --- */
    .kpi-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .kpi-section-header h3 { font-size: 13px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 1.2px; margin: 0; }
    .kpi-section-divider { flex: 1; height: 1px; background: #E2E8F0; }

    /* --- KPI Grid --- */
    .kpi-grid {
        display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 20px;
    }
    .kpi-grid-2 {
        display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px;
    }
    @media(max-width:1400px) { .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(4, 1fr); } }
    @media(max-width:1100px) { .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(3, 1fr); } }
    @media(max-width:768px)  { .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:480px)  { .kpi-grid, .kpi-grid-2 { grid-template-columns: 1fr; } }

    /* --- KPI Cards --- */
    .kpi-card {
        background: #fff; border: 1px solid #E2E8F0; border-radius: 18px;
        padding: 20px 18px; position: relative; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 20px rgba(0,0,0,0.05);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        display: flex; justify-content: space-between; align-items: flex-start;
    }
    .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 4px 8px rgba(0,0,0,0.06), 0 20px 48px rgba(0,0,0,0.10); }
    .kpi-card:hover .kpi-icon-box i { transform: scale(1.12); }
    .kpi-deco { position: absolute; width: 110px; height: 110px; border-radius: 50%; top: -32px; right: -32px; opacity: 0.45; pointer-events: none; }
    .kpi-info { display: flex; flex-direction: column; z-index: 2; flex: 1; min-width: 0; }
    .kpi-label { font-size: 11.5px; font-weight: 600; color: #64748B; margin-bottom: 7px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .kpi-value { font-size: 24px; font-weight: 800; color: #0F172A; line-height: 1.1; margin-bottom: 9px; font-variant-numeric: tabular-nums; }
    .kpi-badge { font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 20px; display: inline-block; width: fit-content; white-space: nowrap; }
    .kpi-icon-box { width: 46px; height: 46px; border-radius: 13px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; z-index: 2; transition: transform 0.22s ease; }

    /* Color variants */
    .ik-blue   { background: rgba(59,130,246,0.1);  color: #0e63ebff; }
    .ik-green  { background: rgba(16,185,129,0.1);  color: #0ebd82ff; }
    .ik-amber  { background: rgba(245,158,11,0.1);  color: #ffa200ff; }
    .ik-orange { background: rgba(249,115,22,0.1);  color: #fc6900ff; }
    .ik-red    { background: rgba(239,68,68,0.1);   color: #ee2222ff; }
    .ik-purple { background: rgba(139,92,246,0.1);  color: #692ef3ff; }
    .ik-sky    { background: rgba(14,165,233,0.1);  color: #00acfcff; }

    .deco-blue   { background: radial-gradient(circle, rgba(59,130,246,0.14) 0%, transparent 70%); }
    .deco-green  { background: radial-gradient(circle, rgba(16,185,129,0.14) 0%, transparent 70%); }
    .deco-amber  { background: radial-gradient(circle, rgba(245,158,11,0.14) 0%, transparent 70%); }
    .deco-orange { background: radial-gradient(circle, rgba(249,115,22,0.14) 0%, transparent 70%); }
    .deco-red    { background: radial-gradient(circle, rgba(239,68,68,0.14) 0%, transparent 70%); }
    .deco-purple { background: radial-gradient(circle, rgba(139,92,246,0.14) 0%, transparent 70%); }
    .deco-sky    { background: radial-gradient(circle, rgba(14,165,233,0.14) 0%, transparent 70%); }

    .bk-blue   { background: rgba(59,130,246,0.08); color: #1D4ED8; }
    .bk-green  { background: rgba(16,185,129,0.08); color: #065F46; }
    .bk-amber  { background: rgba(245,158,11,0.08); color: #92400E; }
    .bk-orange { background: rgba(249,115,22,0.08); color: #9A3412; }
    .bk-red    { background: rgba(239,68,68,0.08);  color: #991B1B; }
    .bk-purple { background: rgba(139,92,246,0.08); color: #5B21B6; }
    .bk-sky    { background: rgba(14,165,233,0.08); color: #0369A1; }

    /* --- Dashboard Bottom Grid --- */
    .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 22px; margin-bottom: 24px; }
    @media(max-width:992px) { .dashboard-grid { grid-template-columns: 1fr; } }

    /* --- Section Cards --- */
    .section-card {
        background: #fff; border: 1px solid #E2E8F0; border-radius: 18px; padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 6px 20px rgba(0,0,0,0.04); margin-bottom: 20px;
        transition: box-shadow 0.22s ease;
    }
    .section-card:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.06), 0 18px 42px rgba(0,0,0,0.08); }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #F1F5F9; }
    .section-title { font-size: 15px; font-weight: 700; color: #0F172A; display: flex; align-items: center; gap: 9px; }
    .section-title-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .btn-view-all { font-size: 12px; font-weight: 600; color: #3B82F6; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 8px; background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.15); transition: all 0.18s ease; }
    .btn-view-all:hover { background: rgba(59,130,246,0.15); transform: translateX(2px); }

    /* --- Tables --- */
    .erp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .erp-table th { padding: 11px 14px; background: #F8FAFC; color: #475569; font-weight: 700; border-bottom: 2px solid #E2E8F0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .erp-table td { padding: 13px 14px; border-bottom: 1px solid #F1F5F9; color: #0F172A; vertical-align: middle; }
    .erp-table tr:last-child td { border-bottom: none; }
    .erp-table tbody tr { transition: background 0.15s ease; }
    .erp-table tbody tr:hover { background: #F0F7FF; }
    .table-container { width: 100%; overflow-x: auto; }

    /* --- Badges --- */
    .ds-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ds-badge.success { background: rgba(16,185,129,0.1); color: #065F46; }
    .ds-badge.warning { background: rgba(245,158,11,0.1); color: #92400E; }
    .ds-badge.danger  { background: rgba(239,68,68,0.1);  color: #991B1B; }
    .ds-badge.info    { background: rgba(59,130,246,0.1); color: #1D4ED8; }

    /* --- Progress Bars --- */
    .status-summary-item { margin-bottom: 18px; }
    .status-summary-item:last-child { margin-bottom: 0; }
    .status-summary-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; color: #0F172A; margin-bottom: 7px; }
    .status-pct { font-size: 12px; color: #64748B; font-weight: 500; }
    .progress-bg { height: 9px; background: #F1F5F9; border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; }

    /* --- Alerts --- */
    .task-item { display: flex; gap: 12px; padding: 13px 14px; border-radius: 12px; margin-bottom: 10px; align-items: flex-start; border-left: 4px solid; transition: all 0.2s ease; }
    .task-item:last-child { margin-bottom: 0; }
    .task-item:hover { transform: translateX(3px); }
    .task-item.danger  { background: rgba(239,68,68,0.04);  border-left-color: #f00000ff; }
    .task-item.warning { background: rgba(245,158,11,0.05); border-left-color: #f89f04ff; }
    .task-item.info    { background: rgba(59,130,246,0.04); border-left-color: #0d64f0ff; }
    .task-item.success { background: rgba(16,185,129,0.04); border-left-color: #00ffaaff; }
    .task-icon-wrap { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .task-item.danger  .task-icon-wrap { background: rgba(239,68,68,0.1);  color: #f10909ff; }
    .task-item.warning .task-icon-wrap { background: rgba(245,158,11,0.1); color: #f89f04ff; }
    .task-item.info    .task-icon-wrap { background: rgba(59,130,246,0.1); color: #0562f8ff; }
    .task-item.success .task-icon-wrap { background: rgba(16,185,129,0.1); color: #07f8a8ff; }
    .task-content h5 { font-size: 13px; font-weight: 700; color: #0F172A; margin-bottom: 3px; }
    .task-content p  { font-size: 12px; color: #64748B; line-height: 1.5; }
    .amt-strong { font-weight: 800; color: #0F172A; font-size: 14px; }
    .amt-green  { color: #059669; }
    </style>

    <div class="dash-welcome">
        <div class="dash-welcome-inner">
            <div>
                <div class="dash-welcome-tag">
                    <i class="fa-solid fa-shield-halved"></i>
                    Super Admin Control Panel
                </div>
                <h2 class="dash-welcome-title">Welcome back, {{ Auth::user()->name ?? 'Administrator' }}!</h2>
                <p class="dash-welcome-sub">System-wide overview for today — {{ now()->format('l, d F Y') }}.</p>
            </div>
            <div class="dash-quick-actions">
                <a href="{{ route('firm-master.create') }}" class="dqa-btn"><i class="fa-solid fa-plus"></i> Add New Firm</a>
                <a href="{{ route('financial-years.create') }}" class="dqa-btn"><i class="fa-solid fa-calendar-plus"></i> Add FY</a>
                <a href="{{ route('users.create') }}" class="dqa-btn"><i class="fa-solid fa-user-plus"></i> Add User</a>
                <a href="{{ route('properties.create') }}" class="dqa-btn"><i class="fa-solid fa-building"></i> Add Property</a>
                <a href="{{ route('customers.create') }}" class="dqa-btn"><i class="fa-solid fa-users"></i> Add Customer</a>
            </div>
        </div>
    </div>

    <!-- ERP Statistics Section -->
    <div class="kpi-section-header">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#1E5AA8,#2F6FE4);border-radius:4px;flex-shrink:0;"></div>
        <h3>Firms &amp; Users Control</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Firms</span>
                <span class="kpi-value">{{ number_format($totalFirms) }}</span>
                <span class="kpi-badge bk-blue">Registered Firms</span>
            </div>
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-building"></i></div>
            <div class="kpi-deco deco-blue"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Active Firms</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($activeFirms) }}</span>
                <span class="kpi-badge bk-green">Operational</span>
            </div>
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-toggle-on"></i></div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Inactive Firms</span>
                <span class="kpi-value" style="color:#EF4444;">{{ number_format($inactiveFirms) }}</span>
                <span class="kpi-badge bk-red">Suspended</span>
            </div>
            <div class="kpi-icon-box ik-red"><i class="fa-solid fa-toggle-off"></i></div>
            <div class="kpi-deco deco-red"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Users</span>
                <span class="kpi-value">{{ number_format($totalUsers) }}</span>
                <span class="kpi-badge bk-purple">All Roles</span>
            </div>
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-user-gear"></i></div>
            <div class="kpi-deco deco-purple"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Active Users</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($activeUsers) }}</span>
                <span class="kpi-badge bk-green">Logged in</span>
            </div>
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-user-check"></i></div>
            <div class="kpi-deco deco-green"></div>
        </div>
    </div>

    <!-- Properties & Bookings -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#F59E0B,#EF4444);border-radius:4px;flex-shrink:0;"></div>
        <h3>Property Portfolio</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Customers</span>
                <span class="kpi-value">{{ number_format($totalCustomers) }}</span>
                <span class="kpi-badge bk-blue">ERP Clients</span>
            </div>
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-deco deco-blue"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Properties</span>
                <span class="kpi-value">{{ number_format($totalProperties) }}</span>
                <span class="kpi-badge bk-purple">All Units</span>
            </div>
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-city"></i></div>
            <div class="kpi-deco deco-purple"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Available Properties</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($availableProperties) }}</span>
                <span class="kpi-badge bk-green">On Market</span>
            </div>
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Sold Properties</span>
                <span class="kpi-value" style="color:#F59E0B;">{{ number_format($soldProperties) }}</span>
                <span class="kpi-badge bk-amber">Closed Sales</span>
            </div>
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-house-circle-xmark"></i></div>
            <div class="kpi-deco deco-amber"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Rented Properties</span>
                <span class="kpi-value" style="color:#0ea5e9ff;">{{ number_format($rentedProperties) }}</span>
                <span class="kpi-badge bk-sky">Active Lease</span>
            </div>
            <div class="kpi-icon-box ik-sky"><i class="fa-solid fa-key"></i></div>
            <div class="kpi-deco deco-sky"></div>
        </div>
    </div>

    <!-- System Finances -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#14B8A6);border-radius:4px;flex-shrink:0;"></div>
        <h3>System-Wide Finances</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 24px;">
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Bookings</span>
                <span class="kpi-value" style="color:#F97316;">{{ number_format($totalBookings) }}</span>
                <span class="kpi-badge bk-orange">Sales Contracts</span>
            </div>
            <div class="kpi-icon-box ik-orange"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="kpi-deco deco-orange"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Revenue</span>
                <span class="kpi-value" style="color:#10B981;">₹{{ number_format($totalReceivedAmt, 0) }}</span>
                <span class="kpi-badge bk-green">Total Received</span>
            </div>
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Total Expenses</span>
                <span class="kpi-value" style="color:#EF4444;">₹{{ number_format($totalExpenses, 0) }}</span>
                <span class="kpi-badge bk-red">All Outflows</span>
            </div>
            <div class="kpi-icon-box ik-red"><i class="fa-solid fa-receipt"></i></div>
            <div class="kpi-deco deco-red"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-info">
                <span class="kpi-label">Net Profit (Est.)</span>
                <span class="kpi-value" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};">₹{{ number_format($netProfit, 0) }}</span>
                <span class="kpi-badge {{ $netProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
            </div>
            <div class="kpi-icon-box {{ $netProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $netProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-deco {{ $netProfit >= 0 ? 'deco-green' : 'deco-red' }}"></div>
        </div>
    </div>

    @php
        $propTotal = max(1, $totalProperties);
        $availPct  = round(($availableProperties / $propTotal) * 100);
        $soldPct   = round(($soldProperties     / $propTotal) * 100);
    @endphp

    <div class="dashboard-grid" style="margin-top: 30px;">
        <div>
            <!-- Recent Customers -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-title-icon ik-blue"><i class="fa-solid fa-users"></i></div>
                        Recent Customers
                    </div>
                    <a href="{{ route('customers.index') }}" class="btn-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-container">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>City</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCustomers as $customer)
                                <tr>
                                    <td><strong>{{ $customer->name }}</strong></td>
                                    <td>{{ $customer->mobile }}</td>
                                    <td>{{ $customer->city ?? '-' }}</td>
                                    <td><span class="badge badge-{{ $customer->customer_type }}">{{ ucfirst($customer->customer_type) }}</span></td>
                                    <td><span class="badge badge-{{ $customer->status }}">{{ ucfirst($customer->status) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">No customers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Payments -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-title-icon ik-green"><i class="fa-solid fa-receipt"></i></div>
                        Recent Payments
                    </div>
                    <a href="{{ route('payments.index') }}" class="btn-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-container">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Property</th>
                                <th>Amount</th>
                                <th>Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                                    <td><strong>{{ $payment->customer->name ?? '-' }}</strong></td>
                                    <td>{{ $payment->property->name ?? '-' }}</td>
                                    <td class="amt-strong amt-green">₹{{ number_format($payment->payment_amount, 2) }}</td>
                                    <td><span class="ds-badge info">{{ $payment->paymentMode->mode_name ?? 'Direct' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center;">No payments recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div>
            <!-- Property Status -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-title-icon" style="background:rgba(245,158,11,0.1);"><i class="fa-solid fa-chart-pie" style="color:#F59E0B;"></i></div>
                        Property Status
                    </div>
                </div>
                <div>
                    <div class="status-summary-item">
                        <div class="status-summary-header">
                            <span>🟢 Available</span>
                            <span class="status-pct">{{ $availableProperties }} units ({{ $availPct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width:{{ $availPct }}%; background: #10B981;"></div>
                        </div>
                    </div>
                    <div class="status-summary-item">
                        <div class="status-summary-header">
                            <span>🟡 Sold</span>
                            <span class="status-pct">{{ $soldProperties }} units ({{ $soldPct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width:{{ $soldPct }}%; background: #F59E0B;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-title-icon" style="background:rgba(239,68,68,0.1);"><i class="fa-solid fa-bell" style="color:#EF4444;"></i></div>
                        Alerts
                    </div>
                </div>
                <div>
                    @if($totalPendingAmt > 0)
                        <div class="task-item warning">
                            <div class="task-icon-wrap"><i class="fa-solid fa-exclamation-triangle"></i></div>
                            <div class="task-content">
                                <h5>Pending Payments</h5>
                                <p>₹{{ number_format($totalPendingAmt, 0) }} outstanding.</p>
                            </div>
                        </div>
                    @else
                        <div class="task-item success">
                            <div class="task-icon-wrap"><i class="fa-solid fa-check"></i></div>
                            <div class="task-content">
                                <h5>All Clear</h5>
                                <p>No pending alerts.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
