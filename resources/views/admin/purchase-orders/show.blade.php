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
    /* ── Luxury Dark Glass System ── */
    .crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
    .crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
    .crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

    .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .form-card {
        background: rgba(20, 27, 41, 0.60) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 24px !important;
        padding: 32px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
        margin-bottom: 24px;
    }

    .section-heading {
        font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
        border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
        display: flex; align-items: center; gap: 8px;
    }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media(max-width: 640px) { .detail-grid { grid-template-columns: 1fr; } }

    .detail-item {
        padding: 16px 18px;
        background: rgba(16, 22, 34, 0.65) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px !important;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .detail-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .detail-label {
        font-size: 12px;
        font-weight: 700;
        color: #94A3B8 !important;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    .detail-value {
        font-size: 13.5px;
        color: #FFFFFF !important;
        font-weight: 600;
        text-align: right;
    }

    .table-container {
        overflow-x: auto;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.10);
        margin-top: 15px;
    }
    .items-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .items-table th {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #94A3B8 !important;
        padding: 14px 16px;
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.6px;
    }
    .items-table td {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        vertical-align: middle;
        color: #FFFFFF;
    }
    .items-table tbody tr:hover { background: rgba(255, 255, 255, 0.03); }
    .items-table tbody tr:last-child td { border-bottom: none; }

    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 12px; font-size: 11px; font-weight: 700;
        border-radius: 20px; text-transform: uppercase; white-space: nowrap !important;
    }
    .badge-Draft    { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
    .badge-Pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
    .badge-Approved { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
    .badge-Ordered  { background: rgba(139, 92, 246, 0.18) !important; color: #C084FC !important; border: 1px solid rgba(139, 92, 246, 0.35) !important; }
    .badge-Received { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
    .badge-Cancelled{ background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

    .summary-box {
        width: 360px;
        margin-left: auto;
        margin-top: 24px;
        border: 1.5px solid rgba(255, 255, 255, 0.12);
        border-radius: 16px;
        padding: 20px 24px;
        background: rgba(16, 22, 34, 0.75);
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 13.5px;
        color: #CBD5E1;
    }
    .summary-row span:last-child {
        font-weight: 600;
        color: #FFFFFF;
    }
    .summary-row.grand-total {
        border-bottom: none;
        border-top: 1px dashed rgba(255, 255, 255, 0.20);
        margin-top: 6px;
        padding-top: 14px;
        font-weight: 800;
        font-size: 16px;
    }
    .summary-row.grand-total span:first-child {
        color: #60A5FA;
    }
    .summary-row.grand-total span:last-child {
        color: #34D399;
        font-size: 18px;
    }

    .btn-outline {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 20px; background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important; font-size: 13.5px; font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 10px; text-decoration: none !important;
        transition: all .25s ease; cursor: pointer;
    }
    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.30) !important;
    }

    .btn-print {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 20px; background: rgba(16, 185, 129, 0.16) !important;
        color: #34D399 !important; font-size: 13.5px; font-weight: 700;
        border: 1px solid rgba(16, 185, 129, 0.35) !important;
        border-radius: 10px; text-decoration: none !important;
        transition: all .25s ease; cursor: pointer;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.15);
    }
    .btn-print:hover {
        background: #10B981 !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
    }

    .btn-gold, .btn-primary {
        background: #2563EB !important; color: #FFFFFF !important;
        padding: 10px 20px; border-radius: 10px;
        font-size: 13.5px; font-weight: 700;
        border: 1px solid #3B82F6 !important;
        cursor: pointer; display: inline-flex; align-items: center; gap: 8px;
        transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
        text-decoration: none !important;
    }
    .btn-gold:hover, .btn-primary:hover {
        background: #1D4ED8 !important; color: #FFFFFF !important;
        transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52);
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Purchase Order: {{ $purchaseOrder->po_number }}</h2>
        <p>Issued on {{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d M Y') : '—' }}</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('purchase-orders.index') }}" class="btn-outline">
            <i class="fa fa-arrow-left"></i> Back
        </a>
        
        @if($authUser->hasPermission('purchase_order_print'))
        <a href="{{ route('purchase-orders.print', $purchaseOrder->id) }}" target="_blank" class="btn-print">
            <i class="fa fa-print"></i> Print PO
        </a>
        @endif

        @if($authUser->hasPermission('purchase_order_edit') && in_array($purchaseOrder->status, ['Draft', 'Pending']))
        <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" class="btn-gold">
            <i class="fa fa-edit"></i> Edit PO
        </a>
        @endif
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> Order Information</div>
    
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-row"><span class="detail-label">PO Number</span><span class="detail-value">{{ $purchaseOrder->po_number }}</span></div>
            <div class="detail-row"><span class="detail-label">Firm</span><span class="detail-value">{{ $purchaseOrder->firm->firm_name ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Status</span><span class="badge badge-{{ $purchaseOrder->status }}">{{ $purchaseOrder->status }}</span></div>
            <div class="detail-row"><span class="detail-label">Created By</span><span class="detail-value">{{ $purchaseOrder->creator->name ?? '-' }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-row"><span class="detail-label">Supplier Name</span><span class="detail-value">{{ $purchaseOrder->vendor->name ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">GST No</span><span class="detail-value">{{ $purchaseOrder->vendor->gst_no ?? 'N/A' }}</span></div>
            <div class="detail-row"><span class="detail-label">PO Date</span><span class="detail-value">{{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d M Y') : '—' }}</span></div>
            <div class="detail-row"><span class="detail-label">Delivery Date</span><span class="detail-value">{{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d M Y') : '—' }}</span></div>
        </div>
    </div>

    @if($purchaseOrder->vendor)
    <div class="detail-grid" style="margin-top:-8px;">
        <div class="detail-item">
            <div class="detail-row"><span class="detail-label">Supplier Mobile</span><span class="detail-value">{{ $purchaseOrder->vendor->mobile ?? '-' }}</span></div>
            <div class="detail-row"><span class="detail-label">Supplier Email</span><span class="detail-value">{{ $purchaseOrder->vendor->email ?? '-' }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-row"><span class="detail-label">Supplier Address</span><span class="detail-value">{{ $purchaseOrder->vendor->address ?? '-' }}{{ $purchaseOrder->vendor->city ? ', '.$purchaseOrder->vendor->city : '' }}</span></div>
        </div>
    </div>
    @endif

    <div class="section-heading" style="margin-top:28px;"><i class="fa-solid fa-list"></i> Order Items</div>
    
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
                @forelse($purchaseOrder->items as $index => $item)
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
                    <td style="text-align:right; font-weight:700; color:#34D399;">₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:#94A3B8; padding:24px;">No items in this purchase order.</td>
                </tr>
                @endforelse
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
        
        <div class="summary-row grand-total">
            <span>Grand Total:</span>
            <span>₹{{ number_format($purchaseOrder->grand_total, 2) }}</span>
        </div>
    </div>

    @if($purchaseOrder->remarks)
    <div style="margin-top:24px; border-top:1px dashed rgba(255,255,255,0.12); padding-top:18px;">
        <label class="form-label" style="font-weight:700; color:#94A3B8; font-size:12px; text-transform:uppercase; letter-spacing:0.6px;">Remarks / Terms &amp; Conditions:</label>
        <p style="font-size:13.5px; color:#FFFFFF; margin-top:6px; white-space:pre-wrap; line-height:1.6;">{{ $purchaseOrder->remarks }}</p>
    </div>
    @endif
</div>
@endsection
