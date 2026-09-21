@extends('admin.layouts.app')
@section('title','Add Loan')
@section('page-title','Loan Management')
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
        border-radius: 24px !important;
        padding: 32px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
        max-width: 960px;
        margin: 0 auto 30px auto;
    }

    .section-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #60A5FA !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section { margin-bottom: 28px; }
    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    @media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
    @media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #CBD5E1 !important;
        margin-bottom: 7px;
    }
    .form-label span.req { color: #F87171 !important; }
    .form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

    .form-control {
        width: 100%;
        padding: 11px 14px !important;
        border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
        border-radius: 10px !important;
        font-size: 14px;
        font-family: var(--font-primary);
        color: #FFFFFF !important;
        outline: none;
        transition: all 0.2s ease;
        background: rgba(16, 22, 34, 0.85) !important;
        box-sizing: border-box;
    }
    .form-control::placeholder { color: #94A3B8 !important; }
    .form-control:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }
    select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
    textarea.form-control { resize: vertical; min-height: 85px; }

    .text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
    .form-hint { font-size: 12px; color: #94A3B8 !important; margin-top: 5px; }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.10);
        flex-wrap: wrap;
    }

    .btn-gold {
        background: #2563EB !important;
        color: #FFFFFF !important;
        padding: 11px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #3B82F6 !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .25s ease;
        box-shadow: 0 4px 18px rgba(37, 99, 235, 0.38);
        text-decoration: none !important;
    }
    .btn-gold:hover {
        background: #1D4ED8 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(37, 99, 235, 0.52);
        color: #FFFFFF !important;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 22px;
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        font-size: 13.5px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px;
        text-decoration: none !important;
        transition: all .25s ease;
        cursor: pointer;
    }
    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
    }

    /* ── Luxury Dark EMI Toggle Switch Card ── */
    .emi-toggle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 22px;
        background: rgba(16, 22, 34, 0.75) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px !important;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .emi-toggle-card:hover {
        border-color: rgba(59, 130, 246, 0.50) !important;
        background: rgba(24, 33, 51, 0.90) !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }
    .emi-toggle-card input[type="checkbox"] {
        display: none;
    }
    .toggle-title {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF !important;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.2px;
    }
    .toggle-title i {
        color: #60A5FA !important;
        font-size: 16px;
    }
    .toggle-desc {
        font-size: 12.5px;
        color: #94A3B8 !important;
        margin-top: 5px;
        font-weight: 500;
        line-height: 1.4;
    }
    .switch-ui {
        width: 52px;
        height: 28px;
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 30px;
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .switch-ui::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: #CBD5E1;
        border-radius: 50%;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }
    .emi-toggle-card input[type="checkbox"]:checked ~ .switch-ui {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 0 14px rgba(37, 99, 235, 0.55);
    }
    .emi-toggle-card input[type="checkbox"]:checked ~ .switch-ui::after {
        transform: translateX(24px);
        background: #FFFFFF !important;
    }
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Add Loan</h2><p>Create a new loan record.</p></div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('loans.store') }}" id="loanForm">
        @csrf

        {{-- Section 1: Loan Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-landmark"></i> Loan Information</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Type <span class="req">*</span></label>
                    <select name="loan_type" id="loan_type" class="form-control @error('loan_type') is-invalid @enderror" onchange="toggleLoanType()">
                        <option value="Business Loan" {{ old('loan_type', 'Business Loan') == 'Business Loan' ? 'selected' : '' }}>Business Loan</option>
                        <option value="Personal Loan" {{ old('loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                    </select>
                    @error('loan_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group business-loan-only">
                    <label class="form-label">Bank Name <span class="req">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="form-control @error('bank_name') is-invalid @enderror" placeholder="e.g. SBI, HDFC Bank, ICICI">
                    @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group personal-only" style="display:none;">
                    <label class="form-label">Person Name <span class="req">*</span></label>
                    <input type="text" name="person_name" id="person_name" value="{{ old('person_name') }}" class="form-control @error('person_name') is-invalid @enderror" placeholder="Enter person's name">
                    @error('person_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row business-loan-only">
                <div class="form-group">
                    <label class="form-label">Customer <span class="opt">(optional)</span></label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->mobile }}{{ $c->alternate_mobile ? ' / ' . $c->alternate_mobile : '' }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" data-project="{{ $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? 'No Project Assigned') }}" {{ old('property_id')==$p->id?'selected':'' }}>{{ $p->property_name }}{{ $p->property_code?' ('.$p->property_code.')':'' }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row business-loan-only">
                <div class="form-group">
                    <label class="form-label" for="project_display">Project</label>
                    <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background:rgba(16, 22, 34, 0.65) !important; cursor:not-allowed; opacity: 0.85;">
                </div>
            </div>
            <div class="form-row personal-only" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Mobile Number <span class="opt">(optional)</span></label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}" class="form-control @error('mobile_number') is-invalid @enderror" placeholder="Enter mobile number">
                    @error('mobile_number')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Relationship <span class="opt">(optional)</span></label>
                    <input type="text" name="relationship" value="{{ old('relationship') }}" class="form-control @error('relationship') is-invalid @enderror" placeholder="e.g. Friend, Brother, Relative">
                    @error('relationship')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- EMI Toggle Section --}}
        <div class="form-section">
            <label class="emi-toggle-card" for="has_emi">
                <input type="checkbox" name="has_emi" id="has_emi" value="1" {{ old('has_emi', '1') == '1' ? 'checked' : '' }} onchange="toggleEmiSection()">
                <div class="toggle-content">
                    <div class="toggle-title"><i class="fa-solid fa-calculator"></i> Enable EMI Schedule</div>
                    <div class="toggle-desc">Tick this if the loan has monthly EMI installments. Uncheck if you prefer manual / lumpsum repayment without EMI schedule.</div>
                </div>
                <div class="switch-ui"></div>
            </label>
        </div>

        {{-- Section 2: Financial Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="loan_amount" id="loan_amount" value="{{ old('loan_amount') }}" class="form-control @error('loan_amount') is-invalid @enderror" placeholder="0.00" oninput="calcEmi()">
                    @error('loan_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Mode <span class="opt">(optional)</span></label>
                    <select name="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
                        <option value="">— Select Payment Mode —</option>
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}" {{ old('payment_mode_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Status <span class="req">*</span></label>
                    <select name="loan_status" class="form-control @error('loan_status') is-invalid @enderror">
                        @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('loan_status','Active')==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('loan_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" id="start_date_label">Loan Start Date <span class="req">*</span></label>
                    <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', date('Y-m-d')) }}" class="form-control @error('loan_start_date') is-invalid @enderror" onchange="calcEmi()">
                    @error('loan_start_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- EMI Specific Fields --}}
            <div id="emi_fields_wrapper">
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label">Interest Rate (% p.a.) <span class="opt">(optional)</span></label>
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" class="form-control @error('interest_rate') is-invalid @enderror" placeholder="e.g. 8.5" oninput="calcEmi()">
                        @error('interest_rate')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total EMI Months <span class="req emi-req">*</span></label>
                        <input type="number" name="total_emi_months" id="total_emi_months" value="{{ old('total_emi_months') }}" class="form-control @error('total_emi_months') is-invalid @enderror" placeholder="e.g. 120" oninput="calcEmi()">
                        @error('total_emi_months')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">EMI Amount (₹) <span class="req emi-req">*</span></label>
                        <input type="number" step="0.01" name="emi_amount" id="emi_amount" value="{{ old('emi_amount') }}" class="form-control @error('emi_amount') is-invalid @enderror" placeholder="Auto-calculated or enter manually">
                        <div class="form-hint">Auto-calculated based on amount, rate & tenure. You can override.</div>
                        @error('emi_amount')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Loan End Date <span class="opt">(optional)</span></label>
                        <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date') }}" class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Remarks --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            <div class="form-group">
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes about this loan...">{{ old('remarks') }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold" id="submit_btn"><i class="fa-solid fa-check"></i> Save Loan & Generate EMI</button>
            <a href="{{ route('loans.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>

<script>
function toggleLoanType() {
    const loanType = document.getElementById('loan_type').value;
    const businessOnly = document.querySelectorAll('.business-loan-only');
    const personalOnly = document.querySelectorAll('.personal-only');
    const startDateLabel = document.getElementById('start_date_label');

    if (loanType === 'Personal Loan') {
        businessOnly.forEach(el => el.style.display = 'none');
        personalOnly.forEach(el => el.style.display = 'block');
        startDateLabel.innerHTML = 'Loan Date <span class="req">*</span>';
    } else {
        businessOnly.forEach(el => el.style.display = 'block');
        personalOnly.forEach(el => el.style.display = 'none');
        startDateLabel.innerHTML = 'Loan Start Date <span class="req">*</span>';
    }
    updateSubmitButton();
}

function toggleEmiSection() {
    const hasEmi = document.getElementById('has_emi').checked;
    const emiWrapper = document.getElementById('emi_fields_wrapper');
    const emiInputs = emiWrapper.querySelectorAll('input');

    if (hasEmi) {
        emiWrapper.style.display = 'block';
        emiInputs.forEach(i => i.disabled = false);
        calcEmi();
    } else {
        emiWrapper.style.display = 'none';
        emiInputs.forEach(i => i.disabled = true);
    }
    updateSubmitButton();
}

function updateSubmitButton() {
    const hasEmi = document.getElementById('has_emi').checked;
    const loanType = document.getElementById('loan_type').value;
    const submitBtn = document.getElementById('submit_btn');

    if (hasEmi) {
        submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Save Loan & Generate EMI';
    } else {
        submitBtn.innerHTML = loanType === 'Personal Loan'
            ? '<i class="fa-solid fa-check"></i> Save Personal Loan'
            : '<i class="fa-solid fa-check"></i> Save Loan';
    }
}

function calcEmi() {
    const hasEmi = document.getElementById('has_emi').checked;
    if (!hasEmi) return;

    const P = parseFloat(document.getElementById('loan_amount').value) || 0;
    const annualRate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const n = parseInt(document.getElementById('total_emi_months').value) || 0;

    if (P > 0 && annualRate > 0 && n > 0) {
        const r = annualRate / 12 / 100;
        const emi = P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
        document.getElementById('emi_amount').value = emi.toFixed(2);
    } else if (P > 0 && n > 0 && annualRate === 0) {
        // 0% interest rate
        const emi = P / n;
        document.getElementById('emi_amount').value = emi.toFixed(2);
    }

    // Auto-calculate end date
    const startDate = document.getElementById('loan_start_date').value;
    if (startDate && n > 0) {
        const d = new Date(startDate);
        d.setMonth(d.getMonth() + n);
        document.getElementById('loan_end_date').value = d.toISOString().split('T')[0];
    }
}

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
    toggleLoanType();
    toggleEmiSection();
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
