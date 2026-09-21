@extends('admin.layouts.app')
@section('title', 'Register Labour')
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
    max-width: 900px; margin: 0 auto 30px auto;
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
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); }

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
        <h2>Register Agriculture Labour</h2>
        <p>Add a new farm worker, specify labour type, wage rate/salary, and optional advance.</p>
    </div>
    <a href="{{ route('agriculture.labours.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Labours</a>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('agriculture.labours.store') }}">
        @csrf

        {{-- Section 1: Basic Information --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-user"></i> Labour Personal Information</div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Labour Name <span class="req">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Ramesh Kumar / Worker Name" required>
                    @error('name')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number <span class="opt">(optional)</span></label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" class="form-control @error('mobile_number') is-invalid @enderror" placeholder="10-digit mobile number">
                    @error('mobile_number')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Firm <span class="req">*</span></label>
                    <select name="firm_id" class="form-control @error('firm_id') is-invalid @enderror">
                        @foreach($firms as $f)
                            <option value="{{ $f->id }}" {{ old('firm_id', session('firm_id')) == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                        @endforeach
                    </select>
                    @error('firm_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Assigned Farm / Land <span class="opt">(optional)</span></label>
                    <select name="farm_id" class="form-control @error('farm_id') is-invalid @enderror">
                        <option value="">— General Farm Labour —</option>
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id') == $farm->id ? 'selected' : '' }}>{{ $farm->farm_name }}</option>
                        @endforeach
                    </select>
                    @error('farm_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Field Activity / Crop <span class="opt">(optional)</span></label>
                    <input type="text" name="field_crop" value="{{ old('field_crop') }}" class="form-control @error('field_crop') is-invalid @enderror" placeholder="e.g. Tractor Driver, Irrigation, Harvesting, General">
                    @error('field_crop')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', date('Y-m-d')) }}" class="form-control @error('joining_date') is-invalid @enderror">
                    @error('joining_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Wage & Compensation Logic --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calculator"></i> Wage & Compensation Details</div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Labour Type <span class="req">*</span></label>
                    <select name="labour_type" id="labour_type" class="form-control @error('labour_type') is-invalid @enderror" onchange="onLabourTypeChange(this.value)">
                        @foreach($labourTypes as $lt)
                            <option value="{{ $lt }}" {{ old('labour_type', 'Normal Labour') == $lt ? 'selected' : '' }}>{{ $lt }}</option>
                        @endforeach
                    </select>
                    @error('labour_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" id="group_daily_wage">
                    <label class="form-label">Daily Wage (₹ / Day)</label>
                    <input type="number" step="0.01" name="daily_wage" id="daily_wage" value="{{ old('daily_wage') }}" class="form-control @error('daily_wage') is-invalid @enderror" placeholder="e.g. 450.00">
                    @error('daily_wage')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" id="group_fixed_salary" style="display:none;">
                    <label class="form-label">Fixed Monthly Salary (₹ / Month)</label>
                    <input type="number" step="0.01" name="fixed_salary" id="fixed_salary" value="{{ old('fixed_salary') }}" class="form-control @error('fixed_salary') is-invalid @enderror" placeholder="e.g. 15000.00">
                    @error('fixed_salary')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group" id="group_initial_advance">
                    <label class="form-label">Initial Advance Given (₹) <span class="opt">(optional)</span></label>
                    <input type="number" step="0.01" name="initial_advance" id="initial_advance" value="{{ old('initial_advance', 0) }}" class="form-control @error('initial_advance') is-invalid @enderror" placeholder="0.00">
                    @error('initial_advance')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status <span class="req">*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="Active" {{ old('status', 'Active') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Notes & Remarks <span class="opt">(optional)</span></label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="ID proof details, village address, guarantor notes...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Register Labour</button>
            <a href="{{ route('agriculture.labours.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function onLabourTypeChange(type) {
    const dailyWageGrp = document.getElementById('group_daily_wage');
    const fixedSalGrp  = document.getElementById('group_fixed_salary');

    if (type === 'Fixed Labour') {
        dailyWageGrp.style.display = 'none';
        fixedSalGrp.style.display = 'block';
    } else if (type === 'Advance Labour') {
        dailyWageGrp.style.display = 'block';
        fixedSalGrp.style.display = 'none';
    } else {
        // Normal Labour
        dailyWageGrp.style.display = 'block';
        fixedSalGrp.style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    onLabourTypeChange(document.getElementById('labour_type').value);
});
</script>
@endsection
