@extends('admin.layouts.app')

@section('title', 'Edit Broker Commission')
@section('page-title', 'Edit Commission')

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
@media (max-width: 768px) { .form-row { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13.5px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }

.form-control {
    width: 100% !important; padding: 11px 16px !important;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important; font-size: 13.5px !important;
    font-family: var(--font-primary) !important; color: #FFFFFF !important;
    outline: none !important; transition: all 0.2s ease !important; box-sizing: border-box !important;
}
input.form-control, select.form-control { height: 44px !important; }
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
textarea.form-control { min-height: 90px !important; height: auto !important; resize: vertical !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(185deg);
    cursor: pointer;
}
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none !important; margin: 0 !important;
}
input[type=number] { -moz-appearance: textfield !important; }

.text-error { color: #F87171; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #94A3B8; margin-top: 6px; }

.form-actions {
    display: flex; align-items: center; justify-content: flex-end; gap: 14px;
    margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10);
}

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
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
        <h2>Edit Broker Commission</h2>
        <p>Update commission payout details for the broker.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('broker-commissions.update', $commission->id) }}" id="commissionForm" autocomplete="off">
        @csrf
        @method('PUT')

        @include('admin.components.firm-select', ['model' => $commission])

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="broker_id">Broker <span>*</span></label>
                <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                    <option value="">Select Broker</option>
                    @foreach($brokers as $b)
                        <option value="{{ $b->id }}" {{ old('broker_id', $commission->broker_id) == $b->id ? 'selected' : '' }} data-commission="{{ $b->commission_percentage }}">{{ $b->name }} ({{ $b->mobile }})</option>
                    @endforeach
                </select>
                @error('broker_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="property_id">Property <span>*</span></label>
                <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                    <option value="">Select Property</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" data-project="{{ $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? 'No Project Assigned') }}" {{ old('property_id', $commission->property_id) == $p->id ? 'selected' : '' }}>{{ $p->property_name }} (₹{{ number_format($p->price, 0) }})</option>
                    @endforeach
                </select>
                @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="project_display">Project</label>
            <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background:rgba(255,255,255,0.06) !important; cursor:not-allowed;">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="booking_id">Booking (Optional)</label>
                <select name="booking_id" id="booking_id" class="form-control @error('booking_id') is-invalid @enderror">
                    <option value="">Select Booking</option>
                    @foreach($bookings as $bk)
                        <option value="{{ $bk->id }}" {{ old('booking_id', $commission->booking_id) == $bk->id ? 'selected' : '' }} data-property="{{ $bk->property_id }}" data-customer="{{ $bk->customer_id }}">
                            Booking #{{ $bk->id }} - {{ $bk->property->property_name ?? '-' }} ({{ $bk->customer->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('booking_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="customer_id">Customer (Optional)</label>
                <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id', $commission->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->mobile }})</option>
                    @endforeach
                </select>
                @error('customer_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="base_amount">Base Amount (₹)</label>
                @php
                    $initialBase = 0.00;
                    if ($commission->booking_id && isset($commission->booking->booking_amount)) {
                        $initialBase = $commission->booking->booking_amount;
                    } elseif ($commission->property_id && isset($commission->property->price)) {
                        $initialBase = $commission->property->price;
                    }
                    if ($commission->commission_type == 'percentage' && $commission->commission_value > 0) {
                        $initialBase = ($commission->commission_amount / $commission->commission_value) * 100;
                    }
                @endphp
                <input type="number" step="0.01" min="0" name="base_amount" id="base_amount" value="{{ old('base_amount', number_format($initialBase, 2, '.', '')) }}" class="form-control @error('base_amount') is-invalid @enderror" placeholder="E.g. Booking amount or Property price">
                <div class="form-hint">Used for percentage-based commission calculations.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="commission_type">Commission Type <span>*</span></label>
                <select name="commission_type" id="commission_type" class="form-control @error('commission_type') is-invalid @enderror">
                    <option value="percentage" {{ old('commission_type', $commission->commission_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('commission_type', $commission->commission_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                </select>
                @error('commission_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="commission_value">Commission Value <span>*</span></label>
                <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value', $commission->commission_value) }}" class="form-control" placeholder="E.g. 2.50 or 5000">
                @error('commission_value') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="commission_amount">Calculated Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount', $commission->commission_amount) }}" class="form-control" placeholder="Calculated automatically or entered manually">
                @error('commission_amount') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                    <option value="pending" {{ old('payment_status', $commission->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ old('payment_status', $commission->payment_status) == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ old('payment_status', $commission->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="payment_date">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $commission->payment_date ? \Carbon\Carbon::parse($commission->payment_date)->format('Y-m-d') : '') }}" class="form-control">
                @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', $commission->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $commission->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter any extra details or transaction references">{{ old('remarks', $commission->remarks) }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Update Commission
            </button>
            <a href="{{ route('broker-commissions.index') }}" class="btn-outline">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const propertyPrices = @json($properties->pluck('price', 'id'));
    const bookingAmounts = @json($bookings->pluck('booking_amount', 'id'));

    const brokerSelect = document.getElementById('broker_id');
    const propertySelect = document.getElementById('property_id');
    const bookingSelect = document.getElementById('booking_id');
    const customerSelect = document.getElementById('customer_id');

    const baseAmountInput = document.getElementById('base_amount');
    const typeSelect = document.getElementById('commission_type');
    const valueInput = document.getElementById('commission_value');
    const amountInput = document.getElementById('commission_amount');

    bookingSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option && option.value) {
            const propId = option.getAttribute('data-property');
            const custId = option.getAttribute('data-customer');

            if (propId) propertySelect.value = propId;
            if (custId) customerSelect.value = custId;

            const amt = bookingAmounts[option.value];
            if (amt) {
                baseAmountInput.value = parseFloat(amt).toFixed(2);
            }
        }
        calculateCommission();
    });

    propertySelect.addEventListener('change', function() {
        if (!bookingSelect.value) {
            const val = propertyPrices[this.value];
            if (val) {
                baseAmountInput.value = parseFloat(val).toFixed(2);
            }
        }
        calculateCommission();
    });

    brokerSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option && option.value && typeSelect.value === 'percentage') {
            const comm = option.getAttribute('data-commission');
            if (comm) {
                valueInput.value = parseFloat(comm).toFixed(2);
            }
        }
        calculateCommission();
    });

    function calculateCommission() {
        const type = typeSelect.value;
        const val = parseFloat(valueInput.value) || 0;
        const base = parseFloat(baseAmountInput.value) || 0;

        let calculatedAmount = 0;
        if (type === 'percentage') {
            calculatedAmount = (base * val) / 100;
        } else {
            calculatedAmount = val;
        }

        amountInput.value = calculatedAmount.toFixed(2);
    }

    [baseAmountInput, typeSelect, valueInput].forEach(elem => {
        elem.addEventListener('input', calculateCommission);
        elem.addEventListener('change', calculateCommission);
    });

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

    const propSelect = document.getElementById('property_id');
    if (propSelect) {
        propSelect.addEventListener('change', updateProjectMapping);
        if (window.jQuery) {
            jQuery('#property_id').on('change select2:select select2:unselect', updateProjectMapping);
        }
        updateProjectMapping();
    }
</script>
@endsection
