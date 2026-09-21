@extends('admin.layouts.app')
@section('title', 'Edit Agriculture Farm')
@section('page-title', 'Agriculture Management')
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
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    max-width: 960px; margin: 0 auto 30px auto;
}

.section-title {
    font-size: 12.5px; font-weight: 800; color: #60A5FA !important;
    text-transform: uppercase; letter-spacing: 1px; margin-bottom: 18px;
    padding-bottom: 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 28px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 7px; }
.form-label span.req { color: #F87171 !important; }
.form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

.form-control {
    width: 100%; padding: 11px 14px !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important; font-size: 14px;
    color: #FFFFFF !important; outline: none; transition: all 0.2s ease;
    background: rgba(16, 22, 34, 0.85) !important; box-sizing: border-box;
}
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
textarea.form-control { resize: vertical; min-height: 85px; }
.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 500; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.38); text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37, 99, 235, 0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 22px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important; font-size: 13.5px;
    font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px;
    text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; transform: translateY(-2px); }

.form-actions {
    display: flex; align-items: center; gap: 15px; margin-top: 30px;
    padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Farm: {{ $farm->farm_name }}</h2>
        <p>Update agriculture property, crop activities, and survey details.</p>
    </div>
    <a href="{{ route('agriculture.farms.show', $farm->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Farm Details</a>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('agriculture.farms.update', $farm->id) }}">
        @csrf
        @method('PUT')

        {{-- Section 1: Basic & Link Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-tractor"></i> Basic Farm Information</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Farm / Land Name <span class="req">*</span></label>
                    <input type="text" name="farm_name" id="farm_name" value="{{ old('farm_name', $farm->farm_name) }}" class="form-control @error('farm_name') is-invalid @enderror" required>
                    @error('farm_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Firm <span class="req">*</span></label>
                    <select name="firm_id" id="firm_id" class="form-control @error('firm_id') is-invalid @enderror">
                        @foreach($firms as $f)
                            <option value="{{ $f->id }}" {{ old('firm_id', $farm->firm_id) == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                        @endforeach
                    </select>
                    @error('firm_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Link Existing Property <span class="opt">(optional)</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Existing Property (Optional) —</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id', $farm->property_id) == $prop->id ? 'selected' : '' }}>
                                {{ $prop->property_name }} {{ $prop->property_code ? '('.$prop->property_code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Link Existing Project <span class="opt">(optional)</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">— Select Existing Project (Optional) —</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id', $farm->project_id) == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Owner / Seller Name <span class="opt">(optional)</span></label>
                    <input type="text" name="owner_seller_name" id="owner_seller_name" value="{{ old('owner_seller_name', $farm->owner_seller_name) }}" class="form-control @error('owner_seller_name') is-invalid @enderror">
                    @error('owner_seller_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Survey / Khasra Number <span class="opt">(optional)</span></label>
                    <input type="text" name="survey_no" id="survey_no" value="{{ old('survey_no', $farm->survey_no) }}" class="form-control @error('survey_no') is-invalid @enderror">
                    @error('survey_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Location Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-map-location-dot"></i> Location & Address</div>
            
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Village / Area</label>
                    <input type="text" name="village" id="village" value="{{ old('village', $farm->village) }}" class="form-control @error('village') is-invalid @enderror">
                    @error('village')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Taluka / Tehsil</label>
                    <input type="text" name="taluka" id="taluka" value="{{ old('taluka', $farm->taluka) }}" class="form-control @error('taluka') is-invalid @enderror">
                    @error('taluka')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">District / City</label>
                    <input type="text" name="district" id="district" value="{{ old('district', $farm->district) }}" class="form-control @error('district') is-invalid @enderror">
                    @error('district')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Land Size, Type & Farming Activity --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-seedling"></i> Land Area & Farming Details</div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Land Area <span class="req">*</span></label>
                    <input type="number" step="0.01" name="land_area" id="land_area" value="{{ old('land_area', $farm->land_area) }}" class="form-control @error('land_area') is-invalid @enderror" required>
                    @error('land_area')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Area Unit <span class="req">*</span></label>
                    <select name="area_unit" id="area_unit" class="form-control @error('area_unit') is-invalid @enderror">
                        @foreach($areaUnits as $u)
                            <option value="{{ $u }}" {{ old('area_unit', $farm->area_unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                    @error('area_unit')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Farm Type <span class="req">*</span></label>
                    <select name="farm_type" id="farm_type" class="form-control @error('farm_type') is-invalid @enderror">
                        @foreach($farmTypes as $ft)
                            <option value="{{ $ft }}" {{ old('farm_type', $farm->farm_type) == $ft ? 'selected' : '' }}>{{ $ft }}</option>
                        @endforeach
                    </select>
                    @error('farm_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Current Crop / Activity <span class="opt">(optional)</span></label>
                    <input type="text" name="crop_activity" id="crop_activity" value="{{ old('crop_activity', $farm->crop_activity) }}" class="form-control @error('crop_activity') is-invalid @enderror">
                    @error('crop_activity')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Start / Possession Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $farm->start_date ? \Carbon\Carbon::parse($farm->start_date)->format('Y-m-d') : '') }}" class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Farm Status <span class="req">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach($statuses as $st)
                            <option value="{{ $st }}" {{ old('status', $farm->status) == $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes & Remarks <span class="opt">(optional)</span></label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $farm->notes) }}</textarea>
                @error('notes')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Update Agriculture Farm</button>
            <a href="{{ route('agriculture.farms.show', $farm->id) }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
