@extends('admin.layouts.app')
@section('title','Edit Property Document')
@section('page-title','Property Documents')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.form-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:768px){.form-grid{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:7px}
.form-label span{color:#EF4444}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:border-color .18s,box-shadow .18s;background:#fff}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
textarea.form-control{resize:vertical;min-height:80px}
.text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500}
.form-hint{font-size:11.5px;color:var(--text-secondary);margin-top:4px}
.form-action-buttons{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
.current-file-box{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;background:#F4F8FF;border:1px solid rgba(30,90,168,.2);border-radius:8px;font-size:13px;margin-bottom:10px}
.current-file-box a{color:#1E5AA8 !important;font-weight:600;text-decoration:none}
.current-file-box a:hover{text-decoration:underline}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Document</h2>
        <p>Updating: <strong>{{ $doc->document_title }}</strong></p>
    </div>
    <a href="{{ route('property-documents.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('property-documents.update', $doc) }}" enctype="multipart/form-data">
@csrf @method('PUT')

@include('admin.components.firm-select', ['model' => $doc])

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-file-lines"></i> Document Information</div>
    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group">
            <label class="form-label">Property <span>*</span></label>
            <select name="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                <option value="">— Select Property —</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ old('property_id', $doc->property_id) == $prop->id ? 'selected' : '' }}>
                        {{ $prop->property_name }}{{ $prop->property_code ? ' ('.$prop->property_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Document Type <span>*</span></label>
            <select name="document_type" class="form-control @error('document_type') is-invalid @enderror" required>
                <option value="">— Select Type —</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type }}" {{ old('document_type', $doc->document_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
            @error('document_type')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Document Title <span>*</span></label>
            <input type="text" name="document_title" value="{{ old('document_title', $doc->document_title) }}" class="form-control" required>
            @error('document_title')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Document Number</label>
            <input type="text" name="document_number" value="{{ old('document_number', $doc->document_number) }}" class="form-control">
            @error('document_number')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" value="{{ old('expiry_date', $doc->expiry_date?->format('Y-m-d')) }}" class="form-control">
            @error('expiry_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active"   {{ old('status', $doc->status) == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $doc->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group" style="margin-bottom:18px">
        <label class="form-label">Upload New Document <span style="font-weight:400;color:var(--text-secondary)">(optional — leave blank to keep current)</span></label>
        @if($doc->document_file)
            <div class="current-file-box">
                <i class="fa-solid fa-file" style="color:#1E5AA8"></i>
                <span>Current file:</span>
                <a href="{{ Storage::url($doc->document_file) }}" target="_blank">View / Download</a>
            </div><br>
        @endif
        <input type="file" name="document_file" class="form-control @error('document_file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
        <div class="form-hint">Accepted: PDF, JPG, JPEG, PNG — Max 5 MB</div>
        @error('document_file')<div class="text-error">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $doc->remarks) }}</textarea>
        @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Update Document</button>
    <a href="{{ route('property-documents.index') }}" class="btn-secondary-custom">Cancel</a>
</div>
</form>
@endsection
