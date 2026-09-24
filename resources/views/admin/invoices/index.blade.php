@extends('admin.layouts.app')

@section('title', 'Invoice Management - Delawala Management')
@section('page-title', 'Invoices & Billing')

@php
    $user = Auth::user();
    $isAdmin = ($user && $user->isAdmin()) || (session('login_type') === 'firm');
@endphp

@section('content')
<style>
/* ── Invoices Styling ── */
.kpi-grid-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}
.kpi-card {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 16px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    transition: transform 0.2s ease, border-color 0.2s ease;
}
.kpi-card:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 255, 255, 0.25);
}
.kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.icon-blue   { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.icon-green  { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.icon-amber  { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.icon-purple { background: rgba(139, 92, 246, 0.18); color: #C084FC; border: 1px solid rgba(139, 92, 246, 0.35); }

.kpi-meta { display: flex; flex-direction: column; min-width: 0; }
.kpi-meta .kpi-title { font-size: 11.5px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 3px; }
.kpi-meta .kpi-val { font-size: 19px; font-weight: 800; color: #FFFFFF; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Filter Bar */
.filter-card {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
}
.filter-form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: 12px;
    align-items: center;
}
@media (max-width: 1100px) {
    .filter-form-grid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (max-width: 650px) {
    .filter-form-grid {
        grid-template-columns: 1fr;
    }
}

.custom-input, .custom-select {
    width: 100%;
    background: rgba(15, 23, 42, 0.85) !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    color: #FFFFFF !important;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 13.5px;
    outline: none;
    transition: border-color 0.2s ease;
}
.custom-input:focus, .custom-select:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.20);
}
.custom-select option { background: #0F172A; color: #FFFFFF; }

/* Invoices Table */
.inv-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.inv-table th {
    background: rgba(15, 23, 42, 0.90);
    color: #94A3B8;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.12);
    white-space: nowrap;
}
.inv-table td {
    padding: 14px 16px;
    font-size: 13.5px;
    color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    vertical-align: middle;
}
.inv-table tbody tr {
    transition: background-color 0.15s ease;
}
.inv-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04);
}

/* Badges */
.badge-inv {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 11.5px;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}
.badge-sale        { background: rgba(59, 130, 246, 0.18); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); }
.badge-rental      { background: rgba(16, 185, 129, 0.18); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-contractor  { background: rgba(245, 158, 11, 0.18); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.35); }
.badge-material    { background: rgba(139, 92, 246, 0.18); color: #D8B4FE; border: 1px solid rgba(139, 92, 246, 0.35); }
.badge-general     { background: rgba(148, 163, 184, 0.18); color: #CBD5E1; border: 1px solid rgba(148, 163, 184, 0.35); }

.badge-paid        { background: rgba(16, 185, 129, 0.20); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.40); }
.badge-partial     { background: rgba(245, 158, 11, 0.20); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.40); }
.badge-unpaid      { background: rgba(239, 68, 68, 0.20); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.40); }

.action-btn-group {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-act {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-act-view { background: rgba(59, 130, 246, 0.15); color: #60A5FA; border-color: rgba(59, 130, 246, 0.30); }
.btn-act-view:hover { background: #3B82F6; color: #FFFFFF; }

.btn-act-print { background: rgba(99, 102, 241, 0.15); color: #A5B4FC; border-color: rgba(99, 102, 241, 0.30); }
.btn-act-print:hover { background: #6366F1; color: #FFFFFF; }

.btn-act-edit { background: rgba(245, 158, 11, 0.15); color: #FBBF24; border-color: rgba(245, 158, 11, 0.30); }
.btn-act-edit:hover { background: #F59E0B; color: #FFFFFF; }

.breadcrumb-nav {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(20, 27, 41, 0.60);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 12px;
    font-size: 13px;
    color: #94A3B8;
    font-weight: 600;
    margin-bottom: 20px;
}
.breadcrumb-nav a {
    color: #60A5FA;
    text-decoration: none;
    font-weight: 700;
    transition: color 0.15s ease;
}
.breadcrumb-nav a:hover { color: #93C5FD; }
.breadcrumb-nav .separator { font-size: 10px; color: #64748B; }
.breadcrumb-nav .active { color: #FFFFFF; font-weight: 700; }

.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}
.crud-title h2 {
    font-size: 26px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}
.crud-title p {
    font-size: 13.5px;
    color: #CBD5E1 !important;
    font-weight: 500;
    margin: 0;
}

/* ── Primary & PDF Action Buttons ── */
.btn-gold, a.btn-gold, button.btn-gold, .btn-primary-custom, a.btn-primary-custom {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 22px !important;
    min-height: 42px !important;
    background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%) !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(96, 165, 250, 0.50) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.btn-gold:hover, .btn-primary-custom:hover {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border-color: #60A5FA !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.55) !important;
    color: #FFFFFF !important;
}

.btn-pdf, a.btn-pdf {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 20px !important;
    min-height: 42px !important;
    background: rgba(99, 102, 241, 0.20) !important;
    color: #C7D2FE !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(99, 102, 241, 0.40) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    backdrop-filter: blur(12px) !important;
    cursor: pointer !important;
    transition: all 0.25s ease !important;
}
.btn-pdf:hover {
    background: #6366F1 !important;
    color: #FFFFFF !important;
    border-color: #818CF8 !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.40) !important;
}

.btn-secondary-custom, a.btn-secondary-custom, .btn-cancel, a.btn-cancel {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 10px 20px !important;
    min-height: 42px !important;
    background: rgba(30, 41, 59, 0.85) !important;
    color: #FFFFFF !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    backdrop-filter: blur(12px) !important;
    cursor: pointer !important;
    transition: all 0.25s ease !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
}
.btn-secondary-custom:hover, .btn-cancel:hover {
    background: rgba(51, 65, 85, 0.95) !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35) !important;
    color: #FFFFFF !important;
}
</style>

{{-- Breadcrumbs & Header --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-file-invoice-dollar" style="color: #60A5FA; margin-right: 6px;"></i>Invoices & Billing</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">All System Invoices</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>Invoices &amp; Billing Management</h2>
        <p>Generate, manage and track customer, rental, contractor, purchase and project invoices</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('invoices.pdf', request()->query()) }}" target="_blank" class="btn-pdf">
            <i class="fa-solid fa-file-pdf"></i> Export PDF List
        </a>
        <a href="{{ route('invoices.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Generate New Invoice
        </a>
    </div>
</div>

{{-- KPI Summary Stats --}}
<div class="kpi-grid-4">
    <div class="kpi-card">
        <div class="kpi-icon-box icon-blue">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="kpi-meta">
            <span class="kpi-title">Total Invoiced</span>
            <span class="kpi-val">₹{{ number_format($totalInvoicedAmount, 2) }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon-box icon-green">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="kpi-meta">
            <span class="kpi-title">Total Collected</span>
            <span class="kpi-val" style="color: #34D399;">₹{{ number_format($totalPaidAmount, 2) }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon-box icon-amber">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>
        <div class="kpi-meta">
            <span class="kpi-title">Balance Outstanding</span>
            <span class="kpi-val" style="color: #FBBF24;">₹{{ number_format($totalBalanceAmount, 2) }}</span>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon-box icon-purple">
            <i class="fa-solid fa-receipt"></i>
        </div>
        <div class="kpi-meta">
            <span class="kpi-title">Total Invoices</span>
            <span class="kpi-val">{{ $totalInvoicesCount }} <span style="font-size: 13px; font-weight: 500; color: #94A3B8;">({{ $paidCount }} Paid)</span></span>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <form method="GET" action="{{ route('invoices.index') }}">
        <div class="filter-form-grid">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" class="custom-input" placeholder="Search by Invoice #, Recipient, Phone, Project...">
            </div>
            <div>
                <select name="project_id" class="custom-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->project_name }} ({{ $p->project_code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="invoice_type" class="custom-select">
                    <option value="">All Types</option>
                    <option value="sale" {{ request('invoice_type') == 'sale' ? 'selected' : '' }}>Property / Plot Sale</option>
                    <option value="rental" {{ request('invoice_type') == 'rental' ? 'selected' : '' }}>Rental / Lease</option>
                    <option value="contractor" {{ request('invoice_type') == 'contractor' ? 'selected' : '' }}>Contractor / Labour</option>
                    <option value="material_purchase" {{ request('invoice_type') == 'material_purchase' ? 'selected' : '' }}>Material / Purchase</option>
                    <option value="custom" {{ request('invoice_type') == 'custom' ? 'selected' : '' }}>General Custom</option>
                </select>
            </div>
            <div>
                <select name="payment_status" class="custom-select">
                    <option value="">All Payment Status</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partially_paid" {{ request('payment_status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary-custom" style="padding: 8px 16px; min-height: 38px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>
                @if(request()->anyFilled(['search', 'project_id', 'invoice_type', 'payment_status', 'date_from', 'date_to']))
                    <a href="{{ route('invoices.index') }}" class="btn-secondary-custom" style="padding: 8px 12px; min-height: 38px;" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Invoices Table Card --}}
<div class="card-box" style="padding: 0 !important; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Recipient / Customer</th>
                    <th>Project</th>
                    <th>Type</th>
                    <th style="text-align: right;">Total Amount</th>
                    <th style="text-align: right;">Paid</th>
                    <th style="text-align: right;">Balance</th>
                    <th style="text-align: center;">Payment Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    <tr>
                        <td>
                            <a href="{{ route('invoices.show', $inv->id) }}" style="color: #60A5FA; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                                <i class="fa-solid fa-file-invoice" style="font-size: 12px;"></i> {{ $inv->invoice_no }}
                            </a>
                        </td>
                        <td style="color: #CBD5E1; font-weight: 600; white-space: nowrap;">
                            {{ $inv->invoice_date->format('d M, Y') }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #FFFFFF;">{{ $inv->recipient_name }}</div>
                            @if($inv->recipient_phone)
                                <div style="font-size: 11.5px; color: #94A3B8;"><i class="fa-solid fa-phone" style="font-size: 10px;"></i> {{ $inv->recipient_phone }}</div>
                            @endif
                        </td>
                        <td>
                            @if($inv->project)
                                <a href="{{ route('projects.show', $inv->project_id) }}" style="color: #93C5FD; text-decoration: none; font-weight: 700;">
                                    <i class="fa-solid fa-city" style="font-size: 11px; margin-right: 4px;"></i>{{ $inv->project->project_name }}
                                </a>
                            @else
                                <span style="color: #64748B;">General / Standalone</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-inv {{ $inv->type_badge_class }}">
                                {{ $inv->type_label }}
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #FFFFFF; white-space: nowrap;">
                            ₹{{ number_format($inv->total_amount, 2) }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: #34D399; white-space: nowrap;">
                            ₹{{ number_format($inv->paid_amount, 2) }}
                        </td>
                        <td style="text-align: right; font-weight: 700; color: {{ $inv->balance_amount > 0 ? '#F87171' : '#94A3B8' }}; white-space: nowrap;">
                            ₹{{ number_format($inv->balance_amount, 2) }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-inv {{ $inv->payment_badge_class }}">
                                {{ str_replace('_', ' ', $inv->payment_status) }}
                            </span>
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <div class="action-btn-group">
                                <a href="{{ route('invoices.show', $inv->id) }}" class="btn-act btn-act-view" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn-act btn-act-print" title="Print Invoice / PDF">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $inv->id) }}" class="btn-act btn-act-edit" title="Edit Invoice">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $inv->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete invoice {{ $inv->invoice_no }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-act btn-act-del" title="Delete Invoice">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px 20px; color: #94A3B8;">
                            <i class="fa-solid fa-file-circle-xmark" style="font-size: 38px; color: #64748B; margin-bottom: 12px; display: block;"></i>
                            <div style="font-size: 16px; font-weight: 700; color: #CBD5E1; margin-bottom: 4px;">No Invoices Found</div>
                            <p style="font-size: 13px; color: #64748B; margin-bottom: 16px;">Create your first invoice or adjust filter criteria.</p>
                            <a href="{{ route('invoices.create') }}" class="btn-gold" style="display: inline-flex;">
                                <i class="fa-solid fa-plus"></i> Generate New Invoice
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.08);">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
