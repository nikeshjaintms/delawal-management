@extends('admin.layouts.app')

@section('title', $vendor->name . ' - Vendor Dossier & Purchases')
@section('page-title', 'Vendor Master')

@section('content')
<style>
    /* ── Luxury Glass Design System ── */
    .vendor-header-card {
        background: rgba(20, 27, 41, 0.70) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35);
    }

    .vendor-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .vendor-profile-identity {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .vendor-avatar {
        width: 68px;
        height: 68px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.45));
        border: 2px solid rgba(96, 165, 250, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 800;
        color: #93C5FD;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        flex-shrink: 0;
    }

    .vendor-identity-info h2 {
        font-size: 24px;
        font-weight: 800;
        color: #FFFFFF !important;
        margin-bottom: 6px;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .vendor-badges-list {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .badge-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .badge-active {
        background: rgba(34, 197, 94, 0.15);
        color: #4ADE80;
        border: 1px solid rgba(34, 197, 94, 0.35);
    }

    .badge-inactive {
        background: rgba(239, 68, 68, 0.15);
        color: #F87171;
        border: 1px solid rgba(239, 68, 68, 0.35);
    }

    .badge-project {
        background: rgba(59, 130, 246, 0.15);
        color: #60A5FA;
        border: 1px solid rgba(59, 130, 246, 0.35);
    }

    .badge-gst {
        background: rgba(168, 85, 247, 0.15);
        color: #C084FC;
        border: 1px solid rgba(168, 85, 247, 0.35);
        font-family: 'Courier New', monospace;
        letter-spacing: 0.5px;
    }

    .badge-city {
        background: rgba(245, 158, 11, 0.15);
        color: #FBBF24;
        border: 1px solid rgba(245, 158, 11, 0.35);
    }

    /* Actions */
    .vendor-actions-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        white-space: nowrap;
    }

    .btn-primary-blue {
        background: linear-gradient(135deg, #2563EB, #1D4ED8) !important;
        color: #FFFFFF !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
    }
    .btn-primary-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.55);
    }

    .btn-success-green {
        background: linear-gradient(135deg, #059669, #047857) !important;
        color: #FFFFFF !important;
        border-color: #10B981 !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    }
    .btn-success-green:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
    }

    .btn-pdf-orange {
        background: rgba(249, 115, 22, 0.18) !important;
        border-color: rgba(249, 115, 22, 0.45) !important;
        color: #FB923C !important;
    }
    .btn-pdf-orange:hover {
        background: #EA580C !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(234, 88, 12, 0.4);
    }

    .btn-dark-glass {
        background: rgba(255, 255, 255, 0.08) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
        color: #E2E8F0 !important;
    }
    .btn-dark-glass:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
    }

    /* ── KPI Grid ── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
        .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .kpi-grid { grid-template-columns: 1fr; }
    }

    .kpi-card {
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.10) !important;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, 0.2) !important;
    }

    .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .kpi-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #94A3B8;
        margin-bottom: 4px;
    }

    .kpi-val {
        font-size: 20px;
        font-weight: 800;
        color: #FFFFFF;
    }

    .kpi-sub {
        font-size: 11.5px;
        color: #64748B;
        margin-top: 2px;
    }

    /* ── Content Card & Tabs ── */
    .content-box {
        background: rgba(20, 27, 41, 0.65) !important;
        backdrop-filter: blur(20px) saturate(160%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
    }

    .nav-tabs-glass {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        padding-bottom: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .tab-btn {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: #94A3B8;
        padding: 9px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .tab-btn:hover {
        background: rgba(255, 255, 255, 0.10);
        color: #FFFFFF;
    }

    .tab-btn.active {
        background: #2563EB !important;
        color: #FFFFFF !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.4);
    }

    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── Table System ── */
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .luxury-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 13px;
    }

    .luxury-table thead {
        background: rgba(16, 22, 34, 0.85);
    }

    .luxury-table th {
        padding: 12px 16px;
        color: #94A3B8;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.6px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        white-space: nowrap;
    }

    .luxury-table td {
        padding: 14px 16px;
        color: #FFFFFF;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        vertical-align: middle;
    }

    .luxury-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.04);
    }

    .amount-cell {
        font-weight: 800;
        font-size: 14px;
        color: #FFFFFF;
        white-space: nowrap;
    }

    .bill-tag {
        display: inline-block;
        padding: 3px 8px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        font-family: monospace;
        font-size: 12px;
        color: #60A5FA;
    }

    .empty-state-box {
        text-align: center;
        padding: 48px 20px;
        color: #94A3B8;
    }
    .empty-state-box i {
        font-size: 42px;
        margin-bottom: 14px;
        color: rgba(255, 255, 255, 0.2);
    }
    .empty-state-box h4 {
        color: #FFFFFF;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    /* ── Profile Details Grid ── */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    @media(max-width:768px){ .profile-grid { grid-template-columns: 1fr; } }

    .profile-item {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 16px;
    }
    .profile-item-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94A3B8;
        letter-spacing: 0.6px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .profile-item-val {
        font-size: 14.5px;
        font-weight: 700;
        color: #FFFFFF;
    }
</style>

{{-- ── Top Vendor Header Card ── --}}
<div class="vendor-header-card">
    <div class="vendor-top-row">
        <div class="vendor-profile-identity">
            <div class="vendor-avatar">
                {{ strtoupper(substr($vendor->name, 0, 1)) }}
            </div>
            <div class="vendor-identity-info">
                <h2>
                    {{ $vendor->name }}
                    <span class="badge-chip badge-{{ $vendor->status }}">
                        <i class="fa-solid fa-circle-dot" style="font-size:9px;"></i> {{ ucfirst($vendor->status) }}
                    </span>
                </h2>
                <div class="vendor-badges-list">
                    @if($vendor->mobile)
                        <span class="badge-chip badge-city">
                            <i class="fa-solid fa-phone"></i> {{ $vendor->mobile }}
                        </span>
                    @endif
                    @if($vendor->email)
                        <span class="badge-chip badge-project">
                            <i class="fa-solid fa-envelope"></i> {{ $vendor->email }}
                        </span>
                    @endif
                    @if($vendor->city)
                        <span class="badge-chip badge-city">
                            <i class="fa-solid fa-location-dot"></i> {{ $vendor->city }}
                        </span>
                    @endif
                    @if($vendor->gst_no)
                        <span class="badge-chip badge-gst">
                            <i class="fa-solid fa-receipt"></i> GST: {{ $vendor->gst_no }}
                        </span>
                    @endif
                    @if($vendor->project)
                        <span class="badge-chip badge-project">
                            <i class="fa-solid fa-city"></i> Project: {{ $vendor->project->project_name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="vendor-actions-group">
            <a href="{{ route('expenses.create', ['vendor_id' => $vendor->id, 'paid_to' => $vendor->name]) }}" class="btn-action btn-primary-blue" title="Record a new bill or purchase from this vendor">
                <i class="fa-solid fa-plus-circle"></i> + Add Purchase / Bill
            </a>
            <a href="{{ route('purchase-orders.create', ['vendor_id' => $vendor->id]) }}" class="btn-action btn-success-green" title="Create a new Purchase Order for this vendor">
                <i class="fa-solid fa-cart-plus"></i> + Create PO
            </a>
            <a href="{{ route('vendors.detail-pdf', $vendor->id) }}" target="_blank" class="btn-action btn-pdf-orange" title="Print full vendor ledger statement">
                <i class="fa-solid fa-file-pdf"></i> Print Statement
            </a>
            <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn-action btn-dark-glass">
                <i class="fa-regular fa-pen-to-square"></i> Edit
            </a>
            <a href="{{ route('vendors.index') }}" class="btn-action btn-dark-glass">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>

{{-- ── Financial Summary KPI Cards ── --}}
<div class="kpi-grid">
    {{-- Total Purchases / Bills --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(59, 130, 246, 0.15); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.3);">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div>
            <div class="kpi-title">Total Purchases / Bills</div>
            <div class="kpi-val" style="color: #60A5FA;">₹{{ number_format($totalExpenseAmount, 2) }}</div>
            <div class="kpi-sub">{{ $totalBillsCount }} Total Bills Recorded</div>
        </div>
    </div>

    {{-- Paid / Approved Amount --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(34, 197, 94, 0.15); color: #4ADE80; border: 1px solid rgba(34, 197, 94, 0.3);">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
            <div class="kpi-title">Paid / Approved</div>
            <div class="kpi-val" style="color: #4ADE80;">₹{{ number_format($approvedExpenseAmount, 2) }}</div>
            <div class="kpi-sub">Total Cleared Payments</div>
        </div>
    </div>

    {{-- Pending / Outstanding Dues --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.3);">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div>
            <div class="kpi-title">Pending / Due</div>
            <div class="kpi-val" style="color: #FBBF24;">₹{{ number_format($pendingExpenseAmount, 2) }}</div>
            <div class="kpi-sub">Awaiting Approval / Payment</div>
        </div>
    </div>

    {{-- Purchase Orders (PO) --}}
    <div class="kpi-card">
        <div class="kpi-icon" style="background: rgba(168, 85, 247, 0.15); color: #C084FC; border: 1px solid rgba(168, 85, 247, 0.3);">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div>
            <div class="kpi-title">Purchase Orders (PO)</div>
            <div class="kpi-val" style="color: #C084FC;">₹{{ number_format($totalPOAmount, 2) }}</div>
            <div class="kpi-sub">{{ $totalPOsCount }} Orders Issued</div>
        </div>
    </div>
</div>

{{-- ── Main Tabs Box ── --}}
<div class="content-box">
    {{-- Tabs Navigation --}}
    <div class="nav-tabs-glass">
        <button type="button" class="tab-btn active" onclick="switchVendorTab('tab-purchases')">
            <i class="fa-solid fa-list-check"></i> Purchases &amp; Bills
            <span style="background:rgba(255,255,255,0.2); padding:2px 7px; border-radius:12px; font-size:11px;">{{ $expenses->count() }}</span>
        </button>
        <button type="button" class="tab-btn" onclick="switchVendorTab('tab-pos')">
            <i class="fa-solid fa-truck-ramp-box"></i> Purchase Orders &amp; Items
            <span style="background:rgba(255,255,255,0.2); padding:2px 7px; border-radius:12px; font-size:11px;">{{ $purchaseOrders->count() }}</span>
        </button>
        @if($debitNotes && $debitNotes->isNotEmpty())
        <button type="button" class="tab-btn" onclick="switchVendorTab('tab-debits')">
            <i class="fa-solid fa-arrow-rotate-left"></i> Debit Notes / Returns
            <span style="background:rgba(255,255,255,0.2); padding:2px 7px; border-radius:12px; font-size:11px;">{{ $debitNotes->count() }}</span>
        </button>
        @endif
        <button type="button" class="tab-btn" onclick="switchVendorTab('tab-profile')">
            <i class="fa-solid fa-address-card"></i> Vendor Profile &amp; Contact Info
        </button>
    </div>

    {{-- ── TAB 1: PURCHASES & BILLS (ITEM / EXPENSE LEDGER) ── --}}
    <div id="tab-purchases" class="tab-content active">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size:16px; font-weight:700; color:#FFFFFF; margin-bottom:2px;">
                    <i class="fa-solid fa-file-invoice-dollar" style="color:#60A5FA;"></i> Vendor Purchase &amp; Bill Ledger
                </h3>
                <p style="font-size:12.5px; color:#94A3B8;">All materials, goods, and expenses purchased from {{ $vendor->name }}.</p>
            </div>
            <a href="{{ route('expenses.create', ['vendor_id' => $vendor->id, 'paid_to' => $vendor->name]) }}" class="btn-action btn-primary-blue" style="font-size:12.5px; padding:7px 14px;">
                <i class="fa-solid fa-plus"></i> + Add New Purchase / Bill
            </a>
        </div>

        @if($expenses->isEmpty())
            <div class="empty-state-box">
                <i class="fa-solid fa-receipt"></i>
                <h4>No Purchases or Bills recorded for this vendor yet.</h4>
                <p>Click the button below to record the first purchase or bill from this vendor.</p>
                <a href="{{ route('expenses.create', ['vendor_id' => $vendor->id, 'paid_to' => $vendor->name]) }}" class="btn-action btn-primary-blue" style="margin-top:14px;">
                    <i class="fa-solid fa-plus-circle"></i> + Add First Purchase / Bill
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="luxury-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Bill / Invoice #</th>
                            <th>Purchased Items / Description</th>
                            <th>Project / Property</th>
                            <th>Category</th>
                            <th style="text-align:right;">Amount</th>
                            <th>Payment Info</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Bill Copy</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $index => $exp)
                        <tr>
                            <td style="color:#64748B;">{{ $index + 1 }}</td>
                            <td style="white-space:nowrap;">
                                <strong style="color:#FFFFFF;">{{ $exp->expense_date ? $exp->expense_date->format('d M Y') : $exp->created_at->format('d M Y') }}</strong>
                                <div style="font-size:11px; color:#64748B;">{{ $exp->expense_date ? $exp->expense_date->format('D') : '' }}</div>
                            </td>
                            <td>
                                @if($exp->bill_no || $exp->invoice_no)
                                    <span class="bill-tag"><i class="fa-solid fa-file-invoice"></i> {{ $exp->bill_no ?: $exp->invoice_no }}</span>
                                @else
                                    <span style="color:#64748B; font-style:italic;">—</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color:#FFFFFF; font-size:13.5px;">{{ $exp->expense_title ?: 'Material / Goods Purchase' }}</strong>
                                @if($exp->description)
                                    <div style="font-size:12px; color:#94A3B8; margin-top:2px;">{{ Str::limit($exp->description, 75) }}</div>
                                @endif
                                @if($exp->purchaseOrder)
                                    <div style="font-size:11px; color:#60A5FA; margin-top:2px;">
                                        <i class="fa-solid fa-link"></i> Linked PO: <strong>{{ $exp->purchaseOrder->po_number }}</strong>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($exp->project)
                                    <div style="color:#60A5FA; font-weight:600;"><i class="fa-solid fa-city" style="font-size:10px;"></i> {{ $exp->project->project_name }}</div>
                                @elseif($exp->property)
                                    <div style="color:#A78BFA; font-weight:600;"><i class="fa-solid fa-building" style="font-size:10px;"></i> {{ $exp->property->property_name }}</div>
                                @else
                                    <span style="color:#94A3B8;">General</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:12px; color:#CBD5E1; background:rgba(255,255,255,0.06); padding:3px 8px; border-radius:6px;">
                                    {{ $exp->expense_category ?: ($exp->category->name ?? 'General') }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="amount-cell">₹{{ number_format($exp->amount, 2) }}</div>
                                @if($exp->total_gst > 0)
                                    <div style="font-size:10.5px; color:#94A3B8;">Incl. GST: ₹{{ number_format($exp->total_gst, 2) }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:12px; color:#FFFFFF;">{{ $exp->payment_mode ?: 'Cash' }}</div>
                                @if($exp->reference_no)
                                    <div style="font-size:11px; color:#94A3B8;">Ref: {{ $exp->reference_no }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($exp->approval_status === 'Approved')
                                    <span class="badge-chip badge-active" style="font-size:11px; padding:2px 8px;"><i class="fa-solid fa-check"></i> Paid / Approved</span>
                                @elseif($exp->approval_status === 'Rejected')
                                    <span class="badge-chip badge-inactive" style="font-size:11px; padding:2px 8px;"><i class="fa-solid fa-xmark"></i> Rejected</span>
                                @else
                                    <span class="badge-chip badge-city" style="font-size:11px; padding:2px 8px;"><i class="fa-solid fa-clock"></i> Pending</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if($exp->bill_file)
                                    <a href="{{ asset('storage/' . $exp->bill_file) }}" target="_blank" class="btn-action btn-dark-glass" style="padding:4px 8px; font-size:11px;" title="View Bill Receipt">
                                        <i class="fa-solid fa-paperclip" style="color:#60A5FA;"></i> Bill
                                    </a>
                                @else
                                    <span style="color:#64748B;">—</span>
                                @endif
                            </td>
                            <td style="text-align:center; white-space:nowrap;">
                                <a href="{{ route('expenses.show', $exp->id) }}" class="btn-action btn-dark-glass" style="padding:4px 8px; font-size:11px;" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('expenses.edit', $exp->id) }}" class="btn-action btn-dark-glass" style="padding:4px 8px; font-size:11px;" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: rgba(16, 22, 34, 0.95); font-weight:800; border-top: 2px solid rgba(255,255,255,0.15);">
                            <td colspan="6" style="text-align:right; color:#94A3B8; text-transform:uppercase; font-size:12px; padding:14px 16px;">
                                Total Purchases / Bills Sum:
                            </td>
                            <td style="text-align:right; font-size:16px; color:#60A5FA; padding:14px 16px;">
                                ₹{{ number_format($totalExpenseAmount, 2) }}
                            </td>
                            <td colspan="4" style="color:#94A3B8; font-size:11.5px; padding:14px 16px;">
                                Paid: <span style="color:#4ADE80; font-weight:bold;">₹{{ number_format($approvedExpenseAmount, 2) }}</span> | 
                                Pending: <span style="color:#FBBF24; font-weight:bold;">₹{{ number_format($pendingExpenseAmount, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- ── TAB 2: PURCHASE ORDERS & ITEM BREAKDOWN ── --}}
    <div id="tab-pos" class="tab-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size:16px; font-weight:700; color:#FFFFFF; margin-bottom:2px;">
                    <i class="fa-solid fa-boxes-stacked" style="color:#C084FC;"></i> Purchase Orders &amp; Ordered Items List
                </h3>
                <p style="font-size:12.5px; color:#94A3B8;">All formal purchase orders and material items requested from {{ $vendor->name }}.</p>
            </div>
            <a href="{{ route('purchase-orders.create', ['vendor_id' => $vendor->id]) }}" class="btn-action btn-success-green" style="font-size:12.5px; padding:7px 14px;">
                <i class="fa-solid fa-plus"></i> + Create New PO
            </a>
        </div>

        @if($purchaseOrders->isEmpty())
            <div class="empty-state-box">
                <i class="fa-solid fa-truck-ramp-box"></i>
                <h4>No Purchase Orders created for this vendor yet.</h4>
                <p>Create a purchase order to track material items, ordered quantities, rates, and delivery dates.</p>
                <a href="{{ route('purchase-orders.create', ['vendor_id' => $vendor->id]) }}" class="btn-action btn-success-green" style="margin-top:14px;">
                    <i class="fa-solid fa-plus-circle"></i> + Create Purchase Order
                </a>
            </div>
        @else
            @foreach($purchaseOrders as $po)
            <div style="background: rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.09); border-radius:14px; padding:18px; margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:12px; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:10px;">
                    <div>
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                            <span style="font-size:15px; font-weight:800; color:#60A5FA;">{{ $po->po_number }}</span>
                            <span class="badge-chip" style="background:rgba(59,130,246,0.15); color:#93C5FD; border:1px solid rgba(59,130,246,0.3);">
                                <i class="fa-regular fa-calendar"></i> PO Date: {{ $po->po_date ? $po->po_date->format('d M Y') : $po->created_at->format('d M Y') }}
                            </span>
                            @if($po->delivery_date)
                            <span class="badge-chip" style="background:rgba(245,158,11,0.15); color:#FCD34D; border:1px solid rgba(245,158,11,0.3);">
                                <i class="fa-solid fa-truck-fast"></i> Delivery Due: {{ $po->delivery_date->format('d M Y') }}
                            </span>
                            @endif
                            <span class="badge-chip {{ in_array($po->status, ['Approved','Received']) ? 'badge-active' : 'badge-city' }}">
                                {{ ucfirst($po->status) }}
                            </span>
                        </div>
                        @if($po->project)
                            <div style="font-size:12px; color:#94A3B8; margin-top:4px;">
                                Project: <strong style="color:#FFFFFF;">{{ $po->project->project_name }}</strong>
                            </div>
                        @endif
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:16px; font-weight:800; color:#FFFFFF;">Total: ₹{{ number_format($po->grand_total, 2) }}</div>
                        <div style="margin-top:4px;">
                            <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn-action btn-dark-glass" style="font-size:11.5px; padding:4px 10px;">
                                <i class="fa-solid fa-eye"></i> View PO
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Item Breakdown Table --}}
                @if($po->items && $po->items->isNotEmpty())
                <div class="table-responsive">
                    <table class="luxury-table" style="font-size:12px;">
                        <thead>
                            <tr style="background:rgba(0,0,0,0.2);">
                                <th style="width:30px;">#</th>
                                <th>Item / Material Name</th>
                                <th style="text-align:right;">Quantity</th>
                                <th style="text-align:right;">Unit Rate (₹)</th>
                                <th style="text-align:right;">GST %</th>
                                <th style="text-align:right;">Line Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($po->items as $itemIdx => $item)
                            <tr>
                                <td style="color:#64748B;">{{ $itemIdx + 1 }}</td>
                                <td>
                                    <strong style="color:#FFFFFF;">{{ $item->material ? $item->material->material_name : ($item->item_name ?? 'Item') }}</strong>
                                    @if($item->description)
                                        <div style="font-size:11px; color:#94A3B8;">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td style="text-align:right; font-weight:700;">{{ $item->quantity }} {{ $item->material->unit ?? ($item->unit ?? '') }}</td>
                                <td style="text-align:right;">₹{{ number_format($item->unit_price ?: $item->rate, 2) }}</td>
                                <td style="text-align:right;">{{ $item->gst_pct ?? 0 }}%</td>
                                <td style="text-align:right; font-weight:800; color:#60A5FA;">₹{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p style="font-size:12px; color:#94A3B8; font-style:italic; margin:0;">No line items detailed in this purchase order.</p>
                @endif
            </div>
            @endforeach
        @endif
    </div>

    {{-- ── TAB 3: DEBIT NOTES / RETURNS ── --}}
    @if($debitNotes && $debitNotes->isNotEmpty())
    <div id="tab-debits" class="tab-content">
        <div style="margin-bottom:16px;">
            <h3 style="font-size:16px; font-weight:700; color:#FFFFFF; margin-bottom:2px;">
                <i class="fa-solid fa-arrow-rotate-left" style="color:#F87171;"></i> Debit Notes &amp; Return Records
            </h3>
            <p style="font-size:12.5px; color:#94A3B8;">Adjustments, damaged materials, or deductions made with {{ $vendor->name }}.</p>
        </div>

        <div class="table-responsive">
            <table class="luxury-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Debit Note #</th>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Reason / Description</th>
                        <th style="text-align:right;">Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($debitNotes as $dnIdx => $dn)
                    <tr>
                        <td style="color:#64748B;">{{ $dnIdx + 1 }}</td>
                        <td><span class="bill-tag">{{ $dn->note_number ?? 'DN-'.$dn->id }}</span></td>
                        <td>{{ $dn->note_date ? \Carbon\Carbon::parse($dn->note_date)->format('d M Y') : $dn->created_at->format('d M Y') }}</td>
                        <td>{{ $dn->project->project_name ?? 'General' }}</td>
                        <td>{{ $dn->reason ?: $dn->remarks ?: 'Material return / adjustment' }}</td>
                        <td style="text-align:right; font-weight:800; color:#F87171;">₹{{ number_format($dn->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 4: VENDOR PROFILE & COMMERCIALS ── --}}
    <div id="tab-profile" class="tab-content">
        <div style="margin-bottom:16px;">
            <h3 style="font-size:16px; font-weight:700; color:#FFFFFF; margin-bottom:2px;">
                <i class="fa-solid fa-address-card" style="color:#60A5FA;"></i> Vendor Information &amp; Commercial Terms
            </h3>
            <p style="font-size:12.5px; color:#94A3B8;">Master contact information and billing registration.</p>
        </div>

        <div class="profile-grid">
            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-building"></i> Full Vendor Name</div>
                <div class="profile-item-val">{{ $vendor->name }}</div>
            </div>

            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-phone"></i> Mobile Phone</div>
                <div class="profile-item-val">{{ $vendor->mobile ?: 'Not provided' }}</div>
            </div>

            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-envelope"></i> Email Address</div>
                <div class="profile-item-val">{{ $vendor->email ?: 'Not provided' }}</div>
            </div>

            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-receipt"></i> GSTIN / Tax Number</div>
                <div class="profile-item-val">
                    @if($vendor->gst_no)
                        <span class="badge-chip badge-gst">{{ $vendor->gst_no }}</span>
                    @else
                        <span style="color:#94A3B8; font-style:italic;">Unregistered / None</span>
                    @endif
                </div>
            </div>

            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-city"></i> City / Town</div>
                <div class="profile-item-val">{{ $vendor->city ?: 'Not specified' }}</div>
            </div>

            <div class="profile-item">
                <div class="profile-item-label"><i class="fa-solid fa-clock"></i> Payment Terms</div>
                <div class="profile-item-val">{{ $vendor->payment_terms ?: 'Standard / Net 30 Days' }}</div>
            </div>

            <div class="profile-item" style="grid-column: 1 / -1;">
                <div class="profile-item-label"><i class="fa-solid fa-location-dot"></i> Complete Address</div>
                <div class="profile-item-val">{{ $vendor->address ?: 'Not provided' }}</div>
            </div>
        </div>

        <div style="margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.08); display:flex; gap:20px; color:#64748B; font-size:12px; flex-wrap:wrap;">
            <div><i class="fa-regular fa-calendar-plus"></i> Registered: {{ $vendor->created_at->format('d M Y, h:i A') }}</div>
            <div><i class="fa-regular fa-calendar-check"></i> Last Updated: {{ $vendor->updated_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>

<script>
    function switchVendorTab(tabId) {
        // Remove active from all tabs
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        // Add active to selected
        const targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.add('active');
        }

        // Highlight active tab button
        event.currentTarget.classList.add('active');
    }
</script>
@endsection
