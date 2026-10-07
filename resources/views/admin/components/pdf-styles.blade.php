@php
    $orientation = $orientation ?? 'portrait';
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* ════════════════════════════════════════════════════════════
       UNIFIED MASTER ERP A4 PDF DESIGN SYSTEM
    ════════════════════════════════════════════════════════════ */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    html, body {
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        font-size: 11px;
        color: #0F172A;
        background: #FFFFFF;
        line-height: 1.45;
        -webkit-font-smoothing: antialiased;
    }
    body {
        padding: 24px;
        min-height: 100%;
    }

    /* ── A4 Page Specifications ── */
    @page {
        size: A4 {{ $orientation }};
        margin: 8mm 8mm 8mm 8mm;
    }
    @media print {
        html, body {
            padding: 0 !important;
            margin: 0 !important;
            background: #FFFFFF !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .delawala-pdf-toolbar, .no-print-bar, .no-print {
            display: none !important;
        }
        .page-break {
            page-break-before: always;
        }
        tr, .stat-box, .grid-col, .grid-2, .finance-card, .info-box, .auth-block, .bw-box, table {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    }

    .delawala-printable-area {
        width: 100%;
        max-width: 100%;
        background: #FFFFFF;
        margin: 0 auto;
    }

    /* ── KPI / Stat Summary Row ── */
    .stat-row {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .stat-box {
        flex: 1;
        min-width: 120px;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 10px 14px;
        background: #F8FAFC;
    }
    .stat-box .s-label {
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748B;
        margin-bottom: 3px;
    }
    .stat-box .s-value {
        font-size: 15px;
        font-weight: 800;
        color: #0F172A;
    }
    .stat-box.s-gold   { border-color: rgba(217, 119, 6, 0.35); background: rgba(217, 119, 6, 0.04); }
    .stat-box.s-gold .s-value { color: #B45309; }
    .stat-box.s-green  { border-color: rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.04); }
    .stat-box.s-green .s-value { color: #059669; }
    .stat-box.s-blue   { border-color: rgba(37, 99, 235, 0.35); background: rgba(37, 99, 235, 0.04); }
    .stat-box.s-blue .s-value { color: #2563EB; }
    .stat-box.s-red    { border-color: rgba(239, 68, 68, 0.35); background: rgba(239, 68, 68, 0.04); }
    .stat-box.s-red .s-value { color: #DC2626; }
    .stat-box.s-purple { border-color: rgba(139, 92, 246, 0.35); background: rgba(139, 92, 246, 0.04); }
    .stat-box.s-purple .s-value { color: #7C3AED; }

    /* ── Section Labels ── */
    .section-label {
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #D97706;
        margin-top: 14px;
        margin-bottom: 8px;
        padding-bottom: 4px;
        border-bottom: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ── Master Data Tables ── */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5px;
        margin-bottom: 16px;
    }
    thead tr {
        background: #0F172A;
    }
    thead th {
        padding: 8px 9px;
        color: #FFFFFF;
        font-weight: 700;
        text-align: left;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        border: 1px solid #1E293B;
    }
    thead th.c, tbody td.c { text-align: center; }
    thead th.r, tbody td.r, thead th.num, tbody td.num { text-align: right; }
    tbody tr:nth-child(even) {
        background: #F8FAFC;
    }
    tbody td {
        padding: 7px 9px;
        border: 1px solid #E2E8F0;
        vertical-align: middle;
        color: #1E293B;
    }
    tfoot tr {
        background: #F1F5F9;
        font-weight: 800;
    }
    tfoot td {
        padding: 8px 9px;
        border: 1px solid #CBD5E1;
        border-top: 2px solid #0F172A;
        font-size: 11px;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .badge-success, .b-paid, .badge-active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .badge-warning, .b-partial             { background: #FFFBEB; color: #D97706; border: 1px solid #FDE68A; }
    .badge-danger,  .b-pending, .badge-inactive { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }
    .badge-info,    .b-info                { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }

    /* ── 2-Column Info Grid for Detail Deeds ── */
    .grid-2 {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
    }
    .grid-col {
        flex: 1;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 12px 14px;
        background: #F8FAFC;
    }
    .col-heading {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #D97706;
        margin-bottom: 8px;
        padding-bottom: 3px;
        border-bottom: 1px solid #E2E8F0;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 3.5px 0;
        border-bottom: 1px dashed #E2E8F0;
        font-size: 10.5px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748B; font-weight: 600; }
    .info-val, .info-value { color: #0F172A; font-weight: 700; text-align: right; }

    /* ── Filter Applied Banner ── */
    .filter-row, .filters-applied {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 6px 10px;
        margin-bottom: 14px;
        font-size: 9.5px;
        color: #475569;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    /* ── Auth / Signatures Block ── */
    .auth-block {
        margin-top: 32px;
        padding-top: 10px;
        display: flex;
        justify-content: space-between;
        page-break-inside: avoid;
    }
    .auth-col {
        width: 180px;
        text-align: center;
    }
    .auth-line {
        border-top: 1.5px solid #0F172A;
        margin-top: 36px;
        padding-top: 4px;
        font-size: 9.5px;
        font-weight: 700;
        color: #0F172A;
    }
</style>
