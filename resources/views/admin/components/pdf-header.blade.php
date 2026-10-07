@php
    $docTitle       = $title ?? ($pdfTitle ?? 'Official Report');
    $docSubtitle    = $subtitle ?? null;
    $docDate        = $date ?? now()->format('d M Y, h:i A');
    $isBw           = $isBw ?? false;
    $activeFirm     = $firm ?? ($reportFirm ?? (session('login_type') === 'firm' && session('firm_id') ? \App\Models\Firm::find(session('firm_id')) : \App\Models\Firm::first()));
    
    $firmDisplayName = $activeFirm->firm_name ?? 'DELAWALA PROPERTIES';
    $firmGstNo       = $activeFirm->gst_no ?? '24CUBPD0770R1ZI';
    $firmAddress     = $activeFirm->address ?? 'Ground Floor, F F SH No. 116, Aman Plazza, Dahegam Road, Dahegam';
    $firmCity        = $activeFirm->city ?? 'Dahegam, Bharuch';
    $firmState       = $activeFirm->state ?? 'Gujarat';
    $firmPin         = $activeFirm->pincode ?? '392012';
    $firmOwner       = $activeFirm->owner_name ?? 'Delawala Zafar';
    $firmPhone       = $activeFirm->mobile ?? null;
@endphp

<div class="delawala-pdf-header {{ $isBw ? 'pdf-header-bw' : '' }}">
    <div class="dph-left">
        <div class="dph-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
        </div>
        <div class="dph-company-details">
            <div class="dph-company-name">{{ $firmDisplayName }}</div>
            <div class="dph-trade-sub">Delawala Infra Co. &bull; Real Estate &amp; Infrastructure</div>
            <div class="dph-address-line">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $firmAddress }}, {{ $firmCity }}, {{ $firmState }} - {{ $firmPin }}</span>
            </div>
            <div class="dph-badges-row">
                <span class="dph-gst-badge"><strong>GSTIN:</strong> {{ $firmGstNo }}</span>
                <span class="dph-info-badge"><strong>Proprietor:</strong> {{ $firmOwner }}</span>
                @if($firmPhone)
                    <span class="dph-info-badge"><i class="fa-solid fa-phone" style="font-size:8px;"></i> {{ $firmPhone }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="dph-right">
        <div class="dph-doc-title">{{ $docTitle }}</div>
        @if($docSubtitle)
            <div class="dph-doc-sub">{{ $docSubtitle }}</div>
        @endif
        <div class="dph-meta-item"><strong>Generated:</strong> {{ $docDate }}</div>
        @if(isset($docRef))
            <div class="dph-meta-item"><strong>Ref No:</strong> {{ $docRef }}</div>
        @endif
    </div>
</div>

<style>
    .delawala-pdf-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 2px solid #D97706;
        gap: 14px;
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .delawala-pdf-header.pdf-header-bw {
        border-bottom: 2px solid #000000 !important;
    }
    .pdf-header-bw .dph-trade-sub {
        color: #333333 !important;
    }
    .pdf-header-bw .dph-address-line i {
        color: #000000 !important;
    }
    .pdf-header-bw .dph-gst-badge {
        background: #FFFFFF !important;
        border: 1.5px solid #000000 !important;
        color: #000000 !important;
        font-weight: 800 !important;
    }
    .pdf-header-bw .dph-info-badge {
        background: #F8FAFC !important;
        border: 1px solid #000000 !important;
        color: #000000 !important;
    }
    .pdf-header-bw .dph-doc-sub {
        color: #333333 !important;
    }
    .dph-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
    }
    .dph-logo-wrap {
        flex-shrink: 0;
    }
    .dph-logo {
        height: 54px;
        width: auto;
        object-fit: contain;
    }
    .dph-company-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .dph-company-name {
        font-size: 20px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.3px;
        line-height: 1.1;
        text-transform: uppercase;
    }
    .dph-trade-sub {
        font-size: 9.5px;
        color: #D97706;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        margin-bottom: 2px;
    }
    .dph-address-line {
        font-size: 10px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
        line-height: 1.3;
    }
    .dph-address-line i {
        color: #D97706;
        font-size: 9px;
    }
    .dph-badges-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }
    .dph-gst-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FCD34D;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .dph-info-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #F1F5F9;
        color: #334155;
        border: 1px solid #CBD5E1;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 9px;
    }
    .dph-right {
        text-align: right;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
    }
    .dph-doc-title {
        font-size: 16px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.2px;
        line-height: 1.2;
    }
    .dph-doc-sub {
        font-size: 11px;
        font-weight: 600;
        color: #2563EB;
        margin-bottom: 2px;
    }
    .dph-meta-item {
        font-size: 10px;
        color: #64748B;
        line-height: 1.3;
    }
    .dph-meta-item strong {
        color: #334155;
    }

    @media print {
        .delawala-pdf-header {
            border-bottom-color: #000 !important;
        }
        .dph-gst-badge {
            background: #fff !important;
            border: 1px solid #000 !important;
            color: #000 !important;
        }
    }
</style>
