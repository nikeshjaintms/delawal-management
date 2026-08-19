@extends('admin.layouts.app')
@section('title','Edit Property Document')
@section('page-title','Property Documents')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:768px) { .form-grid { grid-template-columns: 1fr; } }

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
textarea.form-control { resize: vertical; min-height: 80px; }

.current-file-box {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px;
    background: rgba(255, 255, 255, 0.08) !important; border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 10px; font-size: 13px; color: #CBD5E1 !important; font-weight: 600; margin-bottom: 12px;
}
.current-file-box a { color: #60A5FA !important; font-weight: 700; text-decoration: none; }
.current-file-box a:hover { text-decoration: underline; color: #93C5FD !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 500; }
.form-hint { font-size: 11.5px; color: #CBD5E1 !important; margin-top: 4px; }
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
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
            <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                <option value="">— Select Property —</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" data-project="{{ $prop->project->project_name ?? ($prop->project->propertyMaster->property_name ?? 'No Project Assigned') }}" {{ old('property_id', $doc->property_id) == $prop->id ? 'selected' : '' }}>
                        {{ $prop->property_name }}{{ $prop->property_code ? ' ('.$prop->property_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="project_display">Project</label>
            <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background-color:#F9FAFB; cursor:not-allowed;">
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

<script>
function updateProjectMapping() {
    const select = document.getElementById('property_id');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const projectDisplay = document.getElementById('project_display');
    if (projectDisplay) {
        if (!select.value || !selectedOption) {
            projectDisplay.value = 'Auto-determined';
        } else {
            const projName = selectedOption.getAttribute('data-project');
            projectDisplay.value = projName || 'No Project Assigned';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const propSelect = document.getElementById('property_id');
    if (propSelect) {
        propSelect.addEventListener('change', updateProjectMapping);
        if (window.jQuery) {
            jQuery('#property_id').on('change select2:select select2:unselect', updateProjectMapping);
        }
        updateProjectMapping();
    }
});
</script>
@endsection
