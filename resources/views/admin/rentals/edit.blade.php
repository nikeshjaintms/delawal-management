@extends('admin.layouts.app')

@section('title', 'Edit Rental')
@section('page-title', 'Rental Management')

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
    textarea.form-control { resize:vertical; min-height:90px; }
    .text-error { color:#EF4444; font-size:12.5px; margin-top:6px; font-weight:500; }
    .form-hint  { font-size:12px; color:var(--text-secondary); margin-top:5px; }
    .form-actions { display:flex; align-items:center; gap:15px; margin-top:30px; padding-top:20px; border-top:1px solid var(--border-color); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); font-family:var(--font-primary); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:11px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Rental</h2>
        <p>Update rental record — <strong>{{ $rental->tenant_name }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('rentals.update', $rental->id) }}">
        @csrf
        @method('PUT')
        @include('admin.components.firm-select', ['model' => $rental])

        {{-- Property --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-building"></i> Property Details</div>
            <div class="form-group">
                <label class="form-label" for="property_id">Property <span>*</span></label>
                <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                    <option value="">-- Select Property --</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}"
                            {{ old('property_id', $rental->property_id) == $property->id ? 'selected' : '' }}>
                            {{ $property->property_name }}
                            @if($property->property_code) ({{ $property->property_code }}) @endif
                            @if($property->unit_no) — Unit {{ $property->unit_no }} @endif
                            — {{ ucfirst($property->status) }}
                        </option>
                    @endforeach
                </select>
                @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Tenant Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-user"></i> Tenant Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tenant_name">Tenant Name <span>*</span></label>
                    <input type="text" name="tenant_name" id="tenant_name"
                           value="{{ old('tenant_name', $rental->tenant_name) }}"
                           class="form-control" autocomplete="off" placeholder="Enter tenant full name">
                    @error('tenant_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="tenant_mobile">Tenant Mobile <span>*</span></label>
                    <input type="text" name="tenant_mobile" id="tenant_mobile"
                           value="{{ old('tenant_mobile', $rental->tenant_mobile) }}"
                           class="form-control" autocomplete="off" placeholder="Enter tenant contact number">
                    @error('tenant_mobile') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="tenant_email">Tenant Email</label>
                    <input type="email" name="tenant_email" id="tenant_email"
                           value="{{ old('tenant_email', $rental->tenant_email) }}"
                           class="form-control" placeholder="Enter tenant email address">
                    @error('tenant_email') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Rent Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Rent & Deposit</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="rent_amount">Monthly Rent Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="rent_amount" id="rent_amount"
                           value="{{ old('rent_amount', $rental->rent_amount) }}"
                           class="form-control" placeholder="Enter monthly rent">
                    @error('rent_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="security_deposit">Security Deposit (₹)</label>
                    <input type="number" step="0.01" name="security_deposit" id="security_deposit"
                           value="{{ old('security_deposit', $rental->security_deposit) }}"
                           class="form-control" placeholder="Enter security deposit amount">
                    @error('security_deposit') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Rental Period</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="rent_start_date">Rent Start Date <span>*</span></label>
                    <input type="date" name="rent_start_date" id="rent_start_date"
                           value="{{ old('rent_start_date', $rental->rent_start_date ? \Carbon\Carbon::parse($rental->rent_start_date)->format('Y-m-d') : '') }}"
                           class="form-control">
                    @error('rent_start_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rent_end_date">Rent End Date</label>
                    <input type="date" name="rent_end_date" id="rent_end_date"
                           value="{{ old('rent_end_date', $rental->rent_end_date ? \Carbon\Carbon::parse($rental->rent_end_date)->format('Y-m-d') : '') }}"
                           class="form-control">
                    @error('rent_end_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rent_due_date">Rent Due Day of Month</label>
                    <input type="number" name="rent_due_date" id="rent_due_date" min="1" max="31"
                           value="{{ old('rent_due_date', $rental->rent_due_date) }}"
                           class="form-control" placeholder="e.g. 5">
                    <div class="form-hint">Day of month when rent is due (1–31).</div>
                    @error('rent_due_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('payment_status', $rental->payment_status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rental_status">Rental Status <span>*</span></label>
                    <select name="rental_status" id="rental_status" class="form-control @error('rental_status') is-invalid @enderror">
                        @foreach(['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('rental_status', $rental->rental_status) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Active → property Rented. Completed/Cancelled → property Available.</div>
                    @error('rental_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any notes or remarks...">{{ old('remarks', $rental->remarks) }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Rental
            </button>
            <a href="{{ route('rentals.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
