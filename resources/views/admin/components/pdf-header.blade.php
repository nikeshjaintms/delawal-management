@php
    $docTitle       = $title ?? ($pdfTitle ?? 'Official Report');
    $docSubtitle    = $subtitle ?? null;
    $docDate        = $date ?? now()->format('d M Y, h:i A');
    $isBw           = $isBw ?? true;
    $activeFirm     = $firm ?? ($reportFirm ?? (session('login_type') === 'firm' && session('firm_id') ? \App\Models\Firm::find(session('firm_id')) : \App\Models\Firm::first()));
    
    $firmDisplayName = $activeFirm->firm_name ?? 'DELAWALA PROPERTIES';
    $firmGstNo       = $activeFirm->gst_no ?? '24CUBPD0770R1ZI';
    $firmAddress     = $activeFirm->address ?? 'Ground Floor, FF Sri No. 116, Mhan Plaza, Dattlegam Road, Dahegam, Dahegam, Bharuch';
    $firmCity        = $activeFirm->city ?? 'Dahegam, Bharuch';
    $firmState       = $activeFirm->state ?? 'Gujarat';
    $firmPin         = $activeFirm->pincode ?? '392012';
    $firmOwner       = $activeFirm->owner_name ?? 'DELAWALA ZAFAR';
    $firmPhone       = $activeFirm->mobile ?? '9999999999';
@endphp

<div id="delawala-printable-area" class="delawala-printable-area">
<div class="delawala-pdf-header">
    <div class="dph-left">
        <div class="dph-logo-wrap">
            @if(file_exists(public_path('images/pdf-logo.png')))
                <img src="{{ asset('images/pdf-logo.png') }}?v={{ filemtime(public_path('images/pdf-logo.png')) }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
            @endif
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
                <span class="dph-info-badge"><strong>State:</strong> {{ $firmState }} (24)</span>
            </div>
            <div class="dph-badges-row" style="margin-top: 3px;">
                <span class="dph-info-badge"><strong>Proprietor:</strong> {{ $firmOwner }}</span>
                @if($firmPhone)
                    <span class="dph-info-badge"><strong>Tel:</strong> {{ $firmPhone }}</span>
                @endif
            </div>
        </div>
    </div>
    <div class="dph-right">
        <div class="dph-doc-title">{{ $docTitle }}</div>
        @if($docSubtitle)
            <div class="dph-doc-sub">{{ $docSubtitle }}</div>
        @endif
        <div class="dph-meta-item"><span class="dph-meta-lbl">Generated:</span> <strong>{{ $docDate }}</strong></div>
        @if(isset($docRef))
            <div class="dph-meta-item"><span class="dph-meta-lbl">Ref No:</span> <strong>{{ $docRef }}</strong></div>
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
        border-bottom: 1.5px solid #0F172A;
        gap: 16px;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        width: 100%;
        box-sizing: border-box;
        background: #FFFFFF;
    }
    .dph-left {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1.2;
        min-width: 0;
    }
    .dph-logo-wrap {
        flex-shrink: 0;
        background: #FFFFFF;
    }
    .dph-logo {
        height: 76px;
        max-height: 80px;
        width: auto;
        object-fit: contain;
        display: block;
    }
    .dph-company-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }
    .dph-company-name {
        font-size: 18px;
        font-weight: 800;
        color: #0F172A;
        letter-spacing: 0.3px;
        line-height: 1.15;
        text-transform: uppercase;
    }
    .dph-trade-sub {
        font-size: 9px;
        color: #334155;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 1px;
        margin-bottom: 2px;
    }
    .dph-address-line {
        font-size: 8.5px;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 4px;
        line-height: 1.35;
    }
    .dph-address-line i {
        color: #0F172A;
        font-size: 8px;
    }
    .dph-badges-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 3px;
    }
    .dph-gst-badge, .dph-info-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #FFFFFF;
        color: #1E293B;
        border: 1px solid #CBD5E1;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 8.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .dph-gst-badge strong, .dph-info-badge strong {
        font-weight: 700;
        color: #0F172A;
    }
    .dph-right {
        text-align: right;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 2px;
        max-width: 44%;
        min-width: 0;
        padding-right: 4px;
        box-sizing: border-box;
    }
    .dph-doc-title {
        font-size: 16px;
        font-weight: 900;
        color: #0F172A;
        letter-spacing: 0.3px;
        line-height: 1.15;
        text-transform: uppercase;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .dph-doc-sub {
        font-size: 8.5px;
        font-weight: 600;
        color: #64748B;
        margin-bottom: 3px;
        word-break: break-word;
    }
    .dph-meta-item {
        font-size: 9px;
        color: #1E293B;
        line-height: 1.35;
        white-space: nowrap;
    }
    .dph-meta-lbl {
        color: #475569;
        font-weight: 600;
    }
    .dph-meta-item strong {
        color: #0F172A;
        font-weight: 700;
    }

    @media print {
        .delawala-pdf-header {
            border-bottom: 1.5px solid #000000 !important;
            background: #FFFFFF !important;
        }
        .dph-gst-badge, .dph-info-badge {
            background: #FFFFFF !important;
            border: 1px solid #000000 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
