@php
    $isGstInvoice = ((float)($invoice->tax_amount ?? 0) > 0 || (float)($invoice->tax_percent ?? 0) > 0);
    $invHeading = $isGstInvoice ? 'TAX INVOICE' : 'INVOICE / BILL OF SUPPLY';
    $invSubtitle = $isGstInvoice ? 'Official GST Tax Invoice (Under Rule 46 of CGST Rules)' : 'Commercial Bill / Invoice';

    if (!function_exists('delawalaAmountInWords')) {
        function delawalaAmountInWords($number) {
            $number = (float)$number;
            if ($number <= 0) return 'INR Zero Only';

            $words = [
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
                5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
                14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
                18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
                40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy',
                80 => 'Eighty', 90 => 'Ninety'
            ];

            $numToWords = function($n) use (&$numToWords, $words) {
                $n = (int)$n;
                if ($n == 0) return '';
                if ($n < 20) return $words[$n];
                if ($n < 100) return trim($words[((int)($n / 10)) * 10] . ' ' . $words[$n % 10]);
                if ($n < 1000) return trim($words[(int)($n / 100)] . ' Hundred ' . $numToWords($n % 100));
                if ($n < 100000) return trim($numToWords((int)($n / 1000)) . ' Thousand ' . $numToWords($n % 1000));
                if ($n < 10000000) return trim($numToWords((int)($n / 100000)) . ' Lakh ' . $numToWords($n % 100000));
                return trim($numToWords((int)($n / 10000000)) . ' Crore ' . $numToWords($n % 10000000));
            };

            $intVal = (int)floor($number);
            $fraction = (int)round(($number - $intVal) * 100);

            $rupees = $numToWords($intVal);
            $paise = ($fraction > 0) ? ' and ' . $numToWords($fraction) . ' Paise' : '';

            return 'INR ' . trim($rupees ?: 'Zero') . $paise . ' Only';
        }
    }

    $activeFirm = $invoice->firm ?? (\App\Models\Firm::first());
    $firmDisplayName = $activeFirm->firm_name ?? 'DELAWALA PROPERTIES';
    $firmGstNo = $activeFirm->gst_no ?? '24CUBPD0770R1ZI';
    $firmAddress = $activeFirm->address ?? 'Ground Floor, FF Sri No. 116, Mhan Plaza, Dattlegam Road, Dahegam, Dahegam, Bharuch';
    $firmCity = $activeFirm->city ?? 'Dahegam, Bharuch';
    $firmState = $activeFirm->state ?? 'Gujarat';
    $firmPin = $activeFirm->pincode ?? '392012';
    $firmOwner = $activeFirm->owner_name ?? 'DELAWALA ZAFAR';
    $firmPhone = $activeFirm->mobile ?? '9999999999';
    $firmBank = $activeFirm->bank_name ?? 'Bank of Baroda';
    $firmAcc = $activeFirm->account_number ?? ($activeFirm->bank_account_no ?? '—');
    $firmIfsc = $activeFirm->ifsc_code ?? ($activeFirm->bank_ifsc ?? '—');
    $firmBranch = $activeFirm->branch_name ?? ($activeFirm->bank_branch ?? 'Dahegam Branch');

    $activePaymentMode = $invoice->payment_mode
        ?: ($invoice->payments->first()?->payment_mode
        ?: ($invoice->paymentMode?->name ?? 'Cash'));

    $allPaymentModes = isset($paymentModes) && $paymentModes->isNotEmpty()
        ? $paymentModes
        : \App\Models\PaymentMode::where('status', 'active')->orderBy('name')->get();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invHeading }} - {{ $invoice->invoice_no ?? 'INV' }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
    <style>
        /* ── Exact Match Formal Clean GST Invoice Design Optimized for A4 (Monochrome B&W) ── */
        html, body {
            background: #FFFFFF !important;
            color: #000000 !important;
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            font-size: 8.5px;
            line-height: 1.35;
            margin: 0;
            padding: 0;
            width: 100%;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            padding: 0 0 20px 0;
            background: #FFFFFF !important;
        }

        .invoice-print-sheet {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
            background: #FFFFFF !important;
            color: #000000 !important;
            padding: 10px 14px;
            box-sizing: border-box;
            border: 1px solid #000000;
        }

        /* ── Header ── */
        .inv-header-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            gap: 12px;
            width: 100%;
            box-sizing: border-box;
        }
        .inv-brand-col {
            flex: 1.25;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .inv-logo-img {
            height: 72px;
            max-height: 75px;
            max-width: 135px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
            display: block;
            image-rendering: -webkit-optimize-contrast;
            filter: grayscale(100%) contrast(120%) !important;
            -webkit-filter: grayscale(100%) contrast(120%) !important;
        }
        .inv-firm-name {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.15;
            color: #000000;
        }
        .inv-firm-sub {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333333;
            margin-top: 1px;
            margin-bottom: 2px;
        }
        .inv-firm-addr {
            font-size: 7.5px;
            color: #444444;
            line-height: 1.3;
        }
        .inv-firm-meta {
            display: flex;
            gap: 5px;
            margin-top: 3px;
            flex-wrap: wrap;
        }
        .inv-meta-pill {
            display: inline-flex;
            align-items: center;
            border: 1px solid #000000;
            padding: 1.5px 6px;
            font-size: 7.5px;
            font-weight: 600;
            background: #FFFFFF;
            color: #000000;
            white-space: nowrap;
            border-radius: 3px;
        }
        .inv-meta-pill strong {
            font-weight: 700;
            margin-right: 3px;
            color: #000000;
        }

        .inv-title-col {
            flex: 0.85;
            min-width: 0;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1px;
        }
        .inv-main-title {
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #000000;
            line-height: 1.1;
            margin-bottom: 1px;
        }
        .inv-rule-sub {
            font-size: 7.5px;
            font-weight: 600;
            color: #444444;
            margin-bottom: 3px;
        }
        .inv-doc-info {
            font-size: 8px;
            color: #000000;
            line-height: 1.35;
            display: flex;
            justify-content: flex-end;
            gap: 4px;
            width: 100%;
        }
        .inv-doc-info .inv-doc-label {
            color: #444444;
            font-weight: 600;
        }
        .inv-doc-info strong, .inv-doc-info .inv-doc-val {
            font-weight: 700;
            color: #000000;
        }

        /* ── 2-Column Details Box ── */
        .inv-grid-2 {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        .inv-col-box {
            flex: 1;
            min-width: 0;
            border: 1px solid #000000;
            background: #FFFFFF;
            box-sizing: border-box;
            border-radius: 4px;
            overflow: hidden;
        }
        .inv-box-head {
            background: #F3F4F6;
            color: #000000;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 4px 7px;
            border-bottom: 1px solid #000000;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .inv-box-body {
            padding: 4px 7px;
        }
        .inv-row {
            display: flex;
            justify-content: space-between;
            padding: 2.5px 0;
            font-size: 8px;
            border-bottom: 1px dotted #CCCCCC;
            gap: 6px;
            align-items: baseline;
        }
        .inv-row:last-child {
            border-bottom: none;
        }
        .inv-lbl {
            color: #444444;
            font-weight: 600;
            flex-shrink: 0;
        }
        .inv-val {
            color: #000000;
            font-weight: 700;
            text-align: right;
            word-break: break-word;
        }

        /* ── Particulars Table ── */
        .inv-table-sec-title {
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        table.inv-items-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin-bottom: 6px;
            border: 1.5px solid #000000;
            table-layout: fixed;
            background: #FFFFFF;
        }
        table.inv-items-tbl thead th {
            background: #F3F4F6;
            color: #000000;
            padding: 4.5px 6px;
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #000000;
            border-bottom: 2px solid #000000;
            text-align: left;
            overflow: hidden;
        }
        table.inv-items-tbl thead th.c, table.inv-items-tbl tbody td.c { text-align: center; }
        table.inv-items-tbl thead th.r, table.inv-items-tbl tbody td.r, table.inv-items-tbl tfoot td.r { text-align: right; }
        
        table.inv-items-tbl tbody td {
            background: #FFFFFF;
            padding: 4.5px 6px;
            border: 1px solid #D1D5DB;
            color: #000000;
            vertical-align: top;
            word-break: break-word;
        }
        table.inv-items-tbl tfoot td {
            background: #FFFFFF;
            padding: 3.5px 6px;
            border: 1px solid #000000;
            color: #000000;
            font-size: 8px;
            word-break: break-word;
        }
        .tfoot-grand-total {
            background: #F3F4F6 !important;
            font-weight: 900 !important;
        }
        .tfoot-grand-total td {
            background: #F3F4F6 !important;
            color: #000000 !important;
            font-size: 8.5px !important;
            font-weight: 900 !important;
            padding: 4px 6px !important;
            border-top: 2px solid #000000 !important;
            border-bottom: 3.5px double #000000 !important;
        }

        /* ── Words Bar ── */
        .inv-words-bar {
            border: 1px solid #000000;
            padding: 4px 7px;
            margin-bottom: 8px;
            background: #FFFFFF;
            font-size: 8px;
            color: #000000;
            word-break: break-word;
            border-radius: 4px;
        }
        .inv-words-bar strong {
            font-weight: 800;
            color: #000000;
        }

        /* ── Bank & Terms ── */
        .inv-bank-terms-grid {
            display: flex;
            gap: 8px;
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        /* ── Auth Signatures ── */
        .inv-auth-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            margin-bottom: 6px;
            page-break-inside: avoid;
            width: 100%;
            box-sizing: border-box;
        }
        .inv-auth-box {
            width: 180px;
            text-align: center;
        }
        .inv-auth-line {
            border-top: 1.5px solid #000000;
            margin-top: 24px;
            padding-top: 3px;
            font-size: 8px;
            font-weight: 700;
            color: #000000;
        }

        /* ── Footer ── */
        .inv-footer {
            border-top: 1.5px solid #000000;
            padding-top: 4px;
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5px;
            color: #333333;
            width: 100%;
            box-sizing: border-box;
        }

        @media print {
            html, body {
                background: #FFFFFF !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }
            .invoice-print-sheet {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
            .delawala-pdf-toolbar, .no-print {
                display: none !important;
            }
            .inv-box-head, table.inv-items-tbl thead th, .tfoot-grand-total td {
                background: #F3F4F6 !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .inv-logo-img {
                max-height: 70px !important;
                max-width: 135px !important;
                object-fit: contain !important;
                filter: grayscale(100%) contrast(120%) !important;
                -webkit-filter: grayscale(100%) contrast(120%) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

@include('admin.components.pdf-action-bar', [
    'title' => $invHeading . ' #' . ($invoice->invoice_no ?? 'INV'),
    'orientation' => 'portrait',
    'backUrl' => isset($invoice->id) ? route('invoices.show', $invoice->id) : route('invoices.index')
])

<div class="no-print" style="max-width: 760px; margin: 0 auto 12px auto; padding: 10px 16px; background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border: 1.5px solid rgba(245, 158, 11, 0.45); border-radius: 10px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 16px rgba(0,0,0,0.25); flex-wrap: wrap; gap: 12px;">
    {{-- Left: Payment Mode Selector --}}
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <span style="font-size: 13px; font-weight: 700; color: #F8FAFC; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-credit-card" style="color: #FBBF24; font-size: 15px;"></i>
            <span>Payment Mode:</span>
        </span>
        <select id="quickPaymentModeSelect" style="background: #0F172A; color: #FFFFFF; border: 1.5px solid #F59E0B; padding: 6px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 700; cursor: pointer; outline: none; min-width: 150px;" onchange="handlePaymentModeChange(this.value)">
            @foreach($allPaymentModes as $pm)
                <option value="{{ $pm->name }}" {{ strcasecmp($activePaymentMode, $pm->name) === 0 ? 'selected' : '' }}>
                    {{ $pm->name }}
                </option>
            @endforeach
        </select>
        <span id="saveModeSpinner" style="display: none; color: #FBBF24; font-size: 12px;">
            <i class="fa-solid fa-circle-notch fa-spin"></i>
        </span>
        <span id="saveModeSuccess" style="display: none; color: #34D399; font-size: 12px; font-weight: 700;">
            <i class="fa-solid fa-circle-check"></i>
        </span>
    </div>

    {{-- Right: Digital Signature Photo Upload & Actions --}}
    @php
        $activeSignature = $invoice->signature_image ?: ($activeFirm->digital_signature ?? null);
        $hasSign = !empty($activeSignature) && (
            \Illuminate\Support\Facades\Storage::disk('public')->exists($activeSignature) ||
            file_exists(public_path('storage/' . $activeSignature)) ||
            file_exists(public_path($activeSignature))
        );
        $signSrc = $hasSign
            ? (file_exists(public_path($activeSignature)) ? asset($activeSignature) : asset('storage/' . $activeSignature))
            : '';
        $isFirmDefault = empty($invoice->signature_image) && !empty($activeFirm->digital_signature);
    @endphp
    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        @if($hasSign)
            <span id="signTypeBadge" style="font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.3);">
                {{ $isFirmDefault ? 'Firm Default Signature' : 'Custom Signature' }}
            </span>
        @endif
        <input type="file" id="invSignFileInput" accept="image/*" style="display: none;" onchange="handleSignatureUpload(this)">
        <button type="button" onclick="document.getElementById('invSignFileInput').click()" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: #FFFFFF; padding: 6px 13px; font-size: 12px; font-weight: 700; border-radius: 6px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);">
            <i class="fa-solid fa-signature"></i> <span id="signBtnText">{{ $hasSign ? 'Change Signature Photo' : 'Upload Signature Photo' }}</span>
        </button>
        <label style="font-size: 11px; color: #CBD5E1; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; user-select: none;">
            <input type="checkbox" id="applyToFirmCheck" value="1" checked style="cursor: pointer;">
            <span>Apply to all invoices</span>
        </label>
        <button type="button" id="removeSignBtn" onclick="handleSignatureRemove()" style="background: rgba(239, 68, 68, 0.2); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.4); padding: 6px 10px; font-size: 11px; font-weight: 700; border-radius: 6px; cursor: pointer; display: {{ $hasSign ? 'inline-flex' : 'none' }}; align-items: center; gap: 4px;" title="Remove Signature">
            <i class="fa-solid fa-trash-can"></i> Remove
        </button>
        <span id="signUploadSpinner" style="display: none; color: #34D399; font-size: 12px;">
            <i class="fa-solid fa-circle-notch fa-spin"></i> Saving Signature...
        </span>
        <span id="signUploadMsg" style="display: none; color: #34D399; font-size: 11px; font-weight: 700;"></span>
    </div>
</div>

<div id="delawala-printable-area" class="delawala-printable-area invoice-print-sheet">
    <!-- Header Section -->
    <div class="inv-header-box">
        <div class="inv-brand-col">
            @if(!empty($activeFirm->firm_logo) && file_exists(public_path('storage/' . $activeFirm->firm_logo)))
                <img src="{{ asset('storage/' . $activeFirm->firm_logo) }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            @elseif(file_exists(public_path('images/logo 1.png')))
                <img src="{{ asset('images/logo 1.png') }}?v={{ filemtime(public_path('images/logo 1.png')) }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            @elseif(file_exists(public_path('images/pdf-logo.png')))
                <img src="{{ asset('images/pdf-logo.png') }}?v={{ filemtime(public_path('images/pdf-logo.png')) }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            @elseif(file_exists(public_path('assets/logos/logo 1.png')))
                <img src="{{ asset('assets/logos/logo 1.png') }}?v={{ filemtime(public_path('assets/logos/logo 1.png')) }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            @else
                <img src="{{ asset('images/logo.png') }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            @endif
            <div>
                <div class="inv-firm-name">{{ $firmDisplayName }}</div>
                <div class="inv-firm-sub">Real Estate &bull; Construction &bull; Infrastructure Management</div>
                <div class="inv-firm-addr">
                    {{ $firmAddress }}, {{ $firmCity }}, {{ $firmState }} - {{ $firmPin }}
                </div>
                <div class="inv-firm-meta">
                    <span class="inv-meta-pill"><strong>GSTIN:</strong> {{ $firmGstNo }}</span>
                    <span class="inv-meta-pill"><strong>State:</strong> {{ $firmState }} (24)</span>
                </div>
                <div class="inv-firm-meta" style="margin-top: 3px;">
                    <span class="inv-meta-pill"><strong>Proprietor:</strong> {{ $firmOwner }}</span>
                    @if($firmPhone)
                        <span class="inv-meta-pill"><strong>Tel:</strong> {{ $firmPhone }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="inv-title-col">
            <div class="inv-main-title">{{ $invHeading }}</div>
            <div class="inv-rule-sub">{{ $invSubtitle }}</div>
            <div class="inv-doc-info">
                <span class="inv-doc-label">Invoice No:</span>
                <span class="inv-doc-val">{{ $invoice->invoice_no ?? '—' }}</span>
            </div>
            <div class="inv-doc-info">
                <span class="inv-doc-label">Invoice Date:</span>
                <span class="inv-doc-val">{{ isset($invoice->invoice_date) ? $invoice->invoice_date->format('d M Y') : now()->format('d M Y') }}</span>
            </div>
            @if(!empty($invoice->due_date))
            <div class="inv-doc-info">
                <span class="inv-doc-label">Due Date:</span>
                <span class="inv-doc-val">{{ $invoice->due_date->format('d M Y') }}</span>
            </div>
            @endif
            <div class="inv-doc-info">
                <span class="inv-doc-label">Place of Supply:</span>
                <span class="inv-doc-val">{{ $invoice->recipient_state ?? 'Gujarat' }} (24)</span>
            </div>
            <div class="inv-doc-info">
                <span class="inv-doc-label">Reverse Charge:</span>
                <span class="inv-doc-val">{{ ($invoice->is_reverse_charge ?? false) ? 'Yes' : 'No' }}</span>
            </div>
        </div>
    </div>

    <!-- Receiver & Supply Info Grid -->
    <div class="inv-grid-2">
        <div class="inv-col-box">
            <div class="inv-box-head"><i class="fa-solid fa-user"></i> DETAILS OF RECEIVER / BILLED TO</div>
            <div class="inv-box-body">
                <div class="inv-row">
                    <span class="inv-lbl">Party Name:</span>
                    <span class="inv-val">{{ $invoice->recipient_name ?? '—' }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Phone / Mobile:</span>
                    <span class="inv-val">{{ $invoice->recipient_phone ?? '—' }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Address:</span>
                    <span class="inv-val">{{ $invoice->recipient_address ?? '—' }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">GSTIN / UIN:</span>
                    <span class="inv-val">{{ $invoice->recipient_gstin ?: 'Unregistered (B2C)' }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">State &amp; Code:</span>
                    <span class="inv-val">{{ $invoice->recipient_state ?? 'Gujarat' }} (24)</span>
                </div>
            </div>
        </div>

        <div class="inv-col-box">
            <div class="inv-box-head"><i class="fa-solid fa-file-invoice"></i> SUPPLY &amp; PROJECT DETAILS</div>
            <div class="inv-box-body">
                <div class="inv-row">
                    <span class="inv-lbl">Project / Site:</span>
                    <span class="inv-val">{{ $invoice->project->project_name ?? 'General Real Estate' }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Invoice Type:</span>
                    <span class="inv-val">{{ ucfirst(str_replace('_', ' ', $invoice->invoice_type ?? 'Sale')) }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Payment Status:</span>
                    <span class="inv-val" style="text-transform:uppercase;">{{ str_replace('_', ' ', $invoice->payment_status ?? 'Unpaid') }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Payment Mode:</span>
                    <span class="inv-val" id="invDocPaymentMode" style="font-weight:700; text-transform:uppercase; color:#0F172A;">{{ $activePaymentMode }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Reference No:</span>
                    <span class="inv-val">{{ $invoice->property_sale_id ? ('Sale Ref #' . $invoice->property_sale_id) : ($invoice->booking_id ? ('Booking #' . $invoice->booking_id) : ($invoice->rental_id ? ('Rental #' . $invoice->rental_id) : 'Direct Invoice')) }}</span>
                </div>
                <div class="inv-row">
                    <span class="inv-lbl">Supply Type:</span>
                    <span class="inv-val">{{ ($invoice->tax_type ?? 'gst_intra') === 'gst_intra' ? 'Intra-State (CGST + SGST)' : 'Inter-State (IGST)' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Goods & Services Table -->
    <div class="inv-table-sec-title"><i class="fa-solid fa-list-ul"></i> PARTICULARS OF GOODS &amp; SERVICES SUPPLIED</div>
    <table class="inv-items-tbl">
        <thead>
            <tr>
                <th style="width:24px;" class="c">#</th>
                <th style="width:auto;">ITEM DESCRIPTION / PARTICULARS</th>
                <th class="c" style="width:58px;">HSN/SAC</th>
                <th class="r" style="width:62px;">QTY</th>
                <th class="r" style="width:95px;">RATE (₹)</th>
                <th class="r" style="width:105px;">TAXABLE (₹)</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($invoice->items) && count($invoice->items) > 0)
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td class="c">{{ $idx + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_description }}</strong>
                            @if(!empty($item->notes))
                                <div style="font-size:7.5px; color:#64748B;">{{ $item->notes }}</div>
                            @endif
                        </td>
                        <td class="c">{{ $item->hsn_sac_code ?: '9954' }}</td>
                        <td class="r">{{ number_format($item->quantity, 2) }} {{ $item->unit ?: 'Unit' }}</td>
                        <td class="r">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="r" style="font-weight:700;">₹{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="c">1</td>
                    <td>
                        <strong>Property Sale Consideration for {{ $invoice->recipient_name ?? 'Client' }} (Entire Property)</strong>
                        <div style="font-size:7.5px; color:#64748B;">({{ $invoice->project->project_name ?? 'Delawala Master Project' }})</div>
                    </td>
                    <td class="c">9954</td>
                    <td class="r">1.00 Unit</td>
                    <td class="r">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
                    <td class="r" style="font-weight:700;">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
                </tr>
            @endif
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="r"><strong>Total Taxable Amount</strong></td>
                <td class="r" style="font-weight:700;">₹{{ number_format($invoice->subtotal ?? $invoice->total_amount ?? 0, 2) }}</td>
            </tr>
            @if(!empty($invoice->tax_amount) && $invoice->tax_amount > 0)
                @if(($invoice->tax_type ?? 'gst_intra') === 'gst_intra')
                    <tr>
                        <td colspan="5" class="r">CGST ({{ ($invoice->tax_percent ?? 18) / 2 }}%)</td>
                        <td class="r">₹{{ number_format(($invoice->cgst_amount ?? ($invoice->tax_amount / 2)), 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="r">SGST ({{ ($invoice->tax_percent ?? 18) / 2 }}%)</td>
                        <td class="r">₹{{ number_format(($invoice->sgst_amount ?? ($invoice->tax_amount / 2)), 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td colspan="5" class="r">IGST ({{ $invoice->tax_percent ?? 18 }}%)</td>
                        <td class="r">₹{{ number_format($invoice->igst_amount ?? $invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
            @endif
            @if(!empty($invoice->discount_amount) && $invoice->discount_amount > 0)
            <tr>
                <td colspan="5" class="r">Discount Deducted</td>
                <td class="r">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if(!empty($invoice->round_off) && $invoice->round_off != 0)
            <tr>
                <td colspan="5" class="r">Round Off Adjustment</td>
                <td class="r">{{ $invoice->round_off > 0 ? '+' : '' }}₹{{ number_format($invoice->round_off, 2) }}</td>
            </tr>
            @endif
            <tr class="tfoot-grand-total">
                <td colspan="5" class="r" style="font-size:8px; font-weight:800;">TOTAL INVOICE VALUE (IN FIGURES)</td>
                <td class="r" style="font-size:9px; font-weight:900;">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
            </tr>
            @if(!empty($invoice->paid_amount) && $invoice->paid_amount > 0)
            <tr>
                <td colspan="5" class="r" style="font-weight:700;">Amount Received / Paid</td>
                <td class="r" style="font-weight:700;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            @endif
            @if(($invoice->balance_amount ?? 0) > 0)
            <tr>
                <td colspan="5" class="r" style="font-weight:800;">Net Balance Payable</td>
                <td class="r" style="font-weight:800;">₹{{ number_format($invoice->balance_amount, 2) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    <!-- Amount in Words -->
    <div class="inv-words-bar">
        <strong>Amount in Words:</strong> {{ delawalaAmountInWords($invoice->total_amount ?? 0) }}
    </div>

    <!-- Bank Details & Terms Box -->
    <div class="inv-bank-terms-grid">
        <div class="inv-col-box" style="flex:1;">
            <div class="inv-box-head"><i class="fa-solid fa-building-columns"></i> BANK REMITTANCE DETAILS</div>
            <div class="inv-box-body">
                <div class="inv-row"><span class="inv-lbl">Account Name:</span><span class="inv-val">{{ $firmDisplayName }}</span></div>
                <div class="inv-row"><span class="inv-lbl">Bank Name:</span><span class="inv-val">{{ $invoice->bank_name ?: $firmBank }}</span></div>
                <div class="inv-row"><span class="inv-lbl">Account Number:</span><span class="inv-val">{{ $invoice->bank_account_no ?: $firmAcc }}</span></div>
                <div class="inv-row"><span class="inv-lbl">IFSC Code:</span><span class="inv-val">{{ $invoice->bank_ifsc ?: $firmIfsc }}</span></div>
                <div class="inv-row"><span class="inv-lbl">Branch:</span><span class="inv-val">{{ $invoice->bank_branch ?: $firmBranch }}</span></div>
            </div>
        </div>

        <div class="inv-col-box" style="flex:1;">
            <div class="inv-box-head"><i class="fa-solid fa-file-contract"></i> TERMS &amp; CONDITIONS</div>
            <div class="inv-box-body" style="font-size:7.5px; color:#334155; line-height:1.4;">
                @if(!empty($invoice->terms_conditions))
                    <div style="white-space: pre-line;">{{ $invoice->terms_conditions }}</div>
                @else
                    <div style="margin-bottom:2px;">1. Official Tax Invoice issued by {{ $firmDisplayName }} (GSTIN: {{ $firmGstNo }}).</div>
                    <div>2. Subject to Dahegam / Bharuch Jurisdiction.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Signatures Block -->
    <div class="inv-auth-grid">
        <div class="inv-auth-box" style="display: flex; flex-direction: column; justify-content: flex-end; align-items: center;">
            <div style="min-height: 48px; max-height: 52px;"></div>
            <div class="inv-auth-line" style="width: 100%; margin-top: 0;">Customer / Receiver Signature</div>
        </div>
        <div class="inv-auth-box" style="display: flex; flex-direction: column; justify-content: flex-end; align-items: center;">
            <div id="invSignWrapper" style="min-height: 48px; max-height: 52px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 3px;">
                <img id="invSignImg" src="{{ $signSrc }}" alt="Authorized Signature"
                     style="max-height: 46px; max-width: 140px; object-fit: contain; {{ $hasSign ? 'display:block;' : 'display:none;' }}">
            </div>
            <div class="inv-auth-line" style="width: 100%; margin-top: 0;">For <strong>{{ $firmDisplayName }}</strong><br><span style="font-size:7px; font-weight:600;">(Authorized Signatory)</span></div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="inv-footer">
        <span>Official GST Tax Invoice &bull; {{ $firmDisplayName }} &bull; GSTIN: {{ $firmGstNo }}</span>
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        <span>Computer Generated Document</span>
    </div>
</div>

<script>
    function handlePaymentModeChange(newMode) {
        // 1. Immediately update the display on the printed sheet (synchronous for instant printing)
        const docModeEl = document.getElementById('invDocPaymentMode');
        if (docModeEl) {
            docModeEl.textContent = (newMode || 'CASH').toUpperCase();
        }

        // 2. Show indicator
        const spinner = document.getElementById('saveModeSpinner');
        const success = document.getElementById('saveModeSuccess');
        if (spinner) spinner.style.display = 'inline-block';
        if (success) success.style.display = 'none';

        // 3. Persist via AJAX
        fetch('{{ route('invoices.update-payment-mode', $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ payment_mode: newMode })
        })
        .then(res => res.json())
        .then(data => {
            if (spinner) spinner.style.display = 'none';
            if (success) {
                success.style.display = 'inline-block';
                setTimeout(() => { success.style.display = 'none'; }, 2500);
            }
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            console.error('Error saving payment mode:', err);
        });
    }

    function handleSignatureUpload(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];

        // 1. Instant local preview on the printed invoice
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('invSignImg');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            const removeBtn = document.getElementById('removeSignBtn');
            if (removeBtn) removeBtn.style.display = 'inline-flex';
            const btnText = document.getElementById('signBtnText');
            if (btnText) btnText.textContent = 'Change Signature Photo';
        };
        reader.readAsDataURL(file);

        // 2. Upload to server via AJAX
        const spinner = document.getElementById('signUploadSpinner');
        const msgEl = document.getElementById('signUploadMsg');
        if (spinner) spinner.style.display = 'inline-block';
        if (msgEl) msgEl.style.display = 'none';

        const applyToFirmCheck = document.getElementById('applyToFirmCheck');
        const applyToFirm = applyToFirmCheck ? (applyToFirmCheck.checked ? '1' : '0') : '1';

        const formData = new FormData();
        formData.append('signature_image', file);
        formData.append('apply_to_firm', applyToFirm);

        fetch('{{ route('invoices.upload-signature', $invoice->id) }}', {
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
                const img = document.getElementById('invSignImg');
                if (img) img.src = data.signature_url;

                if (msgEl) {
                    msgEl.textContent = data.applied_to_firm
                        ? '✓ Saved for all ' + (data.firm_name || '') + ' invoices!'
                        : '✓ Saved for this invoice!';
                    msgEl.style.display = 'inline-block';
                    setTimeout(() => { msgEl.style.display = 'none'; }, 4000);
                }

                const badge = document.getElementById('signTypeBadge');
                if (badge) {
                    badge.textContent = data.applied_to_firm ? 'Firm Default Signature' : 'Custom Signature';
                    badge.style.display = 'inline-block';
                }
            }
        })
        .catch(err => {
            if (spinner) spinner.style.display = 'none';
            console.error('Error uploading signature:', err);
        });
    }

    function handleSignatureRemove() {
        if (!confirm('Remove digital signature from this invoice?')) return;

        const removeFromFirm = confirm('Also remove this signature from firm default so all invoices are reset?') ? '1' : '0';

        const img = document.getElementById('invSignImg');
        if (img) {
            img.src = '';
            img.style.display = 'none';
        }
        const removeBtn = document.getElementById('removeSignBtn');
        if (removeBtn) removeBtn.style.display = 'none';
        const btnText = document.getElementById('signBtnText');
        if (btnText) btnText.textContent = 'Upload Signature Photo';
        const badge = document.getElementById('signTypeBadge');
        if (badge) badge.style.display = 'none';

        fetch('{{ route('invoices.remove-signature', $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ remove_from_firm: removeFromFirm })
        }).catch(err => console.error('Error removing signature:', err));
    }
</script>

</body>
</html>