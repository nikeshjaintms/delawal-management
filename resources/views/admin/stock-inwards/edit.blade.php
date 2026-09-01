@extends('admin.layouts.app')
@section('title','Edit Stock Inward')
@section('page-title','Inventory Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 12px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; max-width: 860px; margin: 0 auto 28px;
}

.section-title {
    font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}

.form-section { margin-bottom: 28px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
@media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}

.form-label { display: block; font-size: 13.5px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 8px; }
.form-label span { color: #F87171; }

.form-control {
    width: 100% !important;
    padding: 11px 16px !important;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    font-family: var(--font-primary) !important;
    color: #FFFFFF !important;
    outline: none !important;
    transition: all 0.2s ease !important;
    box-sizing: border-box !important;
}
input.form-control, select.form-control {
    height: 44px !important;
}
select.form-control option {
    background: #101622 !important;
    color: #FFFFFF !important;
}
textarea.form-control {
    min-height: 100px !important;
    height: auto !important;
    resize: vertical !important;
    background: rgba(16, 22, 34, 0.65) !important;
    color: #FFFFFF !important;
}
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(185deg);
    cursor: pointer;
}

.text-error { color: #F87171; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
.calc-hint { font-size: 11.5px; color: #60A5FA; margin-top: 5px; font-weight: 600; }
.form-actions { display: flex; align-items: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
</style>
<div class="crud-header"><div class="crud-title"><h2>Edit Stock Inward</h2><p>Update inward record — stock will be recalculated.</p></div></div>
<div class="card-box">
    <form method="POST" action="{{ route('stock-inwards.update', $stockInward->id) }}">
        @csrf @method('PUT')
        @include('admin.components.firm-select', ['model' => $stockInward])
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-arrow-down-to-bracket"></i> Inward Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_id">Material <span>*</span></label>
                    <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror">
                        <option value="">-- Select Material --</option>
                        @foreach($materials as $m)<option value="{{ $m->id }}" {{ old('material_id',$stockInward->material_id)==$m->id?'selected':'' }}>{{ $m->material_name }} ({{ $m->unit }})</option>@endforeach
                    </select>
                    @error('material_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="inward_date">Inward Date <span>*</span></label>
                    <input type="date" name="inward_date" id="inward_date" value="{{ old('inward_date', $stockInward->inward_date ? \Carbon\Carbon::parse($stockInward->inward_date)->format('Y-m-d') : '') }}" class="form-control @error('inward_date') is-invalid @enderror">
                    @error('inward_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id',$stockInward->project_id)==$p->id?'selected':'' }}>{{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="contractor_id">Contractor</label>
                    <select name="contractor_id" id="contractor_id" class="form-control @error('contractor_id') is-invalid @enderror">
                        <option value="">-- Select Contractor --</option>
                        @foreach($contractors as $c)<option value="{{ $c->id }}" data-project-id="{{ $c->project_id ?? '' }}" {{ old('contractor_id',$stockInward->contractor_id)==$c->id?'selected':'' }}>{{ $c->contractor_name }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Quantity & Rate</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity <span>*</span></label>
                    <input type="number" step="0.001" name="quantity" id="quantity" value="{{ old('quantity', $stockInward->quantity) }}" class="form-control @error('quantity') is-invalid @enderror" oninput="calcTotal()">
                    @error('quantity')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rate">Rate per Unit (₹)</label>
                    <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate', $stockInward->rate) }}" class="form-control @error('rate') is-invalid @enderror" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Total Amount (₹)</label>
                    <input type="text" id="total_display" class="form-control" style="background:rgba(255,255,255,0.06) !important; color:#34D399 !important; font-weight:700; cursor:default;" readonly value="{{ $stockInward->total_amount ? '₹'.number_format($stockInward->total_amount,2) : '' }}">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Qty × Rate</div>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-truck"></i> Supplier & Bill</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="supplier_name">Supplier Name</label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name', $stockInward->supplier_name) }}" class="form-control @error('supplier_name') is-invalid @enderror" autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No</label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no', $stockInward->bill_no) }}" class="form-control @error('bill_no') is-invalid @enderror" autocomplete="off">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks',$stockInward->remarks) }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Inward</button>
            <a href="{{ route('stock-inwards.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
<script>
function calcTotal(){
    const q=parseFloat(document.getElementById('quantity').value)||0;
    const r=parseFloat(document.getElementById('rate').value)||0;
    document.getElementById('total_display').value = q&&r ? '₹'+(q*r).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}) : '';
}

document.addEventListener('DOMContentLoaded', function() {
    const projSelect = document.getElementById('project_id');
    const conSelect  = document.getElementById('contractor_id');

    function syncContractors() {
        if (!projSelect || !conSelect) return;
        const pId = projSelect.value;
        let firstMatch = '';
        let matchCount = 0;

        Array.from(conSelect.options).forEach(opt => {
            if (!opt.value) {
                opt.hidden = false;
                opt.disabled = false;
                return;
            }
            const optPId = opt.dataset.projectId || '';
            if (!pId || !optPId || optPId === pId) {
                opt.hidden = false;
                opt.disabled = false;
                if (pId && optPId === pId) {
                    matchCount++;
                    if (!firstMatch) firstMatch = opt.value;
                }
            } else {
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        const currentSelected = conSelect.selectedOptions[0];
        if (currentSelected && currentSelected.hidden) {
            conSelect.value = firstMatch || '';
        } else if (pId && matchCount > 0 && !conSelect.value) {
            conSelect.value = firstMatch;
        }
    }

    if (projSelect && conSelect) {
        projSelect.addEventListener('change', syncContractors);
        conSelect.addEventListener('change', function() {
            const opt = this.selectedOptions[0];
            if (opt && opt.dataset.projectId && (!projSelect.value || projSelect.value !== opt.dataset.projectId)) {
                projSelect.value = opt.dataset.projectId;
            }
        });
        syncContractors();
    }
});
</script>
@endsection
