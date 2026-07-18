@extends('admin.layouts.app')
@section('title','Edit Loan')
@section('page-title','Loan Management')
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
    @media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span.req{color:#EF4444;}
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
    .regen-box{background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.2);border-radius:8px;padding:14px 16px;display:flex;align-items:flex-start;gap:10px;}
    .regen-box label{font-size:13.5px;font-weight:600;color:var(--text-primary);cursor:pointer;}
    .regen-box small{display:block;font-size:12px;color:#DC2626;margin-top:3px;}
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Edit Loan</h2><p>Update — <strong>{{ $loan->bank_name }}</strong> · {{ $loan->loan_type }}</p></div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('loans.update', $loan->id) }}">
        @csrf @method('PUT')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-landmark"></i> Loan Information</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bank Name <span class="req">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name',$loan- class="@error('bank_name') is-invalid @enderror">bank_name) }}" class="form-control">
                    @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Type <span class="req">*</span></label>
                    <select name="loan_type" class="form-control @error('loan_type') is-invalid @enderror">
                        @foreach(['Home Loan','Personal Loan','Business Loan','Mortgage','Car Loan','Other'] as $t)
                            <option value="{{ $t }}" {{ old('loan_type',$loan->loan_type)==$t?'selected':'' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('loan_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Customer <span class="opt">(optional)</span></label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id',$loan->customer_id)==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->mobile }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id',$loan->property_id)==$p->id?'selected':'' }}>{{ $p->property_name }}{{ $p->property_code?' ('.$p->property_code.')':'' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Loan Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="loan_amount" value="{{ old('loan_amount',$loan- class="@error('loan_amount') is-invalid @enderror">loan_amount) }}" class="form-control">
                    @error('loan_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Interest Rate (% p.a.) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="interest_rate" value="{{ old('interest_rate',$loan- class="@error('interest_rate') is-invalid @enderror">interest_rate) }}" class="form-control">
                    @error('interest_rate')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Total EMI Months <span class="req">*</span></label>
                    <input type="number" name="total_emi_months" value="{{ old('total_emi_months',$loan- class="@error('total_emi_months') is-invalid @enderror">total_emi_months) }}" class="form-control">
                    @error('total_emi_months')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">EMI Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="emi_amount" value="{{ old('emi_amount',$loan- class="@error('emi_amount') is-invalid @enderror">emi_amount) }}" class="form-control">
                    @error('emi_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Status <span class="req">*</span></label>
                    <select name="loan_status" class="form-control @error('loan_status') is-invalid @enderror">
                        @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('loan_status',$loan->loan_status)==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-regular fa-calendar-days"></i> Loan Schedule</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Start Date <span class="req">*</span></label>
                    <input type="date" name="loan_start_date" value="{{ old('loan_start_date',\Carbon\Carbon::parse($loan- class="@error('loan_start_date') is-invalid @enderror">loan_start_date)->format('Y-m-d')) }}" class="form-control">
                    @error('loan_start_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan End Date <span class="req">*</span></label>
                    <input type="date" name="loan_end_date" value="{{ old('loan_end_date',\Carbon\Carbon::parse($loan- class="@error('loan_end_date') is-invalid @enderror">loan_end_date)->format('Y-m-d')) }}" class="form-control">
                    @error('loan_end_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            <div class="form-group">
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes...">{{ old('remarks',$loan->remarks) }}</textarea>
            </div>
        </div>

        {{-- Regenerate option --}}
        <div class="form-section">
            <div class="regen-box">
                <input type="checkbox" name="regenerate_emi" value="1" id="regenerate_emi" style="margin-top:2px;accent-color:#EF4444;" class="@error('regenerate_emi') is-invalid @enderror">
                <div>
                    <label for="regenerate_emi">Regenerate EMI Schedule</label>
                    <small>Warning: This will delete all existing EMI records and payment history for this loan.</small>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Loan</button>
            <a href="{{ route('loans.show', $loan->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>
@endsection
