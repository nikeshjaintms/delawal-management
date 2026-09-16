@extends('admin.layouts.app')
@section('title', 'Seller Profile: ' . $seller->name)
@section('page-title', 'Seller Profile')
@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.profile-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 28px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 26px;
}

.seller-hero {
    display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
    padding-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); margin-bottom: 24px;
}
.seller-avatar {
    width: 64px; height: 64px; border-radius: 18px;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.25) 0%, rgba(217, 119, 6, 0.35) 100%);
    border: 1.5px solid rgba(245, 158, 11, 0.45); color: #FBBF24;
    display: flex; align-items: center; justify-content: center; font-size: 28px;
}
.seller-hero-info h1 { font-size: 22px; font-weight: 800; color: #FFFFFF; margin: 0 0 4px 0; }
.seller-hero-info p { margin: 0; color: #94A3B8; font-size: 13.5px; }

.info-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
.info-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 20px; }
@media (max-width: 900px) { .info-grid-4 { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px) { .info-grid-4, .info-grid-3 { grid-template-columns: 1fr; } }

.info-box {
    background: rgba(16, 22, 34, 0.55); border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px; padding: 14px 16px;
}
.info-box-label { font-size: 11.5px; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 4px; }
.info-box-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF; }

.bank-highlight-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.10) 0%, rgba(5, 150, 105, 0.04) 100%);
    border: 1.5px solid rgba(16, 185, 129, 0.30); border-radius: 16px; padding: 20px; margin-bottom: 26px;
}

.btn-custom {
    padding: 9px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;
    text-decoration: none !important; display: inline-flex; align-items: center; gap: 6px;
    border: 1px solid transparent; cursor: pointer; transition: all .2s ease;
}
.btn-custom.gold { background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: #FFF; border-color: rgba(255,255,255,0.2); }
.btn-custom.outline { background: rgba(255, 255, 255, 0.08); color: #FFF; border-color: rgba(255, 255, 255, 0.15); }
.btn-custom:hover { transform: translateY(-2px); }

.table-responsive { overflow-x: auto; }
.premium-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.premium-table th {
    background: rgba(15, 23, 42, 0.85); color: #94A3B8;
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.8px; padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.12);
}
.premium-table td {
    padding: 12px 14px; font-size: 13.5px; color: #E2E8F0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07); vertical-align: middle;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Seller Dossier: {{ $seller->name }}</h2>
        <p>Complete landowner profile, bank accounts, and linked land acquisitions.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('sellers.detail-pdf', $seller->id) }}" target="_blank" class="btn-custom outline"><i class="fa-solid fa-file-pdf"></i> Download Dossier PDF</a>
        <a href="{{ route('sellers.edit', $seller->id) }}" class="btn-custom gold"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</a>
        <a href="{{ route('sellers.index') }}" class="btn-custom outline"><i class="fa-solid fa-arrow-left"></i> All Sellers</a>
    </div>
</div>

<div class="profile-card">
    <div class="seller-hero">
        <div class="seller-avatar">
            <i class="fa-solid fa-user-tag"></i>
        </div>
        <div class="seller-hero-info" style="flex: 1;">
            <h1>{{ $seller->name }}</h1>
            <p>
                <span style="color: #FBBF24; font-weight: 700;">{{ ucfirst(str_replace('_', ' ', $seller->seller_type ?? 'Individual')) }}</span>
                @if($seller->city) &bull; <i class="fa-solid fa-location-dot" style="font-size: 11px;"></i> {{ $seller->city }}{{ $seller->state ? ', ' . $seller->state : '' }} @endif
                @if($seller->firm) &bull; <i class="fa-solid fa-building" style="font-size: 11px;"></i> {{ $seller->firm->firm_name }} @endif
            </p>
        </div>
        <div>
            <span style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; {{ $seller->status === 'active' ? 'background: rgba(16, 185, 129, 0.2); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.4);' : 'background: rgba(239, 68, 68, 0.2); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.4);' }}">
                {{ $seller->status === 'active' ? '● Active Landowner' : '● Inactive' }}
            </span>
        </div>
    </div>

    <!-- Contact & Legal Info -->
    <div class="info-grid-4">
        <div class="info-box">
            <div class="info-box-label"><i class="fa-solid fa-phone"></i> Mobile</div>
            <div class="info-box-value">{{ $seller->mobile ?: ($seller->phone ?: '—') }}</div>
        </div>
        <div class="info-box">
            <div class="info-box-label"><i class="fa-solid fa-envelope"></i> Email</div>
            <div class="info-box-value">{{ $seller->email ?: '—' }}</div>
        </div>
        <div class="info-box">
            <div class="info-box-label"><i class="fa-solid fa-id-card"></i> PAN Card</div>
            <div class="info-box-value" style="font-family: monospace; color: #FCD34D;">{{ $seller->pan_no ?: '—' }}</div>
        </div>
        <div class="info-box">
            <div class="info-box-label"><i class="fa-solid fa-fingerprint"></i> Aadhaar No</div>
            <div class="info-box-value" style="font-family: monospace;">{{ $seller->aadhaar_no ?: '—' }}</div>
        </div>
    </div>

    @if($seller->address || $seller->remarks)
        <div class="info-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div class="info-box">
                <div class="info-box-label"><i class="fa-solid fa-map-pin"></i> Full Address</div>
                <div class="info-box-value" style="font-size: 13.5px; font-weight: 500; color: #CBD5E1;">{{ $seller->address ?: '—' }}</div>
            </div>
            <div class="info-box">
                <div class="info-box-label"><i class="fa-solid fa-note-sticky"></i> Remarks / Title Notes</div>
                <div class="info-box-value" style="font-size: 13.5px; font-weight: 500; color: #CBD5E1;">{{ $seller->remarks ?: '—' }}</div>
            </div>
        </div>
    @endif

    <!-- Bank Details Box -->
    <div class="bank-highlight-box">
        <div style="font-size: 13px; font-weight: 800; color: #34D399; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-building-columns"></i> Registered Bank Account
        </div>
        <div class="info-grid-4" style="margin-bottom: 0;">
            <div>
                <div class="info-box-label">Bank Name</div>
                <div style="font-weight: 700; color: #FFFFFF; font-size: 15px;">{{ $seller->bank_name ?: 'Not Provided' }}</div>
            </div>
            <div>
                <div class="info-box-label">Account Number</div>
                <div style="font-family: monospace; font-weight: 700; color: #34D399; font-size: 15px;">{{ $seller->account_number ?: '—' }}</div>
            </div>
            <div>
                <div class="info-box-label">IFSC Code</div>
                <div style="font-family: monospace; font-weight: 700; color: #FCD34D; font-size: 15px;">{{ $seller->ifsc_code ?: '—' }}</div>
            </div>
            <div>
                <div class="info-box-label">Branch</div>
                <div style="font-weight: 600; color: #E2E8F0; font-size: 14px;">{{ $seller->branch_name ?: '—' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Linked Land Acquisitions -->
<div class="profile-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
        <h3 style="font-size: 17px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-map-location-dot" style="color: #60A5FA;"></i> Linked Property Acquisitions
        </h3>
        <span style="font-size: 12.5px; color: #94A3B8; font-weight: 600;">Total Deals: {{ $seller->propertyMasters->count() }}</span>
    </div>

    <div class="table-responsive">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Property Master</th>
                    <th>Code</th>
                    <th>Acquisition Date</th>
                    <th>Total Land Area</th>
                    <th>Purchase Deal (₹)</th>
                    <th>Paid (₹)</th>
                    <th>Due (₹)</th>
                    <th>Payment Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($seller->propertyMasters as $pm)
                    <tr>
                        <td>
                            <strong style="color: #FFFFFF; font-size: 14px;">{{ $pm->property_name }}</strong>
                            @if($pm->city || $pm->location)
                                <div style="font-size: 11.5px; color: #94A3B8;"><i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> {{ $pm->location ?: $pm->city }}</div>
                            @endif
                        </td>
                        <td><span style="font-family: monospace; color: #CBD5E1;">{{ $pm->property_code }}</span></td>
                        <td>{{ $pm->purchase_date ? date('d M, Y', strtotime($pm->purchase_date)) : '—' }}</td>
                        <td>
                            <strong style="color: #34D399;">{{ number_format($pm->total_area, 2) }}</strong>
                            <small style="color: #94A3B8;">{{ $pm->area_unit ?? 'Sq.Ft' }}</small>
                        </td>
                        <td><strong style="color: #60A5FA;">₹{{ number_format($pm->purchase_price, 2) }}</strong></td>
                        <td><span style="color: #34D399; font-weight: 700;">₹{{ number_format($pm->paid_amount, 2) }}</span></td>
                        <td><span style="color: {{ $pm->due_amount > 0 ? '#F87171' : '#94A3B8' }}; font-weight: 700;">₹{{ number_format($pm->due_amount, 2) }}</span></td>
                        <td>
                            @php $pStatus = strtolower($pm->payment_status ?? 'unpaid'); @endphp
                            @if($pStatus === 'paid')
                                <span style="background: rgba(16, 185, 129, 0.18); color: #34D399; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Full Paid</span>
                            @elseif($pStatus === 'partial')
                                <span style="background: rgba(245, 158, 11, 0.18); color: #FBBF24; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Partial</span>
                            @else
                                <span style="background: rgba(239, 68, 68, 0.18); color: #F87171; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Unpaid</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('property-masters.show', $pm->id) }}" class="btn-custom outline" style="padding: 5px 12px; font-size: 12px;">
                                <i class="fa-solid fa-eye"></i> View Master
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 24px; color: #94A3B8;">
                            No Property Master acquisitions linked to this seller yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
