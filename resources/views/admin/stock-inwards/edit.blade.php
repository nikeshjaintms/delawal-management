@extends('admin.layouts.app')
@section('title','Edit Stock Inward')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:860px;margin:0 auto;}
    .section-title{font-size:13px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color);}
    .form-section{margin-bottom:28px;}
    .form-group{margin-bottom:20px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
    @media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background-color:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .calc-hint{font-size:11.5px;color:var(--gold);margin-top:5px;font-weight:600;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Edit Stock Inward</h2><p>Update inward record — stock will be recalculated.</p></div></div>
<div class="card-box">
    <form method="POST" action="{{ route('stock-inwards.update', $stockInward->id) }}">
        @csrf @method('PUT')
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
                    <input type="date" name="inward_date" id="inward_date" value="{{ old('inward_date',$stockInward- class="@error('inward_date') is-invalid @enderror">inward_date?\Carbon\Carbon::parse($stockInward->inward_date)->format('Y-m-d'):'') }}" class="form-control">
                    @error('inward_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="property_id">Property</label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">-- General --</option>
                        @foreach($properties as $p)<option value="{{ $p->id }}" {{ old('property_id',$stockInward->property_id)==$p->id?'selected':'' }}>{{ $p->property_name }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Quantity & Rate</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity <span>*</span></label>
                    <input type="number" step="0.001" name="quantity" id="quantity" value="{{ old('quantity',$stockInward- class="@error('quantity') is-invalid @enderror">quantity) }}" class="form-control" oninput="calcTotal()">
                    @error('quantity')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="rate">Rate per Unit (₹)</label>
                    <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate',$stockInward- class="@error('rate') is-invalid @enderror">rate) }}" class="form-control" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Total Amount (₹)</label>
                    <input type="text" id="total_display" class="form-control" style="background:#F9FAFB;cursor:default;" readonly value="{{ $stockInward->total_amount ? '₹'.number_format($stockInward->total_amount,2) : '' }}">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Qty × Rate</div>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-truck"></i> Supplier & Bill</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="supplier_name">Supplier Name</label>
                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name',$stockInward- class="@error('supplier_name') is-invalid @enderror">supplier_name) }}" class="form-control" autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No</label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no',$stockInward- class="@error('bill_no') is-invalid @enderror">bill_no) }}" class="form-control" autocomplete="off">
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
    document.getElementById('total_display').value=q&&r?'₹'+(q*r).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}):'';
}
</script>
@endsection
