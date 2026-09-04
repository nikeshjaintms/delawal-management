@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')
    <style>
    /* --- Welcome Banner --- */
    .dash-welcome {
        position: relative;
        background: rgba(20, 27, 41, 0.70) !important;
        background-color: rgba(20, 27, 41, 0.70) !important;
        backdrop-filter: blur(24px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.20) !important;
        border-radius: 24px !important;
        padding: 32px 38px; margin-bottom: 28px; overflow: hidden;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .dash-welcome:hover {
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        background: rgba(20, 27, 41, 0.80) !important;
    }
    .dash-welcome-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 22px; }
    .dash-welcome-tag {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        font-size: 11px; font-weight: 800;
        letter-spacing: 1.2px; text-transform: uppercase; padding: 6px 14px; border-radius: 20px;
        margin-bottom: 11px; backdrop-filter: blur(8px);
    }
    .dash-welcome-title { font-size: 28px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 7px; }
    .dash-welcome-sub { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; line-height: 1.5; }
    .dash-quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .dqa-btn {
        display: inline-flex !important; align-items: center !important; gap: 8px !important;
        background: rgba(255, 255, 255, 0.08) !important;
        background-color: rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        padding: 9px 18px !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
    }
    .dqa-btn i { font-size: 13px; color: #FFFFFF !important; transition: transform 0.2s ease !important; opacity: 1 !important; }
    .dqa-btn:hover { transform: translateY(-2px) scale(1.03) !important; color: #FFFFFF !important; }
    .dqa-btn:hover i { color: #FFFFFF !important; transform: scale(1.15) !important; }

    .dqa-blue:hover   { background: #2563EB !important; border-color: #3B82F6 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(37, 99, 235, 0.5) !important; }
    .dqa-green:hover  { background: #10B981 !important; border-color: #34D399 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(16, 185, 129, 0.5) !important; }
    .dqa-purple:hover { background: #8B5CF6 !important; border-color: #A78BFA !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(139, 92, 246, 0.5) !important; }
    .dqa-red:hover    { background: #EF4444 !important; border-color: #F87171 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(239, 68, 68, 0.5) !important; }
    .dqa-amber:hover  { background: #D97706 !important; border-color: #FBBF24 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(217, 119, 6, 0.5) !important; }
    .dqa-teal:hover   { background: #0D9488 !important; border-color: #2DD4BF !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(13, 148, 136, 0.5) !important; }

    /* --- KPI Section Header --- */
    .kpi-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .kpi-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; }
    .kpi-section-divider { flex: 1; height: 1px; background: rgba(255, 255, 255, 0.12) !important; }

    /* --- KPI Grid --- */
    .kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px;
    }
    .kpi-grid-2 {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px;
    }
    @media(max-width:1440px) { .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(4, 1fr); } }
    @media(max-width:1280px) { .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
    @media(max-width:580px)  { .kpi-grid, .kpi-grid-2 { grid-template-columns: 1fr; } }

    /* --- KPI Cards --- */
    .kpi-card {
        background: rgba(20, 27, 41, 0.65) !important;
        background-color: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 18px !important;
        padding: 14px 14px; position: relative; overflow: hidden;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: transform 0.22s cubic-bezier(0.4,0,0.2,1), box-shadow 0.22s cubic-bezier(0.4,0,0.2,1), border-color 0.22s ease, background 0.22s ease;
        display: flex; align-items: center; gap: 10px; min-width: 0;
    }
    .kpi-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(20, 27, 41, 0.80) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.20) !important;
    }
    .kpi-card::before, .kpi-card::after { display: none !important; }

    /* Icon Box styling in stat cards */
    .kpi-icon-box {
        width: 36px; height: 36px; min-width: 36px; border-radius: 10px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 15px;
        background: rgba(59, 130, 246, 0.20); border: 1px solid rgba(59, 130, 246, 0.35); color: #60A5FA;
        transition: transform 0.2s ease;
    }
    .ik-blue   { background: rgba(59, 130, 246, 0.20); border-color: rgba(59, 130, 246, 0.40); color: #60A5FA; }
    .ik-green  { background: rgba(16, 185, 129, 0.20); border-color: rgba(16, 185, 129, 0.40); color: #34D399; }
    .ik-red    { background: rgba(239, 68, 68, 0.20); border-color: rgba(239, 68, 68, 0.40); color: #F87171; }
    .ik-purple { background: rgba(139, 92, 246, 0.20); border-color: rgba(139, 92, 246, 0.40); color: #A78BFA; }
    .ik-teal   { background: rgba(20, 184, 166, 0.20); border-color: rgba(20, 184, 166, 0.40); color: #2DD4BF; }
    .ik-amber  { background: rgba(245, 158, 11, 0.20); border-color: rgba(245, 158, 11, 0.40); color: #FBBF24; }
    .ik-orange { background: rgba(249, 115, 22, 0.20); border-color: rgba(249, 115, 22, 0.40); color: #FB923C; }
    .ik-sky    { background: rgba(14, 165, 233, 0.20); border-color: rgba(14, 165, 233, 0.40); color: #38BDF8; }
    .ik-indigo { background: rgba(99, 102, 241, 0.20); border-color: rgba(99, 102, 241, 0.40); color: #818CF8; }

    .kpi-card:hover .kpi-icon-box { transform: scale(1.10); }
    .kpi-deco { display: none !important; }
    .kpi-info { display: flex; flex-direction: column; z-index: 2; flex: 1; min-width: 0; overflow: hidden; }
    .kpi-label { font-size: 10.5px; font-weight: 800; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; line-height: 1.2; white-space: normal; word-break: break-word; }
    .kpi-value { font-size: clamp(14px, 1.1vw, 17px) !important; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 2px; font-variant-numeric: tabular-nums; white-space: nowrap; letter-spacing: -0.4px; }
    .kpi-badge { font-size: 11px; font-weight: 700; display: inline-block; width: fit-content; white-space: nowrap; }

    .bk-blue   { color: #3B82F6 !important; }
    .bk-green  { color: #10B981 !important; }
    .bk-red    { color: #EF4444 !important; }
    .bk-purple { color: #8B5CF6 !important; }
    .bk-teal   { color: #14B8A6 !important; }
    .bk-amber  { color: #F59E0B !important; }
    .bk-orange { color: #F97316 !important; }
    .bk-sky    { color: #0EA5E9 !important; }
    .bk-indigo { color: #6366F1 !important; }

    /* --- Dashboard Bottom Grid --- */
    .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 22px; margin-bottom: 24px; }
    @media(max-width:992px) { .dashboard-grid { grid-template-columns: 1fr; } }

    /* --- Section Cards --- */
    .section-card {
        background: rgba(20, 27, 41, 0.65) !important;
        background-color: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 20px !important;
        padding: 24px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        margin-bottom: 20px;
        transition: box-shadow 0.22s ease, border-color 0.22s ease, background 0.22s ease;
    }
    .section-card:hover {
        background: rgba(20, 27, 41, 0.80) !important;
        border-color: rgba(255, 255, 255, 0.20) !important;
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.45) !important;
    }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .section-title { font-size: 15px; font-weight: 800; color: #FFFFFF !important; display: flex; align-items: center; gap: 9px; }
    .section-title-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

    /* --- Tables --- */
    .erp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .erp-table th { padding: 11px 14px; background: rgba(255, 255, 255, 0.05); color: #94A3B8; font-weight: 800; border-bottom: 1px solid rgba(255, 255, 255, 0.10); font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .erp-table td { padding: 13px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0; vertical-align: middle; }
    .erp-table td strong { color: #FFFFFF !important; }
    .erp-table tr:last-child td { border-bottom: none; }
    .erp-table tbody tr { transition: background 0.15s ease; }
    .erp-table tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
    .table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 18px; }

    /* --- Badges --- */
    .ds-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ds-badge.success { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30); }
    .ds-badge.warning { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.30); }
    .ds-badge.danger  { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.30); }
    .ds-badge.info    { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); }

    /* --- Progress Bars --- */
    .status-summary-item { margin-bottom: 18px; }
    .status-summary-item:last-child { margin-bottom: 0; }
    .status-summary-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 7px; }
    .status-pct { font-size: 12px; color: #94A3B8; font-weight: 600; }
    .progress-bg { height: 9px; background: rgba(255, 255, 255, 0.10); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; }

    /* --- Alerts --- */
    .task-item { display: flex; gap: 12px; padding: 13px 14px; border-radius: 12px; margin-bottom: 10px; align-items: flex-start; border-left: 4px solid; transition: all 0.2s ease; background: rgba(20, 27, 41, 0.65) !important; border: 1px solid rgba(255, 255, 255, 0.10); }
    .task-item:last-child { margin-bottom: 0; }
    .task-item:hover { transform: translateX(3px); background: rgba(20, 27, 41, 0.85) !important; }
    .task-item.danger  { border-left-color: #EF4444; }
    .task-item.warning { border-left-color: #F59E0B; }
    .task-item.info    { border-left-color: #3B82F6; }
    .task-item.success { border-left-color: #10B981; }
    .task-icon-wrap { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .task-icon-wrap.danger  { background: rgba(239,68,68,0.2); color: #F87171; }
    .task-icon-wrap.warning { background: rgba(245,158,11,0.2); color: #FBBF24; }
    .task-icon-wrap.info    { background: rgba(59,130,246,0.2); color: #60A5FA; }
    .task-icon-wrap.success { background: rgba(16,185,129,0.2); color: #34D399; }
    .task-content h5 { font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 3px; }
    .task-content p  { font-size: 12px; color: #94A3B8; line-height: 1.5; }
    .amt-strong { font-weight: 800; color: #FFFFFF; font-size: 14px; }
    .amt-green  { color: #34D399; }
    </style>

    <div class="dash-welcome">
        <div class="dash-welcome-dot"></div>
        <div class="dash-welcome-inner">
            <div>
                <div class="dash-welcome-tag">
                    <i class="fa-solid fa-building-columns"></i>
                    Real Estate ERP &amp; Property Management
                </div>
                <h2 class="dash-welcome-title">Welcome back, {{ session('firm_name') }}!</h2>
                <p class="dash-welcome-sub">Here's your firm overview for today — {{ now()->format('l, d F Y') }}.</p>
            </div>
            <div class="dash-quick-actions">
                <a href="{{ route('properties.create') }}"  class="dqa-btn dqa-blue"><i class="fa-solid fa-plus"></i> Add Property</a>
                <a href="{{ route('customers.create') }}"   class="dqa-btn dqa-green"><i class="fa-solid fa-user-plus"></i> Add Customer</a>
                <a href="{{ route('payments.create') }}"    class="dqa-btn dqa-purple"><i class="fa-solid fa-money-bill-wave"></i> Add Payment</a>
                <a href="{{ route('expenses.create') }}"    class="dqa-btn dqa-red"><i class="fa-solid fa-receipt"></i> Add Expense</a>
                <a href="{{ route('rentals.create') }}"     class="dqa-btn dqa-teal"><i class="fa-solid fa-key"></i> Add Rental</a>
            </div>
        </div>
    </div>

    @php
        $propTotal = max(1, $totalProperties);
        $availPct  = round(($availableProperties / $propTotal) * 100);
        $soldPct   = round(($soldProperties     / $propTotal) * 100);
        $bookedPct = round(($bookedProperties   / $propTotal) * 100);
        $rentedPct = round(($rentedProperties   / $propTotal) * 100);
        $netProfit = $totalReceivedAmt - $totalExpenses;
    @endphp

    <div class="kpi-section-header">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#3B82F6,#8B5CF6);border-radius:4px;flex-shrink:0;"></div>
        <h3>People &amp; Properties</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Customers</span>
                <span class="kpi-value">{{ number_format($totalCustomers) }}</span>
                <span class="kpi-badge bk-purple">{{ $newCustomersMonth > 0 ? "+$newCustomersMonth this month" : 'All clients' }}</span>
            </div>
            <div class="kpi-deco deco-purple"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-city"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Projects</span>
                <span class="kpi-value">{{ number_format($totalProjects) }}</span>
                <span class="kpi-badge bk-blue">All projects</span>
            </div>
            <div class="kpi-deco deco-blue"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Active Projects</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($activeProjects) }}</span>
                <span class="kpi-badge bk-green">Operational</span>
            </div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-building"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Properties</span>
                <span class="kpi-value">{{ number_format($totalProperties) }}</span>
                <span class="kpi-badge bk-blue">All units</span>
            </div>
            <div class="kpi-deco deco-blue"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Available Units</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($availableProperties) }}</span>
                <span class="kpi-badge bk-green">{{ $availPct }}% of portfolio</span>
            </div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-orange"><i class="fa-solid fa-file-contract"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Bookings</span>
                <span class="kpi-value" style="color:#F97316;">{{ number_format($totalBookings) }}</span>
                <span class="kpi-badge bk-orange">{{ $bookedPct }}% booked</span>
            </div>
            <div class="kpi-deco deco-orange"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-handshake"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Sold Properties</span>
                <span class="kpi-value" style="color:#D97706;">{{ number_format($soldProperties) }}</span>
                <span class="kpi-badge bk-amber">{{ $soldPct }}% sold</span>
            </div>
            <div class="kpi-deco deco-amber"></div>
        </div>
    </div>

    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#F59E0B);border-radius:4px;flex-shrink:0;"></div>
        <h3>Financial Overview</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-2">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-teal"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Received Amount</span>
                <span class="kpi-value" style="color:#10B981;">₹{{ number_format($totalReceivedAmt, 0) }}</span>
                <span class="kpi-badge bk-teal">Payments collected</span>
            </div>
            <div class="kpi-deco deco-teal"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box {{ $totalPendingAmt > 0 ? 'ik-red' : 'ik-green' }}"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Pending Amount</span>
                <span class="kpi-value" style="color:{{ $totalPendingAmt > 0 ? '#EF4444' : '#34D399' }};">₹{{ number_format($totalPendingAmt, 0) }}</span>
                <span class="kpi-badge {{ $totalPendingAmt > 0 ? 'bk-red' : 'bk-green' }}">{{ $totalPendingAmt > 0 ? 'Outstanding Dues' : 'All Payments Cleared' }}</span>
            </div>
            <div class="kpi-deco deco-{{ $totalPendingAmt > 0 ? 'red' : 'green' }}"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-sky"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Rental Income</span>
                <span class="kpi-value" style="color:#14B8A6;">₹{{ number_format($totalRentalIncome, 0) }}</span>
                <span class="kpi-badge bk-sky">{{ $activeRentals }} active rentals</span>
            </div>
            <div class="kpi-deco deco-sky"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-rose"><i class="fa-solid fa-receipt"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Expenses</span>
                <span class="kpi-value" style="color:#F43F5E;">₹{{ number_format($totalExpenses, 0) }}</span>
                <span class="kpi-badge bk-rose">All expenses</span>
            </div>
            <div class="kpi-deco deco-rose"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box {{ $netProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $netProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Net Profit (Est.)</span>
                <span class="kpi-value" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};">₹{{ number_format($netProfit, 0) }}</span>
                <span class="kpi-badge {{ $netProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
            </div>
            <div class="kpi-deco {{ $netProfit >= 0 ? 'deco-green' : 'deco-red' }}"></div>
        </div>
    </div>

    <div class="dashboard-grid">
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
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
