<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Firm & Financial Year — Delawala Management</title>
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

        /* Dark overlay */
        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                90deg,
                rgba(10, 20, 45, 0.82) 0%,
                rgba(10, 20, 45, 0.58) 38%,
                rgba(10, 20, 45, 0.22) 68%,
                rgba(0, 0, 0, 0.05) 100%
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
           GLASS CARD
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

        /* ════════════════════════════════════════
           FORM LABELS
         ════════════════════════════════════════ */
        .form-group { margin-bottom: 22px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.95);
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        /* ════════════════════════════════════════
           INPUTS & SELECTS
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

        .form-select {
            width: 100%;
            padding: 14px 46px;
            background: rgba(20, 40, 80, 0.65);
            border: 1.5px solid rgba(255, 255, 255, 0.20);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #FFFFFF;
            outline: none;
            transition: border-color 0.22s, box-shadow 0.22s, background 0.22s;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
        }
        /* Custom arrow for select */
        .select-arrow {
            position: absolute;
            right: 18px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            pointer-events: none;
        }
        .form-select option {
            background: #0f1c3f;
            color: #ffffff;
        }
        .form-select:focus {
            border-color: rgba(59,130,246,0.80);
            background: rgba(20, 40, 90, 0.75);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }

        /* ════════════════════════════════════════
           CONTINUE BUTTON
         ════════════════════════════════════════ */
        .btn-continue {
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
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow:
                0 10px 34px rgba(37,99,235,0.60),
                0 2px 8px rgba(37,99,235,0.35);
        }
        .btn-continue:active { transform: translateY(0); }
        .btn-continue::after {
            content: '';
            position: absolute;
            top: 0; left: -110%; width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.22), transparent);
            transition: left 0.55s ease;
        }
        .btn-continue:hover::after { left: 160%; }

        .card-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.12);
            margin: 24px 0 16px;
        }
        .card-footer {
            text-align: center;
            font-size: 12px;
            color: rgba(255,255,255,0.80);
        }

        @media (max-width: 640px) {
            .card-wrap {
                margin-left: auto;
                margin-right: auto;
                max-width: 100%;
                padding: 0 16px;
            }
            .login-card { padding: 26px 24px 22px; }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card-wrap">
        <div class="login-card">

            {{-- ── Logo ── --}}
            <div class="logo-wrap">
                <div class="logo-text-wrap">
                    <div class="logo-script">Delawala</div>
                    <div class="logo-script-sub">Management ERP</div>
                </div>
            </div>

            <h3 style="color:#ffffff; font-size: 18px; font-weight:700; text-align:center; margin-bottom: 24px;">
                Select Firm &amp; Financial Year
            </h3>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('firm-selection.submit') }}">
                @csrf

                {{-- ── Firm Selector ── --}}
                <div class="form-group">
                    <label class="form-label">Select Firm Name</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-building input-icon"></i>
                        <select name="firm_id" class="form-select" required>
                            @foreach($firms as $f)
                                <option value="{{ $f->id }}" {{ count($firms) === 1 || old('firm_id') == $f->id ? 'selected' : '' }}>
                                    {{ $f->firm_name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                {{-- ── Financial Year Selector ── --}}
                <div class="form-group">
                    <label class="form-label">Select Financial Year</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-calendar-days input-icon"></i>
                        <select name="financial_year_id" class="form-select" required>
                            @foreach($financialYears as $fy)
                                <option value="{{ $fy->id }}" {{ count($financialYears) === 1 || old('financial_year_id') == $fy->id || $fy->is_active ? 'selected' : '' }}>
                                    {{ $fy->year_name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <button type="submit" class="btn-continue" style="margin-top: 28px;">
                    <span>Continue to Dashboard</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top: 12px;">
                @csrf
                <button type="submit" class="btn-continue" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #FECACA; box-shadow: none;">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Back to Login</span>
                </button>
            </form>

            <hr class="card-divider">

            <div class="card-footer">
                &copy; {{ date('Y') }} Delawala Management ERP
            </div>

        </div>
    </div>
</div>

</body>
</html>
