@extends('admin.layouts.app')
@section('title','Edit Credit Note')
@section('page-title','GST / Accounts')
@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.crud-title h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.crud-title p{font-size:13.5px;color:#64748B;}
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:30px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 20px rgba(0,0,0,0.05);max-width:960px;margin:0 auto;}
.section-title{font-size:12.5px;font-weight:700;color:#3B82F6;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;}
.form-section{margin-bottom:28px;}
.form-group{margin-bottom:20px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
@media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
@media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
.form-label{display:block;font-size:13.5px;font-weight:600;color:#0F172A;margin-bottom:8px;}
.form-label span{color:#EF4444;}
.form-label .opt{color:#64748B;font-weight:400;font-size:12px;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:14px;font-family:inherit;color:#0F172A;outline:none;transition:all .18s;background:#fff;}
.form-control:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
textarea.form-control{resize:vertical;min-height:80px;}
.text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
.calc-box{background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;padding:14px 18px;margin-top:4px;}
.calc-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13.5px;}
.form-actions{display:flex;align-items:center;gap:15px;margin-top:28px;padding-top:20px;border-top:1px solid #E2E8F0;}
.btn-gold{background:linear-gradient(135deg,#3B82F6,#2563EB);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .22s;box-shadow:0 2px 8px rgba(59,130,246,0.3);font-family:inherit;}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(59,130,246,0.4);}
.btn-outline{border:1px solid #E2E8F0;background:transparent;color:#64748B;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:all .18s;display:inline-flex;align-items:center;gap:8px;}
.btn-outline:hover{border-color:#3B82F6;color:#3B82F6;background:rgba(59,130,246,0.05);}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Credit Note</h2>
        <p>Update — <strong>{{ $creditNote->credit_note_no ?? 'Credit Note #'.$creditNote->id }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('credit-notes.update', $creditNote->id) }}">
        @csrf @method('PUT')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Credit Note Information</div>
            @include('admin.components.firm-select', ['model' => $creditNote])
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Credit Note No</label>
                    <input type="text" name="credit_note_no" value="{{ old('credit_note_no',$creditNote- class="@error('credit_note_no') is-invalid @enderror">credit_note_no) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Date <span>*</span></label>
                    <input type="date" name="credit_note_date" value="{{ old('credit_note_date',\Carbon\Carbon::parse($creditNote- class="@error('credit_note_date') is-invalid @enderror">credit_note_date)->format('Y-m-d')) }}" class="form-control">
                    @error('credit_note_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id',$creditNote->customer_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Related Invoice No</label>
                    <input type="text" name="related_invoice_no" value="{{ old('related_invoice_no',$creditNote- class="@error('related_invoice_no') is-invalid @enderror">related_invoice_no) }}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status <span>*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('status',$creditNote->status)==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror">{{ old('reason',$creditNote->reason) }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount & GST Breakup</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Taxable Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="taxable_amount" id="taxable_amount"
                           value="{{ old('taxable_amount',$creditNote- class="@error('taxable_amount') is-invalid @enderror">taxable_amount) }}" class="form-control" oninput="calcCredit()">
                    @error('taxable_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">CGST Rate (%)</label>
                    <input type="number" step="0.01" name="cgst_rate" id="cgst_rate" value="{{ old('cgst_rate',$creditNote- class="@error('cgst_rate') is-invalid @enderror">cgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">CGST Amount (₹)</label>
                    <input type="number" step="0.01" name="cgst_amount" id="cgst_amount" value="{{ old('cgst_amount',$creditNote- class="@error('cgst_amount') is-invalid @enderror">cgst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
                <div class="form-group">
                    <label class="form-label">SGST Rate (%)</label>
                    <input type="number" step="0.01" name="sgst_rate" id="sgst_rate" value="{{ old('sgst_rate',$creditNote- class="@error('sgst_rate') is-invalid @enderror">sgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">SGST Amount (₹)</label>
                    <input type="number" step="0.01" name="sgst_amount" id="sgst_amount" value="{{ old('sgst_amount',$creditNote- class="@error('sgst_amount') is-invalid @enderror">sgst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Rate (%)</label>
                    <input type="number" step="0.01" name="igst_rate" id="igst_rate" value="{{ old('igst_rate',$creditNote- class="@error('igst_rate') is-invalid @enderror">igst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Amount (₹)</label>
                    <input type="number" step="0.01" name="igst_amount" id="igst_amount" value="{{ old('igst_amount',$creditNote- class="@error('igst_amount') is-invalid @enderror">igst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
            </div>
            <div class="calc-box">
                <div class="calc-row"><span style="color:#64748B;">Taxable Amount</span><span style="font-weight:700;" id="calc_taxable">₹{{ number_format($creditNote->taxable_amount,2) }}</span></div>
                <div class="calc-row"><span style="color:#64748B;">Total GST</span><span style="font-weight:700;color:#EF4444;" id="calc_gst">₹{{ number_format($creditNote->total_gst,2) }}</span></div>
                <div class="calc-row" style="border-top:1px solid #E2E8F0;margin-top:6px;padding-top:6px;">
                    <span style="font-weight:700;font-size:14px;">Credit Amount</span>
                    <span style="font-weight:800;font-size:16px;color:#059669;" id="calc_credit">₹{{ number_format($creditNote->credit_amount,2) }}</span>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Notes</div>
            <div class="form-group">
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes',$creditNote->notes) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Credit Note</button>
            <a href="{{ route('credit-notes.show', $creditNote->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>
<script>
function fmt(n){ return '₹' + parseFloat(n||0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,','); }
function autoCalcGst(){
    const t = parseFloat(document.getElementById('taxable_amount').value)||0;
    const cr = parseFloat(document.getElementById('cgst_rate').value)||0;
    const sr = parseFloat(document.getElementById('sgst_rate').value)||0;
    const ir = parseFloat(document.getElementById('igst_rate').value)||0;
    if(cr) document.getElementById('cgst_amount').value = (t*cr/100).toFixed(2);
    if(sr) document.getElementById('sgst_amount').value = (t*sr/100).toFixed(2);
    if(ir) document.getElementById('igst_amount').value = (t*ir/100).toFixed(2);
    calcCredit();
}
function calcCredit(){
    const t = parseFloat(document.getElementById('taxable_amount').value)||0;
    const c = parseFloat(document.getElementById('cgst_amount').value)||0;
    const s = parseFloat(document.getElementById('sgst_amount').value)||0;
    const i = parseFloat(document.getElementById('igst_amount').value)||0;
    const gst = c+s+i;
    document.getElementById('calc_taxable').textContent = fmt(t);
    document.getElementById('calc_gst').textContent = fmt(gst);
    document.getElementById('calc_credit').textContent = fmt(t+gst);
}
</script>
@endsection
