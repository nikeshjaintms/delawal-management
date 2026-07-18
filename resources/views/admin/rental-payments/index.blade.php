@extends('admin.layouts.app')

@section('title', 'Rental Payment History')
@section('page-title', 'Rental Management')

@section('content')
<style>
    .crud-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:15px; }
    .crud-title h2 { font-size:22px; font-weight:700; color:var(--text-primary); margin-bottom:4px; }
    .crud-title p  { font-size:13.5px; color:var(--text-secondary); }
    .btn-gold { background-color:var(--gold); color:#FFF; padding:10px 20px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; display:inline-flex; align-items:center; gap:8px; border:none; cursor:pointer; transition:var(--transition); box-shadow:0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color:#B58D1B; transform:translateY(-1px); }
    .btn-outline { border:1px solid var(--border-color); background:transparent; color:var(--text-secondary); padding:9px 18px; border-radius:8px; text-decoration:none; font-size:13.5px; font-weight:600; display:inline-flex; align-items:center; gap:8px; transition:var(--transition); }
    .btn-outline:hover { background:#F9FAFB; color:var(--text-primary); border-color:#D1D5DB; }

    /* Rental Summary Card */
    .rental-summary {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px 24px;
        box-shadow: var(--soft-shadow);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .summary-icon { width:52px; height:52px; border-radius:10px; background:var(--gold-light); border:2px solid var(--gold); display:flex; align-items:center; justify-content:center; font-size:22px; color:var(--gold); flex-shrink:0; }
    .summary-info { flex:1; min-width:200px; }
    .summary-info h3 { font-size:18px; font-weight:700; color:var(--text-primary); margin-bottom:3px; }
    .summary-info p  { font-size:13px; color:var(--text-secondary); }
    .summary-stats { display:flex; gap:20px; flex-wrap:wrap; }
    .stat-chip { text-align:center; }
    .stat-chip .stat-label { font-size:10.5px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.7px; display:block; margin-bottom:3px; }
    .stat-chip .stat-value { font-size:15px; font-weight:800; color:var(--text-primary); }
    .stat-chip .stat-value.gold { color:var(--gold); }

    .card-box { background:var(--card-bg); border:1px solid var(--border-color); border-radius:12px; padding:24px; box-shadow:var(--soft-shadow); }
    .table-container { width:100%; overflow-x:auto; }
    .premium-table { width:100%; border-collapse:collapse; text-align:left; font-size:13.5px; }
    .premium-table th { padding:13px 14px; background:#F9FAFB; color:var(--text-secondary); font-weight:600; border-bottom:1px solid var(--border-color); font-size:11.5px; text-transform:uppercase; letter-spacing:0.5px; white-space:nowrap; }
    .premium-table td { padding:14px; border-bottom:1px solid #F1F5F9; color:var(--text-primary); vertical-align:middle; }
    .premium-table tr:last-child td { border-bottom:none; }
    .premium-table tbody tr:hover { background-color:#F9FAFB; }
    .badge { display:inline-block; padding:4px 10px; font-size:11px; font-weight:600; border-radius:20px; text-transform:uppercase; }
    .badge-pending  { background:rgba(234,179,8,0.12); color:#92710A; }
    .badge-partial  { background:rgba(59,130,246,0.1);  color:#1D4ED8; }
    .badge-paid     { background:rgba(34,197,94,0.1);   color:#16803D; }
    .month-chip { background:var(--gold-light); color:#92710A; padding:4px 10px; border-radius:6px; font-size:12px; font-weight:700; border:1px solid rgba(212,175,55,0.25); display:inline-block; white-space:nowrap; }
    .mode-chip  { background:#F1F5F9; color:#475569; padding:3px 8px; border-radius:5px; font-size:12px; font-weight:600; display:inline-block; }
    .amount-fw  { font-weight:700; }
    .pending-red  { color:#B91C1C; font-weight:700; }
    .paid-green   { color:#16803D; font-weight:700; }
    .action-links { display:flex; gap:10px; align-items:center; }
    .action-link { color:var(--text-secondary); text-decoration:none; font-size:13px; transition:var(--transition); display:inline-flex; align-items:center; gap:4px; }
    .action-link.delete-btn { background:none; border:none; cursor:pointer; color:var(--text-secondary); font-family:var(--font-primary); font-size:13px; padding:0; }
    .action-link.delete-btn:hover { color:#EF4444; }
    .alert-success { background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.2); color:#16803D; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:13.5px; display:flex; align-items:center; gap:8px; }
    .pagination-wrapper { margin-top:20px; display:flex; justify-content:center; }
    .empty-state { text-align:center; padding:40px 20px; color:var(--text-secondary); }
    .empty-state i { font-size:36px; color:#D1D5DB; margin-bottom:12px; display:block; }
    .empty-state p { font-size:14px; }
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
        <a href="{{ route('rentals.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Rentals
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
        <h3>{{ $rental->tenant_name }} <span style="font-size:13px;font-weight:400;color:var(--text-secondary);">— {{ $rental->tenant_mobile }}</span></h3>
        <p>
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
                        <th>Month / Year</th>
                        <th>Rent Amount</th>
                        <th>Paid Amount</th>
                        <th>Pending Amount</th>
                        <th>Payment Mode</th>
                        <th>Payment Date</th>
                        <th>Status</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $key => $pay)
                        <tr>
                            <td>{{ $payments->firstItem() + $key }}</td>
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
                                    <span style="color:var(--text-secondary);">-</span>
                                @endif
                            </td>
                            <td>{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : '-' }}</td>
                            <td><span class="badge badge-{{ $pay->payment_status }}">{{ ucfirst($pay->payment_status) }}</span></td>
                            <td style="max-width:160px;font-size:12.5px;color:var(--text-secondary);">
                                {{ $pay->remarks ? \Illuminate\Support\Str::limit($pay->remarks, 40) : '-' }}
                            </td>
                            <td>
                                <div class="table-action-buttons">
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

        <div class="pagination-wrapper">
            {{ $payments->links() }}
        </div>
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

