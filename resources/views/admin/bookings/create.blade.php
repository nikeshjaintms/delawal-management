@extends('admin.layouts.app')
@section('title', 'Add Booking')
@section('page-title', 'Booking Management')
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
    max-width: 960px; margin-left: auto; margin-right: auto;
}

.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.form-control[readonly] { background: rgba(16, 22, 34, 0.40) !important; color: #94A3B8 !important; border-color: rgba(255, 255, 255, 0.08) !important; cursor: not-allowed; }
textarea.form-control { resize: vertical; min-height: 85px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }

/* Calculator Panel Dark Glass */
.calculator-panel {
    background: rgba(16, 22, 34, 0.55) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.35) !important;
    border-radius: 16px !important;
    padding: 24px !important;
    margin: 24px 0 !important;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
}
.calc-title {
    font-size: 15px;
    font-weight: 800;
    color: #FBBF24 !important;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.summary-badge-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
    margin-top: 20px;
    padding-top: 18px;
    border-top: 1px dashed rgba(255, 255, 255, 0.15) !important;
}
.summary-item {
    background: rgba(16, 22, 34, 0.75) !important;
    backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 12px !important;
    padding: 14px 12px !important;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
}
.summary-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
}
.summary-val {
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -0.2px;
}
.summary-val.blue { color: #60A5FA !important; }
.summary-val.gold { color: #FBBF24 !important; }
.summary-val.green { color: #34D399 !important; }
.summary-val.red { color: #F87171 !important; }

.form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }
.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #CBD5E1 !important; padding: 11px 24px; border-radius: 10px; text-decoration: none !important;
    font-size: 14px; font-weight: 600; transition: all .2s ease;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.10) !important; color: #FFFFFF !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add New Booking</h2>
        <p>Register a property booking with discount calculation, customer and payment details.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
        @csrf
        @include('admin.components.firm-select')

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_id">Project / Scheme</label>
                <select name="project_id" id="project_id" class="form-control">
                    <option value="">— All Properties (Direct & Projects) —</option>
                    <option value="direct" {{ old('project_id') == 'direct' ? 'selected' : '' }}>📌 Standalone / Direct Properties (No Project)</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                            {{ $proj->project_name }}
                        </option>
                    @endforeach
                </select>
                <div style="font-size:12px;color:#94A3B8;margin-top:4px;">Filter by project or select direct standalone properties.</div>
            </div>
            <div class="form-group">
                <label class="form-label" for="property_id">Property / Unit <span>*</span></label>
                <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                    <option value="">— Select Property / Unit —</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}"
                                data-project-id="{{ $p->project_id ?? '' }}"
                                data-project="{{ $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? '') }}"
                                data-price="{{ $p->price ?? '' }}"
                                {{ old('property_id', request('property_id'))==$p->id?'selected':'' }}>
                            {{ $p->property_name }}
                            @if(!$p->project_id) [Direct Property / Standalone] @endif
                            @if($p->unit_no) (Unit: {{ $p->unit_no }}) @endif
                            @if($p->price) [₹{{ number_format($p->price, 2) }}] @endif
                        </option>
                    @endforeach
                </select>
                @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Customer <span>*</span></label>
                <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Booking Date <span>*</span></label>
                <input type="date" name="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" class="form-control @error('booking_date') is-invalid @enderror" required>
                @error('booking_date')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Broker</label>
                <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                    <option value="">No Broker</option>
                    @foreach($brokers as $b)
                        <option value="{{ $b->id }}" {{ old('broker_id')==$b->id?'selected':'' }} data-commission="{{ $b->commission_percentage }}">{{ $b->name }} ({{ $b->commission_percentage ? $b->commission_percentage.'%' : '0%' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Agreement Date</label>
                <input type="date" name="agreement_date" value="{{ old('agreement_date') }}" class="form-control @error('agreement_date') is-invalid @enderror">
            </div>
        </div>

        <!-- PRICING, DISCOUNT & PAYMENT CALCULATOR -->
        <div class="calculator-panel">
            <div class="calc-title">
                <i class="fa-solid fa-calculator"></i> Pricing & Payment Calculator
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="total_amount">Total Property Price (₹)</label>
                    <input type="number" step="0.01" min="0" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" class="form-control @error('total_amount') is-invalid @enderror" placeholder="0.00">
                    @error('total_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Discount Type</label>
                    <select name="discount_type" id="discount_type" class="form-control">
                        <option value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="discount_value">Discount Value</label>
                    <input type="number" step="0.01" min="0" name="discount_value" id="discount_value" value="{{ old('discount_value', '0') }}" class="form-control @error('discount_value') is-invalid @enderror" placeholder="E.g. 5 or 50000">
                    @error('discount_value')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="discount_amount">Discount Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', '0.00') }}" class="form-control" readonly placeholder="0.00">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="final_amount">Final / Net Payable Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="final_amount" id="final_amount" value="{{ old('final_amount') }}" class="form-control @error('final_amount') is-invalid @enderror" readonly placeholder="0.00" style="font-weight:700;color:#60A5FA !important;">
                    @error('final_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="booking_amount">New Payment / Booking Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" min="0" name="booking_amount" id="booking_amount" value="{{ old('booking_amount') }}" class="form-control @error('booking_amount') is-invalid @enderror" placeholder="0.00" required>
                    @error('booking_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="remaining_amount">Remaining / Balance Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="remaining_amount" id="remaining_amount" value="{{ old('remaining_amount') }}" class="form-control" readonly placeholder="0.00" style="font-weight:700;color:#F87171 !important;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode_id">Payment Mode <span>*</span></label>
                    <select name="payment_mode_id" id="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
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
                    <label class="form-label" for="transaction_ref">Payment / Transaction Reference</label>
                    <input type="text" name="transaction_ref" id="transaction_ref" value="{{ old('transaction_ref') }}" class="form-control @error('transaction_ref') is-invalid @enderror" placeholder="Cheque No., UPI Ref, UTR, etc.">
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                        <option value="unpaid"  {{ old('payment_status','unpaid')=='unpaid'  ?'selected':'' }}>Unpaid</option>
                        <option value="partial" {{ old('payment_status')=='partial' ?'selected':'' }}>Partial</option>
                        <option value="paid"    {{ old('payment_status')=='paid'    ?'selected':'' }}>Paid</option>
                    </select>
                    @error('payment_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Visual Live Summary Bar (Dark Glass) -->
            <div class="summary-badge-bar">
                <div class="summary-item">
                    <div class="summary-label">Total Price</div>
                    <div class="summary-val blue" id="sum_total">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Discount (-)</div>
                    <div class="summary-val gold" id="sum_discount">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Net Payable</div>
                    <div class="summary-val blue" id="sum_final">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Paid / Booking</div>
                    <div class="summary-val green" id="sum_paid">₹0.00</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Balance Due</div>
                    <div class="summary-val red" id="sum_balance">₹0.00</div>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Booking Status <span>*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="pending"   {{ old('status','pending')=='pending'   ?'selected':'' }}>Pending</option>
                    <option value="confirmed" {{ old('status')=='confirmed' ?'selected':'' }}>Confirmed</option>
                    <option value="cancelled" {{ old('status')=='cancelled' ?'selected':'' }}>Cancelled</option>
                </select>
                @error('status')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="2" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <!-- Broker Commission Details (shows only when a broker is selected) -->
        <div id="commission_section" style="display: none; margin-top: 24px; padding-top: 20px; border-top: 1px dashed rgba(255, 255, 255, 0.15);">
            <h4 style="font-size: 15px; font-weight: 700; color: #FBBF24; margin-bottom: 15px;">
                <i class="fa-solid fa-percent"></i> Broker Commission Details
            </h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Commission Type</label>
                    <select name="commission_type" id="commission_type" class="form-control">
                        <option value="percentage" {{ old('commission_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Commission Value</label>
                    <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value') }}" class="form-control" placeholder="E.g. 2.0 or 5000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Calculated Commission Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount') }}" class="form-control" placeholder="Auto-calculated">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Booking</button>
            <a href="{{ route('bookings.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalAmountInput = document.getElementById('total_amount');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountValueInput = document.getElementById('discount_value');
        const discountAmountInput = document.getElementById('discount_amount');
        const finalAmountInput = document.getElementById('final_amount');
        const bookingAmountInput = document.getElementById('booking_amount');
        const remainingAmountInput = document.getElementById('remaining_amount');
        const paymentStatusSelect = document.getElementById('payment_status');

        const sumTotal = document.getElementById('sum_total');
        const sumDiscount = document.getElementById('sum_discount');
        const sumFinal = document.getElementById('sum_final');
        const sumPaid = document.getElementById('sum_paid');
        const sumBalance = document.getElementById('sum_balance');

        function formatINR(val) {
            return '₹' + parseFloat(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function calculateAmounts(userChangedPaymentStatus = false) {
            const total = parseFloat(totalAmountInput.value) || 0;
            const discType = discountTypeSelect.value;
            const discVal = parseFloat(discountValueInput.value) || 0;

            let discountAmt = 0;
            if (discType === 'percentage') {
                discountAmt = (total * discVal) / 100;
            } else {
                discountAmt = discVal;
            }
            if (discountAmt > total) {
                discountAmt = total;
            }

            const finalAmt = Math.max(0, total - discountAmt);
            const bookingAmt = parseFloat(bookingAmountInput.value) || 0;
            const remainingAmt = Math.max(0, finalAmt - bookingAmt);

            discountAmountInput.value = discountAmt > 0 ? discountAmt.toFixed(2) : '0.00';
            finalAmountInput.value = (total > 0 || discountAmt > 0) ? finalAmt.toFixed(2) : (bookingAmt > 0 ? bookingAmt.toFixed(2) : '');
            remainingAmountInput.value = remainingAmt.toFixed(2);

            // Update Summary Bar
            sumTotal.textContent = formatINR(total);
            sumDiscount.textContent = formatINR(discountAmt);
            sumFinal.textContent = formatINR(finalAmt > 0 ? finalAmt : bookingAmt);
            sumPaid.textContent = formatINR(bookingAmt);
            sumBalance.textContent = formatINR(remainingAmt);

            // Auto suggest payment status
            if (!userChangedPaymentStatus) {
                const targetFinal = finalAmt > 0 ? finalAmt : bookingAmt;
                if (targetFinal > 0 && bookingAmt >= targetFinal) {
                    paymentStatusSelect.value = 'paid';
                } else if (bookingAmt > 0) {
                    paymentStatusSelect.value = 'partial';
                } else {
                    paymentStatusSelect.value = 'unpaid';
                }
            }

            calculateCommission();
        }

        totalAmountInput.addEventListener('input', () => calculateAmounts());
        discountTypeSelect.addEventListener('change', () => calculateAmounts());
        discountValueInput.addEventListener('input', () => calculateAmounts());
        bookingAmountInput.addEventListener('input', () => calculateAmounts());

        // Broker commission logic
        const brokerSelect = document.getElementById('broker_id');
        const commissionSection = document.getElementById('commission_section');
        const commissionType = document.getElementById('commission_type');
        const commissionValue = document.getElementById('commission_value');
        const commissionAmount = document.getElementById('commission_amount');

        function toggleCommissionSection() {
            if (brokerSelect && brokerSelect.value) {
                commissionSection.style.display = 'block';
                const option = brokerSelect.options[brokerSelect.selectedIndex];
                const defaultComm = option.getAttribute('data-commission');
                if (!commissionValue.value && defaultComm) {
                    commissionValue.value = parseFloat(defaultComm).toFixed(2);
                }
            } else if (commissionSection) {
                commissionSection.style.display = 'none';
                commissionValue.value = '';
                commissionAmount.value = '';
            }
            calculateCommission();
        }

        function calculateCommission() {
            if (!commissionType || !commissionValue || !commissionAmount) return;
            const type = commissionType.value;
            const val = parseFloat(commissionValue.value) || 0;
            const baseAmt = parseFloat(bookingAmountInput.value) || parseFloat(finalAmountInput.value) || 0;

            let calculated = 0;
            if (type === 'percentage') {
                calculated = (baseAmt * val) / 100;
            } else {
                calculated = val;
            }

            commissionAmount.value = calculated > 0 ? calculated.toFixed(2) : '';
        }

        if (brokerSelect) {
            brokerSelect.addEventListener('change', toggleCommissionSection);
            commissionType.addEventListener('change', calculateCommission);
            commissionValue.addEventListener('input', calculateCommission);
            toggleCommissionSection();
        }

        // Project to Property cascading filter & price auto-populate
        const projSelect = document.getElementById('project_id');
        const propSelect = document.getElementById('property_id');

        function filterPropertiesByProject() {
            if (!projSelect || !propSelect) return;
            const selectedProjId = projSelect.value;
            let matchCount = 0;
            let firstMatch = '';

            Array.from(propSelect.options).forEach(opt => {
                if (!opt.value) {
                    opt.hidden = false;
                    opt.disabled = false;
                    return;
                }
                const optProjId = opt.dataset.projectId || '';
                if (!selectedProjId) {
                    opt.hidden = false;
                    opt.disabled = false;
                    matchCount++;
                    if (!firstMatch) firstMatch = opt.value;
                } else if (selectedProjId === 'direct') {
                    if (!optProjId) {
                        opt.hidden = false;
                        opt.disabled = false;
                        matchCount++;
                        if (!firstMatch) firstMatch = opt.value;
                    } else {
                        opt.hidden = true;
                        opt.disabled = true;
                    }
                } else if (optProjId === selectedProjId) {
                    opt.hidden = false;
                    opt.disabled = false;
                    matchCount++;
                    if (!firstMatch) firstMatch = opt.value;
                } else {
                    opt.hidden = true;
                    opt.disabled = true;
                }
            });

            const currentSelected = propSelect.selectedOptions[0];
            if (currentSelected && currentSelected.hidden) {
                propSelect.value = '';
            }
        }

        if (projSelect && propSelect) {
            projSelect.addEventListener('change', filterPropertiesByProject);

            propSelect.addEventListener('change', function() {
                const opt = this.selectedOptions[0];
                if (opt && opt.dataset.projectId && (!projSelect.value || projSelect.value !== opt.dataset.projectId)) {
                    projSelect.value = opt.dataset.projectId;
                } else if (opt && !opt.dataset.projectId && projSelect.value && projSelect.value !== 'direct') {
                    projSelect.value = 'direct';
                }
                if (opt && opt.dataset.price) {
                    const price = parseFloat(opt.dataset.price);
                    if (price > 0 && (!totalAmountInput.value || totalAmountInput.value === '0')) {
                        totalAmountInput.value = price.toFixed(2);
                        calculateAmounts();
                    }
                }
            });

            // If pre-selected on load
            if (propSelect.value) {
                const currentOpt = propSelect.selectedOptions[0];
                if (currentOpt && currentOpt.dataset.price && (!totalAmountInput.value || totalAmountInput.value === '0')) {
                    totalAmountInput.value = parseFloat(currentOpt.dataset.price).toFixed(2);
                }
            }

            if (projSelect.value) {
                filterPropertiesByProject();
            }
        }

        calculateAmounts();
    });
</script>
@endsection
