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
        padding: 28px 34px; margin-bottom: 22px; overflow: hidden;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .dash-welcome:hover {
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        background: rgba(20, 27, 41, 0.80) !important;
    }
    .dash-welcome-inner { position: relative; z-index: 2; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
    .dash-welcome-tag {
        display: inline-flex; align-items: center; gap: 7px;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        font-size: 11px; font-weight: 800;
        letter-spacing: 1.2px; text-transform: uppercase; padding: 5px 12px; border-radius: 20px;
        margin-bottom: 9px; backdrop-filter: blur(8px);
    }
    .dash-welcome-title { font-size: 26px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 6px; }
    .dash-welcome-sub { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; line-height: 1.4; }
    .dash-quick-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .dqa-btn {
        display: inline-flex !important; align-items: center !important; gap: 7px !important;
        background: rgba(255, 255, 255, 0.08) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        padding: 7px 14px !important;
        border-radius: 10px !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
    }
    .dqa-btn i { font-size: 12px; color: #FFFFFF !important; transition: transform 0.2s ease !important; opacity: 1 !important; }
    .dqa-btn:hover { transform: translateY(-2px) scale(1.02) !important; color: #FFFFFF !important; }
    .dqa-btn:hover i { color: #FFFFFF !important; transform: scale(1.15) !important; }

    .dqa-indigo:hover  { background: #4F46E5 !important; border-color: #6366F1 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(79, 70, 229, 0.5) !important; }
    .dqa-slate:hover   { background: #475569 !important; border-color: #64748B !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(71, 85, 105, 0.5) !important; }
    .dqa-sky:hover     { background: #0284C7 !important; border-color: #38BDF8 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(2, 132, 199, 0.5) !important; }
    .dqa-purple:hover  { background: #9333EA !important; border-color: #A855F7 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(147, 51, 234, 0.5) !important; }
    .dqa-teal:hover    { background: #0D9488 !important; border-color: #14B8A6 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(13, 148, 136, 0.5) !important; }
    .dqa-amber:hover   { background: #D97706 !important; border-color: #F59E0B !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(217, 119, 6, 0.5) !important; }
    .dqa-blue:hover    { background: #2563EB !important; border-color: #3B82F6 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(37, 99, 235, 0.5) !important; }
    .dqa-violet:hover  { background: #7C3AED !important; border-color: #8B5CF6 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(124, 58, 237, 0.5) !important; }
    .dqa-emerald:hover { background: #059669 !important; border-color: #10B981 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(5, 150, 105, 0.5) !important; }
    .dqa-rose:hover    { background: #E11D48 !important; border-color: #F43F5E !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(225, 29, 72, 0.5) !important; }
    .dqa-red:hover     { background: #DC2626 !important; border-color: #EF4444 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(220, 38, 38, 0.5) !important; }
    .dqa-green:hover   { background: #16A34A !important; border-color: #22C55E !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(22, 163, 74, 0.5) !important; }
    .dqa-bronze:hover  { background: #854D0E !important; border-color: #A16207 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(133, 77, 14, 0.5) !important; }
    .dqa-fuchsia:hover { background: #C026D3 !important; border-color: #D946EF !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(192, 38, 211, 0.5) !important; }
    .dqa-orange:hover  { background: #EA580C !important; border-color: #FB923C !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(234, 88, 12, 0.5) !important; }
    .dqa-cyan:hover    { background: #0891B2 !important; border-color: #06B6D4 !important; color: #FFFFFF !important; box-shadow: 0 6px 22px rgba(8, 145, 178, 0.5) !important; }

    /* --- Reports Dropdown in Filter Bar --- */
    .dash-reports-dropdown {
        position: relative;
        display: inline-flex;
    }
    .filter-reports-btn {
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
        background: linear-gradient(135deg, #4338CA 0%, #6366F1 50%, #7C3AED 100%) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(199, 210, 254, 0.40) !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.40) !important;
    }
    .filter-reports-btn:hover {
        background: linear-gradient(135deg, #3730A3 0%, #4F46E5 50%, #6D28D9 100%) !important;
        border-color: rgba(255, 255, 255, 0.50) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.60) !important;
        transform: translateY(-1px);
    }
    .dropdown-caret {
        font-size: 10px !important;
        margin-left: 2px;
        transition: transform 0.25s ease !important;
    }
    .reports-dropdown-menu {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        left: auto;
        width: 760px;
        max-width: 92vw;
        background: rgba(13, 19, 33, 0.98) !important;
        backdrop-filter: blur(28px) saturate(190%) !important;
        -webkit-backdrop-filter: blur(28px) saturate(190%) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-radius: 20px !important;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.75), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        padding: 18px 20px 20px !important;
        z-index: 1200;
        display: none;
        opacity: 0;
        transform: translateY(-8px);
        transition: opacity 0.22s ease, transform 0.22s ease;
    }
    .reports-dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
    .reports-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        flex-wrap: wrap;
    }
    .rd-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        gap: 8px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .rd-all-link {
        font-size: 12px;
        font-weight: 700;
        color: #60A5FA;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.30);
        transition: all 0.2s ease;
    }
    .rd-all-link:hover {
        background: #3B82F6;
        color: #FFFFFF;
        transform: translateX(2px);
    }
    .rd-search-box {
        margin-bottom: 14px;
        position: relative;
    }
    .rd-search-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 10px;
        padding: 8px 12px 8px 34px;
        font-size: 12.5px;
        color: #FFFFFF;
        outline: none;
        transition: border-color 0.2s ease;
    }
    .rd-search-input:focus {
        border-color: #6366F1;
        background: rgba(255, 255, 255, 0.09);
    }
    .rd-search-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #94A3B8;
    }
    .reports-dropdown-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        max-height: 420px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .rd-category {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .rd-cat-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94A3B8;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-bottom: 4px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }
    .rd-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 10px;
        border-radius: 10px;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: all 0.2s ease;
    }
    .rd-item:hover {
        background: rgba(255, 255, 255, 0.09);
        border-color: rgba(255, 255, 255, 0.20);
        transform: translateX(3px);
    }
    .rd-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        flex-shrink: 0;
    }
    .rd-icon.ic-blue   { background: rgba(59, 130, 246, 0.20); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
    .rd-icon.ic-sky    { background: rgba(14, 165, 233, 0.20); color: #38BDF8; border: 1px solid rgba(14, 165, 233, 0.35); }
    .rd-icon.ic-green  { background: rgba(16, 185, 129, 0.20); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
    .rd-icon.ic-purple { background: rgba(139, 92, 246, 0.20); color: #C084FC; border: 1px solid rgba(139, 92, 246, 0.35); }
    .rd-icon.ic-teal   { background: rgba(20, 184, 166, 0.20); color: #2DD4BF; border: 1px solid rgba(20, 184, 166, 0.35); }
    .rd-icon.ic-amber  { background: rgba(245, 158, 11, 0.20); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
    .rd-icon.ic-rose   { background: rgba(244, 63, 94, 0.20); color: #FB7185; border: 1px solid rgba(244, 63, 94, 0.35); }
    .rd-icon.ic-gold   { background: rgba(212, 175, 55, 0.20); color: #F7D774; border: 1px solid rgba(212, 175, 55, 0.35); }
    .rd-icon.ic-cyan   { background: rgba(6, 182, 212, 0.20); color: #22D3EE; border: 1px solid rgba(6, 182, 212, 0.35); }

    .rd-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .rd-name {
        font-size: 12px;
        font-weight: 700;
        color: #FFFFFF;
        line-height: 1.2;
    }
    .rd-desc {
        font-size: 10px;
        color: #94A3B8;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    @media (max-width: 768px) {
        .reports-dropdown-menu {
            left: auto !important;
            right: 0 !important;
            width: min(100vw - 24px, 360px) !important;
            max-height: 75vh !important;
            overflow-y: auto !important;
            padding: 14px !important;
        }
        .reports-dropdown-grid {
            grid-template-columns: 1fr !important;
            max-height: none !important;
            gap: 12px !important;
        }
    }

    /* --- FILTER SECTION --- */
    .dash-filter-card {
        background: rgba(20, 27, 41, 0.70) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
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
    .filter-tab-btn.active-sale {
        background: linear-gradient(135deg, #059669, #047857) !important;
        border-color: #34D399 !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.4);
    }
    .filter-tab-btn.active-purchase {
        background: linear-gradient(135deg, #D97706, #B45309) !important;
        border-color: #FBBF24 !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
    }
    .filter-tab-btn.active-expense {
        background: linear-gradient(135deg, #DC2626, #B91C1C) !important;
        border-color: #F87171 !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(220, 38, 38, 0.4);
    }

    /* Filter Mode Panels & Grid */
    .filter-mode-panel {
        display: none;
        animation: fadeInPanel 0.22s ease forwards;
    }
    .filter-mode-panel.active-panel {
        display: block;
    }
    @keyframes fadeInPanel {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .panel-content-row {
        display: flex;
        align-items: flex-end;
        gap: 14px;
        flex-wrap: wrap;
    }
    .panel-input-flex {
        flex: 1;
        min-width: 280px;
    }
    .panel-btn-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }
    .filter-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
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
        background: rgba(16, 22, 34, 0.90) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 12px !important;
        padding: 9px 14px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        outline: none !important;
        cursor: pointer;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
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
    }

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
    .filter-pill-title { font-size: 13.5px; font-weight: 800; color: #FFFFFF; display: flex; align-items: center; gap: 8px; }
    .filter-pill-badges { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .f-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; }
    .fb-purple { background: rgba(139, 92, 246, 0.2); border: 1px solid rgba(139, 92, 246, 0.4); color: #C4B5FD; }
    .fb-teal   { background: rgba(20, 184, 166, 0.2); border: 1px solid rgba(20, 184, 166, 0.4); color: #5EEAD4; }
    .fb-blue   { background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4); color: #93C5FD; }
    .fb-amber  { background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #FDE68A; }

    /* --- KPI Section Header --- */
    .kpi-section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .kpi-section-header h3 { font-size: 13px; font-weight: 800; color: #FFFFFF !important; text-transform: uppercase; letter-spacing: 1.4px; margin: 0; }
    .kpi-section-divider { flex: 1; height: 1px; background: rgba(255, 255, 255, 0.12) !important; }

    /* --- KPI Grid --- */
    .kpi-grid-4, .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
    .kpi-grid-5 { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; margin-bottom: 20px; }
    @media(max-width:1440px) { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: repeat(4, 1fr); } }
    @media(max-width:1200px) { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: repeat(2, 1fr); gap: 12px; } }
    @media(max-width:580px)  { .kpi-grid-4, .kpi-grid, .kpi-grid-5 { grid-template-columns: 1fr; } }

    /* --- KPI Cards --- */
    .kpi-card {
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 18px !important;
        padding: 14px 14px; position: relative; overflow: hidden;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        transition: transform 0.22s cubic-bezier(0.4,0,0.2,1), box-shadow 0.22s cubic-bezier(0.4,0,0.2,1);
        display: flex; align-items: center; gap: 10px; min-width: 0;
        text-decoration: none !important;
    }
    .kpi-card:hover {
        transform: translateY(-3px) !important;
        background: rgba(20, 27, 41, 0.80) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.45) !important;
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
    .kpi-label { font-size: 10.5px; font-weight: 800; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 3px; line-height: 1.2; }
    .kpi-value { font-size: clamp(14px, 1.1vw, 17px) !important; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-bottom: 2px; }
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
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 20px !important;
        padding: 24px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        margin-bottom: 24px;
    }
    .plots-filter-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .plot-pill {
        padding: 5px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.12); background: rgba(255, 255, 255, 0.05); color: #E2E8F0; transition: all 0.2s ease;
    }
    .plot-pill:hover, .plot-pill.active { background: rgba(255, 255, 255, 0.15); border-color: rgba(255, 255, 255, 0.25); color: #FFFFFF; }
    .plot-pill.pill-avail.active { background: rgba(16, 185, 129, 0.25); border-color: #10B981; color: #34D399; }
    .plot-pill.pill-booked.active { background: rgba(139, 92, 246, 0.25); border-color: #8B5CF6; color: #A78BFA; }
    .plot-pill.pill-sold.active   { background: rgba(245, 158, 11, 0.25); border-color: #F59E0B; color: #FBBF24; }
    .plot-pill.pill-rented.active { background: rgba(14, 165, 233, 0.25); border-color: #0EA5E9; color: #38BDF8; }

    /* --- Tables --- */
    .erp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .erp-table th { padding: 11px 14px; background: rgba(255, 255, 255, 0.05); color: #94A3B8; font-weight: 800; border-bottom: 1px solid rgba(255, 255, 255, 0.10); font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; }
    .erp-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0; vertical-align: middle; }
    .erp-table td strong { color: #FFFFFF !important; }
    .erp-table tr:last-child td { border-bottom: none; }
    .erp-table tbody tr { transition: background 0.15s ease; }
    .erp-table tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
    .table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 18px; }

    .ds-badge { display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.3px; }
    .ds-badge.success { background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30); }
    .ds-badge.warning { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.30); }
    .ds-badge.danger  { background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.30); }
    .ds-badge.info    { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.30); }

    .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media(max-width:992px) { .dashboard-grid { grid-template-columns: 1fr; } }
    .section-card {
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
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

    .status-summary-item { margin-bottom: 16px; text-decoration: none; display: block; }
    .status-summary-header { display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; font-weight: 700; color: #FFFFFF; margin-bottom: 6px; }
    .status-pct { font-size: 11.5px; color: #94A3B8; font-weight: 600; }
    .progress-bg { height: 9px; background: rgba(255, 255, 255, 0.10); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 10px; }

    .task-item { display: flex; gap: 12px; padding: 12px 14px; border-radius: 12px; margin-bottom: 10px; align-items: flex-start; border-left: 4px solid; background: rgba(20, 27, 41, 0.65) !important; border: 1px solid rgba(255, 255, 255, 0.10); }
    .task-item.warning { border-left-color: #F59E0B; }
    .task-item.success { border-left-color: #10B981; }
    .task-icon-wrap { width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 13px; }
    .task-icon-wrap.warning { background: rgba(245,158,11,0.2); color: #FBBF24; }
    .task-icon-wrap.success { background: rgba(16,185,129,0.2); color: #34D399; }
    .task-content h5 { font-size: 13px; font-weight: 700; color: #FFFFFF; margin-bottom: 2px; }
    .task-content p  { font-size: 11.5px; color: #94A3B8; line-height: 1.4; }

    @media (max-width: 768px) {
        .dash-welcome {
            padding: 16px 14px !important;
            margin-bottom: 16px !important;
            border-radius: 16px !important;
        }
        .dash-welcome-title {
            font-size: 18px !important;
        }
        .dash-welcome-sub {
            font-size: 12px !important;
        }
        .dash-quick-actions {
            gap: 6px !important;
        }
        .dqa-btn {
            padding: 6px 10px !important;
            font-size: 11.5px !important;
            height: 34px !important;
        }
        .dash-filter-card {
            padding: 14px 12px !important;
            border-radius: 16px !important;
            margin-bottom: 16px !important;
        }
        .filter-title {
            font-size: 12px !important;
        }
        .filter-tabs-wrap {
            gap: 6px !important;
            width: 100% !important;
        }
        .filter-tab-btn {
            padding: 6px 10px !important;
            font-size: 11px !important;
            flex: 1 1 calc(50% - 6px);
            justify-content: center;
        }
        .panel-content-row {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .panel-input-flex {
            min-width: 100% !important;
        }
        .panel-btn-group {
            width: 100% !important;
            display: flex !important;
            gap: 8px !important;
        }
        .panel-btn-group .dqa-btn, .panel-btn-group .btn-filter-reset {
            flex: 1 !important;
            justify-content: center !important;
        }
    }
    </style>

    <!-- Welcome Header & Quick Actions -->
    <div class="dash-welcome">
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
         FIRM LIVE FILTER & ASSET INTELLIGENCE BAR
    ════════════════════════════════════════════════════════════════ -->
    <div class="dash-filter-card">
        <form method="GET" action="{{ route('dashboard') }}" id="dashboardFilterForm">
            <input type="hidden" name="filter_type" id="filterTypeInput" value="{{ $filterType ?? 'all' }}">

            <!-- Top Filter Tabs Row -->
            <div class="filter-header-row">
                <div class="filter-title">
                    <i class="fa-solid fa-sliders" style="color:#38BDF8;"></i>
                    Firm Live Filter &amp; Asset Intelligence
                </div>
                <div class="filter-tabs-wrap">
                    <button type="button" id="tab-btn-all" class="filter-tab-btn {{ $filterType === 'all' ? 'active-all' : '' }}" onclick="switchFilterTab('all')">
                        <i class="fa-solid fa-layer-group"></i> All Overview
                    </button>
                    <button type="button" id="tab-btn-property" class="filter-tab-btn {{ $filterType === 'property' ? 'active-prop' : '' }}" onclick="switchFilterTab('property')">
                        <i class="fa-solid fa-building"></i> Filter by Property
                    </button>
                    <button type="button" id="tab-btn-project" class="filter-tab-btn {{ $filterType === 'project' ? 'active-proj' : '' }}" onclick="switchFilterTab('project')">
                        <i class="fa-solid fa-city"></i> Filter by Project
                    </button>
                    <button type="button" id="tab-btn-sale" class="filter-tab-btn {{ in_array($filterType, ['sale']) ? 'active-sale' : '' }}" onclick="switchFilterTab('sale')">
                        <i class="fa-solid fa-handshake"></i> Filter by Sale
                    </button>
                    <button type="button" id="tab-btn-purchase" class="filter-tab-btn {{ in_array($filterType, ['purchase']) ? 'active-purchase' : '' }}" onclick="switchFilterTab('purchase')">
                        <i class="fa-solid fa-cart-shopping"></i> Filter by Purchase
                    </button>
                    <button type="button" id="tab-btn-expense" class="filter-tab-btn {{ in_array($filterType, ['expense']) ? 'active-expense' : '' }}" onclick="switchFilterTab('expense')">
                        <i class="fa-solid fa-receipt"></i> Filter by Expense
                    </button>

                    <!-- Reports & Analytics Centre Direct Link -->
                    <a href="{{ route('reports.index') }}" class="filter-reports-btn" title="Open Reports Centre">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Reports &amp; Statements</span>
                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px; margin-left: 3px; opacity: 0.85;"></i>
                    </a>
                </div>
            </div>

            <!-- Mode 1: All Overview Panel -->
            <div id="panel-all" class="filter-mode-panel {{ $filterType === 'all' ? 'active-panel' : '' }}">
                <div class="panel-content-row" style="justify-content: space-between; align-items: center;">
                    <div style="display: flex; align-items: center; gap: 12px; color: #CBD5E1; font-size: 13px;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(37,99,235,0.2); border: 1px solid rgba(37,99,235,0.4); display: flex; align-items: center; justify-content: center; color: #60A5FA; font-size: 16px; flex-shrink: 0;">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <div>
                            <strong style="color: #FFFFFF; font-size: 13.5px;">Firm Overview Mode Active</strong>
                            <div style="color: #94A3B8; font-size: 12px;">Displaying firm metrics across all properties, projects, sales, purchases, and expenses.</div>
                        </div>
                    </div>
                    <div class="panel-btn-group">
                        <a href="{{ route('dashboard') }}" class="dqa-btn dqa-blue" style="height: 38px;">
                            <i class="fa-solid fa-rotate"></i> Refresh Overview
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mode 2: Property Filter Panel -->
            <div id="panel-property" class="filter-mode-panel {{ $filterType === 'property' ? 'active-panel' : '' }}">
                <div class="panel-content-row">
                    <div class="panel-input-flex">
                        <label class="filter-label"><i class="fa-solid fa-building text-purple-400"></i> Select Property / Land</label>
                        <select name="property_master_id" id="propertyMasterSelect" class="dash-select-input" onchange="submitModeFilter('property')">
                            <option value="">-- Choose Property / Land to Filter Plots &amp; Financials --</option>
                            @foreach($propertyMastersList as $pm)
                                <option value="{{ $pm->id }}" {{ (isset($selectedPropertyMaster) && $selectedPropertyMaster->id == $pm->id && $filterType === 'property') ? 'selected' : '' }}>
                                    {{ $pm->property_name }} {{ $pm->property_code ? "({$pm->property_code})" : '' }} • {{ $pm->plots_count }} Plots
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-btn-group">
                        <button type="button" class="dqa-btn dqa-purple" style="height: 40px;" onclick="submitModeFilter('property')">
                            <i class="fa-solid fa-magnifying-glass"></i> Apply Property Filter
                        </button>
                        @if($filterType === 'property' && !empty($selectedPropertyMaster))
                            <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Clear Filter">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mode 3: Project Filter Panel -->
            <div id="panel-project" class="filter-mode-panel {{ $filterType === 'project' ? 'active-panel' : '' }}">
                <div class="panel-content-row">
                    <div class="panel-input-flex">
                        <label class="filter-label"><i class="fa-solid fa-city text-teal-400"></i> Select Project</label>
                        <select name="project_id" id="projectSelect" class="dash-select-input" onchange="submitModeFilter('project')">
                            <option value="">-- Choose Project to Filter Units &amp; Financials --</option>
                            @foreach($projectsList as $prj)
                                <option value="{{ $prj->id }}" {{ (isset($selectedProject) && $selectedProject->id == $prj->id && $filterType === 'project') ? 'selected' : '' }}>
                                    {{ $prj->project_name }} {{ $prj->project_code ? "({$prj->project_code})" : '' }} • {{ $prj->properties_count }} Units
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-btn-group">
                        <button type="button" class="dqa-btn dqa-teal" style="height: 40px;" onclick="submitModeFilter('project')">
                            <i class="fa-solid fa-magnifying-glass"></i> Apply Project Filter
                        </button>
                        @if($filterType === 'project' && !empty($selectedProject))
                            <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Clear Filter">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mode 4: Sale Filter Panel -->
            <div id="panel-sale" class="filter-mode-panel {{ in_array($filterType, ['sale']) ? 'active-panel' : '' }}">
                <div class="panel-content-row">
                    <div class="panel-input-flex">
                        <label class="filter-label"><i class="fa-solid fa-handshake text-emerald-400"></i> Filter Sales by Property / Land (Optional)</label>
                        <select name="sale_property_master_id" id="salePropertySelect" class="dash-select-input" onchange="submitModeFilter('sale')">
                            <option value="">-- All Property Sales (Full Portfolio) --</option>
                            @foreach($propertyMastersList as $pm)
                                <option value="{{ $pm->id }}" {{ (isset($selectedPropertyMaster) && $selectedPropertyMaster->id == $pm->id && $filterType === 'sale') ? 'selected' : '' }}>
                                    {{ $pm->property_name }} {{ $pm->property_code ? "({$pm->property_code})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-btn-group">
                        <button type="button" class="dqa-btn dqa-green" style="height: 40px;" onclick="submitModeFilter('sale')">
                            <i class="fa-solid fa-chart-line"></i> View Sales Analysis
                        </button>
                        @if($filterType === 'sale')
                            <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Clear Filter">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mode 5: Purchase Filter Panel -->
            <div id="panel-purchase" class="filter-mode-panel {{ in_array($filterType, ['purchase']) ? 'active-panel' : '' }}">
                <div class="panel-content-row">
                    <div class="panel-input-flex">
                        <label class="filter-label"><i class="fa-solid fa-cart-shopping text-amber-400"></i> Filter Purchases by Project (Materials &amp; Supplies)</label>
                        <select name="purchase_project_id" id="purchaseProjectSelect" class="dash-select-input" onchange="submitModeFilter('purchase')">
                            <option value="">-- All Purchases (Land Acquisitions + All Materials) --</option>
                            @foreach($projectsList as $prj)
                                <option value="{{ $prj->id }}" {{ (isset($selectedProject) && $selectedProject->id == $prj->id && $filterType === 'purchase') ? 'selected' : '' }}>
                                    {{ $prj->project_name }} {{ $prj->project_code ? "({$prj->project_code})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-btn-group">
                        <button type="button" class="dqa-btn dqa-amber" style="height: 40px;" onclick="submitModeFilter('purchase')">
                            <i class="fa-solid fa-boxes-stacked"></i> View Purchase Analysis
                        </button>
                        @if($filterType === 'purchase')
                            <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Clear Filter">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mode 6: Expense Filter Panel -->
            <div id="panel-expense" class="filter-mode-panel {{ in_array($filterType, ['expense']) ? 'active-panel' : '' }}">
                <div class="panel-content-row">
                    <div class="panel-input-flex">
                        <label class="filter-label"><i class="fa-solid fa-receipt text-rose-400"></i> Scope Expenses by Property / Land</label>
                        <select name="expense_property_master_id" id="expensePropertySelect" class="dash-select-input" onchange="submitModeFilter('expense')">
                            <option value="">-- All Expenses &amp; Outflows (Property + General + Rental) --</option>
                            @foreach($propertyMastersList as $pm)
                                <option value="{{ $pm->id }}" {{ (isset($selectedPropertyMaster) && $selectedPropertyMaster->id == $pm->id && $filterType === 'expense') ? 'selected' : '' }}>
                                    {{ $pm->property_name }} {{ $pm->property_code ? "({$pm->property_code})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-btn-group">
                        <button type="button" class="dqa-btn dqa-red" style="height: 40px;" onclick="submitModeFilter('expense')">
                            <i class="fa-solid fa-file-invoice-dollar"></i> View Expense Analysis
                        </button>
                        @if($filterType === 'expense')
                            <a href="{{ route('dashboard') }}" class="btn-filter-reset" title="Clear Filter">
                                <i class="fa-solid fa-xmark"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Active Filter Summary Pills -->
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
                        <span class="f-badge fb-amber"><i class="fa-solid fa-coins"></i> Purchase Price: ₹{{ number_format($selectedPropertyMaster->purchase_price ?? 0, 2) }}</span>
                        @if($selectedPropertyMaster->city)
                            <span class="f-badge fb-blue"><i class="fa-solid fa-location-dot"></i> {{ $selectedPropertyMaster->city }}</span>
                        @endif
                        <a href="{{ route('property-masters.show', $selectedPropertyMaster->id) }}" class="btn-view-all" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Details
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
            @elseif($filterType === 'sale')
                <div class="filter-active-pill-box" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08);">
                    <div class="filter-pill-title">
                        <i class="fa-solid fa-handshake" style="color:#34D399;"></i>
                        Active Filter: <span style="color:#34D399; font-weight:800;">Sales &amp; Revenue Intelligence</span>
                    </div>
                    <div class="filter-pill-badges">
                        <span class="f-badge fb-teal"><i class="fa-solid fa-circle-dollar-to-slot"></i> Sold Value: ₹{{ number_format($totalSalesRevenue, 2) }}</span>
                        <span class="f-badge fb-purple"><i class="fa-solid fa-cubes"></i> {{ $totalSoldUnitsCount }} Units Sold</span>
                        <span class="f-badge fb-amber"><i class="fa-solid fa-arrow-trend-up"></i> Realized Profit: ₹{{ number_format($totalSalesProfit, 2) }}</span>
                        <span class="f-badge fb-blue"><i class="fa-solid fa-percent"></i> Margin: {{ $salesProfitMargin }}%</span>
                        <a href="{{ route('property-sales.index') }}" class="btn-view-all">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> All Sales Records
                        </a>
                    </div>
                </div>
            @elseif($filterType === 'purchase')
                <div class="filter-active-pill-box" style="border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
                    <div class="filter-pill-title">
                        <i class="fa-solid fa-cart-shopping" style="color:#FBBF24;"></i>
                        Active Filter: <span style="color:#FBBF24; font-weight:800;">Land &amp; Material Purchases</span>
                    </div>
                    <div class="filter-pill-badges">
                        <span class="f-badge fb-amber"><i class="fa-solid fa-money-bill-wave"></i> Total Purchases: ₹{{ number_format($totalPurchases ?? 0, 2) }}</span>
                        <span class="f-badge fb-purple"><i class="fa-solid fa-building"></i> Land: ₹{{ number_format($totalLandPurchases ?? 0, 2) }} ({{ $totalLandPurchaseCount ?? 0 }} Lands)</span>
                        <span class="f-badge fb-teal"><i class="fa-solid fa-boxes-stacked"></i> Materials: ₹{{ number_format($totalMaterialPurchases ?? 0, 2) }}</span>
                        <a href="{{ route('property-masters.index') }}" class="btn-view-all">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Land Purchases
                        </a>
                        <a href="{{ route('stock-inwards.index') }}" class="btn-view-all">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Material Inwards
                        </a>
                    </div>
                </div>
            @elseif($filterType === 'expense')
                <div class="filter-active-pill-box" style="border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.08);">
                    <div class="filter-pill-title">
                        <i class="fa-solid fa-receipt" style="color:#F87171;"></i>
                        Active Filter: <span style="color:#F87171; font-weight:800;">Expense &amp; Cost Analysis</span>
                    </div>
                    <div class="filter-pill-badges">
                        <span class="f-badge fb-red" style="background:rgba(239,68,68,0.2);color:#FCA5A5;border:1px solid rgba(239,68,68,0.4);"><i class="fa-solid fa-receipt"></i> Total Expenses: ₹{{ number_format($totalExpenses ?? 0, 2) }}</span>
                        <span class="f-badge fb-purple"><i class="fa-solid fa-building"></i> Property: ₹{{ number_format($propertyExpenses ?? 0, 2) }}</span>
                        <span class="f-badge fb-blue"><i class="fa-solid fa-folder"></i> General: ₹{{ number_format($generalExpenses ?? 0, 2) }}</span>
                        <span class="f-badge fb-amber"><i class="fa-solid fa-key"></i> Rental: ₹{{ number_format($rentalExpenses ?? 0, 2) }}</span>
                        <a href="{{ route('expenses.index') }}" class="btn-view-all">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> All Expense Records
                        </a>
                    </div>
                </div>
            @endif
        </form>
    </div>

    <script>
    function switchFilterTab(mode) {
        document.getElementById('filterTypeInput').value = mode;

        var tabMap = {
            'all': 'tab-btn-all',
            'property': 'tab-btn-property',
            'project': 'tab-btn-project',
            'sale': 'tab-btn-sale',
            'purchase': 'tab-btn-purchase',
            'expense': 'tab-btn-expense'
        };
        var activeClasses = {
            'all': 'active-all',
            'property': 'active-prop',
            'project': 'active-proj',
            'sale': 'active-sale',
            'purchase': 'active-purchase',
            'expense': 'active-expense'
        };

        Object.keys(tabMap).forEach(function(key) {
            var btn = document.getElementById(tabMap[key]);
            if (btn) {
                btn.classList.remove('active-all', 'active-prop', 'active-proj', 'active-sale', 'active-purchase', 'active-expense');
                if (key === mode) {
                    btn.classList.add(activeClasses[key]);
                }
            }
        });

        var panels = ['panel-all', 'panel-property', 'panel-project', 'panel-sale', 'panel-purchase', 'panel-expense'];
        panels.forEach(function(pId) {
            var p = document.getElementById(pId);
            if (p) {
                if (pId === 'panel-' + mode) {
                    p.classList.add('active-panel');
                } else {
                    p.classList.remove('active-panel');
                }
            }
        });

        if (mode === 'all') {
            if (document.getElementById('propertyMasterSelect')) document.getElementById('propertyMasterSelect').value = '';
            if (document.getElementById('projectSelect')) document.getElementById('projectSelect').value = '';
            if (document.getElementById('salePropertySelect')) document.getElementById('salePropertySelect').value = '';
            if (document.getElementById('purchaseProjectSelect')) document.getElementById('purchaseProjectSelect').value = '';
            if (document.getElementById('expensePropertySelect')) document.getElementById('expensePropertySelect').value = '';
            document.getElementById('dashboardFilterForm').submit();
        } else if (mode === 'property') {
            var sel = document.getElementById('propertyMasterSelect');
            if (sel) sel.focus();
        } else if (mode === 'project') {
            var selPrj = document.getElementById('projectSelect');
            if (selPrj) selPrj.focus();
        } else if (mode === 'sale') {
            var selSale = document.getElementById('salePropertySelect');
            if (selSale) selSale.focus();
        } else if (mode === 'purchase') {
            var selPur = document.getElementById('purchaseProjectSelect');
            if (selPur) selPur.focus();
        } else if (mode === 'expense') {
            var selExp = document.getElementById('expensePropertySelect');
            if (selExp) selExp.focus();
        }
    }

    function submitModeFilter(mode) {
        document.getElementById('filterTypeInput').value = mode;
        if (mode === 'property') {
            if (document.getElementById('projectSelect')) document.getElementById('projectSelect').value = '';
            if (document.getElementById('salePropertySelect')) document.getElementById('salePropertySelect').value = '';
            if (document.getElementById('purchaseProjectSelect')) document.getElementById('purchaseProjectSelect').value = '';
            if (document.getElementById('expensePropertySelect')) document.getElementById('expensePropertySelect').value = '';
        } else if (mode === 'project') {
            if (document.getElementById('propertyMasterSelect')) document.getElementById('propertyMasterSelect').value = '';
            if (document.getElementById('salePropertySelect')) document.getElementById('salePropertySelect').value = '';
            if (document.getElementById('purchaseProjectSelect')) document.getElementById('purchaseProjectSelect').value = '';
            if (document.getElementById('expensePropertySelect')) document.getElementById('expensePropertySelect').value = '';
        } else if (mode === 'sale') {
            if (document.getElementById('projectSelect')) document.getElementById('projectSelect').value = '';
            if (document.getElementById('purchaseProjectSelect')) document.getElementById('purchaseProjectSelect').value = '';
            if (document.getElementById('expensePropertySelect')) document.getElementById('expensePropertySelect').value = '';
        } else if (mode === 'purchase') {
            if (document.getElementById('propertyMasterSelect')) document.getElementById('propertyMasterSelect').value = '';
            if (document.getElementById('salePropertySelect')) document.getElementById('salePropertySelect').value = '';
            if (document.getElementById('expensePropertySelect')) document.getElementById('expensePropertySelect').value = '';
        } else if (mode === 'expense') {
            if (document.getElementById('projectSelect')) document.getElementById('projectSelect').value = '';
            if (document.getElementById('purchaseProjectSelect')) document.getElementById('purchaseProjectSelect').value = '';
            if (document.getElementById('salePropertySelect')) document.getElementById('salePropertySelect').value = '';
        }
        document.getElementById('dashboardFilterForm').submit();
    }


    </script>

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
                Plots &amp; Asset Overview
            @endif
        </h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-5">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-purple"><i class="fa-solid fa-layer-group"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Plots</span>
                <span class="kpi-value">{{ number_format($totalProperties) }}</span>
                <span class="kpi-badge bk-purple">All Units</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-green"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Available Plots</span>
                <span class="kpi-value" style="color:#10B981;">{{ number_format($availableProperties) }}</span>
                <span class="kpi-badge bk-green">Ready to Sell</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-indigo"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Booked Plots</span>
                <span class="kpi-value" style="color:#818CF8;">{{ number_format($bookedProperties) }}</span>
                <span class="kpi-badge bk-indigo">Under Booking</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-house-circle-xmark"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Sold Plots</span>
                <span class="kpi-value" style="color:#F59E0B;">{{ number_format($soldProperties) }}</span>
                <span class="kpi-badge bk-amber">Closed Sales</span>
            </div>
        </div>

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
         SALES & PROFIT PERFORMANCE
    ════════════════════════════════════════════════════════════════ -->
    <div class="kpi-section-header" style="margin-top:10px;">
        <div style="width:6px;height:18px;background:linear-gradient(180deg,#10B981,#3B82F6);border-radius:4px;flex-shrink:0;"></div>
        <h3>
            @if($filterType === 'property' && $selectedPropertyMaster)
                {{ $selectedPropertyMaster->property_name }} — Pricing, Cost &amp; Profit Analysis (Purchase Cost vs Selling Price = Profit)
            @elseif($filterType === 'project' && $selectedProject)
                {{ $selectedProject->project_name }} — Financials &amp; Profit Analysis
            @else
                Sales &amp; Profit Performance (Lidhi Price - Sell Price = Profit)
            @endif
        </h3>
        <div class="kpi-section-divider"></div>
    </div>

    <div class="kpi-grid-4" style="margin-bottom: 24px;">
        <div class="kpi-card">
            <div class="kpi-icon-box ik-blue"><i class="fa-solid fa-handshake"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Sold Value</span>
                <span class="kpi-value" style="color:#60A5FA;" title="₹{{ number_format($totalSalesRevenue, 2) }}">₹{{ number_format($totalSalesRevenue, 2) }}</span>
                <span class="kpi-badge bk-blue">{{ $totalSoldUnitsCount ?? 0 }} Units Sold</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box ik-amber"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Purchase Cost</span>
                <span class="kpi-value" style="color:#FBBF24;" title="₹{{ number_format($totalSalesPurchaseCost, 2) }}">₹{{ number_format($totalSalesPurchaseCost, 2) }}</span>
                <span class="kpi-badge bk-amber">Acquisition Cost</span>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon-box {{ $totalSalesProfit >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $totalSalesProfit >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Total Realized Profit</span>
                <span class="kpi-value" style="color:{{ $totalSalesProfit >= 0 ? '#10B981' : '#EF4444' }};" title="₹{{ number_format($totalSalesProfit, 2) }}">₹{{ number_format($totalSalesProfit, 2) }}</span>
                <span class="kpi-badge {{ $totalSalesProfit >= 0 ? 'bk-green' : 'bk-red' }}">{{ $totalSalesProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</span>
            </div>
        </div>

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
            @php $netCash = $totalReceivedAmt - $totalExpenses; @endphp
            <div class="kpi-icon-box {{ $netCash >= 0 ? 'ik-green' : 'ik-red' }}"><i class="fa-solid fa-{{ $netCash >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i></div>
            <div class="kpi-info">
                <span class="kpi-label">Net Cash Flow</span>
                <span class="kpi-value" style="color:{{ $netCash >= 0 ? '#10B981' : '#EF4444' }};" title="₹{{ number_format($netCash, 0) }}">₹{{ number_format($netCash, 0) }}</span>
                <span class="kpi-badge {{ $netCash >= 0 ? 'bk-green' : 'bk-red' }}">{{ $netCash >= 0 ? 'Surplus' : 'Deficit' }}</span>
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
         SALE FILTER EXPLORER TABLE (When Sale mode is selected)
    ════════════════════════════════════════════════════════════════ -->
    @if(($filterType === 'sale' || ($flowType ?? '') === 'sale') && isset($recentSalesList) && $recentSalesList->count() > 0)
    <div class="plots-table-card" style="border-color: rgba(16, 185, 129, 0.35);">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon ik-green"><i class="fa-solid fa-handshake"></i></div>
                Sales &amp; Realized Revenue Transactions Explorer
            </div>
            <a href="{{ route('property-sales.create') }}" class="dqa-btn dqa-green" style="height:32px; padding: 4px 14px !important; font-size:12px !important;">
                <i class="fa-solid fa-plus"></i> New Property Sale
            </a>
        </div>

        <div class="table-container">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Sale Date</th>
                        <th>Customer / Buyer</th>
                        <th>Property / Plot Unit</th>
                        <th>Broker</th>
                        <th>Sale Price</th>
                        <th>Initial Paid</th>
                        <th>Remaining Due</th>
                        <th>Gross Profit</th>
                        <th>Net Profit</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSalesList as $sale)
                        @php
                            $sPlot = $sale->property ?? ($sale->properties->first() ?? null);
                        @endphp
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: #FFFFFF;">
                                    {{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #60A5FA;">
                                    {{ $sale->customer->name ?? $sale->customer_name ?? 'Walk-in Customer' }}
                                </span>
                                @if(!empty($sale->customer->phone))
                                    <div style="font-size:11px; color:#94A3B8;"><i class="fa-solid fa-phone"></i> {{ $sale->customer->phone }}</div>
                                @endif
                            </td>
                            <td>
                                <strong style="color:#C4B5FD;">
                                    @if($sale->properties && $sale->properties->count() > 1)
                                        {{ $sale->properties->count() }} Plots / Units
                                    @elseif($sPlot)
                                        {{ $sPlot->unit_no ? "Unit {$sPlot->unit_no}" : ($sPlot->property_name ?: "Plot #{$sPlot->id}") }}
                                    @else
                                        Plot #{{ $sale->property_id }}
                                    @endif
                                </strong>
                            </td>
                            <td>
                                <span style="color: #CBD5E1; font-size: 12px;">{{ $sale->broker->name ?? $sale->broker_name ?? 'Direct' }}</span>
                            </td>
                            <td>
                                <strong style="color: #34D399;">₹{{ number_format($sale->sale_amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="color: #60A5FA;">₹{{ number_format($sale->booking_amount ?? 0, 2) }}</span>
                            </td>
                            <td>
                                <span style="color: #FB923C;">₹{{ number_format($sale->remaining_amount ?? 0, 2) }}</span>
                            </td>
                            <td>
                                <span style="color: #FBBF24;">₹{{ number_format($sale->gross_profit ?? 0, 2) }}</span>
                            </td>
                            <td>
                                <strong style="color: {{ ($sale->net_profit ?? 0) >= 0 ? '#10B981' : '#EF4444' }};">
                                    ₹{{ number_format($sale->net_profit ?? 0, 2) }}
                                </strong>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('property-sales.show', $sale->id) }}" class="dqa-btn dqa-sky" style="padding: 4px 10px !important; font-size: 11px !important;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════
         PURCHASE FILTER EXPLORER TABLE (When Purchase mode is selected)
    ════════════════════════════════════════════════════════════════ -->
    @if(($filterType === 'purchase' || ($flowType ?? '') === 'purchase') && isset($recentPurchasesList) && $recentPurchasesList->count() > 0)
    <div class="plots-table-card" style="border-color: rgba(245, 158, 11, 0.35);">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon ik-amber"><i class="fa-solid fa-cart-shopping"></i></div>
                Purchases &amp; Material Stock Inward Explorer
            </div>
            <a href="{{ route('stock-inwards.create') }}" class="dqa-btn dqa-amber" style="height:32px; padding: 4px 14px !important; font-size:12px !important;">
                <i class="fa-solid fa-plus"></i> New Material Purchase
            </a>
        </div>

        <div class="table-container">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Inward / Bill No</th>
                        <th>Material Item</th>
                        <th>Supplier / Vendor</th>
                        <th>Project / Property</th>
                        <th>Qty</th>
                        <th>Unit Rate</th>
                        <th>GST Amt</th>
                        <th>Total Cost</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPurchasesList as $inward)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: #FFFFFF;">
                                    {{ $inward->inward_date ? \Carbon\Carbon::parse($inward->inward_date)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #FBBF24;">
                                    {{ $inward->inward_number ?: ($inward->bill_no ?: '-') }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #60A5FA;">
                                    {{ $inward->material->name ?? $inward->material_name ?? 'Material Item' }}
                                </strong>
                            </td>
                            <td>
                                <span style="color: #CBD5E1; font-size: 12px;">{{ $inward->supplier_name ?: '-' }}</span>
                            </td>
                            <td>
                                <span style="color: #A78BFA; font-size: 12px;">
                                    {{ $inward->project->project_name ?? ($inward->property->property_name ?? 'General') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: #FFFFFF;">{{ $inward->quantity }} {{ $inward->material->unit ?? '' }}</span>
                            </td>
                            <td>
                                <span style="color: #94A3B8;">₹{{ number_format($inward->rate, 2) }}</span>
                            </td>
                            <td>
                                <span style="color: #FCA5A5;">₹{{ number_format($inward->gst_amount ?? 0, 2) }}</span>
                            </td>
                            <td>
                                <strong style="color: #FBBF24;">₹{{ number_format($inward->total_amount, 2) }}</strong>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('stock-inwards.show', $inward->id) }}" class="dqa-btn dqa-sky" style="padding: 4px 10px !important; font-size: 11px !important;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ════════════════════════════════════════════════════════════════
         EXPENSE FILTER EXPLORER TABLE (When Expense mode is selected)
    ════════════════════════════════════════════════════════════════ -->
    @if(($filterType === 'expense' || ($flowType ?? '') === 'expense') && isset($recentExpensesList) && $recentExpensesList->count() > 0)
    <div class="plots-table-card" style="border-color: rgba(239, 68, 68, 0.35);">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon ik-red"><i class="fa-solid fa-receipt"></i></div>
                Expenses &amp; Outflows Breakdown Explorer
            </div>
            <a href="{{ route('expenses.create') }}" class="dqa-btn dqa-red" style="height:32px; padding: 4px 14px !important; font-size:12px !important;">
                <i class="fa-solid fa-plus"></i> Add New Expense
            </a>
        </div>

        <div class="table-container">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category / Title</th>
                        <th>Expense Type</th>
                        <th>Project / Property</th>
                        <th>Paid To / Vendor</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentExpensesList as $exp)
                        <tr>
                            <td>
                                <span style="font-weight: 700; color: #FFFFFF;">
                                    {{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #F87171;">{{ $exp->expense_title ?: ($exp->expense_category ?: 'Expense') }}</strong>
                                @if($exp->description)
                                    <div style="font-size:11px; color:#94A3B8;">{{ Str::limit($exp->description, 35) }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="ds-badge info">{{ $exp->expense_type ?: 'General' }}</span>
                            </td>
                            <td>
                                <span style="color: #A78BFA; font-size: 12px;">
                                    {{ $exp->project->project_name ?? ($exp->property->property_name ?? 'General') }}
                                </span>
                            </td>
                            <td>
                                <span style="color: #CBD5E1; font-size: 12px;">{{ $exp->paid_to ?: ($exp->vendor->name ?? '-') }}</span>
                            </td>
                            <td>
                                <span style="color: #94A3B8; font-size: 12px;">{{ $exp->payment_mode ?: 'Cash' }}</span>
                            </td>
                            <td>
                                <strong style="color: #EF4444; font-size: 14px;">₹{{ number_format($exp->amount, 2) }}</strong>
                            </td>
                            <td>
                                @if(($exp->approval_status ?? 'Approved') === 'Approved')
                                    <span class="ds-badge success">Approved</span>
                                @elseif(($exp->approval_status ?? '') === 'Rejected')
                                    <span class="ds-badge danger">Rejected</span>
                                @else
                                    <span class="ds-badge warning">Pending</span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('expenses.show', $exp->id) }}" class="dqa-btn dqa-sky" style="padding: 4px 10px !important; font-size: 11px !important;">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @php
        $propTotal = max(1, $totalProperties);
        $availPct  = round(($availableProperties / $propTotal) * 100);
        $soldPct   = round(($soldProperties     / $propTotal) * 100);
        $bookedPct = round(($bookedProperties   / $propTotal) * 100);
        $rentedPct = round(($rentedProperties   / $propTotal) * 100);
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
                        <div class="section-title-icon ik-purple"><i class="fa-solid fa-users"></i></div>
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
                            <div class="task-icon-wrap warning"><i class="fa-solid fa-exclamation-triangle"></i></div>
                            <div class="task-content">
                                <h5>Pending Balance</h5>
                                <p>₹{{ number_format($totalPendingAmt, 0) }} outstanding payments.</p>
                            </div>
                        </div>
                    @else
                        <div class="task-item success">
                            <div class="task-icon-wrap success"><i class="fa-solid fa-check"></i></div>
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
