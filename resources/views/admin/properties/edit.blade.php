@extends('admin.layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Property Master')

@section('content')
<style>
    .crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .crud-title h2 { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .crud-title p  { font-size: 13.5px; color: var(--text-secondary); }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 900px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--gold);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }
    .form-group { margin-bottom: 20px; }
    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .form-row-3 { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 576px) { .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }
    .form-label { display: block; font-size: 13.5px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px; }
    .form-label span { color: #EF4444; }
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
    .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 3px var(--gold-light); }
    textarea.form-control { resize: vertical; min-height: 100px; }
    .text-error { color: #EF4444; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
    .form-hint  { font-size: 12px; color: var(--text-secondary); margin-top: 5px; }
    .form-section { margin-bottom: 28px; }
    .current-img {
        display: block;
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        margin-top: 8px;
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
        padding: 5px 12px;
        border: 1px solid rgba(212,175,55,0.35);
        border-radius: 7px;
        background: var(--gold-light);
    }
    .current-doc-link:hover { background: rgba(212,175,55,0.2); color: #B58D1B; }
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
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212,175,55,0.2);
        font-family: var(--font-primary);
    }
    .btn-gold:hover { background-color: #B58D1B; transform: translateY(-1px); box-shadow: 0 6px 14px rgba(212,175,55,0.3); }
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
    .btn-outline:hover { background: #F9FAFB; color: var(--text-primary); border-color: #D1D5DB; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Property</h2>
        <p>Add and manage all properties, units, flats, shops, plots and offices firm-wise.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('properties.update', $property->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.components.firm-select', ['model' => $property])

        {{-- Basic Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Basic Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_name">Property Name <span>*</span></label>
                    <input type="text" name="property_name" id="property_name"
                           value="{{ old('property_name', $property- class="@error('property_name') is-invalid @enderror">property_name) }}"
                           class="form-control" autocomplete="off" placeholder="Enter property name">
                    @error('property_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_type_id">Property Type</label>
                    <select name="property_type_id" id="property_type_id" class="form-control @error('property_type_id') is-invalid @enderror">
                        <option value="">-- Select Property Type --</option>
                        @foreach($propertyTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ old('property_type_id', $property->property_type_id) == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_type_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_code">Property Code</label>
                    <input type="text" name="property_code" id="property_code"
                           value="{{ old('property_code', $property- class="@error('property_code') is-invalid @enderror">property_code) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. DEL-FLAT-001">
                    @error('property_code') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Property Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach(['available' => 'Available', 'booked' => 'Booked', 'sold' => 'Sold', 'rented' => 'Rented'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('status', $property->status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="location">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $property- class="@error('location') is-invalid @enderror">location) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. Zadeshwar, Bharuch">
                    @error('location') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input type="text" name="city" id="city"
                           value="{{ old('city', $property- class="@error('city') is-invalid @enderror">city) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. Ahmedabad">
                    @error('city') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Full address">{{ old('address', $property->address) }}</textarea>
                @error('address') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Property Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-ruler-combined"></i> Property Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="size">Size</label>
                    <input type="text" name="size" id="size"
                           value="{{ old('size', $property- class="@error('size') is-invalid @enderror">size) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. 1200">
                    @error('size') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="size_unit">Size Unit</label>
                    <select name="size_unit" id="size_unit" class="form-control @error('size_unit') is-invalid @enderror">
                        <option value="">-- Select Unit --</option>
                        @foreach(['sq.ft' => 'Sq. Ft', 'sq.yard' => 'Sq. Yard', 'sq.meter' => 'Sq. Meter', 'acre' => 'Acre', 'bigha' => 'Bigha'] as $val => $label)
                            <option value="{{ $val }}" {{ old('size_unit', $property->size_unit) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('size_unit') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="price">Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="price"
                           value="{{ old('price', $property- class="@error('price') is-invalid @enderror">price) }}"
                           class="form-control" placeholder="Enter property price">
                    @error('price') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="unit_no">Unit No</label>
                    <input type="text" name="unit_no" id="unit_no"
                           value="{{ old('unit_no', $property- class="@error('unit_no') is-invalid @enderror">unit_no) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. A-101">
                    @error('unit_no') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="floor_no">Floor No</label>
                    <input type="text" name="floor_no" id="floor_no"
                           value="{{ old('floor_no', $property- class="@error('floor_no') is-invalid @enderror">floor_no) }}"
                           class="form-control" autocomplete="off" placeholder="e.g. 3rd Floor">
                    @error('floor_no') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="facing">Facing</label>
                    <select name="facing" id="facing" class="form-control @error('facing') is-invalid @enderror">
                        <option value="">-- Select Facing --</option>
                        @foreach(['East','West','North','South','North-East','North-West','South-East','South-West'] as $dir)
                            <option value="{{ $dir }}" {{ old('facing', $property->facing) == $dir ? 'selected' : '' }}>{{ $dir }}</option>
                        @endforeach
                    </select>
                    @error('facing') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Property Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Add property details, amenities, notes, or special information">{{ old('description', $property->description) }}</textarea>
                @error('description') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Files --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-paperclip"></i> Images & Documents</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="main_image">Main Property Image</label>
                    <input type="file" name="main_image" id="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                    @if($property->main_image)
                        <img src="{{ asset('storage/' . $property->main_image) }}"
                             alt="Current Image" class="current-img">
                        <div class="form-hint">Upload a new image to replace the current one.</div>
                    @else
                        <div class="form-hint">Upload property photo (JPG, PNG, WEBP).</div>
                    @endif
                    @error('main_image') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="document_file">Property Document</label>
                    <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror">
                    @if($property->document_file)
                        <a href="{{ asset('storage/' . $property->document_file) }}" target="_blank" class="current-doc-link">
                            <i class="fa-solid fa-file-arrow-down"></i> View Current Document
                        </a>
                        <div class="form-hint">Upload a new file to replace the current document.</div>
                    @else
                        <div class="form-hint">Upload property document (PDF, DOC, etc.).</div>
                    @endif
                    @error('document_file') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Property
            </button>
            <a href="{{ route('properties.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
