@extends('admin.layouts.app')
@section('title','Add Credit Note')
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
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

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
    <div class="crud-title"><h2>Add Credit Note</h2><p>Record a new customer credit adjustment or return.</p></div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('credit-notes.store') }}" id="cnForm">
        @csrf

        {{-- Basic Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Credit Note Information</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Credit Note No <span class="opt">(optional)</span></label>
                    <input type="text" name="credit_note_no" value="{{ old('credit_note_no') }}" class="form-control @error('credit_note_no') is-invalid @enderror" placeholder="e.g. CN-2026-001">
                    @error('credit_note_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Date <span>*</span></label>
                    <input type="date" name="credit_note_date" value="{{ old('credit_note_date', date('Y-m-d')) }}" class="form-control @error('credit_note_date') is-invalid @enderror">
                    @error('credit_note_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Customer <span class="opt">(optional)</span></label>
                    <select name="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Related Invoice No <span class="opt">(optional)</span></label>
                    <input type="text" name="related_invoice_no" value="{{ old('related_invoice_no') }}" class="form-control @error('related_invoice_no') is-invalid @enderror" placeholder="e.g. INV-2026-045">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status <span>*</span></label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('status','Pending')==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" placeholder="Reason for credit note...">{{ old('reason') }}</textarea>
            </div>
        </div>

        {{-- GST Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount & GST Breakup</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Taxable Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="taxable_amount" id="taxable_amount"
                           value="{{ old('taxable_amount',0) }}" class="form-control @error('taxable_amount') is-invalid @enderror" placeholder="0.00" oninput="calcCredit()">
                    @error('taxable_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">CGST Rate (%) <span class="opt">(optional)</span></label>
                    <input type="number" step="0.01" name="cgst_rate" id="cgst_rate" value="{{ old('cgst_rate') }}" class="form-control @error('cgst_rate') is-invalid @enderror" placeholder="e.g. 9" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">CGST Amount (₹)</label>
                    <input type="number" step="0.01" name="cgst_amount" id="cgst_amount" value="{{ old('cgst_amount',0) }}" class="form-control @error('cgst_amount') is-invalid @enderror" placeholder="0.00" oninput="calcCredit()">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <div style="width:100%;background:rgba(14,165,233,0.15);border:1px solid rgba(14,165,233,0.35);border-radius:10px;padding:10px 14px;font-size:13.5px;font-weight:700;color:#60A5FA;">
                        CGST: ₹<span id="cgst_display">0.00</span>
                    </div>
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">SGST Rate (%) <span class="opt">(optional)</span></label>
                    <input type="number" step="0.01" name="sgst_rate" id="sgst_rate" value="{{ old('sgst_rate') }}" class="form-control @error('sgst_rate') is-invalid @enderror" placeholder="e.g. 9" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">SGST Amount (₹)</label>
                    <input type="number" step="0.01" name="sgst_amount" id="sgst_amount" value="{{ old('sgst_amount',0) }}" class="form-control @error('sgst_amount') is-invalid @enderror" placeholder="0.00" oninput="calcCredit()">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <div style="width:100%;background:rgba(20,184,166,0.15);border:1px solid rgba(20,184,166,0.35);border-radius:10px;padding:10px 14px;font-size:13.5px;font-weight:700;color:#2DD4BF;">
                        SGST: ₹<span id="sgst_display">0.00</span>
                    </div>
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label">IGST Rate (%) <span class="opt">(optional)</span></label>
                    <input type="number" step="0.01" name="igst_rate" id="igst_rate" value="{{ old('igst_rate') }}" class="form-control @error('igst_rate') is-invalid @enderror" placeholder="e.g. 18" oninput="autoCalcGst()">
                </div>
                <div class="form-group">
                    <label class="form-label">IGST Amount (₹)</label>
                    <input type="number" step="0.01" name="igst_amount" id="igst_amount" value="{{ old('igst_amount',0) }}" class="form-control @error('igst_amount') is-invalid @enderror" placeholder="0.00" oninput="calcCredit()">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <div style="width:100%;background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.35);border-radius:10px;padding:10px 14px;font-size:13.5px;font-weight:700;color:#C4B5FD;">
                        IGST: ₹<span id="igst_display">0.00</span>
                    </div>
                </div>
            </div>
            {{-- Live calculation display --}}
            <div class="calc-box">
                <div class="calc-row"><span style="color:#CBD5E1;">Taxable Amount</span><span style="font-weight:700;color:#FFFFFF;" id="calc_taxable">₹0.00</span></div>
                <div class="calc-row"><span style="color:#CBD5E1;">Total GST (CGST + SGST + IGST)</span><span style="font-weight:700;color:#F87171;" id="calc_gst">₹0.00</span></div>
                <div class="calc-row">
                    <span style="font-weight:800;font-size:15px;color:#FFFFFF;">Credit Amount (Grand Total)</span>
                    <span style="font-weight:800;font-size:18px;color:#34D399;" id="calc_credit">₹0.00</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Additional Notes</div>
            <div class="form-group">
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Credit Note</button>
            <a href="{{ route('credit-notes.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>
    </form>
</div>

<script>
function fmt(n){ return '₹' + parseFloat(n||0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,','); }
function autoCalcGst(){
    const taxable = parseFloat(document.getElementById('taxable_amount').value)||0;
    const cr = parseFloat(document.getElementById('cgst_rate').value)||0;
    const sr = parseFloat(document.getElementById('sgst_rate').value)||0;
    const ir = parseFloat(document.getElementById('igst_rate').value)||0;
    if(cr) document.getElementById('cgst_amount').value = (taxable * cr / 100).toFixed(2);
    if(sr) document.getElementById('sgst_amount').value = (taxable * sr / 100).toFixed(2);
    if(ir) document.getElementById('igst_amount').value = (taxable * ir / 100).toFixed(2);
    calcCredit();
}
function calcCredit(){
    const t = parseFloat(document.getElementById('taxable_amount').value)||0;
    const c = parseFloat(document.getElementById('cgst_amount').value)||0;
    const s = parseFloat(document.getElementById('sgst_amount').value)||0;
    const i = parseFloat(document.getElementById('igst_amount').value)||0;
    const gst = c + s + i;
    document.getElementById('cgst_display').textContent = c.toFixed(2);
    document.getElementById('sgst_display').textContent = s.toFixed(2);
    document.getElementById('igst_display').textContent = i.toFixed(2);
    document.getElementById('calc_taxable').textContent = fmt(t);
    document.getElementById('calc_gst').textContent = fmt(gst);
    document.getElementById('calc_credit').textContent = fmt(t + gst);
}
document.addEventListener('DOMContentLoaded', calcCredit);
</script>
@endsection
