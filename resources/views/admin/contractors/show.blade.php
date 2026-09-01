@extends('admin.layouts.app')
@section('title', 'View Contractor')
@section('page-title', 'Contractor Details')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    max-width: 900px;
    margin: 0 auto 28px;
}

.hero-box {
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    padding-bottom: 24px; margin-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
.hero-left { display: flex; align-items: center; gap: 18px; }
.hero-icon {
    width: 64px; height: 64px; border-radius: 18px;
    background: rgba(59, 130, 246, 0.18); border: 1.5px solid rgba(59, 130, 246, 0.35);
    display: flex; align-items: center; justify-content: center; font-size: 28px; color: #60A5FA;
    flex-shrink: 0;
}
.hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.hero-info p { font-size: 14px; color: #94A3B8 !important; font-weight: 600; margin: 0; }

.section-title {
    font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin: 26px 0 16px; padding-bottom: 8px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:768px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-card {
    padding: 16px 18px; background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 14px;
    transition: all .2s ease;
}
.detail-card:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(96, 165, 250, 0.30); }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA; font-size: 12px; }
.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B; font-weight: 500; font-style: italic; }

.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; font-size: 12px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; }
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.btn-pc {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 24px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 12px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-sc {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 22px; min-height: 42px; background: rgba(255, 255, 255, 0.08) !important;
    color: #CBD5E1 !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 12px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover { background: rgba(255, 255, 255, 0.14) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.form-actions {
    display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Contractor Details</h2>
        <p>Contractor profile, identity cards, and banking information.</p>
    </div>
    <a href="{{ route('contractors.index') }}" class="btn-sc"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="card-box">
    <div class="hero-box">
        <div class="hero-left">
            <div class="hero-icon"><i class="fa-solid fa-helmet-safety"></i></div>
            <div class="hero-info">
                <h3>{{ $contractor->contractor_name }}</h3>
                <p>
                    <i class="fa-solid fa-city" style="color: #60A5FA;"></i>
                    {{ $contractor->project->project_name ?? 'No Project' }}
                </p>
            </div>
        </div>
        <div>
            <span class="badge badge-{{ $contractor->status }}">
                <i class="fa-solid fa-circle" style="font-size: 7px;"></i> {{ ucfirst($contractor->status) }}
            </span>
        </div>
    </div>

    {{-- Project Section --}}
    <div class="section-title"><i class="fa-solid fa-city"></i> Assigned Project</div>
    <div class="detail-grid">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Project Name</div>
            <div class="detail-value">
                <a href="{{ route('projects.show', $contractor->project_id) }}" style="color: #60A5FA; text-decoration: none; font-weight: 800;">
                    {{ $contractor->project->project_name ?? '—' }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                </a>
            </div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-landmark"></i> Property Master</div>
            <div class="detail-value">{{ $contractor->project->propertyMaster->property_name ?? '—' }}</div>
        </div>
    </div>

    {{-- Identity & Contact Information --}}
    <div class="section-title"><i class="fa-solid fa-id-card"></i> Identity & Contact Details</div>
    <div class="detail-grid-3">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile Number</div>
            <div class="detail-value {{ $contractor->mobile ? '' : 'empty' }}">{{ $contractor->mobile ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-address-card"></i> Aadhar Card</div>
            <div class="detail-value {{ $contractor->aadhar_no ? '' : 'empty' }}">{{ $contractor->aadhar_no ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> PAN Card</div>
            <div class="detail-value {{ $contractor->pan_no ? '' : 'empty' }}">{{ $contractor->pan_no ?: 'Not provided' }}</div>
        </div>
    </div>

    {{-- Bank Details --}}
    <div class="section-title"><i class="fa-solid fa-building-columns"></i> Bank Details</div>
    <div class="detail-grid">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-bank"></i> Bank Name</div>
            <div class="detail-value {{ $contractor->bank_name ? '' : 'empty' }}">{{ $contractor->bank_name ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Account Number</div>
            <div class="detail-value {{ $contractor->account_number ? '' : 'empty' }}" style="letter-spacing: 0.5px;">{{ $contractor->account_number ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> IFSC Code</div>
            <div class="detail-value {{ $contractor->ifsc_code ? '' : 'empty' }}">{{ $contractor->ifsc_code ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Branch / City</div>
            <div class="detail-value {{ $contractor->branch_name ? '' : 'empty' }}">{{ $contractor->branch_name ?: 'Not provided' }}</div>
        </div>
    </div>

    {{-- Address --}}
    @if($contractor->address)
    <div class="section-title"><i class="fa-solid fa-location-arrow"></i> Address</div>
    <div class="detail-card">
        <div class="detail-value">{{ $contractor->address }}</div>
    </div>
    @endif

    <div class="form-actions">
        <a href="{{ route('contractors.edit', $contractor) }}" class="btn-pc">
            <i class="fa-solid fa-pen-to-square"></i> Edit Contractor
        </a>
        <a href="{{ route('contractors.index') }}" class="btn-sc">Back to List</a>
    </div>
</div>
@endsection
