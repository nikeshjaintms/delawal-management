@php
    $pageTitle = $title ?? ($pdfTitle ?? 'Delawala Management Document');
    $fileName = $fileName ?? (\Illuminate\Support\Str::slug($pageTitle) . '-' . date('Ymd-Hi'));
    $orientation = $orientation ?? 'portrait';
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* ── Floating PDF Action Bar ── */
    .delawala-pdf-toolbar {
        position: sticky;
        top: 0;
        z-index: 99999;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
        color: #FFFFFF;
        padding: 12px 20px;
        margin: -24px -24px 22px -24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22);
        border-bottom: 2.5px solid #D97706;
    }
    .pdf-bar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pdf-bar-brand img {
        height: 32px;
        width: auto;
        object-fit: contain;
    }
    .pdf-bar-title {
        font-size: 13.5px;
        font-weight: 800;
        color: #F8FAFC;
        letter-spacing: 0.3px;
    }
    .pdf-bar-badge {
        font-size: 9.5px;
        font-weight: 700;
        background: rgba(217, 119, 6, 0.2);
        color: #FBBF24;
        border: 1px solid rgba(245, 158, 11, 0.4);
        padding: 2px 7px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pdf-bar-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .btn-pdf-act {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Segoe UI', Arial, sans-serif;
    }
    .btn-pdf-act:hover {
        transform: translateY(-1px);
    }
    .btn-pdf-act:active {
        transform: translateY(0);
    }
    .btn-pdf-back {
        background: #334155;
        color: #F8FAFC;
        border: 1px solid #475569;
    }
    .btn-pdf-back:hover {
        background: #475569;
        color: #FFFFFF;
    }
    .btn-pdf-download {
        background: linear-gradient(135deg, #D97706 0%, #B45309 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    }
    .btn-pdf-download:hover {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        box-shadow: 0 6px 16px rgba(217, 119, 6, 0.45);
    }
    .btn-pdf-print {
        background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
    .btn-pdf-print:hover {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.45);
    }

    @media print {
        .delawala-pdf-toolbar, .no-print-bar, .no-print {
            display: none !important;
        }
        body {
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>

<div class="delawala-pdf-toolbar no-print">
    <div class="pdf-bar-brand">
        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties">
        <div>
            <div class="pdf-bar-title">{{ $pageTitle }}</div>
        </div>
        <span class="pdf-bar-badge">Delawala PDF</span>
    </div>
    <div class="pdf-bar-actions">
        <button type="button" onclick="handlePdfBack(event, '{{ $backUrl ?? '' }}')" class="btn-pdf-act btn-pdf-back" title="Go Back">
            <i class="fa-solid fa-arrow-left"></i> Back
        </button>

        <button type="button" onclick="window.downloadDelawalaPDF('{{ $fileName }}', '{{ $orientation }}')" class="btn-pdf-act btn-pdf-download" id="btnDownloadPDF" title="Download PDF to Computer">
            <i class="fa-solid fa-file-arrow-down"></i> Download PDF
        </button>

        <button type="button" onclick="window.print()" class="btn-pdf-act btn-pdf-print" title="Print / Save Document">
            <i class="fa-solid fa-print"></i> Print / Save PDF
        </button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    window.handlePdfBack = function(e, fallbackUrl) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // 1. If this was opened in a new tab/window, closing tab is instant (0ms latency)
        if (window.opener || window.history.length <= 1) {
            window.close();
        }

        // 2. If it's in the same tab or window.close was blocked by browser
        setTimeout(function() {
            if (fallbackUrl && fallbackUrl !== '') {
                window.location.href = fallbackUrl;
            } else if (window.history.length > 1) {
                window.history.back();
            }
        }, 50);
    };

    window.downloadDelawalaPDF = function(filename, orientation) {
        const btn = document.getElementById('btnDownloadPDF');
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating PDF...';
            btn.disabled = true;
        }

        const toolbar = document.querySelector('.delawala-pdf-toolbar');
        const element = document.body;
        
        const opt = {
            margin:       [8, 8, 8, 8],
            filename:     (filename || 'Delawala-Document') + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: orientation || 'portrait' }
        };

        if (toolbar) toolbar.style.display = 'none';

        html2pdf().set(opt).from(element).save().then(() => {
            if (toolbar) toolbar.style.display = 'flex';
            if (btn) {
                btn.innerHTML = oldHtml;
                btn.disabled = false;
            }
        }).catch(err => {
            console.error('PDF export error:', err);
            if (toolbar) toolbar.style.display = 'flex';
            if (btn) {
                btn.innerHTML = oldHtml;
                btn.disabled = false;
            }
            window.print();
        });
    };
</script>
