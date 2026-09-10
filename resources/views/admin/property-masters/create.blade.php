@extends('admin.layouts.app')

@section('title', 'Add Property Master')
@section('page-title', 'Property Management')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .form-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
        max-width: 900px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .form-control {
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: var(--transition);
        background: #FFF;
    }

    .form-control:focus {
        border-color: var(--gold);
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-gold:hover {
        background-color: #B58D1B;
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
    }

    .invalid-feedback {
        color: #EF4444;
        font-size: 12px;
        margin-top: 2px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Property Master</h2>
        <p>Create a top-level Property entry under your firm.</p>
    </div>
    <a href="{{ route('property-masters.index') }}" class="btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Properties
    </a>
</div>

<div class="form-card">
    <form method="POST" action="{{ route('property-masters.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            @if(Auth::user() && Auth::user()->isAdmin())
                <div class="form-group">
                    <label class="form-label">Firm <span style="color:#EF4444;">*</span></label>
                    <select name="firm_id" class="form-control" required>
                        <option value="">Select Firm</option>
                        @foreach($firms as $firm)
                            <option value="{{ $firm->id }}" {{ old('firm_id') == $firm->id ? 'selected' : '' }}>
                                {{ $firm->firm_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('firm_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Property Name <span style="color:#EF4444;">*</span></label>
                <input type="text" name="property_name" value="{{ old('property_name') }}" class="form-control" placeholder="e.g. Delawala Heights" required>
                @error('property_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Property Code</label>
                <input type="text" name="property_code" value="{{ old('property_code') }}" class="form-control" placeholder="Auto-generated if left blank">
                @error('property_code') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:#EF4444;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <span><i class="fa-solid fa-indian-rupee-sign"></i> Purchase Price &amp; Payment Breakdown</span>
                    <div style="display: flex; gap: 6px; font-size: 11.5px; text-transform: none;">
                        <button type="button" class="btn-outline" onclick="setQuickPayment('full')" style="padding: 3px 8px; font-size: 11.5px; border-radius: 6px; color: #34D399; border-color: rgba(16, 185, 129, 0.4);">
                            <i class="fa-solid fa-check-double"></i> Full Paid (100%)
                        </button>
                        <button type="button" class="btn-outline" onclick="setQuickPayment('half')" style="padding: 3px 8px; font-size: 11.5px; border-radius: 6px; color: #FBBF24; border-color: rgba(245, 158, 11, 0.4);">
                            <i class="fa-solid fa-percent"></i> 50% Advance
                        </button>
                        <button type="button" class="btn-outline" onclick="setQuickPayment('unpaid')" style="padding: 3px 8px; font-size: 11.5px; border-radius: 6px; color: #F87171; border-color: rgba(239, 68, 68, 0.4);">
                            <i class="fa-solid fa-clock"></i> Unpaid (0%)
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group full-width" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.10) 0%, rgba(5, 150, 105, 0.05) 100%); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 12px; padding: 18px; margin-bottom: 10px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                    <!-- 1. Total Purchase Price -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #34D399; font-weight: 700;">
                            <i class="fa-solid fa-money-bill-wave"></i> Total Purchase Price (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 10px; color: #34D399; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" class="form-control" placeholder="e.g. 5000000.00" style="padding-left: 30px; font-weight: 700; font-size: 15px; color: #34D399; border-color: rgba(16, 185, 129, 0.4);" oninput="recalculatePayment()">
                        </div>
                        <small style="color: #A7F3D0; font-size: 11.5px; margin-top: 4px; display: block;">Total purchase amount</small>
                        @error('purchase_price') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- 2. Paid Amount -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #60A5FA; font-weight: 700;">
                            <i class="fa-solid fa-circle-check"></i> Paid Amount (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 10px; color: #60A5FA; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="{{ old('paid_amount') }}" class="form-control" placeholder="e.g. 2000000.00" style="padding-left: 30px; font-weight: 700; font-size: 15px; color: #60A5FA; border-color: rgba(59, 130, 246, 0.4);" oninput="recalculatePayment()">
                        </div>
                        <small style="color: #BFDBFE; font-size: 11.5px; margin-top: 4px; display: block;">Amount paid to vendor/seller so far</small>
                        @error('paid_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Due Balance -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label" style="color: #FBBF24; font-weight: 700;">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Due Balance (₹)
                        </label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 12px; top: 10px; color: #FBBF24; font-weight: 700; font-size: 15px;">₹</span>
                            <input type="number" step="0.01" name="due_amount" id="due_amount" value="{{ old('due_amount', 0) }}" class="form-control" placeholder="0.00" style="padding-left: 30px; font-weight: 800; font-size: 15px; color: #FBBF24; background: rgba(0,0,0,0.15); border-color: rgba(245, 158, 11, 0.4);" readonly>
                        </div>
                        <small style="color: #FDE68A; font-size: 11.5px; margin-top: 4px; display: block;">Auto-calculated (Total - Paid)</small>
                        @error('due_amount') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 14px; padding-top: 14px; border-top: 1px dashed rgba(16, 185, 129, 0.25);">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-calendar-day"></i> Purchase Date</label>
                        <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" class="form-control">
                        @error('purchase_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label"><i class="fa-solid fa-wallet"></i> Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-control" style="font-weight: 700;">
                            <option value="paid" {{ old('payment_status', 'paid') === 'paid' ? 'selected' : '' }}>Paid (100% Full)</option>
                            <option value="partial" {{ old('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="unpaid" {{ old('payment_status') === 'unpaid' ? 'selected' : '' }}>Unpaid (0%)</option>
                        </select>
                        @error('payment_status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Seller / Vendor / Owner</label>
                <select name="vendor_id" id="vendor_id" class="form-control">
                    <option value="">— Select Seller / Vendor —</option>
                    @if(isset($vendors))
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} {{ ($v->mobile ?: $v->phone) ? '(' . ($v->mobile ?: $v->phone) . ')' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('vendor_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode" class="form-control">
                    <option value="">— Select Payment Mode —</option>
                    @foreach(['Bank Transfer / RTGS / NEFT', 'Cheque', 'Cash', 'UPI', 'Other'] as $mode)
                        <option value="{{ $mode }}" {{ old('payment_mode') == $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>
                @error('payment_mode') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Total Land Area <span style="color:#94A3B8; font-weight: normal;">(optional)</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" step="0.01" name="total_area" id="total_area" value="{{ old('total_area') }}" class="form-control" placeholder="e.g. 150000" oninput="calculateLandTotalPrice()">
                    <select name="area_unit" id="area_unit" class="form-control" style="width: 120px; flex-shrink: 0;" onchange="calculateLandTotalPrice()">
                        @foreach(['Sq.Ft', 'Sq.Yd', 'Sq.Mtr', 'Acre', 'Vigha', 'Guntha'] as $u)
                            <option value="{{ $u }}" {{ old('area_unit', 'Sq.Ft') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                @error('total_area') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Purchase Rate per Unit <span style="color:#94A3B8; font-weight: normal;">(optional)</span></label>
                <input type="number" step="0.01" name="purchase_rate" id="purchase_rate" value="{{ old('purchase_rate') }}" class="form-control" placeholder="e.g. 1500.00" oninput="calculateLandTotalPrice()">
                @error('purchase_rate') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <!-- Total Land Price Live Calculation Box -->
            <div class="form-group full-width" id="calculatedPriceCard" style="display: none; background: rgba(59, 130, 246, 0.10); border: 1.5px solid rgba(59, 130, 246, 0.35); border-radius: 12px; padding: 14px 18px; margin-top: -5px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                        <div>
                            <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: #93C5FD; font-weight: 700; display: block;">Total Calculated Land Price</span>
                            <div style="display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;">
                                <span id="calculatedPriceText" style="font-size: 20px; font-weight: 800; color: #FFFFFF;">₹ 0.00</span>
                                <span id="calculatedPriceFormula" style="font-size: 12.5px; color: #94A3B8; font-weight: 600;"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-outline" onclick="applyCalculatedToPurchasePrice()" style="padding: 7px 16px; font-size: 13px; border-radius: 8px; color: #93C5FD; border-color: rgba(59, 130, 246, 0.50); background: rgba(59, 130, 246, 0.20); font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-arrow-up"></i> Set as Total Purchase Price
                    </button>
                </div>
            </div>

            <div class="form-group full-width" style="margin-top: 10px; margin-bottom: 5px;">
                <div style="font-size: 13px; font-weight: 800; color: #60A5FA; text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-map-location-dot"></i> Location &amp; Address Details
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Zadeshwar Road">
                @error('location') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">City</label>
                <input type="text" name="city" value="{{ old('city') }}" class="form-control" placeholder="e.g. Bharuch">
                @error('city') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">State</label>
                <input type="text" name="state" value="{{ old('state') }}" class="form-control" placeholder="e.g. Gujarat">
                @error('state') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Country</label>
                <input type="text" name="country" value="{{ old('country', 'India') }}" class="form-control">
                @error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" value="{{ old('pincode') }}" class="form-control">
                @error('pincode') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" placeholder="Full property address">{{ old('address') }}</textarea>
                @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full-width">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Property details or notes">{{ old('description') }}</textarea>
                @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Main Image</label>
                <input type="file" name="main_image" class="form-control" accept="image/*">
                @error('main_image') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Document File</label>
                <input type="file" name="document_file" class="form-control">
                @error('document_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 15px; justify-content: flex-end;">
            <a href="{{ route('property-masters.index') }}" class="btn-cancel-custom" style="padding: 10px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); color: #CBD5E1; text-decoration: none;">Cancel</a>
            <button type="submit" class="btn-gold">Save Property Master</button>
        </div>
    </form>
</div>

<script>
function calculateLandTotalPrice(autoFillIfEmpty = true) {
    const areaInput = document.getElementById('total_area');
    const rateInput = document.getElementById('purchase_rate');
    const unitSelect = document.getElementById('area_unit');
    const card = document.getElementById('calculatedPriceCard');
    const textSpan = document.getElementById('calculatedPriceText');
    const formulaSpan = document.getElementById('calculatedPriceFormula');
    const priceInput = document.getElementById('purchase_price');

    if (!areaInput || !rateInput) return;

    const area = parseFloat(areaInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;
    const unit = unitSelect ? unitSelect.value : 'Sq.Ft';

    if (area > 0 && rate > 0) {
        const total = area * rate;
        if (card) card.style.display = 'block';
        if (textSpan) textSpan.textContent = '₹ ' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (formulaSpan) formulaSpan.textContent = `(${area.toLocaleString('en-IN')} ${unit} × ₹${rate.toLocaleString('en-IN')})`;

        // Auto-fill purchase price if purchase price is currently 0 or empty
        if (autoFillIfEmpty && priceInput && (!priceInput.value || parseFloat(priceInput.value) === 0)) {
            priceInput.value = total.toFixed(2);
            recalculatePayment();
        }
    } else {
        if (card) card.style.display = 'none';
    }
}

function applyCalculatedToPurchasePrice() {
    const areaInput = document.getElementById('total_area');
    const rateInput = document.getElementById('purchase_rate');
    const priceInput = document.getElementById('purchase_price');

    const area = parseFloat(areaInput.value) || 0;
    const rate = parseFloat(rateInput.value) || 0;

    if (area > 0 && rate > 0 && priceInput) {
        const total = area * rate;
        priceInput.value = total.toFixed(2);
        recalculatePayment();
    }
}

function recalculatePayment() {
    const priceInput = document.getElementById('purchase_price');
    const paidInput  = document.getElementById('paid_amount');
    const dueInput   = document.getElementById('due_amount');
    const statusSelect = document.getElementById('payment_status');

    const price = parseFloat(priceInput.value) || 0;
    const paid  = parseFloat(paidInput.value) || 0;
    const due   = Math.max(0, price - paid);

    dueInput.value = due.toFixed(2);

    if (statusSelect) {
        if (price > 0) {
            if (paid >= price) {
                statusSelect.value = 'paid';
            } else if (paid > 0) {
                statusSelect.value = 'partial';
            } else {
                statusSelect.value = 'unpaid';
            }
        }
    }
}

function setQuickPayment(type) {
    const priceInput = document.getElementById('purchase_price');
    const paidInput  = document.getElementById('paid_amount');
    const price = parseFloat(priceInput.value) || 0;

    if (type === 'full') {
        paidInput.value = price > 0 ? price.toFixed(2) : '';
    } else if (type === 'half') {
        paidInput.value = price > 0 ? (price / 2).toFixed(2) : '';
    } else if (type === 'unpaid') {
        paidInput.value = (0).toFixed(2);
    }
    recalculatePayment();
}

document.addEventListener('DOMContentLoaded', function() {
    recalculatePayment();
    calculateLandTotalPrice(false);
});
</script>
@endsection
