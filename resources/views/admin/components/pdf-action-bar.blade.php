@php
    $pageTitle = $title ?? ($pdfTitle ?? 'Delawala Management Document');
    $fileName = $fileName ?? (\Illuminate\Support\Str::slug($pageTitle) . '-' . date('Ymd-Hi'));
    $orientation = $orientation ?? 'portrait';
    $barFirm = $firm ?? ($reportFirm ?? (session('login_type') === 'firm' && session('firm_id') ? \App\Models\Firm::find(session('firm_id')) : \App\Models\Firm::first()));
    $hasBarSig = $barFirm?->hasDigitalSignature() ?? false;
    $barSigUrl = $barFirm?->digital_signature_url;
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
        flex-wrap: wrap;
        gap: 12px;
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
        filter: none !important;
        -webkit-filter: none !important;
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
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-pdf-act {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 13px;
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
    .btn-pdf-signature {
        background: #1E293B;
        color: #FFFFFF;
        border: 1px solid #475569;
    }
    .btn-pdf-signature:hover {
        background: #334155;
        border-color: #64748B;
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
        {{-- Digital Signature Field & Upload Action --}}
        <input type="file" id="pdfBarSigInput" accept="image/*" style="display: none;" onchange="window.handlePdfBarSignatureUpload(this)">
        
        <span id="pdfBarSigBadge" class="pdf-bar-sig-badge" style="font-size: 10px; font-weight: 700; padding: 4px 9px; border-radius: 5px; border: 1px solid {{ $hasBarSig ? '#10B981' : '#64748B' }}; color: {{ $hasBarSig ? '#34D399' : '#CBD5E1' }}; background: rgba(0,0,0,0.35); display: inline-flex; align-items: center; gap: 5px;">
            <i class="fa-solid fa-signature"></i> <span id="pdfBarSigBadgeText">{{ $hasBarSig ? 'Signature Active' : 'No Signature' }}</span>
        </span>

        <button type="button" onclick="document.getElementById('pdfBarSigInput').click()" class="btn-pdf-act btn-pdf-signature" id="btnPdfSig" title="Upload or Change Digital Signature Photo">
            <i class="fa-solid fa-file-signature"></i> <span id="pdfBarSigBtnText">{{ $hasBarSig ? 'Change Signature' : 'Add Signature' }}</span>
        </button>

        <button type="button" onclick="window.handlePdfBarSignatureRemove()" class="btn-pdf-act btn-pdf-remove-sig" id="btnPdfRemoveSig" style="{{ $hasBarSig ? 'display:inline-flex;' : 'display:none;' }}; background: rgba(239, 68, 68, 0.2); color: #FCA5A5; border: 1px solid rgba(239, 68, 68, 0.4); padding: 7px 10px;" title="Remove Digital Signature">
            <i class="fa-solid fa-trash-can"></i>
        </button>

        <span id="pdfBarSigSpinner" style="display: none; color: #38BDF8; font-size: 12px;">
            <i class="fa-solid fa-spinner fa-spin"></i>
        </span>
        <span id="pdfBarSigMsg" style="display: none; color: #34D399; font-size: 11px; font-weight: 700;"></span>

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

    function removeAllDelawalaSigPlaceholders() {
        try {
            document.querySelectorAll('#delawalaSigPlaceholder, .delawala-sig-placeholder, [class*="sig-placeholder"]').forEach(el => {
                el.remove();
            });
        } catch(e) {}
    }
    removeAllDelawalaSigPlaceholders();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeAllDelawalaSigPlaceholders);
    }
    window.addEventListener('load', removeAllDelawalaSigPlaceholders);
    window.addEventListener('beforeprint', removeAllDelawalaSigPlaceholders);

    window.triggerPdfSignatureUpload = function() {
        const inp = document.getElementById('pdfBarSigInput') || document.getElementById('invSignFileInput');
        if (inp) inp.click();
    };

    window.handlePdfBarSignatureUpload = function(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // 1. Instant local preview on document signature area
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImgs = document.querySelectorAll('#delawalaSigImg, .delawala-sig-img, #invSignImg');
            previewImgs.forEach(img => {
                img.src = e.target.result;
                img.style.display = 'block';
            });
            removeAllDelawalaSigPlaceholders();

            const removeBtn = document.getElementById('btnPdfRemoveSig');
            if (removeBtn) removeBtn.style.display = 'inline-flex';
            const btnText = document.getElementById('pdfBarSigBtnText');
            if (btnText) btnText.textContent = 'Change Signature';
            const badgeText = document.getElementById('pdfBarSigBadgeText');
            if (badgeText) badgeText.textContent = 'Signature Active';
            const badge = document.getElementById('pdfBarSigBadge');
            if (badge) {
                badge.style.borderColor = '#10B981';
                badge.style.color = '#34D399';
            }
        };
        reader.readAsDataURL(file);

        // 2. AJAX Upload to backend
        const spinner = document.getElementById('pdfBarSigSpinner');
        const msg = document.getElementById('pdfBarSigMsg');
        if (spinner) spinner.style.display = 'inline-block';
        if (msg) msg.style.display = 'none';

        const formData = new FormData();
        formData.append('signature_image', file);
        formData.append('firm_id', '{{ $barFirm->id ?? '' }}');

        fetch('{{ route('pdf.upload-signature') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';
            if (data.success && data.signature_url) {
                const previewImgs = document.querySelectorAll('#delawalaSigImg, .delawala-sig-img, #invSignImg');
                previewImgs.forEach(img => {
                    img.src = data.signature_url;
                    img.style.display = 'block';
                });
                if (msg) {
                    msg.textContent = '✓ Saved!';
                    msg.style.display = 'inline-block';
                    setTimeout(() => { msg.style.display = 'none'; }, 3500);
                }
            } else {
                alert(data.message || 'Error uploading signature');
            }
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            console.error('Signature upload error:', err);
        });
    };

    window.handlePdfBarSignatureRemove = function() {
        if (!confirm('Remove digital signature from firm defaults?')) return;

        const spinner = document.getElementById('pdfBarSigSpinner');
        if (spinner) spinner.style.display = 'inline-block';

        const formData = new FormData();
        formData.append('firm_id', '{{ $barFirm->id ?? '' }}');

        fetch('{{ route('pdf.remove-signature') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';
            if (data.success) {
                const previewImgs = document.querySelectorAll('#delawalaSigImg, .delawala-sig-img, #invSignImg');
                previewImgs.forEach(img => {
                    img.src = '';
                    img.style.display = 'none';
                });

                const removeBtn = document.getElementById('btnPdfRemoveSig');
                if (removeBtn) removeBtn.style.display = 'none';
                const btnText = document.getElementById('pdfBarSigBtnText');
                if (btnText) btnText.textContent = 'Add Signature';
                const badgeText = document.getElementById('pdfBarSigBadgeText');
                if (badgeText) badgeText.textContent = 'No Signature';
                const badge = document.getElementById('pdfBarSigBadge');
                if (badge) {
                    badge.style.borderColor = '#64748B';
                    badge.style.color = '#CBD5E1';
                }
            }
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            console.error('Signature remove error:', err);
        });
    };

    window.downloadDelawalaPDF = function(filename, orientation) {
        const btn = document.getElementById('btnDownloadPDF');
        const oldHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating PDF...';
            btn.disabled = true;
        }

        window.scrollTo(0, 0);

        removeAllDelawalaSigPlaceholders();

        // Hide all no-print elements and toolbar before generating PDF
        const noPrintEls = document.querySelectorAll('.delawala-pdf-toolbar, .no-print, .no-print-bar, .delawala-sig-placeholder, #delawalaSigPlaceholder');
        noPrintEls.forEach(el => el.style.setProperty('display', 'none', 'important'));

        const restoreNoPrints = function() {
            noPrintEls.forEach(el => el.style.removeProperty('display'));
            const toolbar = document.querySelector('.delawala-pdf-toolbar');
            if (toolbar) toolbar.style.display = 'flex';
            if (btn) {
                btn.innerHTML = oldHtml;
                btn.disabled = false;
            }
        };

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
                scrollX: 0,
                ignoreElements: function(node) {
                    if (!node) return false;
                    if (node.id === 'delawalaSigPlaceholder') return true;
                    if (node.classList) {
                        return node.classList.contains('no-print') || 
                               node.classList.contains('delawala-pdf-toolbar') || 
                               node.classList.contains('delawala-sig-placeholder') ||
                               node.classList.contains('no-print-bar');
                    }
                    return false;
                }
            },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: orientation || 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        // Allow DOM to settle before snapshot
        setTimeout(function() {
            html2pdf().set(opt).from(element).save().then(() => {
                restoreNoPrints();
            }).catch(err => {
                console.error('PDF export error:', err);
                restoreNoPrints();
                window.print();
            });
        }, 120);
    };
</script>
