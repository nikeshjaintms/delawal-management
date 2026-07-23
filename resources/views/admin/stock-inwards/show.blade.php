@extends('admin.layouts.app')
@section('title','View Stock Inward')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:800px;margin:0 auto;}
    .inw-hero{display:flex;align-items:center;gap:20px;padding-bottom:22px;margin-bottom:22px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
    .inw-icon{width:56px;height:56px;border-radius:12px;background:rgba(34,197,94,0.1);border:2px solid rgba(34,197,94,0.3);display:flex;align-items:center;justify-content:center;font-size:22px;color:#16803D;flex-shrink:0;}
    .inw-hero-info h3{font-size:18px;font-weight:700;color:var(--text-primary);margin-bottom:3px;}
    .inw-hero-info p{font-size:13px;color:var(--text-secondary);}
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
    .qty-big{font-size:18px;font-weight:800;color:#16803D;}
    .amount-big{font-size:18px;font-weight:800;color:var(--text-primary);}
    .meta-info{margin-top:20px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Stock Inward Details</h2><p>Full record of this stock receipt.</p></div></div>
<div class="card-box">
    <div class="inw-hero">
        <div class="inw-icon"><i class="fa-solid fa-arrow-down-to-bracket"></i></div>
        <div class="inw-hero-info">
            <h3>{{ $stockInward->material->material_name ?? '-' }}</h3>
            <p>{{ \Carbon\Carbon::parse($stockInward->inward_date)->format('d M Y') }} &nbsp;·&nbsp; Qty: <strong style="color:#16803D;">+{{ number_format($stockInward->quantity,3) }} {{ $stockInward->material?->unit }}</strong>
            @if($stockInward->total_amount) &nbsp;·&nbsp; ₹{{ number_format($stockInward->total_amount,2) }} @endif</p>
        </div>
    </div>
    <div class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Material & Property</div>
    <div class="detail-grid">
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-boxes-stacked"></i> Material</div><div class="detail-value">{{ $stockInward->material->material_name ?? '-' }}@if($stockInward->material?->materialCategory)<div style="font-size:12px;color:var(--text-secondary);margin-top:2px;">{{ $stockInward->material->materialCategory->category_name }}</div>@endif</div></div>
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>@if($stockInward->property)<div class="detail-value">{{ $stockInward->property->property_name }}</div>@else<div class="detail-value empty">General</div>@endif</div>
        <div class="detail-item"><div class="detail-label"><i class="fa-regular fa-calendar"></i> Inward Date</div><div class="detail-value">{{ \Carbon\Carbon::parse($stockInward->inward_date)->format('d M Y') }}</div></div>
    </div>
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Quantity & Amount</div>
    <div class="detail-grid-3">
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-cubes"></i> Quantity</div><div class="detail-value qty-big">+{{ number_format($stockInward->quantity,3) }} {{ $stockInward->material?->unit }}</div></div>
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-tag"></i> Rate / Unit</div>@if($stockInward->rate)<div class="detail-value">₹{{ number_format($stockInward->rate,2) }}</div>@else<div class="detail-value empty">Not set</div>@endif</div>
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Total Amount</div>@if($stockInward->total_amount)<div class="detail-value amount-big">₹{{ number_format($stockInward->total_amount,2) }}</div>@else<div class="detail-value empty">Not set</div>@endif</div>
    </div>
    <div class="section-title"><i class="fa-solid fa-truck"></i> Supplier & Bill</div>
    <div class="detail-grid">
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-user"></i> Supplier</div>@if($stockInward->supplier_name)<div class="detail-value">{{ $stockInward->supplier_name }}</div>@else<div class="detail-value empty">Not provided</div>@endif</div>
        <div class="detail-item"><div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Bill No</div>@if($stockInward->bill_no)<div class="detail-value">{{ $stockInward->bill_no }}</div>@else<div class="detail-value empty">Not provided</div>@endif</div>
        @if($stockInward->remarks)<div class="detail-item" style="grid-column:1/-1"><div class="detail-label"><i class="fa-solid fa-note-sticky"></i> Remarks</div><div class="detail-value" style="font-weight:400;font-size:14px;">{{ $stockInward->remarks }}</div></div>@endif
    </div>
    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Created: {{ $stockInward->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $stockInward->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>
    <div class="form-actions">
        <a href="{{ route('stock-inwards.edit', $stockInward->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
        <a href="{{ route('stock-inwards.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
