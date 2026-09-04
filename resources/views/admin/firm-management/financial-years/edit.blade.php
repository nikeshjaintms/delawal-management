@extends('admin.layouts.app')
@section('title','Edit Financial Year — ' . $financialYear->year_name)
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px; max-width: 760px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:640px) { .form-grid { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 0; }
.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: border-color .18s, box-shadow .18s;
}
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 600; }
.form-hint { font-size: 11.5px; color: #94A3B8; margin-top: 4px; }
.toggle-row {
    display: flex; align-items: center; gap: 12px; padding: 12px 16px;
    background: rgba(59, 130, 246, 0.10); border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 10px; margin-top: 4px;
}
.toggle-row label { font-size: 13.5px; font-weight: 700; color: #FFFFFF !important; cursor: pointer; }
.toggle-row input[type=checkbox] { width: 18px; height: 18px; accent-color: #3B82F6; cursor: pointer; }

.info-box {
    background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35);
    border-radius: 12px; padding: 14px 18px; font-size: 13.5px; color: #FDE68A;
    display: flex; align-items: flex-start; gap: 10px; margin-bottom: 24px; max-width: 760px;
}
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.10); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Financial Year</h2>
        <p>Updating: <strong>{{ $financialYear->year_name }}</strong></p>
    </div>
    <a href="{{ route('financial-years.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

@if($errors->any())
<div class="alert-danger-box" style="background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.35); color: #FCA5A5; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-weight: 600; max-width: 760px;">
    <div style="display:flex; align-items:center; gap: 8px;"><i class="fa-solid fa-circle-exclamation"></i> Please correct the following errors:</div>
    <ul style="margin: 8px 0 0 20px; font-size: 13px;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="info-box">
    <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px; color: #F59E0B;"></i>
    <span>If you mark this year as <strong>Active</strong>, all other financial years will be automatically deactivated.</span>
</div>

<div class="form-card">
<form method="POST" action="{{ route('financial-years.update', $financialYear) }}">
@csrf @method('PUT')
    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Financial Year Name <span>*</span></label>
            <input type="text" name="year_name" value="{{ old('year_name', $financialYear->year_name) }}" class="form-control" required>
            <div class="form-hint">Format: YYYY-YYYY (e.g. 2026-2027)</div>
            @error('year_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Start Date <span>*</span></label>
            <input type="date" name="start_date" value="{{ old('start_date', $financialYear->start_date ? $financialYear->start_date->format('Y-m-d') : '') }}" class="form-control" required>
            @error('start_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">End Date <span>*</span></label>
            <input type="date" name="end_date" value="{{ old('end_date', $financialYear->end_date ? $financialYear->end_date->format('Y-m-d') : '') }}" class="form-control" required>
            @error('end_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <div class="toggle-row">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $financialYear->is_active) ? 'checked':'' }}>
                <label for="is_active">Set as Active Financial Year</label>
            </div>
            @error('is_active')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-action-buttons">
        <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Update Financial Year</button>
        <a href="{{ route('financial-years.index') }}" class="btn-secondary-custom"><i class="fa fa-times"></i> Cancel</a>
    </div>
</form>
</div>
@endsection
