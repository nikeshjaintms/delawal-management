@php
    $footerNote = $note ?? 'Confidential & Proprietary • Delawala Management ERP';
    $footerDate = $date ?? now()->format('d M Y, h:i A');
    $skipSignature = $skipSignature ?? false;
    $activeFirm = getActiveFirm($firm ?? ($reportFirm ?? null));
    $firmDisplayName = $activeFirm->firm_name ?? 'COMPANY';
    $firmCity = $activeFirm->city ?? 'Bharuch';
    $firmGst = !empty($activeFirm->gst_no) ? (' &bull; GSTIN: ' . $activeFirm->gst_no) : '';
    $hideFooterNote = $hideFooterNote ?? false;
    $hideFooterFirm = $hideFooterFirm ?? false;
@endphp

@if(!$skipSignature && (!isset($hasCustomAuthBlock) || !$hasCustomAuthBlock))
    @include('admin.components.pdf-signature', ['firm' => $activeFirm])
@endif

<div class="delawala-pdf-footer">
    <div class="dpf-left">
        @if(!empty($footerNote) && !$hideFooterNote)
            <span>{{ $footerNote }}</span>
        @endif
    </div>
    <div class="dpf-center">
        @if(!$hideFooterFirm)
            <span>{{ strtoupper($firmDisplayName) }}{!! $firmGst !!} &bull; {{ $firmCity }}</span>
        @endif
    </div>
    <div class="dpf-right">
        <span>Generated: {{ $footerDate }}</span>
    </div>
</div>
</div><!-- end #delawala-printable-area -->

<style>
    .delawala-pdf-footer {
        margin-top: 20px;
        padding-top: 6px;
        border-top: 1.5px solid #000000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #333333;
        font-size: 8px;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        line-height: 1.4;
        page-break-inside: avoid;
    }
    .delawala-pdf-footer .dpf-left {
        display: flex;
        align-items: center;
        font-weight: 700;
        color: #000000;
    }
    .delawala-pdf-footer .dpf-center {
        text-align: center;
        color: #333333;
        font-size: 8px;
        font-weight: 600;
    }
    .delawala-pdf-footer .dpf-right {
        text-align: right;
        color: #333333;
        font-weight: 600;
    }

    @media print {
        .delawala-pdf-footer {
            border-top: 1.5px solid #000000 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .delawala-pdf-footer .dpf-left,
        .delawala-pdf-footer .dpf-center,
        .delawala-pdf-footer .dpf-right {
            color: #000000 !important;
        }
    }
</style>
