@extends('admin.layouts.app')
@section('title','Add Material')
@section('page-title','Inventory Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; }

.btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; max-width: 860px; margin: 0 auto 28px;
}

.section-title {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 24px; }
.form-group { margin-bottom: 18px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
@media(max-width:768px) { .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px) { .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: border-color .18s, box-shadow .18s;
}
.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 500; }
.form-hint { font-size: 11.5px; color: #CBD5E1 !important; margin-top: 5px; }
.form-actions { display: flex; align-items: center; gap: 12px; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
</style>
<div class="crud-header"><div class="crud-title"><h2>Add Material</h2><p>Register a new material item in inventory.</p></div></div>
<div class="card-box">  
    <form method="POST" action="{{ route('materials.store') }}">
        @csrf
        @include('admin.components.firm-select')
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Material Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_name">Material Name <span>*</span></label>
                    <input type="text" name="material_name" id="material_name" value="{{ old('material_name') }}" class="form-control @error('material_name') is-invalid @enderror" autocomplete="off" placeholder="e.g. Portland Cement, TMT Steel Bar">
                    @error('material_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="material_category_id">Material Category</label>
                    <select name="material_category_id" id="material_category_id" class="form-control @error('material_category_id') is-invalid @enderror">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('material_category_id')==$cat->id?'selected':'' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @error('material_category_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="unit">Unit</label>
                    <input type="text" name="unit" id="unit" value="{{ old('unit') }}" class="form-control @error('unit') is-invalid @enderror" autocomplete="off" placeholder="e.g. Bag, Kg, Ton, Pcs, Litre">
                    @error('unit')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="opening_stock">Opening Stock</label>
                    <input type="number" step="0.001" name="opening_stock" id="opening_stock" value="{{ old('opening_stock',0) }}" class="form-control @error('opening_stock') is-invalid @enderror" placeholder="0">
                    <div class="form-hint">This will also be set as current stock.</div>
                    @error('opening_stock')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="minimum_stock">Minimum Stock Level</label>
                    <input type="number" step="0.001" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock',0) }}" class="form-control @error('minimum_stock') is-invalid @enderror" placeholder="0">
                    <div class="form-hint">Low stock warning shown below this level.</div>
                    @error('minimum_stock')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="status">Status <span>*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ old('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                    @error('status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Material</button>
            <a href="{{ route('materials.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
