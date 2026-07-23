@extends('admin.layouts.app')
@section('title','View Material')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:800px;margin:0 auto;}
    .mat-hero{display:flex;align-items:center;gap:20px;padding-bottom:22px;margin-bottom:22px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
    .mat-icon{width:60px;height:60px;border-radius:12px;background:var(--gold-light);border:2px solid var(--gold);display:flex;align-items:center;justify-content:center;font-size:24px;color:var(--gold);flex-shrink:0;}
    .mat-hero-info h3{font-size:19px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .mat-hero-info p{font-size:13.5px;color:var(--text-secondary);}
    .section-title{font-size:12px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin:20px 0 14px;padding-bottom:8px;border-bottom:1px solid var(--border-color);}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .detail-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
    @media(max-width:768px){.detail-grid-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.detail-grid,.detail-grid-3{grid-template-columns:1fr;}}
    .detail-item{padding:14px 16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);background:#FFF;}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:7px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary);}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .badge{display:inline-block;padding:4px 12px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#B91C1C;}
    .stock-val{font-size:18px;font-weight:800;}
    .stock-ok{color:#16803D;}
    .stock-low{color:#B91C1C;}
    .low-warn{display:inline-flex;align-items:center;gap:4px;background:rgba(239,68,68,0.08);color:#B91C1C;padding:3px 8px;border-radius:5px;font-size:11.5px;font-weight:700;margin-top:4px;}
    .meta-info{margin-top:20px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Material Details</h2><p>Full inventory material record.</p></div></div>
<div class="card-box">
    <div class="mat-hero">
        <div class="mat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="mat-hero-info">
            <h3>{{ $material->material_name }}</h3>
            <p>{{ $material->materialCategory->category_name ?? 'No Category' }} &nbsp;·&nbsp; Unit: {{ $material->unit ?? '-' }}</p>
            @if($material->current_stock <= $material->minimum_stock && $material->minimum_stock > 0)
                <span class="low-warn"><i class="fa-solid fa-triangle-exclamation" style="font-size:10px;"></i> Low Stock Warning</span>
            @endif
        </div>
    </div>
    <div class="section-title"><i class="fa-solid fa-chart-bar"></i> Stock Summary</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-box"></i> Opening Stock</div>
            <div class="detail-value stock-val">{{ number_format($material->opening_stock,3) }} <small style="font-size:13px;font-weight:400;">{{ $material->unit }}</small></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-warehouse"></i> Current Stock</div>
            <div class="detail-value stock-val {{ $material->current_stock <= $material->minimum_stock && $material->minimum_stock > 0 ? 'stock-low' : 'stock-ok' }}">
                {{ number_format($material->current_stock,3) }} <small style="font-size:13px;font-weight:400;">{{ $material->unit }}</small>
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-triangle-exclamation"></i> Minimum Stock</div>
            <div class="detail-value">{{ number_format($material->minimum_stock,3) }} {{ $material->unit }}</div>
        </div>
    </div>
    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Material Info</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Category</div>
            @if($material->materialCategory)
                <div class="detail-value">{{ $material->materialCategory->category_name }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-ruler"></i> Unit</div>
            @if($material->unit)
                <div class="detail-value">{{ $material->unit }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value"><span class="badge badge-{{ $material->status }}">{{ ucfirst($material->status) }}</span></div>
        </div>
    </div>
    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Created: {{ $material->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $material->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>
    <div class="form-actions">
        <a href="{{ route('materials.edit', $material->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit Material</a>
        <a href="{{ route('materials.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
