<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Delawala Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
    /* ================================================================
       DESIGN TOKENS
    ================================================================ */
    :root {
        --glass-bg:          rgba(255, 255, 255, 0.06);
        --glass-bg-hover:    rgba(255, 255, 255, 0.10);
        --glass-bg-card:     rgba(255, 255, 255, 0.06);
        --glass-border:      rgba(255, 255, 255, 0.18);
        --glass-blur:        blur(20px);
        --glass-blur-lg:     blur(28px);

        --sidebar-bg:        rgba(15, 20, 32, 0.85);
        --sidebar-hover:     rgba(255, 255, 255, 0.08);
        --sidebar-active:    rgba(255, 255, 255, 0.14);
        --sidebar-border:    rgba(255, 255, 255, 0.14);

        --topbar-bg:         rgba(15, 20, 32, 0.85);
        --main-bg:           #111318;
        --card-bg:           rgba(255, 255, 255, 0.06);
        --text-primary:      #FFFFFF;
        --text-secondary:    rgba(255, 255, 255, 0.75);
        --text-muted:        rgba(255, 255, 255, 0.55);
        --border-color:      rgba(255, 255, 255, 0.18);

        --blue:              #60A5FA;
        --green:             #34D399;
        --red:               #FCA5A5;
        --purple:            #C084FC;
        --amber:             #FBBF24;

        --soft-shadow:       0 8px 32px rgba(0, 0, 0, 0.38);
        --card-shadow:       0 15px 40px rgba(0, 0, 0, 0.38);
        --card-hover:        0 18px 48px rgba(0, 0, 0, 0.48), 0 0 24px rgba(255, 255, 255, 0.10);
        --font-primary:      'Inter', 'Poppins', 'Manrope', sans-serif;
        --sidebar-width:     280px;
        --topbar-height:     65px;
        --radius-sm:         8px;
        --radius-md:         12px;
        --radius-lg:         18px;
        --radius-xl:         22px;
        --transition:        all 0.22s cubic-bezier(0.4,0,0.2,1);
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
        background-color: #0A101D;
        background-image: 
            linear-gradient(90deg, rgba(10, 15, 28, 0.45) 0%, rgba(10, 15, 28, 0.25) 40%, rgba(0, 0, 0, 0.08) 100%),
            url("{{ asset('assets/login.png') }}");
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
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
    .logo-title  { font-weight: 700; font-size: 14.5px; color: #FFFFFF; letter-spacing: 0.3px; text-transform: uppercase; }
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
       PREMIUM PAGINATION DESIGN SYSTEM
    ================================================================ */
    .pagination-wrapper, .pagination-wrap {
        margin-top: 24px !important;
        padding-top: 16px !important;
        border-top: 1px solid var(--border-color) !important;
        width: 100% !important;
        display: flex !important;
        justify-content: center !important;
    }

    .pagination-nav-container {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        width: 100% !important;
        flex-wrap: wrap !important;
        gap: 16px !important;
    }

    .pagination-info-text {
        font-size: 13.5px !important;
        color: var(--text-secondary) !important;
        font-weight: 500 !important;
    }

    .pagination-info-text strong {
        color: var(--text-primary) !important;
        font-weight: 700 !important;
    }

    .pagination-buttons {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        flex-wrap: wrap !important;
    }

    .page-item,
    .pagination-wrapper nav a, .pagination-wrapper nav span,
    .pagination-wrap nav a, .pagination-wrap nav span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 36px !important;
        height: 36px !important;
        padding: 0 12px !important;
        border-radius: 8px !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        font-family: var(--font-primary) !important;
        color: var(--text-primary) !important;
        background: #FFFFFF !important;
        border: 1px solid var(--border-color) !important;
        text-decoration: none !important;
        transition: var(--transition) !important;
        cursor: pointer !important;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
    }

    .page-item:hover,
    .pagination-wrapper nav a:hover,
    .pagination-wrap nav a:hover {
        background: #F1F5F9 !important;
        color: #1E40AF !important;
        border-color: #CBD5E1 !important;
        transform: translateY(-1px) !important;
    }

    .page-item.active,
    .pagination-wrapper nav span[aria-current="page"],
    .pagination-wrap nav span[aria-current="page"] {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
        color: #FFFFFF !important;
        border-color: #2563EB !important;
        box-shadow: 0 2px 8px rgba(59,130,246,0.35) !important;
    }

    .page-item.disabled,
    .pagination-wrapper nav span[aria-disabled="true"],
    .pagination-wrap nav span[aria-disabled="true"] {
        opacity: 0.45 !important;
        cursor: not-allowed !important;
        background: #F8FAFC !important;
        color: var(--text-muted) !important;
        border-color: var(--border-color) !important;
        box-shadow: none !important;
        transform: none !important;
    }

    /* Fix un-styled SVG elements in default Laravel pagination */
    .pagination-wrapper svg, .pagination-wrap svg, nav[role="navigation"] svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
        display: inline-block !important;
        vertical-align: middle !important;
        fill: currentColor !important;
        flex-shrink: 0 !important;
    }

    @media (max-width: 640px) {
        .pagination-nav-container {
            justify-content: center !important;
            text-align: center !important;
        }
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
       RESPONSIVE DESIGN SYSTEM OVERRIDES
       Matches Breakpoints: Mobile (320px-767px), Tablet (768px-1024px), Desktop (1025px+)
    ================================================================ */
    @media (max-width: 1024px) {
        :root {
            --sidebar-width: 260px;
        }
        .sidebar {
            left: calc(-1 * var(--sidebar-width)) !important;
            box-shadow: none !important;
            transition: left 0.3s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s ease !important;
        }
        .sidebar.active {
            left: 0 !important;
            box-shadow: 4px 0 32px rgba(0,0,0,0.3) !important;
        }
        .main-content {
            margin-left: 0 !important;
            transition: none !important;
        }
        .sidebar-overlay.active { 
            display: block !important; 
        }
        /* Collapsed logic fallback on tablet view */
        .sidebar-collapsed .sidebar { 
            width: var(--sidebar-width) !important; 
            left: calc(-1 * var(--sidebar-width)) !important; 
        }
        .sidebar-collapsed .sidebar.active { 
            left: 0 !important; 
        }
        .sidebar-collapsed .main-content { 
            margin-left: 0 !important; 
        }
        .sidebar-collapsed .menu-link span,
        .sidebar-collapsed .menu-group-label,
        .sidebar-collapsed .logo-text { 
            opacity: 1 !important; 
            width: auto !important; 
        }

        /* Prevent table squeezed columns, support natural scrolling */
        body .table-container, body .table-wrap, body .table-responsive, body div:has(> table) {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            width: 100% !important;
            margin-bottom: 1rem !important;
            border-radius: var(--radius-md) !important;
            border: 1px solid var(--border-color) !important;
        }
        body table, body .premium-table, body .erp-table, body .sales-table, body .pay-table, body .rent-table, body .r-table {
            min-width: max-content !important;
            width: 100% !important;
        }
        /* Table action row styling - prevent word wrap height explosion */
        body td.actions, body td.action-column, body .action-links, body .table-action-buttons, body .action-buttons, body .btn-actions {
            white-space: nowrap !important;
            flex-wrap: nowrap !important;
        }

        /* Dashboard Grid Layout Adjustments */
        body .dashboard-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        body .summary-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        body .kpi-grid, body .kpi-grid-2 {
            grid-template-columns: repeat(2, 1fr) !important;
        }

        /* Filter bar responsiveness */
        body .filter-bar {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 12px !important;
            align-items: end !important;
            width: 100% !important;
        }
        body .filter-group {
            width: 100% !important;
            margin-bottom: 0 !important;
        }
        body .filter-control, body .search-input {
            width: 100% !important;
            min-width: 0 !important;
        }
        body .btn-search, body .btn-reset {
            width: 100% !important;
            justify-content: center !important;
            align-self: stretch !important;
        }
    }

    @media (max-width: 768px) {
        .topbar { 
            padding: 0 16px !important; 
        }
        .content-body { 
            padding: 12px 12px 24px !important; 
        }
        .user-info { 
            display: none !important; 
        }
        .page-header-title {
            font-size: 14px !important;
            max-width: 160px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .topbar-right {
            gap: 12px !important;
        }
        .user-panel {
            gap: 6px !important;
        }

        /* General Forms stack and spacing */
        body .form-row, body .form-row-2, body .form-row-3, body .form-row-4, body .form-grid, body .grid-cols-2, body .grid-cols-3, body .grid-cols-4, body .form-row-extra {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        body .form-group, body .form-group-row {
            margin-bottom: 16px !important;
            width: 100% !important;
        }
        body .form-control, body select, body input, body textarea {
            width: 100% !important;
            max-width: 100% !important;
            display: block !important;
        }

        /* Touch friendly form actions - primary CTA on top, cancel/back on bottom */
        body .form-actions {
            flex-direction: column-reverse !important;
            align-items: stretch !important;
            gap: 10px !important;
            margin-top: 20px !important;
            padding-top: 16px !important;
        }
        body .form-actions > * {
            width: 100% !important;
            text-align: center !important;
            justify-content: center !important;
        }

        /* Cards & KPI Boxes Spacing */
        body .card-box, body .card, body .crud-box, body .stat-card, body .kpi-card {
            padding: 16px !important;
        }

        /* Mobile specific font adjustments */
        body h1, body .page-header-title { font-size: 15px !important; }
        body h2, body .crud-title h2, body .rpt-title-block h2 { font-size: 18px !important; }
        body h3, body .summary-title { font-size: 13px !important; }
        body { font-size: 13px !important; }

        /* KPI Layout */
        body .kpi-grid, body .kpi-grid-2 {
            grid-template-columns: 1fr !important;
        }

        /* Table header styling for small screens */
        body .crud-header, body .rpt-header {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 16px !important;
            margin-bottom: 20px !important;
        }
        body .crud-title, body .rpt-title-block {
            text-align: center !important;
        }
        body .crud-header .action-buttons, body .crud-header .btn-actions, body .rpt-action-btns {
            justify-content: center !important;
            width: 100% !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        body .crud-header .action-buttons > *, body .crud-header .btn-actions > *, body .rpt-action-btns > * {
            flex: 1 !important;
            min-width: 120px !important;
            text-align: center !important;
            justify-content: center !important;
        }
    }

    @media (max-width: 480px) {
        body .filter-bar {
            grid-template-columns: 1fr !important;
        }
        body .logout-btn span {
            display: none !important;
        }
        body .logout-btn {
            padding: 8px 10px !important;
        }
        body .page-header-title {
            max-width: 100px !important;
        }
        body .crud-header .action-buttons > *, body .crud-header .btn-actions > *, body .rpt-action-btns > * {
            width: 100% !important;
            flex: none !important;
        }
    }

    /* Keep generic touch padding and alignment for action buttons */
    body .btn, body .btn-gold, body .btn-outline, body .btn-view, body .btn-edit, body .btn-delete, body .btn-search {
        min-height: 38px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13px !important;
        padding: 8px 16px !important;
    }

    /* Scale images automatically inside wrappers */
    body img {
        max-width: 100% !important;
        height: auto !important;
    }

    /* Avoid dropdown screen overflow */
    .dropdown-menu {
        max-width: 290px;
        overflow-x: hidden;
        text-overflow: ellipsis;
    }

    /* ================================================================
       PREMIUM DARK GLASS & PURE WHITE TYPOGRAPHY
    ================================================================ */
    
    /* Body & Base */
    body {
        background-color: #0A101D !important;
        background-image: 
            linear-gradient(90deg, rgba(10, 15, 28, 0.45) 0%, rgba(10, 15, 28, 0.25) 40%, rgba(0, 0, 0, 0.08) 100%),
            url("{{ asset('assets/login.png') }}") !important;
        background-size: cover !important;
        background-position: center !important;
        background-attachment: fixed !important;
        background-repeat: no-repeat !important;
        color: #FFFFFF !important;
        font-family: 'Inter', 'Poppins', 'Manrope', sans-serif !important;
    }
    
    /* Glass Cards & Containers */
    .card-box, .card, .modal-box, .modal-content, .crud-box, .stat-card, .kpi-card, .sum-card, .section-card, .form-card, .summary-card, .rpt-box, .dash-welcome, .rpt-card {
        background: rgba(15, 20, 32, 0.65) !important;
        background-color: rgba(15, 20, 32, 0.65) !important;
        backdrop-filter: blur(28px) !important;
        -webkit-backdrop-filter: blur(28px) !important;
        border: 1px solid rgba(255, 255, 255, 0.20) !important;
        border-radius: 18px !important;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.38) !important;
        color: #FFFFFF !important;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease !important;

        --text-primary:  #FFFFFF;
        --text-secondary:rgba(255, 255, 255, 0.75);
        --text-muted:    rgba(255, 255, 255, 0.55);
        --border-color:  rgba(255, 255, 255, 0.20);
    }
    .card-box:hover, .card:hover, .crud-box:hover, .stat-card:hover, .kpi-card:hover, .sum-card:hover, .section-card:hover, .form-card:hover, .summary-card:hover, .dash-welcome:hover, .rpt-card:hover {
        border-color: rgba(255, 255, 255, 0.40) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 18px 48px rgba(0, 0, 0, 0.48), 0 0 24px rgba(255, 255, 255, 0.12) !important;
    }

    /* ULTRA-HIGH CONTRAST & PURE WHITE BOLD TYPOGRAPHY OVERRIDES */
    .kpi-label, .card-label, .stat-label {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 11px !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        opacity: 1 !important;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6) !important;
    }
    .kpi-value, .card-value, .stat-value {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 24px !important;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6) !important;
    }
    .kpi-badge, .card-subtext, .stat-subtext, .bk-blue, .bk-green, .bk-red, .bk-teal, .bk-rose, .bk-indigo, .bk-purple, .bk-amber, .bk-orange, .bk-sky {
        color: #FFFFFF !important;
        font-weight: 700 !important;
        font-size: 12px !important;
        opacity: 0.95 !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5) !important;
    }
    .kpi-section-header h3, .summary-section-header h3, .section-title, .rpt-section-title {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 14px !important;
        letter-spacing: 1.6px !important;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.7) !important;
    }

    /* Reports Hub Specific Overrides */
    .rpt-hub-header h2 { font-size: 26px !important; font-weight: 800 !important; color: #FFFFFF !important; margin-bottom: 6px !important; text-shadow: 0 2px 14px rgba(0, 0, 0, 0.7) !important; }
    .rpt-hub-header h2 i { color: #60A5FA !important; }
    .rpt-hub-header p { font-size: 14.5px !important; color: rgba(255, 255, 255, 0.85) !important; text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5) !important; }
    .rpt-section-title { font-size: 14px !important; font-weight: 800 !important; color: #FFFFFF !important; text-transform: uppercase !important; letter-spacing: 1.6px !important; margin: 36px 0 18px !important; display: flex !important; align-items: center !important; gap: 10px !important; text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6) !important; }
    .rpt-section-title i { color: #60A5FA !important; font-size: 16px !important; }
    .rpt-card-info h3 { font-size: 16px !important; font-weight: 800 !important; color: #FFFFFF !important; margin-bottom: 6px !important; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4) !important; }
    .rpt-card-info p { font-size: 13px !important; color: rgba(255, 255, 255, 0.80) !important; line-height: 1.55 !important; }
    .rpt-icon, .rpt-icon.blue, .rpt-icon.green, .rpt-icon.amber, .rpt-icon.purple, .rpt-icon.red, .rpt-icon.sky, .rpt-icon.teal, .rpt-icon.orange {
        background: rgba(59, 130, 246, 0.18) !important;
        color: #60A5FA !important;
        border: 1.5px solid rgba(59, 130, 246, 0.40) !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25) !important;
    }
    .rpt-open-btn {
        background: rgba(255, 255, 255, 0.14) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        color: #FFFFFF !important;
        backdrop-filter: blur(8px) !important;
    }
    .rpt-open-btn:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        border-color: rgba(255, 255, 255, 0.60) !important;
        color: #FFFFFF !important;
    }
    
    /* Glass Sidebar & Navigation */
    .sidebar {
        background: rgba(15, 20, 32, 0.85) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.14) !important;
        box-shadow: 4px 0 32px rgba(0, 0, 0, 0.48) !important;
    }
    .logo-container {
        border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
        background: rgba(255, 255, 255, 0.02) !important;
    }
    .menu-group-label {
        color: rgba(255, 255, 255, 0.65) !important;
        opacity: 0.95 !important;
        font-weight: 800 !important;
        letter-spacing: 1.8px !important;
    }
    .menu-link {
        color: #FFFFFF !important;
        border: 1px solid transparent !important;
        border-radius: var(--radius-md) !important;
        transition: var(--transition) !important;
    }
    .menu-link i {
        color: #FFFFFF !important;
    }
    .menu-link:hover {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.20) !important;
    }
    .menu-link:hover i {
        color: #FFFFFF !important;
    }
    .menu-link.active, .menu-link.parent-active {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.14) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.20) !important;
        font-weight: 600 !important;
    }
    .menu-link.active i, .menu-link.parent-active i {
        color: #FFFFFF !important;
    }
    .submenu-link {
        color: rgba(255, 255, 255, 0.80) !important;
        border: 1px solid transparent !important;
        border-radius: var(--radius-sm) !important;
    }
    .submenu-link i {
        color: rgba(255, 255, 255, 0.70) !important;
    }
    .submenu-link:hover {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.20) !important;
    }
    .submenu-link:hover i {
        color: #FFFFFF !important;
    }
    .submenu-link.active {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.14) !important;
        border: 1px solid rgba(255, 255, 255, 0.30) !important;
        box-shadow: 0 2px 10px rgba(255, 255, 255, 0.15) inset !important;
    }
    .submenu-link.active i {
        color: #FFFFFF !important;
    }

    /* Glass Topbar */
    .topbar {
        background: rgba(15, 20, 32, 0.85) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.14) !important;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35) !important;
    }
    .sidebar-toggle-btn {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
    }
    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        border-color: rgba(255, 255, 255, 0.35) !important;
        box-shadow: 0 0 12px rgba(255, 255, 255, 0.2) !important;
    }
    .page-header-title { color: #FFFFFF !important; font-weight: 700 !important; }
    .user-avatar {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.35) !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35) !important;
        color: #FFFFFF !important;
        font-weight: 800 !important;
    }
    .user-name { color: #FFFFFF !important; font-weight: 600 !important; }
    .user-role { color: rgba(255, 255, 255, 0.65) !important; }
    .logout-btn {
        background: rgba(239, 68, 68, 0.12) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(239, 68, 68, 0.25) !important;
        backdrop-filter: blur(8px) !important;
    }
    .logout-btn:hover {
        background: rgba(239, 68, 68, 0.25) !important;
        color: #FFFFFF !important;
        border-color: rgba(239, 68, 68, 0.45) !important;
        box-shadow: 0 4px 16px rgba(239, 68, 68, 0.35) !important;
    }

    /* Glass Tables */
    .table-container, .table-wrap, .table-responsive, div:has(> table) {
        background: rgba(255, 255, 255, 0.04) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        border-radius: 16px !important;
    }
    .premium-table th, .erp-table th, table th {
        background: rgba(255, 255, 255, 0.06) !important;
        color: #FFFFFF !important;
        font-size: 11px !important;
        letter-spacing: 1.2px !important;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.16) !important;
        font-weight: 700 !important;
        padding: 13px 16px !important;
        text-transform: uppercase !important;
    }
    .premium-table td, .erp-table td, table td {
        color: #FFFFFF !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    }
    .premium-table tbody tr:hover, .erp-table tbody tr:hover, table tbody tr:hover {
        background: rgba(255, 255, 255, 0.08) !important;
    }

    /* Glass Forms & Inputs */
    .form-control, .search-input, select.form-control, input.form-control, select, input, textarea, .filter-control {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: 1.5px solid rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border-radius: 10px !important;
    }
    .form-control::placeholder, .search-input::placeholder, input::placeholder, textarea::placeholder {
        color: rgba(255, 255, 255, 0.38) !important;
    }
    .form-control:focus, .search-input:focus, select:focus, input:focus, textarea:focus, .filter-control:focus {
        border-color: rgba(255, 255, 255, 0.60) !important;
        background: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.18) !important;
        outline: none !important;
        color: #FFFFFF !important;
    }
    select option {
        background: #151820 !important;
        color: #FFFFFF !important;
    }
    .form-label, label {
        color: #FFFFFF !important;
        font-weight: 600 !important;
    }

    /* Glass Badges */
    .badge {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
    }
    .badge-active, .badge-status-active, .badge-success, .ds-badge.success {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #34D399 !important;
        border: 1px solid rgba(16, 185, 129, 0.35) !important;
    }
    .badge-inactive, .badge-status-inactive, .badge-danger, .ds-badge.danger {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #FCA5A5 !important;
        border: 1px solid rgba(239, 68, 68, 0.35) !important;
    }
    .ds-badge.warning {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #FBBF24 !important;
        border: 1px solid rgba(245, 158, 11, 0.35) !important;
    }
    .ds-badge.info {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #60A5FA !important;
        border: 1px solid rgba(59, 130, 246, 0.35) !important;
    }

    /* Glass Buttons System */
    .btn-gold, .btn-primary, .btn-search, .dqa-btn {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        border: 1px solid rgba(255, 255, 255, 0.30) !important;
        border-radius: 10px !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
        transition: var(--transition) !important;
        cursor: pointer !important;
        backdrop-filter: blur(8px) !important;
    }
    .btn-gold:hover, .btn-primary:hover, .btn-search:hover, .dqa-btn:hover {
        background: rgba(255, 255, 255, 0.22) !important;
        border-color: rgba(255, 255, 255, 0.50) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.35) !important;
        color: #FFFFFF !important;
    }
    .btn-outline, .btn-secondary, .btn-reset {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.22) !important;
        color: #FFFFFF !important;
        backdrop-filter: blur(8px) !important;
        border-radius: 10px !important;
        transition: var(--transition) !important;
        cursor: pointer !important;
    }
    .btn-outline:hover, .btn-secondary:hover, .btn-reset:hover {
        background: rgba(255, 255, 255, 0.14) !important;
        border-color: rgba(255, 255, 255, 0.40) !important;
        color: #FFFFFF !important;
        transform: translateY(-1px) !important;
    }
    .btn-view, a.btn-view, button.btn-view {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 9px !important;
        backdrop-filter: blur(6px) !important;
    }
    .btn-view:hover {
        background: rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.30) !important;
    }
    .btn-edit, a.btn-edit, button.btn-edit {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 9px !important;
        backdrop-filter: blur(6px) !important;
    }
    .btn-edit:hover {
        background: rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.30) !important;
    }
    .btn-delete, a.btn-delete, button.btn-delete {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #FCA5A5 !important;
        border: 1px solid rgba(239, 68, 68, 0.30) !important;
        border-radius: 9px !important;
        backdrop-filter: blur(6px) !important;
    }
    .btn-delete:hover {
        background: #DC2626 !important;
        color: #FFFFFF !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 16px rgba(220, 38, 38, 0.35) !important;
    }

    /* Blue Accent Statistics & Photo Cards System */
    .kpi-icon-box, .ik-blue, .ik-green, .ik-red, .ik-purple, .ik-teal, .ik-amber, .ik-orange, .ik-sky, .ik-indigo, .ik-rose, .section-title-icon, .task-icon-wrap {
        background: rgba(59, 130, 246, 0.16) !important;
        color: #60A5FA !important;
        border: 1px solid rgba(59, 130, 246, 0.35) !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.20) !important;
    }
    .kpi-icon-box { display: flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 12px; font-size: 16px; flex-shrink: 0; }

    .kpi-badge, .bk-blue, .bk-green, .bk-red, .bk-purple, .bk-teal, .bk-amber, .bk-orange, .bk-sky, .bk-indigo, .bk-rose {
        color: #60A5FA !important;
    }

    /* Blue Decorative Radial Glows */
    .deco-blue, .deco-green, .deco-red, .deco-purple, .deco-teal, .deco-amber, .deco-orange, .deco-sky, .deco-indigo, .kpi-deco {
        background: radial-gradient(circle, rgba(59, 130, 246, 0.30) 0%, transparent 70%) !important;
    }

    .ch-blue, .ch-green, .ch-amber, .ch-orange, .ch-red, .ch-purple, .ch-sky {
        background: rgba(59, 130, 246, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.35) !important;
        color: #60A5FA !important;
    }

    /* Glass Pagination */
    .page-item,
    .pagination-wrapper nav a, .pagination-wrapper nav span,
    .pagination-wrap nav a, .pagination-wrap nav span {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.14) !important;
        color: #FFFFFF !important;
        backdrop-filter: blur(8px) !important;
    }
    .page-item:hover,
    .pagination-wrapper nav a:hover,
    .pagination-wrap nav a:hover {
        background: rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        border-color: rgba(255, 255, 255, 0.40) !important;
        transform: translateY(-1px) !important;
    }
    .page-item.active,
    .pagination-wrapper nav span[aria-current="page"],
    .pagination-wrap nav span[aria-current="page"] {
        background: rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        border-color: rgba(255, 255, 255, 0.50) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35) !important;
    }

    /* Select2 & Dropdown Glass Overrides */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 10px !important;
        color: #FFFFFF !important;
        min-height: 42px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #FFFFFF !important;
        line-height: 40px !important;
    }
    .select2-dropdown {
        background: rgba(17, 19, 24, 0.95) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 14px !important;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5) !important;
        color: #FFFFFF !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: rgba(255, 255, 255, 0.18) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
    }
    .select2-results__option {
        color: rgba(255, 255, 255, 0.90) !important;
    }
    .dropdown-menu {
        background: rgba(17, 19, 24, 0.95) !important;
        backdrop-filter: blur(24px) !important;
        -webkit-backdrop-filter: blur(24px) !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        border-radius: 14px !important;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.5) !important;
        color: #FFFFFF !important;
    }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<!-- Ambient Glow Nodes Background -->
<div class="ambient-glow-wrapper">
    <div class="ambient-glow-orb ambient-glow-orb-1"></div>
    <div class="ambient-glow-orb ambient-glow-orb-2"></div>
    <div class="ambient-glow-orb ambient-glow-orb-3"></div>
</div>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ================================================================
     SIDEBAR
================================================================ -->
<div class="sidebar" id="sidebar">
    <div class="logo-container">
        @if(file_exists(public_path('assets/logos/logo 1.png')))
            <img src="{{ asset('assets/logos/logo 1.png') }}" alt="Delawala Properties" class="logo-img">
        @elseif(file_exists(public_path('images/logo.png')))
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

        {{-- Firm Management --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="Firm Management">
                <i class="fa-solid fa-building"></i><span>Firm Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if(!session('login_type') === 'firm' || session('login_type') !== 'firm')
                <li class="submenu-item">
                    <a href="{{ route('firm-master.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'firm-master.') ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i><span>Firms</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- 1. Property Management --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="1. Property Management">
                <i class="fa-solid fa-building"></i><span>1. Property Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                @if($authUser->hasPermission('property_view'))
                <li class="submenu-item">
                    <a href="{{ route('property-masters.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'property-masters.') ? 'active' : '' }}">
                        <i class="fa-solid fa-building"></i><span>Property Master</span>
                    </a>
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

        {{-- 2. Project Management --}}
        @if($authUser->hasPermission('project_view'))
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="2. Project Management">
                <i class="fa-solid fa-city"></i><span>2. Project Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                <li class="submenu-item">
                    <a href="{{ route('projects.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'projects.') ? 'active' : '' }}">
                        <i class="fa-solid fa-city"></i><span>Projects</span>
                    </a>
                </li>

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
                        @if($authUser->hasPermission('purchase_order_view'))
                        <li class="submenu-item">
                            <a href="{{ route('purchase-orders.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'purchase-orders.') ? 'active' : '' }}">
                                <i class="fa-solid fa-file-invoice"></i><span>Purchase Order</span>
                            </a>
                        </li>
                        @endif
                        <li class="submenu-item">
                            <a href="{{ route('stock-report.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'stock-report.') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-bar"></i><span>Current Stock Report</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </li>
        @endif

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
                    <a href="{{ route('rentals.index') }}" class="submenu-link {{ (str_starts_with($currentRoute ?? '', 'rentals.') && !request()->has('collect') && !str_starts_with($currentRoute ?? '', 'rental-payments.')) ? 'active' : '' }}">
                        <i class="fa-solid fa-key"></i><span>Rent Agreement</span>
                    </a>
                </li>
                @endif
                @if($authUser->hasPermission('rental_view'))
                <li class="submenu-item">
                    <a href="{{ route('rentals.index', ['collect' => 1]) }}" class="submenu-link {{ (request()->has('collect') || str_starts_with($currentRoute ?? '', 'rental-payments.')) ? 'active' : '' }}">
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
                    <a href="{{ route('emi-schedules.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'emi-schedules.') ? 'active' : '' }}">
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

const isDesktop = () => window.innerWidth > 1024;

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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2-multi').select2({
            placeholder: "Search and select firm(s)...",
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
<script src="{{ asset('js/validation.js') }}?v={{ time() }}"></script>
</body>
</html>
