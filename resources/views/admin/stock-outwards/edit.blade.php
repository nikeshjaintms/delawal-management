@extends('admin.layouts.app')
@section('title','Edit Stock Outward')
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
    @media(max-width:576px){.form-row{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background-color:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    textarea.form-control{resize:vertical;min-height:80px;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .form-hint{font-size:12px;color:var(--text-secondary);margin-top:5px;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Edit Stock Outward</h2><p>Update outward — stock will be recalculated.</p></div></div>
<div class="card-box">
    <form method="POST" action="{{ route('stock-outwards.update', $stockOutward->id) }}">
        @csrf @method('PUT')
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-arrow-up-from-bracket"></i> Outward Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_id">Material <span>*</span></label>
                    <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror">
                        <option value="">-- Select Material --</option>
                        @foreach($materials as $m)<option value="{{ $m->id }}" {{ old('material_id',$stockOutward->material_id)==$m->id?'selected':'' }}>{{ $m->material_name }} ({{ $m->unit }}) — Stock: {{ number_format($m->current_stock,2) }}</option>@endforeach
                    </select>
                    @error('material_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="outward_date">Outward Date <span>*</span></label>
                    <input type="date" name="outward_date" id="outward_date" value="{{ old('outward_date',$stockOutward->outward_date?\Carbon\Carbon::parse($stockOutward->outward_date)->format('Y-m-d'):'') }}" class="form-control">
                    @error('outward_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity <span>*</span></label>
                    <input type="number" step="0.001" name="quantity" id="quantity" value="{{ old('quantity',$stockOutward->quantity) }}" class="form-control">
                    <div class="form-hint">Current record: {{ number_format($stockOutward->quantity,3) }} {{ $stockOutward->material?->unit }}</div>
                    @error('quantity')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_id">Property</label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">-- Not property-specific --</option>
                        @foreach($properties as $p)<option value="{{ $p->id }}" {{ old('property_id',$stockOutward->property_id)==$p->id?'selected':'' }}>{{ $p->property_name }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-clipboard-list"></i> Usage Details</div>
            <div class="form-group">
                <label class="form-label" for="used_for">Used For</label>
                <input type="text" name="used_for" id="used_for" value="{{ old('used_for',$stockOutward->used_for) }}" class="form-control" autocomplete="off" placeholder="e.g. Foundation work, Plastering">
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks',$stockOutward->remarks) }}</textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Outward</button>
            <a href="{{ route('stock-outwards.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
