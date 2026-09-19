<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delawala Properties — Real Estate & ERP Management System</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@400;500;600;700;800&family=Pinyon+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-navy: #0A0F1D;
            --secondary-navy: #111827;
            --accent-gold: #D4AF37;
            --accent-gold-light: #F7E7A9;
            --accent-gold-dark: #997A15;
            --accent-blue: #2563EB;
            --accent-blue-light: #3B82F6;
            --accent-cyan: #06B6D4;
            --glass-card: rgba(17, 24, 39, 0.78);
            --glass-border: rgba(212, 175, 55, 0.22);
            --glass-glow: rgba(37, 99, 235, 0.18);
            --text-main: #FFFFFF;
            --text-muted: #94A3B8;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            min-height: 100%;
            background-color: var(--primary-navy);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* ══════════════════════════════════════════════
           BACKGROUND & LUXURY LIGHT EFFECTS
        ══════════════════════════════════════════════ */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
            background: radial-gradient(circle at 50% 10%, #151d38 0%, #080c16 65%, #04060b 100%);
        }

        .ambient-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.45;
            animation: floatGlow 18s ease-in-out infinite alternate;
        }
        .orb-1 {
            top: -10%;
            left: 20%;
            width: 550px;
            height: 550px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.35) 0%, transparent 70%);
        }
        .orb-2 {
            bottom: 5%;
            right: 15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.25) 0%, transparent 70%);
            animation-duration: 22s;
            animation-delay: -5s;
        }
        .orb-3 {
            top: 40%;
            left: -10%;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.2) 0%, transparent 70%);
            animation-duration: 25s;
            animation-delay: -10s;
        }

        @keyframes floatGlow {
            0% { transform: translateY(0) scale(1); }
            50% { transform: translateY(40px) scale(1.08); }
            100% { transform: translateY(-30px) scale(0.95); }
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            opacity: 0.6;
        }

        /* ══════════════════════════════════════════════
           NAVIGATION
        ══════════════════════════════════════════════ */
        .site-header {
            position: relative;
            z-index: 30;
            width: 100%;
            max-width: 1320px;
            margin: 0 auto;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-link {
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            transition: transform 0.3s ease;
        }
        .brand-logo-link:hover {
            transform: scale(1.02);
        }

        .header-logo-img {
            height: 52px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(37, 99, 235, 0.35));
        }

        .brand-text-block {
            display: flex;
            flex-direction: column;
        }
        .brand-title {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 2px;
            background: linear-gradient(135deg, #FFFFFF 30%, var(--accent-gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-transform: uppercase;
        }
        .brand-sub {
            font-family: 'Manrope', sans-serif;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 3.5px;
            color: var(--accent-gold);
            text-transform: uppercase;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-nav-ghost {
            padding: 10px 22px;
            font-size: 13.5px;
            font-weight: 600;
            color: #E2E8F0;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-nav-ghost:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
            border-color: rgba(212, 175, 55, 0.4);
            transform: translateY(-2px);
        }

        .btn-nav-primary {
            padding: 10px 24px;
            font-size: 13.5px;
            font-weight: 700;
            color: #0F172A;
            background: linear-gradient(135deg, #F7E7A9 0%, #D4AF37 50%, #B89020 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.35);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(212, 175, 55, 0.55);
            color: #000000;
        }

        /* ══════════════════════════════════════════════
           HERO SECTION & MAIN SHOWCASE
        ══════════════════════════════════════════════ */
        .main-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1240px;
            margin: 20px auto 60px;
            padding: 0 24px;
        }

        .hero-card {
            background: var(--glass-card);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8),
                        inset 0 1px 0 0 rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            overflow: hidden;
            position: relative;
        }

        @media (max-width: 960px) {
            .hero-card {
                grid-template-columns: 1fr;
            }
        }

        .hero-content {
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @media (max-width: 640px) {
            .hero-content {
                padding: 36px 24px;
            }
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.35);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 700;
            color: var(--accent-gold-light);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 24px;
            width: fit-content;
        }
        .badge-dot {
            width: 7px;
            height: 7px;
            background: #22C55E;
            border-radius: 50%;
            box-shadow: 0 0 10px #22C55E;
            animation: pulseDot 2s infinite;
        }
        @keyframes pulseDot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .hero-title {
            font-family: 'Cinzel', serif;
            font-size: 40px;
            font-weight: 800;
            line-height: 1.18;
            letter-spacing: 0.5px;
            margin-bottom: 18px;
            background: linear-gradient(135deg, #FFFFFF 40%, #E2E8F0 75%, var(--accent-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        @media (max-width: 640px) {
            .hero-title {
                font-size: 30px;
            }
        }

        .hero-subtitle {
            font-size: 15.5px;
            line-height: 1.65;
            color: #94A3B8;
            margin-bottom: 36px;
            font-weight: 400;
        }

        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px 18px;
            margin-bottom: 40px;
        }
        @media (max-width: 640px) {
            .features-list {
                grid-template-columns: 1fr;
            }
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 500;
            color: #E2E8F0;
            transition: all 0.25s ease;
        }
        .feature-item:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(212, 175, 55, 0.3);
            transform: translateX(3px);
        }
        .feature-icon-box {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.25) 0%, rgba(212, 175, 55, 0.2) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-gold-light);
            font-size: 13px;
            flex-shrink: 0;
        }

        .cta-actions-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-launch-hero {
            padding: 14px 34px;
            font-size: 15px;
            font-weight: 700;
            color: #0B0E17;
            background: linear-gradient(135deg, #F9E79F 0%, #D4AF37 50%, #A37E17 100%);
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            cursor: pointer;
        }
        .btn-launch-hero:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 14px 40px rgba(212, 175, 55, 0.6);
            color: #000;
        }

        .btn-firm-hero {
            padding: 14px 26px;
            font-size: 14.5px;
            font-weight: 600;
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-firm-hero:hover {
            background: rgba(255, 255, 255, 0.12);
            border-color: var(--accent-blue-light);
            color: #FFFFFF;
            transform: translateY(-2px);
        }

        /* ══════════════════════════════════════════════
           RIGHT BRANDING & LOGO SHOWCASE PANEL
        ══════════════════════════════════════════════ */
        .hero-visual-panel {
            background: linear-gradient(145deg, rgba(16, 26, 48, 0.85) 0%, rgba(10, 16, 30, 0.95) 100%);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            padding: 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        @media (max-width: 960px) {
            .hero-visual-panel {
                border-left: none;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                padding: 42px 24px;
            }
        }

        .visual-glow-ring {
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(212, 175, 55, 0.15) 50%, transparent 70%);
            filter: blur(40px);
            animation: pulseGlow 10s ease-in-out infinite alternate;
        }
        @keyframes pulseGlow {
            0% { transform: scale(0.9); opacity: 0.6; }
            100% { transform: scale(1.15); opacity: 1; }
        }

        .logo-main-container {
            position: relative;
            z-index: 2;
            padding: 32px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(212, 175, 55, 0.08);
            max-width: 320px;
            width: 100%;
            transition: all 0.35s ease;
        }
        .logo-main-container:hover {
            transform: translateY(-5px);
            border-color: rgba(212, 175, 55, 0.5);
            box-shadow: 0 25px 60px rgba(212, 175, 55, 0.25), inset 0 0 30px rgba(212, 175, 55, 0.15);
        }

        .showcase-logo-img {
            max-width: 220px;
            width: 100%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 10px 25px rgba(0, 0, 0, 0.6));
            margin: 0 auto 18px;
            display: block;
        }

        .showcase-title {
            font-family: 'Cinzel', serif;
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .showcase-subtitle {
            font-family: 'Pinyon Script', cursive;
            font-size: 26px;
            color: var(--accent-gold-light);
            margin-top: 2px;
            letter-spacing: 1px;
        }

        .meta-badges {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .meta-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #CBD5E1;
        }

        /* ══════════════════════════════════════════════
           ERP STATS / QUICK CARDS
        ══════════════════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 32px;
        }
        @media (max-width: 840px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: rgba(17, 24, 39, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            padding: 22px 20px;
            backdrop-filter: blur(16px);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.25s ease;
        }
        .stat-card:hover {
            border-color: rgba(212, 175, 55, 0.35);
            transform: translateY(-3px);
            background: rgba(20, 30, 50, 0.75);
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(212, 175, 55, 0.15) 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--accent-gold);
            flex-shrink: 0;
        }
        .stat-info {
            display: flex;
            flex-direction: column;
        }
        .stat-title {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-val {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
            margin-top: 2px;
        }

        /* ══════════════════════════════════════════════
           FOOTER
        ══════════════════════════════════════════════ */
        .site-footer {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 28px 20px 36px;
            color: #64748B;
            font-size: 12.5px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .site-footer a {
            color: var(--accent-gold);
            text-decoration: none;
            font-weight: 600;
        }
        .site-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Ambient Canvas & Orbs -->
    <div class="bg-canvas">
        <div class="grid-pattern"></div>
        <div class="ambient-orb orb-1"></div>
        <div class="ambient-orb orb-2"></div>
        <div class="ambient-orb orb-3"></div>
    </div>

    <!-- Top Navigation -->
    <header class="site-header">
        <a href="{{ url('/') }}" class="brand-logo-link">
            @if(file_exists(public_path('assets/logos/logo 1.png')))
                <img src="{{ asset('assets/logos/logo 1.png') }}" alt="Delawala Properties" class="header-logo-img">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" class="header-logo-img">
            @endif
            <div class="brand-text-block">
                <span class="brand-title">Delawala</span>
                <span class="brand-sub">Properties & ERP</span>
            </div>
        </a>

        <div class="nav-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn-nav-primary">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav-primary">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    <span>Sign In</span>
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-wrapper">
        <div class="hero-card">
            
            <!-- Left Info Panel -->
            <div class="hero-content">
                <div class="badge-pill">
                    <span class="badge-dot"></span>
                    <span>Delawala Enterprise Management System</span>
                </div>

                <h1 class="hero-title">
                    Unified Real Estate &amp; Construction ERP
                </h1>

                <p class="hero-subtitle">
                    Comprehensive property lifecycle management, tenant and customer records, construction material inventory, financial ledgers, and automated reporting in one secure platform.
                </p>

                <ul class="features-list">
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <span>Properties &amp; Units</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <span>Tenancy &amp; Rentals</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <span>Stock &amp; Inventory</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span>Expenses &amp; Ledgers</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <span>Brokers &amp; Contractors</span>
                    </li>
                    <li class="feature-item">
                        <div class="feature-icon-box">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <span>Multi-Firm Access</span>
                    </li>
                </ul>

                <div class="cta-actions-group">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-launch-hero">
                            <i class="fa-solid fa-gauge-high"></i>
                            <span>Open Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-launch-hero">
                            <i class="fa-solid fa-lock"></i>
                            <span>Access ERP Portal</span>
                        </a>
                        <a href="{{ route('firm-selection') }}" class="btn-firm-hero">
                            <i class="fa-solid fa-building-flag"></i>
                            <span>Firm Selection</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Visual / Logo Panel -->
            <div class="hero-visual-panel">
                <div class="visual-glow-ring"></div>

                <div class="logo-main-container">
                    @if(file_exists(public_path('assets/logos/logo 1.png')))
                        <img src="{{ asset('assets/logos/logo 1.png') }}" alt="Delawala Properties Logo" class="showcase-logo-img">
                    @elseif(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties Logo" class="showcase-logo-img">
                    @elseif(file_exists(public_path('images/logo.jpeg')))
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Delawala Properties Logo" class="showcase-logo-img">
                    @endif

                    <h2 class="showcase-title">Delawala</h2>
                    <div class="showcase-subtitle">Properties</div>

                    <div class="meta-badges">
                        <span class="meta-badge"><i class="fa-solid fa-check-double" style="color:#D4AF37; margin-right:4px;"></i> Enterprise Edition</span>
                        <span class="meta-badge"><i class="fa-solid fa-shield-check" style="color:#3B82F6; margin-right:4px;"></i> Secure Cloud</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- System Highlights / Stats Bar -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-city"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-title">Portfolio</span>
                    <span class="stat-val">Projects &amp; Sites</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-title">Stakeholders</span>
                    <span class="stat-val">Clients &amp; Vendors</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-title">Finance</span>
                    <span class="stat-val">Ledgers &amp; Invoices</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-title">Inventory</span>
                    <span class="stat-val">Materials &amp; Stock</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <p>&copy; {{ date('Y') }} <strong>Delawala Properties &amp; Management Group</strong>. All Rights Reserved.</p>
    </footer>

</body>
</html>
