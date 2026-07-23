@extends('admin.layouts.app')
@section('title','Add Financial Year')
@section('page-title','Firm Management')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-primary-custom i,.btn-secondary-custom i{font-size:14px;line-height:1}
.form-action-buttons{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.form-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);max-width:700px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:7px}
.form-label span{color:#EF4444}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:border-color .18s,box-shadow .18s;background:#fff}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
.text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500}
.form-hint{font-size:11.5px;color:var(--text-secondary);margin-top:4px}
.toggle-row{display:flex;align-items:center;gap:12px;padding:12px 16px;background:rgba(59,130,246,.05);border:1px solid rgba(59,130,246,.12);border-radius:8px;margin-top:4px}
.toggle-row label{font-size:13.5px;font-weight:600;color:var(--text-primary);cursor:pointer}
.toggle-row input[type=checkbox]{width:16px;height:16px;accent-color:var(--blue);cursor:pointer}
.info-box{background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:12px 16px;font-size:13px;color:#92400E;display:flex;align-items:flex-start;gap:10px;margin-bottom:20px}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Financial Year</h2>
        <p>Create a new financial year for the system.</p>
    </div>
    <a href="{{ route('financial-years.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="info-box">
    <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px"></i>
    <span>If you mark this year as <strong>Active</strong>, all other financial years will be automatically deactivated.</span>
</div>

<div class="form-card">
<form method="POST" action="{{ route('financial-years.store') }}">
@csrf
    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Financial Year Name <span>*</span></label>
            <input type="text" name="year_name" value="{{ old('year_name') }}" class="form-control @error('year_name') is-invalid @enderror" placeholder="e.g. 2026-2027" required>
            <div class="form-hint">Format: YYYY-YYYY (e.g. 2026-2027)</div>
            @error('year_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Start Date <span>*</span></label>
            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control @error('start_date') is-invalid @enderror" required>
            @error('start_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">End Date <span>*</span></label>
            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control @error('end_date') is-invalid @enderror" required>
            @error('end_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active"   {{ old('status','active')=='active'   ? 'selected':'' }}>Active</option>
                <option value="inactive" {{ old('status')=='inactive' ? 'selected':'' }}>Inactive</option>
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group" style="display:flex;flex-direction:column;justify-content:flex-end">
            <label class="form-label">Set as Active Year</label>
            <div class="toggle-row">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked':'' }} class="@error('is_active') is-invalid @enderror">
                <label for="is_active">Mark as current active financial year</label>
            </div>
            @error('is_active')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="form-action-buttons">
        <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Save Financial Year</button>
        <a href="{{ route('financial-years.index') }}" class="btn-secondary-custom">Cancel</a>
    </div>
</form>
</div>
@endsection
