@extends('admin.layouts.app')
@section('title','Edit Stock Outward')
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
@media(max-width:576px){.form-row{grid-template-columns:1fr;gap:0;}}

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
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }
.form-actions { display: flex; align-items: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
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
                    <label class="form-label" for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id',$stockOutward->project_id)==$p->id?'selected':'' }}>{{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})</option>@endforeach
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
