@extends('admin.layouts.app')

@section('title', 'Edit Broker Commission')
@section('page-title', 'Edit Commission')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 900px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .form-label span {
        color: #EF4444;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }

    .form-control:focus {
        border-color: #fc6900ff;
        box-shadow: 0 0 0 3px rgba(252, 105, 0, 0.15);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    .text-error {
        color: #EF4444;
        font-size: 12.5px;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-hint {
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 5px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn-gold {
        background-color: #fc6900ff;
        color: #FFFFFF;
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(252, 105, 0, 0.2);
    }

    .btn-gold:hover {
        background-color: #e05c00;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(252, 105, 0, 0.3);
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
        border-color: #D1D5DB;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Broker Commission</h2>
        <p>Update commission payout details for the broker.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('broker-commissions.update', $commission->id) }}" id="commissionForm">
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
                        <option value="{{ $p->id }}" {{ old('property_id', $commission->property_id) == $p->id ? 'selected' : '' }}>{{ $p->property_name }} (₹{{ number_format($p->price, 0) }})</option>
                    @endforeach
                </select>
                @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
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
                <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value', $commission- class="@error('commission_value') is-invalid @enderror">commission_value) }}" class="form-control" placeholder="E.g. 2.50 or 5000">
                @error('commission_value') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="commission_amount">Calculated Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount', $commission- class="@error('commission_amount') is-invalid @enderror">commission_amount) }}" class="form-control" placeholder="Calculated automatically or entered manually">
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
                <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $commission- class="@error('payment_date') is-invalid @enderror">payment_date ? \Carbon\Carbon::parse($commission->payment_date)->format('Y-m-d') : '') }}" class="form-control">
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
</script>
@endsection
