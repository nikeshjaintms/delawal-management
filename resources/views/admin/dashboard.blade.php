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
        padding: 28px 34px; margin-bottom: 22px; overflow: hidden;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: box-shadow 0.3s ease, transform 0.3s ease, background 0.3s ease, border-color 0.3s ease;
    }
    .dash-welcome:hover {
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
    }
    .dash-welcome-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
    .dash-welcome-tag {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        color: #FFFFFF !important;
        font-size: 11px; font-weight: 800;
        letter-spacing: 1.2px; text-transform: uppercase; padding: 5px 12px; border-radius: 20px;
        margin-bottom: 9px; backdrop-filter: blur(8px);
    }
    .dash-welcome-title { font-size: 26px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 6px; text-shadow: 0 2px 8px rgba(0,0,0,0.6); }
    .dash-welcome-sub { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; line-height: 1.4; }
    .dash-quick-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .dqa-btn {
        display: inline-flex !important; align-items: center !important; gap: 7px !important;
        background: rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        color: #FFFFFF !important;
        padding: 7px 14px !important;
        border-radius: 10px !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.20) !important;
    }
    .dqa-btn i { font-size: 12px; color: #FFFFFF !important; transition: transform 0.2s ease !important; opacity: 1 !important; }
    .dqa-btn:hover { transform: translateY(-2px) scale(1.02) !important; color: #FFFFFF !important; }
    .dqa-btn:hover i { color: #FFFFFF !important; transform: scale(1.15) !important; }

    .dqa-indigo:hover  { background: #4F46E5 !important; border-color: #6366F1 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5) !important; }
    .dqa-slate:hover   { background: #475569 !important; border-color: #64748B !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(71, 85, 105, 0.5) !important; }
    .dqa-sky:hover     { background: #0284C7 !important; border-color: #38BDF8 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(2, 132, 199, 0.5) !important; }
    .dqa-purple:hover  { background: #9333EA !important; border-color: #A855F7 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(147, 51, 234, 0.5) !important; }
    .dqa-teal:hover    { background: #0D9488 !important; border-color: #14B8A6 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(13, 148, 136, 0.5) !important; }
    .dqa-amber:hover   { background: #D97706 !important; border-color: #F59E0B !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(217, 119, 6, 0.5) !important; }
    .dqa-blue:hover    { background: #2563EB !important; border-color: #3B82F6 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.5) !important; }
    .dqa-violet:hover  { background: #7C3AED !important; border-color: #8B5CF6 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(124, 58, 237, 0.5) !important; }
    .dqa-emerald:hover { background: #059669 !important; border-color: #10B981 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(5, 150, 105, 0.5) !important; }
    .dqa-rose:hover    { background: #E11D48 !important; border-color: #F43F5E !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(225, 29, 72, 0.5) !important; }
    .dqa-red:hover     { background: #DC2626 !important; border-color: #EF4444 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5) !important; }
    .dqa-green:hover   { background: #16A34A !important; border-color: #22C55E !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5) !important; }
    .dqa-bronze:hover  { background: #854D0E !important; border-color: #A16207 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(133, 77, 14, 0.5) !important; }
    .dqa-fuchsia:hover { background: #C026D3 !important; border-color: #D946EF !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(192, 38, 211, 0.5) !important; }
    .dqa-orange:hover  { background: #EA580C !important; border-color: #FB923C !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(234, 88, 12, 0.5) !important; }
    .dqa-cyan:hover    { background: #0891B2 !important; border-color: #06B6D4 !important; color: #FFFFFF !important; box-shadow: 0 6px 20px rgba(8, 145, 178, 0.5) !important; }

    /* --- FILTER SECTION --- */
    .dash-filter-card {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(16px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        border-radius: 20px !important;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 14px 38px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.12) !important;
    }
    .filter-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .filter-title {
        font-size: 14px;
        font-weight: 800;
        color: #FFFFFF !important;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-tabs-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .filter-tab-btn {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.06);
        color: #CBD5E1 !important;
    }
    .filter-tab-btn:hover {
        background: rgba(255, 255, 255, 0.14);
        color: #FFFFFF !important;
        transform: translateY(-1px);
    }
    .filter-tab-btn.active-all {
        background: linear-gradient(135deg, #2563EB, #1D4ED8) !important;
        border-color: #60A5FA !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
    }
    .filter-tab-btn.active-prop {
        background: linear-gradient(135deg, #7C3AED, #6D28D9) !important;
        border-color: #A78BFA !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4);
    }
    .filter-tab-btn.active-proj {
        background: linear-gradient(135deg, #0D9488, #0F766E) !important;
        border-color: #2DD4BF !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4);
    }

    .filter-inputs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 14px;
        align-items: flex-end;
    }
    @media(max-width: 900px) {
        .filter-inputs-grid { grid-template-columns: 1fr; }
    }
    .filter-input-group label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }
    .dash-select-input {
        width: 100%;
        background: rgba(15, 23, 42, 0.85) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 12px !important;
        padding: 9px 14px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        outline: none !important;
        cursor: pointer;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        transition: all 0.2s ease;
    }
    .dash-select-input:focus {
        border-color: #38BDF8 !important;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25) !important;
    }
    .dash-select-input option {
        background: #0F172A !important;
        color: #FFFFFF !important;
        padding: 8px 12px;
    }
    .btn-filter-reset {
        padding: 9px 16px;
        border-radius: 12px;
        font-size: 12.5px;
        font-weight: 700;
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.30);
        color: #F87171 !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.2s ease;
        height: 40px;
    }
    .btn-filter-reset:hover {
        background: rgba(239, 68, 68, 0.25);
        border-color: #EF4444;
        color: #FFFFFF !important;
        transform: translateY(-1px);
    }

    /* Active Filter Details Pill Banner */
    .filter-active-pill-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 12px 18px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        margin-top: 14px;
    }
    .filter-pill-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-pill-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    .f-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .fb-purple { background: rgba(139, 92, 246, 0.2); border: 1px solid rgba(139, 92, 246, 0.4); color: #C4B5FD; }
    .fb-teal   { background: rgba(20, 184, 166, 0.2); border: 1px solid rgba(20, 184, 166, 0.4); color: #5EEAD4; }
    .fb-blue   { background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); color: #93C5FD; }
    .fb-amber  { background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #FDE68A; }

    /* --- KPI Section Header --- */
    .kpi-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .kpi-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; text-shadow: 0 1px 4px rgba(0,0,0,0.6); }
    .kpi-section-divider { flex: 1; height: 1px; background: rgba(255, 255, 255, 0.12) !important; }

    /* --- KPI Grid --- */
    .kpi-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 16px; }
    .kpi-grid   { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
    .kpi-grid-5 { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 20px; }
    @media(max-width:1440px) { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: repeat(4, 1fr); } }
    @media(max-width:1200px) { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
    @media(max-width:580px)  { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: 1fr; } }

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
        text-decoration: none !important;
    }
    .kpi-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
    }

    .kpi-icon-box {
        width: 38px; height: 38px; min-width: 38px; border-radius: 11px; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; font-size: 15px;
        transition: transform 0.2s ease;
    }
    .ik-blue   { background: rgba(59, 130, 246, 0.20); border: 1px solid rgba(59, 130, 246, 0.40); color: #60A5FA; }
    .ik-green  { background: rgba(16, 185, 129, 0.20); border: 1px solid rgba(16, 185, 129, 0.40); color: #34D399; }
    .ik-red    { background: rgba(239, 68, 68, 0.20); border: 1px solid rgba(239, 68, 68, 0.40); color: #F87171; }
    .ik-purple { background: rgba(139, 92, 246, 0.20); border: 1px solid rgba(139, 92, 246, 0.40); color: #A78BFA; }
    .ik-teal   { background: rgba(20, 184, 166, 0.20); border: 1px solid rgba(20, 184, 166, 0.40); color: #2DD4BF; }
    .ik-amber  { background: rgba(245, 158, 11, 0.20); border: 1px solid rgba(245, 158, 11, 0.40); color: #FBBF24; }
    .ik-orange { background: rgba(249, 115, 22, 0.20); border: 1px solid rgba(249, 115, 22, 0.40); color: #FB923C; }
    .ik-sky    { background: rgba(14, 165, 233, 0.20); border: 1px solid rgba(14, 165, 233, 0.40); color: #38BDF8; }
    .ik-indigo { background: rgba(99, 102, 241, 0.20); border: 1px solid rgba(99, 102, 241, 0.40); color: #818CF8; }

    .kpi-card:hover .kpi-icon-box { transform: scale(1.10); }
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

    /* --- Filtered Plots Table Card --- */
    .plots-table-card {
        background: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 22px !important;
        padding: 24px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        margin-bottom: 24px;
    }
    .plots-filter-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }
    .plot-pill {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.05);
        color: #E2E8F0;
        transition: all 0.2s ease;
    }
    .plot-pill:hover, .plot-pill.active {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.25);
        color: #FFFFFF;
    }
    .plot-pill.pill-avail.active { background: rgba(16, 185, 129, 0.25); border-color: #10B981; color: #34D399; }
    .plot-pill.pill-booked.active { background: rgba(139, 92, 246, 0.25); border-color: #8B5CF6; color: #A78BFA; }
    .plot-pill.pill-sold.active   { background: rgba(245, 158, 11, 0.25); border-color: #F59E0B; color: #FBBF24; }
    .plot-pill.pill-rented.active { background: rgba(14, 165, 233, 0.25); border-color: #0EA5E9; color: #38BDF8; }

    /* --- Tables --- */
    .erp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .erp-table th { padding: 11px 14px; background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8; font-weight: 800; border-bottom: 1px solid rgba(255, 255, 255, 0.10); font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .erp-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0; vertical-align: middle; }
    .erp-table td strong { color: #FFFFFF !important; }
    .erp-table tr:last-child td { border-bottom: none; }
    .erp-table tbody tr { transition: background 0.18s ease; }
    .erp-table tbody tr:hover { background: rgba(255, 255, 255, 0.06) !important; }
    .table-container { width: 100%; overflow-x: auto; background: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 16px; backdrop-filter: blur(8px); }

    .ds-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ds-badge.success { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
    .ds-badge.warning { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
    .ds-badge.danger  { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }
    .ds-badge.info    { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }

    /* --- Status Summaries --- */
    .summary-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; margin-top: 24px; }
    .summary-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; }
    .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    @media(max-width:1380px) { .summary-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; } }
    @media(max-width:768px)  { .summary-grid { grid-template-columns: 1fr; } }

    .summary-card {
        background: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px !important;
        padding: 22px 24px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .summary-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(15, 23, 42, 0.68) !important;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40) !important;
    }
    .summary-title { font-size: 14.5px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
    .chip-row { display: flex; flex-wrap: wrap; gap: 8px; }
    .chip {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap;
        text-decoration: none !important;
    }
    .ch-green  { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; }
    .ch-orange { background: rgba(249, 115, 22, 0.15) !important; border: 1px solid rgba(249, 115, 22, 0.30) !important; color: #FB923C !important; }
    .ch-sky    { background: rgba(14, 165, 233, 0.15) !important; border: 1px solid rgba(14, 165, 233, 0.30) !important; color: #38BDF8 !important; }
    .ch-blue   { background: rgba(59, 130, 246, 0.15) !important; border: 1px solid rgba(59, 130, 246, 0.30) !important; color: #60A5FA !important; }
    .ch-purple { background: rgba(139, 92, 246, 0.15) !important; border: 1px solid rgba(139, 92, 246, 0.30) !important; color: #A78BFA !important; }
    .ch-red    { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; color: #F87171 !important; }

    .summary-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 9px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        text-decoration: none !important;
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { font-size: 12.5px; color: #CBD5E1; font-weight: 600; display: flex; align-items: center; gap: 8px; }
    .summary-val { font-size: 13.5px; font-weight: 800; color: #FFFFFF; }
    .summary-val.g { color: #34D399 !important; }
    .summary-val.r { color: #F87171 !important; }
    .summary-val.o { color: #FB923C !important; }

    /* Bottom section grid */
    .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media(max-width:992px) { .dashboard-grid { grid-template-columns: 1fr; } }
    .section-card {
        background: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px !important;
        padding: 24px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        margin-bottom: 20px;
    }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
    .section-title { font-size: 14.5px; font-weight: 800; color: #FFFFFF !important; display: flex; align-items: center; gap: 9px; }
    .section-title-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
    .btn-view-all {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 5px 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        color: #FFFFFF !important;
        text-decoration: none !important;
    }
    .amt-strong { font-weight: 800; color: #FFFFFF; font-size: 13.5px; }
    .amt-green  { color: #34D399; }

    .status-summary-item { margin-bottom: 16px; text-decoration: none; display: block; }
    .status-summary-header { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; font-weight: 700; color: #FFFFFF; margin-bottom: 6px; }
    .status-pct { font-size: 11.5px; color: #94A3B8; font-weight: 600; }
    .progress-bg { height: 9px; background: rgba(255, 255, 255, 0.08); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; }

    .task-item { display: flex; gap: 12px; padding: 12px 14px; border-radius: 12px; margin-bottom: 10px; align-items: flex-start; border-left: 4px solid; background: rgba(255, 255, 255, 0.04) !important; border: 1px solid rgba(255, 255, 255, 0.10); }
    .task-item.warning { border-left-color: #F59E0B; }
    .task-item.success { border-left-color: #10B981; }
    .task-icon-wrap { width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .task-item.warning .task-icon-wrap { background: rgba(245,158,11,0.22); color: #FBBF24; }
    .task-item.success .task-icon-wrap { background: rgba(16,185,129,0.22); color: #34D399; }
    .task-content h5 { font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 2px; }
    .task-content p  { font-size: 11.5px; color: #94A3B8; line-height: 1.4; }
    </style>

    <!-- Welcome Header & Quick Actions -->
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
                @if(session('login_type') !== 'firm')
                <a href="{{ route('firm-master.create') }}" class="dqa-btn dqa-indigo"><i class="fa-solid fa-plus"></i> Add Firm</a>
                <a href="{{ route('financial-years.create') }}" class="dqa-btn dqa-slate"><i class="fa-solid fa-calendar-plus"></i> Add FY</a>
                <a href="{{ route('users.create') }}" class="dqa-btn dqa-sky"><i class="fa-solid fa-user-plus"></i> Add User</a>
                @endif
                <a href="{{ route('property-masters.create') }}" class="dqa-btn dqa-purple"><i class="fa-solid fa-building"></i> Add Property</a>
                <a href="{{ route('property-sales.create') }}" class="dqa-btn dqa-green"><i class="fa-solid fa-handshake"></i> Add Property Sell</a>
                <a href="{{ route('property-availability.index') }}" class="dqa-btn dqa-teal"><i class="fa-solid fa-circle-check"></i> Property Status</a>
                <a href="{{ route('property-documents.create') }}" class="dqa-btn dqa-sky"><i class="fa-solid fa-folder-plus"></i> Add Document</a>
                <a href="{{ route('projects.create') }}" class="dqa-btn dqa-teal"><i class="fa-solid fa-city"></i> Add Project</a>
                <a href="{{ route('contractors.create') }}" class="dqa-btn dqa-amber"><i class="fa-solid fa-helmet-safety"></i> Add Contractor</a>
                <a href="{{ route('vendors.create') }}" class="dqa-btn dqa-orange"><i class="fa-solid fa-truck-field"></i> Add Vendor</a>
                <a href="{{ route('sellers.create') }}" class="dqa-btn dqa-amber"><i class="fa-solid fa-user-tag"></i> Add Seller</a>
                <a href="{{ route('customers.create') }}" class="dqa-btn dqa-blue"><i class="fa-solid fa-users"></i> Add Customer</a>
                <a href="{{ route('tenants.create') }}" class="dqa-btn dqa-cyan"><i class="fa-solid fa-house-user"></i> Add Tenant</a>
                <a href="{{ route('brokers.create') }}" class="dqa-btn dqa-violet"><i class="fa-solid fa-user-tie"></i> Add Broker</a>
                <a href="{{ route('bookings.create') }}" class="dqa-btn dqa-emerald"><i class="fa-solid fa-calendar-check"></i> Add Booking</a>
                <a href="{{ route('payments.create') }}" class="dqa-btn dqa-rose"><i class="fa-solid fa-money-bill-wave"></i> Add Payment</a>
                <a href="{{ route('expenses.create') }}" class="dqa-btn dqa-red"><i class="fa-solid fa-receipt"></i> Add Expense</a>
                <a href="{{ route('expenses.property') }}" class="dqa-btn dqa-rose"><i class="fa-solid fa-file-invoice-dollar"></i> Property Expenses</a>
                <a href="{{ route('expenses.project-wise') }}" class="dqa-btn dqa-amber"><i class="fa-solid fa-city"></i> Project Expenses</a>
                <a href="{{ route('expenses.general') }}" class="dqa-btn dqa-slate"><i class="fa-solid fa-calculator"></i> General Expenses</a>
                <a href="{{ route('expenses.rental') }}" class="dqa-btn dqa-cyan"><i class="fa-solid fa-house-chimney-user"></i> Rental Expenses</a>
                <a href="{{ route('expenses.personal') }}" class="dqa-btn dqa-violet"><i class="fa-solid fa-user-lock"></i> Personal Expenses</a>
                <a href="{{ route('incomes.create') }}" class="dqa-btn dqa-green"><i class="fa-solid fa-arrow-trend-up"></i> Add Income</a>
                <a href="{{ route('materials.create') }}" class="dqa-btn dqa-bronze"><i class="fa-solid fa-box"></i> Add Material</a>
                <a href="{{ route('rentals.create') }}" class="dqa-btn dqa-fuchsia"><i class="fa-solid fa-key"></i> Add Rental</a>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════
         DASHBOARD PROPERTY & PROJECT FILTER BAR
    ════════════════════════════════════════════════════════════════ -->
    <div class="dash-filter-card">
        <form method="GET" action="{{ route('dashboard') }}" id="dashboardFilterForm">
            <input type="hidden" name="filter_type" id="filterTypeInput" value="{{ $filterType ?? 'all' }}">

            <div class="filter-header-row">
                <div class="filter-title">
                    <i class="fa-solid fa-sliders" style="color:#38BDF8;"></i>
                    Dashboard Live Filter &amp; Asset Intelligence
                </div>
                <div class="filter-tabs-wrap">
                    <button type="button" class="filter-tab-btn {{ $filterType === 'all' ? 'active-all' : '' }}" onclick="selectFilterMode('all')">
                        <i class="fa-solid fa-layer-group"></i> All Overview
                    </button>
                    <button type="button" class="filter-tab-btn {{ $filterType === 'property' ? 'active-prop' : '' }}" onclick="selectFilterMode('property')">
                        <i class="fa-solid fa-building"></i> Filter by Property / Land
                    </button>
                    <button type="button" class="filter-tab-btn {{ $filterType === 'project' ? 'active-proj' : '' }}" onclick="selectFilterMode('project')">
                        <i class="fa-solid fa-city"></i> Filter by Project
                    </button>
                </div>
            </div>

            <div class="filter-inputs-grid">
                <!-- Select Property Master -->
                <div class="filter-input-group">
                    <label><i class="fa-solid fa-building text-purple-400"></i> Select Property / Land</label>
                    <select name="property_master_id" id="propertyMasterSelect" class="dash-select-input" onchange="onPropertySelect(this.value)">
                        <option value="">-- All Properties / Select to Filter --</option>
                        @foreach($propertyMastersList as $pm)
                            <option value="{{ $pm->id }}" {{ (isset($selectedPropertyMaster) && $selectedPropertyMaster->id == $pm->id) ? 'selected' : '' }}>
                                {{ $pm->property_name }} {{ $pm->property_code ? "({$pm->property_code})" : '' }} • {{ $pm->plots_count }} Plots
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Project -->
                <div class="filter-input-group">
                    <label><i class="fa-solid fa-city text-teal-400"></i> Select Project</label>
                    <select name="project_id" id="projectSelect" class="dash-select-input" onchange="onProjectSelect(this.value)">
                        <option value="">-- All Projects / Select to Filter --</option>
                        @foreach($projectsList as $prj)
                            <option value="{{ $prj->id }}" {{ (isset($selectedProject) && $selectedProject->id == $prj->id) ? 'selected' : '' }}>
                                {{ $prj->project_name }} {{ $prj->project_code ? "({$prj->project_code})" : '' }} • {{ $prj->properties_count }} Units
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reset Button -->
                <div>
                    @if($filterType !== 'all' || !empty($selectedPropertyMaster) || !empty($selectedProject))
                        <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Reset all filters">
                            <i class="fa-solid fa-rotate-left"></i> Clear Filter
                        </a>
                    @else
                        <button type="submit" class="dqa-btn dqa-blue" style="height: 40px;">
                            <i class="fa-solid fa-magnifying-glass"></i> Apply Filter
                        </button>
                    @endif
                </div>
            </div>

            <!-- Active Filter Summary Pill -->
            @if($filterType === 'property' && $selectedPropertyMaster)
                <div class="filter-active-pill-box">
                    <div class="filter-pill-title">
                        <i class="fa-solid fa-circle-check" style="color:#A78BFA;"></i>
                        Filtered Property: <span style="color:#FFFFFF; text-decoration: underline;">{{ $selectedPropertyMaster->property_name }}</span>
                        @if($selectedPropertyMaster->property_code)
                            <span class="f-badge fb-purple">{{ $selectedPropertyMaster->property_code }}</span>
                        @endif
                    </div>
                    <div class="filter-pill-badges">
                        <span class="f-badge fb-purple"><i class="fa-solid fa-layer-group"></i> Total {{ $totalProperties }} Plots</span>
                        <span class="f-badge fb-amber"><i class="fa-solid fa-coins"></i> Lidhi Price: ₹{{ number_format($selectedPropertyMaster->purchase_price ?? 0, 2) }}</span>
                        @if($selectedPropertyMaster->city)
                            <span class="f-badge fb-blue"><i class="fa-solid fa-location-dot"></i> {{ $selectedPropertyMaster->city }}</span>
                        @endif
                        <a href="{{ route('property-masters.show', $selectedPropertyMaster->id) }}" class="btn-view-all" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Property Master
                        </a>
                    </div>
                </div>
            @elseif($filterType === 'project' && $selectedProject)
                <div class="filter-active-pill-box">
                    <div class="filter-pill-title">
                        <i class="fa-solid fa-circle-check" style="color:#2DD4BF;"></i>
                        Filtered Project: <span style="color:#FFFFFF; text-decoration: underline;">{{ $selectedProject->project_name }}</span>
                        @if($selectedProject->project_code)
                            <span class="f-badge fb-teal">{{ $selectedProject->project_code }}</span>
                        @endif
                    </div>
                    <div class="filter-pill-badges">
                        <span class="f-badge fb-teal"><i class="fa-solid fa-cubes"></i> Total {{ $totalProperties }} Units</span>
                        @if($selectedProject->city)
                            <span class="f-badge fb-blue"><i class="fa-solid fa-location-dot"></i> {{ $selectedProject->city }}</span>
                        @endif
                        <a href="{{ route('projects.show', $selectedProject->id) }}" class="btn-view-all" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Project
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </div>

    <script>
    function selectFilterMode(mode) {
        document.getElementById('filterTypeInput').value = mode;
        if (mode === 'all') {
            document.getElementById('propertyMasterSelect').value = '';
            document.getElementById('projectSelect').value = '';
            document.getElementById('dashboardFilterForm').submit();
        } else if (mode === 'property') {
            document.getElementById('projectSelect').value = '';
            var pSelect = document.getElementById('propertyMasterSelect');
            if (pSelect.value) {
                document.getElementById('dashboardFilterForm').submit();
            } else {
                pSelect.focus();
            }
        } else if (mode === 'project') {
            document.getElementById('propertyMasterSelect').value = '';
            var prjSelect = document.getElementById('projectSelect');
            if (prjSelect.value) {
                document.getElementById('dashboardFilterForm').submit();
            } else {
                prjSelect.focus();
            }
        }
    }

    function onPropertySelect(val) {
        if (val) {
            document.getElementById('filterTypeInput').value = 'property';
            document.getElementById('projectSelect').value = '';
            document.getElementById('dashboardFilterForm').submit();
        } else {
            selectFilterMode('all');
        }
    }

    function onProjectSelect(val) {
        if (val) {
            document.getElementById('filterTypeInput').value = 'project';
            document.getElementById('propertyMasterSelect').value = '';
            document.getElementById('dashboardFilterForm').submit();
        } else {
            selectFilterMode('all');
        }
    }
    </script>

    <!-- ════════════════════════════════════════════════════════════════
         GLOBAL CONTROL: FIRMS & USERS (Shown only on All Overview)
    ════════════════════════════════════════════════════════════════ -->
    @if($filterType === 'all')
    <div class="kpi-section-header">
        <div style="width:4px;height:18px;background:#2563EB;border-radius:3px;flex-shrink:0;"></div>
        <h3>Firms &amp; Users Control</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-4">
        <a href="{{ route('firm-master.index') }}" class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-building"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Firms</span>
                <span class="kpi-value">{{ number_format($totalFirms) }}</span>
                <span class="kpi-badge bk-blue">Registered Firms</span>
            </div>
        </a>
        <a href="{{ route('firm-master.index') }}?status=active" class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-toggle-on"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Active Firms</span>
                <span class="kpi-value">{{ number_format($activeFirms) }}</span>
                <span class="kpi-badge bk-blue">Operational</span>
            </div>
        </a>
        <a href="{{ route('firm-master.index') }}?status=inactive" class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-toggle-off"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Inactive Firms</span>
                <span class="kpi-value">{{ number_format($inactiveFirms) }}</span>
                <span class="kpi-badge bk-blue">Suspended</span>
            </div>
        </a>
        <a href="{{ route('users.index') }}" class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-user-gear"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Users</span>
                <span class="kpi-value">{{ number_format($totalUsers) }}</span>
                <span class="kpi-badge bk-blue">All Roles</span>
            </div>
        </a>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════
         PLOTS & INVENTORY KPI SECTION (Filtered or Global)
    ════════════════════════════════════════════════════════════════ -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:4px;height:18px;background:#EA580C;border-radius:3px;flex-shrink:0;"></div>
        <h3>
            @if($filterType === 'property' && $selectedPropertyMaster)
                {{ $selectedPropertyMaster->property_name }} — Plots &amp; Inventory Overview
            @elseif($filterType === 'project' && $selectedProject)
                {{ $selectedProject->project_name }} — Units &amp; Inventory Overview
            @else
                Project &amp; Property Units Overview
            @endif
        </h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-5">
        <!-- Total Plots -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-layer-group"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Plots / Units</span>
                <span class="kpi-value">{{ number_format($totalProperties) }}</span>
                <span class="kpi-badge bk-purple">All Units</span>
            </div>
        </div>

        <!-- Available Plots -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Available Plots</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($availableProperties) }}</span>
                <span class="kpi-badge bk-green">Ready to Sell</span>
            </div>
        </div>

        <!-- Booked Plots -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-indigo"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Booked Plots</span>
                <span class="kpi-value" style="color:#818CF8;">{{ number_format($bookedProperties) }}</span>
                <span class="kpi-badge bk-indigo">Under Booking</span>
            </div>
        </div>

        <!-- Sold Plots -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-house-circle-xmark"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Sold Plots</span>
                <span class="kpi-value" style="color:#F59E0B;">{{ number_format($soldProperties) }}</span>
                <span class="kpi-badge bk-amber">Closed Sales</span>
            </div>
        </div>

        <!-- Rented Plots -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-sky"><i class="fa-solid fa-key"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Rented Plots</span>
                <span class="kpi-value" style="color:#0EA5E9;">{{ number_format($rentedProperties) }}</span>
                <span class="kpi-badge bk-sky">Active Lease</span>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════
         PROPERTY SALES & PROFIT PERFORMANCE (Purchase Cost vs Sell Value = Profit)
    ════════════════════════════════════════════════════════════════ -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#3B82F6);border-radius:4px;flex-shrink:0;"></div>
        <h3>
            @if($filterType === 'property' && $selectedPropertyMaster)
                {{ $selectedPropertyMaster->property_name }} — Pricing, Cost &amp; Profit Analysis (Purchase Cost vs Selling Price = Profit)
            @elseif($filterType === 'project' && $selectedProject)
                {{ $selectedProject->project_name }} — Financials &amp; Profit Analysis
            @else
                Property Sales &amp; Profit Analysis (Lidhi Price - Sell Price = Profit)
            @endif
        </h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-4" style="margin-bottom: 24px;">
        <!-- Total Selling / Market Value -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-handshake"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Sold Value</span>
                <span class="kpi-value" style="color:#60A5FA;" title="₹{{ number_format($totalSalesRevenue, 2) }}">₹{{ number_format($totalSalesRevenue, 2) }}</span>
                <span class="kpi-badge bk-blue">{{ $totalSoldUnitsCount ?? 0 }} Units Sold</span>
            </div>
        </div>

        <!-- Purchase Cost (Lidhi Price) -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Purchase Cost</span>
                <span class="kpi-value" style="color:#FBBF24;" title="₹{{ number_format($totalSalesPurchaseCost, 2) }}">₹{{ number_format($totalSalesPurchaseCost, 2) }}</span>
                <span class="kpi-badge bk-amber">Acquisition Cost</span>
            </div>
        </div>

        <!-- Realized Profit -->
        <div class="kpi-card">
            <div class="kpi-icon-box {{ $totalSalesProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $totalSalesProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Realized Profit</span>
                <span class="kpi-value" style="color:{{ $totalSalesProfit >= 0 ? '#10B981' : '#EF4444' }};" title="₹{{ number_format($totalSalesProfit, 2) }}">₹{{ number_format($totalSalesProfit, 2) }}</span>
                <span class="kpi-badge {{ $totalSalesProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $totalSalesProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</span>
            </div>
        </div>

        <!-- Profit Margin -->
        <div class="kpi-card">
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-percent"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Sales Profit Margin</span>
                <span class="kpi-value" style="color:{{ $salesProfitMargin >= 0 ? '#A78BFA' : '#EF4444' }};" title="{{ $salesProfitMargin }}%">{{ $salesProfitMargin }}%</span>
                <span class="kpi-badge bk-purple">Margin on Sales</span>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════
         CASHFLOW & EXPENSES
    ════════════════════════════════════════════════════════════════ -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#14B8A6);border-radius:4px;flex-shrink:0;"></div>
        <h3>Cashflow, Expenses &amp; Outstanding Balances</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-4" style="margin-bottom: 24px;">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-money-bill-trend-up"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Revenue Received</span>
                <span class="kpi-value" style="color:#10B981;" title="₹{{ number_format($totalReceivedAmt, 0) }}">₹{{ number_format($totalReceivedAmt, 0) }}</span>
                <span class="kpi-badge bk-green">Total Received</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-red"><i class="fa-solid fa-receipt"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Expenses</span>
                <span class="kpi-value" style="color:#EF4444;" title="₹{{ number_format($totalExpenses, 0) }}">₹{{ number_format($totalExpenses, 0) }}</span>
                <span class="kpi-badge bk-red">All Outflows</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box {{ $netProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $netProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Net Cash Flow</span>
                <span class="kpi-value" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};" title="₹{{ number_format($netProfit, 0) }}">₹{{ number_format($netProfit, 0) }}</span>
                <span class="kpi-badge {{ $netProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $netProfit >= 0 ? 'Surplus' : 'Deficit' }}</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-orange"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Outstanding Due</span>
                <span class="kpi-value" style="color:#FB923C;" title="₹{{ number_format($totalPendingAmt, 0) }}">₹{{ number_format($totalPendingAmt, 0) }}</span>
                <span class="kpi-badge bk-orange">Pending Inflow</span>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════
         DETAILED PLOTS INVENTORY TABLE (When Filtered by Property or Project)
    ════════════════════════════════════════════════════════════════ -->
    @if(!empty($filteredPlots) && $filteredPlots->count() > 0)
    <div class="plots-table-card">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon ik-purple"><i class="fa-solid fa-table-cells"></i></div>
                @if($filterType === 'property' && $selectedPropertyMaster)
                    Plots Inventory &amp; Status Explorer: {{ $selectedPropertyMaster->property_name }}
                @elseif($filterType === 'project' && $selectedProject)
                    Units Inventory &amp; Status Explorer: {{ $selectedProject->project_name }}
                @endif
            </div>
            <div class="plots-filter-pills">
                <span class="plot-pill active" onclick="filterPlotRows('all', this)">All ({{ $totalProperties }})</span>
                <span class="plot-pill pill-avail" onclick="filterPlotRows('available', this)">🟢 Available ({{ $availableProperties }})</span>
                <span class="plot-pill pill-booked" onclick="filterPlotRows('booked', this)">🟣 Booked ({{ $bookedProperties }})</span>
                <span class="plot-pill pill-sold" onclick="filterPlotRows('sold', this)">🟡 Sold ({{ $soldProperties }})</span>
                <span class="plot-pill pill-rented" onclick="filterPlotRows('rented', this)">🔵 Rented ({{ $rentedProperties }})</span>
            </div>
        </div>

        <div class="table-container">
            <table class="erp-table" id="plotsDetailedTable">
                <thead>
                    <tr>
                        <th>Plot / Unit No</th>
                        <th>Name / Code</th>
                        <th>Size / Area</th>
                        <th>Status</th>
                        <th>Listed Price</th>
                        <th>Purchase Cost (Lidhi)</th>
                        <th>Expenses</th>
                        <th>Profit / Est. Gain</th>
                        <th>Buyer / Tenant</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredPlots as $plot)
                        @php
                            $pCost = (float)($plot->effective_purchase_cost ?? 0);
                            $pPrice = (float)($plot->price ?? 0);
                            $pExp = (float)($plot->total_expenses ?? 0);
                            $pProfit = $pPrice > 0 ? ($pPrice - $pCost - $pExp) : 0;
                            $activeB = $plot->active_booking;
                            $activeR = $plot->active_rental;
                            $activeS = $plot->sales()->where('sale_status', '!=', 'cancelled')->latest()->first();
                        @endphp
                        <tr class="plot-row" data-status="{{ $plot->status }}">
                            <td>
                                <span style="font-weight: 800; color: #FFFFFF; font-size: 13.5px;">
                                    {{ $plot->unit_no ? "Unit {$plot->unit_no}" : ($plot->property_name ?: "Plot #{$plot->id}") }}
                                </span>
                            </td>
                            <td>
                                <span style="color: #94A3B8; font-size: 12px;">{{ $plot->property_code ?: '-' }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $plot->formatted_size }}</span>
                            </td>
                            <td>
                                @if($plot->status === 'available')
                                    <span class="ds-badge success">Available</span>
                                @elseif($plot->status === 'booked')
                                    <span class="ds-badge info">Booked</span>
                                @elseif($plot->status === 'sold')
                                    <span class="ds-badge warning">Sold</span>
                                @elseif($plot->status === 'rented')
                                    <span class="ds-badge info" style="color:#38BDF8; border-color: rgba(14,165,233,0.35);">Rented</span>
                                @else
                                    <span class="ds-badge">{{ ucfirst($plot->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color: #60A5FA;">₹{{ number_format($pPrice, 2) }}</strong>
                            </td>
                            <td>
                                <span style="color: #FBBF24;">₹{{ number_format($pCost, 2) }}</span>
                            </td>
                            <td>
                                <span style="color: #F87171;">₹{{ number_format($pExp, 2) }}</span>
                            </td>
                            <td>
                                <strong style="color: {{ $pProfit >= 0 ? '#34D399' : '#F87171' }};">
                                    ₹{{ number_format($pProfit, 2) }}
                                </strong>
                            </td>
                            <td>
                                @if($activeS && $activeS->customer)
                                    <span style="color: #FBBF24; font-weight: 700;"><i class="fa-solid fa-user-check"></i> {{ $activeS->customer->name }}</span>
                                @elseif($activeB && $activeB->customer)
                                    <span style="color: #A78BFA; font-weight: 700;"><i class="fa-solid fa-user-clock"></i> {{ $activeB->customer->name }}</span>
                                @elseif($activeR && $activeR->tenant)
                                    <span style="color: #38BDF8; font-weight: 700;"><i class="fa-solid fa-house-user"></i> {{ $activeR->tenant->name }}</span>
                                @else
                                    <span style="color: #64748B;">—</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                @if($plot->status === 'available')
                                    <a href="{{ route('property-sales.create') }}?property_id={{ $plot->id }}" class="dqa-btn dqa-green" style="padding: 4px 10px !important; font-size: 11px !important;">
                                        <i class="fa-solid fa-handshake"></i> Sell
                                    </a>
                                    <a href="{{ route('bookings.create') }}?property_id={{ $plot->id }}" class="dqa-btn dqa-emerald" style="padding: 4px 10px !important; font-size: 11px !important;">
                                        <i class="fa-solid fa-calendar-plus"></i> Book
                                    </a>
                                @endif
                                <a href="{{ route('properties.show', $plot->id) }}" class="dqa-btn dqa-sky" style="padding: 4px 10px !important; font-size: 11px !important;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function filterPlotRows(status, el) {
        document.querySelectorAll('.plot-pill').forEach(function(pill) {
            pill.classList.remove('active');
        });
        el.classList.add('active');

        var rows = document.querySelectorAll('#plotsDetailedTable tbody tr.plot-row');
        rows.forEach(function(row) {
            if (status === 'all' || row.getAttribute('data-status') === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>
    @endif

    <!-- ════════════════════════════════════════════════════════════════
         STATUS SUMMARIES SECTION
    ════════════════════════════════════════════════════════════════ -->
    <div class="summary-section-header">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#F97316,#EA580C);border-radius:4px;flex-shrink:0;"></div>
        <h3>Performance &amp; Status Summaries</h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="summary-grid">
        <!-- Project Status Summary -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-city" style="color:#10B981;"></i> Plots Inventory Status
            </div>
            <div class="chip-row">
                <span class="chip ch-green"><span class="chip-num">{{ $availableProperties }}</span> Available</span>
                <span class="chip ch-purple"><span class="chip-num">{{ $bookedProperties }}</span> Booked</span>
                <span class="chip ch-orange"><span class="chip-num">{{ $soldProperties }}</span> Sold</span>
                <span class="chip ch-sky"><span class="chip-num">{{ $rentedProperties }}</span> Rented</span>
                <span class="chip ch-blue"><span class="chip-num">{{ $totalProperties }}</span> Total Units</span>
            </div>
        </div>

        <!-- Firms & Users Summary (or Acquisition Summary) -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-layer-group" style="color:#3B82F6;"></i>
                @if($filterType === 'property' && $selectedPropertyMaster)
                    Acquisition &amp; Location
                @else
                    Firms &amp; Users Control
                @endif
            </div>
            @if($filterType === 'property' && $selectedPropertyMaster)
                <div class="summary-row">
                    <span class="summary-label"><i class="fa-solid fa-ruler-combined"></i> Total Area</span>
                    <span class="summary-val">{{ $selectedPropertyMaster->total_area ?: '-' }} {{ $selectedPropertyMaster->area_unit }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><i class="fa-solid fa-user-tag"></i> Seller</span>
                    <span class="summary-val">{{ $selectedPropertyMaster->seller->name ?? $selectedPropertyMaster->seller_name ?? 'Direct' }}</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label"><i class="fa-solid fa-user-tie"></i> Broker</span>
                    <span class="summary-val">{{ $selectedPropertyMaster->broker->name ?? $selectedPropertyMaster->broker_name ?? 'None' }}</span>
                </div>
            @else
                <div class="chip-row">
                    <a href="{{ route('firm-master.index') }}?status=active" class="chip ch-green">
                        <span class="chip-num">{{ $activeFirms }}</span> Active Firms
                    </a>
                    <a href="{{ route('firm-master.index') }}?status=inactive" class="chip ch-red">
                        <span class="chip-num">{{ $inactiveFirms }}</span> Inactive
                    </a>
                    <a href="{{ route('users.index') }}?status=active" class="chip ch-blue">
                        <span class="chip-num">{{ $activeUsers }}</span> Active Users
                    </a>
                    <a href="{{ route('users.index') }}" class="chip ch-purple">
                        <span class="chip-num">{{ $totalUsers }}</span> Total Users
                    </a>
                </div>
            @endif
        </div>

        <!-- Property Sales & Profit Breakdown -->
        <div class="summary-card">
            <div class="summary-title">
                <i class="fa-solid fa-chart-pie" style="color:#10B981;"></i> Sales Profit Breakdown
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-handshake" style="color:#60A5FA;"></i> Total Selling Value</span>
                <span class="summary-val" style="color:#60A5FA;">₹{{ number_format($totalSalesRevenue, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-cart-shopping" style="color:#FBBF24;"></i> Total Purchase Cost</span>
                <span class="summary-val" style="color:#FBBF24;">₹{{ number_format($totalSalesPurchaseCost, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-arrow-trend-up" style="color:{{ $totalSalesProfit >= 0 ? '#34D399' : '#F87171' }};"></i> Net Sales Profit</span>
                <span class="summary-val {{ $totalSalesProfit >= 0 ? 'g' : 'r' }}">₹{{ number_format($totalSalesProfit, 0) }}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label"><i class="fa-solid fa-percent" style="color:#A78BFA;"></i> Profit Margin</span>
                <span class="summary-val" style="color:#A78BFA;">{{ $salesProfitMargin }}%</span>
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
                <span class="summary-label"><i class="fa-solid fa-chart-line" style="color:{{ $netProfit >= 0 ? '#10B981' : '#EF4444' }};"></i> Net Cash Flow</span>
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
        $bookedPct = round(($bookedProperties    / $propTotal) * 100);
        $soldPct   = round(($soldProperties      / $propTotal) * 100);
        $rentedPct = round(($rentedProperties    / $propTotal) * 100);
    @endphp

    <!-- ════════════════════════════════════════════════════════════════
         BOTTOM GRID: RECENT CUSTOMERS & PAYMENTS & PROGRESS
    ════════════════════════════════════════════════════════════════ -->
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
                        Recent Payments {{ $filterType !== 'all' ? '(Filtered)' : '' }}
                    </div>
                    <a href="{{ route('payments.index') }}" class="btn-view-all">View All <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                <div class="table-container">
                    <table class="erp-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Property / Plot</th>
                                <th>Amount</th>
                                <th>Mode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                                    <td><strong>{{ $payment->customer->name ?? '-' }}</strong></td>
                                    <td>{{ $payment->property->name ?? $payment->property->property_name ?? '-' }}</td>
                                    <td class="amt-strong amt-green">₹{{ number_format($payment->payment_amount, 2) }}</td>
                                    <td><span class="ds-badge info">{{ $payment->payment_mode ?? 'Direct' }}</span></td>
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
            <!-- Plots Status Progress -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-title">
                        <div class="section-title-icon" style="background:rgba(245,158,11,0.1);"><i class="fa-solid fa-chart-pie" style="color:#F59E0B;"></i></div>
                        Plots Status Ratio
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
                    <div class="status-summary-item" style="margin-top: 14px;">
                        <div class="status-summary-header">
                            <span>🟣 Booked</span>
                            <span class="status-pct">{{ $bookedProperties }} units ({{ $bookedPct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width:{{ $bookedPct }}%; background: #8B5CF6;"></div>
                        </div>
                    </div>
                    <div class="status-summary-item" style="margin-top: 14px;">
                        <div class="status-summary-header">
                            <span>🟡 Sold</span>
                            <span class="status-pct">{{ $soldProperties }} units ({{ $soldPct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width:{{ $soldPct }}%; background: #F59E0B;"></div>
                        </div>
                    </div>
                    <div class="status-summary-item" style="margin-top: 14px;">
                        <div class="status-summary-header">
                            <span>🔵 Rented</span>
                            <span class="status-pct">{{ $rentedProperties }} units ({{ $rentedPct }}%)</span>
                        </div>
                        <div class="progress-bg">
                            <div class="progress-fill" style="width:{{ $rentedPct }}%; background: #0EA5E9;"></div>
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
                                <h5>Pending Balance</h5>
                                <p>₹{{ number_format($totalPendingAmt, 0) }} outstanding payments.</p>
                            </div>
                        </div>
                    @else
                        <div class="task-item success">
                            <div class="task-icon-wrap"><i class="fa-solid fa-check"></i></div>
                            <div class="task-content">
                                <h5>All Clear</h5>
                                <p>No pending alerts for this selection.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
