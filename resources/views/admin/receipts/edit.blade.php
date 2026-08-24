@extends('admin.layouts.app')
@section('title', 'Edit Receipt')
@section('page-title', 'Receipt Management')
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
.form-control[readonly] { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; border: 1px solid rgba(255, 255, 255, 0.10) !important; }
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
        <h2>Edit Receipt</h2>
        <p>Update receipt — <strong>{{ $receipt->receipt_no ?? '' }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('receipts.update', $receipt->id) }}">
        @csrf
        @method('PUT')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-invoice-dollar"></i> Receipt Details</div>
            @include('admin.components.firm-select', ['model' => $receipt])

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="receipt_no">Receipt No</label>
                    <input type="text" name="receipt_no" id="receipt_no"
                           value="{{ old('receipt_no', $receipt->receipt_no) }}"
                           class="form-control" placeholder="Receipt number">
                    @error('receipt_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="receipt_date">Receipt Date <span>*</span></label>
                    <input type="date" name="receipt_date" id="receipt_date"
                           value="{{ old('receipt_date', \Carbon\Carbon::parse($receipt->receipt_date)->format('Y-m-d')) }}"
                           class="form-control">
                    @error('receipt_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="received_from">Received From <span>*</span></label>
                    <input type="text" name="received_from" id="received_from"
                           value="{{ old('received_from', $receipt->received_from) }}"
                           class="form-control" placeholder="Customer / person / organisation name">
                    @error('received_from')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount"
                           value="{{ old('amount', $receipt->amount) }}"
                           class="form-control" placeholder="0.00" min="0">
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_mode_id">Payment Mode</label>
                    <select name="payment_mode_id" id="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach($paymentModes as $mode)
                            <option value="{{ $mode->id }}"
                                {{ old('payment_mode_id', $receipt->payment_mode_id) == $mode->id ? 'selected' : '' }}>
                                {{ $mode->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_mode_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reference_no">Reference No <span class="opt">(optional)</span></label>
                    <input type="text" name="reference_no" id="reference_no"
                           value="{{ old('reference_no', $receipt->reference_no) }}"
                           class="form-control" placeholder="Cheque / UTR / transaction no">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active"   {{ old('status', $receipt->status) == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $receipt->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Remarks <span class="opt">(optional)</span></label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Additional notes about this receipt...">{{ old('remarks', $receipt->remarks) }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Receipt
            </button>
            <a href="{{ route('receipts.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
