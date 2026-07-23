@extends('admin.layouts.app')
@section('title', 'Edit Booking')
@section('page-title', 'Booking Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:900px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    @media(max-width:640px){.form-row{grid-template-columns:1fr;gap:0;}}
    .form-group{margin-bottom:20px;}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:7px;}
    .form-label span{color:#EF4444;}
    .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background:#FFF;}
    .form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow);}
    .text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500;}
    .form-actions{display:flex;gap:12px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Booking</h2>
        <p>Update booking details and status.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('bookings.update', $booking->id) }}">
        @csrf @method('PUT')
        @include('admin.components.firm-select', ['model' => $booking])
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Property <span>*</span></label>
                <select name="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                    <option value="">Select Property</option>
                    @foreach($properties as $p)
                        <option value="{{ $p->id }}" {{ old('property_id',$booking->property_id)==$p->id?'selected':'' }}>{{ $p->property_name }}</option>
                    @endforeach
                </select>
                @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Customer <span>*</span></label>
                <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id',$booking->customer_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Broker</label>
                <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                    <option value="">No Broker</option>
                    @foreach($brokers as $b)
                        <option value="{{ $b->id }}" {{ old('broker_id', $booking->broker_id) == $b->id ? 'selected' : '' }} data-commission="{{ $b->commission_percentage }}">
                            {{ $b->name }}
                        </option>
                    @endforeach
                </select>
                @error('broker_id')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Booking Date</label>
                <input type="date" name="booking_date" value="{{ old('booking_date', is_string($booking->booking_date) ? $booking->booking_date : ($booking->booking_date ? $booking->booking_date->format('Y-m-d') : '')) }}" class="form-control @error('booking_date') is-invalid @enderror">
                @error('booking_date')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Booking Amount (₹)</label>
                <input type="number" step="0.01" name="booking_amount" value="{{ old('booking_amount', $booking->booking_amount) }}" class="form-control @error('booking_amount') is-invalid @enderror">
                @error('booking_amount')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Agreement Date</label>
                <input type="date" name="agreement_date" value="{{ old('agreement_date', is_string($booking->agreement_date) ? $booking->agreement_date : ($booking->agreement_date ? $booking->agreement_date->format('Y-m-d') : '')) }}" class="form-control @error('agreement_date') is-invalid @enderror">
                @error('agreement_date')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Booking Status <span>*</span></label>
                <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                    @foreach(['pending','confirmed','cancelled'] as $s)
                        <option value="{{ $s }}" {{ old('status',$booking->status)==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('status')<div class="text-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Payment Status <span>*</span></label>
                <select name="payment_status" class="form-control @error('payment_status') is-invalid @enderror" required>
                    @foreach(['unpaid','partial','paid'] as $ps)
                        <option value="{{ $ps }}" {{ old('payment_status',$booking->payment_status)==$ps?'selected':'' }}>{{ ucfirst($ps) }}</option>
                    @endforeach
                </select>
                @error('payment_status')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks',$booking->remarks) }}</textarea>
        </div>

        <!-- Broker Commission Details (shows only when a broker is selected) -->
        <div id="commission_section" style="display: none; margin-top: 24px; padding-top: 20px; border-top: 1px dashed var(--border-color);">
            <h4 style="font-size: 15px; font-weight: 700; color: var(--gold); margin-bottom: 15px;">
                <i class="fa-solid fa-percent"></i> Broker Commission Details
            </h4>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Commission Type</label>
                    <select name="commission_type" id="commission_type" class="form-control">
                        <option value="percentage" {{ old('commission_type', $commission->commission_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('commission_type', $commission->commission_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Commission Value</label>
                    <input type="number" step="0.01" min="0" name="commission_value" id="commission_value" value="{{ old('commission_value', $commission->commission_value ?? '') }}" class="form-control" placeholder="E.g. 2.0 or 5000">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Calculated Commission Amount (₹)</label>
                    <input type="number" step="0.01" min="0" name="commission_amount" id="commission_amount" value="{{ old('commission_amount', $commission->commission_amount ?? '') }}" class="form-control" placeholder="Auto-calculated">
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Update Booking</button>
            <a href="{{ route('bookings.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const brokerSelect = document.getElementById('broker_id');
        const commissionSection = document.getElementById('commission_section');
        const commissionType = document.getElementById('commission_type');
        const commissionValue = document.getElementById('commission_value');
        const commissionAmount = document.getElementById('commission_amount');
        const bookingAmountInput = document.querySelector('input[name="booking_amount"]');

        function toggleCommissionSection() {
            if (brokerSelect.value) {
                commissionSection.style.display = 'block';
                // If value is empty, auto-fill default commission
                const option = brokerSelect.options[brokerSelect.selectedIndex];
                const defaultComm = option.getAttribute('data-commission');
                if (!commissionValue.value && defaultComm) {
                    commissionValue.value = parseFloat(defaultComm).toFixed(2);
                }
            } else {
                commissionSection.style.display = 'none';
                commissionValue.value = '';
                commissionAmount.value = '';
            }
            calculateCommission();
        }

        function calculateCommission() {
            const type = commissionType.value;
            const val = parseFloat(commissionValue.value) || 0;
            const bookingAmt = parseFloat(bookingAmountInput.value) || 0;

            let calculated = 0;
            if (type === 'percentage') {
                calculated = (bookingAmt * val) / 100;
            } else {
                calculated = val;
            }

            commissionAmount.value = calculated > 0 ? calculated.toFixed(2) : '';
        }

        brokerSelect.addEventListener('change', toggleCommissionSection);
        commissionType.addEventListener('change', calculateCommission);
        commissionValue.addEventListener('input', calculateCommission);
        bookingAmountInput.addEventListener('input', calculateCommission);

        // Run on load to restore state
        toggleCommissionSection();
    });
</script>
@endsection
