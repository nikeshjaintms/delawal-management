@extends('admin.layouts.app')
@section('title','Edit Material')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:800px;margin:0 auto;}
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
    .form-control-readonly{background:#F9FAFB;color:var(--text-secondary);cursor:default;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .form-hint{font-size:12px;color:var(--text-secondary);margin-top:5px;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Edit Material</h2><p>Update — <strong>{{ $material->material_name }}</strong></p></div></div>
<div class="card-box">
    <form method="POST" action="{{ route('materials.update', $material->id) }}">
        @csrf @method('PUT')
        @include('admin.components.firm-select', ['model' => $material])
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Material Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_name">Material Name <span>*</span></label>
                    <input type="text" name="material_name" id="material_name" value="{{ old('material_name', $material->material_name) }}" class="form-control @error('material_name') is-invalid @enderror" autocomplete="off">
                    @error('material_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="material_category_id">Material Category</label>
                    <select name="material_category_id" id="material_category_id" class="form-control @error('material_category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('material_category_id',$material->material_category_id)==$cat->id?'selected':'' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="unit">Unit</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit', $material->unit) }}" class="form-control @error('unit') is-invalid @enderror" autocomplete="off" placeholder="e.g. Bag, Kg, Pcs">
                </div>
                <div class="form-group">
                    <label class="form-label">Current Stock</label>
                    <input type="text" class="form-control form-control-readonly" readonly value="{{ number_format($material->current_stock,3) }} {{ $material->unit }}">
                    <div class="form-hint">Managed automatically via Stock Inward / Outward.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="minimum_stock">Minimum Stock Level</label>
                    <input type="number" step="0.001" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock',$material- class="@error('minimum_stock') is-invalid @enderror">minimum_stock) }}" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="opening_stock">Opening Stock</label>
                    <input type="number" step="0.001" name="opening_stock" id="opening_stock" value="{{ old('opening_stock',$material- class="@error('opening_stock') is-invalid @enderror">opening_stock) }}" class="form-control">
                    <div class="form-hint">Changing opening stock does NOT recalculate current stock.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status',$material->status)=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ old('status',$material->status)=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Material</button>
            <a href="{{ route('materials.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
