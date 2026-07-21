@extends('admin.layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Tenant Master')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 576px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .form-label span {
        color: #EF4444;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }

    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-light);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .text-error {
        color: #EF4444;
        font-size: 12.5px;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 5px;
    }

    .current-doc-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--gold);
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 6px;
    }

    .current-doc-link:hover {
        color: #B58D1B;
        text-decoration: underline;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
        font-family: var(--font-primary);
    }

    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
        border-color: #D1D5DB;
    }
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
                <input type="text" name="name" id="name" value="{{ old('name', $tenant- class="@error('name') is-invalid @enderror">name) }}" class="form-control" placeholder="Enter tenant name">
                @error('name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Mobile <span>*</span></label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $tenant->mobile) }}" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                @error('mobile') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $tenant- class="@error('email') is-invalid @enderror">email) }}" class="form-control" placeholder="Enter email address">
                @error('email') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $tenant- class="@error('city') is-invalid @enderror">city) }}" class="form-control" placeholder="Enter city name">
                @error('city') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="address">Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter physical address">{{ old('address', $tenant->address) }}</textarea>
            @error('address') <div class="text-error">{{ $message }}</div> @enderror
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
                <input type="text" name="identity_number" id="identity_number" value="{{ old('identity_number', $tenant- class="@error('identity_number') is-invalid @enderror">identity_number) }}" class="form-control" placeholder="Enter Aadhaar, PAN, Passport or other ID number">
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
