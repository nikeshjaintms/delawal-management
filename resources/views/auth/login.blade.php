<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Delawala Management</title>
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
            overflow: hidden;
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
            max-width: 456px;
            animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)   scale(1); }
        }

        /* ════════════════════════════════════════
           GLASS LOGIN CARD
        ════════════════════════════════════════ */
        .login-card {
            background: rgba(255, 255, 255, 0.13);
            backdrop-filter: blur(22px) saturate(160%);
            -webkit-backdrop-filter: blur(22px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 22px;
            padding: 32px 40px 28px;
            box-shadow:
                0 28px 72px rgba(0, 0, 0, 0.32),
                0 4px 18px rgba(0, 0, 0, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.26);
            position: relative;
        }

        /* Blue top accent stripe */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 32px; right: 32px;
            height: 2px;
            background: linear-gradient(90deg,
                transparent 0%,
                rgba(59,130,246,0.5) 20%,
                #3B82F6 50%,
                rgba(59,130,246,0.5) 80%,
                transparent 100%
            );
            border-radius: 0 0 4px 4px;
        }

        /* ════════════════════════════════════════
           LOGO
        ════════════════════════════════════════ */
        .logo-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 24px;
        }

        /* Image logo */
        .logo-img {
            max-height: 64px;
            max-width: 200px;
            object-fit: contain;
            filter: brightness(0) invert(1);
            opacity: 0.95;
        }

        /* Script fallback logo */
        .logo-script {
            font-family: 'Pinyon Script', cursive;
            font-size: 42px;
            color: #fff;
            letter-spacing: 1px;
            text-shadow: 0 2px 12px rgba(0,0,0,0.35);
            line-height: 1;
        }
        .logo-script-sub {
            font-family: 'Inter', sans-serif;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 4px;
            color: rgba(200,220,255,0.85);
            text-transform: uppercase;
            text-align: center;
            margin-top: 2px;
        }
        .logo-text-wrap { text-align: center; }

        /* ════════════════════════════════════════
           ERROR ALERT
        ════════════════════════════════════════ */
        .alert-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(239, 68, 68, 0.55);
            border-left: 3.5px solid #EF4444;
            border-radius: 10px;
            padding: 11px 14px;
            margin-bottom: 20px;
            font-size: 13.5px;
            color: #FECACA;
            animation: shake 0.4s both;
        }
        .alert-error i { font-size: 14px; color: #F87171; flex-shrink: 0; }
        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%      { transform: translateX(-5px); }
            40%      { transform: translateX(5px); }
            60%      { transform: translateX(-3px); }
            80%      { transform: translateX(3px); }
        }

        /* ════════════════════════════════════════
           ADMIN / FIRM TOGGLE
        ════════════════════════════════════════ */
        .toggle-wrap {
            display: flex;
            gap: 0;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 26px;
        }

        .toggle-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 8px;
            border: none;
            border-radius: 9px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.25s, box-shadow 0.25s, color 0.25s, transform 0.15s;
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
            background: rgba(255, 255, 255, 0.09);
            color: rgba(255, 255, 255, 0.85);
        }

        /* ACTIVE tab — blue */
        .toggle-btn.tab-active {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 55%, #3B82F6 100%);
            color: #FFFFFF;
            box-shadow:
                0 4px 16px rgba(37, 99, 235, 0.52),
                0 1px 4px rgba(37, 99, 235, 0.28),
                inset 0 1px 0 rgba(255, 255, 255, 0.16);
            transform: translateY(-1px);
        }

        /* Shimmer on active */
        .toggle-btn.tab-active::after {
            content: '';
            position: absolute;
            top: 0; left: -80%; width: 50%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: tabShimmer 2.6s infinite;
        }
        @keyframes tabShimmer {
            0%   { left: -80%; }
            60%, 100% { left: 160%; }
        }

        /* ════════════════════════════════════════
           FORM LABELS
        ════════════════════════════════════════ */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        /* ════════════════════════════════════════
           INPUTS
        ════════════════════════════════════════ */
        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 15px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.45);
            font-size: 14px;
            pointer-events: none;
            transition: color 0.2s;
            z-index: 2;
        }
        .input-wrap:focus-within .input-icon { color: rgba(200,220,255,0.9); }

        .form-input {
            width: 100%;
            padding: 14px 46px;
            background: rgba(20, 40, 80, 0.52);
            border: 1.5px solid rgba(255, 255, 255, 0.20);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            outline: none;
            transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
            caret-color: #93C5FD;
        }
        .form-input::placeholder {
            color: rgba(255,255,255,0.32);
            font-size: 13.5px;
        }
        .form-input:focus {
            border-color: rgba(59,130,246,0.80);
            background: rgba(20, 40, 90, 0.62);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }
        .form-input.is-invalid { border-color: rgba(239,68,68,0.65); }

        /* Autofill */
        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgba(20,40,80,0.75) inset;
            -webkit-text-fill-color: #FFFFFF;
            caret-color: #FFFFFF;
            border-color: rgba(255,255,255,0.4);
        }

        /* Eye toggle */
        .pwd-toggle {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer;
            color: rgba(255,255,255,0.45);
            font-size: 15px;
            padding: 4px;
            transition: color 0.2s;
            z-index: 2;
            display: flex; align-items: center;
        }
        .pwd-toggle:hover { color: rgba(200,220,255,0.9); }

        /* Field errors */
        .field-error {
            font-size: 12px; color: #FECACA;
            margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
        }
        .field-error i { font-size: 11px; }

        /* ════════════════════════════════════════
           REMEMBER + FORGOT ROW
        ════════════════════════════════════════ */
        .form-row-extra {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer;
            font-size: 13px; font-weight: 500;
            color: rgba(255, 255, 255, 0.90);
            user-select: none;
        }
        .remember-label input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: #3B82F6; cursor: pointer;
        }
        .forgot-link {
            font-size: 13px; color: #FFFFFF;
            text-decoration: none; font-weight: 600;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #93C5FD; text-decoration: underline; }

        /* ════════════════════════════════════════
           SIGN IN BUTTON
        ════════════════════════════════════════ */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 55%, #3B82F6 100%);
            color: #FFFFFF;
            font-size: 15px; font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none; border-radius: 10px;
            cursor: pointer; letter-spacing: 0.5px;
            position: relative; overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow:
                0 4px 20px rgba(37,99,235,0.52),
                0 1px 6px rgba(37,99,235,0.30);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow:
                0 10px 34px rgba(37,99,235,0.60),
                0 2px 8px rgba(37,99,235,0.35);
        }
        .btn-login:active { transform: translateY(0); }
        /* Shimmer sweep */
        .btn-login::after {
            content: '';
            position: absolute;
            top: 0; left: -110%; width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.22), transparent);
            transition: left 0.55s ease;
        }
        .btn-login:hover::after { left: 160%; }

        /* ════════════════════════════════════════
           DIVIDER + FOOTER
        ════════════════════════════════════════ */
        .card-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.12);
            margin: 20px 0 14px;
        }
        .card-footer {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.80);
            line-height: 1.65;
        }
        .card-footer a {
            color: #60A5FA;
            text-decoration: none; font-weight: 600;
            transition: color 0.2s;
        }
        .card-footer a:hover { color: #93C5FD; text-decoration: underline; }

        /* ════════════════════════════════════════
           RESPONSIVE
        ════════════════════════════════════════ */
        @media (max-width: 640px) {
            .card-wrap {
                margin-left: auto;
                margin-right: auto;
                max-width: 100%;
                padding: 0 16px;
            }
            .login-card { padding: 26px 24px 22px; }
        }
        @media (max-width: 400px) {
            .login-card { padding: 20px 18px 18px; border-radius: 18px; }
            .form-input { padding: 12px 44px; }
        }
    </style>
</head>
<body>

<div class="page">

    <div class="card-wrap">
        <div class="login-card">

            {{-- ── Logo ── --}}
            <div class="logo-wrap">
                @if(file_exists(public_path('images/logo.png')))
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

            {{-- ── Flash error ── --}}
            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
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

                {{-- Remember + Forgot --}}
                <div class="form-row-extra">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <span>Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Sign In
                </button>

            </form>

            <hr class="card-divider">

            <div class="card-footer">
                <p>&copy; {{ date('Y') }} DELAWALA GROUP. All Rights Reserved.<br>
                Designed &amp; Developed By
                <a href="https://techomaxsolution.com" target="_blank" rel="noopener noreferrer">Techomax Solution</a></p>
            </div>

        </div>{{-- /.login-card --}}
    </div>{{-- /.card-wrap --}}

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
</script>
</body>
</html>
