@extends('admin.layouts.app')
@section('title','Add Loan')
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
    .calc-box{background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;padding:16px 18px;margin-top:4px;}
    .calc-row{display:flex;justify-content:space-between;align-items:center;font-size:13px;padding:5px 0;border-bottom:1px solid #F1F5F9;}
    .calc-row:last-child{border-bottom:none;font-weight:700;font-size:14px;}
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Add Loan</h2><p>Create a new loan record and auto-generate EMI schedule.</p></div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('loans.store') }}" id="loanForm">
        @csrf

        {{-- Section 1: Loan Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-landmark"></i> Loan Information</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Bank Name <span class="req">*</span></label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="form-control @error('bank_name') is-invalid @enderror" placeholder="e.g. SBI, HDFC Bank, ICICI">
                    @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Type <span class="req">*</span></label>
                    <select name="loan_type" class="form-control @error('loan_type') is-invalid @enderror">
                        <option value="">— Select Type —</option>
                        @foreach(['Home Loan','Personal Loan','Business Loan','Mortgage','Car Loan','Other'] as $t)
                            <option value="{{ $t }}" {{ old('loan_type')==$t?'selected':'' }}>{{ $t }}</option>
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
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->mobile }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" {{ old('property_id')==$p->id?'selected':'' }}>{{ $p->property_name }}{{ $p->property_code?' ('.$p->property_code.')':'' }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Financial Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">Loan Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="loan_amount" id="loan_amount" value="{{ old('loan_amount') }}" class="form-control @error('loan_amount') is-invalid @enderror" placeholder="0.00" oninput="calcEmi()">
                    @error('loan_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Interest Rate (% p.a.) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" class="form-control @error('interest_rate') is-invalid @enderror" placeholder="e.g. 8.5" oninput="calcEmi()">
                    @error('interest_rate')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Total EMI Months <span class="req">*</span></label>
                    <input type="number" name="total_emi_months" id="total_emi_months" value="{{ old('total_emi_months') }}" class="form-control @error('total_emi_months') is-invalid @enderror" placeholder="e.g. 120" oninput="calcEmi()">
                    @error('total_emi_months')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">EMI Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="emi_amount" id="emi_amount" value="{{ old('emi_amount') }}" class="form-control @error('emi_amount') is-invalid @enderror" placeholder="Auto-calculated or enter manually">
                    <div class="form-hint">Auto-calculated based on loan amount, rate & tenure. You can override.</div>
                    @error('emi_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan Status <span class="req">*</span></label>
                    <select name="loan_status" class="form-control @error('loan_status') is-invalid @enderror">
                        @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('loan_status','Active')==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('loan_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Schedule --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-regular fa-calendar-days"></i> Loan Schedule</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Start Date <span class="req">*</span></label>
                    <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date') }}" class="form-control @error('loan_start_date') is-invalid @enderror">
                    @error('loan_start_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Loan End Date <span class="req">*</span></label>
                    <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date') }}" class="form-control @error('loan_end_date') is-invalid @enderror">
                    @error('loan_end_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Remarks --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            <div class="form-group">
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes about this loan...">{{ old('remarks') }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Loan & Generate EMI</button>
            <a href="{{ route('loans.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>

<script>
function calcEmi() {
    const P = parseFloat(document.getElementById('loan_amount').value) || 0;
    const annualRate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const n = parseInt(document.getElementById('total_emi_months').value) || 0;

    if (P > 0 && annualRate > 0 && n > 0) {
        const r = annualRate / 12 / 100;
        const emi = P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
        document.getElementById('emi_amount').value = emi.toFixed(2);
    }

    // Auto-calculate end date
    const startDate = document.getElementById('loan_start_date').value;
    if (startDate && n > 0) {
        const d = new Date(startDate);
        d.setMonth(d.getMonth() + n);
        document.getElementById('loan_end_date').value = d.toISOString().split('T')[0];
    }
}
document.getElementById('loan_start_date').addEventListener('change', calcEmi);
</script>
@endsection
