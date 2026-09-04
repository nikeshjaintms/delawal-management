@extends('admin.layouts.app')
@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')
@section('content')
    <style>
    /* --- Welcome Banner --- */
    .dash-welcome {
        position: relative;
        background: rgba(15, 23, 42, 0.55) !important;
        background-color: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 24px !important;
        padding: 32px 38px; margin-bottom: 28px; overflow: hidden;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: box-shadow 0.3s ease, transform 0.3s ease, background 0.3s ease, border-color 0.3s ease;
    }
    .dash-welcome:hover {
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
    }
    .dash-welcome-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 22px; }
    .dash-welcome-tag {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        color: #FFFFFF !important;
        font-size: 11px; font-weight: 800;
        letter-spacing: 1.2px; text-transform: uppercase; padding: 6px 14px; border-radius: 20px;
        margin-bottom: 11px; backdrop-filter: blur(8px);
    }
    .dash-welcome-title { font-size: 28px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 7px; text-shadow: 0 2px 8px rgba(0,0,0,0.6); }
    .dash-welcome-sub { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; line-height: 1.5; }
    .dash-quick-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .dqa-btn {
        display: inline-flex !important; align-items: center !important; gap: 8px !important;
        background: rgba(255, 255, 255, 0.08) !important;
        background-color: rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
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
    .kpi-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
    .kpi-section-divider { flex: 1; height: 1px; background: rgba(255, 255, 255, 0.12) !important; }

    /* --- KPI Grid --- */
    .kpi-grid-4 {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 16px;
    }
    .kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px;
    }
    .kpi-grid-2 {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px;
    }
    @media(max-width:1440px) { .kpi-grid-4, .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(4, 1fr); } }
    @media(max-width:1280px) { .kpi-grid-4, .kpi-grid, .kpi-grid-2 { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
    @media(max-width:580px)  { .kpi-grid-4, .kpi-grid, .kpi-grid-2 { grid-template-columns: 1fr; } }

    /* --- KPI Cards --- */
    .kpi-card {
        background: rgba(15, 23, 42, 0.55) !important;
        background-color: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 18px !important;
        padding: 14px 14px; position: relative; overflow: hidden;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: transform 0.22s cubic-bezier(0.4,0,0.2,1), box-shadow 0.22s cubic-bezier(0.4,0,0.2,1), border-color 0.22s ease, background 0.22s ease;
        display: flex; align-items: center; gap: 10px; min-width: 0;
    }
    .kpi-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
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

    /* --- Section Cards (Recent Customers, Property Status, Alerts, Recent Payments) --- */
    .section-card {
        background: rgba(15, 23, 42, 0.55) !important;
        background-color: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 22px !important;
        padding: 26px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        margin-bottom: 24px;
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.25s ease, background 0.25s ease;
    }
    .section-card:hover {
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
        transform: translateY(-2px);
    }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .section-title { font-size: 15px; font-weight: 800; color: #FFFFFF !important; display: flex; align-items: center; gap: 10px; }
    .section-title-icon { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }

    .btn-view-all {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        color: #FFFFFF !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .btn-view-all:hover {
        background: rgba(255, 255, 255, 0.18);
        color: #FFFFFF !important;
        transform: translateY(-1px);
        border-color: rgba(255, 255, 255, 0.28);
    }

    /* --- Tables inside Glass Cards --- */
    .erp-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .erp-table th { padding: 12px 16px; background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8; font-weight: 800; border-bottom: 1px solid rgba(255, 255, 255, 0.10); font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .erp-table td { padding: 13px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0; vertical-align: middle; }
    .erp-table td strong { color: #FFFFFF !important; }
    .erp-table tr:last-child td { border-bottom: none; }
    .erp-table tbody tr { transition: background 0.18s ease; }
    .erp-table tbody tr:hover { background: rgba(255, 255, 255, 0.06) !important; }
    .table-container { width: 100%; overflow-x: auto; background: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 16px; backdrop-filter: blur(8px); }

    /* --- Badges --- */
    .ds-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ds-badge.success { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
    .ds-badge.warning { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
    .ds-badge.danger  { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }
    .ds-badge.info    { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }

    /* --- Progress Bars --- */
    .status-summary-item { margin-bottom: 18px; }
    .status-summary-item:last-child { margin-bottom: 0; }
    .status-summary-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 7px; }
    .status-pct { font-size: 12px; color: #94A3B8; font-weight: 600; }
    .progress-bg { height: 10px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; }

    /* --- Alerts --- */
    .task-item { display: flex; gap: 12px; padding: 14px 16px; border-radius: 14px; margin-bottom: 12px; align-items: flex-start; border-left: 4px solid; transition: all 0.2s ease; background: rgba(255, 255, 255, 0.04) !important; border: 1px solid rgba(255, 255, 255, 0.10); }
    .task-item:last-child { margin-bottom: 0; }
    .task-item:hover { transform: translateX(3px); background: rgba(255, 255, 255, 0.08) !important; border-color: rgba(255, 255, 255, 0.20); }
    .task-item.danger  { border-left-color: #EF4444; }
    .task-item.warning { border-left-color: #F59E0B; }
    .task-item.info    { border-left-color: #3B82F6; }
    .task-item.success { border-left-color: #10B981; }
    .task-icon-wrap { width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .task-item.danger  .task-icon-wrap { background: rgba(239,68,68,0.22); color: #F87171; border: 1px solid rgba(239,68,68,0.35); }
    .task-item.warning .task-icon-wrap { background: rgba(245,158,11,0.22); color: #FBBF24; border: 1px solid rgba(245,158,11,0.35); }
    .task-item.info    .task-icon-wrap { background: rgba(59,130,246,0.22); color: #60A5FA; border: 1px solid rgba(59,130,246,0.35); }
    .task-item.success .task-icon-wrap { background: rgba(16,185,129,0.22); color: #34D399; border: 1px solid rgba(16,185,129,0.35); }
    .task-content h5 { font-size: 13.5px; font-weight: 700; color: #FFFFFF; margin-bottom: 3px; }
    .task-content p  { font-size: 12px; color: #94A3B8; line-height: 1.5; }
    .amt-strong { font-weight: 800; color: #FFFFFF; font-size: 14px; }
    .amt-green  { color: #34D399; }

    /* --- Status Summaries --- */
    .summary-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; margin-top: 30px; }
    .summary-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; }
    .summary-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px;
    }
    @media(max-width:1100px) { .summary-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:768px)  { .summary-grid { grid-template-columns: 1fr; } }

    .summary-card {
        background: rgba(15, 23, 42, 0.55) !important;
        background-color: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 22px !important;
        padding: 24px 26px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        position: relative;
        transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
    }
    .summary-title {
        font-size: 15px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 16px;
        display: flex; align-items: center; gap: 8px;
    }
    .chip-row { display: flex; flex-wrap: wrap; gap: 10px; }
    .chip {
        display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px;
        border-radius: 24px; font-size: 12.5px; font-weight: 700; white-space: nowrap;
        transition: transform 0.18s, box-shadow 0.18s;
        cursor: default;
    }
    .chip:hover { transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.25); }
    .chip-num { font-size: 15px; font-weight: 800; line-height: 1; }

    .ch-green  { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; }
    .ch-orange { background: rgba(249, 115, 22, 0.15) !important; border: 1px solid rgba(249, 115, 22, 0.30) !important; color: #FB923C !important; }
    .ch-sky    { background: rgba(14, 165, 233, 0.15) !important; border: 1px solid rgba(14, 165, 233, 0.30) !important; color: #38BDF8 !important; }
    .ch-blue   { background: rgba(59, 130, 246, 0.15) !important; border: 1px solid rgba(59, 130, 246, 0.30) !important; color: #60A5FA !important; }
    .ch-red    { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; color: #F87171 !important; }
    .ch-purple { background: rgba(139, 92, 246, 0.15) !important; border: 1px solid rgba(139, 92, 246, 0.30) !important; color: #A78BFA !important; }
    .ch-amber  { background: rgba(245, 158, 11, 0.15) !important; border: 1px solid rgba(245, 158, 11, 0.30) !important; color: #FBBF24 !important; }

    /* Summary table rows */
    .summary-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { font-size: 13px; color: #CBD5E1; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .summary-val { font-size: 14px; font-weight: 800; color: #FFFFFF; }
    .summary-val.g { color: #34D399 !important; }
    .summary-val.r { color: #F87171 !important; }
    .summary-val.o { color: #FB923C !important; }
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
                <a href="{{ route('firm-master.create') }}" class="dqa-btn dqa-blue"><i class="fa-solid fa-plus"></i> Add New Firm</a>
                <a href="{{ route('financial-years.create') }}" class="dqa-btn dqa-green"><i class="fa-solid fa-calendar-plus"></i> Add FY</a>
                <a href="{{ route('users.create') }}" class="dqa-btn dqa-purple"><i class="fa-solid fa-user-plus"></i> Add User</a>
                <a href="{{ route('expenses.create') }}" class="dqa-btn dqa-red"><i class="fa-solid fa-receipt"></i> Add Expense</a>
                <a href="{{ route('properties.create') }}" class="dqa-btn dqa-amber"><i class="fa-solid fa-building"></i> Add Property</a>
                <a href="{{ route('customers.create') }}" class="dqa-btn dqa-teal"><i class="fa-solid fa-users"></i> Add Customer</a>
            </div>
        </div>
    </div>

    <!-- ERP Statistics Section -->
    <div class="kpi-section-header">
        <div style="width:4px;height:18px;background:#2563EB;border-radius:3px;flex-shrink:0;"></div>
        <h3>Firms &amp; Users Control</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-4">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-building"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Firms</span>
                <span class="kpi-value">{{ number_format($totalFirms) }}</span>
                <span class="kpi-badge bk-blue">Registered Firms</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-toggle-on"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Active Firms</span>
                <span class="kpi-value">{{ number_format($activeFirms) }}</span>
                <span class="kpi-badge bk-blue">Operational</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-toggle-off"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Inactive Firms</span>
                <span class="kpi-value">{{ number_format($inactiveFirms) }}</span>
                <span class="kpi-badge bk-blue">Suspended</span>
            </div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-user-gear"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Users</span>
                <span class="kpi-value">{{ number_format($totalUsers) }}</span>
                <span class="kpi-badge bk-blue">All Roles</span>
            </div>
        </div>
    </div>

    <div class="kpi-grid-4" style="margin-bottom: 28px;">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-user-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Active Users</span>
                <span class="kpi-value">{{ number_format($activeUsers) }}</span>
                <span class="kpi-badge bk-blue">Logged in</span>
            </div>
        </div>
    </div>

    <!-- Properties & Bookings -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:4px;height:18px;background:#EA580C;border-radius:3px;flex-shrink:0;"></div>
        <h3>Property Portfolio</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-indigo"><i class="fa-solid fa-users"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Customers</span>
                <span class="kpi-value">{{ number_format($totalCustomers) }}</span>
                <span class="kpi-badge bk-indigo">ERP Clients</span>
            </div>
            <div class="kpi-deco deco-indigo"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-city"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Projects</span>
                <span class="kpi-value">{{ number_format($totalProjects) }}</span>
                <span class="kpi-badge bk-blue">All Projects</span>
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
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-city"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Properties</span>
                <span class="kpi-value">{{ number_format($totalProperties) }}</span>
                <span class="kpi-badge bk-purple">All Units</span>
            </div>
            <div class="kpi-deco deco-purple"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Available Properties</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($availableProperties) }}</span>
                <span class="kpi-badge bk-green">On Market</span>
            </div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-house-circle-xmark"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Sold Properties</span>
                <span class="kpi-value" style="color:#F59E0B;">{{ number_format($soldProperties) }}</span>
                <span class="kpi-badge bk-amber">Closed Sales</span>
            </div>
            <div class="kpi-deco deco-amber"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-sky"><i class="fa-solid fa-key"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Rented Properties</span>
                <span class="kpi-value" style="color:#0ea5e9ff;">{{ number_format($rentedProperties) }}</span>
                <span class="kpi-badge bk-sky">Active Lease</span>
            </div>
            <div class="kpi-deco deco-sky"></div>
        </div>
    </div>

    <!-- System Finances -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#14B8A6);border-radius:4px;flex-shrink:0;"></div>
        <h3>System-Wide Finances</h3>
        <div class="kpi-section-divider"></div>
    </div>    <div class="kpi-grid-4" style="margin-bottom: 24px;">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-orange"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Bookings</span>
                <span class="kpi-value" style="color:#F97316;" title="{{ number_format($totalBookings) }}">{{ number_format($totalBookings) }}</span>
                <span class="kpi-badge bk-orange">Sales Contracts</span>
            </div>
            <div class="kpi-deco deco-orange"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Revenue</span>
                <span class="kpi-value" style="color:#10B981;" title="₹{{ number_format($totalReceivedAmt, 0) }}">₹{{ number_format($totalReceivedAmt, 0) }}</span>
                <span class="kpi-badge bk-green">Total Received</span>
            </div>
            <div class="kpi-deco deco-green"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box ik-red"><i class="fa-solid fa-receipt"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Expenses</span>
                <span class="kpi-value" style="color:#EF4444;" title="₹{{ number_format($totalExpenses, 0) }}">₹{{ number_format($totalExpenses, 0) }}</span>
                <span class="kpi-badge bk-red">All Outflows</span>
            </div>
            <div class="kpi-deco deco-red"></div>
        </div>
        <div class="kpi-card">
            <div class="kpi-icon-box {{ $netProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $netProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Net Profit (Est.)</span>
                <span class="kpi-value" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};" title="₹{{ number_format($netProfit, 0) }}">₹{{ number_format($netProfit, 0) }}</span>
                <span class="kpi-badge {{ $netProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</span>
            </div>
            <div class="kpi-deco {{ $netProfit >= 0 ? 'deco-green' : 'deco-red' }}"></div>
        </div>
    </div>

    <!-- Status Summaries Section -->
    <div class="summary-section-header">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#F97316,#EA580C);border-radius:4px;flex-shrink:0;"></div>
        <h3>Status Summaries</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="summary-grid">
        <!-- Property Status Summary -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-house-circle-check" style="color:#10B981;"></i> Property Portfolio
            </div>
            <div class="chip-row">
                <div class="chip ch-green">
                    <span class="chip-num">{{ $availableProperties }}</span> Available
                </div>
                <div class="chip ch-orange">
                    <span class="chip-num">{{ $soldProperties }}</span> Sold
                </div>
                <div class="chip ch-sky">
                    <span class="chip-num">{{ $rentedProperties }}</span> Rented
                </div>
                <div class="chip ch-blue">
                    <span class="chip-num">{{ $totalProperties }}</span> Total Units
                </div>
            </div>
        </div>

        <!-- Firms & Users Summary -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-users-gear" style="color:#3B82F6;"></i> Firms &amp; Users
            </div>
            <div class="chip-row">
                <div class="chip ch-green">
                    <span class="chip-num">{{ $activeFirms }}</span> Active Firms
                </div>
                <div class="chip ch-red">
                    <span class="chip-num">{{ $inactiveFirms }}</span> Inactive
                </div>
                <div class="chip ch-blue">
                    <span class="chip-num">{{ $activeUsers }}</span> Active Users
                </div>
                <div class="chip ch-purple">
                    <span class="chip-num">{{ $totalUsers }}</span> Total Users
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-wallet" style="color:#F59E0B;"></i> Financial Summary
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-money-bill-trend-up" style="color:#10B981;"></i> Total Revenue</span>
                <span class="summary-val g">₹{{ number_format($totalReceivedAmt, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-receipt" style="color:#EF4444;"></i> Total Expenses</span>
                <span class="summary-val r">₹{{ number_format($totalExpenses, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-chart-line" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};"></i> Net Profit (Est.)</span>
                <span class="summary-val {{ $netProfit >= 0 ? 'g' : 'r' }}">₹{{ number_format($netProfit, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-clock-rotate-left" style="color:#F97316;"></i> Outstanding</span>
                <span class="summary-val o">₹{{ number_format($totalPendingAmt, 0) }}</span>
            </div>
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
