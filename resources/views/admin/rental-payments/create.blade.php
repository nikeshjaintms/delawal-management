@extends('admin.layouts.app')

@section('title', 'Add Rental Payment')
@section('page-title', 'Rental Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:860px; margin:0 auto; }
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

    /* Rental summary bar */
    .rental-bar { background:#FFFBEB; border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:14px 20px; margin-bottom:24px; display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
    .rental-bar-icon { font-size:22px; color:var(--gold); }
    .rental-bar-info p { margin:0; font-size:13.5px; color:var(--text-primary); }
    .rental-bar-info p strong { color:var(--gold); }

    .form-actions { display:flex; align-items:center; gap:15px; margin-top:30px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); font-family:var(--font-primary); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Rental Payment</h2>
        <p>Record a monthly rent payment for this tenant.</p>
    </div>
</div>

<div style="max-width:860px;margin:0 auto;">
    {{-- Rental summary bar --}}
    <div class="rental-bar">
        <div class="rental-bar-icon"><i class="fa-solid fa-key"></i></div>
        <div class="rental-bar-info">
            <p>
                <strong>{{ $rental->tenant_name }}</strong>
                &nbsp;·&nbsp; {{ $rental->tenant_mobile }}
                &nbsp;·&nbsp; {{ $rental->property->property_name ?? '' }}
                @if($rental->property?->unit_no) — Unit {{ $rental->property->unit_no }} @endif
                &nbsp;·&nbsp; Monthly Rent: <strong>₹{{ number_format($rental->rent_amount, 0) }}</strong>
            </p>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('rental-payments.store', $rental->id) }}">
        @csrf
        @include('admin.components.firm-select', ['model' => $rental])

        {{-- Period --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Payment Period</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_month">Payment Month <span>*</span></label>
                    <select name="payment_month" id="payment_month" class="form-control @error('payment_month') is-invalid @enderror">
                        <option value="">-- Select Month --</option>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ old('payment_month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('payment_month') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_year">Payment Year <span>*</span></label>
                    <select name="payment_year" id="payment_year" class="form-control @error('payment_year') is-invalid @enderror">
                        <option value="">-- Select Year --</option>
                        @for($y = date('Y') + 1; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('payment_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('payment_year') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Payment Amount</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="rent_amount">Rent Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="rent_amount" id="rent_amount"
                           value="{{ old('rent_amount', $rental- class="@error('rent_amount') is-invalid @enderror">rent_amount) }}"
                           class="form-control" placeholder="Enter rent amount"
                           oninput="calcPending()">
                    @error('rent_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="paid_amount">Paid Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                           value="{{ old('paid_amount', 0) }}"
                           class="form-control @error('paid_amount') is-invalid @enderror" placeholder="Enter amount paid"
                           oninput="calcPending()">
                    @error('paid_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pending_display">Pending Amount (₹)</label>
                    <input type="text" id="pending_display" class="form-control form-control-readonly"
                           readonly placeholder="Auto-calculated">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Rent − Paid</div>
                </div>
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-wallet"></i> Payment Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">-- Select Mode --</option>
                        @foreach(['Cash','Bank Transfer','UPI','Cheque','Other'] as $mode)
                            <option value="{{ $mode }}" {{ old('payment_mode') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_date">Payment Date</label>
                    <input type="date" name="payment_date" id="payment_date"
                           value="{{ old('payment_date', date('Y-m-d')) }}" class="form-control @error('payment_date') is-invalid @enderror">
                    @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any notes about this payment...">{{ old('remarks') }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Payment
            </button>
            <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
function calcPending() {
    const rent   = parseFloat(document.getElementById('rent_amount').value)   || 0;
    const paid   = parseFloat(document.getElementById('paid_amount').value)   || 0;
    const pending = Math.max(0, rent - paid);
    document.getElementById('pending_display').value =
        '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
window.addEventListener('DOMContentLoaded', calcPending);
</script>
@endsection
