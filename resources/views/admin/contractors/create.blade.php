@extends('admin.layouts.app')
@section('title', 'Add Contractor')
@section('page-title', 'Contractor Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-pc, .btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover, .btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-sc, .btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover, .btn-secondary-custom:hover {
    background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
    max-width: 820px;
}

.section-heading {
    font-size: 12.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #60A5FA !important;
    margin-top: 18px;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:640px){.form-grid{grid-template-columns:1fr}}
.form-group { margin-bottom: 16px; }
.form-group:last-child { margin-bottom: 0; }

.form-label {
    display: block;
    font-size: 12.5px;
    font-weight: 700;
    color: #CBD5E1 !important;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.form-label span { color: #F87171; }

.form-control, select.form-control, input[type="text"].form-control, textarea.form-control {
    width: 100%;
    padding: 11px 16px;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 14px;
    font-family: var(--font-primary);
    color: #FFFFFF !important;
    outline: none;
    transition: all .2s ease;
    background: rgba(16, 22, 34, 0.65) !important;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

select.form-control option {
    background: #111827 !important;
    color: #FFFFFF !important;
}

textarea.form-control { resize: vertical; min-height: 80px; }
.text-error { color: #F87171; font-size: 12.5px; margin-top: 5px; font-weight: 600; }
.form-hint { font-size: 12px; color: #94A3B8; margin-top: 5px; }
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Contractor</h2>
        <p>Add project contractor with identity and bank details.</p>
    </div>
    <a href="{{ route('contractors.index') }}" class="btn-sc"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('contractors.store') }}" autocomplete="off">
@csrf

<div class="form-card">
    {{-- Firm Selection --}}
    @include('admin.components.firm-select')

    <div class="section-heading"><i class="fa-solid fa-city"></i> Project Assignment</div>
    <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label" for="project_id">Assigned Project <span>*</span></label>
            <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" required>
                <option value="">— Select Project —</option>
                @foreach($projects as $proj)
                    @php
                        $pFirmIds = $proj->firms->pluck('id')->push($proj->firm_id)->filter()->unique()->values()->all();
                    @endphp
                    <option value="{{ $proj->id }}"
                        data-firm-ids="{{ implode(',', $pFirmIds) }}"
                        {{ old('project_id', $selectedProjectId ?? '') == $proj->id ? 'selected' : '' }}>
                        {{ $proj->project_name }} {{ $proj->propertyMaster ? '('.$proj->propertyMaster->property_name.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-user-shield"></i> Contractor & ID Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label" for="contractor_name">Contractor Name <span>*</span></label>
            <input type="text" name="contractor_name" id="contractor_name" value="{{ old('contractor_name') }}"
                   class="form-control @error('contractor_name') is-invalid @enderror" placeholder="Enter contractor name" required>
            @error('contractor_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="mobile">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                   class="form-control @error('mobile') is-invalid @enderror" placeholder="10-digit mobile number" maxlength="15">
            @error('mobile')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="aadhar_no">Aadhar Card Number</label>
            <input type="text" name="aadhar_no" id="aadhar_no" value="{{ old('aadhar_no') }}"
                   class="form-control @error('aadhar_no') is-invalid @enderror" placeholder="12-digit Aadhar number" maxlength="20">
            @error('aadhar_no')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="pan_no">PAN Card Number</label>
            <input type="text" name="pan_no" id="pan_no" value="{{ old('pan_no') }}"
                   class="form-control @error('pan_no') is-invalid @enderror" placeholder="10-character PAN (e.g. ABCDE1234F)" maxlength="20">
            @error('pan_no')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-building-columns"></i> Bank Details</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label" for="bank_name">Bank Name</label>
            <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}"
                   class="form-control @error('bank_name') is-invalid @enderror" placeholder="e.g. State Bank of India, HDFC Bank">
            @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="account_number">Bank Account Number</label>
            <input type="text" name="account_number" id="account_number" value="{{ old('account_number') }}"
                   class="form-control @error('account_number') is-invalid @enderror" placeholder="Enter bank account number">
            @error('account_number')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="ifsc_code">IFSC Code</label>
            <input type="text" name="ifsc_code" id="ifsc_code" value="{{ old('ifsc_code') }}"
                   class="form-control @error('ifsc_code') is-invalid @enderror" placeholder="e.g. SBIN0001234" maxlength="20">
            @error('ifsc_code')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="branch_name">Branch / City</label>
            <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name') }}"
                   class="form-control @error('branch_name') is-invalid @enderror" placeholder="e.g. Bharuch, Main Branch">
            @error('branch_name')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="section-heading"><i class="fa-solid fa-location-dot"></i> Address & Status</div>
    <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label" for="address">Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                      placeholder="Contractor full address...">{{ old('address') }}</textarea>
            @error('address')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="status">Status <span>*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-action-buttons">
        <button type="submit" class="btn-pc"><i class="fa-solid fa-check"></i> Save Contractor</button>
        <a href="{{ route('contractors.index') }}" class="btn-sc">Cancel</a>
    </div>
</div>
</form>

<script>
function getSelectedFirmIds() {
    const firmSelect = document.getElementById('firm_ids');
    if (firmSelect) {
        if (firmSelect.multiple) {
            return Array.from(firmSelect.selectedOptions).map(o => parseInt(o.value)).filter(Boolean);
        } else if (firmSelect.value) {
            return [parseInt(firmSelect.value)];
        }
        return [];
    }
    const hiddenFirms = document.querySelectorAll('input[name="firm_ids[]"], input[name="firm_id"]');
    const ids = [];
    hiddenFirms.forEach(input => {
        if (input.value) ids.push(parseInt(input.value));
    });
    return [...new Set(ids)];
}

function filterProjectsByFirm() {
    const projSelect = document.getElementById('project_id');
    if (!projSelect) return;

    const selectedFirms = getSelectedFirmIds();
    const currentVal = projSelect.value;
    const options = projSelect.querySelectorAll('option');

    let currentStillVisible = false;

    options.forEach(opt => {
        if (!opt.value) return;
        const firmIdsStr = opt.getAttribute('data-firm-ids') || '';
        const firmIds = firmIdsStr.split(',').map(s => parseInt(s.trim())).filter(Boolean);

        let match = false;
        if (selectedFirms.length === 0 || firmIds.length === 0) {
            match = true;
        } else {
            match = firmIds.some(f => selectedFirms.includes(f));
        }

        if (match) {
            opt.style.display = '';
            if (opt.value === currentVal) currentStillVisible = true;
        } else {
            opt.style.display = 'none';
        }
    });

    if (!currentStillVisible && currentVal) {
        projSelect.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    filterProjectsByFirm();

    if (window.jQuery && $('#firm_ids').length) {
        $('#firm_ids').on('change select2:select select2:unselect', function() {
            filterProjectsByFirm();
        });
    }
    const firmEl = document.getElementById('firm_ids');
    if (firmEl) {
        firmEl.addEventListener('change', filterProjectsByFirm);
    }
});
</script>
@endsection
