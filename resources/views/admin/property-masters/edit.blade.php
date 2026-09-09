@extends('admin.layouts.app')

@section('title', 'Edit Property Master')
@section('page-title', 'Property Management')

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

    .form-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
        max-width: 900px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
        background: #FFF;
    }

    .form-control:focus {
        border-color: var(--gold);
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-gold:hover {
        background-color: #B58D1B;
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
    }

    .invalid-feedback {
        color: #EF4444;
        font-size: 12px;
        margin-top: 2px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Property Master: {{ $propertyMaster->property_name }}</h2>
        <p>Update top-level Property details.</p>
    </div>
    <a href="{{ route('property-masters.show', $propertyMaster->id) }}" class="btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Details
    </a>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('property-masters.update', $propertyMaster->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            @if(Auth::user() && Auth::user()->isAdmin())
                <div class="form-group">
                    <label class="form-label">Firm <span style="color:#EF4444;">*</span></label>
                    <select name="firm_id" class="form-control" required>
                        @foreach($firms as $firm)
                            <option value="{{ $firm->id }}" {{ old('firm_id', $propertyMaster->firm_id) == $firm->id ? 'selected' : '' }}>
                                {{ $firm->firm_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Property Name <span style="color:#EF4444;">*</span></label>
                <input type="text" name="property_name" value="{{ old('property_name', $propertyMaster->property_name) }}" class="form-control" required>
                @error('property_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Property Code</label>
                <input type="text" name="property_code" value="{{ old('property_code', $propertyMaster->property_code) }}" class="form-control">
                @error('property_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:#EF4444;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $propertyMaster->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $propertyMaster->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price &amp; Financial Details
                </div>
            </div>

            <div class="form-group full-width" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.10) 0%, rgba(5, 150, 105, 0.05) 100%); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 12px; padding: 18px; margin-bottom: 10px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #34D399; font-weight: 700;">
                            <i class="fa-solid fa-money-bill-wave"></i> Purchase Price / Buy Amount (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 10px; color: #34D399; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price"
                                   value="{{ old('purchase_price', $propertyMaster->purchase_price) }}"
                                   class="form-control" placeholder="e.g. 5000000.00"
                                   style="padding-left: 30px; font-weight: 700; font-size: 15px; color: #34D399; border-color: rgba(16, 185, 129, 0.4);">
                        </div>
                        <small style="color: #A7F3D0; font-size: 11.5px; margin-top: 4px; display: block;">Total price at which this entire property was purchased.</small>
                        @error('purchase_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</label>
                        <input type="date" name="purchase_date"
                               value="{{ old('purchase_date', $propertyMaster->purchase_date ? date('Y-m-d', strtotime($propertyMaster->purchase_date)) : date('Y-m-d')) }}"
                               class="form-control">
                        @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Seller / Vendor / Owner</label>
                <select name="vendor_id" class="form-control">
                    <option value="">— Select Seller / Vendor —</option>
                    @if(isset($vendors))
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id', $propertyMaster->vendor_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} {{ ($v->mobile ?: $v->phone) ? '(' . ($v->mobile ?: $v->phone) . ')' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('vendor_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-control">
                    <option value="">— Select Payment Mode —</option>
                    @foreach(['Bank Transfer / RTGS / NEFT', 'Cheque', 'Cash', 'UPI', 'Other'] as $mode)
                        <option value="{{ $mode }}" {{ old('payment_mode', $propertyMaster->payment_mode) == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>
                @error('payment_mode') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Total Land Area <span style="color:#94A3B8; font-weight: normal;">(optional)</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" step="0.01" name="total_area" value="{{ old('total_area', $propertyMaster->total_area) }}" class="form-control" placeholder="e.g. 15000">
                    <select name="area_unit" class="form-control" style="width: 120px; flex-shrink: 0;">
                        @foreach(['Sq.Ft', 'Sq.Yd', 'Sq.Mtr', 'Acre', 'Vigha', 'Guntha'] as $u)
                            <option value="{{ $u }}" {{ old('area_unit', $propertyMaster->area_unit ?? 'Sq.Ft') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                @error('total_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Purchase Rate per Unit <span style="color:#94A3B8; font-weight: normal;">(optional)</span></label>
                <input type="number" step="0.01" name="purchase_rate" value="{{ old('purchase_rate', $propertyMaster->purchase_rate) }}" class="form-control" placeholder="e.g. 1200.00">
                @error('purchase_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-location-dot"></i> Location &amp; Address Details
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" value="{{ old('location', $propertyMaster->location) }}" class="form-control">
                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" value="{{ old('city', $propertyMaster->city) }}" class="form-control">
                @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" value="{{ old('state', $propertyMaster->state) }}" class="form-control">
                @error('state') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" value="{{ old('country', $propertyMaster->country) }}" class="form-control">
                @error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode', $propertyMaster->pincode) }}" class="form-control">
                @error('pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $propertyMaster->address) }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $propertyMaster->description) }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Main Image</label>
                <input type="file" name="main_image" class="form-control" accept="image/*">
                @if($propertyMaster->main_image)
                    <small style="color: var(--text-secondary);">Current: {{ basename($propertyMaster->main_image) }}</small>
                @endif
                @error('main_image') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Document File</label>
                <input type="file" name="document_file" class="form-control">
                @if($propertyMaster->document_file)
                    <small style="color: var(--text-secondary);">Current: {{ basename($propertyMaster->document_file) }}</small>
                @endif
                @error('document_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('property-masters.show', $propertyMaster->id) }}" class="btn-cancel-custom" style="padding: 10px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); color: #CBD5E1; text-decoration: none;">Cancel</a>
            <button type="submit" class="btn-gold">Update Property Master</button>
        </div>
    </form>
</div>
@endsection
