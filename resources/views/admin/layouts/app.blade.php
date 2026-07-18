<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Delawala Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* ================================================================
       DESIGN TOKENS
    ================================================================ */
    :root {
        --sidebar-bg:    #E6EFF9;
        --sidebar-hover: rgba(30, 58, 138, 0.05);
        --sidebar-active:#2563EB;
        --sidebar-border:rgba(0, 0, 0, 0.06);
        --topbar-bg:     #1F2937;
        --main-bg:       #F1F5F9;
        --card-bg:       #FFFFFF;
        --text-primary:  #0F172A;
        --text-secondary:#64748B;
        --text-muted:    #94A3B8;
        --border-color:  #E2E8F0;
        --blue:          #3B82F6;
        --blue-light:    rgba(59,130,246,0.12);
        --blue-glow:     rgba(59,130,246,0.25);
        --green:         #10B981;
        --green-light:   rgba(16,185,129,0.1);
        --purple:        #8B5CF6;
        --purple-light:  rgba(139,92,246,0.1);
        --amber:         #F59E0B;
        --amber-light:   rgba(245,158,11,0.1);
        --red:           #EF4444;
        --red-light:     rgba(239,68,68,0.1);
        --sky:           #0EA5E9;
        --sky-light:     rgba(14,165,233,0.1);
        --soft-shadow:   0 1px 3px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.05);
        --card-shadow:   0 1px 3px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06);
        --card-hover:    0 4px 8px rgba(0,0,0,0.06), 0 16px 40px rgba(0,0,0,0.10);
        --font-primary:  'Inter', sans-serif;
        --sidebar-width: 280px;
        --topbar-height: 60px;
        --radius-sm:     8px;
        --radius-md:     12px;
        --radius-lg:     16px;
        --radius-xl:     20px;
        --transition:    all 0.22s cubic-bezier(0.4,0,0.2,1);
    }
    </style>
    <style>
    /* ================================================================
       RESET & BASE
    ================================================================ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; height: 100%; }
    body {
        font-family: var(--font-primary);
        background: var(--main-bg);
        color: var(--text-primary);
        min-height: 100vh;
        height: 100%;
        display: flex;
        overflow: hidden;          /* body itself does NOT scroll */
        animation: pageIn 0.35s ease both;
    }
    @keyframes pageIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Scrollbar — global */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

    /* ================================================================
       SIDEBAR
    ================================================================ */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        height: 100vh;           /* full viewport height always */
        position: fixed;
        left: 0; top: 0;
        display: flex;
        flex-direction: column;
        z-index: 100;
        border-right: 1px solid var(--sidebar-border);
        transition: width 0.3s cubic-bezier(0.4,0,0.2,1),
                    left 0.3s cubic-bezier(0.4,0,0.2,1),
                    box-shadow 0.3s ease;
        box-shadow: 4px 0 24px rgba(0,0,0,0.18);
        overflow: hidden;        /* clip children, menu scrolls internally */
    }

    /* Logo container */
    .logo-container {
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid var(--sidebar-border);
        height: 85px;
        min-height: 85px;
        flex-shrink: 0;
        overflow: hidden;
    }
    .logo-img {
        max-height: 68px;
        width: auto;
        max-width: 160px;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        border-radius: 0;
        transition: opacity 0.2s ease;
    }
    .logo-img:hover { opacity: 0.9; }
    /* Fallback text logo (shown if image missing) */
    .logo-icon {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, #D4AF37 0%, #F7D774 100%);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 16px; color: #0F172A;
        box-shadow: 0 4px 12px rgba(212,175,55,0.35);
        flex-shrink: 0;
    }
    .logo-text { display: flex; flex-direction: column; }
    .logo-title  { font-weight: 700; font-size: 14.5px; color: #0F172A; letter-spacing: 0.3px; text-transform: uppercase; }
    .logo-subtitle { font-size: 9.5px; color: #D4AF37; letter-spacing: 2px; text-transform: uppercase; font-weight: 600; margin-top: 1px; }

    /* Sidebar Menu — scrolls independently */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 12px 0 24px;
        list-style: none;
        /* Thin styled scrollbar */
        scrollbar-width: thin;
        scrollbar-color: rgba(15, 23, 42, 0.1) transparent;
    }
    .sidebar-menu::-webkit-scrollbar { width: 4px; }
    .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
    .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(15, 23, 42, 0.1); border-radius: 4px; }
    .sidebar-menu::-webkit-scrollbar-thumb:hover { background: rgba(15, 23, 42, 0.2); }

    .menu-group-label {
        font-size: 9.5px;
        font-weight: 700;
        color: #64748B;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        padding: 18px 20px 7px;
    }
    .menu-item {
        margin: 4px 10px; /* Consistent vertical spacing between menu items */
    }
    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        color: #334155;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }
    .menu-link::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: var(--blue);
        opacity: 0;
        transform: scaleY(0);
        transition: var(--transition);
    }
    .menu-link i {
        font-size: 16px;
        width: 22px; /* Perfect centered box size */
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        transition: var(--transition);
        flex-shrink: 0;
        margin-left: 0; /* Keep equal left margin for every icon */
    }
    .menu-link:hover {
        color: #1E40AF;
        background: rgba(30, 58, 138, 0.04);
    }
    .menu-link:hover i {
        color: var(--blue);
    }
    .menu-link.active, .menu-link.parent-active {
        color: #1E40AF;
        background: rgba(30, 58, 138, 0.08);
        font-weight: 600;
        box-shadow: 0 0 0 1px rgba(30, 58, 138, 0.12) inset;
    }
    .menu-link.active::before, .menu-link.parent-active::before {
        opacity: 1;
        transform: scaleY(1);
    }
    .menu-link.active i, .menu-link.parent-active i {
        color: #1E40AF;
    }

    /* Collapsible Submenus styling */
    .submenu-list {
        list-style: none;
        padding-left: 20px;
        margin-top: 3px;
        margin-bottom: 5px;
        display: none; /* Collapsed by default */
        transition: max-height 0.3s ease;
    }
    .submenu-item {
        margin: 3px 0; /* Consistent vertical spacing */
    }
    .submenu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 14px;
        color: #475569;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 500;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        white-space: nowrap;
    }
    .submenu-link i {
        font-size: 14px;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748B;
        transition: var(--transition);
        flex-shrink: 0;
    }
    .submenu-link:hover {
        color: #1E40AF;
        background: rgba(30, 58, 138, 0.04);
    }
    .submenu-link:hover i {
        color: var(--blue);
    }
    .submenu-link.active {
        color: #1E40AF;
        background: rgba(30, 58, 138, 0.08);
        font-weight: 600;
        box-shadow: 0 0 0 1px rgba(30, 58, 138, 0.12) inset;
    }
    .submenu-link.active i {
        color: #1E40AF;
    }
    .submenu-arrow {
        margin-left: auto;
        font-size: 10px !important;
        width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease !important;
        color: #94A3B8 !important;
        align-self: center; /* Center arrow icon vertically */
    }
    .menu-item.open > .submenu-toggle .submenu-arrow {
        transform: rotate(90deg);
    }
    .menu-item.open > .submenu-list {
        display: block;
    }
    .menu-link.parent-active {
        color: #1E40AF;
        background: rgba(30, 58, 138, 0.03);
        font-weight: 600;
    }
    .menu-link.parent-active i {
        color: #1E40AF;
    }
    .submenu-link.disabled-link {
        opacity: 0.6;
        cursor: not-allowed;
    }
    .submenu-link.disabled-link:hover {
        background: transparent;
        color: #64748B;
    }
    .sidebar-collapsed .submenu-list {
        display: none !important;
    }

    /* ================================================================
       MAIN CONTENT — scrolls independently from sidebar
    ================================================================ */
    .main-content {
        margin-left: var(--sidebar-width);
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        height: 100vh;             /* fill viewport height */
        overflow-y: auto;          /* MAIN content scrolls, not body */
        overflow-x: hidden;
        transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1);
    }

    /* ================================================================
       TOPBAR
    ================================================================ */                                                                                             
    .topbar {
        height: var(--topbar-height);
        min-height: var(--topbar-height);
        background: var(--topbar-bg);
        border-bottom: 3px solid rgba(0, 0, 0, 0.15);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 44px 0 16px;
        position: sticky;
        top: 0;
        z-index: 90;
    }
    .topbar-left { display: flex; align-items: center; gap: 32px; }
    .sidebar-toggle-btn {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.07);
        color: #94A3B8;
        font-size: 16px;
        cursor: pointer;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        transition: background 0.2s ease, color 0.2s ease,
                    border-color 0.2s ease, transform 0.2s ease;
        width: 38px; height: 38px;
        flex-shrink: 0;
    }
    .sidebar-toggle-btn:hover {
        background: rgba(255,255,255,0.10);
        color: #E2E8F0;
        border-color: rgba(255,255,255,0.14);
    }
    .sidebar-toggle-btn.is-collapsed {
        background: rgba(59,130,246,0.15);
        color: #93C5FD;
        border-color: rgba(59,130,246,0.3);
    }
    .sidebar-toggle-btn.is-collapsed:hover {
        background: rgba(59,130,246,0.22);
        color: #BFDBFE;
        border-color: rgba(59,130,246,0.4);
    }
    .page-header-title { font-size: 16px; color: #E2E8F0; font-weight: 600; letter-spacing: 0.2px; }
    .topbar-right { display: flex; align-items: center; gap: 32px; }
    .user-panel { display: flex; align-items: center; gap: 18px; }
    .user-avatar {
        width: 34px; height: 34px;
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 13px; color: #FFF;
        box-shadow: 0 2px 8px rgba(59,130,246,0.35);
    }
    .user-info { display: flex; flex-direction: column; }
    .user-name { font-size: 13px; font-weight: 600; color: #E2E8F0; }
    .user-role { font-size: 10.5px; color: #E2E8F0; }
    .logout-form { display: inline-block; }
    .logout-btn {
        background: rgba(239,68,68,0.1);
        color: #FCA5A5;
        border: 1px solid rgba(239,68,68,0.2);
        padding: 7px 14px;
        font-size: 12.5px;
        font-weight: 600;
        font-family: var(--font-primary);
        cursor: pointer;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; gap: 6px;
        transition: var(--transition);
    }
    .logout-btn:hover { background: rgba(239,68,68,0.18); color: #FEE2E2; border-color: rgba(239,68,68,0.35); }

    /* ================================================================
       CONTENT BODY
    ================================================================ */
    .content-body { padding: 16px 36px 48px; flex: 1; }

    /* ================================================================
       GLOBAL CARD OVERRIDE — lift all card-box styles
    ================================================================ */
    .card-box {
        background: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: var(--card-shadow) !important;
        transition: box-shadow 0.22s ease, transform 0.22s ease !important;
    }
    .card-box:hover {
        box-shadow: var(--card-hover) !important;
    }

    /* ================================================================
       GLOBAL ACTION BUTTON SYSTEM (table rows — View / Edit / Delete)
    ================================================================ */
    .table-action-buttons,
    .action-buttons,
    .btn-actions {
        display: flex !important;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        white-space: nowrap;
    }
    .table-action-buttons > *,
    .action-buttons > *,
    .btn-actions > * {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        margin: 0 !important;
        white-space: nowrap;
    }
    td.actions,
    td.action-column {
        white-space: nowrap;
        min-width: 220px;
    }
    @media (max-width: 768px) {
        .table-action-buttons,
        .action-buttons,
        .btn-actions {
            flex-wrap: wrap;
        }
    }
    .btn-view, a.btn-view, button.btn-view {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: #F4F8FF; color: #1E5AA8 !important;
        border: 1px solid rgba(30,90,168,.20); border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        box-shadow: 0 4px 12px rgba(30,90,168,.08);
        transition: all .25s ease; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-view:hover {
        background: #1E5AA8; color: #fff !important;
        text-decoration: none !important; transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(30,90,168,.22);
    }
    .btn-edit, a.btn-edit, button.btn-edit {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: #F4F8FF; color: #1E5AA8 !important;
        border: 1px solid rgba(30,90,168,.20); border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        box-shadow: 0 4px 12px rgba(30,90,168,.08);
        transition: all .25s ease; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-edit:hover {
        background: #2F6FE4; color: #fff !important;
        text-decoration: none !important; transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(47,111,228,.22);
    }
    .btn-delete, a.btn-delete, button.btn-delete {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: linear-gradient(135deg,#DC3545,#C82333);
        color: #fff !important; border: none; border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        box-shadow: 0 8px 18px rgba(220,53,69,.22);
        transition: all .25s ease; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-delete:hover {
        color: #fff !important; text-decoration: none !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(220,53,69,.32);
    }
    .btn-view i, .btn-edit i, .btn-delete i,
    .btn-view svg, .btn-edit svg, .btn-delete svg {
        font-size: 14px; line-height: 1;
    }

    /* ================================================================
       GLOBAL FORM-PAGE BUTTON SYSTEM (Save / Cancel / Back)
    ================================================================ */
    .btn-gold {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
        color: #FFF !important;
        border: none !important;
        border-radius: var(--radius-sm) !important;
        box-shadow: 0 2px 8px rgba(59,130,246,0.35) !important;
        transition: var(--transition) !important;
    }
    .btn-gold:hover {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(59,130,246,0.45) !important;
    }
    .btn-outline {
        border: 1px solid var(--border-color) !important;
        border-radius: var(--radius-sm) !important;
        transition: var(--transition) !important;
    }
    .btn-outline:hover {
        border-color: var(--blue) !important;
        color: var(--blue) !important;
        background: var(--blue-light) !important;
        transform: translateY(-1px) !important;
    }

    /* ================================================================
       GLOBAL FORM OVERRIDES
    ================================================================ */
    .form-control {
        border-radius: var(--radius-sm) !important;
        border: 1.5px solid var(--border-color) !important;
        font-family: var(--font-primary) !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
        font-size: 13.5px !important;
    }
    .form-control:focus {
        border-color: var(--blue) !important;
        box-shadow: 0 0 0 3px var(--blue-glow) !important;
        outline: none !important;
    }
    .form-control.is-invalid, .is-invalid {
        border: 1px solid #dc3545 !important;
    }
    .form-control.is-invalid:focus, .is-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.15rem rgba(220,53,69,.15) !important;
    }
    .text-error, .dw-invalid-feedback {
        color: #dc3545 !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        margin-top: 4px !important;
        margin-left: 2px !important;
        line-height: 1.3 !important;
        display: block !important;
        opacity: 0;
        transition: opacity 0.18s ease-in-out !important;
    }
    .text-error.show, .dw-invalid-feedback.show {
        opacity: 1 !important;
    }
    .search-input {
        border-radius: var(--radius-sm) !important;
        border: 1.5px solid var(--border-color) !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
    }
    .search-input:focus {
        border-color: var(--blue) !important;
        box-shadow: 0 0 0 3px var(--blue-glow) !important;
        outline: none !important;
    }
    .filter-control {
        border-radius: var(--radius-sm) !important;
        border: 1.5px solid var(--border-color) !important;
        transition: border-color 0.18s ease !important;
    }
    .filter-control:focus {
        border-color: var(--blue) !important;
        outline: none !important;
    }

    /* ================================================================
       GLOBAL TABLE OVERRIDES
    ================================================================ */
    .premium-table th {
        background: #F8FAFC !important;
        color: #475569 !important;
        font-size: 11px !important;
        letter-spacing: 0.8px !important;
        border-bottom: 2px solid var(--border-color) !important;
        font-weight: 700 !important;
        padding: 12px 16px !important;
    }
    .premium-table td {
        padding: 14px 16px !important;
        border-bottom: 1px solid #F1F5F9 !important;
        vertical-align: middle !important;
        transition: background 0.15s ease !important;
    }
    .premium-table tbody tr {
        transition: background 0.15s ease, box-shadow 0.15s ease !important;
    }
    .premium-table tbody tr:hover {
        background: #F0F7FF !important;
    }

    /* ================================================================
       BADGE FIXES
    ================================================================ */
    .badge-active   { background: rgba(16,185,129,0.1) !important; color: #059669 !important; }
    .badge-inactive { background: rgba(239,68,68,0.1)  !important; color: #DC2626 !important; }

    /* ================================================================
       ALERT SUCCESS
    ================================================================ */
    .alert-success {
        background: rgba(16,185,129,0.07) !important;
        border: 1px solid rgba(16,185,129,0.2) !important;
        color: #065F46 !important;
        border-radius: var(--radius-sm) !important;
    }

    /* ================================================================
       STAT / SUMMARY CARDS
    ================================================================ */
    .stat-card, .sum-card {
        border-radius: var(--radius-lg) !important;
        box-shadow: var(--card-shadow) !important;
        border: 1px solid var(--border-color) !important;
        transition: transform 0.22s ease, box-shadow 0.22s ease !important;
    }
    .stat-card:hover, .sum-card:hover {
        transform: translateY(-3px) !important;
        box-shadow: var(--card-hover) !important;
    }

    /* ================================================================
       DESKTOP SIDEBAR COLLAPSED STATE
    ================================================================ */

    /* When body has .sidebar-collapsed class, sidebar shrinks to icon-only rail */
    .sidebar-collapsed .sidebar {
        width: 72px;
    }
    .sidebar-collapsed .main-content {
        margin-left: 72px;
    }
    /* Hide text labels and section headings when collapsed */
    .sidebar-collapsed .menu-link span,
    .sidebar-collapsed .menu-group-label,
    .sidebar-collapsed .logo-text {
        opacity: 0;
        pointer-events: none;
        width: 0;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.2s ease, width 0.25s ease;
    }
    /* Smooth text fade-in when expanding */
    .menu-link span,
    .menu-group-label,
    .logo-text {
        transition: opacity 0.2s ease, width 0.25s ease;
        opacity: 1;
        width: auto;
    }
    /* Center icons when collapsed */
    .sidebar-collapsed .menu-item { margin: 1px 6px; }
    .sidebar-collapsed .menu-link {
        justify-content: center;
        padding: 10px;
        gap: 0;
    }
    .sidebar-collapsed .menu-link i {
        width: auto;
        font-size: 17px;
    }
    .sidebar-collapsed .logo-container {
        padding: 10px;
        justify-content: center;
    }
    /* Scale down logo image in collapsed mode for a clean look */
    .sidebar-collapsed .logo-img {
        max-height: 48px;
        max-width: 52px;
        margin: 0 auto;
        display: block;
    }
    /* Smooth transition for menu-link layout change */
    .menu-link {
        transition: background 0.2s ease, color 0.2s ease,
                    padding 0.3s cubic-bezier(0.4,0,0.2,1),
                    justify-content 0.3s ease,
                    gap 0.3s ease,
                    box-shadow 0.2s ease;
    }
    /* Tooltip on hover when collapsed */
    .sidebar-collapsed .menu-link {
        position: relative;
    }
    .sidebar-collapsed .menu-link:hover::after {
        content: attr(data-label);
        position: absolute;
        left: 72px;
        top: 50%;
        transform: translateY(-50%);
        background: #1E293B;
        color: #F1F5F9;
        font-size: 12.5px;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
        white-space: nowrap;
        z-index: 9999;
        pointer-events: none;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.08);
        animation: tooltipIn 0.15s ease both;
    }
    @keyframes tooltipIn {
        from { opacity: 0; transform: translateY(-50%) translateX(-6px); }
        to   { opacity: 1; transform: translateY(-50%) translateX(0); }
    }

    /* ================================================================
       MOBILE OVERLAY
    ================================================================ */
    .sidebar-overlay {
        display: none;
        position: fixed; top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.55);
        z-index: 95;
        backdrop-filter: blur(2px);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .sidebar-overlay.active {
        display: block;
        opacity: 1;
    }

    /* ================================================================
       RESPONSIVE
    ================================================================ */
    @media (max-width: 992px) {
        :root {
            --sidebar-width: 260px;
        }
        .sidebar {
            left: calc(-1 * var(--sidebar-width));
            box-shadow: none;
            /* width transition not needed on mobile, only left */
            transition: left 0.3s cubic-bezier(0.4,0,0.2,1),
                        box-shadow 0.3s ease;
        }
        .sidebar.active {
            left: 0;
            box-shadow: 4px 0 32px rgba(0,0,0,0.3);
        }
        .main-content {
            margin-left: 0;        /* full width on mobile */
            transition: none;      /* no margin shift on mobile */
        }
        .sidebar-overlay.active { display: block; }
        /* No collapsed state on mobile — use slide in/out instead */
        .sidebar-collapsed .sidebar { width: var(--sidebar-width); left: calc(-1 * var(--sidebar-width)); }
        .sidebar-collapsed .sidebar.active { left: 0; }
        .sidebar-collapsed .main-content { margin-left: 0; }
        .sidebar-collapsed .menu-link span,
        .sidebar-collapsed .menu-group-label,
        .sidebar-collapsed .logo-text { opacity: 1; width: auto; }
    }
    @media (max-width: 576px) {
        .topbar { padding: 0 14px; }
        .content-body { padding: 16px 14px 32px; }
        .user-info { display: none; }
    }

    /* ================================================================
       PAGE-LEVEL ANIMATION HELPERS
    ================================================================ */
    .content-body > * {
        animation: slideUp 0.28s cubic-bezier(0.4,0,0.2,1) both;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Stagger children */
    .content-body > *:nth-child(1) { animation-delay: 0.02s; }
    .content-body > *:nth-child(2) { animation-delay: 0.06s; }
    .content-body > *:nth-child(3) { animation-delay: 0.10s; }
    .content-body > *:nth-child(4) { animation-delay: 0.14s; }

    /* Modal animation */
    .modal.active .modal-box {
        animation: modalIn 0.22s cubic-bezier(0.4,0,0.2,1) both;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.94) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    /* Responsive overrides and utility improvements */
    html, body {
        overflow-x: hidden; /* Prevent horizontal scrolling */
        width: 100%;
    }
    .main-content {
        overflow-x: hidden; /* Prevent layout shifts */
    }
    .table-responsive {
        width: 100%;
        margin-bottom: 1rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
    }
    @media (max-width: 768px) {
        .content-body {
            padding: 12px 12px 24px !important;
        }
        .topbar {
            padding: 0 16px !important;
        }
        .user-panel {
            gap: 8px !important;
        }
        /* Buttons wrapping & stacking on mobile */
        .btn, .btn-gold, .btn-outline, .btn-view, .btn-edit, .btn-delete {
            white-space: normal !important;
            word-wrap: break-word;
            text-align: center;
        }
        .table-action-buttons, .action-buttons, .btn-actions {
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        /* Form fields stacking */
        .form-group, .row > [class*="col-"] {
            margin-bottom: 12px;
        }
        /* Ensure tables fill width */
        .premium-table {
            width: 100% !important;
            display: table !important;
        }
    }
    /* Avoid dropdown screen overflow */
    .dropdown-menu {
        max-width: 290px;
        overflow-x: hidden;
        text-overflow: ellipsis;
    }
    </style>
</head>
<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ================================================================
     SIDEBAR
================================================================ -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}" alt="Delawala Properties" class="logo-img">
        @elseif(file_exists(public_path('images/logo.jpg')))
            <img src="{{ asset('images/logo.jpg') }}?v={{ filemtime(public_path('images/logo.jpg')) }}" alt="Delawala Properties" class="logo-img">
        @else
            <div class="logo-icon">D</div>
            <div class="logo-text">
                <span class="logo-title">Delawala</span>
                <span class="logo-subtitle">Properties</span>
            </div>
        @endif
    </div>

    <ul class="sidebar-menu">
        @php
            $currentRoute  = Route::currentRouteName();
            $isFirmSession = session('login_type') === 'firm' && session('firm_id');

            if ($isFirmSession) {
                // Firm login — build a lightweight proxy object so sidebar permission
                // checks don't throw. Firm owners bypass all permission checks.
                $authUser = new class {
                    public function isAdmin()        { return true; }  // bypass permission gates
                    public function hasPermission($p){ return true; }  // bypass permission gates
                    public $role = null;
                    public $name = '';
                };
                $authUser->name = session('firm_name', 'Firm');
            } else {
                $authUser = Auth::user();
                $authUser->loadMissing('role');
                if ($authUser->role && is_object($authUser->role)) {
                    $authUser->role->loadMissing('permissions');
                }
            }
        @endphp

        <li class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link {{ $currentRoute == 'dashboard' ? 'active' : '' }}" data-label="Dashboard">
                <i class="fa-solid fa-chart-pie"></i><span>Dashboard</span>
            </a>
        </li>

        {{-- 1. Masters --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="1. Masters">
                <i class="fa-solid fa-layer-group"></i><span>1. Masters</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if(!session('login_type') === 'firm' || session('login_type') !== 'firm')
                <li class="submenu-item">
                    <a href="{{ route('firm-master.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'firm-master.') ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i><span>Firms</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('financial-years.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'financial-years.') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-days"></i><span>Financial Years</span>
                    </a>
                </li>
                @if($authUser->hasPermission('user_management_view'))
                <li class="submenu-item">
                    <a href="{{ route('users.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'users.') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i><span>Users</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('role_permission_view'))
                <li class="submenu-item">
                    <a href="{{ route('roles.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'roles.') ? 'active' : '' }}">
                        <i class="fa-solid fa-shield-halved"></i><span>Roles & Permissions</span>
                    </a>
                </li>
                @endif
                @endif

                @if($authUser->hasPermission('customer_view'))
                <li class="submenu-item">
                    <a href="{{ route('customers.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'customers.') ? 'active' : '' }}">
                        <i class="fa-solid fa-users"></i><span>Customer</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('vendor_view'))
                <li class="submenu-item">
                    <a href="{{ route('vendors.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'vendors.') ? 'active' : '' }}">
                        <i class="fa-solid fa-truck-field"></i><span>Vendor</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('broker_view'))
                <li class="submenu-item">
                    <a href="{{ route('brokers.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'brokers.') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-tie"></i><span>Broker</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('property_type_view'))
                <li class="submenu-item">
                    <a href="{{ route('property-types.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'property-types.') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i><span>Property Type</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('expense_category_view'))
                <li class="submenu-item">
                    <a href="{{ route('expense-categories.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'expense-categories.') ? 'active' : '' }}">
                        <i class="fa-solid fa-tags"></i><span>Expense Category</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('payment_mode_view'))
                <li class="submenu-item">
                    <a href="{{ route('payment-modes.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'payment-modes.') ? 'active' : '' }}">
                        <i class="fa-solid fa-wallet"></i><span>Payment Mode</span>
                    </a>
                </li>
                @endif
                <li class="submenu-item">
                    <a href="{{ route('invoice-settings.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'invoice-settings.') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders"></i><span>Tax / GST Settings</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- 2. Property Management --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="2. Property Management">
                <i class="fa-solid fa-building"></i><span>2. Property Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('property_view'))
                <li class="submenu-item">
                    <a href="{{ route('properties.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'properties.') ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i><span>Property Master</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('inventory_view'))
                <li class="submenu-item">
                    <a href="javascript:void(0);" class="submenu-link nested-submenu-toggle" style="padding-left: 20px;">
                        <i class="fa-solid fa-boxes-stacked"></i><span>Property Inventory</span>
                        <i class="fa-solid fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu-list nested-submenu-list" style="display: none; padding-left: 15px;">
                        <li class="submenu-item">
                            <a href="{{ route('material-categories.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'material-categories.') ? 'active' : '' }}">
                                <i class="fa-solid fa-folder-tree"></i><span>Material Category</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('materials.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'materials.') ? 'active' : '' }}">
                                <i class="fa-solid fa-box"></i><span>Material Master</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('stock-inwards.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'stock-inwards.') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-down-to-bracket"></i><span>Stock Inward</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('stock-outwards.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'stock-outwards.') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i><span>Stock Outward</span>
                            </a>
                        </li>
                        <li class="submenu-item">
                            <a href="{{ route('stock-report.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'stock-report.') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-bar"></i><span>Current Stock Report</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if($authUser->hasPermission('property_view'))
                <li class="submenu-item">
                    <a href="{{ route('property-availability.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'property-availability.') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-check"></i><span>Property Status</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('property_documents_view'))
                <li class="submenu-item">
                    <a href="{{ route('property-documents.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'property-documents.') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-open"></i><span>Property Documents</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>

        {{-- 3. Customer Process --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="3. Customer Process">
                <i class="fa-solid fa-people-group"></i><span>3. Customer Process</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('customer_view'))
                <li class="submenu-item">
                    <a href="{{ route('customers.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'customers.') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-plus"></i><span>Customer Registration</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('form_management_view'))
                <li class="submenu-item">
                    <a href="{{ route('forms.index') }}" class="submenu-link {{ (str_starts_with($currentRoute ?? '', 'forms.') || str_starts_with($currentRoute ?? '', 'form-submissions.')) ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-question"></i><span>Inquiry</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('booking_view'))
                <li class="submenu-item">
                    <a href="{{ route('bookings.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'bookings.') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-check"></i><span>Booking</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>

        {{-- 4. Sales Management --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="4. Sales Management">
                <i class="fa-solid fa-chart-line"></i><span>4. Sales Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('property_sales_view'))
                <li class="submenu-item">
                    <a href="{{ route('property-sales.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'property-sales.') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-contract"></i><span>Sales Agreement</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('credit_note_view'))
                <li class="submenu-item">
                    <a href="{{ route('credit-notes.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'credit-notes.') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-plus"></i><span>Credit Note</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('debit_note_view'))
                <li class="submenu-item">
                    <a href="{{ route('debit-notes.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'debit-notes.') ? 'active' : '' }}">
                        <i class="fa-solid fa-circle-minus"></i><span>Debit Note</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('payment_view'))
                <li class="submenu-item">
                    <a href="{{ route('payments.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'payments.') ? 'active' : '' }}">
                        <i class="fa-solid fa-money-bill-wave"></i><span>Payment Collection</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>

        {{-- 5. Rental Management --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="5. Rental Management">
                <i class="fa-solid fa-house"></i><span>5. Rental Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('tenant_view'))
                <li class="submenu-item">
                    <a href="{{ route('tenants.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'tenants.') ? 'active' : '' }}">
                        <i class="fa-solid fa-house-user"></i><span>Tenant</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('rental_view'))
                <li class="submenu-item">
                    <a href="{{ route('rentals.index') }}" class="submenu-link {{ (str_starts_with($currentRoute ?? '', 'rentals.') || str_starts_with($currentRoute ?? '', 'rental-payments.')) ? 'active' : '' }}">
                        <i class="fa-solid fa-key"></i><span>Rent Agreement</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('rental_view'))
                <li class="submenu-item">
                    <a href="{{ route('rentals.index') }}" class="submenu-link">
                        <i class="fa-solid fa-hand-holding-dollar"></i><span>Rent Collection</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('reports_view'))
                <li class="submenu-item">
                    <a href="{{ route('reports.rentals') }}" class="submenu-link {{ $currentRoute == 'reports.rentals' ? 'active' : '' }}">
                        <i class="fa-solid fa-file-contract"></i><span>Rental Reports</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- 6. Finance & Accounts --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="6. Finance & Accounts">
                <i class="fa-solid fa-calculator"></i><span>6. Finance & Accounts</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('income_view'))
                <li class="submenu-item">
                    <a href="{{ route('incomes.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'incomes.') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-trend-up"></i><span>Income</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('expense_view'))
                <li class="submenu-item">
                    <a href="{{ route('expenses.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'expenses.') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i><span>Expenses</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('purchase_view'))
                <li class="submenu-item">
                    <a href="{{ route('purchases.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'purchases.') ? 'active' : '' }}">
                        <i class="fa-solid fa-cart-shopping"></i><span>Purchases</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('receipt_view'))
                <li class="submenu-item">
                    <a href="{{ route('receipts.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'receipts.') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i><span>Receipt Voucher</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('loan_view'))
                <li class="submenu-item">
                    <a href="{{ route('loans.index') }}" class="submenu-link">
                        <i class="fa-solid fa-calendar-minus"></i><span>EMI Schedule</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('loans.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'loans.') ? 'active' : '' }}">
                        <i class="fa-solid fa-landmark"></i><span>Loan Management</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- 7. Broker Commission ── --}}
        @if($authUser->hasPermission('broker_commission_view'))
        <li class="menu-item">
            <a href="{{ route('broker-commissions.index') }}" class="menu-link {{ str_starts_with($currentRoute ?? '', 'broker-commissions.') ? 'active' : '' }}" data-label="7. Broker Commission">
                <i class="fa-solid fa-percent"></i><span>7. Broker Commission</span>
            </a>
        </li>
        @endif

        {{-- 8. Reports --}}
        @if($authUser->hasPermission('reports_view'))
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="8. Reports">
                <i class="fa-solid fa-chart-column"></i><span>8. Reports</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                <li class="submenu-item">
                    <a href="{{ route('reports.sales') }}" class="submenu-link {{ $currentRoute == 'reports.sales' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i><span>Sales Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.inventory') }}" class="submenu-link {{ $currentRoute == 'reports.inventory' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-column"></i><span>Purchase Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.payments') }}" class="submenu-link {{ $currentRoute == 'reports.payments' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i><span>Payment Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.rentals') }}" class="submenu-link {{ $currentRoute == 'reports.rentals' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-bar"></i><span>Rental Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.index') }}" class="submenu-link {{ $currentRoute == 'reports.index' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-area"></i><span>Property Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.gst-sales') }}" class="submenu-link {{ $currentRoute == 'reports.gst-sales' ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i><span>GST Sales Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.gst-purchase') }}" class="submenu-link {{ $currentRoute == 'reports.gst-purchase' ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i><span>GST Purchase Report</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.profit-loss') }}" class="submenu-link {{ $currentRoute == 'reports.profit-loss' ? 'active' : '' }}">
                        <i class="fa-solid fa-scale-balanced"></i><span>Profit & Loss</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.balance-sheet') }}" class="submenu-link {{ $currentRoute == 'reports.balance-sheet' ? 'active' : '' }}">
                        <i class="fa-solid fa-wallet"></i><span>Balance Sheet</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.cash-flow') }}" class="submenu-link {{ $currentRoute == 'reports.cash-flow' ? 'active' : '' }}">
                        <i class="fa-solid fa-money-bill-transfer"></i><span>Cash Flow</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('reports.index') }}" class="submenu-link">
                        <i class="fa-solid fa-clock-rotate-left"></i><span>Outstanding Report</span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- 9. Utilities --}}
        @if(session('login_type') !== 'firm')
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="9. Utilities">
                <i class="fa-solid fa-screwdriver-wrench"></i><span>9. Utilities</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('audit_logs_view'))
                <li class="submenu-item">
                    <a href="{{ route('audit-logs.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'audit-logs.') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i><span>Audit Logs</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('backup_view'))
                <li class="submenu-item">
                    <a href="{{ route('backups.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'backups.') ? 'active' : '' }}">
                        <i class="fa-solid fa-database"></i><span>Backup Database</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif

        {{-- 10. Settings --}}
        @if(session('login_type') !== 'firm')
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="10. Settings">
                <i class="fa-solid fa-gears"></i><span>10. Settings</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                <li class="submenu-item">
                    <a href="{{ route('invoice-settings.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'invoice-settings.') ? 'active' : '' }}">
                        <i class="fa-solid fa-sliders"></i><span>Company Settings</span>
                    </a>
                </li>
                @if($authUser->hasPermission('user_management_view'))
                <li class="submenu-item">
                    <a href="{{ route('users.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'users.') ? 'active' : '' }}">
                        <i class="fa-solid fa-users-gear"></i><span>User Settings</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif
    </ul>
</div>

<!-- ================================================================
     MAIN CONTENT
================================================================ -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle-btn" id="sidebarToggle">
                <i class="fa-solid fa-bars-staggered"></i>
            </button>
            <h1 class="page-header-title">@yield('page-title')</h1>
        </div>
        <div class="topbar-right">
            <div class="user-panel">
                @php
                    if (session('login_type') === 'firm') {
                        $displayName   = session('firm_name', 'Firm');
                        $displayInitial = strtoupper(substr($displayName, 0, 1));
                        $displayRole   = 'Firm Account';
                    } else {
                        $displayName    = Auth::user()->name ?? 'Administrator';
                        $displayInitial = strtoupper(substr($displayName, 0, 1));
                        $u = Auth::user();
                        $displayRole = is_object($u->role)
                            ? ($u->role->role_name ?? $u->role->name ?? 'User')
                            : ucfirst($u->role ?? 'User');
                    }
                @endphp
                <div class="user-avatar">{{ $displayInitial }}</div>
                <div class="user-info">
                    <span class="user-name">{{ $displayName }}</span>
                    <span class="user-role">{{ $displayRole }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Page Body -->
    <div class="content-body">
        @yield('content')
    </div>
</div>

<script>
const sidebarToggle = document.getElementById('sidebarToggle');
const sidebar       = document.getElementById('sidebar');
const overlay       = document.getElementById('sidebarOverlay');
const body          = document.body;

const isDesktop = () => window.innerWidth > 992;

// ── Sync toggle button active state with current collapsed state ──
function syncToggleBtn() {
    if (!sidebarToggle) return;
    if (isDesktop()) {
        // On desktop: button is "active" (blue tint) when sidebar IS collapsed
        if (body.classList.contains('sidebar-collapsed')) {
            sidebarToggle.classList.add('is-collapsed');
        } else {
            sidebarToggle.classList.remove('is-collapsed');
        }
    } else {
        // On mobile: button is "active" when sidebar IS open
        if (sidebar.classList.contains('active')) {
            sidebarToggle.classList.add('is-collapsed');
        } else {
            sidebarToggle.classList.remove('is-collapsed');
        }
    }
}

// ── Restore persisted collapsed state on page load (desktop only) ──
const STORAGE_KEY = 'dw_sidebar_collapsed';
if (isDesktop() && localStorage.getItem(STORAGE_KEY) === '1') {
    body.classList.add('sidebar-collapsed');
}
syncToggleBtn();

if (sidebarToggle && sidebar && overlay) {

    sidebarToggle.addEventListener('click', () => {
        if (isDesktop()) {
            // Desktop: collapse ↔ expand rail
            const isNowCollapsed = body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, isNowCollapsed ? '1' : '0');
        } else {
            // Mobile: slide sidebar in/out as off-canvas
            const isNowOpen = sidebar.classList.toggle('active');
            overlay.classList.toggle('active', isNowOpen);
        }
        syncToggleBtn();
    });

    // Clicking overlay closes mobile sidebar
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        syncToggleBtn();
    });

    // Handle resize — clean up conflicting states
    window.addEventListener('resize', () => {
        if (!isDesktop()) {
            // Switched to mobile: remove desktop collapsed class
            body.classList.remove('sidebar-collapsed');
        } else {
            // Switched to desktop: close mobile sidebar/overlay
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            // Restore persisted desktop state
            if (localStorage.getItem(STORAGE_KEY) === '1') {
                body.classList.add('sidebar-collapsed');
            }
        }
        syncToggleBtn();
    });
}

// ── Attach data-label for tooltip when sidebar is collapsed ──
document.querySelectorAll('.menu-link').forEach(link => {
    const span = link.querySelector('span');
    if (span) link.setAttribute('data-label', span.textContent.trim());
});

// ── Collapsible Submenu Logic ──
document.querySelectorAll('.submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        
        // If sidebar is collapsed, expand it first
        if (body.classList.contains('sidebar-collapsed')) {
            body.classList.remove('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, '0');
            syncToggleBtn();
        }
        
        const parentItem = this.closest('.menu-item');
        const submenu = parentItem.querySelector('.submenu-list');
        
        if (parentItem.classList.contains('open')) {
            parentItem.classList.remove('open');
            if (submenu) submenu.style.display = 'none';
        } else {
            // Close other open submenus first for accordion effect
            document.querySelectorAll('.menu-item.open').forEach(openItem => {
                if (openItem !== parentItem) {
                    openItem.classList.remove('open');
                    const openSubmenu = openItem.querySelector('.submenu-list');
                    if (openSubmenu) openSubmenu.style.display = 'none';
                }
            });
            
            parentItem.classList.add('open');
            if (submenu) submenu.style.display = 'block';
        }
    });
});

// Nested submenu toggle (e.g. Property Inventory)
document.querySelectorAll('.nested-submenu-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const parentItem = this.closest('.submenu-item');
        const submenu = parentItem.querySelector('.nested-submenu-list');
        
        if (parentItem.classList.contains('open')) {
            parentItem.classList.remove('open');
            if (submenu) submenu.style.display = 'none';
        } else {
            parentItem.classList.add('open');
            if (submenu) submenu.style.display = 'block';
        }
    });
});

// ── Auto Expand Submenu on Active Route ──
document.querySelectorAll('.submenu-link.active').forEach(activeLink => {
    // Traverse up to find parent submenu-list
    const submenuList = activeLink.closest('.submenu-list');
    if (submenuList) {
        submenuList.style.display = 'block';
        const parentMenu = submenuList.closest('.menu-item');
        if (parentMenu) {
            parentMenu.classList.add('open');
            const parentToggle = parentMenu.querySelector('.submenu-toggle');
            if (parentToggle) {
                parentToggle.classList.add('parent-active');
            }
        }
    }
});

// ── Close sidebar when clicking any menu or submenu link on mobile/tablet ──
document.querySelectorAll('.menu-link:not(.submenu-toggle), .submenu-link').forEach(link => {
    link.addEventListener('click', () => {
        if (!isDesktop() && sidebar && overlay) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            syncToggleBtn();
        }
    });
});


// ── Client-side 10-digit numeric-only validation for mobile inputs ──
document.addEventListener('DOMContentLoaded', function () {
    // Select all inputs that look like mobile fields (either by name, id, or placeholder)
    const mobileSelectors = [
        'input[name="mobile"]',
        'input[name="alternate_mobile"]',
        'input[name="mobile_number"]',
        'input[id="mobile"]',
        'input[id="alternate_mobile"]',
        'input[id="mobile_number"]',
        'input[id="m_mobile"]',
        'input[placeholder*="XXXXX"]',
        'input[placeholder*="mobile"]',
        'input[placeholder*="contact"]'
    ];

    document.querySelectorAll(mobileSelectors.join(', ')).forEach(input => {
        // Enforce numeric only on keypress/input
        input.addEventListener('input', function () {
            // Trim leading/trailing spaces and remove any non-numeric character
            let val = this.value.trim().replace(/[^0-9]/g, '');
            // Limit to exactly 10 digits
            if (val.length > 10) {
                val = val.substring(0, 10);
            }
            this.value = val;
        });

        // Additional blur check to trim and notify user if invalid
        input.addEventListener('blur', function () {
            this.value = this.value.trim();
            if (this.value.length > 0 && this.value.length !== 10) {
                this.classList.add('is-invalid');
                let errDiv = this.parentNode.querySelector('.text-error, .field-error, .invalid-feedback');
                if (!errDiv) {
                    errDiv = document.createElement('div');
                    errDiv.className = 'text-error';
                    errDiv.style.color = '#EF4444';
                    errDiv.style.fontSize = '12px';
                    errDiv.style.marginTop = '4px';
                    this.parentNode.appendChild(errDiv);
                }
                errDiv.textContent = 'Mobile number must be exactly 10 digits.';
            } else if (this.value.length === 10) {
                this.classList.remove('is-invalid');
                const errDiv = this.parentNode.querySelector('.text-error, .field-error, .invalid-feedback');
                if (errDiv) errDiv.remove();
            }
        });
    });
});

// ── Track Print Actions ──
window.addEventListener('beforeprint', () => {
    fetch("{{ route('audit-logs.track') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            action_type: 'Print',
            module_name: document.title ? document.title.replace(' - Delawala Management', '') : 'Admin Panel',
            description: 'Printed page: ' + window.location.href
        })
    }).catch(err => console.error('Error tracking print action:', err));
});
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/validation.js') }}?v={{ time() }}"></script>
</body>
</html>
