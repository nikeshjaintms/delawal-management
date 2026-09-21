@extends('admin.layouts.app')
@section('title', 'Record Labour Payment')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass Form System ── */
.form-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.form-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.form-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-back {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    transition: all .25s ease;
}
.btn-back:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }

.glass-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 30px;
}

.section-divider {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin: 28px 0 18px 0; display: flex; align-items: center; gap: 10px;
}
.section-divider::after { content: ''; flex: 1; height: 1px; background: rgba(255, 255, 255, 0.10); }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group.full-width { grid-column: 1 / -1; }

.form-label { font-size: 13px; font-weight: 700; color: #CBD5E1 !important; display: flex; align-items: center; gap: 4px; }
.form-label .req { color: #F87171; font-weight: 800; }

.form-control-glass {
    width: 100%; padding: 12px 16px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 12px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .25s ease;
    box-sizing: border-box;
}
.form-control-glass:focus {
    border-color: #3B82F6 !important; background: rgba(16, 22, 34, 0.85) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}
select.form-control-glass option { background: #101622 !important; color: #FFFFFF !important; }

.labour-preview-box {
    background: rgba(37, 99, 235, 0.10); border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 14px; padding: 16px 20px; margin-bottom: 24px; display: flex;
    justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
}
.preview-stat { display: flex; flex-direction: column; gap: 3px; }
.preview-stat span { font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; }
.preview-stat strong { font-size: 16px; font-weight: 800; color: #FFFFFF; }

.calc-summary-card {
    background: rgba(16, 185, 129, 0.10); border: 1px solid rgba(16, 185, 129, 0.30);
    border-radius: 16px; padding: 20px; margin-top: 20px; display: flex;
    justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;
}

.btn-submit {
    background: #2563EB !important; color: #FFFFFF !important; padding: 14px 32px;
    border-radius: 12px; font-size: 15px; font-weight: 800; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.40);
    display: inline-flex; align-items: center; gap: 10px;
}
.btn-submit:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 8px 26px rgba(37, 99, 235, 0.55); }

.checkbox-wrap { display: flex; align-items: center; gap: 10px; cursor: pointer; color: #E2E8F0; font-size: 13.5px; font-weight: 600; margin-top: 6px; }
.checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: #2563EB; cursor: pointer; }
</style>

<div class="form-header">
    <div class="form-title">
        <h2><i class="fa-solid fa-hand-holding-dollar" style="color: #60A5FA;"></i> Record Labour Payment</h2>
        <p>Log daily wages, advance disbursement, salary payment, or wage settlements.</p>
    </div>
    <a href="{{ route('agriculture.labour-payments.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Ledger</a>
</div>

<div class="glass-card">
    <form method="POST" action="{{ route('agriculture.labour-payments.store') }}" id="paymentForm">
        @csrf

        <div class="section-divider"><i class="fa-solid fa-user-tag"></i> 1. Labour & Farm Selection</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Select Labour <span class="req">*</span></label>
                <select name="labour_id" id="labourSelect" class="form-control-glass" required onchange="onLabourChange()">
                    <option value="">-- Choose Labour Worker --</option>
                    @foreach($labours as $l)
                        <option value="{{ $l->id }}" 
                            data-type="{{ $l->labour_type }}"
                            data-farm-id="{{ $l->farm_id }}"
                            data-farm-name="{{ $l->farm?->farm_name }}"
                            data-wage="{{ $l->daily_wage }}"
                            data-salary="{{ $l->monthly_salary }}"
                            data-advance-bal="{{ $l->advance_balance }}"
                            data-pending="{{ $l->pending_amount }}"
                            {{ (old('labour_id', request('labour_id')) == $l->id || ($selectedLabour && $selectedLabour->id == $l->id)) ? 'selected' : '' }}>
                            {{ $l->name }} ({{ $l->labour_type }}) - {{ $l->farm?->farm_name ?: 'General' }}
                        </option>
                    @endforeach
                </select>
                @error('labour_id')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Assigned Farm</label>
                <select name="farm_id" id="farmSelect" class="form-control-glass">
                    <option value="">-- Select or Auto-filled Farm --</option>
                    @foreach($farms as $f)
                        <option value="{{ $f->id }}" {{ old('farm_id') == $f->id ? 'selected' : '' }}>{{ $f->farm_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Dynamic Labour Info Banner -->
        <div id="labourPreviewBox" class="labour-preview-box" style="display: none; margin-top: 16px;">
            <div class="preview-stat">
                <span>Labour Type</span>
                <strong id="previewType" style="color: #60A5FA;">—</strong>
            </div>
            <div class="preview-stat">
                <span>Standard Rate</span>
                <strong id="previewRate" style="color: #FBBF24;">₹0.00</strong>
            </div>
            <div class="preview-stat">
                <span>Current Advance Balance</span>
                <strong id="previewAdvance" style="color: #F87171;">₹0.00</strong>
            </div>
            <div class="preview-stat">
                <span>Pending Wages</span>
                <strong id="previewPending" style="color: #34D399;">₹0.00</strong>
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-calculator"></i> 2. Wage & Payment Calculation</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Payment Type <span class="req">*</span></label>
                <select name="payment_type" id="paymentTypeSelect" class="form-control-glass" required onchange="onPaymentTypeChange()">
                    @foreach($paymentTypes as $pt)
                        <option value="{{ $pt }}" {{ old('payment_type', 'Daily Wage') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Date <span class="req">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-control-glass" required>
            </div>

            <div class="form-group" id="groupWorkingDays">
                <label class="form-label">Working Days</label>
                <input type="number" step="0.5" min="0" name="working_days" id="workingDaysInput" value="{{ old('working_days', 1) }}" class="form-control-glass" placeholder="e.g. 7 or 15.5" oninput="calculateAmounts()">
            </div>

            <div class="form-group" id="groupDailyWage">
                <label class="form-label">Daily Wage Rate (₹)</label>
                <input type="number" step="0.01" min="0" name="daily_wage_rate" id="dailyWageRateInput" value="{{ old('daily_wage_rate', 0) }}" class="form-control-glass" placeholder="Wage rate per day" oninput="calculateAmounts()">
            </div>

            <div class="form-group" id="groupGross">
                <label class="form-label">Gross Wage / Amount (₹)</label>
                <input type="number" step="0.01" min="0" name="gross_amount" id="grossAmountInput" value="{{ old('gross_amount', 0) }}" class="form-control-glass" placeholder="Gross total" oninput="calculateAmounts()">
            </div>

            <div class="form-group" id="groupAdvDeduct">
                <label class="form-label">Advance to Deduct (₹)</label>
                <input type="number" step="0.01" min="0" name="advance_deducted" id="advDeductedInput" value="{{ old('advance_deducted', 0) }}" class="form-control-glass" placeholder="Deduct against advance" oninput="calculateAmounts()">
            </div>

            <div class="form-group">
                <label class="form-label">Net Paid Amount (₹) <span class="req">*</span></label>
                <input type="number" step="0.01" min="0" name="amount" id="netAmountInput" value="{{ old('amount', 0) }}" class="form-control-glass" required style="font-weight: 800; font-size: 16px; color: #34D399 !important;">
                @error('amount')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Payment Status <span class="req">*</span></label>
                <select name="payment_status" class="form-control-glass" required>
                    <option value="Paid" {{ old('payment_status', 'Paid') == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-wallet"></i> 3. Mode, Sync & Reference</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode_id" class="form-control-glass">
                    <option value="">-- Cash / Select Mode --</option>
                    @foreach($paymentModes as $pm)
                        <option value="{{ $pm->id }}" {{ old('payment_mode_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Reference No / Transaction ID</label>
                <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control-glass" placeholder="Cheque #, UPI Ref, UTR...">
            </div>

            <div class="form-group full-width">
                <label class="checkbox-wrap">
                    <input type="checkbox" name="sync_to_expense" value="1" {{ old('sync_to_expense', '1') ? 'checked' : '' }}>
                    <span>Auto-record this payout under <strong>Agriculture Expenses</strong> (Category: Labour)</span>
                </label>
                <div style="font-size: 12px; color: #94A3B8; margin-top: 4px; padding-left: 28px;">
                    Keeping this checked ensures your Farm Profit/Loss reports accurately calculate labour expenditure automatically.
                </div>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Remarks / Description</label>
                <textarea name="notes" rows="3" class="form-control-glass" placeholder="Add specific notes, work description, or deductions notes...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 14px;">
            <a href="{{ route('agriculture.labour-payments.index') }}" class="btn-back">Cancel</a>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-check-circle"></i> Save Payment Record</button>
        </div>
    </form>
</div>

<script>
function onLabourChange() {
    const sel = document.getElementById('labourSelect');
    const opt = sel.options[sel.selectedIndex];
    const preview = document.getElementById('labourPreviewBox');
    
    if (!opt || !opt.value) {
        preview.style.display = 'none';
        return;
    }

    preview.style.display = 'flex';
    const lType = opt.dataset.type || 'Normal (Daily Wage)';
    document.getElementById('previewType').innerText = lType;

    const farmId = opt.dataset.farmId;
    if (farmId) {
        document.getElementById('farmSelect').value = farmId;
    }

    const wage = parseFloat(opt.dataset.wage) || 0;
    const salary = parseFloat(opt.dataset.salary) || 0;
    const advBal = parseFloat(opt.dataset.advanceBal) || 0;
    const pending = parseFloat(opt.dataset.pending) || 0;

    document.getElementById('previewRate').innerText = lType === 'Fixed (Monthly)' ? '₹' + salary.toFixed(2) + '/mo' : '₹' + wage.toFixed(2) + '/day';
    document.getElementById('previewAdvance').innerText = '₹' + advBal.toFixed(2);
    document.getElementById('previewPending').innerText = '₹' + pending.toFixed(2);

    // Auto set wage rate if Daily Wage
    if (lType === 'Normal (Daily Wage)' || lType === 'Advance Labour') {
        document.getElementById('dailyWageRateInput').value = wage;
    } else if (lType === 'Fixed (Monthly)') {
        document.getElementById('paymentTypeSelect').value = 'Salary';
        document.getElementById('grossAmountInput').value = salary;
    }

    onPaymentTypeChange();
}

function onPaymentTypeChange() {
    const pType = document.getElementById('paymentTypeSelect').value;
    const grpDays = document.getElementById('groupWorkingDays');
    const grpWage = document.getElementById('groupDailyWage');
    const grpGross = document.getElementById('groupGross');
    const grpAdvDeduct = document.getElementById('groupAdvDeduct');

    if (pType === 'Daily Wage') {
        grpDays.style.display = 'flex';
        grpWage.style.display = 'flex';
        grpGross.style.display = 'flex';
        grpAdvDeduct.style.display = 'flex';
    } else if (pType === 'Salary') {
        grpDays.style.display = 'none';
        grpWage.style.display = 'none';
        grpGross.style.display = 'flex';
        grpAdvDeduct.style.display = 'flex';
    } else if (pType === 'Advance Given' || pType === 'Bonus') {
        grpDays.style.display = 'none';
        grpWage.style.display = 'none';
        grpGross.style.display = 'none';
        grpAdvDeduct.style.display = 'none';
    } else if (pType === 'Advance Deduction') {
        grpDays.style.display = 'none';
        grpWage.style.display = 'none';
        grpGross.style.display = 'none';
        grpAdvDeduct.style.display = 'none';
    }

    calculateAmounts();
}

function calculateAmounts() {
    const pType = document.getElementById('paymentTypeSelect').value;
    const days = parseFloat(document.getElementById('workingDaysInput').value) || 0;
    const rate = parseFloat(document.getElementById('dailyWageRateInput').value) || 0;
    const advDeduct = parseFloat(document.getElementById('advDeductedInput').value) || 0;
    
    let gross = parseFloat(document.getElementById('grossAmountInput').value) || 0;

    if (pType === 'Daily Wage') {
        if (days > 0 && rate > 0) {
            gross = days * rate;
            document.getElementById('grossAmountInput').value = gross.toFixed(2);
        }
        const net = Math.max(0, gross - advDeduct);
        document.getElementById('netAmountInput').value = net.toFixed(2);
    } else if (pType === 'Salary') {
        const net = Math.max(0, gross - advDeduct);
        document.getElementById('netAmountInput').value = net.toFixed(2);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('labourSelect').value) {
        onLabourChange();
    } else {
        onPaymentTypeChange();
    }
});
</script>
@endsection
