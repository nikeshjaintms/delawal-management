@extends('admin.layouts.app')
@section('title', $firm->firm_name . ' — Firm Details')
@section('page-title','Firm Management')

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
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important; }

.detail-card, .card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 26px 30px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1px; color: #60A5FA !important; margin-bottom: 20px;
    padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; align-items: center; gap: 10px;
}
.section-heading i { color: #3B82F6; font-size: 14px; }

.detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.detail-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 768px) { .detail-grid, .detail-grid-2 { grid-template-columns: 1fr 1fr; } }
@media (max-width: 480px) { .detail-grid, .detail-grid-2 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important; border-radius: 14px;
    transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.35) !important; background: rgba(16, 22, 34, 0.85) !important; }

.detail-label {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.8px; color: #94A3B8 !important; margin-bottom: 6px;
    display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.firm-logo-lg { width: 90px; height: 90px; object-fit: cover; border-radius: 16px; border: 2px solid rgba(255, 255, 255, 0.15); box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
.firm-logo-placeholder { width: 90px; height: 90px; border-radius: 16px; border: 2px solid rgba(59, 130, 246, 0.40); background: rgba(59, 130, 246, 0.15); display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: 800; color: #60A5FA; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $firm->firm_name }}</h2>
        <p>Firm profile and GST details overview.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('firm-master.edit', $firm) }}" class="btn-primary-custom"><i class="fa fa-edit"></i> Edit</a>
        <a href="{{ route('firm-master.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="detail-card" style="display:flex;align-items:center;gap:24px;flex-wrap:wrap">
    @if($firm->firm_logo)
        <img src="{{ Storage::url($firm->firm_logo) }}" class="firm-logo-lg" alt="Logo">
    @else
        <div class="firm-logo-placeholder">{{ strtoupper(substr($firm->firm_name,0,1)) }}</div>
    @endif
    <div>
        <div style="font-size:24px;font-weight:800;color:#FFFFFF;letter-spacing:-0.3px">{{ $firm->firm_name }}</div>
        @if($firm->owner_name)
        <div style="font-size:14.5px;color:#CBD5E1;margin-top:6px;font-weight:500"><i class="fa-solid fa-user" style="color:#60A5FA;margin-right:8px"></i>{{ $firm->owner_name }}</div>
        @endif
        <div style="margin-top:10px"><span class="badge badge-{{ $firm->status }}"><i class="fa-solid fa-circle-dot"></i> {{ ucfirst($firm->status) }}</span></div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-circle-info"></i> Basic Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-envelope"></i> Email</div>
            <div class="detail-value">{{ $firm->email ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile</div>
            <div class="detail-value">{{ $firm->mobile ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-phone-volume"></i> Alternate Mobile</div>
            <div class="detail-value">{{ $firm->alternate_mobile ?? '—' }}</div>
        </div>
        <div class="detail-item" style="grid-column:1/-1">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Address</div>
            <div class="detail-value" style="font-weight:500">{{ $firm->address ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-city"></i> City</div>
            <div class="detail-value">{{ $firm->city ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-map"></i> State</div>
            <div class="detail-value">{{ $firm->state ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-map-pin"></i> Pincode</div>
            <div class="detail-value">{{ $firm->pincode ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> GST &amp; Tax Details</div>
    <div class="detail-grid-2">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> GST Number</div>
            <div class="detail-value">{{ $firm->gst_no ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-id-card"></i> PAN Number</div>
            <div class="detail-value">{{ $firm->pan_number ?? '—' }}</div>
        </div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-landmark"></i> Bank Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-columns"></i> Bank Name</div>
            <div class="detail-value">{{ $firm->bank_name ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Account Number</div>
            <div class="detail-value">{{ $firm->account_number ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-barcode"></i> IFSC Code</div>
            <div class="detail-value">{{ $firm->ifsc_code ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-code-branch"></i> Branch Name</div>
            <div class="detail-value">{{ $firm->branch_name ?? '—' }}</div>
        </div>
    </div>
</div>
@endsection
