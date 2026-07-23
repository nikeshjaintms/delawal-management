@extends('admin.layouts.app')

@section('title', 'Add Payment')
@section('page-title', 'Payment Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:900px; margin:0 auto; }
    .section-title { font-size:13px; font-weight:700; color:var(--gold); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid var(--border-color); }
    .form-section { margin-bottom:28px; }
    .form-group { margin-bottom:20px; }
    .form-row   { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
    .form-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
    @media(max-width:768px){ .form-row-3{ grid-template-columns:1fr 1fr; } }
    @media(max-width:576px){ .form-row,.form-row-3{ grid-template-columns:1fr; gap:0; } }
    .form-label { display:block; font-size:13.5px; font-weight:600; color:var(--text-primary); margin-bottom:8px; }
    .form-label span { color:#EF4444; }
    .form-control { width:100%; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; font-size:14px; font-family:var(--font-primary); color:var(--text-primary); outline:none; transition:var(--transition); background-color:#FFF; }
    .form-control:focus { border-color:var(--gold); box-shadow:0 0 0 3px var(--gold-light); }
    .form-control-readonly { background-color:#F9FAFB; color:var(--text-secondary); cursor:default; }
    textarea.form-control { resize:vertical; min-height:90px; }
    .text-error { color:#EF4444; font-size:12.5px; margin-top:6px; font-weight:500; }
    .form-hint  { font-size:12px; color:var(--text-secondary); margin-top:5px; }
    .auto-fill-box { background:#FFFBEB; border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:16px 20px; margin-bottom:20px; display:none; }
    .auto-fill-box.visible { display:block; }
    .auto-fill-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    @media(max-width:576px){ .auto-fill-grid{ grid-template-columns:1fr; } }
    .auto-item label { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.7px; display:block; margin-bottom:4px; }
    .auto-item span  { font-size:14.5px; font-weight:600; color:var(--text-primary); }
    .auto-item .amount-val { font-size:16px; font-weight:800; }
    .auto-item .pending-val { color:#B91C1C; }
    .auto-item .paid-val { color:#16803D; }
    .calc-hint { font-size:11.5px; color:var(--gold); margin-top:5px; font-weight:600; }
    .form-actions { display:flex; align-items:center; gap:15px; margin-top:30px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); font-family:var(--font-primary); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
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
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">-- Select Mode --</option>
                        @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()->firm_id); })->where('status', 'active')->orderBy('name')->get() as $pm)
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
