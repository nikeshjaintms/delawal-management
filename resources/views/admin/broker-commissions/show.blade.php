@extends('admin.layouts.app')

@section('title', 'Broker Commission Details')
@section('page-title', 'Commission Details')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 800px;
        margin: 0 auto;
    }

    .details-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }

    .details-table th, .details-table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid #F1F5F9;
    }

    .details-table th {
        width: 250px;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .details-table td {
        font-size: 14px;
        color: var(--text-primary);
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #B45309; }
    .badge-partial { background: rgba(59, 130, 246, 0.1); color: #1D4ED8; }
    .badge-paid { background: rgba(34, 197, 94, 0.1); color: #16803D; }

    .badge-active { background: rgba(34, 197, 94, 0.1); color: #16803D; }
    .badge-inactive { background: rgba(239, 68, 68, 0.1); color: #B91C1C; }

    .commission-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: rgba(252, 105, 0, 0.08);
        color: #e05c00;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        border: 1px solid rgba(252, 105, 0, 0.2);
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
        border-color: #D1D5DB;
    }

    .btn-gold {
        background-color: #fc6900ff;
        color: #FFFFFF;
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }

    .btn-gold:hover {
        background-color: #e05c00;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Commission Payment Details</h2>
        <p>Detailed view of recorded broker commission payout.</p>
    </div>
</div>

<div class="card-box">
    <table class="details-table">
        <tr>
            <th>Broker Name</th>
            <td><strong>{{ $commission->broker->name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <th>Broker Contact</th>
            <td>{{ $commission->broker->mobile ?? '-' }}</td>
        </tr>
        <tr>
            <th>Property Details</th>
            <td><strong>{{ $commission->property->property_name ?? '-' }}</strong></td>
        </tr>
        <tr>
            <th>Property Value</th>
            <td>₹{{ number_format($commission->property->price ?? 0, 2) }}</td>
        </tr>
        <tr>
            <th>Associated Customer</th>
            <td>{{ $commission->customer->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Associated Booking</th>
            <td>
                @if($commission->booking_id)
                    <a href="{{ route('bookings.show', $commission->booking_id) }}" style="color:#fc6900ff; text-decoration:none; font-weight:600;">
                        Booking #{{ $commission->booking_id }}
                    </a>
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <th>Commission Type</th>
            <td>{{ ucfirst($commission->commission_type) }}</td>
        </tr>
        <tr>
            <th>Commission Value</th>
            <td>
                <span class="commission-chip">
                    @if($commission->commission_type == 'percentage')
                        {{ number_format($commission->commission_value, 2) }}%
                    @else
                        ₹{{ number_format($commission->commission_value, 2) }}
                    @endif
                </span>
            </td>
        </tr>
        <tr>
            <th>Calculated Payout</th>
            <td><strong style="font-size:16px; color:#e05c00;">₹{{ number_format($commission->commission_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td>
                <span class="badge badge-{{ $commission->payment_status }}">
                    {{ ucfirst($commission->payment_status) }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Payment Date</th>
            <td>{{ $commission->payment_date ? \Carbon\Carbon::parse($commission->payment_date)->format('d F Y') : 'Not Paid Yet' }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <span class="badge badge-{{ $commission->status }}">
                    {{ ucfirst($commission->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Recorded By</th>
            <td>{{ $commission->creator->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Remarks</th>
            <td>{{ $commission->remarks ?? '-' }}</td>
        </tr>
        <tr>
            <th>Created At</th>
            <td>{{ $commission->created_at->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <th>Last Updated</th>
            <td>{{ $commission->updated_at->format('d M Y, h:i A') }}</td>
        </tr>
    </table>

    <div class="form-actions">
        @if(Auth::user()->hasPermission('broker_commission_edit'))
        <a href="{{ route('broker-commissions.edit', $commission->id) }}" class="btn-gold">
            <i class="fa-solid fa-edit"></i> Edit Details
        </a>
        @endif
        <a href="{{ route('broker-commissions.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
