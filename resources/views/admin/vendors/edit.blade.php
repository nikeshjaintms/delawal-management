@extends('admin.layouts.app')

@section('title', 'Edit Vendor')
@section('page-title', 'Vendor Master')

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

    .form-section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 24px 0 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section-title:first-of-type {
        margin-top: 0;
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
        <h2>Edit Vendor</h2>
        <p>Update vendor details — <strong>{{ $vendor->name }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('vendors.update', $vendor->id) }}">
        @csrf
        @method('PUT')
        @include('admin.components.firm-select', ['model' => $vendor])

        <p class="form-section-title">Basic Information</p>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="name">Vendor Name <span>*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $vendor->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Enter vendor or supplier name">
                @error('name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="mobile">Mobile <span>*</span></label>
                <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $vendor->mobile) }}" class="form-control @error('mobile') is-invalid @enderror" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                @error('mobile') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $vendor->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address">
                @error('email') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="gst_no">GST No</label>
                <input type="text" name="gst_no" id="gst_no" value="{{ old('gst_no', $vendor->gst_no) }}" class="form-control @error('gst_no') is-invalid @enderror" placeholder="Enter vendor GST number" style="text-transform:uppercase;">
                @error('gst_no') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <p class="form-section-title">Address & Business Details</p>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $vendor->city) }}" class="form-control @error('city') is-invalid @enderror" placeholder="Enter city name">
                @error('city') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="payment_terms">Payment Terms</label>
                <input type="text" name="payment_terms" id="payment_terms" value="{{ old('payment_terms', $vendor->payment_terms) }}" class="form-control @error('payment_terms') is-invalid @enderror" placeholder="Enter payment terms, e.g. 15 days, 30 days, advance">
                <div class="form-hint">e.g. Net 30, Net 60, Advance, Cash on Delivery</div>
                @error('payment_terms') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="address">Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter full address">{{ old('address', $vendor->address) }}</textarea>
            @error('address') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $vendor->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $vendor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Vendor
            </button>
            <a href="{{ route('vendors.index') }}" class="btn-outline">
                Back
            </a>
        </div>
    </form>
</div>
@endsection
