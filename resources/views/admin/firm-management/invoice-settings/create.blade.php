@extends('admin.layouts.app')
@section('title','Add Invoice Settings')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 28px 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:768px) { .form-grid, .form-grid-2 { grid-template-columns: 1fr; } }

.form-group { margin-bottom: 0; }
.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171; }
.form-label small { font-weight: 400; color: #94A3B8; font-size: 11px; margin-left: 4px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: border-color .18s, box-shadow .18s;
}
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 600; }
.form-hint { font-size: 11.5px; color: #94A3B8; margin-top: 4px; }
.preview-box {
    background: rgba(16, 22, 34, 0.70); border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 14px; padding: 18px 22px; margin-top: 20px;
}
.preview-box h4 { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; color: #60A5FA; margin-bottom: 12px; }
.preview-pills { display: flex; flex-wrap: wrap; gap: 8px; }
.preview-pill { background: rgba(59, 130, 246, 0.18); color: #60A5FA; font-size: 12px; font-weight: 700; border-radius: 8px; padding: 6px 14px; font-family: monospace; border: 1px solid rgba(59, 130, 246, 0.30); }
.form-action-buttons { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.10); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Invoice Settings</h2>
        <p>Configure invoice number series for all modules.</p>
    </div>
    <a href="{{ route('invoice-settings.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('invoice-settings.store') }}">
@csrf

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-gear"></i> General Settings</div>
    <div class="form-grid-2">
        <div class="form-group">
            <label class="form-label" for="firm_ids">Firms <span>*</span></label>
            <select name="firm_ids[]" id="firm_ids" class="form-control select2-multi @error('firm_ids') is-invalid @enderror" multiple required data-placeholder="Search and select firm(s)..." style="background: #101622 !important; color: #FFFFFF !important;">
                @foreach($firms as $firm)
                    <option value="{{ $firm->id }}" {{ in_array($firm->id, old('firm_ids', [Auth::user()->firm_id])) ? 'selected' : '' }} style="background: #101622; color: #FFFFFF;">
                        {{ $firm->firm_name }}
                    </option>
                @endforeach
            </select>
            @error('firm_ids')<div class="text-error">{{ $message }}</div>@enderror
            @error('firm_ids.*')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="financial_year_id">Financial Year</label>
            <select name="financial_year_id" id="financial_year_id" class="form-control @error('financial_year_id') is-invalid @enderror" style="background: #101622 !important; color: #FFFFFF !important;">
                <option value="" style="background: #101622; color: #FFFFFF;">All Financial Years</option>
                @foreach($financialYears as $fy)
                    <option value="{{ $fy->id }}" {{ old('financial_year_id') == $fy->id ? 'selected' : '' }} style="background: #101622; color: #FFFFFF;">
                        {{ $fy->year_name }} {{ $fy->is_active ? '(Active)' : '' }}
                    </option>
                @endforeach
            </select>
            @error('financial_year_id')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> Module Number Series Prefixes</div>
    <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Booking Prefix <small>(e.g. BKG-)</small></label>
            <input type="text" name="booking_prefix" value="{{ old('booking_prefix', 'BKG-') }}" class="form-control @error('booking_prefix') is-invalid @enderror" maxlength="20">
            @error('booking_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Sale Prefix <small>(e.g. INV-)</small></label>
            <input type="text" name="sale_prefix" value="{{ old('sale_prefix', 'INV-') }}" class="form-control @error('sale_prefix') is-invalid @enderror" maxlength="20">
            @error('sale_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Receipt Prefix <small>(e.g. RCP-)</small></label>
            <input type="text" name="receipt_prefix" value="{{ old('receipt_prefix', 'RCP-') }}" class="form-control @error('receipt_prefix') is-invalid @enderror" maxlength="20">
            @error('receipt_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Payment Prefix <small>(e.g. PMT-)</small></label>
            <input type="text" name="payment_prefix" value="{{ old('payment_prefix', 'PMT-') }}" class="form-control @error('payment_prefix') is-invalid @enderror" maxlength="20">
            @error('payment_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Rental Prefix <small>(e.g. RNT-)</small></label>
            <input type="text" name="rental_prefix" value="{{ old('rental_prefix', 'RNT-') }}" class="form-control @error('rental_prefix') is-invalid @enderror" maxlength="20">
            @error('rental_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Purchase Order Prefix <small>(e.g. PO-)</small></label>
            <input type="text" name="po_prefix" value="{{ old('po_prefix', 'PO-') }}" class="form-control @error('po_prefix') is-invalid @enderror" maxlength="20">
            @error('po_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Purchase Prefix <small>(e.g. PUR-)</small></label>
            <input type="text" name="purchase_prefix" value="{{ old('purchase_prefix', 'PUR-') }}" class="form-control @error('purchase_prefix') is-invalid @enderror" maxlength="20">
            @error('purchase_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Credit Note Prefix <small>(e.g. CN-)</small></label>
            <input type="text" name="credit_note_prefix" value="{{ old('credit_note_prefix', 'CN-') }}" class="form-control @error('credit_note_prefix') is-invalid @enderror" maxlength="20">
            @error('credit_note_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Debit Note Prefix <small>(e.g. DN-)</small></label>
            <input type="text" name="debit_note_prefix" value="{{ old('debit_note_prefix', 'DN-') }}" class="form-control @error('debit_note_prefix') is-invalid @enderror" maxlength="20">
            @error('debit_note_prefix')<div class="text-error">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Save Invoice Settings</button>
    <a href="{{ route('invoice-settings.index') }}" class="btn-secondary-custom"><i class="fa fa-times"></i> Cancel</a>
</div>
</form>
@endsection
