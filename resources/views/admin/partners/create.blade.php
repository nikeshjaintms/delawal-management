@extends('admin.layouts.app')
@section('title', 'Add Partner')
@section('page-title', 'Add Partner & Profit Share')

@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; margin: 0; }
.form-card {
    background: rgba(20, 27, 41, 0.65) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12) !important; border-radius: 20px !important;
    padding: 28px 32px !important; margin-bottom: 24px; max-width: 980px;
}
.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255,255,255,0.10); display: flex; align-items: center; gap: 8px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:768px) { .form-grid { grid-template-columns: 1fr; } }
.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171; }
.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.70) !important;
    border: 1.5px solid rgba(255,255,255,0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.form-control option { background: #111827 !important; color: #FFFFFF !important; }
.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 600; }
.btn-primary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; min-height: 42px;
    background: #2563EB !important; color: #FFFFFF !important; font-size: 14px; font-weight: 700;
    border: 1px solid #3B82F6 !important; border-radius: 10px; cursor: pointer; text-decoration: none !important;
}
.btn-secondary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; min-height: 42px;
    background: #1E293B !important; color: #FFFFFF !important; font-size: 14px; font-weight: 700;
    border: 1px solid #475569 !important; border-radius: 10px; text-decoration: none !important;
}
.silent-box {
    display: flex; align-items: center; gap: 10px; padding: 12px 14px;
    background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.30); border-radius: 10px;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Partner</h2>
        <p>Register a partner (including silent / off-paper partners) and optionally assign their initial profit %.</p>
    </div>
    <a href="{{ route('partners.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('partners.store') }}">
    @csrf

    <div class="form-card">
        <div class="section-heading"><i class="fa-solid fa-user-secret"></i> Partner Details</div>
        <div class="form-grid">
            <div>
                <label class="form-label">Partner Name <span>*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g., Ramesh Patel" required>
                @error('name')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Default Firm (Optional)</label>
                <select name="firm_id" class="form-control">
                    <option value="">-- Any / Multi-Firm --</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
                @error('firm_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Mobile Number (Optional)</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control" placeholder="10-digit mobile">
                @error('mobile')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Email (Optional)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="partner@example.com">
                @error('email')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Partner Type</label>
                <label class="silent-box" style="cursor: pointer;">
                    <input type="hidden" name="is_silent" value="0">
                    <input type="checkbox" name="is_silent" value="1" {{ old('is_silent', '1') == '1' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 700; color: #E2E8F0; font-size: 13.5px;">Silent Partner (Not on Paper)</div>
                        <div style="font-size: 11.5px; color: #94A3B8;">Hidden from Firm logins — visible only to Admin.</div>
                    </div>
                </label>
            </div>

            <div>
                <label class="form-label">Status <span>*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div style="grid-column: 1 / -1;">
                <label class="form-label">Private Notes</label>
                <textarea name="notes" rows="2" class="form-control" placeholder="Optional internal remarks about arrangement...">{{ old('notes') }}</textarea>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="section-heading"><i class="fa-solid fa-percent"></i> Initial Profit Share Assignment (Optional — You can add more later)</div>
        <div class="form-grid">
            <div>
                <label class="form-label">Profit Share Scope</label>
                <select name="initial_scope_type" id="initial_scope_type" class="form-control" onchange="toggleScopeFields()">
                    <option value="">-- Skip for now (assign on next page) --</option>
                    <option value="firm" {{ old('initial_scope_type') === 'firm' ? 'selected' : '' }}>Entire Firm</option>
                    <option value="project" {{ old('initial_scope_type') === 'project' ? 'selected' : '' }}>Project</option>
                    <option value="property_master" {{ old('initial_scope_type') === 'property_master' ? 'selected' : '' }}>Property / Land Master</option>
                    <option value="property" {{ old('initial_scope_type') === 'property' ? 'selected' : '' }}>Individual Plot / Unit</option>
                </select>
            </div>

            <div>
                <label class="form-label">Profit Share Percentage (%)</label>
                <input type="number" step="0.01" min="0.01" max="100" name="initial_share_percentage" value="{{ old('initial_share_percentage') }}" class="form-control" placeholder="e.g., 25.00">
                @error('initial_share_percentage')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div id="wrap_initial_firm" style="display: none;">
                <label class="form-label">Select Firm</label>
                <select name="initial_firm_id" class="form-control">
                    <option value="">-- Select Firm --</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ old('initial_firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="wrap_initial_project" style="display: none;">
                <label class="form-label">Select Project</label>
                <select name="initial_project_id" class="form-control">
                    <option value="">-- Select Project --</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('initial_project_id') == $proj->id ? 'selected' : '' }}>
                            {{ $proj->project_name }} ({{ $proj->firm?->firm_name ?? 'Firm' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="wrap_initial_pm" style="display: none;">
                <label class="form-label">Select Property / Land Master</label>
                <select name="initial_property_master_id" class="form-control">
                    <option value="">-- Select Property Master --</option>
                    @foreach($propertyMasters as $pm)
                        <option value="{{ $pm->id }}" {{ old('initial_property_master_id') == $pm->id ? 'selected' : '' }}>
                            {{ $pm->property_name }} ({{ $pm->firm?->firm_name ?? 'Firm' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="wrap_initial_prop" style="display: none;">
                <label class="form-label">Select Plot / Property Unit</label>
                <select name="initial_property_id" class="form-control">
                    <option value="">-- Select Plot / Unit --</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ old('initial_property_id') == $prop->id ? 'selected' : '' }}>
                            {{ $prop->property_name }} {{ $prop->unit_no ? '(Unit #' . $prop->unit_no . ')' : '' }} — {{ $prop->firm?->firm_name ?? 'Firm' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label">Effective From (Optional)</label>
                <input type="date" name="initial_effective_from" value="{{ old('initial_effective_from') }}" class="form-control">
            </div>

            <div>
                <label class="form-label">Share Remarks (Optional)</label>
                <input type="text" name="initial_share_notes" value="{{ old('initial_share_notes') }}" class="form-control" placeholder="e.g., 25% of realized profit">
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.10);">
            <button type="submit" class="btn-primary-custom"><i class="fa-solid fa-check"></i> Save Partner</button>
            <a href="{{ route('partners.index') }}" class="btn-secondary-custom">Cancel</a>
        </div>
    </div>
</form>

<script>
function toggleScopeFields() {
    const scope = document.getElementById('initial_scope_type').value;
    document.getElementById('wrap_initial_firm').style.display    = scope === 'firm' ? 'block' : 'none';
    document.getElementById('wrap_initial_project').style.display = scope === 'project' ? 'block' : 'none';
    document.getElementById('wrap_initial_pm').style.display      = scope === 'property_master' ? 'block' : 'none';
    document.getElementById('wrap_initial_prop').style.display    = scope === 'property' ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', toggleScopeFields);
</script>
@endsection
