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
        left: 0;
        right: 0;
        width: 100%;
        box-sizing: border-box;
        z-index: 99999;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0F172A;
        color: #FFFFFF;
        padding: 10px 24px;
        margin: 0 0 20px 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        border-bottom: 2px solid #000000;
    }
    .pdf-bar-brand {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .pdf-bar-logo-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFFFFF;
        padding: 3px 8px;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        height: 34px;
        box-sizing: border-box;
    }
    .pdf-bar-logo-wrap img {
        height: 28px;
        max-height: 28px;
        width: auto;
        object-fit: contain;
        display: block;
        filter: grayscale(100%) contrast(120%);
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
        background: rgba(255, 255, 255, 0.15);
        color: #FFFFFF;
        border: 1px solid rgba(255, 255, 255, 0.35);
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
        background: #1E293B;
        color: #FFFFFF;
        border: 1px solid #475569;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }
    .btn-pdf-download:hover {
        background: #334155;
        border-color: #64748B;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
    }
    .btn-pdf-print {
        background: #000000;
        color: #FFFFFF;
        border: 1px solid #334155;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
    }
    .btn-pdf-print:hover {
        background: #1E293B;
        border-color: #475569;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.45);
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
        <div class="pdf-bar-logo-wrap">
            @if(file_exists(public_path('images/logo 1.png')))
                <img src="{{ asset('images/logo 1.png') }}?v={{ filemtime(public_path('images/logo 1.png')) }}" alt="Delawala Properties">
            @elseif(file_exists(public_path('images/pdf-logo.png')))
                <img src="{{ asset('images/pdf-logo.png') }}?v={{ filemtime(public_path('images/pdf-logo.png')) }}" alt="Delawala Properties">
            @elseif(file_exists(public_path('assets/logos/logo 1.png')))
                <img src="{{ asset('assets/logos/logo 1.png') }}?v={{ filemtime(public_path('assets/logos/logo 1.png')) }}" alt="Delawala Properties">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties">
            @endif
        </div>
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
        
        if (window.opener || window.history.length <= 1) {
            window.close();
        }

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

        window.scrollTo(0, 0);

        const toolbar = document.querySelector('.delawala-pdf-toolbar');
        if (toolbar) toolbar.style.display = 'none';

        const element = document.getElementById('delawala-printable-area') 
            || document.querySelector('.delawala-printable-area') 
            || document.body;
        
        const isLandscape = (orientation === 'landscape');

        const opt = {
            margin:       isLandscape ? [6, 8, 6, 8] : [6, 6, 6, 6],
            filename:     (filename || 'Delawala-Document') + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { 
                scale: 2, 
                useCORS: true, 
                logging: false,
                scrollY: 0,
                scrollX: 0
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: orientation || 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Allow DOM to settle before snapshot
        setTimeout(function() {
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
        }, 120);
    };
</script>
