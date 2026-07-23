@extends('admin.layouts.app')
@section('title','Edit Debit Note')
@section('page-title','GST / Accounts')
@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.crud-title h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.crud-title p{font-size:13.5px;color:#64748B;}
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:30px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 20px rgba(0,0,0,0.05);max-width:960px;margin:0 auto;}
.section-title{font-size:12.5px;font-weight:700;color:#EF4444;text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;}
.form-section{margin-bottom:28px;}
.form-group{margin-bottom:20px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
@media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
@media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
.form-label{display:block;font-size:13.5px;font-weight:600;color:#0F172A;margin-bottom:8px;}
.form-label span{color:#EF4444;}
.form-control{width:100%;padding:10px 14px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:14px;font-family:inherit;color:#0F172A;outline:none;transition:all .18s;background:#fff;}
.form-control:focus{border-color:#EF4444;box-shadow:0 0 0 3px rgba(239,68,68,0.12);}
textarea.form-control{resize:vertical;min-height:80px;}
.text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
.calc-box{background:#FFF5F5;border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:14px 18px;margin-top:4px;}
.calc-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;font-size:13.5px;}
.form-actions{display:flex;align-items:center;gap:15px;margin-top:28px;padding-top:20px;border-top:1px solid #E2E8F0;}
.btn-red{background:linear-gradient(135deg,#EF4444,#DC2626);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:all .22s;box-shadow:0 2px 8px rgba(239,68,68,0.3);font-family:inherit;}
.btn-red:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(239,68,68,0.4);}
.btn-outline{border:1px solid #E2E8F0;background:transparent;color:#64748B;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:all .18s;display:inline-flex;align-items:center;gap:8px;}
.btn-outline:hover{border-color:#EF4444;color:#EF4444;background:rgba(239,68,68,0.05);}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Debit Note</h2>
        <p>Update — <strong>{{ $debitNote->debit_note_no ?? 'Debit Note #'.$debitNote->id }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('debit-notes.update', $debitNote->id) }}">
        @csrf @method('PUT')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Debit Note Information</div>
            @include('admin.components.firm-select', ['model' => $debitNote])
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Debit Note No</label>
                    <input type="text" name="debit_note_no" value="{{ old('debit_note_no',$debitNote->debit_note_no) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Date <span>*</span></label>
                    <input type="date" name="debit_note_date" value="{{ old('debit_note_date',\Carbon\Carbon::parse($debitNote->debit_note_date)->format('Y-m-d')) }}" class="form-control">
                    @error('debit_note_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Vendor / Supplier</label>
                    <select name="vendor_id" class="form-control @error('vendor_id') is-invalid @enderror">
                        <option value="">— Select Vendor —</option>
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}" {{ old('vendor_id',$debitNote->vendor_id)==$v->id?'selected':'' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Related Bill No</label>
                    <input type="text" name="related_bill_no" value="{{ old('related_bill_no',$debitNote->related_bill_no) }}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status <span>*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('status',$debitNote->status)==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror">{{ old('reason',$debitNote->reason) }}</textarea>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount & GST Breakup</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Taxable Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="taxable_amount" id="taxable_amount"
                           value="{{ old('taxable_amount',$debitNote->taxable_amount) }}" class="form-control" oninput="calcDebit()">
                    @error('taxable_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">CGST Rate (%)</label>
                    <input type="number" step="0.01" name="cgst_rate" id="cgst_rate" value="{{ old('cgst_rate',$debitNote->cgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">CGST Amount (₹)</label>
                    <input type="number" step="0.01" name="cgst_amount" id="cgst_amount" value="{{ old('cgst_amount',$debitNote->cgst_amount) }}" class="form-control" oninput="calcDebit()">
                </div>
                <div class="form-group">
                    <label class="form-label">SGST Rate (%)</label>
                    <input type="number" step="0.01" name="sgst_rate" id="sgst_rate" value="{{ old('sgst_rate',$debitNote->sgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">SGST Amount (₹)</label>
                    <input type="number" step="0.01" name="sgst_amount" id="sgst_amount" value="{{ old('sgst_amount',$debitNote->sgst_amount) }}" class="form-control" oninput="calcDebit()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Rate (%)</label>
                    <input type="number" step="0.01" name="igst_rate" id="igst_rate" value="{{ old('igst_rate',$debitNote->igst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Amount (₹)</label>
                    <input type="number" step="0.01" name="igst_amount" id="igst_amount" value="{{ old('igst_amount',$debitNote->igst_amount) }}" class="form-control" oninput="calcDebit()">
                </div>
            </div>
            <div class="calc-box">
                <div class="calc-row"><span style="color:#64748B;">Taxable Amount</span><span style="font-weight:700;" id="calc_taxable">₹{{ number_format($debitNote->taxable_amount,2) }}</span></div>
                <div class="calc-row"><span style="color:#64748B;">Total GST</span><span style="font-weight:700;color:#EF4444;" id="calc_gst">₹{{ number_format($debitNote->total_gst,2) }}</span></div>
                <div class="calc-row" style="border-top:1px solid rgba(239,68,68,0.15);margin-top:6px;padding-top:6px;">
                    <span style="font-weight:700;font-size:14px;">Debit Amount</span>
                    <span style="font-weight:800;font-size:16px;color:#DC2626;" id="calc_debit">₹{{ number_format($debitNote->debit_amount,2) }}</span>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Notes</div>
            <div class="form-group">
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes',$debitNote->notes) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-red"><i class="fa-solid fa-floppy-disk"></i> Update Debit Note</button>
            <a href="{{ route('debit-notes.show', $debitNote->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
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
    calcDebit();
}
function calcDebit(){
    const t = parseFloat(document.getElementById('taxable_amount').value)||0;
    const c = parseFloat(document.getElementById('cgst_amount').value)||0;
    const s = parseFloat(document.getElementById('sgst_amount').value)||0;
    const i = parseFloat(document.getElementById('igst_amount').value)||0;
    const gst = c+s+i;
    document.getElementById('calc_taxable').textContent = fmt(t);
    document.getElementById('calc_gst').textContent = fmt(gst);
    document.getElementById('calc_debit').textContent = fmt(t+gst);
}
</script>
@endsection
