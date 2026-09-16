@extends('admin.layouts.app')
@section('title', 'Edit Seller: ' . $seller->name)
@section('page-title', 'Edit Seller')
@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 28px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    max-width: 980px; margin: 0 auto;
}

.section-divider {
    font-size: 13px; font-weight: 800; color: #FBBF24 !important; text-transform: uppercase;
    letter-spacing: 0.8px; margin: 24px 0 16px 0; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.section-divider:first-of-type { margin-top: 0; }

.form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px; }
.form-grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px; }
@media (max-width: 768px) { .form-grid-3, .form-grid-2 { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 0; }
.form-label { display: block; font-size: 12.5px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; }
.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    color: #FFFFFF !important; font-size: 13.5px; outline: none; transition: all .2s ease;
    box-sizing: border-box;
}
.form-control:focus { border-color: #F59E0B !important; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25) !important; }
.text-danger { color: #F87171; font-size: 11.5px; margin-top: 4px; display: block; }

.btn-submit {
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%) !important;
    color: #FFFFFF !important; font-weight: 700; padding: 11px 26px;
    border-radius: 12px; font-size: 14px; text-decoration: none !important;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.2) !important;
    box-shadow: 0 4px 18px rgba(245, 158, 11, 0.38); cursor: pointer; transition: all .25s ease;
}
.btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(245, 158, 11, 0.52); }
.btn-cancel {
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important;
    font-weight: 600; padding: 11px 22px; border-radius: 12px; font-size: 14px;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 6px;
    border: 1px solid rgba(255, 255, 255, 0.15) !important; transition: all .2s ease;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Seller / Landowner</h2>
        <p>Updating profile for: <strong style="color: #FCD34D;">{{ $seller->name }}</strong></p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('sellers.show', $seller->id) }}" class="btn-cancel"><i class="fa-solid fa-eye"></i> View Profile</a>
        <a href="{{ route('sellers.index') }}" class="btn-cancel"><i class="fa-solid fa-arrow-left"></i> Back to Sellers</a>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('sellers.update', $seller->id) }}">
        @csrf
        @method('PUT')

        @if(isset($firms) && $firms->isNotEmpty() && auth()->user() && auth()->user()->isAdmin())
            <div class="form-grid-2" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-building"></i> Linked Firm</label>
                    <select name="firm_id" class="form-control">
                        <option value="">— Select Firm (Global / All) —</option>
                        @foreach($firms as $f)
                            <option value="{{ $f->id }}" {{ old('firm_id', $seller->firm_id) == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <!-- 1. PERSONAL & CONTACT DETAILS -->
        <div class="section-divider"><i class="fa-solid fa-user"></i> 1. Personal &amp; Contact Details</div>
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Seller Name <span style="color:#F87171;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $seller->name) }}" class="form-control" required>
                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Seller Type</label>
                <select name="seller_type" class="form-control">
                    <option value="individual" {{ old('seller_type', $seller->seller_type) === 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="joint_owner" {{ old('seller_type', $seller->seller_type) === 'joint_owner' ? 'selected' : '' }}>Joint Owner</option>
                    <option value="organization" {{ old('seller_type', $seller->seller_type) === 'organization' ? 'selected' : '' }}>Company / Firm</option>
                </select>
                @error('seller_type') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Contact Person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $seller->contact_person) }}" class="form-control">
                @error('contact_person') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile', $seller->mobile) }}" class="form-control">
                @error('mobile') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Alternate Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $seller->phone) }}" class="form-control">
                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $seller->email) }}" class="form-control">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 2. LEGAL & IDENTITY PROOFS -->
        <div class="section-divider"><i class="fa-solid fa-id-card"></i> 2. Legal &amp; Identity Details</div>
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan_no" value="{{ old('pan_no', $seller->pan_no) }}" class="form-control" style="text-transform: uppercase;">
                @error('pan_no') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Aadhaar Number</label>
                <input type="text" name="aadhaar_no" value="{{ old('aadhaar_no', $seller->aadhaar_no) }}" class="form-control">
                @error('aadhaar_no') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">GSTIN (if applicable)</label>
                <input type="text" name="gst_no" value="{{ old('gst_no', $seller->gst_no) }}" class="form-control" style="text-transform: uppercase;">
                @error('gst_no') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 3. BANK PAYMENT COORDINATES -->
        <div class="section-divider"><i class="fa-solid fa-building-columns"></i> 3. Bank Payment Details</div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" value="{{ old('bank_name', $seller->bank_name) }}" class="form-control">
                @error('bank_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Bank Account Number</label>
                <input type="text" name="account_number" value="{{ old('account_number', $seller->account_number) }}" class="form-control">
                @error('account_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">IFSC Code</label>
                <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $seller->ifsc_code) }}" class="form-control" style="text-transform: uppercase;">
                @error('ifsc_code') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Branch Name / City</label>
                <input type="text" name="branch_name" value="{{ old('branch_name', $seller->branch_name) }}" class="form-control">
                @error('branch_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- 4. ADDRESS & REMARKS -->
        <div class="section-divider"><i class="fa-solid fa-location-dot"></i> 4. Address &amp; Status</div>
        <div class="form-grid-3">
            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" value="{{ old('city', $seller->city) }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" value="{{ old('state', $seller->state) }}" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode', $seller->pincode) }}" class="form-control">
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 16px;">
            <label class="form-label">Full Address / Village</label>
            <textarea name="address" rows="2" class="form-control">{{ old('address', $seller->address) }}</textarea>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Remarks / Land Deed Notes</label>
                <textarea name="remarks" rows="2" class="form-control">{{ old('remarks', $seller->remarks) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', $seller->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $seller->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 26px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 12px;">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Update Seller Profile</button>
            <a href="{{ route('sellers.index') }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection
