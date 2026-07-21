@extends('admin.layouts.app')
@section('title','Edit Property Status Record')
@section('page-title','Property Availability')

@section('content')
<style>
.btn-pc{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff!important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none!important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-pc:hover{color:#fff!important;transform:translateY(-2px)}
.btn-sc{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8!important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none!important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-sc:hover{background:#EEF3FA;color:#10233F!important;transform:translateY(-2px)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.form-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px;max-width:740px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:7px}
.form-label span{color:#EF4444}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:border-color .18s,box-shadow .18s;background:#fff}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
textarea.form-control{resize:vertical;min-height:90px}
.text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500}
.form-action-buttons{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Status Record</h2>
        <p>Updating: <strong>{{ $record->property->property_name ?? '—' }}</strong></p>
    </div>
    <a href="{{ route('property-availability.index') }}" class="btn-sc"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('property-availability.update', $record) }}">
@csrf @method('PUT')

@include('admin.components.firm-select', ['model' => $record])

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-circle-check"></i> Status Information</div>

    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Property <span>*</span></label>
            <select name="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                <option value="">— Select Property —</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}"
                        {{ old('property_id', $record->property_id) == $p->id ? 'selected' : '' }}>
                        {{ $p->property_name }}{{ $p->property_code ? ' ('.$p->property_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="">— Select Status —</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $record->status) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status Date <span>*</span></label>
            <input type="date" name="status_date"
                   value="{{ old('status_date', $record- class="@error('status_date') is-invalid @enderror">status_date->format('Y-m-d')) }}"
                   class="form-control" required>
            @error('status_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $record->remarks) }}</textarea>
        @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-pc"><i class="fa fa-save"></i> Update Status</button>
    <a href="{{ route('property-availability.index') }}" class="btn-sc">Cancel</a>
</div>
</form>
@endsection
