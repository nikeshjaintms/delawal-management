@extends('admin.layouts.app')
@section('title', 'Add Receipt')
@section('page-title', 'Receipt Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:900px;margin:0 auto;}
    .section-title{font-size:13px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .form-section{margin-bottom:28px;}
    .form-group{margin-bottom:20px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    @media(max-width:576px){.form-row{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-label .opt{color:var(--text-secondary);font-weight:400;font-size:12px;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .form-control[readonly]{background:#F8FAFC;color:var(--text-secondary);}
    textarea.form-control{resize:vertical;min-height:90px;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);display:inline-flex;align-items:center;gap:8px;}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
    .input-hint{font-size:11.5px;color:var(--text-secondary);margin-top:5px;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add New Receipt</h2>
        <p>Fill in the details to create a new receipt.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('receipts.store') }}">
        @csrf

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-invoice-dollar"></i> Receipt Details</div>
            @include('admin.components.firm-select')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="receipt_no">Receipt No</label>
                    <input type="text" name="receipt_no" id="receipt_no"
                           value="{{ old('receipt_no', $nextReceiptNo ?? '') }}"
                           class="form-control @error('receipt_no') is-invalid @enderror" placeholder="Auto-generated receipt number">
                    <div class="input-hint"><i class="fa-solid fa-info-circle"></i> Auto-suggested. You may edit if needed.</div>
                    @error('receipt_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="receipt_date">Receipt Date <span>*</span></label>
                    <input type="date" name="receipt_date" id="receipt_date"
                           value="{{ old('receipt_date', date('Y-m-d')) }}"
                           class="form-control @error('receipt_date') is-invalid @enderror">
                    @error('receipt_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="received_from">Received From <span>*</span></label>
                    <input type="text" name="received_from" id="received_from"
                           value="{{ old('received_from') }}"
                           class="form-control @error('received_from') is-invalid @enderror" placeholder="Customer / person / organisation name">
                    @error('received_from')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount"
                           value="{{ old('amount') }}"
                           class="form-control @error('amount') is-invalid @enderror" placeholder="0.00" min="0">
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_mode_id">Payment Mode</label>
                    <select name="payment_mode_id" id="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach($paymentModes as $mode)
                            <option value="{{ $mode->id }}" {{ old('payment_mode_id') == $mode->id ? 'selected' : '' }}>
                                {{ $mode->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_mode_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="reference_no">Reference No <span class="opt">(optional)</span></label>
                    <input type="text" name="reference_no" id="reference_no"
                           value="{{ old('reference_no') }}"
                           class="form-control @error('reference_no') is-invalid @enderror" placeholder="Cheque / UTR / transaction no">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active"   {{ old('status', 'active') == 'active'   ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="remarks">Remarks <span class="opt">(optional)</span></label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Additional notes about this receipt...">{{ old('remarks') }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Save Receipt
            </button>
            <a href="{{ route('receipts.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
