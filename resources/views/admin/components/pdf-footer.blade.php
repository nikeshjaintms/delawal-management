@php
    $footerNote = $note ?? 'Confidential & Proprietary • Delawala Management ERP';
    $footerDate = $date ?? now()->format('d M Y, h:i A');
@endphp

<div class="delawala-pdf-footer">
    <div class="dpf-left">
        <i class="fa-solid fa-shield-halved" style="color: #D97706; margin-right: 4px;"></i>
        <span>{{ $footerNote }}</span>
    </div>
    <div class="dpf-center">
        <span>DELAWALA INFRA CO. &bull; GSTIN: 24CUBPD0770R1ZI &bull; Dahegam, Bharuch</span>
    </div>
    <div class="dpf-right">
        <span>Generated: {{ $footerDate }}</span>
    </div>
</div>

<style>
    .delawala-pdf-footer {
        margin-top: 24px;
        padding-top: 10px;
        border-top: 1px solid #E2E8F0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #94A3B8;
        font-size: 9px;
        font-family: 'Segoe UI', Arial, sans-serif;
        line-height: 1.4;
    }
    .delawala-pdf-footer .dpf-left {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: #64748B;
    }
    .delawala-pdf-footer .dpf-center {
        text-align: center;
        color: #94A3B8;
        font-size: 8.5px;
    }
    .delawala-pdf-footer .dpf-right {
        text-align: right;
        color: #64748B;
        font-weight: 500;
    }

    @media print {
        .delawala-pdf-footer {
            border-top: 1px solid #000 !important;
            color: #555 !important;
        }
    }
</style>
