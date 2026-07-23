@extends('admin.layouts.app')
@section('title','Add Ledger Entry')
@section('page-title','GST / Accounts')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:960px;margin:0 auto;}
    .section-title{font-size:13px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .form-section{margin-bottom:28px;}
    .form-group{margin-bottom:20px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
    .form-row-4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:20px;}
    @media(max-width:900px){.form-row-4{grid-template-columns:1fr 1fr;}.form-row-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.form-row,.form-row-3,.form-row-4{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-label .opt{color:var(--text-secondary);font-weight:400;font-size:12px;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .form-hint{font-size:12px;color:var(--text-secondary);margin-top:5px;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);display:inline-flex;align-items:center;gap:8px;}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);}
    .debit-input:focus{border-color:#DC2626!important;box-shadow:0 0 0 3px rgba(239,68,68,0.12)!important;}
    .credit-input:focus{border-color:#16803D!important;box-shadow:0 0 0 3px rgba(34,197,94,0.12)!important;}
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Add Ledger Entry</h2><p>Record a new debit or credit transaction.</p></div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('ledgers.store') }}">
        @csrf

        {{-- Transaction Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-book-open"></i> Transaction Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Transaction Title <span>*</span></label>
                    <input type="text" name="transaction_title" value="{{ old('transaction_title') }}" class="form-control @error('transaction_title') is-invalid @enderror" placeholder="e.g. Flat A-101 Sale, Cement Purchase">
                    @error('transaction_title')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Ledger Date <span>*</span></label>
                    <input type="date" name="ledger_date" value="{{ old('ledger_date', date('Y-m-d')) }}" class="form-control @error('ledger_date') is-invalid @enderror">
                    @error('ledger_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Transaction Type <span>*</span></label>
                    <select name="transaction_type" class="form-control @error('transaction_type') is-invalid @enderror">
                        <option value="">— Select Type —</option>
                        @foreach(['Sale','Payment Received','Expense','Purchase','Rent Received','Loan EMI','Other'] as $t)
                            <option value="{{ $t }}" {{ old('transaction_type')==$t?'selected':'' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('transaction_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Mode <span class="opt">(optional)</span></label>
                    <select name="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode')==$pm->name?'selected':'' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Debit / Credit --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Debit & Credit Amounts</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" style="color:#DC2626;">Debit Amount (₹) <span class="opt">— money going out</span></label>
                    <input type="number" step="0.01" name="debit_amount" value="{{ old('debit_amount', 0) }}" class="form-control debit-input @error('debit_amount') is-invalid @enderror" placeholder="0.00">
                    @error('debit_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" style="color:#16803D;">Credit Amount (₹) <span class="opt">— money coming in</span></label>
                    <input type="number" step="0.01" name="credit_amount" value="{{ old('credit_amount', 0) }}" class="form-control credit-input @error('credit_amount') is-invalid @enderror" placeholder="0.00">
                    @error('credit_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Reference No <span class="opt">(optional)</span></label>
                    <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="form-control @error('reference_no') is-invalid @enderror" placeholder="Invoice / cheque / receipt no">
                </div>
            </div>
        </div>

        {{-- Linked Parties --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-link"></i> Linked Parties & Property</div>
            <div class="form-row-4">
                <div class="form-group">
                    <label class="form-label">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id')==$p->id?'selected':'' }}>{{ $p->property_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Customer <span class="opt">(optional)</span></label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Vendor <span class="opt">(optional)</span></label>
                    <select name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror">
                        <option value="">— Select Vendor —</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id')==$v->id?'selected':'' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Broker <span class="opt">(optional)</span></label>
                    <select name="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                        <option value="">— Select Broker —</option>
                        @foreach($brokers as $b)
                            <option value="{{ $b->id }}" {{ old('broker_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Remarks --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            <div class="form-group">
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Additional notes about this transaction...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Entry</button>
            <a href="{{ route('ledgers.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>
@endsection
