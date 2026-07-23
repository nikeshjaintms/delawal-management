@extends('admin.layouts.app')

@section('title', 'Edit Payment')
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
    .calc-hint  { font-size:11.5px; color:var(--gold); margin-top:5px; font-weight:600; }
    .booking-summary { background:#FFFBEB; border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:14px 18px; margin-bottom:20px; }
    .booking-summary p { font-size:13.5px; color:var(--text-primary); margin:0; font-weight:600; }
    .booking-summary span { color:var(--gold); }
    .form-actions { display:flex; align-items:center; gap:15px; margin-top:30px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); font-family:var(--font-primary); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Payment</h2>
        <p>Update payment record — <strong>#{{ $payment->id }}</strong></p>
    </div>
</div>

<div class="card-box">
    {{-- Current booking summary --}}
    <div class="booking-summary">
        <p>
            Booking <span>#{{ $payment->property_sale_id }}</span> &nbsp;·&nbsp;
            {{ $payment->property->property_name ?? '' }}
            @if($payment->property?->property_code) ({{ $payment->property->property_code }}) @endif
            &nbsp;·&nbsp; {{ $payment->customer->name ?? '' }}
        </p>
    </div>

    <form method="POST" action="{{ route('payments.update', $payment->id) }}">
        @csrf
        @method('PUT')

        {{-- Booking selection --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-contract"></i> Booking Reference</div>
            @include('admin.components.firm-select', ['model' => $payment])
            <div class="form-group">
                <label class="form-label" for="property_sale_id">Property Booking / Sale <span>*</span></label>
                <select name="property_sale_id" id="property_sale_id" class="form-control @error('property_sale_id') is-invalid @enderror">
                    @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}"
                            {{ old('property_sale_id', $payment->property_sale_id) == $booking->id ? 'selected' : '' }}>
                            #{{ $booking->id }} —
                            {{ $booking->property->property_name ?? 'N/A' }}
                            @if($booking->property?->property_code) ({{ $booking->property->property_code }}) @endif
                            — {{ $booking->customer->name ?? '' }}
                        </option>
                    @endforeach
                </select>
                @error('property_sale_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Payment Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Total Sale Amount</label>
                    <input type="text" class="form-control form-control-readonly" readonly
                           value="₹{{ number_format($payment->total_amount, 2) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Already Paid (cumulative)</label>
                    <input type="text" class="form-control form-control-readonly" readonly
                           value="₹{{ number_format($payment->paid_amount, 2) }}">
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_amount">This Payment Amount <span>*</span></label>
                    <input type="number" step="0.01" name="payment_amount" id="payment_amount"
                           value="{{ old('payment_amount', $payment->payment_amount) }}"
                           class="form-control" placeholder="Enter payment amount">
                    @error('payment_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode <span>*</span></label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">-- Select Mode --</option>
                        @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()->firm_id); })->where('status', 'active')->orderBy('name')->get() as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode', $payment->payment_mode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_date">Payment Date <span>*</span></label>
                    <input type="date" name="payment_date" id="payment_date"
                           value="{{ old('payment_date', $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') : '') }}"
                           class="form-control">
                    @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="transaction_ref">Transaction ID / Cheque No</label>
                    <input type="text" name="transaction_ref" id="transaction_ref"
                           value="{{ old('transaction_ref', $payment->transaction_ref) }}"
                           class="form-control" autocomplete="off"
                           placeholder="Optional reference number">
                    @error('transaction_ref') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any remarks...">{{ old('remarks', $payment->remarks) }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Payment
            </button>
            <a href="{{ route('payments.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
