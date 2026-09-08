@extends('admin.layouts.app')

@section('title', 'Edit Bulk Plot / Property')
@section('page-title', 'Bulk Management')

@section('content')
<style>
    .crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .crud-title h2 { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .crud-title p  { font-size: 13.5px; color: var(--text-secondary); }
    .card-box {
        background: rgba(15, 23, 42, 0.65) !important;
        backdrop-filter: blur(14px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.35);
        max-width: 950px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: #60A5FA;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-group { margin-bottom: 20px; }
    .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    @media (max-width: 768px) { .form-row-3 { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 576px) { .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }
    .form-label { display: block; font-size: 13.5px; font-weight: 600; color: #FFFFFF; margin-bottom: 8px; }
    .form-label span { color: #EF4444; }
    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        color: #FFFFFF !important;
        outline: none;
        transition: var(--transition);
        background-color: rgba(16, 22, 34, 0.70) !important;
    }
    .form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
    .form-control option { background-color: #1E293B !important; color: #FFFFFF !important; }
    textarea.form-control { resize: vertical; min-height: 90px; }
    .text-error { color: #EF4444; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
    .form-hint  { font-size: 12px; color: #94A3B8; margin-top: 5px; }
    .form-section { margin-bottom: 28px; }
    .current-img {
        display: block;
        width: 100px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        margin-top: 8px;
    }
    .current-doc-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #60A5FA;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        margin-top: 6px;
        padding: 5px 12px;
        border: 1px solid rgba(59, 130, 246, 0.35);
        border-radius: 7px;
        background: rgba(59, 130, 246, 0.15);
    }
    .current-doc-link:hover { background: rgba(59, 130, 246, 0.25); color: #93C5FD; }
    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.10);
    }

    /* Luxury Auto-Fetch Master Info Card */
    .master-summary-card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.70) 0%, rgba(15, 23, 42, 0.85) 100%);
        border: 1px solid rgba(59, 130, 246, 0.35);
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.30);
        position: relative;
        overflow: hidden;
    }
    .master-summary-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #3B82F6, #10B981);
    }
    .master-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    .master-card-title {
        font-size: 14px;
        font-weight: 700;
        color: #93C5FD;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .master-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
    }
    .master-stat-item {
        display: flex;
        flex-direction: column;
    }
    .master-stat-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94A3B8;
        font-weight: 700;
        margin-bottom: 3px;
    }
    .master-stat-value {
        font-size: 13.5px;
        font-weight: 600;
        color: #FFFFFF;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Bulk Plot / Property</h2>
        <p>Manage and update plot information linked with Property Master.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('properties.update', $property->id) }}" enctype="multipart/form-data" id="bulkPlotForm">
        @csrf
        @method('PUT')

        @include('admin.components.firm-select', ['model' => $property])

        <input type="hidden" name="property_master_id" id="property_master_id" value="{{ old('property_master_id', $property->property_master_id) }}">

        {{-- 1. Project / Property Master Selection (Auto-Fetch Source) --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-layer-group"></i> 1. Linked Project / Property Master
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project / Master Location <span>*</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required onchange="handleProjectChange(this.value)">
                        <option value="">-- Select Project / Master Property --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}"
                                    data-firm-id="{{ $proj->firm_id }}"
                                    data-master-id="{{ $proj->property_id }}"
                                    data-master-name="{{ $proj->propertyMaster?->property_name ?? $proj->project_name }}"
                                    data-project-code="{{ $proj->project_code }}"
                                    data-city="{{ $proj->city }}"
                                    data-location="{{ $proj->location }}"
                                    data-address="{{ $proj->address }}"
                                    {{ old('project_id', $property->project_id) == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_name }} ({{ $proj->project_code }})
                                @if($proj->propertyMaster)
                                    - {{ $proj->propertyMaster->property_name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Changing project will refresh linked Property Master details.</div>
                    @error('project_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="property_type_id">Property Type <span>*</span></label>
                    <select name="property_type_id" id="property_type_id" class="form-control @error('property_type_id') is-invalid @enderror" required>
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

            {{-- Auto-Fetched Live Master Summary Card --}}
            <div id="masterSummaryCard" class="master-summary-card">
                <div class="master-card-header">
                    <div class="master-card-title">
                        <i class="fa-solid fa-circle-nodes" style="color: #60A5FA;"></i>
                        <span>Auto-Fetched Master Details</span>
                    </div>
                    <span class="badge-success" style="font-size: 11px; padding: 2px 10px;">
                        <i class="fa-solid fa-link"></i> Linked to Property Master
                    </span>
                </div>
                <div class="master-grid">
                    <div class="master-stat-item">
                        <span class="master-stat-label">Firm</span>
                        <span class="master-stat-value" id="disp_firm_name">{{ $property->firm?->firm_name ?? '-' }}</span>
                    </div>
                    <div class="master-stat-item">
                        <span class="master-stat-label">Project</span>
                        <span class="master-stat-value" id="disp_project_name">{{ $property->project?->project_name ?? 'Standalone' }}</span>
                    </div>
                    <div class="master-stat-item">
                        <span class="master-stat-label">Property Master</span>
                        <span class="master-stat-value" id="disp_master_name">{{ $property->propertyMaster?->property_name ?? ($property->project?->propertyMaster?->property_name ?? 'Standalone') }}</span>
                    </div>
                    <div class="master-stat-item">
                        <span class="master-stat-label">City / Location</span>
                        <span class="master-stat-value" id="disp_city_location">{{ implode(', ', array_filter([$property->location, $property->city])) ?: '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Plot Details --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-map-location-dot"></i> 2. Plot & Unit Details
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_name">Plot / Property Name <span>*</span></label>
                    <input type="text" name="property_name" id="property_name"
                           value="{{ old('property_name', $property->property_name) }}"
                           class="form-control @error('property_name') is-invalid @enderror" autocomplete="off" placeholder="Enter property name" required>
                    @error('property_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_code">Property / Plot Code <span>*</span></label>
                    <input type="text" name="property_code" id="property_code"
                           value="{{ old('property_code', $property->property_code) }}"
                           class="form-control @error('property_code') is-invalid @enderror" autocomplete="off" placeholder="e.g. DEL-FLAT-001" required>
                    @error('property_code') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="unit_no">Unit / Plot No</label>
                    <input type="text" name="unit_no" id="unit_no"
                           value="{{ old('unit_no', $property->unit_no) }}"
                           class="form-control @error('unit_no') is-invalid @enderror" autocomplete="off" placeholder="e.g. P-01 / A-101">
                    @error('unit_no') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="size">Size / Area</label>
                    <input type="text" name="size" id="size"
                           value="{{ old('size', $property->size) }}"
                           class="form-control @error('size') is-invalid @enderror" autocomplete="off" placeholder="e.g. 1200">
                    @error('size') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="size_unit">Size Unit</label>
                    <select name="size_unit" id="size_unit" class="form-control @error('size_unit') is-invalid @enderror">
                        @foreach(['sq.ft' => 'Sq. Ft', 'sq.yard' => 'Sq. Yard', 'sq.meter' => 'Sq. Meter', 'acre' => 'Acre', 'bigha' => 'Bigha'] as $val => $label)
                            <option value="{{ $val }}" {{ old('size_unit', $property->size_unit) == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('size_unit') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="price">Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="price"
                           value="{{ old('price', $property->price) }}"
                           class="form-control @error('price') is-invalid @enderror" placeholder="Enter property price">
                    @error('price') <div class="text-error">{{ $message }}</div> @enderror
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
        </div>

        {{-- 3. Location & Address --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-location-dot"></i> 3. Location Details
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="city">City</label>
                    <input type="text" name="city" id="city"
                           value="{{ old('city', $property->city) }}"
                           class="form-control @error('city') is-invalid @enderror" autocomplete="off" placeholder="e.g. Ahmedabad">
                    @error('city') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $property->location) }}"
                           class="form-control @error('location') is-invalid @enderror" autocomplete="off" placeholder="e.g. Zadeshwar, Bharuch">
                    @error('location') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="address">Address</label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Full address">{{ old('address', $property->address) }}</textarea>
                @error('address') <div class="text-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="description">Description / Notes</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                          placeholder="Add property details, amenities, notes, or special information">{{ old('description', $property->description) }}</textarea>
                @error('description') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- 4. Files --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-paperclip"></i> 4. Images & Documents</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="main_image">Main Property Image</label>
                    <input type="file" name="main_image" id="main_image" class="form-control @error('main_image') is-invalid @enderror" accept="image/*">
                    @if($property->main_image)
                        <img src="{{ asset('storage/' . $property->main_image) }}" alt="Property Image" class="current-img">
                    @endif
                    @error('main_image') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="document_file">Property Document</label>
                    <input type="file" name="document_file" id="document_file" class="form-control @error('document_file') is-invalid @enderror">
                    @if($property->document_file)
                        <a href="{{ asset('storage/' . $property->document_file) }}" target="_blank" class="current-doc-link">
                            <i class="fa-solid fa-file-pdf"></i> View Current Document
                        </a>
                    @endif
                    @error('document_file') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Update Bulk Plot
            </button>
            <a href="{{ route('properties.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
    function handleProjectChange(projectId) {
        const select = document.getElementById('project_id');
        const selectedOpt = select.options[select.selectedIndex];

        if (!projectId || !selectedOpt) {
            return;
        }

        const firmId = selectedOpt.getAttribute('data-firm-id');
        const masterId = selectedOpt.getAttribute('data-master-id');
        const masterName = selectedOpt.getAttribute('data-master-name') || '';
        const city = selectedOpt.getAttribute('data-city') || '';
        const location = selectedOpt.getAttribute('data-location') || '';
        const address = selectedOpt.getAttribute('data-address') || '';

        if (masterId) {
            document.getElementById('property_master_id').value = masterId;
        }

        document.getElementById('disp_project_name').innerText = selectedOpt.text.split('(')[0].trim();
        document.getElementById('disp_master_name').innerText = masterName || 'Standalone';
        document.getElementById('disp_city_location').innerText = [location, city].filter(Boolean).join(', ') || '-';

        const firmSelect = document.getElementById('firm_id') || document.querySelector('select[name="firm_id"]');
        if (firmSelect && firmId) {
            firmSelect.value = firmId;
            const firmOpt = firmSelect.options[firmSelect.selectedIndex];
            document.getElementById('disp_firm_name').innerText = firmOpt ? firmOpt.text : 'Firm #' + firmId;
        }

        // AJAX update
        fetch(`{{ route('properties.master-info') }}?project_id=${projectId}`)
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    const d = res.data;
                    if (d.firm_name) document.getElementById('disp_firm_name').innerText = d.firm_name;
                    if (d.property_master_id) document.getElementById('property_master_id').value = d.property_master_id;
                }
            })
            .catch(err => console.log('Master info sync:', err));
    }
</script>
@endsection
