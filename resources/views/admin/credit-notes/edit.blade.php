@extends('admin.layouts.app')
@section('title','Edit Credit Note')
@section('page-title','GST / Accounts')
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
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 30px; }
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

.calc-box {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 16px; padding: 20px 22px; margin-top: 14px;
}
.calc-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 13.5px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
.calc-row:last-child { border-bottom: none; padding-top: 12px; margin-top: 4px; }

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
                    <input type="text" name="credit_note_no" value="{{ old('credit_note_no',$creditNote->credit_note_no) }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Date <span>*</span></label>
                    <input type="date" name="credit_note_date" value="{{ old('credit_note_date',\Carbon\Carbon::parse($creditNote->credit_note_date)->format('Y-m-d')) }}" class="form-control">
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
                    <input type="text" name="related_invoice_no" value="{{ old('related_invoice_no',$creditNote->related_invoice_no) }}" class="form-control">
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
                           value="{{ old('taxable_amount',$creditNote->taxable_amount) }}" class="form-control" oninput="calcCredit()">
                    @error('taxable_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">CGST Rate (%)</label>
                    <input type="number" step="0.01" name="cgst_rate" id="cgst_rate" value="{{ old('cgst_rate',$creditNote->cgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">CGST Amount (₹)</label>
                    <input type="number" step="0.01" name="cgst_amount" id="cgst_amount" value="{{ old('cgst_amount',$creditNote->cgst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
                <div class="form-group">
                    <label class="form-label">SGST Rate (%)</label>
                    <input type="number" step="0.01" name="sgst_rate" id="sgst_rate" value="{{ old('sgst_rate',$creditNote->sgst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">SGST Amount (₹)</label>
                    <input type="number" step="0.01" name="sgst_amount" id="sgst_amount" value="{{ old('sgst_amount',$creditNote->sgst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Rate (%)</label>
                    <input type="number" step="0.01" name="igst_rate" id="igst_rate" value="{{ old('igst_rate',$creditNote->igst_rate) }}" class="form-control" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Amount (₹)</label>
                    <input type="number" step="0.01" name="igst_amount" id="igst_amount" value="{{ old('igst_amount',$creditNote->igst_amount) }}" class="form-control" oninput="calcCredit()">
                </div>
            </div>
            <div class="calc-box">
                <div class="calc-row"><span style="color:#CBD5E1;">Taxable Amount</span><span style="font-weight:700;color:#FFFFFF;" id="calc_taxable">₹{{ number_format($creditNote->taxable_amount,2) }}</span></div>
                <div class="calc-row"><span style="color:#CBD5E1;">Total GST</span><span style="font-weight:700;color:#F87171;" id="calc_gst">₹{{ number_format($creditNote->total_gst,2) }}</span></div>
                <div class="calc-row">
                    <span style="font-weight:800;font-size:15px;color:#FFFFFF;">Credit Amount (Grand Total)</span>
                    <span style="font-weight:800;font-size:18px;color:#34D399;" id="calc_credit">₹{{ number_format($creditNote->credit_amount,2) }}</span>
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
