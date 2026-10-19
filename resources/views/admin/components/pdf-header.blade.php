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
            @if(file_exists(public_path('images/logo 1.png')))
                <img src="{{ asset('images/logo 1.png') }}?v={{ filemtime(public_path('images/logo 1.png')) }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
            @elseif(file_exists(public_path('images/pdf-logo.png')))
                <img src="{{ asset('images/pdf-logo.png') }}?v={{ filemtime(public_path('images/pdf-logo.png')) }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
            @elseif(file_exists(public_path('assets/logos/logo 1.png')))
                <img src="{{ asset('assets/logos/logo 1.png') }}?v={{ filemtime(public_path('assets/logos/logo 1.png')) }}" alt="{{ $firmDisplayName }}" class="dph-logo" onerror="this.style.display='none';">
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
        @if(isset($period) && !empty($period))
            <div class="dph-meta-item"><span class="dph-meta-lbl">Period:</span> <strong>{{ $period }}</strong></div>
        @elseif(request('from_date') || request('to_date'))
            <div class="dph-meta-item">
                <span class="dph-meta-lbl">Period:</span>
                <strong>
                    {{ request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'Beginning' }}
                    to
                    {{ request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Present' }}
                </strong>
            </div>
        @elseif(request('as_on_date'))
            <div class="dph-meta-item"><span class="dph-meta-lbl">As On:</span> <strong>{{ \Carbon\Carbon::parse(request('as_on_date'))->format('d M Y') }}</strong></div>
        @endif
        @if(isset($docRef))
            <div class="dph-meta-item"><span class="dph-meta-lbl">Ref No:</span> <strong>{{ $docRef }}</strong></div>
        @endif
    </div>
</div>

{{-- ── Audit / Filter Criteria Chips ── --}}
@php
    $chips = [];
    if (isset($filters) && is_array($filters)) {
        $chips = $filters;
    } else {
        if (request('from_date')) $chips['From Date'] = \Carbon\Carbon::parse(request('from_date'))->format('d M Y');
        if (request('to_date')) $chips['To Date'] = \Carbon\Carbon::parse(request('to_date'))->format('d M Y');
        if (request('as_on_date')) $chips['As On Date'] = \Carbon\Carbon::parse(request('as_on_date'))->format('d M Y');
        if (request('payment_type')) $chips['Payment Type'] = ucfirst(request('payment_type'));
        if (request('filter_status')) $chips['Status'] = ucfirst(request('filter_status'));
        if (request('filter_mode')) $chips['Payment Mode'] = request('filter_mode');
    }
@endphp
@if(count($chips) > 0)
<div class="filter-criteria-bar">
    <span style="font-weight:700; color:#000000;"><i class="fa-solid fa-filter" style="font-size:7.5px; color:#000000;"></i> Filter Criteria:</span>
    @foreach($chips as $lbl => $val)
        <span class="filter-chip">{{ $lbl }}: <strong>{{ $val }}</strong></span>
    @endforeach
</div>
@endif

<style>
    .delawala-pdf-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-bottom: 12px;
        margin-bottom: 14px;
        border-bottom: 2px solid #000000;
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
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dph-logo {
        height: 72px;
        max-height: 74px;
        max-width: 135px;
        width: auto;
        object-fit: contain;
        display: block;
        filter: none !important;
        -webkit-filter: none !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
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
        color: #000000;
        letter-spacing: 0.3px;
        line-height: 1.15;
        text-transform: uppercase;
    }
    .dph-trade-sub {
        font-size: 9px;
        color: #333333;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 1px;
        margin-bottom: 2px;
    }
    .dph-address-line {
        font-size: 8.5px;
        color: #444444;
        display: flex;
        align-items: center;
        gap: 4px;
        line-height: 1.35;
    }
    .dph-address-line i {
        color: #000000;
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
        color: #000000;
        border: 1px solid #000000;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 8.5px;
        font-weight: 600;
        white-space: nowrap;
    }
    .dph-gst-badge strong, .dph-info-badge strong {
        font-weight: 700;
        color: #000000;
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
        color: #000000;
        letter-spacing: 0.3px;
        line-height: 1.15;
        text-transform: uppercase;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .dph-doc-sub {
        font-size: 8.5px;
        font-weight: 600;
        color: #444444;
        margin-bottom: 3px;
        word-break: break-word;
    }
    .dph-meta-item {
        font-size: 9px;
        color: #000000;
        line-height: 1.35;
        white-space: nowrap;
    }
    .dph-meta-lbl {
        color: #444444;
        font-weight: 600;
    }
    .dph-meta-item strong {
        color: #000000;
        font-weight: 700;
    }

    @media print {
        .delawala-pdf-header {
            border-bottom: 2px solid #000000 !important;
            background: #FFFFFF !important;
        }
        .dph-logo {
            max-height: 70px !important;
            max-width: 135px !important;
            object-fit: contain !important;
            filter: none !important;
            -webkit-filter: none !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
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
