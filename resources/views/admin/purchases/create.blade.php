@extends('admin.layouts.app')
@section('title', 'Add Property Buy')
@section('page-title', 'Property Buy')
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
    max-width: 960px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 28px; }
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
textarea.form-control { resize: vertical; min-height: 85px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }

.price-highlight-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.08) 100%);
    border: 1.5px solid rgba(16, 185, 129, 0.35);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 24px;
}

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

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
        <h2>Add Property Buy</h2>
        <p>Record property purchase, purchase price, legal survey numbers, and seller details.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('purchases.store') }}">
        @csrf

        {{-- Firm Selection --}}
        @include('admin.components.firm-select')

        {{-- 1. Property Details --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-building"></i> 1. Property & Location Details
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_name">Property / Plot Name <span>*</span></label>
                    <input type="text" name="property_name" id="property_name"
                           value="{{ old('property_name') }}"
                           class="form-control @error('property_name') is-invalid @enderror"
                           placeholder="e.g. Delawala Prime Plot #14" required>
                    @error('property_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_type">Property Type</label>
                    <select name="property_type" id="property_type" class="form-control @error('property_type') is-invalid @enderror">
                        <option value="">— Select Type —</option>
                        @foreach(['Plot', 'Flat', 'House', 'Commercial', 'Land', 'Other'] as $pt)
                            <option value="{{ $pt }}" {{ old('property_type', 'Plot') == $pt ? 'selected' : '' }}>{{ $pt }}</option>
                        @endforeach
                    </select>
                    @error('property_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_code">Property Number / Code <span class="opt">(optional)</span></label>
                    <input type="text" name="property_code" id="property_code"
                           value="{{ old('property_code') }}"
                           class="form-control @error('property_code') is-invalid @enderror"
                           placeholder="e.g. PLT-104 / FLAT-302">
                    @error('property_code')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="location">Location <span class="opt">(optional)</span></label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location') }}"
                           class="form-control @error('location') is-invalid @enderror"
                           placeholder="e.g. Vesu, VIP Road, Surat">
                    @error('location')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="address">Address <span class="opt">(optional)</span></label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                          placeholder="Full address or property description...">{{ old('address') }}</textarea>
                @error('address')<div class="text-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="survey_no">Survey No. <span class="opt">(optional)</span></label>
                    <input type="text" name="survey_no" id="survey_no"
                           value="{{ old('survey_no') }}"
                           class="form-control @error('survey_no') is-invalid @enderror"
                           placeholder="e.g. 142/A">
                    @error('survey_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="tp_no">TP No. <span class="opt">(optional)</span></label>
                    <input type="text" name="tp_no" id="tp_no"
                           value="{{ old('tp_no') }}"
                           class="form-control @error('tp_no') is-invalid @enderror"
                           placeholder="e.g. TP-28">
                    @error('tp_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="fp_no">FP No. <span class="opt">(optional)</span></label>
                    <input type="text" name="fp_no" id="fp_no"
                           value="{{ old('fp_no') }}"
                           class="form-control @error('fp_no') is-invalid @enderror"
                           placeholder="e.g. FP-45">
                    @error('fp_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="area">Area <span class="opt">(optional)</span></label>
                    <input type="number" step="0.01" name="area" id="area"
                           value="{{ old('area') }}"
                           class="form-control @error('area') is-invalid @enderror"
                           placeholder="e.g. 1250.00" min="0">
                    @error('area')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="area_unit">Area Unit</label>
                    <select name="area_unit" id="area_unit" class="form-control @error('area_unit') is-invalid @enderror">
                        @foreach(['Sq.Ft', 'Sq.Yd', 'Sq.Mtr', 'Acre'] as $u)
                            <option value="{{ $u }}" {{ old('area_unit', 'Sq.Ft') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                    @error('area_unit')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- 2. Financial & Purchase Price Details --}}
        <div class="form-section">
            <div class="section-title">
                <i class="fa-solid fa-indian-rupee-sign"></i> 2. Purchase Price & Financial Details
            </div>

            <div class="price-highlight-box">
                <div class="form-row">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="purchase_amount" style="color: #34D399 !important; font-size: 14px;">
                            <i class="fa-solid fa-money-bill-wave"></i> Purchase Price / Buy Amount (₹) <span>*</span>
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 14px; top: 11px; color: #34D399; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="purchase_amount" id="purchase_amount"
                                   value="{{ old('purchase_amount') }}"
                                   class="form-control @error('purchase_amount') is-invalid @enderror"
                                   placeholder="e.g. 2500000.00" min="0" required
                                   style="padding-left: 32px; font-weight: 700; font-size: 16px; color: #34D399 !important; border-color: rgba(16, 185, 129, 0.4) !important;">
                        </div>
                        <div class="form-hint" style="color: #A7F3D0;">Enter total price at which this property was purchased.</div>
                        @error('purchase_amount')<div class="text-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" for="purchase_date">
                            <i class="fa-solid fa-calendar-day"></i> Purchase Date <span>*</span>
                        </label>
                        <input type="date" name="purchase_date" id="purchase_date"
                               value="{{ old('purchase_date', date('Y-m-d')) }}"
                               class="form-control @error('purchase_date') is-invalid @enderror" required>
                        @error('purchase_date')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="vendor_id">
                        <i class="fa-solid fa-user-tie"></i> Seller / Vendor / Owner <span class="opt">(optional)</span>
                    </label>
                    <select name="vendor_id" id="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror">
                        <option value="">— Select Seller / Vendor —</option>
                        @if(isset($vendors))
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} {{ $v->phone ? '(' . $v->phone . ')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('vendor_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Payment Mode —</option>
                        @foreach(['Bank Transfer / RTGS / NEFT', 'Cheque', 'Cash', 'UPI', 'Other'] as $mode)
                            <option value="{{ $mode }}" {{ old('payment_mode') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['paid' => 'Paid (Full)', 'partial' => 'Partial Payment', 'unpaid' => 'Unpaid / Due'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('payment_status', 'paid') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('payment_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="reference_no">Cheque / Transaction Ref No. <span class="opt">(optional)</span></label>
                    <input type="text" name="reference_no" id="reference_no"
                           value="{{ old('reference_no') }}"
                           class="form-control @error('reference_no') is-invalid @enderror"
                           placeholder="e.g. CHQ-481920 / UTR-8291038">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Purchase Remarks / Notes <span class="opt">(optional)</span></label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Special terms, payment schedule, registry notes, or deed details...">{{ old('remarks') }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Save Property Buy
            </button>
            <a href="{{ route('purchases.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
