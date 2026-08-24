@extends('admin.layouts.app')

@section('title', 'Rental Payment History')
@section('page-title', 'Rental Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

/* Rental Summary Card */
.rental-summary {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 22px 26px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
    display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
}
.summary-icon {
    width: 54px; height: 54px; border-radius: 14px;
    background: rgba(59, 130, 246, 0.20) !important; border: 2px solid #3B82F6 !important;
    display: flex; align-items: center; justify-content: center; font-size: 24px; color: #60A5FA !important; flex-shrink: 0;
}
.summary-info { flex: 1; min-width: 200px; }
.summary-info h3 { font-size: 20px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; }
.summary-info p { font-size: 13.5px; color: #CBD5E1 !important; margin: 0; }

.summary-stats { display: flex; gap: 24px; flex-wrap: wrap; }
.stat-chip { text-align: center; }
.stat-chip .stat-label { font-size: 10.5px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 3px; }
.stat-chip .stat-value { font-size: 15px; font-weight: 800; color: #FFFFFF !important; }
.stat-chip .stat-value.gold { color: #60A5FA !important; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial  { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid     { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }

.month-chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important; display: inline-block; white-space: nowrap; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.10); }

.amount-fw { font-weight: 800; color: #60A5FA !important; }
.pending-red { color: #F87171 !important; font-weight: 800; }
.paid-green { color: #34D399 !important; font-weight: 800; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 20px; display: flex; justify-content: center; }
.empty-state { text-align: center; padding: 40px 20px; color: #CBD5E1 !important; }
.empty-state i { font-size: 36px; color: #94A3B8 !important; margin-bottom: 12px; display: block; }
.empty-state p { font-size: 14px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Payment History</h2>
        <p>Monthly rent payment records for this rental.</p>
    </div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('rental-payments.create', $rental->id) }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Payment
        </a>
        <a href="{{ route('rentals.index', ['collect' => 1]) }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Rent Collection
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Rental Summary --}}
<div class="rental-summary">
    <div class="summary-icon"><i class="fa-solid fa-key"></i></div>
    <div class="summary-info">
        <h3>{{ $rental->tenant_name }} <span style="font-size:13px;font-weight:400;color:#94A3B8;">— {{ $rental->tenant_mobile }}</span></h3>
        <p>
            Firm: <strong>{{ $rental->firm->firm_name ?? '-' }}</strong> &nbsp;·&nbsp;
            {{ $rental->property->property_name ?? '' }}
            @if($rental->property?->property_code) ({{ $rental->property->property_code }}) @endif
            @if($rental->property?->unit_no) &nbsp;·&nbsp; Unit {{ $rental->property->unit_no }} @endif
        </p>
    </div>
    <div class="summary-stats">
        <div class="stat-chip">
            <span class="stat-label">Monthly Rent</span>
            <span class="stat-value gold">₹{{ number_format($rental->rent_amount, 0) }}</span>
        </div>
        <div class="stat-chip">
            <span class="stat-label">Rental Status</span>
            <span class="badge badge-{{ $rental->rental_status }}" style="font-size:12px;">{{ ucfirst($rental->rental_status) }}</span>
        </div>
        <div class="stat-chip">
            <span class="stat-label">Payment Status</span>
            <span class="badge badge-{{ $rental->payment_status }}" style="font-size:12px;">{{ ucfirst($rental->payment_status) }}</span>
        </div>
        @if($rental->rent_start_date)
        <div class="stat-chip">
            <span class="stat-label">Since</span>
            <span class="stat-value" style="font-size:13px;">{{ \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') }}</span>
        </div>
        @endif
    </div>
</div>

{{-- Payment Records --}}
<div class="card-box">
    @if($payments->isEmpty())
        <div class="empty-state">
            <i class="fa-regular fa-folder-open"></i>
            <p>No payment records yet. Click <strong>Add Payment</strong> to record the first payment.</p>
        </div>
    @else
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Property Name</th>
                        <th>Property Type</th>
                        <th>Property Code</th>
                        <th>Month / Year</th>
                        <th>Rent Amount</th>
                        <th>Paid Amount</th>
                        <th>Pending Amount</th>
                        <th>Payment Mode</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th style="width: 160px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $key => $pay)
                        <tr>
                            <td>{{ method_exists($payments, 'firstItem') ? ($payments->firstItem() + $key) : ($key + 1) }}</td>
                            <td><strong style="color:#FFFFFF !important;">{{ $pay->property->property_name ?? '—' }}</strong></td>
                            <td style="color:#CBD5E1;">{{ $pay->property->propertyType->name ?? '—' }}</td>
                            <td style="color:#CBD5E1;">{{ $pay->property->property_code ?? '—' }}</td>
                            <td><span class="month-chip">{{ $pay->payment_month }} {{ $pay->payment_year }}</span></td>
                            <td class="amount-fw">₹{{ number_format($pay->rent_amount, 0) }}</td>
                            <td class="paid-green">₹{{ number_format($pay->paid_amount, 0) }}</td>
                            <td class="{{ $pay->pending_amount > 0 ? 'pending-red' : 'paid-green' }}">
                                ₹{{ number_format($pay->pending_amount, 0) }}
                            </td>
                            <td>
                                @if($pay->payment_mode)
                                    <span class="mode-chip">{{ $pay->payment_mode }}</span>
                                @else
                                    <span style="color:#94A3B8;">-</span>
                                @endif
                            </td>
                            <td style="color:#CBD5E1;">{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $pay->payment_status }}">{{ ucfirst($pay->payment_status) }}</span></td>
                            <td style="max-width:160px;font-size:12.5px;color:#CBD5E1;">
                                {{ $pay->remarks ? \Illuminate\Support\Str::limit($pay->remarks, 40) : '-' }}
                            </td>
                            <td>
                                <div class="action-buttons-wrap">
                                    <a href="{{ route('rental-payments.edit', [$rental->id, $pay->id]) }}" class="btn-edit">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('rental-payments.destroy', [$rental->id, $pay->id]) }}"
                                          method="POST" style="display:inline;"
                                          id="del-pay-{{ $pay->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-delete"
                                            onclick="confirmPayDelete({{ $pay->id }}, '{{ $pay->payment_month }} {{ $pay->payment_year }}')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($payments, 'links'))
            <div class="pagination-wrapper">
                {{ $payments->links() }}
            </div>
        @endif
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmPayDelete(id, label) {
    Swal.fire({
        title: 'Delete Payment?',
        html: 'Delete payment record for <strong>' + label + '</strong>?<br><small style="color:#64748B;">The rental status will be recalculated.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-rental-popup' }
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pay-' + id).submit(); });
}
</script>
<style>
    .swal-rental-popup { font-family:'Outfit',sans-serif !important; border-radius:14px !important; }
</style>
@endsection

