@extends('admin.layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Tenant Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
    max-width: 820px; margin-left: auto; margin-right: auto;
}

.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media (max-width: 576px) { .form-row { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
.form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
textarea.form-control { resize: vertical; min-height: 100px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

.current-doc-link {
    display: inline-flex; align-items: center; gap: 6px; color: #60A5FA !important;
    font-size: 13px; font-weight: 700; text-decoration: none !important; margin-top: 6px;
}
.current-doc-link:hover { color: #93C5FD !important; text-decoration: underline !important; }

/* Select2 Glass Styling Overrides */
.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    background-color: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important; min-height: 42px !important; padding: 4px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(37, 99, 235, 0.35) !important;
    border: 1px solid rgba(59, 130, 246, 0.50) !important;
    color: #FFFFFF !important; border-radius: 6px !important; font-weight: 600; padding: 3px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #F87171 !important; margin-right: 6px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #FFFFFF !important; line-height: 32px !important;
}
.select2-dropdown { background-color: #101622 !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }
.select2-results__option { color: #CBD5E1 !important; }
.select2-results__option--highlighted[aria-selected] { background-color: #2563EB !important; color: #FFFFFF !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); font-family: inherit;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Tenant</h2>
        <p>Update tenant details — <strong>{{ $tenant->name }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('tenants.update', $tenant->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.components.firm-select', ['model' => $tenant])

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="name">Tenant Name <span>*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $tenant->name) }}" class="form-control" placeholder="Enter tenant name" required>
                @error('name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Primary Mobile <span>*</span></label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $tenant->mobile) }}" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" required>
                @error('mobile') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="alternate_mobile">Alternate Mobile</label>
                <input type="text" name="alternate_mobile" id="alternate_mobile" value="{{ old('alternate_mobile', $tenant->alternate_mobile) }}" class="form-control @error('alternate_mobile') is-invalid @enderror" placeholder="Optional alternate contact">
                @error('alternate_mobile') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $tenant->email) }}" class="form-control" placeholder="Enter email address">
                @error('email') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="occupation">Occupation / Profession</label>
                <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $tenant->occupation) }}" class="form-control @error('occupation') is-invalid @enderror" placeholder="Job, Business, Company Name, etc.">
                @error('occupation') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $tenant->city) }}" class="form-control" placeholder="Enter city name">
                @error('city') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="address">Current Address</label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter physical address">{{ old('address', $tenant->address) }}</textarea>
                @error('address') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="permanent_address">Permanent Address</label>
                <textarea name="permanent_address" id="permanent_address" class="form-control @error('permanent_address') is-invalid @enderror" placeholder="Enter permanent native address">{{ old('permanent_address', $tenant->permanent_address) }}</textarea>
                @error('permanent_address') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="emergency_contact_name">Emergency Contact Name</label>
                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}" class="form-control @error('emergency_contact_name') is-invalid @enderror" placeholder="Relative / Reference name">
                @error('emergency_contact_name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="emergency_contact_mobile">Emergency Contact Mobile</label>
                <input type="text" name="emergency_contact_mobile" id="emergency_contact_mobile" value="{{ old('emergency_contact_mobile', $tenant->emergency_contact_mobile) }}" class="form-control @error('emergency_contact_mobile') is-invalid @enderror" placeholder="Emergency mobile number">
                @error('emergency_contact_mobile') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="identity_type">Identity Type</label>
                <select name="identity_type" id="identity_type" class="form-control @error('identity_type') is-invalid @enderror">
                    <option value="">-- Select Identity Type --</option>
                    @foreach(['Aadhaar Card', 'PAN Card', 'Passport', 'Driving Licence', 'Voter ID'] as $type)
                        <option value="{{ $type }}" {{ old('identity_type', $tenant->identity_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
                @error('identity_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="identity_number">Identity Number</label>
                <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number', $tenant->identity_number) }}" class="form-control" placeholder="Enter Aadhaar, PAN, Passport or other ID number">
                @error('identity_number') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="document_file">Document Upload</label>
                <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror">
                @if($tenant->document_file)
                    <a href="{{ asset('storage/' . $tenant->document_file) }}" target="_blank" class="current-doc-link">
                        <i class="fa-solid fa-file-arrow-down"></i> View Current Document
                    </a>
                    <div class="form-hint">Upload a new file to replace the current document.</div>
                @else
                    <div class="form-hint">Upload identity document (PDF, JPG, PNG, etc.).</div>
                @endif
                @error('document_file') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Tenant
            </button>
            <a href="{{ route('tenants.index') }}" class="btn-outline">
                Back
            </a>
        </div>
    </form>
</div>
@endsection
