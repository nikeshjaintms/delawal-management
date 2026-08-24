@extends('admin.layouts.app')

@section('title', 'Add Rental')
@section('page-title', 'Rental Management')

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
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
    max-width: 920px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3{ grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3{ grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
.form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.form-control[readonly] { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; border: 1px solid rgba(255, 255, 255, 0.10) !important; }
textarea.form-control { resize: vertical; min-height: 90px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

/* Select2 Glass Styling Overrides */
.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    background-color: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important; min-height: 42px !important; padding: 4px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(37, 99, 235, 0.35) !important;
    border: 1px solid rgba(59, 130, 246, 0.50) !important;
    color: #FFFFFF !important; border-radius: 6px !important; font-weight: 600; padding: 3px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #F87171 !important; margin-right: 6px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #FFFFFF !important; line-height: 32px !important;
}
.select2-dropdown { background-color: #101622 !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }
.select2-results__option { color: #CBD5E1 !important; }
.select2-results__option--highlighted[aria-selected] { background-color: #2563EB !important; color: #FFFFFF !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); font-family: inherit;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Rental</h2>
        <p>Create a new property rental or tenancy record.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('rentals.store') }}">
        @csrf
        @include('admin.components.firm-select')

        {{-- Property --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-building"></i> Property Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_id">Property <span>*</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">-- Select Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}"
                                    data-project="{{ $property->project->project_name ?? ($property->project->propertyMaster->property_name ?? 'No Project Assigned') }}"
                                    {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->property_name }}
                                @if($property->property_code) ({{ $property->property_code }}) @endif
                                @if($property->unit_no) — Unit {{ $property->unit_no }} @endif
                                — {{ ucfirst($property->status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="project_display">Project</label>
                    <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background-color:#F9FAFB; cursor:not-allowed;">
                </div>
            </div>
        </div>

        {{-- Tenant Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-user"></i> Tenant Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tenant_name">Tenant Name <span>*</span></label>
                    <input type="text" name="tenant_name" id="tenant_name" value="{{ old('tenant_name') }}"
                           class="form-control @error('tenant_name') is-invalid @enderror" autocomplete="off" placeholder="Enter tenant full name">
                    @error('tenant_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="tenant_mobile">Tenant Mobile <span>*</span></label>
                    <input type="text" name="tenant_mobile" id="tenant_mobile" value="{{ old('tenant_mobile') }}"
                           class="form-control @error('tenant_mobile') is-invalid @enderror" autocomplete="off" placeholder="Enter tenant contact number">
                    @error('tenant_mobile') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tenant_email">Tenant Email</label>
                    <input type="email" name="tenant_email" id="tenant_email" value="{{ old('tenant_email') }}"
                           class="form-control @error('tenant_email') is-invalid @enderror" placeholder="Enter tenant email address">
                    @error('tenant_email') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Rent Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Rent & Deposit</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="rent_amount">Monthly Rent Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="rent_amount" id="rent_amount" value="{{ old('rent_amount') }}"
                           class="form-control @error('rent_amount') is-invalid @enderror" placeholder="Enter monthly rent">
                    @error('rent_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="security_deposit">Security Deposit (₹)</label>
                    <input type="number" step="0.01" name="security_deposit" id="security_deposit" value="{{ old('security_deposit') }}"
                           class="form-control @error('security_deposit') is-invalid @enderror" placeholder="Enter security deposit amount">
                    @error('security_deposit') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Rental Period</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="rent_start_date">Rent Start Date <span>*</span></label>
                    <input type="date" name="rent_start_date" id="rent_start_date"
                           value="{{ old('rent_start_date') }}" class="form-control @error('rent_start_date') is-invalid @enderror">
                    @error('rent_start_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rent_end_date">Rent End Date</label>
                    <input type="date" name="rent_end_date" id="rent_end_date"
                           value="{{ old('rent_end_date') }}" class="form-control @error('rent_end_date') is-invalid @enderror">
                    @error('rent_end_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rent_due_date">Rent Due Day of Month</label>
                    <input type="number" name="rent_due_date" id="rent_due_date" min="1" max="31"
                           value="{{ old('rent_due_date') }}" class="form-control @error('rent_due_date') is-invalid @enderror" placeholder="e.g. 5">
                    <div class="form-hint">Day of month when rent is due (1–31).</div>
                    @error('rent_due_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('payment_status', 'pending') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rental_status">Rental Status <span>*</span></label>
                    <select name="rental_status" id="rental_status" class="form-control @error('rental_status') is-invalid @enderror">
                        @foreach(['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('rental_status', 'active') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Active → sets property as Rented. Completed/Cancelled → sets property as Available.</div>
                    @error('rental_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any notes or remarks about this rental agreement...">{{ old('remarks') }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Rental
            </button>
            <a href="{{ route('rentals.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

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
