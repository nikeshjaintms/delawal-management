<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Unauthorized | Delawala Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #F1F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 40px rgba(0,0,0,0.10);
            padding: 60px 48px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            border: 1px solid #E2E8F0;
        }
        .error-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(239,68,68,0.12) 0%, rgba(239,68,68,0.06) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
            border: 2px solid rgba(239,68,68,0.15);
        }
        .error-icon i {
            font-size: 38px;
            color: #EF4444;
        }
        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: #EF4444;
            line-height: 1;
            margin-bottom: 12px;
            letter-spacing: -2px;
        }
        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 12px;
        }
        .error-message {
            font-size: 14.5px;
            color: #64748B;
            line-height: 1.65;
            margin-bottom: 36px;
        }
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: #fff;
            padding: 11px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(59,130,246,0.3);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59,130,246,0.4);
        }
        .btn-secondary {
            background: transparent;
            color: #64748B;
            padding: 11px 26px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            border: 1.5px solid #E2E8F0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: #0F172A;
        }
        .divider {
            width: 48px;
            height: 3px;
            background: linear-gradient(135deg, #3B82F6, #8B5CF6);
            border-radius: 4px;
            margin: 20px auto 28px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fa-solid fa-shield-xmark"></i>
        </div>
        <div class="error-code">403</div>
        <div class="divider"></div>
        <div class="error-title">Access Denied</div>
        <div class="error-message">
            You don't have permission to access this page.<br>
            Please contact your administrator if you believe this is an error.
        </div>
        <div class="error-actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Go Back
            </a>
            <a href="{{ route('dashboard') }}" class="btn-primary">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </div>
    </div>
</body>
</html>
