@extends('admin.layouts.app')

@section('title', 'Add Payment')
@section('page-title', 'Payment Management')

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
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

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
.form-control-readonly { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; border: 1px solid rgba(255, 255, 255, 0.10) !important; cursor: default; }
textarea.form-control { resize: vertical; min-height: 90px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

.auto-fill-box {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 16px !important; padding: 20px 22px !important; margin-bottom: 24px !important;
    display: none;
}
.auto-fill-box.visible { display: block; }
.auto-fill-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width:576px){ .auto-fill-grid { grid-template-columns: 1fr; } }
.auto-item label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 4px; }
.auto-item span { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; }
.auto-item .amount-val { font-size: 16px; font-weight: 800; color: #FFFFFF !important; }
.auto-item .pending-val { color: #F87171 !important; }
.auto-item .paid-val { color: #34D399 !important; }
.calc-hint { font-size: 11.5px; color: #60A5FA !important; margin-top: 5px; font-weight: 600; }

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
        <h2>Add Payment</h2>
        <p>Record a new payment against a property booking or sale.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
        @csrf

        {{-- Select Booking --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-contract"></i> Select Booking</div>
            @include('admin.components.firm-select')
            <div class="form-group">
                <label class="form-label" for="property_sale_id">Property Booking / Sale <span>*</span></label>
                <select name="property_sale_id" id="property_sale_id" class="form-control @error('property_sale_id') is-invalid @enderror" onchange="loadBookingInfo(this.value)">
                    <option value="">-- Select Booking --</option>
                    @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}" {{ old('property_sale_id') == $booking->id ? 'selected' : '' }}>
                            #{{ $booking->id }} —
                            {{ $booking->property->property_name ?? 'N/A' }}
                            @if($booking->property?->property_code) ({{ $booking->property->property_code }}) @endif
                            — {{ $booking->customer->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('property_sale_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            {{-- Auto-fill info card --}}
            <div class="auto-fill-box {{ old('property_sale_id') ? 'visible' : '' }}" id="bookingInfoBox">
                <div class="auto-fill-grid">
                    <div class="auto-item">
                        <label>Customer Name</label>
                        <span id="info_customer_name">-</span>
                    </div>
                    <div class="auto-item">
                        <label>Mobile</label>
                        <span id="info_customer_mobile">-</span>
                    </div>
                    <div class="auto-item">
                        <label>Property Name</label>
                        <span id="info_property_name">-</span>
                    </div>
                    <div class="auto-item">
                        <label>Unit / Plot No</label>
                        <span id="info_unit_no">-</span>
                    </div>
                    <div class="auto-item">
                        <label>Total Sale Amount</label>
                        <span id="info_total" class="amount-val">₹0</span>
                    </div>
                    <div class="auto-item">
                        <label>Already Paid</label>
                        <span id="info_paid" class="amount-val paid-val">₹0</span>
                    </div>
                    <div class="auto-item">
                        <label>Remaining / Pending</label>
                        <span id="info_pending" class="amount-val pending-val">₹0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Payment Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="total_amount_display">Total Sale Amount</label>
                    <input type="text" id="total_amount_display" class="form-control form-control-readonly"
                           readonly placeholder="Auto-filled from booking">
                </div>
                <div class="form-group">
                    <label class="form-label" for="already_paid_display">Already Paid Amount</label>
                    <input type="text" id="already_paid_display" class="form-control form-control-readonly"
                           readonly placeholder="Auto-filled from booking">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_amount">New Payment Amount <span>*</span></label>
                    <input type="number" step="0.01" name="payment_amount" id="payment_amount"
                           value="{{ old('payment_amount') }}"
                           class="form-control @error('payment_amount') is-invalid @enderror" placeholder="Enter amount being paid now"
                           oninput="calcPending()">
                    @error('payment_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="pending_display">Pending After This Payment</label>
                    <input type="text" id="pending_display" class="form-control form-control-readonly"
                           readonly placeholder="Auto-calculated">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Remaining − New Payment</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode <span>*</span></label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror" required>
                        <option value="">-- Select Mode --</option>
                        @php
                            $firmId = Auth::user()?->firm_id ?? session('firm_id');
                            $pModes = \App\Models\PaymentMode::where('status', 'active')
                                ->when($firmId, function($q) use ($firmId) {
                                    $q->where(function($sub) use ($firmId) {
                                        $sub->whereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                                            ->orWhereDoesntHave('firms');
                                    });
                                })
                                ->orderBy('name')
                                ->get();
                            if ($pModes->isEmpty()) {
                                $pModes = \App\Models\PaymentMode::where('status', 'active')->orderBy('name')->get();
                            }
                        @endphp
                        @foreach($pModes as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode') == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_date">Payment Date <span>*</span></label>
                    <input type="date" name="payment_date" id="payment_date"
                           value="{{ old('payment_date', date('Y-m-d')) }}" class="form-control @error('payment_date') is-invalid @enderror">
                    @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="transaction_ref">Transaction ID / Cheque No</label>
                    <input type="text" name="transaction_ref" id="transaction_ref"
                           value="{{ old('transaction_ref') }}"
                           class="form-control @error('transaction_ref') is-invalid @enderror" autocomplete="off"
                           placeholder="Optional — enter transaction or cheque reference">
                    @error('transaction_ref') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any remarks or notes about this payment...">{{ old('remarks') }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Payment
            </button>
            <a href="{{ route('payments.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
let bookingData = null;

function formatINR(num) {
    return '₹' + parseFloat(num || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function loadBookingInfo(id) {
    if (!id) {
        document.getElementById('bookingInfoBox').classList.remove('visible');
        bookingData = null;
        clearDisplays();
        return;
    }

    fetch('{{ route("payments.booking-info", ":id") }}'.replace(':id', id))
        .then(r => r.json())
        .then(data => {
            bookingData = data;
            document.getElementById('info_customer_name').textContent   = data.customer_name || '-';
            document.getElementById('info_customer_mobile').textContent  = data.customer_mobile || '-';
            document.getElementById('info_property_name').textContent   = data.property_name || '-';
            document.getElementById('info_unit_no').textContent         = data.unit_no || '-';
            document.getElementById('info_total').textContent           = formatINR(data.total_amount);
            document.getElementById('info_paid').textContent            = formatINR(data.paid_amount);
            document.getElementById('info_pending').textContent         = formatINR(data.pending_amount);
            document.getElementById('total_amount_display').value       = formatINR(data.total_amount);
            document.getElementById('already_paid_display').value       = formatINR(data.paid_amount);
            document.getElementById('bookingInfoBox').classList.add('visible');
            calcPending();
        })
        .catch(() => {});
}

function clearDisplays() {
    document.getElementById('total_amount_display').value  = '';
    document.getElementById('already_paid_display').value  = '';
    document.getElementById('pending_display').value       = '';
}

function calcPending() {
    if (!bookingData) return;
    const newPayment = parseFloat(document.getElementById('payment_amount').value) || 0;
    const pending    = Math.max(0, parseFloat(bookingData.pending_amount) - newPayment);
    document.getElementById('pending_display').value = formatINR(pending);
}

// Auto-load if old value exists (on validation fail return)
window.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('property_sale_id');
    if (sel && sel.value) {
        loadBookingInfo(sel.value);
    }
});
</script>
@endsection
