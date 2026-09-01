@extends('admin.layouts.app')

@section('title', 'Add Broker Commission')
@section('page-title', 'Add Commission')

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
        <h2>Add Broker Commission</h2>
        <p>Record a new commission payout entry for a broker.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('broker-commissions.store') }}" id="commissionForm" autocomplete="off">
        @csrf

        @include('admin.components.firm-select')

        <!-- Top Row: Project First & Broker -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_select">Project <span>*</span></label>
                <select id="project_select" class="form-control" onchange="filterPropertiesByProject(this.value)">
                    <option value="">-- Select Project / All Projects --</option>
                    @if(isset($projects))
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">
                                {{ $proj->project_name }} {{ $proj->propertyMaster ? '('.$proj->propertyMaster->property_name.')' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                <div class="form-hint">Selecting a project filters properties/plots below.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="broker_id">Broker <span>*</span></label>
                <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                    <option value="">Select Broker</option>
                    @foreach($brokers as $b)
                        <option value="{{ $b->id }}" {{ old('broker_id') == $b->id ? 'selected' : '' }} data-commission="{{ $b->commission_percentage }}" data-project-id="{{ $b->project_id }}">
                            {{ $b->name }} ({{ $b->mobile }}) — {{ $b->commission_percentage ? $b->commission_percentage.'%' : '0%' }} Commission
                        </option>
                    @endforeach
                </select>
                <div id="brokerCommissionInfoBox" style="display: none; align-items: center; gap: 6px; margin-top: 8px; font-size: 12px; font-weight: 700; color: #60A5FA; background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.30); padding: 5px 12px; border-radius: 8px;">
                    <i class="fa-solid fa-percent"></i> Master Commission: <span id="brokerCommissionInfoVal" style="color: #FBBF24;">0%</span> (Auto-loaded)
                </div>
                @error('broker_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Next Row: Property / Plot & Customer -->
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="property_id">Property / Plot <span>*</span></label>
                <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                    <option value="">Select Property / Plot</option>
                    @foreach($properties as $p)
                        @php
                            $propMasterTitle = $p->propertyMaster->property_name ?? ($p->project->propertyMaster->property_name ?? ($p->project->project_name ?? 'Property'));
                        @endphp
                        <option value="{{ $p->id }}" data-project-id="{{ $p->project_id }}" data-project="{{ $propMasterTitle }}" data-price="{{ $p->price }}" {{ old('property_id') == $p->id ? 'selected' : '' }}>
                            {{ $propMasterTitle }} — {{ $p->property_name }} - ₹{{ number_format($p->price, 0) }}
                        </option>
                    @endforeach
                </select>
                @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="customer_id">Customer (Optional)</label>
                <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->mobile }})</option>
                    @endforeach
                </select>
                @error('customer_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="base_amount">Base Amount (₹)</label>
                <input type="number" step="0.01" min="0" name="base_amount" id="base_amount" value="{{ old('base_amount', '0.00') }}" class="form-control @error('base_amount') is-invalid @enderror" placeholder="E.g. Booking amount or Property price">
                <div class="form-hint">Used for percentage-based commission calculations.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="commission_type">Commission Type <span>*</span></label>
                <select name="commission_type" id="commission_type" class="form-control @error('commission_type') is-invalid @enderror">
                    <option value="percentage" {{ old('commission_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                </select>
                @error('commission_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="commission_value">Commission Value <span>*</span></label>
                <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value') }}" class="form-control @error('commission_value') is-invalid @enderror" placeholder="E.g. 2.50 or 5000">
                @error('commission_value') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="commission_amount">Calculated Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount') }}" class="form-control @error('commission_amount') is-invalid @enderror" placeholder="Calculated automatically or entered manually">
                @error('commission_amount') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
                @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="payment_date">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date') }}" class="form-control @error('payment_date') is-invalid @enderror">
                @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Enter any extra details or transaction references">{{ old('remarks') }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Commission
            </button>
            <a href="{{ route('broker-commissions.index') }}" class="btn-outline">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Pluck lookup variables for offline calculation
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

    // Auto set Customer and Property when booking changes
    bookingSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option && option.value) {
            const propId = option.getAttribute('data-property');
            const custId = option.getAttribute('data-customer');

            if (propId) propertySelect.value = propId;
            if (custId) customerSelect.value = custId;

            // Set base amount from booking amount
            const amt = bookingAmounts[option.value];
            if (amt) {
                baseAmountInput.value = parseFloat(amt).toFixed(2);
            }
        }
        calculateCommission();
    });

    // Auto set base amount when property changes (if no booking is selected)
    propertySelect.addEventListener('change', function() {
        if (!bookingSelect.value) {
            const val = propertyPrices[this.value];
            if (val) {
                baseAmountInput.value = parseFloat(val).toFixed(2);
            }
        }
        calculateCommission();
    });

    // Auto-fill broker default commission percentage whenever broker is selected
    function onBrokerChange() {
        const option = brokerSelect.options[brokerSelect.selectedIndex];
        const infoBox = document.getElementById('brokerCommissionInfoBox');
        const infoVal = document.getElementById('brokerCommissionInfoVal');

        if (option && option.value) {
            const comm = option.getAttribute('data-commission');
            if (comm !== null && comm !== '') {
                const commNum = parseFloat(comm);
                if (infoBox && infoVal) {
                    infoVal.textContent = commNum + '%';
                    infoBox.style.display = 'inline-flex';
                }
                typeSelect.value = 'percentage';
                valueInput.value = commNum.toFixed(2);
            } else {
                if (infoBox) infoBox.style.display = 'none';
            }
        } else {
            if (infoBox) infoBox.style.display = 'none';
        }
        calculateCommission();
    }

    brokerSelect.addEventListener('change', onBrokerChange);
    if (brokerSelect.value) {
        onBrokerChange();
    }

    // Handle calculation
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

    function filterPropertiesByProject(projectId) {
        const propSelect = document.getElementById('property_id');
        if (!propSelect) return;
        const options = propSelect.querySelectorAll('option');
        
        options.forEach(opt => {
            if (!opt.value) {
                opt.style.display = '';
                return;
            }
            const pProjId = opt.getAttribute('data-project-id');
            if (!projectId || pProjId == projectId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });

        const currentOpt = propSelect.options[propSelect.selectedIndex];
        if (currentOpt && currentOpt.style.display === 'none') {
            propSelect.value = '';
        }
    }

    const initialProj = document.getElementById('project_select');
    if (initialProj && initialProj.value) {
        filterPropertiesByProject(initialProj.value);
    }
</script>
@endsection
