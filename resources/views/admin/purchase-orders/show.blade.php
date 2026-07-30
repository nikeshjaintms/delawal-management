@extends('admin.layouts.app')
@section('title', 'Purchase Order Detail')
@section('page-title', 'Purchase Order Detail')
@php
    $user = Auth::user();
    if (!$user && session('login_type') === 'firm' && session('firm_id')) {
        $authUser = new class {
            public function isAdmin()        { return true; }
            public function hasPermission($p){ return true; }
            public $role = null;
            public $name = '';
            public $firm_id = null;
        };
        $authUser->name = session('firm_name', 'Firm');
        $authUser->firm_id = session('firm_id');
    } else {
        $authUser = $user;
    }
@endphp
@section('content')
<style>
    .form-card {background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:28px 32px; box-shadow:var(--card-shadow); margin-bottom:24px;}
    .section-heading {font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:var(--blue); margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid var(--blue-light); display:flex; align-items:center; gap:8px;}
    .detail-grid {display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;}
    @media(max-width:640px){.detail-grid{grid-template-columns:1fr}}
    .detail-item {font-size:13.5px; line-height:1.6;}
    .detail-label {font-weight:600; color:var(--text-secondary); width:150px; display:inline-block;}
    .detail-value {color:var(--text-primary); font-weight:500;}
    
    .items-table {width:100%; border-collapse:collapse; margin-top:15px; font-size:13px;}
    .items-table th {background:#F9FAFB; padding:12px 10px; font-weight:600; border-bottom:1.5px solid var(--border-color); text-transform:uppercase; font-size:11px; letter-spacing:0.5px;}
    .items-table td {padding:12px 10px; border-bottom:1px solid #F1F5F9; vertical-align:middle;}
    
    .badge {display:inline-block; padding:4px 10px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase;}
    .badge-Draft{background:rgba(100,116,139,0.1);color:#64748B;}
    .badge-Pending{background:rgba(245,158,11,0.1);color:#D97706;}
    .badge-Approved{background:rgba(59,130,246,0.1);color:#2563EB;}
    .badge-Ordered{background:rgba(139,92,246,0.1);color:#7C3AED;}
    .badge-Received{background:rgba(16,185,129,0.1);color:#059669;}
    .badge-Cancelled{background:rgba(239,68,68,0.1);color:#DC2626;}

    .summary-box {width:320px; margin-left:auto; margin-top:20px; border:1px solid var(--border-color); border-radius:8px; padding:16px; background:#F9FAFB;}
    .summary-row {display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #E2E8F0; font-size:13px;}
    .summary-row:last-child {border-bottom:none; font-weight:700; font-size:15px; color:#059669;}
    .btn-outline {border:1px solid var(--border-color); background:#fff; color:var(--text-secondary); padding:10px 20px; border-radius:8px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer;}
    .btn-outline:hover {background:#f9fafb; color:var(--text-primary);}
</style>

<div class="crud-header" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
    <div class="crud-title">
        <h2>Purchase Order: {{ $purchaseOrder->po_number }}</h2>
        <p>Issued on {{ $purchaseOrder->po_date->format('d M Y') }}</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('purchase-orders.index') }}" class="btn-outline"><i class="fa fa-arrow-left"></i> Back</a>
        
        @if($authUser->hasPermission('purchase_order_print'))
        <a href="{{ route('purchase-orders.print', $purchaseOrder->id) }}" target="_blank" class="btn-outline" style="border-color:#10B981; color:#059669;"><i class="fa fa-print"></i> Print PO</a>
        @endif

        @if($authUser->hasPermission('purchase_order_edit') && in_array($purchaseOrder->status, ['Draft', 'Pending']))
        <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" class="btn-gold" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;"><i class="fa fa-edit"></i> Edit PO</a>
        @endif
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> Order Information</div>
    
    <div class="detail-grid">
        <div class="detail-item">
            <div><span class="detail-label">PO Number:</span><span class="detail-value">{{ $purchaseOrder->po_number }}</span></div>
            <div><span class="detail-label">Firm:</span><span class="detail-value">{{ $purchaseOrder->firm->firm_name ?? '-' }}</span></div>
            <div><span class="detail-label">Status:</span><span class="badge badge-{{ $purchaseOrder->status }}">{{ $purchaseOrder->status }}</span></div>
            <div><span class="detail-label">Created By:</span><span class="detail-value">{{ $purchaseOrder->creator->name ?? '-' }}</span></div>
        </div>
        <div class="detail-item">
            <div><span class="detail-label">Supplier Name:</span><span class="detail-value">{{ $purchaseOrder->vendor->name ?? '-' }}</span></div>
            <div><span class="detail-label">GST No:</span><span class="detail-value">{{ $purchaseOrder->vendor->gst_no ?? 'N/A' }}</span></div>
            <div><span class="detail-label">PO Date:</span><span class="detail-value">{{ $purchaseOrder->po_date->format('d M Y') }}</span></div>
            <div><span class="detail-label">Delivery Date:</span><span class="detail-value">{{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d M Y') : '—' }}</span></div>
        </div>
    </div>

    @if($purchaseOrder->vendor)
    <div class="detail-grid" style="margin-top:-10px; border-top:1px dashed #E2E8F0; padding-top:15px;">
        <div class="detail-item">
            <div><span class="detail-label">Supplier Mobile:</span><span class="detail-value">{{ $purchaseOrder->vendor->mobile ?? '-' }}</span></div>
            <div><span class="detail-label">Supplier Email:</span><span class="detail-value">{{ $purchaseOrder->vendor->email ?? '-' }}</span></div>
        </div>
        <div class="detail-item">
            <div><span class="detail-label">Supplier Address:</span><span class="detail-value">{{ $purchaseOrder->vendor->address ?? '-' }}, {{ $purchaseOrder->vendor->city ?? '' }}</span></div>
        </div>
    </div>
    @endif

    <div class="section-heading" style="margin-top:30px;"><i class="fa-solid fa-list"></i> Order Items</div>
    
    <div class="table-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Material Description</th>
                    <th style="text-align:right; width:12%;">Quantity</th>
                    <th style="text-align:right; width:15%;">Unit Rate</th>
                    <th style="text-align:right; width:10%;">Disc %</th>
                    <th style="text-align:right; width:10%;">GST %</th>
                    <th style="text-align:right; width:15%;">GST Amount</th>
                    <th style="text-align:right; width:15%;">Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->material->material_name ?? '-' }}</strong>
                    </td>
                    <td style="text-align:right;">{{ number_format($item->qty, 2) }} {{ $item->material->unit ?? '' }}</td>
                    <td style="text-align:right;">₹{{ number_format($item->rate, 2) }}</td>
                    <td style="text-align:right;">{{ number_format($item->discount_pct, 2) }}%</td>
                    <td style="text-align:right;">{{ number_format($item->gst_pct, 2) }}%</td>
                    <td style="text-align:right;">₹{{ number_format($item->gst_amount, 2) }}</td>
                    <td style="text-align:right; font-weight:600;">₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span>Sub Total:</span>
            <span>₹{{ number_format($purchaseOrder->sub_total, 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Discount:</span>
            <span>₹{{ number_format($purchaseOrder->discount_amount, 2) }}</span>
        </div>
        <div class="summary-row">
            <span>Taxable Value:</span>
            <span>₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</span>
        </div>
        
        @if($purchaseOrder->igst_amount > 0)
        <div class="summary-row">
            <span>IGST:</span>
            <span>₹{{ number_format($purchaseOrder->igst_amount, 2) }}</span>
        </div>
        @else
        <div class="summary-row">
            <span>CGST:</span>
            <span>₹{{ number_format($purchaseOrder->cgst_amount, 2) }}</span>
        </div>
        <div class="summary-row">
            <span>SGST:</span>
            <span>₹{{ number_format($purchaseOrder->sgst_amount, 2) }}</span>
        </div>
        @endif
        
        <div class="summary-row">
            <span>Grand Total:</span>
            <span>₹{{ number_format($purchaseOrder->grand_total, 2) }}</span>
        </div>
    </div>

    @if($purchaseOrder->remarks)
    <div style="margin-top:20px; border-top:1px dashed #E2E8F0; padding-top:15px;">
        <label class="form-label" style="font-weight:700; color:var(--text-secondary);">Remarks / Terms &amp; Conditions:</label>
        <p style="font-size:13px; color:var(--text-primary); margin-top:5px; white-space:pre-wrap;">{{ $purchaseOrder->remarks }}</p>
    </div>
    @endif
</div>
@endsection
