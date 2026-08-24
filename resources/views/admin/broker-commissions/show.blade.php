@extends('admin.layouts.app')

@section('title', 'Broker Commission Details')
@section('page-title', 'Commission Details')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.header-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.06) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: rgba(255, 255, 255, 0.12) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    max-width: 850px;
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
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.details-table th {
    width: 240px;
    font-weight: 800;
    color: #94A3B8 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.details-table td {
    font-size: 14.5px;
    font-weight: 600;
    color: #FFFFFF !important;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    text-transform: uppercase;
}

.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.commission-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: rgba(245, 158, 11, 0.15) !important;
    color: #FBBF24 !important;
    font-size: 12.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid rgba(245, 158, 11, 0.30) !important;
}

.form-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Commission Payment Details</h2>
        <p>Detailed view of recorded broker commission payout.</p>
    </div>
    <div class="header-actions">
        @if(Auth::user()->hasPermission('broker_commission_edit'))
        <a href="{{ route('broker-commissions.edit', $commission->id) }}" class="btn-primary-custom">
            <i class="fa-solid fa-edit"></i> Edit Details
        </a>
        @endif
        <a href="{{ route('broker-commissions.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="card-box">
    <table class="details-table">
        <tr>
            <th>Firm</th>
            <td><strong>{{ $commission->firm_names }}</strong></td>
        </tr>
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
                    <a href="{{ route('bookings.show', $commission->booking_id) }}" style="color:#60A5FA; text-decoration:none; font-weight:700;">
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
            <td><strong style="font-size:18px; color:#34D399;">₹{{ number_format($commission->commission_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <th>Payment Status</th>
            <td>
                <span class="badge badge-{{ $commission->payment_status }}">
                    <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($commission->payment_status) }}
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
                    <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($commission->status) }}
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
        <a href="{{ route('broker-commissions.edit', $commission->id) }}" class="btn-primary-custom">
            <i class="fa-solid fa-edit"></i> Edit Details
        </a>
        @endif
        <a href="{{ route('broker-commissions.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
