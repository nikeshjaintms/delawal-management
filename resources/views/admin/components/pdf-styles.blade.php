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
       UNIFIED MASTER ERP CENTERED A4-OPTIMIZED PDF DESIGN SYSTEM
    ════════════════════════════════════════════════════════════ */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    html, body {
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        font-size: 9.5px;
        color: #0F172A;
        background: #F1F5F9;
        line-height: 1.4;
        margin: 0;
        padding: 0;
        -webkit-font-smoothing: antialiased;
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
    .delawala-printable-area, .invoice-print-sheet {
        width: 100%;
        max-width: {{ $isLandscape ? '1040px' : '820px' }};
        background: #FFFFFF !important;
        margin: 20px auto;
        padding: 24px 28px;
        box-sizing: border-box;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-radius: 4px;
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
        .delawala-printable-area, .invoice-print-sheet {
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
            border-radius: 0 !important;
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
        border: 1px solid #CBD5E1;
        border-radius: 4px;
        padding: 7px 11px;
        background: #FFFFFF;
        box-sizing: border-box;
    }
    .stat-box .s-label {
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 2px;
    }
    .stat-box .s-value {
        font-size: 13.5px;
        font-weight: 800;
        color: #0F172A;
    }
    .stat-box.s-gold, .stat-box.s-green, .stat-box.s-blue, .stat-box.s-red, .stat-box.s-purple {
        border-color: #CBD5E1;
        background: #FFFFFF;
    }
    .stat-box.s-gold .s-value, .stat-box.s-green .s-value, .stat-box.s-blue .s-value, .stat-box.s-red .s-value, .stat-box.s-purple .s-value {
        color: #0F172A;
    }

    /* ── Section Labels ── */
    .section-label {
        font-size: 9.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #0F172A;
        margin-top: 12px;
        margin-bottom: 6px;
        padding-bottom: 3px;
        border-bottom: 1.5px solid #0F172A;
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        box-sizing: border-box;
    }

    /* ── Master Data Tables ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9px;
        margin-bottom: 10px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        table-layout: auto;
        box-sizing: border-box;
    }
    thead tr {
        background: #F8FAFC;
    }
    thead th {
        padding: 6px 7px;
        color: #0F172A;
        font-weight: 800;
        text-align: left;
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border: 1px solid #CBD5E1;
        border-bottom: 1.5px solid #0F172A;
    }
    thead th.c, tbody td.c { text-align: center; }
    thead th.r, tbody td.r, thead th.num, tbody td.num, tfoot td.r { text-align: right; }
    tbody tr:nth-child(even) {
        background: #FFFFFF;
    }
    tbody td {
        padding: 5.5px 7px;
        border: 1px solid #CBD5E1;
        vertical-align: top;
        color: #0F172A;
        word-break: break-word;
    }
    tfoot tr {
        background: #F8FAFC;
        font-weight: 800;
    }
    tfoot td {
        padding: 5.5px 7px;
        border: 1px solid #CBD5E1;
        border-top: 1.5px solid #0F172A;
        font-size: 9px;
        color: #0F172A;
        word-break: break-word;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 1.5px 5.5px;
        border-radius: 3px;
        font-size: 7.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        color: #0F172A;
        white-space: nowrap;
    }
    .badge-success, .b-paid, .badge-active { background: #FFFFFF; color: #0F172A; border: 1px solid #94A3B8; }
    .badge-warning, .b-partial             { background: #FFFFFF; color: #0F172A; border: 1px solid #CBD5E1; }
    .badge-danger,  .b-pending, .badge-inactive { background: #FFFFFF; color: #0F172A; border: 1px solid #94A3B8; }
    .badge-info,    .b-info                { background: #FFFFFF; color: #0F172A; border: 1px solid #94A3B8; }

    /* ── 2-Column Info Grid for Detail Deeds ── */
    .grid-2 {
        display: flex;
        gap: 10px;
        margin-bottom: 12px;
        width: 100%;
        box-sizing: border-box;
    }
    .grid-col {
        flex: 1;
        min-width: 0;
        border: 1px solid #CBD5E1;
        border-radius: 4px;
        background: #FFFFFF;
        overflow: hidden;
        box-sizing: border-box;
    }
    .col-heading {
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #0F172A;
        background: #F8FAFC;
        padding: 5px 9px;
        border-bottom: 1px solid #CBD5E1;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 9px;
        border-bottom: 1px dotted #E2E8F0;
        font-size: 8.5px;
        align-items: baseline;
        gap: 6px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #475569; font-weight: 600; flex-shrink: 0; }
    .info-val, .info-value { color: #0F172A; font-weight: 700; text-align: right; word-break: break-word; }

    /* ── Filter Applied Banner ── */
    .filter-row, .filters-applied {
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 4px;
        padding: 5px 9px;
        margin-bottom: 10px;
        font-size: 8.5px;
        color: #334155;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        width: 100%;
        box-sizing: border-box;
    }

    /* ── Auth / Signatures Block ── */
    .auth-block {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        page-break-inside: avoid;
        width: 100%;
        box-sizing: border-box;
    }
    .auth-col {
        width: 180px;
        text-align: center;
    }
    .auth-line {
        border-top: 1.5px solid #0F172A;
        margin-top: 30px;
        padding-top: 3px;
        font-size: 8.5px;
        font-weight: 700;
        color: #0F172A;
    }
</style>
