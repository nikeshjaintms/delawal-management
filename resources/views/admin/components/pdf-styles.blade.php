@php
    $orientation = $orientation ?? 'portrait';
    $isLandscape = ($orientation === 'landscape');
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* ════════════════════════════════════════════════════════════
       UNIFIED MASTER ERP BLACK & WHITE A4-OPTIMIZED PDF DESIGN SYSTEM
       Monochrome High-Contrast Print & Digital Document Architecture
    ════════════════════════════════════════════════════════════ */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    html, body {
        font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, Arial, sans-serif;
        font-size: 9px;
        color: #000000;
        background: #F1F5F9;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    body {
        padding: 0 0 30px 0;
        min-height: 100%;
        margin: 0;
        background: #F1F5F9;
    }

    /* ── Main Printable Sheet Centered Like A4 Paper ── */
    .delawala-printable-area, .invoice-print-sheet, #delawala-printable-area {
        width: 100%;
        max-width: {{ $isLandscape ? '1040px' : '820px' }};
        background: #FFFFFF !important;
        color: #000000 !important;
        margin: 20px auto;
        padding: 24px 28px;
        box-sizing: border-box;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-radius: 3px;
        border: 1px solid #D1D5DB;
    }

    /* ── Universal Monochrome Color Neutralization with !important ── */
    .delawala-printable-area,
    .invoice-print-sheet,
    #delawala-printable-area {
        color: #000000 !important;
    }

    /* Neutralize all inline text colors (green, red, blue, purple, amber, etc.) to true black */
    .delawala-printable-area [style*="color"],
    .invoice-print-sheet [style*="color"],
    #delawala-printable-area [style*="color"] {
        color: #000000 !important;
    }

    /* High-contrast Black & White filter for all company logos, photos, and digital signatures */
    .delawala-printable-area img,
    .invoice-print-sheet img,
    #delawala-printable-area img,
    .dph-logo,
    .inv-logo-img,
    #invSignImg {
        filter: grayscale(100%) contrast(120%) !important;
        -webkit-filter: grayscale(100%) contrast(120%) !important;
    }

    /* Neutralize any inline colored backgrounds to pure white/light gray */
    .delawala-printable-area [style*="background:#F0FDF4"],
    .delawala-printable-area [style*="background: #F0FDF4"],
    .delawala-printable-area [style*="background:#ECFDF5"],
    .delawala-printable-area [style*="background: #ECFDF5"],
    .delawala-printable-area [style*="background:#FEF2F2"],
    .delawala-printable-area [style*="background: #FEF2F2"] {
        background: #FFFFFF !important;
    }

    /* Neutralize all Font-Awesome icons to clean black */
    .delawala-printable-area i,
    .invoice-print-sheet i,
    #delawala-printable-area i,
    .delawala-printable-area .fa,
    .delawala-printable-area .fa-solid,
    .delawala-printable-area .fa-regular {
        color: #000000 !important;
    }

    /* Secondary / metadata labels in crisp readable dark charcoal */
    .delawala-printable-area .dph-meta-lbl,
    .delawala-printable-area .dph-address-line,
    .delawala-printable-area .info-label,
    .delawala-printable-area .inv-lbl,
    .delawala-printable-area .inv-doc-label,
    .delawala-printable-area .inv-words-bar,
    .delawala-printable-area .dpf-left,
    .delawala-printable-area .dpf-center,
    .delawala-printable-area .dpf-right,
    .delawala-printable-area .s-label,
    .delawala-printable-area .text-muted,
    .delawala-printable-area [style*="color:#64748B"],
    .delawala-printable-area [style*="color: #64748B"],
    .delawala-printable-area [style*="color:#94A3B8"],
    .delawala-printable-area [style*="color: #94A3B8"],
    .delawala-printable-area [style*="color:#475569"],
    .delawala-printable-area [style*="color: #475569"] {
        color: #333333 !important;
    }

    /* ── Exact A4 Dimensions & Print Rules ── */
    @page {
        size: A4 {{ $orientation }};
        margin: 6mm 6mm 6mm 6mm;
    }
    @media print {
        html, body {
            padding: 0 !important;
            margin: 0 !important;
            background: #FFFFFF !important;
            color: #000000 !important;
            width: 100% !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .delawala-pdf-toolbar, .no-print-bar, .no-print {
            display: none !important;
        }
        .page-break {
            page-break-before: always;
        }
        tr, .stat-box, .grid-col, .grid-2, .finance-card, .info-box, .auth-block, .bw-box, table, .inv-col-box, .inv-bank-terms-grid {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .delawala-printable-area, .invoice-print-sheet, #delawala-printable-area {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            border-radius: 0 !important;
            background: #FFFFFF !important;
            color: #000000 !important;
        }
        .delawala-printable-area *, .invoice-print-sheet *, #delawala-printable-area * {
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .delawala-printable-area img, .invoice-print-sheet img, #delawala-printable-area img {
            filter: grayscale(100%) contrast(120%) !important;
            -webkit-filter: grayscale(100%) contrast(120%) !important;
        }
    }

    /* ── KPI / Stat Summary Row ── */
    .stat-row {
        display: flex;
        gap: 10px;
        margin-bottom: 14px;
        flex-wrap: wrap;
        width: 100%;
        box-sizing: border-box;
    }
    .stat-box {
        flex: 1;
        min-width: 110px;
        border: 1.5px solid #000000 !important;
        border-radius: 4px;
        padding: 7px 11px;
        background: #FFFFFF !important;
        box-sizing: border-box;
    }
    .stat-box .s-label {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #333333 !important;
        margin-bottom: 2px;
    }
    .stat-box .s-value {
        font-size: 13.5px;
        font-weight: 800;
        color: #000000 !important;
    }
    .stat-box.s-gold, .stat-box.s-green, .stat-box.s-blue, .stat-box.s-red, .stat-box.s-purple, .stat-box.s-amber {
        border: 1.5px solid #000000 !important;
        background: #FFFFFF !important;
    }
    .stat-box.s-gold .s-value, .stat-box.s-green .s-value, .stat-box.s-blue .s-value, .stat-box.s-red .s-value, .stat-box.s-purple .s-value, .stat-box.s-amber .s-value {
        color: #000000 !important;
    }

    /* ── Section Labels ── */
    .section-label {
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #000000 !important;
        margin-top: 12px;
        margin-bottom: 6px;
        padding-bottom: 3px;
        border-bottom: 2px solid #000000 !important;
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        box-sizing: border-box;
    }
    .section-label i {
        color: #000000 !important;
    }

    /* ── Master Data Tables ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 10px;
        border: 1.5px solid #000000 !important;
        background: #FFFFFF !important;
        table-layout: auto;
        box-sizing: border-box;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
    thead tr {
        background: #F3F4F6 !important;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    thead th {
        padding: 6px 7px;
        color: #000000 !important;
        font-weight: 800;
        text-align: left;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: 1px solid #000000 !important;
        border-bottom: 2px solid #000000 !important;
    }
    thead th.c, tbody td.c { text-align: center; }
    thead th.r, tbody td.r, thead th.num, tbody td.num, tfoot td.r {
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    tbody tr {
        background: #FFFFFF !important;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    tbody tr:nth-child(even) {
        background: #FAFAFA !important;
    }
    tbody td {
        padding: 5.5px 7px;
        border: 1px solid #D1D5DB !important;
        vertical-align: top;
        color: #000000 !important;
        word-break: break-word;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    tfoot tr {
        background: #F3F4F6 !important;
        font-weight: 800;
        page-break-inside: avoid;
        break-inside: avoid;
    }
    tfoot td {
        padding: 5.5px 7px;
        border: 1px solid #000000 !important;
        border-top: 2px solid #000000 !important;
        font-size: 9px;
        color: #000000 !important;
        word-break: break-word;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }
    .double-total {
        border-top: 2px solid #000000 !important;
        border-bottom: 3.5px double #000000 !important;
        font-weight: 800 !important;
        background: #F3F4F6 !important;
    }
    .double-total td {
        border-top: 2px solid #000000 !important;
        border-bottom: 3.5px double #000000 !important;
        color: #000000 !important;
        font-weight: 800 !important;
        background: #F3F4F6 !important;
    }

    /* ── Audit / Filter Criteria Chips ── */
    .filter-criteria-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        background: #F9FAFB !important;
        border: 1px solid #000000 !important;
        border-left: 3.5px solid #000000 !important;
        padding: 5px 10px;
        border-radius: 4px;
        margin-bottom: 12px;
        font-size: 8px;
        color: #000000 !important;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #FFFFFF !important;
        border: 1px solid #000000 !important;
        border-radius: 3px;
        padding: 2px 6px;
        font-weight: 600;
        color: #000000 !important;
    }
    .filter-chip strong {
        color: #000000 !important;
    }

    /* ── Executive B&W Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 7.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: 1px solid #000000 !important;
        background: #FFFFFF !important;
        color: #000000 !important;
        white-space: nowrap;
    }
    .badge-success, .b-paid, .badge-active, .badge-confirmed,
    .badge[style*="background:#ECFDF5"], .badge[style*="background: #ECFDF5"] {
        background: #000000 !important;
        color: #FFFFFF !important;
        border: 1px solid #000000 !important;
    }
    .badge-warning, .b-partial, .badge-due {
        background: #FFFFFF !important;
        color: #000000 !important;
        border: 1px solid #000000 !important;
    }
    .badge-danger, .b-pending, .badge-inactive, .badge-cancelled,
    .badge[style*="background:#FEF2F2"], .badge[style*="background: #FEF2F2"] {
        background: #FFFFFF !important;
        color: #000000 !important;
        border: 1px dashed #000000 !important;
    }
    .badge-info, .b-info, .badge-neutral {
        background: #F3F4F6 !important;
        color: #000000 !important;
        border: 1px solid #000000 !important;
    }

    /* ── 2-Column Info Grid for Detail Deeds ── */
    .grid-2, .inv-grid-2 {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
        width: 100%;
        box-sizing: border-box;
    }
    .grid-col, .inv-col-box {
        flex: 1;
        min-width: 0;
        border: 1px solid #000000 !important;
        border-radius: 4px;
        background: #FFFFFF !important;
        overflow: hidden;
        box-sizing: border-box;
    }
    .col-heading, .inv-box-head {
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #000000 !important;
        background: #F3F4F6 !important;
        padding: 5px 9px;
        border-bottom: 1px solid #000000 !important;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .col-heading i, .inv-box-head i {
        color: #000000 !important;
    }
    .info-row, .inv-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 9px;
        border-bottom: 1px dotted #CCCCCC !important;
        font-size: 8.5px;
        align-items: baseline;
        gap: 6px;
    }
    .info-row:last-child, .inv-row:last-child {
        border-bottom: none !important;
    }
    .info-label, .inv-lbl {
        color: #444444 !important;
        font-weight: 600;
        flex-shrink: 0;
    }
    .info-val, .info-value, .inv-val {
        color: #000000 !important;
        font-weight: 700;
        text-align: right;
        word-break: break-word;
    }

    /* ── Filter Applied Banner ── */
    .filter-row, .filters-applied {
        background: #FFFFFF !important;
        border: 1px solid #000000 !important;
        border-radius: 4px;
        padding: 5px 9px;
        margin-bottom: 10px;
        font-size: 8.5px;
        color: #000000 !important;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
    }

    /* ── Auth / Signatures Block ── */
    .auth-block, .inv-auth-grid {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        page-break-inside: avoid;
        width: 100%;
        box-sizing: border-box;
    }
    .auth-col, .inv-auth-box {
        width: 180px;
        text-align: center;
    }
    .auth-line, .inv-auth-line {
        border-top: 1.5px solid #000000 !important;
        margin-top: 30px;
        padding-top: 3px;
        font-size: 8.5px;
        font-weight: 700;
        color: #000000 !important;
    }
</style>
