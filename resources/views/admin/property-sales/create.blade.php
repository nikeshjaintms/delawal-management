@extends('admin.layouts.app')

@section('title', 'Add Property Sale')
@section('page-title', 'Property Sales')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:30px; box-shadow:var(--soft-shadow); max-width:900px; margin:0 auto; }
    .section-title { font-size:13px; font-weight:700; color:var(--gold); text-transform:uppercase; letter-spacing:1px; margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid var(--border-color); }
    .form-section { margin-bottom:28px; }
    .form-group { margin-bottom:20px; }
    .form-row  { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
    .form-row-3{ display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; }
    @media(max-width:768px){ .form-row-3{ grid-template-columns:1fr 1fr; } }
    @media(max-width:576px){ .form-row,.form-row-3{ grid-template-columns:1fr; gap:0; } }
    .form-label { display:block; font-size:13.5px; font-weight:600; color:var(--text-primary); margin-bottom:8px; }
    .form-label span { color:#EF4444; }
    .form-control { width:100%; padding:10px 14px; border:1px solid var(--border-color); border-radius:8px; font-size:14px; font-family:var(--font-primary); color:var(--text-primary); outline:none; transition:var(--transition); background-color:#FFFFFF; }
    .form-control:focus { border-color:var(--gold); box-shadow:0 0 0 3px var(--gold-light); }
    textarea.form-control { resize:vertical; min-height:90px; }
    .text-error { color:#EF4444; font-size:12.5px; margin-top:6px; font-weight:500; }
    .form-hint  { font-size:12px; color:var(--text-secondary); margin-top:5px; }
    .form-actions { display:flex; align-items:center; gap:15px; margin-top:30px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); font-family:var(--font-primary); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); box-shadow:0 6px 14px rgba(212,175,55,0.3); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
    .calc-hint { font-size:11.5px; color:var(--gold); margin-top:5px; font-weight:600; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Property Sale</h2>
        <p>Record a new property booking or sale transaction.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('property-sales.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Parties --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-handshake"></i> Sale Parties</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_id">Property <span>*</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">-- Select Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->property_name }}
                                @if($property->property_code) ({{ $property->property_code }}) @endif
                                — {{ ucfirst($property->status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="customer_id">Customer <span>*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} — {{ $customer->mobile }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="broker_id">Broker</label>
                    <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                        <option value="">-- Select Broker (Optional) --</option>
                        @foreach($brokers as $broker)
                            <option value="{{ $broker->id }}" {{ old('broker_id') == $broker->id ? 'selected' : '' }}>
                                {{ $broker->name }} — {{ $broker->mobile }}
                            </option>
                        @endforeach
                    </select>
                    @error('broker_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_date">Sale Date</label>
                    <input type="date" name="sale_date" id="sale_date"
                           value="{{ old('sale_date') }}" class="form-control @error('sale_date') is-invalid @enderror">
                    @error('sale_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="sale_amount">Sale Amount (₹)</label>
                    <input type="number" step="0.01" name="sale_amount" id="sale_amount"
                           value="{{ old('sale_amount') }}" class="form-control @error('sale_amount') is-invalid @enderror"
                           placeholder="Enter total sale amount" oninput="calcRemaining()">
                    @error('sale_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="booking_amount">Booking Amount (₹)</label>
                    <input type="number" step="0.01" name="booking_amount" id="booking_amount"
                           value="{{ old('booking_amount') }}" class="form-control @error('booking_amount') is-invalid @enderror"
                           placeholder="Enter advance / booking amount" oninput="calcRemaining()">
                    @error('booking_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="remaining_amount">Remaining Amount (₹)</label>
                    <input type="number" step="0.01" name="remaining_amount" id="remaining_amount"
                           value="{{ old('remaining_amount') }}" class="form-control @error('remaining_amount') is-invalid @enderror"
                           placeholder="Auto-calculated">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> Auto = Sale − Booking</div>
                    @error('remaining_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status & Documents</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_status', 'pending') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_status">Sale Status <span>*</span></label>
                    <select name="sale_status" id="sale_status" class="form-control @error('sale_status') is-invalid @enderror">
                        @foreach(['booked' => 'Booked', 'sold' => 'Sold', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('sale_status', 'booked') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Changing this will also update the property status.</div>
                    @error('sale_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="agreement_file">Agreement / Document</label>
                    <input type="file" name="agreement_file" id="agreement_file" class="form-control @error('agreement_file') is-invalid @enderror">
                    <div class="form-hint">Upload sale agreement (PDF, DOC, JPG, etc.).</div>
                    @error('agreement_file') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="note">Note</label>
                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror"
                          placeholder="Add any additional notes about this sale or booking...">{{ old('note') }}</textarea>
                @error('note') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Property Sale
            </button>
            <a href="{{ route('property-sales.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
function calcRemaining() {
    const sale    = parseFloat(document.getElementById('sale_amount').value)    || 0;
    const booking = parseFloat(document.getElementById('booking_amount').value) || 0;
    const remaining = sale - booking;
    document.getElementById('remaining_amount').value = remaining >= 0 ? remaining.toFixed(2) : '';
}
</script>
@endsection
