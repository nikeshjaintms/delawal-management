@extends('admin.layouts.app')
@section('title','Edit Firm')
@section('page-title','Firm Management')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-primary-custom i,.btn-secondary-custom i{font-size:14px;line-height:1}
.form-action-buttons{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
/* Layout */
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.form-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
@media(max-width:768px){.form-grid,.form-grid-3{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:7px}
.form-label span{color:#EF4444}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:border-color .18s,box-shadow .18s;background:#fff}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
textarea.form-control{resize:vertical;min-height:80px}
.text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500}
.form-hint{font-size:11.5px;color:var(--text-secondary);margin-top:4px}
.logo-preview, .logo-current{width:80px;height:80px;object-fit:cover;border-radius:10px;border:1px solid var(--border-color)}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Firm</h2>
        <p>Update company profile, credentials, and settings.</p>
    </div>
    <a href="{{ route('firm-master.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('firm-master.update', $firm) }}" enctype="multipart/form-data">
@csrf @method('PUT')

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-building"></i> Basic Information</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Firm Name <span>*</span></label>
            <input type="text" name="firm_name" value="{{ old('firm_name', $firm->firm_name) }}" class="form-control @error('firm_name') is-invalid @enderror" required>
            @error('firm_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Owner Name <span>*</span></label>
            <input type="text" name="owner_name" value="{{ old('owner_name', $firm->owner_name) }}" class="form-control @error('owner_name') is-invalid @enderror" required>
            @error('owner_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Email Address <span>*</span></label>
            <input type="email" name="email" value="{{ old('email', $firm->email) }}" class="form-control @error('email') is-invalid @enderror" required>
            @error('email')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div style="position: relative;">
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current" style="padding-right: 40px;">
                <button type="button" class="btn-toggle-pwd" data-target="password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748B;">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            @error('password')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <div style="position: relative;">
                <input type="password" name="confirm_password" id="confirm_password" class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Leave blank to keep current" style="padding-right: 40px;">
                <button type="button" class="btn-toggle-pwd" data-target="confirm_password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748B;">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            @error('confirm_password')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Mobile Number <span>*</span></label>
            <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $firm->mobile) }}" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" required>
            @error('mobile')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Alternate Mobile</label>
            <input type="text" name="alternate_mobile" id="alternate_mobile" value="{{ old('alternate_mobile', $firm->alternate_mobile) }}" class="form-control @error('alternate_mobile') is-invalid @enderror" placeholder="Enter 10-digit alternate number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
            @error('alternate_mobile')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active"   {{ old('status',$firm->status)=='active'   ? 'selected':'' }}>Active</option>
                <option value="inactive" {{ old('status',$firm->status)=='inactive' ? 'selected':'' }}>Inactive</option>
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-location-dot"></i> Address Details</div>
    <div class="form-group" style="margin-bottom:18px">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $firm->address) }}</textarea>
        @error('address')<div class="text-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-grid-3">
        <div class="form-group">
            <label class="form-label">City</label>
            <input type="text" name="city" value="{{ old('city', $firm->city) }}" class="form-control @error('city') is-invalid @enderror">
            @error('city')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">State</label>
            <input type="text" name="state" value="{{ old('state', $firm->state) }}" class="form-control @error('state') is-invalid @enderror">
            @error('state')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Pincode</label>
            <input type="text" name="pincode" value="{{ old('pincode', $firm->pincode) }}" class="form-control @error('pincode') is-invalid @enderror" maxlength="10">
            @error('pincode')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> GST &amp; Tax Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">GST Number</label>
            <input type="text" name="gst_no" value="{{ old('gst_no', $firm->gst_no) }}" class="form-control @error('gst_no') is-invalid @enderror" style="text-transform:uppercase">
            @error('gst_no')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">PAN Number</label>
            <input type="text" name="pan_number" value="{{ old('pan_number', $firm->pan_number) }}" class="form-control @error('pan_number') is-invalid @enderror" style="text-transform:uppercase">
            @error('pan_number')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Firm Logo</label>
            @if($firm->firm_logo)
                <div style="margin-bottom:10px;display:flex;align-items:center;gap:12px">
                    <img src="{{ Storage::url($firm->firm_logo) }}" class="logo-current" alt="Current Logo">
                    <span style="font-size:12px;color:var(--text-secondary)">Current logo — upload new to replace</span>
                </div>
            @endif
            <input type="file" name="firm_logo" id="firm_logo" class="form-control @error('firm_logo') is-invalid @enderror" accept="image/*" onchange="previewLogo(this)">
            <div class="form-hint">JPEG, PNG, WebP — Max 2 MB</div>
            <img id="logoPreview" class="logo-preview" style="margin-top:10px;display:none;" alt="New Preview">
            @error('firm_logo')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-landmark"></i> Bank Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Bank Name</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $firm->bank_name) }}" class="form-control @error('bank_name') is-invalid @enderror">
            @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Account Number</label>
            <input type="text" name="account_number" value="{{ old('account_number', $firm->account_number) }}" class="form-control @error('account_number') is-invalid @enderror">
            @error('account_number')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">IFSC Code</label>
            <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $firm->ifsc_code) }}" class="form-control @error('ifsc_code') is-invalid @enderror" style="text-transform:uppercase">
            @error('ifsc_code')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Branch Name</label>
            <input type="text" name="branch_name" value="{{ old('branch_name', $firm->branch_name) }}" class="form-control @error('branch_name') is-invalid @enderror">
            @error('branch_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Update Firm</button>
    <a href="{{ route('firm-master.index') }}" class="btn-secondary-custom">Cancel</a>
</div>
</form>

<script>
function previewLogo(input) {
    const preview = document.getElementById('logoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).ready(function() {
    $('.btn-toggle-pwd').on('click', function() {
        const targetId = $(this).data('target');
        const targetInput = $('#' + targetId);
        const icon = $(this).find('i');
        
        if (targetInput.attr('type') === 'password') {
            targetInput.attr('type', 'text');
            icon.removeClass('fa-regular fa-eye').addClass('fa-solid fa-eye-slash');
        } else {
            targetInput.attr('type', 'password');
            icon.removeClass('fa-solid fa-eye-slash').addClass('fa-regular fa-eye');
        }
    });
});
</script>
@endsection
