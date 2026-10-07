@php
    $isGstInvoice = ((float)($invoice->tax_amount ?? 0) > 0 || (float)($invoice->tax_percent ?? 0) > 0);
    $invHeading = $isGstInvoice ? 'TAX INVOICE' : 'INVOICE / BILL OF SUPPLY';
    $invSubtitle = $isGstInvoice ? 'Official GST Tax Invoice (Under Rule 46 of CGST Rules)' : 'Commercial Bill / Invoice';

    if (!function_exists('delawalaAmountInWords')) {
        function delawalaAmountInWords($number) {
            $number = (float)$number;
            if ($number <= 0) return 'INR Zero Only';

            $ones = [
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
                5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
                10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
                14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
                18 => 'Eighteen', 19 => 'Nineteen'
            ];
            $tens = [
                2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
                6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
            ];

            $getBelowHundred = function($num) use ($ones, $tens) {
                if ($num < 20) return $ones[$num] ?? '';
                $t = (int)($num / 10);
                $o = $num % 10;
                return trim(($tens[$t] ?? '') . ' ' . ($ones[$o] ?? ''));
            };

            $getBelowThousand = function($num) use ($ones, $getBelowHundred) {
                $h = (int)($num / 100);
                $r = $num % 100;
                $res = '';
                if ($h > 0) {
                    $res .= ($ones[$h] ?? '') . ' Hundred';
                    if ($r > 0) $res .= ' and ';
                }
                if ($r > 0) {
                    $res .= $getBelowHundred($r);
                }
                return trim($res);
            };

            $intVal = (int)floor($number);
            $fraction = (int)round(($number - $intVal) * 100);

            $crore = (int)floor($intVal / 10000000);
            $rem = $intVal % 10000000;

            $lakh = (int)floor($rem / 100000);
            $rem = $rem % 100000;

            $thousand = (int)floor($rem / 1000);
            $rem = $rem % 1000;

            $parts = [];
            if ($crore > 0) {
                $parts[] = $getBelowThousand($crore) . ' Crore';
            }
            if ($lakh > 0) {
                $parts[] = $getBelowHundred($lakh) . ' Lakh';
            }
            if ($thousand > 0) {
                $parts[] = $getBelowHundred($thousand) . ' Thousand';
            }
            if ($rem > 0) {
                $parts[] = $getBelowThousand($rem);
            }

            $rupees = implode(' ', array_filter($parts));
            $paise = ($fraction > 0) ? ' and ' . $getBelowHundred($fraction) . ' Paise' : '';

            return 'INR ' . trim($rupees ?: 'Zero') . $paise . ' Only';
        }
    }

    $activeFirm = $invoice->firm ?? (\App\Models\Firm::first());
    $firmDisplayName = $activeFirm->firm_name ?? 'DELAWALA PROPERTIES';
    $firmGstNo = $activeFirm->gst_no ?? '24CUBPD0770R1ZI';
    $firmAddress = $activeFirm->address ?? 'Ground Floor, F F SH No. 116, Aman Plazza, Dahegam Road';
    $firmCity = $activeFirm->city ?? 'Dahegam, Bharuch';
    $firmState = $activeFirm->state ?? 'Gujarat';
    $firmPin = $activeFirm->pincode ?? '392012';
    $firmOwner = $activeFirm->owner_name ?? 'Delawala Zafar';
    $firmPhone = $activeFirm->mobile ?? '7016517040';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invHeading }} - {{ $invoice->invoice_no ?? 'INV' }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
    <style>
        /* ── Formal Clean White GST Invoice Theme ── */
        html, body {
            background: #F1F5F9;
            color: #000000;
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 10.5px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        body {
            padding: 0 0 30px 0;
        }

        .invoice-print-sheet {
            width: 100%;
            max-width: 820px;
            margin: 0 auto;
            background: #FFFFFF;
            color: #000000;
            padding: 24px;
            box-sizing: border-box;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        /* ── Header ── */
        .inv-header-box {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 12px;
            gap: 14px;
        }
        .inv-brand-col {
            flex: 1.2;
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .inv-logo-img {
            height: 52px;
            width: auto;
            object-fit: contain;
            filter: grayscale(100%);
            flex-shrink: 0;
        }
        .inv-firm-name {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.15;
            color: #000000;
        }
        .inv-firm-sub {
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #222222;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .inv-firm-addr {
            font-size: 9px;
            color: #222222;
            line-height: 1.3;
        }
        .inv-firm-meta {
            display: flex;
            gap: 6px;
            margin-top: 5px;
            flex-wrap: wrap;
        }
        .inv-meta-pill {
            display: inline-block;
            border: 1px solid #000000;
            padding: 2px 7px;
            font-size: 8.5px;
            font-weight: 700;
            background: #FFFFFF;
            color: #000000;
            white-space: nowrap;
            border-radius: 2px;
        }

        .inv-title-col {
            flex: 0.8;
            min-width: 0;
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        .inv-main-title {
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 2px solid #000000;
            padding: 4px 12px;
            background: #FFFFFF;
            color: #000000;
            display: inline-block;
            margin-bottom: 3px;
            border-radius: 2px;
        }
        .inv-rule-sub {
            font-size: 8.5px;
            font-weight: 600;
            color: #333333;
            margin-bottom: 3px;
        }
        .inv-doc-info {
            font-size: 9.5px;
            color: #111111;
            line-height: 1.3;
        }
        .inv-doc-info strong {
            font-weight: 700;
            color: #000000;
        }

        /* ── 2-Column Details Box ── */
        .inv-grid-2 {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }
        .inv-col-box {
            flex: 1;
            min-width: 0;
            border: 1.5px solid #000000;
            background: #FFFFFF;
            box-sizing: border-box;
            border-radius: 2px;
        }
        .inv-box-head {
            background: #FFFFFF;
            color: #000000;
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 5px 8px;
            border-bottom: 1.5px solid #000000;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .inv-box-body {
            padding: 7px 9px;
        }
        .inv-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 9.5px;
            border-bottom: 1px dotted #D1D5DB;
            gap: 6px;
        }
        .inv-row:last-child {
            border-bottom: none;
        }
        .inv-lbl {
            color: #333333;
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
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #000000;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        table.inv-items-tbl {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 10px;
            border: 1.5px solid #000000;
            table-layout: fixed;
            background: #FFFFFF;
        }
        table.inv-items-tbl thead th {
            background: #FFFFFF;
            color: #000000;
            padding: 6px 7px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000000;
            border-bottom: 2px solid #000000;
            text-align: left;
            overflow: hidden;
        }
        table.inv-items-tbl thead th.c, table.inv-items-tbl tbody td.c { text-align: center; }
        table.inv-items-tbl thead th.r, table.inv-items-tbl tbody td.r, table.inv-items-tbl tfoot td.r { text-align: right; }
        
        table.inv-items-tbl tbody td {
            background: #FFFFFF;
            padding: 6px 7px;
            border: 1px solid #000000;
            color: #000000;
            vertical-align: top;
            word-break: break-word;
        }
        table.inv-items-tbl tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
        table.inv-items-tbl tfoot td {
            background: #FFFFFF;
            padding: 5px 7px;
            border: 1px solid #000000;
            color: #000000;
            font-size: 9.5px;
            word-break: break-word;
        }
        .tfoot-grand-total {
            background: #FFFFFF !important;
            color: #000000 !important;
            font-weight: 900 !important;
            font-size: 10.5px !important;
        }
        .tfoot-grand-total td {
            color: #000000 !important;
            border-top: 2px solid #000000 !important;
            border-bottom: 2px solid #000000 !important;
            font-size: 10.5px !important;
            font-weight: 900 !important;
            padding: 6px 7px !important;
        }

        /* ── Words & Remittance Grid ── */
        .inv-words-bar {
            border: 1.5px solid #000000;
            padding: 6px 9px;
            margin-bottom: 12px;
            background: #FFFFFF;
            font-size: 9.5px;
            color: #000000;
            word-break: break-word;
            border-radius: 2px;
        }
        .inv-words-bar strong {
            font-weight: 800;
            color: #000000;
        }

        .inv-bank-terms-grid {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
        }

        /* ── Auth Signatures ── */
        .inv-auth-grid {
            display: flex;
            justify-content: space-between;
            margin-top: 16px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .inv-auth-box {
            width: 200px;
            text-align: center;
        }
        .inv-auth-line {
            border-top: 1.5px solid #000000;
            margin-top: 32px;
            padding-top: 4px;
            font-size: 9px;
            font-weight: 700;
            color: #000000;
        }

        /* ── Footer ── */
        .inv-footer {
            border-top: 1px solid #000000;
            padding-top: 4px;
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #444444;
        }

        @media print {
            html, body {
                background: #FFFFFF !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .invoice-print-sheet {
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .delawala-pdf-toolbar, .no-print {
                display: none !important;
            }
            .inv-main-title {
                background: #FFFFFF !important;
                color: #000000 !important;
                border: 2px solid #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .inv-box-head {
                background: #FFFFFF !important;
                color: #000000 !important;
                border-bottom: 1.5px solid #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            table.inv-items-tbl thead th {
                background: #FFFFFF !important;
                color: #000000 !important;
                border: 1px solid #000000 !important;
                border-bottom: 2px solid #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .tfoot-grand-total td {
                background: #FFFFFF !important;
                color: #000000 !important;
                border-top: 2px solid #000000 !important;
                border-bottom: 2px solid #000000 !important;
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

<div id="delawala-printable-area" class="delawala-printable-area invoice-print-sheet">
    <!-- Header Section -->
    <div class="inv-header-box">
        <div class="inv-brand-col">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $firmDisplayName }}" class="inv-logo-img" onerror="this.style.display='none';">
            <div>
                <div class="inv-firm-name">{{ $firmDisplayName }}</div>
                <div class="inv-firm-sub">Delawala Infra Co. &bull; Real Estate &amp; Infrastructure</div>
                <div class="inv-firm-addr">
                    {{ $firmAddress }}, {{ $firmCity }}, {{ $firmState }} - {{ $firmPin }}
                </div>
                <div class="inv-firm-meta">
                    <span class="inv-meta-pill"><strong>GSTIN:</strong> {{ $firmGstNo }}</span>
                    <span class="inv-meta-pill"><strong>State:</strong> {{ $firmState }} (24)</span>
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
            <div class="inv-doc-info"><strong>Invoice No:</strong> {{ $invoice->invoice_no ?? '—' }}</div>
            <div class="inv-doc-info"><strong>Invoice Date:</strong> {{ isset($invoice->invoice_date) ? $invoice->invoice_date->format('d M Y') : now()->format('d M Y') }}</div>
            @if(!empty($invoice->due_date))
                <div class="inv-doc-info"><strong>Due Date:</strong> {{ $invoice->due_date->format('d M Y') }}</div>
            @endif
            <div class="inv-doc-info"><strong>Place of Supply:</strong> Gujarat (24)</div>
            <div class="inv-doc-info"><strong>Reverse Charge:</strong> No</div>
        </div>
    </div>

    <!-- Receiver & Supply Info Grid -->
    <div class="inv-grid-2">
        <div class="inv-col-box">
            <div class="inv-box-head"><i class="fa-solid fa-user"></i> Details of Receiver / Billed To</div>
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
            <div class="inv-box-head"><i class="fa-solid fa-building"></i> Supply &amp; Project Details</div>
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
    <div class="inv-table-sec-title"><i class="fa-solid fa-list-check"></i> Particulars of Goods &amp; Services Supplied</div>
    <table class="inv-items-tbl">
        <thead>
            <tr>
                <th style="width:24px;" class="c">#</th>
                <th style="width:auto;">Item Description / Particulars</th>
                <th class="c" style="width:65px;">HSN/SAC</th>
                <th class="r" style="width:70px;">Qty</th>
                <th class="r" style="width:110px;">Rate (₹)</th>
                <th class="r" style="width:125px;">Taxable (₹)</th>
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
                                <div style="font-size:8.5px; color:#444444;">{{ $item->notes }}</div>
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
                        <strong>Real Estate Construction &amp; Infrastructure Services</strong>
                        <div style="font-size:8.5px; color:#444444;">Plotted Land Development &amp; Property Consideration</div>
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
                <td colspan="5" class="r" style="font-size:10px; font-weight:800;">TOTAL INVOICE VALUE (IN FIGURES)</td>
                <td class="r" style="font-size:11px; font-weight:900;">₹{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
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
            <div class="inv-box-head"><i class="fa-solid fa-building-columns"></i> Bank Remittance Details</div>
            <div class="inv-box-body">
                <div class="inv-row"><span class="inv-lbl">Account Name:</span><span class="inv-val">DELAWALA INFRA CO.</span></div>
                <div class="inv-row"><span class="inv-lbl">Bank Name:</span><span class="inv-val">{{ $invoice->bank_name ?: ($invoice->firm->bank_name ?? 'Bank of Baroda') }}</span></div>
                <div class="inv-row"><span class="inv-lbl">Account Number:</span><span class="inv-val">{{ $invoice->bank_account_no ?: ($invoice->firm->bank_account_no ?? '—') }}</span></div>
                <div class="inv-row"><span class="inv-lbl">IFSC Code:</span><span class="inv-val">{{ $invoice->bank_ifsc ?: ($invoice->firm->bank_ifsc ?? '—') }}</span></div>
                <div class="inv-row"><span class="inv-lbl">Branch:</span><span class="inv-val">{{ $invoice->bank_branch ?: 'Dahegam Branch' }}</span></div>
            </div>
        </div>

        <div class="inv-col-box" style="flex:1;">
            <div class="inv-box-head"><i class="fa-solid fa-file-contract"></i> TERMS &amp; CONDITIONS</div>
            <div class="inv-box-body" style="font-size:9.5px; color:#111111; line-height:1.45;">
                @if(!empty($invoice->terms_conditions))
                    <div style="white-space: pre-line;">{{ $invoice->terms_conditions }}</div>
                @else
                    <div style="margin-bottom:3px;"><strong>1. Payment Terms:</strong> Payment due on or before specified due date via Cheque / RTGS / NEFT to company account.</div>
                    <div style="margin-bottom:3px;"><strong>2. Interest on Delay:</strong> Overdue payments shall attract interest @ 18% p.a. from due date until realization.</div>
                    <div style="margin-bottom:3px;"><strong>3. Tax Declaration:</strong> We declare that this invoice shows the actual price and all particulars are true and correct.</div>
                    <div><strong>4. Jurisdiction:</strong> All disputes are subject to <strong>Dahegam / Bharuch Jurisdiction</strong> only.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Signatures Block -->
    <div class="inv-auth-grid">
        <div class="inv-auth-box">
            <div class="inv-auth-line">Customer / Receiver Signature</div>
        </div>
        <div class="inv-auth-box">
            <div class="inv-auth-line">For <strong>DELAWALA INFRA CO.</strong><br><span style="font-size:8px; font-weight:600;">(Authorized Signatory)</span></div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="inv-footer">
        <span>Official GST Tax Invoice &bull; Delawala Infra Co. &bull; GSTIN: {{ $firmGstNo }}</span>
        <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        <span>Computer Generated Document</span>
    </div>
</div>

</body>
</html>