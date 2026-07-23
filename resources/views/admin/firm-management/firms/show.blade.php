@extends('admin.layouts.app')
@section('title', $firm->firm_name . ' — Firm Details')
@section('page-title','Firm Management')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-primary-custom i,.btn-secondary-custom i{font-size:14px;line-height:1}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.header-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.detail-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.detail-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
@media(max-width:768px){.detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}
.detail-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:5px}
.detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary)}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.firm-logo-lg{width:100px;height:100px;object-fit:cover;border-radius:12px;border:1px solid var(--border-color);box-shadow:var(--soft-shadow)}
.firm-logo-placeholder{width:100px;height:100px;border-radius:12px;border:1px solid var(--border-color);background:var(--blue-light);display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:800;color:var(--blue)}
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

<div class="detail-card" style="display:flex;align-items:center;gap:28px;flex-wrap:wrap">
    @if($firm->firm_logo)
        <img src="{{ Storage::url($firm->firm_logo) }}" class="firm-logo-lg" alt="Logo">
    @else
        <div class="firm-logo-placeholder">{{ strtoupper(substr($firm->firm_name,0,1)) }}</div>
    @endif
    <div>
        <div style="font-size:22px;font-weight:800;color:var(--text-primary)">{{ $firm->firm_name }}</div>
        @if($firm->owner_name)
        <div style="font-size:14px;color:var(--text-secondary);margin-top:4px"><i class="fa-solid fa-user" style="margin-right:6px"></i>{{ $firm->owner_name }}</div>
        @endif
        <div style="margin-top:8px"><span class="badge badge-{{ $firm->status }}">{{ ucfirst($firm->status) }}</span></div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-circle-info"></i> Basic Information</div>
    <div class="detail-grid">
        <div><div class="detail-label">Email</div><div class="detail-value">{{ $firm->email ?? '—' }}</div></div>
        <div><div class="detail-label">Mobile</div><div class="detail-value">{{ $firm->mobile ?? '—' }}</div></div>
        <div><div class="detail-label">Alternate Mobile</div><div class="detail-value">{{ $firm->alternate_mobile ?? '—' }}</div></div>
        <div style="grid-column:1/-1"><div class="detail-label">Address</div><div class="detail-value" style="font-weight:400">{{ $firm->address ?? '—' }}</div></div>
        <div><div class="detail-label">City</div><div class="detail-value">{{ $firm->city ?? '—' }}</div></div>
        <div><div class="detail-label">State</div><div class="detail-value">{{ $firm->state ?? '—' }}</div></div>
        <div><div class="detail-label">Pincode</div><div class="detail-value">{{ $firm->pincode ?? '—' }}</div></div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> GST &amp; Tax Details</div>
    <div class="detail-grid">
        <div><div class="detail-label">GST Number</div><div class="detail-value">{{ $firm->gst_no ?? '—' }}</div></div>
        <div><div class="detail-label">PAN Number</div><div class="detail-value">{{ $firm->pan_number ?? '—' }}</div></div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-landmark"></i> Bank Details</div>
    <div class="detail-grid">
        <div><div class="detail-label">Bank Name</div><div class="detail-value">{{ $firm->bank_name ?? '—' }}</div></div>
        <div><div class="detail-label">Account Number</div><div class="detail-value">{{ $firm->account_number ?? '—' }}</div></div>
        <div><div class="detail-label">IFSC Code</div><div class="detail-value">{{ $firm->ifsc_code ?? '—' }}</div></div>
        <div><div class="detail-label">Branch Name</div><div class="detail-value">{{ $firm->branch_name ?? '—' }}</div></div>
    </div>
</div>
@endsection
