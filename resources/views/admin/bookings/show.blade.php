@extends('admin.layouts.app')
@section('title', 'Booking Details')
@section('page-title', 'Booking Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:860px;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:24px;}
    @media(max-width:576px){.detail-grid{grid-template-columns:1fr;}}
    .detail-item{border-bottom:1px solid #F1F5F9;padding-bottom:12px;}
    .detail-label{font-size:12px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;margin-bottom:5px;}
    .detail-value{font-size:15px;font-weight:700;color:var(--text-primary);}
    .badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-pending{background:rgba(245,158,11,0.1);color:#B45309;}
    .badge-confirmed{background:rgba(34,197,94,0.1);color:#16803D;}
    .badge-cancelled{background:rgba(239,68,68,0.1);color:#B91C1C;}
    .badge-unpaid{background:rgba(239,68,68,0.08);color:#B91C1C;}
    .badge-partial{background:rgba(245,158,11,0.1);color:#B45309;}
    .badge-paid{background:rgba(34,197,94,0.1);color:#16803D;}
    .form-actions{display:flex;gap:12px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-gold:hover{background-color:#B58D1B;}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Booking Details</h2>
        <p>Complete booking information for this record.</p>
    </div>
</div>

<div class="card-box">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label">Property</div>
            <div class="detail-value">{{ $booking->property->property_name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Customer</div>
            <div class="detail-value">{{ $booking->customer->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Broker</div>
            <div class="detail-value">{{ $booking->broker->name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Booking Date</div>
            <div class="detail-value">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Booking Amount</div>
            <div class="detail-value">{{ $booking->booking_amount ? '₹'.number_format($booking->booking_amount,2) : '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Agreement Date</div>
            <div class="detail-value">{{ $booking->agreement_date ? \Carbon\Carbon::parse($booking->agreement_date)->format('d M Y') : '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Booking Status</div>
            <div class="detail-value"><span class="badge badge-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Payment Status</div>
            <div class="detail-value"><span class="badge badge-{{ $booking->payment_status }}">{{ ucfirst($booking->payment_status) }}</span></div>
        </div>
        <div class="detail-item" style="grid-column:span 2;">
            <div class="detail-label">Remarks</div>
            <div class="detail-value" style="font-weight:500;">{{ $booking->remarks ?: '-' }}</div>
        </div>
    </div>
    <div class="form-actions">
        <a href="{{ route('bookings.edit', $booking->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit Booking</a>
        <a href="{{ route('bookings.index') }}" class="btn-outline">Back to List</a>
    </div>
</div>
@endsection
