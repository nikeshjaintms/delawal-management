@extends('admin.layouts.app')

@section('title', 'Edit Payment')
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
.calc-hint { font-size: 11.5px; color: #60A5FA !important; margin-top: 5px; font-weight: 600; }

/* Booking summary box - Dark Glass */
.booking-summary {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 16px !important; padding: 18px 22px !important; margin-bottom: 24px !important;
}
.booking-summary p { font-size: 14.5px; color: #FFFFFF !important; margin: 0; font-weight: 600; }
.booking-summary span { color: #60A5FA !important; font-weight: 800; }

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
