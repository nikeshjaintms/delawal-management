@extends('admin.layouts.app')
@section('title','View Material Category')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:700px;margin:0 auto;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .detail-item{padding:14px 16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);background:#FFF;}
    .detail-item-full{grid-column:1/-1;}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:7px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary);}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .badge{display:inline-block;padding:4px 12px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#B91C1C;}
    .meta-info{margin-top:20px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>
<div class="crud-header"><div class="crud-title"><h2>Material Category Details</h2><p>Full details of this inventory category.</p></div></div>
<div class="card-box">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Category Name</div>
            <div class="detail-value">{{ $materialCategory->category_name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value"><span class="badge badge-{{ $materialCategory->status }}">{{ ucfirst($materialCategory->status) }}</span></div>
        </div>
        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-align-left"></i> Description</div>
            @if($materialCategory->description)
                <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.6;">{{ $materialCategory->description }}</div>
            @else
                <div class="detail-value empty">No description provided</div>
            @endif
        </div>
    </div>
    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Created: {{ $materialCategory->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $materialCategory->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>
    <div class="form-actions">
        <a href="{{ route('material-categories.edit', $materialCategory->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
        <a href="{{ route('material-categories.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
