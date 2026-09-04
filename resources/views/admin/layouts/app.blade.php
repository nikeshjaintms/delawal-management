<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Delawala Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* ================================================================
       DESIGN TOKENS
    /* ================================================================
       DESIGN TOKENS
    ================================================================ */
    :root {
        --glass-bg:          rgba(20, 27, 41, 0.75);
        --glass-bg-hover:    rgba(255, 255, 255, 0.10);
        --glass-bg-card:     rgba(20, 27, 41, 0.65);
        --glass-border:      rgba(255, 255, 255, 0.12);
        --glass-blur:        blur(20px);
        --glass-blur-lg:     blur(28px);

        --sidebar-bg:        rgba(16, 22, 34, 0.88);
        --sidebar-hover:     rgba(255, 255, 255, 0.06);
        --sidebar-active:    rgba(255, 255, 255, 0.12);
        --sidebar-border:    rgba(255, 255, 255, 0.08);

        --topbar-bg:         rgba(16, 22, 34, 0.85);
        --main-bg:           #0B0E17;
        --card-bg:           rgba(20, 27, 41, 0.65);
        --text-primary:      #FFFFFF;
        --text-secondary:    #E2E8F0;
        --text-muted:        #94A3B8;
        --border-color:      rgba(255, 255, 255, 0.10);

        --blue:              #3B82F6;
        --green:             #10B981;
        --red:               #EF4444;
        --purple:            #8B5CF6;
        --amber:             #F59E0B;

        --soft-shadow:       0 8px 30px rgba(0, 0, 0, 0.25);
        --card-shadow:       0 10px 30px rgba(0, 0, 0, 0.30);
        --card-hover:        0 18px 42px rgba(0, 0, 0, 0.45);
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
        background-color: #0B0E17 !important;
        background: #0B0E17 url('{{ asset('images/luxury-bg.jpg') }}?v=11') no-repeat center center fixed !important;
        background-size: cover !important;
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
    ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.35); }

    /* ================================================================
       SIDEBAR
    ================================================================ */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
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
        box-shadow: 4px 0 24px rgba(0,0,0,0.45);
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
        scrollbar-color: rgba(255, 255, 255, 0.15) transparent;
    }
    .sidebar-menu::-webkit-scrollbar { width: 4px; }
    .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
    .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 4px; }
    .sidebar-menu::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.25); }

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
        color: #94A3B8;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        border-radius: var(--radius-md);
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
        background: #3B82F6;
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
        color: #94A3B8;
        transition: var(--transition);
        flex-shrink: 0;
        margin-left: 0; /* Keep equal left margin for every icon */
    }
    .menu-link:hover {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.06);
        transform: translateX(3px);
        border-left: 2px solid rgba(255, 255, 255, 0.3);
    }
    .menu-link:hover i {
        color: #60A5FA;
        transform: scale(1.08);
    }
    .menu-link.active, .menu-link.parent-active {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.08);
        font-weight: 700;
        border: none;
        border-left: 3px solid #3B82F6;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.20);
    }
    .menu-link.active::before, .menu-link.parent-active::before {
        opacity: 1;
        transform: scaleY(1);
    }
    .menu-link.active i, .menu-link.parent-active i {
        color: #FFFFFF;
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
        color: #94A3B8;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 500;
        border-radius: var(--radius-md);
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
        color: #94A3B8;
        transition: var(--transition);
        flex-shrink: 0;
    }
    .submenu-link:hover {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.05);
        transform: translateX(3px);
        border-left: 2px solid rgba(255, 255, 255, 0.25);
    }
    .submenu-link:hover i {
        color: #60A5FA;
    }
    .submenu-link.active {
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.08);
        font-weight: 700;
        border: none;
        border-left: 3px solid #3B82F6;
    }
    .submenu-link.active i {
        color: #FFFFFF;
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
        color: #FFFFFF;
        background: rgba(255, 255, 255, 0.06);
        font-weight: 600;
    }
    .menu-link.parent-active i {
        color: #FFFFFF;
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
        background: transparent !important;
        background-color: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    /* ================================================================
       TOPBAR
    ================================================================ */                                                                                             
    .topbar {
        height: var(--topbar-height);
        min-height: var(--topbar-height);
        background: rgba(12, 17, 29, 0.80) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
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
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #FFFFFF;
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
        background: rgba(255, 255, 255, 0.15);
        color: #FFFFFF;
        border-color: rgba(255, 255, 255, 0.25);
    }
    .sidebar-toggle-btn.is-collapsed {
        background: rgba(59,130,246,0.25);
        color: #FFFFFF;
        border-color: rgba(59,130,246,0.4);
    }
    .sidebar-toggle-btn.is-collapsed:hover {
        background: rgba(59,130,246,0.35);
        color: #FFFFFF;
        border-color: rgba(59,130,246,0.5);
    }
    .page-header-title { font-size: 17px; color: #FFFFFF; font-weight: 700; letter-spacing: 0.2px; }
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
    .user-name { font-size: 13px; font-weight: 700; color: #FFFFFF; }
    .user-role { font-size: 10.5px; color: #94A3B8; }
    .logout-form { display: inline-block; }
    .logout-btn {
        background: rgba(220, 38, 38, 0.20) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(220, 38, 38, 0.40) !important;
        padding: 8px 16px;
        font-size: 12.5px;
        font-weight: 700;
        font-family: var(--font-primary);
        cursor: pointer;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
    }
    .logout-btn:hover { background: rgba(220, 38, 38, 0.85) !important; color: #FFFFFF !important; border-color: #DC2626 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5) !important; }

    /* ================================================================
       CONTENT BODY
    ================================================================ */
    .content-body { padding: 28px 40px 60px !important; flex: 1; }

    /* ================================================================
       BREADCRUMB NAV — Global high contrast luxury glass styling
    ================================================================ */
    .breadcrumb-nav {
        display: inline-flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        padding: 9px 18px !important;
        background: rgba(16, 22, 34, 0.85) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 12px !important;
        font-size: 13px !important;
        color: #CBD5E1 !important;
        font-weight: 600 !important;
        margin-bottom: 22px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.40) !important;
    }
    .breadcrumb-nav a {
        color: #60A5FA !important;
        text-decoration: none !important;
        font-weight: 700 !important;
        transition: all 0.18s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 5px !important;
    }
    .breadcrumb-nav a:hover {
        color: #93C5FD !important;
        text-decoration: underline !important;
        transform: translateY(-1px);
    }
    .breadcrumb-nav .separator {
        font-size: 10px !important;
        color: #94A3B8 !important;
        opacity: 0.85 !important;
        margin: 0 2px !important;
    }
    .breadcrumb-nav .active {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        letter-spacing: 0.3px !important;
    }


    /* ================================================================
       GLOBAL CARD OVERRIDE — lift all card-box styles
    ================================================================ */
    .card-box, .card, .stat-card, .sum-card, .section-card {
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: var(--radius-lg) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35) !important;
        color: #FFFFFF !important;
        transition: box-shadow 0.22s ease, transform 0.22s ease, border-color 0.22s ease !important;
    }
    .card-box:hover, .card:hover, .stat-card:hover, .sum-card:hover, .section-card:hover {
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.45) !important;
        border-color: rgba(255, 255, 255, 0.20) !important;
    }

    /* ================================================================
       UNIFIED MASTER BUTTON STYLING (LOGOUT-STYLE HOVER & ANIMATIONS)
    ================================================================ */
    .btn, a.btn, button.btn, .dqa-btn, .btn-view-all, .btn-primary-custom, .btn-pc, .download-btn, .docs-add-btn, .btn-view, .btn-edit, .btn-delete, .sidebar-toggle-btn, input[type="submit"] {
        transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1),
                    background-color 0.22s ease,
                    border-color 0.22s ease,
                    box-shadow 0.22s ease,
                    color 0.22s ease !important;
        cursor: pointer !important;
    }

    .btn i, a.btn i, button.btn i, .dqa-btn i, .btn-view-all i, .btn-primary-custom i, .btn-pc i, .download-btn i, .docs-add-btn i, .btn-view i, .btn-edit i, .btn-delete i, .logout-btn i {
        transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        display: inline-block;
    }

    /* General Button Hover Effects */
    .btn:hover, a.btn:hover, button.btn:hover, .btn-primary-custom:hover, .btn-pc:hover, .download-btn:hover, .docs-add-btn:hover {
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.40) !important;
    }

    .btn:hover i, a.btn:hover i, button.btn:hover i, .btn-primary-custom:hover i, .btn-pc:hover i, .download-btn:hover i, .docs-add-btn:hover i {
        transform: scale(1.15) !important;
    }

    /* Table Action Buttons (View / Edit / Delete) */
    .btn-view, a.btn-view, button.btn-view {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: rgba(59, 130, 246, 0.15); color: #60A5FA !important;
        border: 1px solid rgba(96, 165, 250, 0.30); border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-view:hover, a.btn-view:hover, button.btn-view:hover {
        background: #2563EB !important; color: #FFFFFF !important;
        border-color: #2563EB !important; transform: translateY(-2px) scale(1.03) !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.40) !important;
    }
    .btn-view:hover i { transform: scale(1.15) !important; }

    .btn-edit, a.btn-edit, button.btn-edit {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: rgba(245, 158, 11, 0.15); color: #FBBF24 !important;
        border: 1px solid rgba(245, 158, 11, 0.30); border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-edit:hover, a.btn-edit:hover, button.btn-edit:hover {
        background: #D97706 !important; color: #FFFFFF !important;
        border-color: #D97706 !important; transform: translateY(-2px) scale(1.03) !important;
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.40) !important;
    }
    .btn-edit:hover i { transform: scale(1.15) !important; }

    .btn-delete, a.btn-delete, button.btn-delete {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 8px 14px; min-height: 38px;
        background: rgba(239, 68, 68, 0.15); color: #FCA5A5 !important;
        border: 1px solid rgba(239, 68, 68, 0.30); border-radius: 9px;
        font-size: 13px; font-weight: 600; line-height: 1;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer;
        font-family: var(--font-primary);
    }
    .btn-delete:hover, a.btn-delete:hover, button.btn-delete:hover {
        background: #DC2626 !important; color: #FFFFFF !important;
        border-color: #DC2626 !important; transform: translateY(-2px) scale(1.03) !important;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.40) !important;
    }
    .btn-delete:hover i { transform: scale(1.15) rotate(-5deg) !important; }

    /* Quick Action Buttons (.dqa-btn) */
    .dqa-btn, .dash-quick-actions a {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
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
    .dqa-btn i, .dash-quick-actions a i {
        color: #FFFFFF !important;
        font-size: 13px !important;
        transition: transform 0.2s ease !important;
    }
    .dqa-btn:hover, .dash-quick-actions a:hover {
        transform: translateY(-2px) scale(1.03) !important;
        color: #FFFFFF !important;
    }
    .dqa-btn:hover i, .dash-quick-actions a:hover i {
        color: #FFFFFF !important;
        transform: scale(1.15) !important;
    }

    /* Hover Colors for each distinct quick action */
    .dqa-blue:hover, a.dqa-blue:hover {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(37, 99, 235, 0.5) !important;
    }
    .dqa-green:hover, a.dqa-green:hover {
        background: #10B981 !important;
        border-color: #34D399 !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(16, 185, 129, 0.5) !important;
    }
    .dqa-purple:hover, a.dqa-purple:hover {
        background: #8B5CF6 !important;
        border-color: #A78BFA !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(139, 92, 246, 0.5) !important;
    }
    .dqa-red:hover, a.dqa-red:hover {
        background: #EF4444 !important;
        border-color: #F87171 !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(239, 68, 68, 0.5) !important;
    }
    .dqa-amber:hover, a.dqa-amber:hover {
        background: #D97706 !important;
        border-color: #FBBF24 !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(217, 119, 6, 0.5) !important;
    }
    .dqa-teal:hover, a.dqa-teal:hover {
        background: #0D9488 !important;
        border-color: #2DD4BF !important;
        color: #FFFFFF !important;
        box-shadow: 0 6px 22px rgba(13, 148, 136, 0.5) !important;
    }

    /* Global Export & Action Buttons - Direct Solid Vibrant Colors */
    .btn-export-csv, .btn-export-excel, a.btn-export-csv, a.btn-export-excel {
        display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 7px !important;
        padding: 9px 18px !important; background: linear-gradient(135deg, #10B981, #059669) !important;
        color: #FFFFFF !important; font-size: 13.5px !important; font-weight: 700 !important; border: 1px solid #10B981 !important;
        border-radius: 10px !important; text-decoration: none !important; transition: all .25s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important; white-space: nowrap !important;
    }
    .btn-export-csv:hover, .btn-export-excel:hover, a.btn-export-csv:hover, a.btn-export-excel:hover {
        background: linear-gradient(135deg, #059669, #047857) !important; border-color: #059669 !important; color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.02) !important; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.55) !important;
    }
    .btn-export-csv:hover i, .btn-export-excel:hover i { transform: scale(1.12) !important; }

    .btn-export-pdf, a.btn-export-pdf {
        display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 7px !important;
        padding: 9px 18px !important; background: linear-gradient(135deg, #EF4444, #DC2626) !important;
        color: #FFFFFF !important; font-size: 13.5px !important; font-weight: 700 !important; border: 1px solid #EF4444 !important;
        border-radius: 10px !important; text-decoration: none !important; transition: all .25s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important; white-space: nowrap !important;
    }
    .btn-export-pdf:hover, a.btn-export-pdf:hover {
        background: linear-gradient(135deg, #DC2626, #B91C1C) !important; border-color: #DC2626 !important; color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.02) !important; box-shadow: 0 6px 20px rgba(239, 68, 68, 0.55) !important;
    }
    .btn-export-pdf:hover i { transform: scale(1.12) !important; }

    .btn-export-print, a.btn-export-print {
        display: inline-flex !important; align-items: center !important; justify-content: center !important; gap: 7px !important;
        padding: 9px 18px !important; background: linear-gradient(135deg, #6366F1, #4F46E5) !important;
        color: #FFFFFF !important; font-size: 13.5px !important; font-weight: 700 !important; border: 1px solid #6366F1 !important;
        border-radius: 10px !important; text-decoration: none !important; transition: all .25s cubic-bezier(0.4, 0, 0.2, 1) !important; cursor: pointer !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important; white-space: nowrap !important;
    }
    .btn-export-print:hover, a.btn-export-print:hover {
        background: linear-gradient(135deg, #4F46E5, #4338CA) !important; border-color: #4F46E5 !important; color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.02) !important; box-shadow: 0 6px 20px rgba(99, 102, 241, 0.55) !important;
    }
    .btn-export-print:hover i { transform: scale(1.12) !important; }

    /* View All Button (.btn-view-all) */
    .btn-view-all {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        border-radius: 8px !important;
        padding: 5px 12px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }
    .btn-view-all:hover {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.03) !important;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.40) !important;
    }
    .btn-view-all:hover i {
        transform: translateX(3px) scale(1.15) !important;
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
        border: 1px solid rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        border-radius: var(--radius-sm) !important;
        transition: var(--transition) !important;
    }
    .btn-outline:hover {
        border-color: var(--blue) !important;
        color: #FFFFFF !important;
        background: rgba(59, 130, 246, 0.20) !important;
        transform: translateY(-1px) !important;
    }

    /* ================================================================
       GLOBAL FORM OVERRIDES
    ================================================================ */
    .form-control, input, select, textarea, .search-input, .filter-control {
        background: rgba(16, 22, 34, 0.65) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        border-radius: var(--radius-sm) !important;
        font-family: var(--font-primary) !important;
        transition: border-color 0.18s ease, box-shadow 0.18s ease !important;
        font-size: 13.5px !important;
    }
    .form-control:focus, input:focus, select:focus, textarea:focus, .search-input:focus, .filter-control:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
        outline: none !important;
        background: rgba(16, 22, 34, 0.85) !important;
    }
    select option {
        background: #101622 !important;
        color: #FFFFFF !important;
    }
    .form-control.is-invalid, .is-invalid {
        border: 1px solid #EF4444 !important;
    }
    .form-control.is-invalid:focus, .is-invalid:focus {
        border-color: #EF4444 !important;
        box-shadow: 0 0 0 0.15rem rgba(239, 68, 68, 0.25) !important;
    }
    .text-error, .dw-invalid-feedback {
        color: #F87171 !important;
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

    /* ================================================================
       GLOBAL TABLE OVERRIDES
    ================================================================ */
    .premium-table, .erp-table, table {
        color: #F8FAFC !important;
    }
    .premium-table th, .erp-table th, table th {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #94A3B8 !important;
        font-size: 11px !important;
        letter-spacing: 0.8px !important;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
        font-weight: 700 !important;
        padding: 12px 16px !important;
        text-transform: uppercase !important;
    }
    .premium-table td, .erp-table td, table td {
        padding: 14px 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        vertical-align: middle !important;
        color: #E2E8F0 !important;
        transition: background 0.15s ease !important;
    }
    .premium-table tbody tr:hover, .erp-table tbody tr:hover, table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }

    /* ================================================================
       BADGE FIXES
    ================================================================ */
    .badge-active   { background: rgba(16,185,129,0.15) !important; color: #34D399 !important; border: 1px solid rgba(16,185,129,0.3) !important; }
    .badge-inactive { background: rgba(239,68,68,0.15)  !important; color: #F87171 !important; border: 1px solid rgba(239,68,68,0.3) !important; }

    /* ================================================================
       ALERT SUCCESS
    ================================================================ */
    .alert-success {
        background: rgba(16,185,129,0.15) !important;
        border: 1px solid rgba(16,185,129,0.3) !important;
        color: #34D399 !important;
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
        min-width: 38px !important;
        height: 38px !important;
        padding: 0 12px !important;
        border-radius: 10px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        font-family: var(--font-primary) !important;
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.20) !important;
    }

    .page-item:hover,
    .pagination-wrapper nav a:hover,
    .pagination-wrap nav a:hover {
        background: rgba(59, 130, 246, 0.25) !important;
        color: #FFFFFF !important;
        border-color: #3B82F6 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35) !important;
    }

    .page-item.active,
    .pagination-wrapper nav span[aria-current="page"],
    .pagination-wrap nav span[aria-current="page"] {
        background: #2563EB !important;
        color: #FFFFFF !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45) !important;
        font-weight: 800 !important;
    }

    .page-item.disabled,
    .pagination-wrapper nav span[aria-disabled="true"],
    .pagination-wrap nav span[aria-disabled="true"] {
        opacity: 0.40 !important;
        cursor: not-allowed !important;
        background: rgba(255, 255, 255, 0.03) !important;
        color: rgba(255, 255, 255, 0.35) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
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

    /* Sidebar Footer Card (Weather & Status) */
    .sidebar-footer-card {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 12px 14px 16px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        backdrop-filter: blur(10px);
        transition: all 0.25s ease;
        position: relative;
    }
    .sfc-weather-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: rgba(245, 158, 11, 0.15);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        color: #F59E0B;
        font-size: 16px;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }
    .sfc-weather-info {
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
        min-width: 0;
        transition: opacity 0.2s ease, width 0.25s ease;
    }
    .sfc-temp {
        color: #FFFFFF !important;
        font-size: 13.5px;
        font-weight: 700 !important;
        line-height: 1.2;
    }
    .sfc-cond {
        color: #94A3B8 !important;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .sfc-status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
        flex-shrink: 0;
        animation: sfcPulse 2s infinite ease-in-out;
    }
    @keyframes sfcPulse {
        0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 6px rgba(16, 185, 129, 0.6); }
        50% { transform: scale(1.2); opacity: 0.75; box-shadow: 0 0 12px rgba(16, 185, 129, 0.9); }
    }

    /* Collapsed Sidebar overrides for Footer Card */
    .sidebar-collapsed .sidebar-footer-card {
        margin: 10px 8px;
        padding: 10px 0;
        justify-content: center;
        gap: 0;
    }
    .sidebar-collapsed .sfc-weather-info {
        display: none !important;
        opacity: 0;
        width: 0;
        pointer-events: none;
    }
    .sidebar-collapsed .sfc-status-dot {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 7px;
        height: 7px;
    }
    .sidebar-collapsed .sidebar-footer-card:hover::after {
        content: "30°C • Mostly Cloudy";
        position: absolute;
        left: 72px;
        top: 50%;
        transform: translateY(-50%);
        background: #1E293B;
        color: #F1F5F9;
        font-size: 12px;
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
       DELAWALA LUXURY DARK GLASSMORPHISM SYSTEM
    ================================================================ */
    
    html, body {
        min-height: 100vh !important;
        background-color: #0B0E17 !important;
        background: #0B0E17 url('{{ asset('images/luxury-bg.jpg') }}?v=11') no-repeat center center fixed !important;
        background-size: cover !important;
        color: #FFFFFF !important;
        font-family: 'Inter', 'Poppins', 'Manrope', sans-serif !important;
    }

    .wrapper, .main-content, .content-body, .content-page, #app, .page-wrapper, .main-wrapper {
        background: transparent !important;
        background-color: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }

    .ambient-glow-wrapper {
        display: none !important;
    }
    
    /* Luxury Dark Translucent Glass Cards Across All Modules */
    .card-box, .card, .modal-box, .modal-content, .crud-box, .stat-card, .kpi-card, .sum-card, .section-card, .form-card, .summary-card, .rpt-box, .dash-welcome, .rpt-card, .table-container, .task-item, .info-card, .detail-card, .info-box, .pl-stat-card, .gst-stat-card, .summary-box, .box, .table-card, .type-btn, .modal-dialog, .filter-bar, .table-toolbar {
        background: rgba(15, 23, 42, 0.55) !important;
        background-color: rgba(15, 23, 42, 0.55) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30), inset 0 1px 0 rgba(255, 255, 255, 0.10) !important;
        color: #FFFFFF !important;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease, background 0.25s ease !important;

        --text-primary:  #FFFFFF;
        --text-secondary:#CBD5E1;
        --text-muted:    #94A3B8;
        --border-color:  rgba(255, 255, 255, 0.12);
    }
    .card-box:hover, .card:hover, .stat-card:hover, .kpi-card:hover, .sum-card:hover, .section-card:hover, .summary-card:hover, .dash-welcome:hover {
        background: rgba(15, 23, 42, 0.68) !important;
        background-color: rgba(15, 23, 42, 0.68) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.18) !important;
    }

    /* Webkit / Chrome / Edge Browser Autofill & Autocomplete Dark Mode Overrides */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus, 
    input:-webkit-autofill:active,
    textarea:-webkit-autofill,
    select:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 1000px #101622 inset !important;
        -webkit-text-fill-color: #FFFFFF !important;
        caret-color: #FFFFFF !important;
        transition: background-color 50000s ease-in-out 0s !important;
    }

    /* Remove ugly white background spin box on Chrome/Edge number inputs */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    input[type=number] {
        -moz-appearance: textfield !important;
    }

    /* Uniform Export & Print Buttons System (Direct Solid Colors) */
    .btn-export-pdf, .btn-pdf, a.btn-export-pdf, a.btn-pdf {
        background: linear-gradient(135deg, #EF4444, #DC2626) !important;
        color: #FFFFFF !important;
        border: 1px solid #EF4444 !important;
        border-radius: 10px !important;
        padding: 9px 18px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
        cursor: pointer !important;
    }
    .btn-export-pdf:hover, .btn-pdf:hover, a.btn-export-pdf:hover, a.btn-pdf:hover {
        background: linear-gradient(135deg, #DC2626, #B91C1C) !important;
        color: #FFFFFF !important;
        border-color: #DC2626 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.55) !important;
    }

    .btn-excel, .btn-export, .btn-export-excel, .btn-export-csv, a.btn-excel, a.btn-export, a.btn-export-excel, a.btn-export-csv {
        background: linear-gradient(135deg, #10B981, #059669) !important;
        color: #FFFFFF !important;
        border: 1px solid #10B981 !important;
        border-radius: 10px !important;
        padding: 9px 18px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
        cursor: pointer !important;
    }
    .btn-excel:hover, .btn-export:hover, .btn-export-excel:hover, .btn-export-csv:hover, a.btn-excel:hover, a.btn-export:hover, a.btn-export-excel:hover, a.btn-export-csv:hover {
        background: linear-gradient(135deg, #059669, #047857) !important;
        color: #FFFFFF !important;
        border-color: #059669 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.55) !important;
    }

    .btn-print, .btn-export-print, a.btn-print, a.btn-export-print {
        background: linear-gradient(135deg, #6366F1, #4F46E5) !important;
        color: #FFFFFF !important;
        border: 1px solid #6366F1 !important;
        border-radius: 10px !important;
        padding: 9px 18px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
        cursor: pointer !important;
    }
    .btn-print:hover, .btn-export-print:hover, a.btn-print:hover, a.btn-export-print:hover {
        background: linear-gradient(135deg, #4F46E5, #4338CA) !important;
        color: #FFFFFF !important;
        border-color: #4F46E5 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.55) !important;
    }

    .btn-print {
        background: #4F46E5 !important;
        color: #FFFFFF !important;
        border: 1px solid #6366F1 !important;
        border-radius: 10px !important;
        padding: 9px 18px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        text-decoration: none !important;
        white-space: nowrap !important;
        transition: all 0.25s ease !important;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.40) !important;
        cursor: pointer !important;
    }
    .btn-print:hover {
        background: #4338CA !important;
        color: #FFFFFF !important;
        border-color: #818CF8 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.60) !important;
    }

    /* Global Table Text & Firm Name Override for Dark Theme */
    .table td, .premium-table td, table td {
        color: #E2E8F0 !important;
    }
    .table td strong, .premium-table td strong, table td strong,
    td strong[style*="color:#0F172A"], td strong[style*="color: #0F172A"] {
        color: #FFFFFF !important;
    }
    .card-box div[style*="color:#0F172A"],
    .card-box div[style*="color: #0F172A"],
    .card-box h2, .card-box h3, .card-box h4,
    .rpt-title-block h2 {
        color: #FFFFFF !important;
    }

    .card-box:hover, .card:hover, .crud-box:hover, .stat-card:hover, .kpi-card:hover, .sum-card:hover, .section-card:hover, .form-card:hover, .summary-card:hover, .dash-welcome:hover, .rpt-card:hover, .task-item:hover, .pl-stat-card:hover, .gst-stat-card:hover {
        background: rgba(20, 27, 41, 0.80) !important;
        background-color: rgba(20, 27, 41, 0.80) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45) !important;
    }

    /* Crisp White & Gold Typography */
    .kpi-label, .card-label, .stat-label {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 11px !important;
        letter-spacing: 0.8px !important;
        text-transform: uppercase !important;
        opacity: 1 !important;
    }
    .kpi-value, .card-value, .stat-value {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 32px !important;
    }
    .dash-welcome-title, .page-header-title, .crud-title h2, .crud-title h3, .crud-title h1, h1, h2, h3, h4, h5, h6 {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.40);
    }
    .dash-welcome-sub, .crud-title p, .crud-header p, .page-header-sub, .page-subtitle, .rpt-title-block p, .crud-title span {
        color: #FFFFFF !important;
        font-weight: 700 !important;
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.40);
    }
    .dash-welcome-tag {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        font-weight: 800 !important;
        letter-spacing: 1.2px !important;
        text-transform: uppercase !important;
    }
    .kpi-badge, .card-subtext, .stat-subtext {
        font-weight: 700 !important;
        font-size: 12px !important;
    }
    .kpi-section-header h3, .summary-section-header h3, .section-title, .rpt-section-title {
        color: #FFFFFF !important;
        font-weight: 800 !important;
        font-size: 14px !important;
        letter-spacing: 1.4px !important;
    }
    .kpi-section-divider {
        background: rgba(255, 255, 255, 0.12) !important;
    }

    /* Luxury Dark Glass Sidebar */
    .sidebar {
        background: rgba(16, 22, 34, 0.88) !important;
        background-color: rgba(16, 22, 34, 0.88) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.45) !important;
    }
    .logo-container {
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        background: transparent !important;
    }
    .menu-group-label {
        color: #64748B !important;
        font-weight: 700 !important;
        letter-spacing: 1.8px !important;
        font-size: 9.5px !important;
    }
    .menu-link {
        color: #E2E8F0 !important;
        border-radius: 10px !important;
        transition: all 0.2s ease !important;
        font-size: 13.5px !important;
        font-weight: 600 !important;
        border: none !important;
    }
    .menu-link i, .menu-link span, .menu-link .submenu-arrow {
        color: #94A3B8 !important;
        transition: all 0.2s ease !important;
    }
    .menu-link:hover {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.06) !important;
        border: none !important;
        border-left: 2px solid rgba(255, 255, 255, 0.3) !important;
        transform: translateX(3px) !important;
        box-shadow: none !important;
    }
    .menu-link:hover i {
        color: #60A5FA !important;
        transform: scale(1.08) !important;
        filter: none !important;
    }
    .menu-link:hover span {
        color: #FFFFFF !important;
    }
    .menu-link:hover .submenu-arrow {
        color: #60A5FA !important;
        transform: translateX(2px) !important;
    }
    .menu-link.active, .menu-link.parent-active {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        border: none !important;
        border-left: 3px solid #3B82F6 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.20) !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
    }
    .menu-link.active i, .menu-link.parent-active i {
        color: #60A5FA !important;
        filter: none !important;
    }
    .menu-link.active span, .menu-link.parent-active span {
        color: #FFFFFF !important;
    }
    .submenu-link {
        color: #94A3B8 !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        border: none !important;
    }
    .submenu-link i, .submenu-link span {
        color: #94A3B8 !important;
        transition: all 0.2s ease !important;
    }
    .submenu-link:hover {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: none !important;
        border-left: 2px solid rgba(255, 255, 255, 0.25) !important;
        transform: translateX(3px) !important;
    }
    .submenu-link:hover i {
        color: #60A5FA !important;
    }
    .submenu-link:hover span {
        color: #FFFFFF !important;
    }
    .submenu-link.active {
        color: #FFFFFF !important;
        background: rgba(255, 255, 255, 0.08) !important;
        border: none !important;
        border-left: 3px solid #3B82F6 !important;
        font-weight: 700 !important;
    }
    .submenu-link.active i {
        color: #60A5FA !important;
    }
    .submenu-link.active span {
        color: #FFFFFF !important;
    }

    /* Sidebar Footer Weather / Status Card */
    .sidebar-footer-card {
        margin: 12px 14px 16px;
        padding: 10px 14px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25);
    }
    .sfc-weather-icon {
        font-size: 18px;
        color: #F59E0B;
    }
    .sfc-weather-info {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .sfc-temp {
        font-size: 13px;
        font-weight: 800;
        color: #FFFFFF;
        line-height: 1.1;
    }
    .sfc-cond {
        font-size: 10px;
        font-weight: 600;
        color: #94A3B8;
    }
    .sfc-status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #10B981;
        box-shadow: 0 0 8px #10B981;
    }

    /* Dark Glass Topbar */
    .topbar {
        background: rgba(16, 22, 34, 0.85) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
    }
    .sidebar-toggle-btn {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        border-radius: 10px !important;
    }
    .sidebar-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        border-color: rgba(255, 255, 255, 0.25) !important;
    }
    .user-avatar {
        background: linear-gradient(135deg, #3B82F6 0%, #8B5CF6 100%) !important;
        border: 1px solid rgba(255, 255, 255, 0.20) !important;
        color: #FFFFFF !important;
        font-weight: 800 !important;
    }
    .user-name { color: #FFFFFF !important; font-weight: 700 !important; }
    .user-role { color: #94A3B8 !important; font-weight: 500 !important; }
    .logout-btn {
        background: rgba(220, 38, 38, 0.20) !important;
        color: #FFFFFF !important;
        border: 1px solid rgba(220, 38, 38, 0.40) !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
    }
    .logout-btn:hover {
        background: rgba(220, 38, 38, 0.85) !important;
        color: #FFFFFF !important;
        border-color: #DC2626 !important;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.50) !important;
    }

    .topbar-quick-expense {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        padding: 8px 16px !important;
        background: rgba(239, 68, 68, 0.16) !important;
        color: #FCA5A5 !important;
        border: 1px solid rgba(239, 68, 68, 0.35) !important;
        border-radius: 10px !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        white-space: nowrap !important;
        cursor: pointer !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.20) !important;
    }
    .topbar-quick-expense:hover {
        background: linear-gradient(135deg, #EF4444, #DC2626) !important;
        border-color: #EF4444 !important;
        color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 6px 22px rgba(239, 68, 68, 0.50) !important;
    }
    .topbar-quick-expense i {
        font-size: 11px !important;
        color: inherit !important;
    }
    @media (max-width: 576px) {
        .topbar-quick-expense span { display: none !important; }
        .topbar-quick-expense { padding: 8px 11px !important; }
    }

    /* Dark Glass Tables */
    .table-container, .table-wrap, .table-responsive, div:has(> table) {
        background: rgba(16, 22, 34, 0.70) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 18px !important;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30) !important;
    }
    .premium-table th, .erp-table th, table th {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #94A3B8 !important;
        font-size: 11px !important;
        letter-spacing: 0.8px !important;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
        font-weight: 800 !important;
        padding: 12px 16px !important;
        text-transform: uppercase !important;
    }
    .premium-table td, .erp-table td, table td {
        color: #E2E8F0 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        font-weight: 500 !important;
    }
    .premium-table tbody tr:hover, .erp-table tbody tr:hover, table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }

    /* Dark Glass Forms & Inputs Across All Modules */
    .form-control, .search-input, select.form-control, input.form-control, select, input[type="text"], input[type="number"], input[type="date"], input[type="email"], input[type="password"], textarea, .filter-control, .filter-ctrl, .btn-reset, .btn-manage, .btn-action, .select2-container--default .select2-selection--single, .select2-container--default .select2-selection--multiple {
        background: rgba(16, 22, 34, 0.65) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        color: #FFFFFF !important;
        font-weight: 500 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
    }
    .form-control::placeholder, .search-input::placeholder, input::placeholder, textarea::placeholder {
        color: #94A3B8 !important;
    }
    .form-control:focus, .search-input:focus, select:focus, input:focus, textarea:focus, .filter-control:focus {
        border-color: #3B82F6 !important;
        background: rgba(16, 22, 34, 0.85) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
        outline: none !important;
        color: #FFFFFF !important;
    }
    /* Universal WebKit Autofill Dark Background Override */
    input:-webkit-autofill,
    input:-webkit-autofill:hover, 
    input:-webkit-autofill:focus,
    input:-webkit-autofill:active,
    textarea:-webkit-autofill,
    textarea:-webkit-autofill:hover,
    textarea:-webkit-autofill:focus,
    select:-webkit-autofill,
    select:-webkit-autofill:hover,
    select:-webkit-autofill:focus {
        -webkit-text-fill-color: #FFFFFF !important;
        -webkit-box-shadow: 0 0 0px 1000px #101622 inset !important;
        transition: background-color 50000s ease-in-out 0s !important;
        background-color: #101622 !important;
        color: #FFFFFF !important;
    }
    .btn-toggle-pwd, .toggle-password, .pwd-toggle {
        color: #CBD5E1 !important;
        background: transparent !important;
        border: none !important;
    }
    /* Select2 Luxury Dark Glass System */
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        background: rgba(16, 22, 34, 0.65) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px !important;
        min-height: 42px !important;
        padding: 2px 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #FFFFFF !important;
        font-weight: 600 !important;
        padding-left: 6px !important;
        line-height: 34px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 2px !important;
        right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        border-color: #CBD5E1 transparent transparent transparent !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: rgba(59, 130, 246, 0.20) !important;
        border: 1px solid rgba(96, 165, 250, 0.35) !important;
        color: #60A5FA !important;
        font-weight: 700 !important;
        border-radius: 8px !important;
        padding: 4px 10px 4px 24px !important;
        margin-top: 4px !important;
        margin-bottom: 4px !important;
        font-size: 13px !important;
        position: relative !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #F87171 !important;
        font-weight: 800 !important;
        margin-right: 6px !important;
        border: none !important;
        background: transparent !important;
        position: absolute !important;
        left: 8px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 14px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #EF4444 !important;
        background: transparent !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        color: #FFFFFF !important;
        font-family: inherit !important;
        margin-top: 4px !important;
    }
    .select2-dropdown {
        background: #101622 !important;
        border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.50) !important;
        overflow: hidden !important;
    }
    .select2-container--default .select2-results__option {
        color: #CBD5E1 !important;
        padding: 10px 14px !important;
        font-size: 13.5px !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #2563EB !important;
        color: #FFFFFF !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: rgba(59, 130, 246, 0.25) !important;
        color: #60A5FA !important;
        font-weight: 700 !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        background: rgba(16, 22, 34, 0.85) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
    }

    /* Action Buttons (View / Edit / Delete) */
    .btn-view, a.btn-view, button.btn-view, .action-link:has(.fa-eye) {
        background: rgba(59, 130, 246, 0.15) !important;
        color: #60A5FA !important;
        border: 1px solid rgba(96, 165, 250, 0.30) !important;
    }
    .btn-view:hover, a.btn-view:hover, button.btn-view:hover, .action-link:has(.fa-eye):hover {
        background: #2563EB !important;
        color: #FFFFFF !important;
        border-color: #2563EB !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.40) !important;
    }
    .btn-edit, a.btn-edit, button.btn-edit, .action-link:has(.fa-pen), .action-link:has(.fa-pen-to-square) {
        background: rgba(245, 158, 11, 0.15) !important;
        color: #FBBF24 !important;
        border: 1px solid rgba(245, 158, 11, 0.30) !important;
    }
    .btn-edit:hover, a.btn-edit:hover, button.btn-edit:hover, .action-link:has(.fa-pen):hover {
        background: #D97706 !important;
        color: #FFFFFF !important;
        border-color: #D97706 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(217, 119, 6, 0.40) !important;
    }
    .btn-delete, a.btn-delete, button.btn-delete, .action-link.delete, .action-link:has(.fa-trash), .action-link:has(.fa-trash-can) {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #F87171 !important;
        border: 1px solid rgba(239, 68, 68, 0.30) !important;
    }
    .btn-delete:hover, a.btn-delete:hover, button.btn-delete:hover, .action-link.delete:hover {
        background: #DC2626 !important;
        color: #FFFFFF !important;
        border-color: #DC2626 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.40) !important;
    }

    /* Primary CTAs */
    .btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom,
    .btn-pc, .btn-primary, .btn-search, .btn-gold, .btn-create, .btn-add, input[type="submit"] {
        background: #2563EB !important;
        color: #FFFFFF !important;
        border: 1px solid #3B82F6 !important;
        border-radius: 10px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35) !important;
        transition: transform 0.25s ease, background-color 0.25s ease !important;
    }
    .btn-primary-custom:hover, a.btn-primary-custom:hover, button.btn-primary-custom:hover,
    .btn-pc:hover, .btn-primary:hover, .btn-search:hover, .btn-gold:hover, .btn-create:hover, .btn-add:hover, input[type="submit"]:hover {
        background: #1D4ED8 !important;
        border-color: #2563EB !important;
        color: #FFFFFF !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 6px 22px rgba(37, 99, 235, 0.50) !important;
    }
    .page-item.active, .pagination-wrapper nav span[aria-current="page"], .pagination-wrap nav span[aria-current="page"] {
        background: #2563EB !important;
        color: #FFFFFF !important;
        border-color: #2563EB !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
    }

    /* Detail / Show Page Glass Cards & Data Items */
    .detail-item, .info-item, .prop-box, .data-item, .view-item, .show-item, .detail-box, .info-block {
        background: rgba(16, 22, 34, 0.65) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 14px !important;
        padding: 14px 18px !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.20) !important;
        margin-bottom: 12px !important;
    }
    .detail-label, .info-label, .field-label, .prop-label, .data-label {
        color: #94A3B8 !important;
        font-weight: 800 !important;
        font-size: 11px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.8px !important;
        margin-bottom: 4px !important;
    }
    .detail-value, .info-value, .field-value, .prop-value, .data-value {
        color: #FFFFFF !important;
        font-weight: 700 !important;
        font-size: 15px !important;
    }

    /* Universal Status Badges */
    .badge-success, .badge-active, .status-active, .badge-approved, .badge-completed, .badge.bg-success, span.badge-active {
        background: rgba(16, 185, 129, 0.18) !important;
        color: #34D399 !important;
        border: 1px solid rgba(16, 185, 129, 0.35) !important;
        font-weight: 700 !important;
        border-radius: 20px !important;
        padding: 4px 12px !important;
        display: inline-block !important;
    }
    .badge-danger, .badge-inactive, .status-inactive, .badge-cancelled, .badge-rejected, .badge.bg-danger, span.badge-inactive {
        background: rgba(239, 68, 68, 0.18) !important;
        color: #F87171 !important;
        border: 1px solid rgba(239, 68, 68, 0.35) !important;
        font-weight: 700 !important;
        border-radius: 20px !important;
        padding: 4px 12px !important;
        display: inline-block !important;
    }
    .badge-warning, .badge-pending, .status-pending, .badge.bg-warning {
        background: rgba(245, 158, 11, 0.18) !important;
        color: #FBBF24 !important;
        border: 1px solid rgba(245, 158, 11, 0.35) !important;
        font-weight: 700 !important;
        border-radius: 20px !important;
        padding: 4px 12px !important;
        display: inline-block !important;
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
        @if(session('login_type') !== 'firm')
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="Firm Management">
                <i class="fa-solid fa-building-flag"></i><span>Firm Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
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
            </ul>
        </li>
        @endif

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
            <a href="javascript:void(0);" class="menu-link submenu-toggle {{ (str_starts_with($currentRoute ?? '', 'projects.') || str_starts_with($currentRoute ?? '', 'contractors.') || str_starts_with($currentRoute ?? '', 'expenses.') || str_starts_with($currentRoute ?? '', 'vendors.') || str_starts_with($currentRoute ?? '', 'materials.') || str_starts_with($currentRoute ?? '', 'purchase-orders.') || str_starts_with($currentRoute ?? '', 'stock-inwards.') || str_starts_with($currentRoute ?? '', 'stock-outwards.') || str_starts_with($currentRoute ?? '', 'stock-report.')) ? 'parent-active' : '' }}" data-label="2. Project Management">
                <i class="fa-solid fa-city"></i><span>2. Project Management</span>
                <i class="fa-solid fa-chevron-right submenu-arrow"></i>
            </a>
            <ul class="submenu-list">
                <li class="submenu-item">
                    <a href="{{ route('projects.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'projects.') ? 'active' : '' }}">
                        <i class="fa-solid fa-city"></i><span>Projects</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('contractors.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'contractors.') ? 'active' : '' }}">
                        <i class="fa-solid fa-helmet-safety"></i><span>Contractors</span>
                    </a>
                </li>
                @if($authUser->hasPermission('expense_view'))
                <li class="submenu-item">
                    <a href="{{ route('expenses.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'expenses.') ? 'active' : '' }}">
                        <i class="fa-solid fa-receipt"></i><span>Project Expenses</span>
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
                        @if($authUser->hasPermission('vendor_view'))
                        <li class="submenu-item">
                            <a href="{{ route('vendors.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'vendors.') ? 'active' : '' }}">
                                <i class="fa-solid fa-truck-field"></i><span>Vendor Master</span>
                            </a>
                        </li>
                        @endif
                        <li class="submenu-item">
                            <a href="{{ route('materials.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'materials.') ? 'active' : '' }}">
                                <i class="fa-solid fa-box"></i><span>Material Master</span>
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
            </ul>
        </li>
        @endif

        {{-- 3. Customer Process --}}
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle {{ (str_starts_with($currentRoute ?? '', 'brokers.') || str_starts_with($currentRoute ?? '', 'broker-commissions.') || str_starts_with($currentRoute ?? '', 'customers.') || str_starts_with($currentRoute ?? '', 'forms.') || str_starts_with($currentRoute ?? '', 'form-submissions.') || str_starts_with($currentRoute ?? '', 'bookings.')) ? 'parent-active' : '' }}" data-label="3. Customer Process">
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

                @if($authUser->hasPermission('broker_view'))
                <li class="submenu-item">
                    <a href="{{ route('brokers.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'brokers.') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-tie"></i><span>Broker Master</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('broker_commission_view'))
                <li class="submenu-item">
                    <a href="{{ route('broker-commissions.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'broker-commissions.') ? 'active' : '' }}">
                        <i class="fa-solid fa-percent"></i><span>Broker Commission</span>
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
            <a href="javascript:void(0);" class="menu-link submenu-toggle {{ (str_starts_with($currentRoute ?? '', 'property-sales.') || str_starts_with($currentRoute ?? '', 'credit-notes.') || str_starts_with($currentRoute ?? '', 'debit-notes.') || str_starts_with($currentRoute ?? '', 'payments.')) ? 'parent-active' : '' }}" data-label="4. Sales Management">
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

                {{-- Temporarily Hidden: Credit Note & Debit Note
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
                --}}
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

                @if($authUser->hasPermission('receipt_view'))
                <li class="submenu-item">
                    <a href="{{ route('receipts.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'receipts.') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i><span>Receipt Voucher</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasPermission('loan_view'))
                <li class="submenu-item">
                    <a href="{{ route('loans.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'loans.') ? 'active' : '' }}">
                        <i class="fa-solid fa-landmark"></i><span>Loan Management</span>
                    </a>
                </li>
                <li class="submenu-item">
                    <a href="{{ route('emi-schedules.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'emi-schedules.') ? 'active' : '' }}">
                        <i class="fa-solid fa-calendar-minus"></i><span>EMI Schedule</span>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        {{-- 7. Reports --}}
        @if($authUser->hasPermission('reports_view'))
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link submenu-toggle" data-label="7. Reports">
                <i class="fa-solid fa-chart-column"></i><span>7. Reports</span>
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
                @if($authUser->hasPermission('role_permission_view'))
                <li class="submenu-item">
                    <a href="{{ route('roles.index') }}" class="submenu-link {{ str_starts_with($currentRoute ?? '', 'roles.') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-shield"></i><span>Role & Permissions</span>
                    </a>
                </li>
                @endif

            </ul>
        </li>
        @endif
    </ul>

    <!-- Sidebar Footer Compact Weather / Status Card -->
    <div class="sidebar-footer-card">
        <div class="sfc-weather-icon"><i class="fa-solid fa-cloud-sun"></i></div>
        <div class="sfc-weather-info">
            <span class="sfc-temp">30°C</span>
            <span class="sfc-cond">Mostly Cloudy</span>
        </div>
        <div class="sfc-status-dot" title="System Active"></div>
    </div>
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
            @if($authUser && $authUser->hasPermission('expense_create'))
            <a href="{{ route('expenses.create') }}" class="topbar-quick-expense" title="Quick Add Expense">
                <i class="fa-solid fa-plus"></i><span>Expense</span>
            </a>
            @endif

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
    let current = activeLink.parentElement;
    while (current && !current.classList.contains('sidebar-menu')) {
        if (current.classList.contains('submenu-list') || current.classList.contains('nested-submenu-list')) {
            current.style.display = 'block';
        }
        if (current.classList.contains('menu-item') || current.classList.contains('submenu-item')) {
            current.classList.add('open');
            const toggle = current.querySelector(':scope > .submenu-toggle, :scope > .nested-submenu-toggle');
            if (toggle) {
                toggle.classList.add('parent-active');
            }
        }
        current = current.parentElement;
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

// ── Universal Password Show/Hide Toggle Handler (Vanilla JS) ──
document.addEventListener('click', function (e) {
    const toggleBtn = e.target.closest('.btn-toggle-pwd, .toggle-password, .pwd-toggle');
    if (toggleBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        let targetId = toggleBtn.getAttribute('data-target');
        let targetInput = null;
        
        if (targetId) {
            targetInput = document.getElementById(targetId);
        }
        
        if (!targetInput) {
            targetInput = toggleBtn.parentElement ? toggleBtn.parentElement.querySelector('input') : null;
        }
        
        if (!targetInput) {
            targetInput = toggleBtn.closest('.form-group, div') ? toggleBtn.closest('.form-group, div').querySelector('input') : null;
        }

        if (targetInput) {
            const icon = toggleBtn.querySelector('i') || toggleBtn;
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye', 'fa-regular');
                    icon.classList.add('fa-eye-slash', 'fa-solid');
                }
            } else {
                targetInput.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash', 'fa-solid');
                    icon.classList.add('fa-eye', 'fa-regular');
                }
            }
        }
    }
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
<style>
/* ── UNIVERSAL HEADER & SUBTITLE WHITE BOLD STYLING ACROSS ALL MODULES ── */
html body .crud-title h2,
html body .crud-title h1,
html body .crud-title h3,
html body .page-header-title,
html body .dash-welcome-title,
html body .rpt-title-block h2 {
    color: #FFFFFF !important;
    font-weight: 800 !important;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.45) !important;
}

html body .crud-title p,
html body .crud-header p,
html body .page-header-sub,
html body .page-subtitle,
html body .dash-welcome-sub,
html body .rpt-title-block p,
html body .crud-title span,
html body .card-title-sub,
html body .page-header p,
html body .panel-subtitle {
    color: #FFFFFF !important;
    font-weight: 700 !important;
    opacity: 1 !important;
    text-shadow: 0 1px 8px rgba(0, 0, 0, 0.50) !important;
}

/* ── UNIVERSAL DARK GLASS TABLE HEADER ── */
html body .card-box table thead th,
html body table.projects-table thead th,
html body table.table thead th,
html body .table thead th {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10) !important;
}

/* ── UNIVERSAL DARK GLASS PAGINATION ── */
html body .page-item,
html body .pagination-wrapper nav a,
html body .pagination-wrapper nav span,
html body .pagination-wrap nav a,
html body .pagination-wrap nav span,
html body .pagination-buttons .page-item {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.20) !important;
}

html body .page-item:hover,
html body .pagination-wrapper nav a:hover,
html body .pagination-wrap nav a:hover,
html body .pagination-buttons a.page-item:hover {
    background: rgba(59, 130, 246, 0.25) !important;
    color: #FFFFFF !important;
    border-color: #3B82F6 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35) !important;
}

html body .page-item.active,
html body .pagination-wrapper nav span[aria-current="page"],
html body .pagination-wrap nav span[aria-current="page"],
html body .pagination-buttons span.page-item.active {
    background: #2563EB !important;
    color: #FFFFFF !important;
    border-color: #3B82F6 !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45) !important;
    font-weight: 800 !important;
}

html body .page-item.disabled,
html body .pagination-wrapper nav span[aria-disabled="true"],
html body .pagination-wrap nav span[aria-disabled="true"],
html body .pagination-buttons span.page-item.disabled {
    opacity: 0.40 !important;
    cursor: not-allowed !important;
    background: rgba(255, 255, 255, 0.03) !important;
    color: rgba(255, 255, 255, 0.35) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: none !important;
    transform: none !important;
}
/* ── UNIVERSAL CODE CHIP BADGES ── */
html body code,
html body .code-chip {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    font-family: monospace !important;
    display: inline-block !important;
}

/* ── UNIVERSAL PROPERTY PREVIEW INFO BOX ── */
html body .prop-info-box,
html body .property-preview-box,
html body .info-preview-box {
    background: rgba(255, 255, 255, 0.05) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 14px !important;
    padding: 14px 18px !important;
    margin-top: 14px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.20) !important;
}

html body .prop-info-box .pi-item,
html body .property-preview-box .pi-item {
    color: #94A3B8 !important;
    font-size: 13.5px !important;
    font-weight: 600 !important;
}

html body .prop-info-box .pi-item strong,
html body .property-preview-box .pi-item strong {
    color: #FFFFFF !important;
    font-weight: 800 !important;
}
/* ── UNIVERSAL LUXURY DARK GLASS ALERTS ── */
html body .alert-success,
html body .alert-succ {
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1px solid rgba(16, 185, 129, 0.38) !important;
    color: #34D399 !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.20) !important;
}

html body .alert-err,
html body .alert-danger,
html body .alert-error {
    background: rgba(239, 68, 68, 0.16) !important;
    border: 1px solid rgba(239, 68, 68, 0.38) !important;
    color: #F87171 !important;
    border-radius: 12px !important;
    padding: 14px 18px !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 16px rgba(239, 68, 68, 0.20) !important;
}

/* ── UNIVERSAL WHITE CALENDAR & TIME ICONS FOR ALL DATE INPUTS ── */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="datetime-local"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator,
input[type="month"]::-webkit-calendar-picker-indicator,
.form-control[type="date"]::-webkit-calendar-picker-indicator,
.form-control[type="datetime-local"]::-webkit-calendar-picker-indicator,
.m-form-control[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1) brightness(100%) saturate(0%) !important;
    -webkit-filter: invert(1) brightness(100%) saturate(0%) !important;
    cursor: pointer !important;
    opacity: 0.95 !important;
    padding: 2px !important;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

input[type="date"]::-webkit-calendar-picker-indicator:hover,
input[type="datetime-local"]::-webkit-calendar-picker-indicator:hover,
input[type="time"]::-webkit-calendar-picker-indicator:hover,
input[type="month"]::-webkit-calendar-picker-indicator:hover,
.form-control[type="date"]::-webkit-calendar-picker-indicator:hover {
    opacity: 1 !important;
    transform: scale(1.15);
}

/* ── UNIVERSAL EXPORT & PRINT BUTTONS (Direct Solid Colors) ── */
html body .btn-export-pdf,
html body .btn-pdf {
    background: linear-gradient(135deg, #EF4444, #DC2626) !important;
    color: #FFFFFF !important;
    border: 1px solid #EF4444 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
}
html body .btn-export-pdf:hover,
html body .btn-pdf:hover {
    background: linear-gradient(135deg, #DC2626, #B91C1C) !important;
    border-color: #DC2626 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.55) !important;
}

html body .btn-excel,
html body .btn-export,
html body .btn-export-excel,
html body .btn-export-csv {
    background: linear-gradient(135deg, #10B981, #059669) !important;
    color: #FFFFFF !important;
    border: 1px solid #10B981 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
}
html body .btn-excel:hover,
html body .btn-export:hover,
html body .btn-export-excel:hover,
html body .btn-export-csv:hover {
    background: linear-gradient(135deg, #059669, #047857) !important;
    border-color: #059669 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.55) !important;
}

html body .btn-print,
html body .btn-export-print {
    background: linear-gradient(135deg, #6366F1, #4F46E5) !important;
    color: #FFFFFF !important;
    border: 1px solid #6366F1 !important;
    border-radius: 10px !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
}
html body .btn-print:hover,
html body .btn-export-print:hover {
    background: linear-gradient(135deg, #4F46E5, #4338CA) !important;
    border-color: #4F46E5 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.55) !important;
}

/* ── Luxury Dark Glass SweetAlert2 System ── */
div.swal2-container {
    backdrop-filter: blur(14px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(14px) saturate(180%) !important;
    background: rgba(3, 7, 18, 0.72) !important;
    z-index: 99999 !important;
}

div.swal2-popup {
    background: rgba(18, 25, 38, 0.96) !important;
    backdrop-filter: blur(24px) saturate(190%) !important;
    -webkit-backdrop-filter: blur(24px) saturate(190%) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.16) !important;
    border-radius: 22px !important;
    box-shadow: 0 28px 70px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.08) !important;
    padding: 30px 26px !important;
    color: #FFFFFF !important;
    font-family: 'Inter', 'Poppins', 'Manrope', sans-serif !important;
}

div.swal2-title {
    color: #FFFFFF !important;
    font-size: 22px !important;
    font-weight: 800 !important;
    letter-spacing: -0.3px !important;
    margin-top: 10px !important;
    margin-bottom: 8px !important;
}

div.swal2-html-container {
    color: #CBD5E1 !important;
    font-size: 14.5px !important;
    line-height: 1.6 !important;
    margin-top: 8px !important;
}

div.swal2-html-container strong {
    color: #60A5FA !important;
    font-weight: 700 !important;
}

/* SweetAlert Icons in Dark Theme */
.swal2-icon.swal2-warning {
    border-color: #F59E0B !important;
    color: #F59E0B !important;
    box-shadow: 0 0 25px rgba(245, 158, 11, 0.25) !important;
}

.swal2-icon.swal2-success {
    border-color: #10B981 !important;
    color: #10B981 !important;
    box-shadow: 0 0 25px rgba(16, 185, 129, 0.30) !important;
}
.swal2-icon.swal2-success [class^='swal2-success-line'] {
    background-color: #10B981 !important;
}
.swal2-icon.swal2-success .swal2-success-ring {
    border-color: rgba(16, 185, 129, 0.40) !important;
}

.swal2-icon.swal2-error {
    border-color: #EF4444 !important;
    color: #EF4444 !important;
    box-shadow: 0 0 25px rgba(239, 68, 68, 0.30) !important;
}

.swal2-icon.swal2-info {
    border-color: #3B82F6 !important;
    color: #3B82F6 !important;
    box-shadow: 0 0 25px rgba(59, 130, 246, 0.30) !important;
}

/* SweetAlert Action Buttons */
.swal2-actions {
    margin-top: 24px !important;
    gap: 12px !important;
}

button.swal2-confirm {
    border-radius: 10px !important;
    font-weight: 700 !important;
    font-size: 14px !important;
    padding: 11px 24px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35) !important;
    transition: all .2s ease !important;
}
button.swal2-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.50) !important;
}

button.swal2-cancel {
    border-radius: 10px !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    padding: 11px 22px !important;
    background: rgba(255, 255, 255, 0.12) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.16) !important;
    transition: all .2s ease !important;
}
button.swal2-cancel:hover {
    background: rgba(255, 255, 255, 0.20) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px) !important;
}

.swal2-timer-progress-bar {
    background: linear-gradient(90deg, #3B82F6, #10B981) !important;
}

/* Hide default browser password reveal eye icons to prevent click conflicts */
input::-ms-reveal,
input::-ms-clear {
    display: none !important;
}
input::-webkit-credentials-auto-fill-button {
    visibility: hidden !important;
    pointer-events: none !important;
    position: absolute !important;
    right: 0 !important;
}
.btn-toggle-pwd, .pwd-toggle-btn {
    cursor: pointer !important;
    user-select: none !important;
}
.btn-toggle-pwd i, .pwd-toggle-btn i {
    pointer-events: none !important;
}
</style>

<script>
window.togglePasswordVisibility = function(fieldId, buttonElement) {
    var input = document.getElementById(fieldId);
    if (!input && buttonElement) {
        var wrapper = buttonElement.closest('div, .pwd-wrapper, .form-group');
        if (wrapper) {
            input = wrapper.querySelector('input[type="password"], input[type="text"]');
        }
    }
    if (!input) return;

    var icon = buttonElement ? buttonElement.querySelector('i') : null;
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            icon.style.color = '#60A5FA';
        }
    } else {
        input.type = 'password';
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            icon.style.color = '#94A3B8';
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Global delegation for any password toggle button
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-toggle-pwd, .pwd-toggle-btn, [data-toggle="password"]');
        if (!btn) return;
        
        var targetId = btn.getAttribute('data-target');
        var input = targetId ? document.getElementById(targetId) : null;
        if (!input) {
            var wrapper = btn.closest('div, .pwd-wrapper, .form-group');
            if (wrapper) {
                input = wrapper.querySelector('input[type="password"], input[type="text"]');
            }
        }
        if (input) {
            e.preventDefault();
            e.stopPropagation();
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    icon.style.color = '#60A5FA';
                }
            } else {
                input.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    icon.style.color = '#94A3B8';
                }
            }
        }
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            background: 'rgba(18, 25, 38, 0.96)',
            color: '#FFFFFF',
            confirmButtonColor: '#2563EB',
            confirmButtonText: '<i class="fa-solid fa-check"></i> Great',
            timer: 2800,
            timerProgressBar: true,
            showClass: {
                popup: 'animate__animated animate__zoomIn animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__zoomOut animate__faster'
            }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Notice',
            text: "{{ session('error') }}",
            background: 'rgba(18, 25, 38, 0.96)',
            color: '#FFFFFF',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK',
            showClass: {
                popup: 'animate__animated animate__shakeX animate__faster'
            }
        });
    @endif
});
</script>
</body>
</html>
