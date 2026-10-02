@extends('admin.layouts.app')
@section('title', 'Change Password')
@section('page-title', 'Change Password')

@section('content')
<style>
    .security-container {
        max-width: 1050px;
        margin: 0 auto;
    }

    /* --- Breadcrumb & Header --- */
    .sec-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .sec-title-group h2 {
        font-size: 24px;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .sec-title-group p {
        font-size: 13.5px;
        color: #94A3B8;
    }

    /* --- Grid Layout --- */
    .sec-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 24px;
        align-items: start;
    }
    @media(max-width: 900px) {
        .sec-grid {
            grid-template-columns: 1fr;
        }
    }

    /* --- Glass Cards --- */
    .sec-card {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(16px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 22px !important;
        padding: 30px;
        box-shadow: 0 14px 40px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.12) !important;
    }
    .sec-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .sec-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        background: rgba(99, 102, 241, 0.20);
        border: 1px solid rgba(99, 102, 241, 0.40);
        color: #818CF8;
    }
    .sec-card-header h3 {
        font-size: 16px;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0;
    }

    /* --- Form Elements --- */
    .form-group-sec {
        margin-bottom: 22px;
    }
    .form-label-sec {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12.5px;
        font-weight: 700;
        color: #CBD5E1;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .input-wrapper-sec {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-icon-left {
        position: absolute;
        left: 14px;
        color: #64748B;
        font-size: 14px;
        pointer-events: none;
        transition: color 0.2s ease;
    }
    .input-control-sec {
        width: 100%;
        background: rgba(15, 23, 42, 0.85) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-radius: 12px !important;
        padding: 12px 46px 12px 42px !important;
        font-size: 14px !important;
        color: #FFFFFF !important;
        outline: none !important;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3) !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .input-control-sec:focus {
        border-color: #6366F1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25), inset 0 2px 4px rgba(0, 0, 0, 0.3) !important;
    }
    .input-control-sec:focus + .input-icon-left,
    .input-wrapper-sec:focus-within .input-icon-left {
        color: #818CF8;
    }
    .toggle-pwd-btn {
        position: absolute;
        right: 12px;
        background: transparent;
        border: none;
        color: #64748B;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        font-size: 14px;
        transition: color 0.2s ease;
    }
    .toggle-pwd-btn:hover {
        color: #FFFFFF;
    }
    .error-feedback {
        font-size: 12px;
        color: #F87171;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* --- Password Strength Bar --- */
    .pwd-strength-container {
        margin-top: 8px;
    }
    .pwd-strength-bar {
        height: 5px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 6px;
        overflow: hidden;
        margin-bottom: 5px;
    }
    .pwd-strength-fill {
        height: 100%;
        width: 0%;
        border-radius: 6px;
        transition: width 0.3s ease, background-color 0.3s ease;
    }
    .pwd-strength-text {
        font-size: 11.5px;
        font-weight: 700;
        color: #94A3B8;
    }

    /* --- Buttons --- */
    .sec-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }
    .btn-update-pwd {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #4F46E5, #3730A3) !important;
        border: 1px solid #6366F1 !important;
        color: #FFFFFF !important;
        padding: 12px 28px !important;
        border-radius: 12px !important;
        font-size: 13.5px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        box-shadow: 0 4px 20px rgba(79, 70, 229, 0.4) !important;
        transition: all 0.22s ease !important;
    }
    .btn-update-pwd:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 26px rgba(79, 70, 229, 0.6) !important;
        background: linear-gradient(135deg, #6366F1, #4F46E5) !important;
    }
    .btn-cancel-pwd {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.14);
        color: #CBD5E1 !important;
        padding: 12px 22px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s ease;
    }
    .btn-cancel-pwd:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #FFFFFF !important;
    }

    /* --- Profile Card --- */
    .user-profile-badge-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.10);
        border-radius: 16px;
        margin-bottom: 22px;
    }
    .user-big-avatar {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #3B82F6, #1D4ED8);
        border: 2px solid rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
        font-size: 20px;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
        flex-shrink: 0;
    }
    .user-big-info h4 {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0 0 4px 0;
    }
    .user-big-info p {
        font-size: 12px;
        color: #94A3B8;
        margin: 0 0 6px 0;
    }
    .user-role-badge {
        display: inline-block;
        font-size: 10.5px;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 20px;
        background: rgba(16, 185, 129, 0.18);
        border: 1px solid rgba(16, 185, 129, 0.35);
        color: #34D399;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="security-container">
    <!-- Header -->
    <div class="sec-header">
        <div class="sec-title-group">
            <h2>
                <i class="fa-solid fa-shield-halved" style="color:#6366F1;"></i>
                Account Security &amp; Password
            </h2>
            <p>Update your login credentials to keep your administrator account safe and secure.</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn-cancel-pwd">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Flash Alerts -->
    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34D399; border-radius: 14px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); color: #F87171; border-radius: 14px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-exclamation" style="font-size: 18px;"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="sec-grid">
        <!-- Main Form Column -->
        <div class="sec-card">
            <div class="sec-card-header">
                <div class="sec-card-icon">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h3>Change Password</h3>
                    <p style="font-size: 12px; color: #94A3B8; margin: 0;">Enter your existing password and choose a strong new one.</p>
                </div>
            </div>

            <form action="{{ route('change-password.update') }}" method="POST" id="changePasswordForm">
                @csrf

                <!-- Current Password -->
                <div class="form-group-sec">
                    <label class="form-label-sec" for="current_password">
                        <span>Current Password</span>
                        <span style="color: #F87171;">*</span>
                    </label>
                    <div class="input-wrapper-sec">
                        <i class="fa-solid fa-lock input-icon-left"></i>
                        <input type="password" name="current_password" id="current_password" class="input-control-sec @error('current_password') is-invalid @enderror" placeholder="Enter your current password" required autocomplete="current-password" oninput="validatePasswordDiff()">
                        <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('current_password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <div class="error-feedback">
                            <i class="fa-solid fa-circle-xmark"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="form-group-sec">
                    <label class="form-label-sec" for="password">
                        <span>New Password</span>
                        <span style="color: #F87171;">* (Min. 6 chars)</span>
                    </label>
                    <div class="input-wrapper-sec">
                        <i class="fa-solid fa-key input-icon-left"></i>
                        <input type="password" name="password" id="password" class="input-control-sec @error('password') is-invalid @enderror" placeholder="Enter new password" required autocomplete="new-password" oninput="checkPasswordStrength(this.value)">
                        <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('password', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <div id="samePasswordFeedback" style="font-size: 11.5px; font-weight: 700; color: #F87171; margin-top: 5px; display: none;"></div>
                    <div class="pwd-strength-container" id="strengthContainer" style="display: none;">
                        <div class="pwd-strength-bar">
                            <div class="pwd-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="pwd-strength-text" id="strengthText">Strength: Weak</span>
                    </div>
                    @error('password')
                        <div class="error-feedback">
                            <i class="fa-solid fa-circle-xmark"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="form-group-sec">
                    <label class="form-label-sec" for="password_confirmation">
                        <span>Confirm New Password</span>
                        <span style="color: #F87171;">*</span>
                    </label>
                    <div class="input-wrapper-sec">
                        <i class="fa-solid fa-circle-check input-icon-left"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="input-control-sec" placeholder="Re-enter new password" required autocomplete="new-password" oninput="checkPasswordMatch()">
                        <button type="button" class="toggle-pwd-btn" onclick="togglePasswordVisibility('password_confirmation', this)">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    <div id="matchFeedback" style="font-size: 11.5px; font-weight: 700; margin-top: 5px; display: none;"></div>
                </div>

                <!-- Action Buttons -->
                <div class="sec-actions">
                    <button type="submit" class="btn-update-pwd" id="submitBtn">
                        <i class="fa-solid fa-lock"></i> Update Password
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-cancel-pwd">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar / Security Info Column -->
        <div>
            <!-- Account Badge Card -->
            <div class="sec-card" style="padding: 24px;">
                <div class="user-profile-badge-card">
                    <div class="user-big-avatar">
                        {{ strtoupper(substr($currentUser->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="user-big-info">
                        <h4>{{ $currentUser->name ?? 'Admin User' }}</h4>
                        <p><i class="fa-solid fa-envelope"></i> {{ $currentUser->email ?? '-' }}</p>
                        <span class="user-role-badge">
                            <i class="fa-solid fa-shield"></i> {{ $currentUser->role ?? 'Administrator' }}
                        </span>
                    </div>
                </div>

                <div class="summary-row" style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.06);">
                    <span style="font-size: 12px; color: #94A3B8;"><i class="fa-solid fa-circle-dot" style="color:#10B981;"></i> Account Status</span>
                    <span style="font-size: 12.5px; font-weight: 700; color: #34D399;">Active &amp; Verified</span>
                </div>
                <div class="summary-row" style="padding: 8px 0;">
                    <span style="font-size: 12px; color: #94A3B8;"><i class="fa-solid fa-clock"></i> Today's Date</span>
                    <span style="font-size: 12.5px; font-weight: 700; color: #E2E8F0;">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function validatePasswordDiff() {
    const current = document.getElementById('current_password').value;
    const next = document.getElementById('password').value;
    const sameFeedback = document.getElementById('samePasswordFeedback');

    if (current && next && current === next) {
        sameFeedback.style.display = 'block';
        sameFeedback.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> New password cannot be the same as your current password!';
        return false;
    } else {
        sameFeedback.style.display = 'none';
        return true;
    }
}

function checkPasswordStrength(password) {
    const container = document.getElementById('strengthContainer');
    const fill = document.getElementById('strengthFill');
    const text = document.getElementById('strengthText');

    validatePasswordDiff();

    if (!password || password.length === 0) {
        container.style.display = 'none';
        return;
    }

    container.style.display = 'block';
    let strength = 0;

    if (password.length >= 6) strength += 25;
    if (password.length >= 10) strength += 25;
    if (/[A-Z]/.test(password)) strength += 15;
    if (/[0-9]/.test(password)) strength += 15;
    if (/[^A-Za-z0-9]/.test(password)) strength += 20;

    fill.style.width = strength + '%';

    if (strength < 40) {
        fill.style.backgroundColor = '#EF4444';
        text.style.color = '#F87171';
        text.textContent = 'Strength: Weak';
    } else if (strength < 75) {
        fill.style.backgroundColor = '#F59E0B';
        text.style.color = '#FBBF24';
        text.textContent = 'Strength: Moderate';
    } else {
        fill.style.backgroundColor = '#10B981';
        text.style.color = '#34D399';
        text.textContent = 'Strength: Strong & Secure';
    }

    checkPasswordMatch();
}

function checkPasswordMatch() {
    const pwd = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const feedback = document.getElementById('matchFeedback');

    if (!confirm || confirm.length === 0) {
        feedback.style.display = 'none';
        return;
    }

    feedback.style.display = 'block';
    if (pwd === confirm) {
        feedback.style.color = '#34D399';
        feedback.innerHTML = '<i class="fa-solid fa-circle-check"></i> Passwords match perfectly!';
    } else {
        feedback.style.color = '#F87171';
        feedback.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> Passwords do not match yet.';
    }
}

document.getElementById('changePasswordForm')?.addEventListener('submit', function(e) {
    const current = document.getElementById('current_password').value;
    const next = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;

    if (current && next && current === next) {
        e.preventDefault();
        validatePasswordDiff();
        document.getElementById('password').focus();
        return false;
    }

    if (next !== confirm) {
        e.preventDefault();
        checkPasswordMatch();
        document.getElementById('password_confirmation').focus();
        return false;
    }
});
</script>
@endsection
