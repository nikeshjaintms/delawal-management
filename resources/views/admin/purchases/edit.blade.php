@extends('admin.layouts.app')
@section('title', 'Edit Purchase')
@section('page-title', 'Purchase Management')
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
    max-width: 900px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
@media(max-width:576px){ .form-row { grid-template-columns: 1fr; gap: 0; } }

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
textarea.form-control { resize: vertical; min-height: 90px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }

/* Select2 Glass Styling Overrides */
.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    background-color: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important; min-height: 42px !important; padding: 4px 8px !important;
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
        <h2>Edit Purchase</h2>
        <p>Update purchase — <strong>{{ $purchase->item_name }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('purchases.update', $purchase->id) }}">
        @csrf
        @method('PUT')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-box-open"></i> Purchase Details</div>
            @include('admin.components.firm-select', ['model' => $purchase])

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="item_name">Item Name <span>*</span></label>
                    <input type="text" name="item_name" id="item_name"
                           value="{{ old('item_name', $purchase->item_name) }}"
                           class="form-control" placeholder="e.g. Cement, Steel Rods">
                    @error('item_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="vendor_id">Vendor <span class="opt">(optional)</span></label>
                    <select name="vendor_id" id="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror">
                        <option value="">No Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}"
                                {{ old('vendor_id', $purchase->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendor_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="purchase_date">Purchase Date <span>*</span></label>
                    <input type="date" name="purchase_date" id="purchase_date"
                           value="{{ old('purchase_date', \Carbon\Carbon::parse($purchase->purchase_date)->format('Y-m-d')) }}"
                           class="form-control">
                    @error('purchase_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity"
                           value="{{ old('quantity', $purchase->quantity) }}"
                           class="form-control" placeholder="1" min="0" step="any">
                    @error('quantity')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="purchase_amount">Purchase Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="purchase_amount" id="purchase_amount"
                           value="{{ old('purchase_amount', $purchase->purchase_amount) }}"
                           class="form-control @error('purchase_amount') is-invalid @enderror" placeholder="0.00" min="0">
                    @error('purchase_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()->firm_id); })->where('status', 'active')->orderBy('name')->get() as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode', $purchase->payment_mode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['unpaid','partial','paid'] as $ps)
                            <option value="{{ $ps }}"
                                {{ old('payment_status', $purchase->payment_status) == $ps ? 'selected' : '' }}>
                                {{ ucfirst($ps) }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reference_no">Reference No <span class="opt">(optional)</span></label>
                    <input type="text" name="reference_no" id="reference_no"
                           value="{{ old('reference_no', $purchase->reference_no) }}"
                           class="form-control" placeholder="Bill / Invoice / PO number">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active"   {{ old('status', $purchase->status) == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $purchase->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Remarks <span class="opt">(optional)</span></label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Any additional notes...">{{ old('remarks', $purchase->remarks) }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Purchase
            </button>
            <a href="{{ route('purchases.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
