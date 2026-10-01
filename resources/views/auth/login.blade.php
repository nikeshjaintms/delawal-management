<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Delawala Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v=2">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Pinyon+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ── RESET ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        /* ════════════════════════════════════════
           FULL-SCREEN BACKGROUND
        ════════════════════════════════════════ */
        .page {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
            background-image: url("{{ asset('assets/login.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Dark overlay — denser on left for card readability */
        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                rgba(10, 20, 45, 0.78) 0%,
                rgba(10, 20, 45, 0.52) 38%,
                rgba(10, 20, 45, 0.15) 68%,
                rgba(0, 0, 0, 0.04) 100%
            );
            z-index: 1;
        }

        /* Vignette top/bottom */
        .page::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                180deg,
                rgba(0,0,0,0.18) 0%,
                transparent 20%,
                transparent 80%,
                rgba(0,0,0,0.22) 100%
            );
            z-index: 1;
            pointer-events: none;
        }

        /* ════════════════════════════════════════
           CARD WRAPPER
        ════════════════════════════════════════ */
        .card-wrap {
            position: relative;
            z-index: 10;
            margin-left: clamp(24px, 6vw, 90px);
            width: 100%;
            max-width: 440px;
            animation: cardIn 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ════════════════════════════════════════
           LOGIN CONTAINER
        ════════════════════════════════════════ */
        .login-card {
            background: rgba(15, 23, 42, 0.70);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), inset 0 1px 0 0 rgba(255, 255, 255, 0.20);
            border-radius: 22px;
            padding: 32px 36px 28px;
            position: relative;
            overflow: hidden;
        }

        /* ════════════════════════════════════════
           LOGO
        ════════════════════════════════════════ */
        .logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 22px;
        }

        .logo-img {
            max-height: 60px;
            max-width: 190px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.95;
        }

        .logo-script {
            font-family: 'Pinyon Script', cursive;
            font-size: 38px;
            color: #fff;
            letter-spacing: 1px;
            text-shadow: 0 2px 12px rgba(0,0,0,0.35);
            line-height: 1;
        }
        .logo-script-sub {
            font-family: 'Inter', sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 4px;
            color: rgba(200,220,255,0.85);
            text-transform: uppercase;
            text-align: center;
            margin-top: 2px;
        }
        .logo-text-wrap { text-align: center; }

        /* ════════════════════════════════════════
           ALERTS (ERROR & SUCCESS)
        ════════════════════════════════════════ */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.45);
            border-left: 3.5px solid #EF4444;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #FECACA;
            animation: shake 0.4s both;
        }
        .alert-error i { font-size: 14px; color: #F87171; flex-shrink: 0; }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(16, 185, 129, 0.16);
            border: 1px solid rgba(16, 185, 129, 0.45);
            border-left: 3.5px solid #10B981;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 18px;
            font-size: 13px;
            color: #A7F3D0;
            animation: fadeIn 0.4s both;
        }
        .alert-success i { font-size: 14px; color: #34D399; flex-shrink: 0; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-4px); }
            40%      { transform: translateX(4px); }
            60%      { transform: translateX(-2px); }
            80%      { transform: translateX(2px); }
        }

        /* ════════════════════════════════════════
           ADMIN / FIRM TOGGLE
        ════════════════════════════════════════ */
        .toggle-wrap {
            display: flex;
            gap: 0;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 11px;
            padding: 3px;
            margin-bottom: 22px;
        }

        .toggle-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 8px;
            border: none;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.22s ease;
            letter-spacing: 0.2px;
            position: relative;
            overflow: hidden;
        }

        /* INACTIVE tab */
        .toggle-btn.tab-inactive {
            background: transparent;
            color: rgba(255, 255, 255, 0.60);
        }
        .toggle-btn.tab-inactive:hover {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.90);
        }

        /* ACTIVE tab — gold */
        .toggle-btn.tab-active {
            background: linear-gradient(135deg, #8A6E3B 0%, #C5A87E 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(197, 168, 126, 0.40), inset 0 1px 0 rgba(255, 255, 255, 0.20);
            transform: translateY(-1px);
        }

        /* ════════════════════════════════════════
           FORM LABELS & INPUTS
        ════════════════════════════════════════ */
        .form-group { margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #CBD5E1;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.40);
            font-size: 13.5px;
            pointer-events: none;
            transition: color 0.2s;
            z-index: 2;
        }
        .input-wrap:focus-within .input-icon { color: #93C5FD; }

        .form-input {
            width: 100%;
            height: 44px;
            padding: 10px 42px;
            background: rgba(15, 23, 42, 0.55);
            border: 1.5px solid rgba(255, 255, 255, 0.16);
            border-radius: 10px;
            font-size: 13.5px;
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            outline: none;
            transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
            caret-color: #93C5FD;
        }
        .form-input::placeholder {
            color: rgba(255,255,255,0.30);
            font-size: 13px;
        }
        .form-input:focus {
            border-color: #3B82F6;
            background: rgba(15, 23, 42, 0.75);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.22);
        }
        .form-input.is-invalid { border-color: rgba(239,68,68,0.70); }

        /* Hide default browser password reveal */
        input::-ms-reveal,
        input::-ms-clear { display: none !important; }

        /* Autofill override */
        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgba(15,23,42,0.85) inset;
            -webkit-text-fill-color: #FFFFFF;
            caret-color: #FFFFFF;
            border-color: rgba(255,255,255,0.3);
        }

        /* Eye toggle */
        .pwd-toggle {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.40);
            font-size: 14px;
            padding: 5px;
            transition: color 0.2s;
            z-index: 2;
            display: flex; align-items: center;
        }
        .pwd-toggle:hover { color: #FFFFFF; }

        .field-error {
            font-size: 11.5px; color: #FECACA;
            margin-top: 5px;
            display: flex; align-items: center; gap: 5px;
        }

        /* ════════════════════════════════════════
           REMEMBER + CHANGE PASSWORD ROW
        ════════════════════════════════════════ */
        .form-row-extra {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            cursor: pointer;
            font-size: 12.5px; font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            user-select: none;
        }
        .remember-label input[type="checkbox"] {
            width: 14px; height: 14px;
            accent-color: #C5A87E; cursor: pointer;
        }
        .forgot-link {
            font-size: 12.5px; color: #93C5FD;
            text-decoration: none; font-weight: 600;
            transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .forgot-link:hover { color: #FFFFFF; text-decoration: underline; }

        /* ════════════════════════════════════════
           SIGN IN BUTTON
        ════════════════════════════════════════ */
        .btn-login {
            width: 100%;
            height: 46px;
            background: linear-gradient(135deg, #8A6E3B 0%, #C5A87E 100%);
            color: #FFFFFF;
            font-size: 14.5px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none; border-radius: 10px;
            cursor: pointer; letter-spacing: 0.4px;
            position: relative; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 18px rgba(197, 168, 126, 0.45);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 26px rgba(197, 168, 126, 0.55);
        }
        .btn-login:active { transform: translateY(0); }

        /* Bottom Alt Row */
        .login-alt-row {
            text-align: center;
            margin-top: 14px;
            font-size: 12.5px;
            color: rgba(255,255,255,0.65);
        }
        .login-alt-link {
            color: #C5A87E;
            text-decoration: none;
            font-weight: 700;
            margin-left: 4px;
            transition: color 0.2s;
        }
        .login-alt-link:hover {
            color: #E6D0B2;
            text-decoration: underline;
        }

        /* ════════════════════════════════════════
           DIVIDER + FOOTER
        ════════════════════════════════════════ */
        .card-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.12);
            margin: 18px 0 12px;
        }
        .card-footer {
            text-align: center;
            font-size: 11.5px;
            color: rgba(255,255,255,0.75);
            line-height: 1.6;
        }
        .card-footer a {
            color: #60A5FA;
            text-decoration: none; font-weight: 600;
            transition: color 0.2s;
        }
        .card-footer a:hover { color: #93C5FD; text-decoration: underline; }

        /* ════════════════════════════════════════
           MODAL STYLES (NO SCROLLBAR OVERFLOW)
        ════════════════════════════════════════ */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(4, 8, 20, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-card {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(28px) saturate(190%);
            -webkit-backdrop-filter: blur(28px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.20);
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            padding: 26px 28px 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.85), inset 0 1px 0 rgba(255,255,255,0.25);
            position: relative;
            margin: auto;
            transform: scale(0.96) translateY(8px);
            transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .modal-overlay.active .modal-card {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        }
        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .modal-title i {
            color: #C5A87E;
            font-size: 17px;
        }
        .modal-close-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.7);
            border-radius: 8px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }
        .modal-close-btn:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.5);
            color: #EF4444;
        }

        .modal-btn-cancel {
            flex: 1;
            height: 42px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 10px;
            color: #E2E8F0;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .modal-btn-cancel:hover {
            background: rgba(255,255,255,0.16);
            color: #FFFFFF;
        }

        .modal-btn-submit {
            flex: 1.6;
            height: 42px;
            background: linear-gradient(135deg, #8A6E3B 0%, #C5A87E 100%);
            border: none;
            border-radius: 10px;
            color: #FFFFFF;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            box-shadow: 0 4px 14px rgba(197, 168, 126, 0.4);
            transition: all 0.2s;
        }
        .modal-btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(197, 168, 126, 0.55);
        }

        /* Live Strength Bar on Modal */
        .strength-track {
            height: 4px;
            background: rgba(255,255,255,0.12);
            border-radius: 4px;
            margin-top: 6px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .strength-text {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            margin-top: 3px;
            display: flex;
            justify-content: space-between;
        }

        /* ════════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════════ */
        @media (max-width: 640px) {
            .card-wrap {
                margin: 0 auto;
                max-width: 100%;
                padding: 0 16px;
            }
            .login-card { padding: 24px 22px 20px; }
            .modal-card { padding: 20px 18px; }
        }
    </style>
</head>
<body>

<div class="page">

    <div class="card-wrap">
        <div class="login-card">

            {{-- ── Logo ── --}}
            <div class="logo-wrap">
                @if(file_exists(public_path('assets/logos/logo 1.png')))
                    <img src="{{ asset('assets/logos/logo 1.png') }}"
                         alt="Delawala Management"
                         class="logo-img"
                         onerror="this.style.display='none';document.getElementById('logoFallback').style.display='block';">
                    <div id="logoFallback" style="display:none;" class="logo-text-wrap">
                        <div class="logo-script">Delawala</div>
                        <div class="logo-script-sub">Properties</div>
                    </div>
                @elseif(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}"
                         alt="Delawala Management"
                         class="logo-img"
                         onerror="this.style.display='none';document.getElementById('logoFallback').style.display='block';">
                    <div id="logoFallback" style="display:none;" class="logo-text-wrap">
                        <div class="logo-script">Delawala</div>
                        <div class="logo-script-sub">Properties</div>
                    </div>
                @elseif(file_exists(public_path('images/logo.jpeg')))
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Delawala Management" class="logo-img">
                @elseif(file_exists(public_path('assets/images/logo.png')))
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Delawala Management" class="logo-img">
                @else
                    <div class="logo-text-wrap">
                        <div class="logo-script">Delawala</div>
                        <div class="logo-script-sub">Properties</div>
                    </div>
                @endif
            </div>

            {{-- ── Flash error & success ── --}}
            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ── Form ── --}}
            <form method="POST" action="{{ route('login.submit') }}" novalidate>
                @csrf

                {{-- Hidden field: tells AuthController which table to authenticate against --}}
                <input type="hidden" name="login_type" id="loginType" value="admin">

                {{-- ── Admin / Firm Toggle ── --}}
                <div class="toggle-wrap" id="loginToggle">
                    <button type="button" class="toggle-btn tab-active" id="tabAdmin" onclick="switchTab('admin')">
                        <i class="fa-solid fa-user-shield"></i> Admin
                    </button>
                    <button type="button" class="toggle-btn tab-inactive" id="tabFirm" onclick="switchTab('firm')">
                        <i class="fa-solid fa-clipboard-list"></i> Firm
                    </button>
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            placeholder="Enter your email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            autocomplete="current-password"
                        >
                        <button type="button" class="pwd-toggle" id="pwdToggle"
                                aria-label="Toggle password visibility" tabindex="-1">
                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Remember + Change Password Link --}}
                <div class="form-row-extra">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    <a href="javascript:void(0);" onclick="openChangePwdModal()" class="forgot-link">
                        <i class="fa-solid fa-key" style="font-size: 11px;"></i> Change Password?
                    </a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                </button>

                <div class="login-alt-row">
                    <span>Forgot password or need an update?</span>
                    <a href="javascript:void(0);" onclick="openChangePwdModal()" class="login-alt-link">Change Password</a>
                </div>

            </form>

            <hr class="card-divider">

            <div class="card-footer">
                <p>&copy; {{ date('Y') }} DELAWALA GROUP. All Rights Reserved.<br>
                Designed &amp; Developed By
                <a href="https://techomaxsolution.com" target="_blank" rel="noopener noreferrer">Techomax Solution</a></p>
            </div>

        </div>{{-- /.login-card --}}
    </div>{{-- /.card-wrap --}}

    {{-- ════════════════════════════════════════
         CHANGE PASSWORD MODAL
    ════════════════════════════════════════ --}}
    <div class="modal-overlay {{ session('open_change_modal') ? 'active' : '' }}" id="changePwdModal" onclick="handleModalOverlayClick(event)">
        <div class="modal-card">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Change Password</span>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeChangePwdModal()" title="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            @if(session('error_change_pwd'))
                <div class="alert-error" style="margin-bottom: 14px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error_change_pwd') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.change-password') }}" id="publicChangePwdForm">
                @csrf

                <input type="hidden" name="account_type" id="modalAccountType" value="{{ old('account_type', 'admin') }}">

                {{-- Account Type Toggle --}}
                <div class="toggle-wrap" style="margin-bottom: 14px;">
                    <button type="button" class="toggle-btn {{ old('account_type', 'admin') === 'admin' ? 'tab-active' : 'tab-inactive' }}" id="modalTabAdmin" onclick="switchModalTab('admin')">
                        <i class="fa-solid fa-user-shield"></i> Admin
                    </button>
                    <button type="button" class="toggle-btn {{ old('account_type') === 'firm' ? 'tab-active' : 'tab-inactive' }}" id="modalTabFirm" onclick="switchModalTab('firm')">
                        <i class="fa-solid fa-clipboard-list"></i> Firm
                    </button>
                </div>

                {{-- Email Address --}}
                <div class="form-group">
                    <label class="form-label" for="modal_email">Account Email</label>
                    <div class="input-wrap">
                        <i class="fa-regular fa-envelope input-icon"></i>
                        <input
                            type="email"
                            name="email"
                            id="modal_email"
                            value="{{ old('email') }}"
                            placeholder="Enter your registered email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            required
                        >
                    </div>
                </div>

                {{-- Current Password --}}
                <div class="form-group">
                    <label class="form-label" for="modal_current_password">Current Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <input
                            type="password"
                            name="current_password"
                            id="modal_current_password"
                            placeholder="Enter your current password"
                            class="form-input"
                            required
                        >
                        <button type="button" class="pwd-toggle" onclick="toggleFieldVisibility('modal_current_password', 'modalCurrentEye')" tabindex="-1">
                            <i class="fa-regular fa-eye" id="modalCurrentEye"></i>
                        </button>
                    </div>
                </div>

                {{-- New Password --}}
                <div class="form-group">
                    <label class="form-label" for="modal_new_password">New Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-key input-icon"></i>
                        <input
                            type="password"
                            name="password"
                            id="modal_new_password"
                            placeholder="Min. 6 characters"
                            class="form-input"
                            oninput="checkModalPwdStrength()"
                            required
                        >
                        <button type="button" class="pwd-toggle" onclick="toggleFieldVisibility('modal_new_password', 'modalNewEye')" tabindex="-1">
                            <i class="fa-regular fa-eye" id="modalNewEye"></i>
                        </button>
                    </div>
                    <div class="strength-track">
                        <div class="strength-bar" id="modalStrengthBar"></div>
                    </div>
                    <div class="strength-text">
                        <span>Strength:</span>
                        <strong id="modalStrengthLabel" style="color: #94A3B8;">None</strong>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label class="form-label" for="modal_confirm_password">Confirm New Password</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-circle-check input-icon"></i>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="modal_confirm_password"
                            placeholder="Re-type new password"
                            class="form-input"
                            oninput="checkModalPwdMatch()"
                            required
                        >
                        <button type="button" class="pwd-toggle" onclick="toggleFieldVisibility('modal_confirm_password', 'modalConfirmEye')" tabindex="-1">
                            <i class="fa-regular fa-eye" id="modalConfirmEye"></i>
                        </button>
                    </div>
                    <div id="modalMatchLabel" style="font-size: 11px; margin-top: 4px; display: none;"></div>
                </div>

                {{-- Modal Actions --}}
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="button" class="modal-btn-cancel" onclick="closeChangePwdModal()">
                        Cancel
                    </button>
                    <button type="submit" class="modal-btn-submit">
                        <i class="fa-solid fa-check"></i> Update Password
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>{{-- /.page --}}

<script>
    // ── Tab switcher — also updates hidden login_type field ──
    function switchTab(tab) {
        const adminBtn   = document.getElementById('tabAdmin');
        const firmBtn    = document.getElementById('tabFirm');
        const loginType  = document.getElementById('loginType');
        if (tab === 'admin') {
            adminBtn.classList.replace('tab-inactive', 'tab-active');
            firmBtn.classList.replace('tab-active',   'tab-inactive');
            if (loginType) loginType.value = 'admin';
        } else {
            firmBtn.classList.replace('tab-inactive', 'tab-active');
            adminBtn.classList.replace('tab-active',  'tab-inactive');
            if (loginType) loginType.value = 'firm';
        }
    }

    // ── Password show / hide ──
    const pwdInput  = document.getElementById('password');
    const pwdToggle = document.getElementById('pwdToggle');
    const eyeIcon   = document.getElementById('eyeIcon');

    if (pwdToggle && pwdInput) {
        pwdToggle.addEventListener('click', function () {
            const hidden      = pwdInput.type === 'password';
            pwdInput.type     = hidden ? 'text' : 'password';
            eyeIcon.className = hidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    }

    // ── Modal Password show / hide helper ──
    function toggleFieldVisibility(fieldId, iconId) {
        const field = document.getElementById(fieldId);
        const icon  = document.getElementById(iconId);
        if (field && icon) {
            const isHidden = field.type === 'password';
            field.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        }
    }

    // ── Modal Open / Close ──
    function openChangePwdModal() {
        const modal = document.getElementById('changePwdModal');
        if (modal) {
            modal.classList.add('active');
            // Pre-fill email from login form if present
            const loginEmail = document.getElementById('email');
            const modalEmail = document.getElementById('modal_email');
            if (loginEmail && modalEmail && loginEmail.value && !modalEmail.value) {
                modalEmail.value = loginEmail.value;
            }
            // Sync active tab
            const loginType = document.getElementById('loginType');
            if (loginType && loginType.value === 'firm') {
                switchModalTab('firm');
            } else {
                switchModalTab('admin');
            }
        }
    }

    function closeChangePwdModal() {
        const modal = document.getElementById('changePwdModal');
        if (modal) {
            modal.classList.remove('active');
        }
    }

    function handleModalOverlayClick(event) {
        if (event.target === document.getElementById('changePwdModal')) {
            closeChangePwdModal();
        }
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeChangePwdModal();
        }
    });

    // ── Modal Tab Switcher ──
    function switchModalTab(tab) {
        const adminBtn = document.getElementById('modalTabAdmin');
        const firmBtn  = document.getElementById('modalTabFirm');
        const accountType = document.getElementById('modalAccountType');
        if (tab === 'admin') {
            adminBtn.classList.replace('tab-inactive', 'tab-active');
            firmBtn.classList.replace('tab-active',   'tab-inactive');
            if (accountType) accountType.value = 'admin';
        } else {
            firmBtn.classList.replace('tab-inactive', 'tab-active');
            adminBtn.classList.replace('tab-active',  'tab-inactive');
            if (accountType) accountType.value = 'firm';
        }
    }

    // ── Modal Password Strength ──
    function checkModalPwdStrength() {
        const val   = document.getElementById('modal_new_password').value;
        const bar   = document.getElementById('modalStrengthBar');
        const label = document.getElementById('modalStrengthLabel');

        let score = 0;
        if (val.length >= 6)  score += 25;
        if (val.length >= 10) score += 25;
        if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score += 25;
        if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score += 25;

        bar.style.width = score + '%';
        if (score === 0) {
            bar.style.backgroundColor = 'transparent';
            label.textContent = 'None';
            label.style.color = '#94A3B8';
        } else if (score <= 25) {
            bar.style.backgroundColor = '#EF4444';
            label.textContent = 'Weak';
            label.style.color = '#EF4444';
        } else if (score <= 50) {
            bar.style.backgroundColor = '#F59E0B';
            label.textContent = 'Fair';
            label.style.color = '#F59E0B';
        } else if (score <= 75) {
            bar.style.backgroundColor = '#3B82F6';
            label.textContent = 'Good';
            label.style.color = '#3B82F6';
        } else {
            bar.style.backgroundColor = '#10B981';
            label.textContent = 'Strong';
            label.style.color = '#10B981';
        }
        checkModalPwdMatch();
    }

    // ── Modal Password Match ──
    function checkModalPwdMatch() {
        const p1    = document.getElementById('modal_new_password').value;
        const p2    = document.getElementById('modal_confirm_password').value;
        const label = document.getElementById('modalMatchLabel');

        if (!p2) {
            label.style.display = 'none';
            return;
        }

        label.style.display = 'block';
        if (p1 === p2) {
            label.textContent = '✓ Passwords match';
            label.style.color = '#10B981';
        } else {
            label.textContent = '✗ Passwords do not match';
            label.style.color = '#EF4444';
        }
    }

    // Ensure CSRF token is fresh if navigating back via browser history/cache
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
</body>
</html>
