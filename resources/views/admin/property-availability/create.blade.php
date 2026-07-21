@extends('admin.layouts.app')
@section('title','Update Property Status')
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
/* property info preview */
.prop-info-box{background:#F8FAFC;border:1px solid var(--border-color);border-radius:10px;padding:14px 16px;margin-top:12px;display:none}
.prop-info-box .pi-row{display:flex;gap:20px;flex-wrap:wrap}
.prop-info-box .pi-item{font-size:12.5px;color:var(--text-secondary)}
.prop-info-box .pi-item strong{color:var(--text-primary)}
/* status colour dots for select */
.status-dot-available::before{content:"● ";color:#059669}
.status-dot-booked::before{content:"● ";color:#1D4ED8}
.status-dot-sold::before{content:"● ";color:#991B1B}
.status-dot-rented::before{content:"● ";color:#9A3412}
.status-dot-reserved::before{content:"● ";color:#5B21B6}
.status-dot-under_maintenance::before{content:"● ";color:#475569}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Update Property Status</h2>
        <p>Set or change the availability status of a property.</p>
    </div>
    <a href="{{ route('property-availability.index') }}" class="btn-sc"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('property-availability.store') }}">
@csrf

@include('admin.components.firm-select')

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-circle-check"></i> Status Information</div>

    <div class="form-grid" style="margin-bottom:18px">
        <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Property <span>*</span></label>
            <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required onchange="showPropInfo(this)">
                <option value="">— Select Property —</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}"
                        data-type="{{ $p->propertyType->name ?? '—' }}"
                        data-unit="{{ $p->unit_no ?? '—' }}"
                        data-code="{{ $p->property_code ?? '' }}"
                        data-status="{{ $p->status }}"
                        {{ old('property_id', request('property_id')) == $p->id ? 'selected' : '' }}>
                        {{ $p->property_name }}{{ $p->property_code ? ' ('.$p->property_code.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('property_id')<div class="text-error">{{ $message }}</div>@enderror

            {{-- Live property info preview --}}
            <div class="prop-info-box" id="propInfoBox">
                <div class="pi-row">
                    <div class="pi-item">Type: <strong id="piType">—</strong></div>
                    <div class="pi-item">Unit No: <strong id="piUnit">—</strong></div>
                    <div class="pi-item">Current Status: <strong id="piStatus">—</strong></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">New Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="">— Select Status —</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Status Date <span>*</span></label>
            <input type="date" name="status_date" value="{{ old('status_date', date('Y-m-d')) }}"
                   class="form-control @error('status_date') is-invalid @enderror" required>
            @error('status_date')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                  placeholder="Reason for status change, buyer name, booking ref, etc.">{{ old('remarks') }}</textarea>
        @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-pc"><i class="fa fa-save"></i> Save Status</button>
    <a href="{{ route('property-availability.index') }}" class="btn-sc">Cancel</a>
</div>
</form>

<script>
const propData = {
    @foreach($properties as $p)
    {{ $p->id }}: {
        type:   "{{ $p->propertyType->name ?? '—' }}",
        unit:   "{{ $p->unit_no ?? '—' }}",
        status: "{{ ucfirst(str_replace('_',' ',$p->status)) }}"
    },
    @endforeach
};

function showPropInfo(sel) {
    const box = document.getElementById('propInfoBox');
    const id  = parseInt(sel.value);
    if (!id || !propData[id]) { box.style.display = 'none'; return; }
    const d = propData[id];
    document.getElementById('piType').textContent   = d.type;
    document.getElementById('piUnit').textContent   = d.unit;
    document.getElementById('piStatus').textContent = d.status;
    box.style.display = 'block';
}

// Auto-trigger on page load (old() or query param)
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('property_id');
    if (sel.value) showPropInfo(sel);
});
</script>
@endsection
