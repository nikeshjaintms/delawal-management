<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - {{ $invoice->invoice_no }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #F1F5F9;
            color: #1E293B;
            font-size: 13px;
            padding: 30px 15px;
        }

        .print-actions {
            max-width: 820px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-p {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-p-primary { background: #2563EB; color: #FFFFFF; }
        .btn-p-primary:hover { background: #1D4ED8; }
        .btn-p-back { background: #E2E8F0; color: #334155; }
        .btn-p-back:hover { background: #CBD5E1; }

        .invoice-sheet {
            max-width: 820px;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 40px;
            position: relative;
        }

        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #E2E8F0;
            margin-bottom: 24px;
        }
        .firm-name { font-size: 24px; font-weight: 800; color: #0F172A; margin-bottom: 4px; }
        .firm-meta { color: #64748B; font-size: 12.5px; line-height: 1.5; }
        .tax-invoice-label {
            font-size: 26px;
            font-weight: 900;
            color: #2563EB;
            text-align: right;
            letter-spacing: 0.5px;
        }
        .inv-number { font-size: 15px; font-weight: 800; color: #0F172A; margin-top: 4px; }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
            padding: 16px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
        }
        .block-title { font-size: 10.5px; font-weight: 800; text-transform: uppercase; color: #64748B; letter-spacing: 0.8px; margin-bottom: 6px; }
        .block-name { font-size: 15px; font-weight: 700; color: #0F172A; margin-bottom: 3px; }
        .block-text { font-size: 12.5px; color: #475569; line-height: 1.5; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table th {
            background: #F1F5F9;
            color: #334155;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 10px 12px;
            border: 1px solid #CBD5E1;
        }
        .items-table td {
            padding: 10px 12px;
            border: 1px solid #E2E8F0;
            font-size: 12.5px;
            color: #1E293B;
        }

        .summary-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .bank-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px;
            font-size: 12px;
            line-height: 1.6;
        }
        .calc-table {
            width: 100%;
            border-collapse: collapse;
        }
        .calc-table td {
            padding: 6px 10px;
            font-size: 12.5px;
        }
        .calc-table tr.total td {
            font-size: 15px;
            font-weight: 800;
            color: #0F172A;
            border-top: 2px solid #0F172A;
            border-bottom: 2px solid #0F172A;
            padding: 8px 10px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
            align-items: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }
        .terms-text { font-size: 11.5px; color: #64748B; line-height: 1.5; white-space: pre-line; }
        .sign-box {
            text-align: right;
            padding-top: 40px;
            border-top: 1px dashed #94A3B8;
            font-size: 12px;
            font-weight: 700;
            color: #334155;
        }

        @media print {
            body { background: #FFFFFF; padding: 0; }
            .print-actions { display: none !important; }
            .invoice-sheet { box-shadow: none; border-radius: 0; padding: 20px 0; }
        }
    </style>
</head>
<body>

<div class="print-actions">
    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-p btn-p-back">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
    <button onclick="window.print()" class="btn-p btn-p-primary">
        <i class="fa-solid fa-print"></i> Print Invoice
    </button>
</div>

<div class="invoice-sheet">
    <!-- Header -->
    <div class="inv-header">
        <div>
            <div class="firm-name">{{ $invoice->firm->firm_name ?? 'Delawala Management' }}</div>
            <div class="firm-meta">
                <div>{{ $invoice->firm->address ?? '' }}{{ $invoice->firm->city ? ', ' . $invoice->firm->city : '' }}</div>
                @if($invoice->firm && $invoice->firm->mobile)
                    <div>Phone: {{ $invoice->firm->mobile }}</div>
                @endif
                @if($invoice->firm && $invoice->firm->gst_number)
                    <div><strong>GSTIN:</strong> {{ $invoice->firm->gst_number }}</div>
                @endif
            </div>
        </div>

        <div style="text-align: right;">
            <div class="tax-invoice-label">TAX INVOICE</div>
            <div class="inv-number">{{ $invoice->invoice_no }}</div>
            <div style="color: #64748B; font-size: 12px; margin-top: 4px;">
                Date: <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong>
            </div>
            @if($invoice->due_date)
                <div style="color: #64748B; font-size: 12px;">
                    Due Date: <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>
                </div>
            @endif
        </div>
    </div>

    <!-- Metadata: Bill To & Project Info -->
    <div class="meta-grid">
        <div>
            <div class="block-title">Bill To:</div>
            <div class="block-name">{{ $invoice->recipient_name }}</div>
            <div class="block-text">
                @if($invoice->recipient_phone)
                    <div>Phone: {{ $invoice->recipient_phone }}</div>
                @endif
                @if($invoice->recipient_email)
                    <div>Email: {{ $invoice->recipient_email }}</div>
                @endif
                @if($invoice->recipient_address)
                    <div>Address: {{ $invoice->recipient_address }}</div>
                @endif
                @if($invoice->recipient_gstin)
                    <div><strong>GSTIN:</strong> {{ $invoice->recipient_gstin }}</div>
                @endif
            </div>
        </div>

        <div>
            <div class="block-title">Project &amp; Invoice Nature:</div>
            @if($invoice->project)
                <div class="block-name">{{ $invoice->project->project_name }} ({{ $invoice->project->project_code }})</div>
                <div class="block-text">
                    <div>Type: {{ ucfirst($invoice->project->project_type ?: 'Plotted Development') }}</div>
                    <div>Location: {{ $invoice->project->display_address }}</div>
                </div>
            @else
                <div class="block-name">General / Standalone Invoice</div>
            @endif
            <div style="margin-top: 6px; font-size: 11.5px; color: #2563EB; font-weight: 700;">
                Category: {{ $invoice->type_label }}
            </div>
        </div>
    </div>

    <!-- Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px; text-align: center;">#</th>
                <th>Item Description</th>
                <th style="width: 80px; text-align: center;">HSN/SAC</th>
                <th style="width: 70px; text-align: right;">Qty</th>
                <th style="width: 85px; text-align: right;">Rate (₹)</th>
                <th style="width: 75px; text-align: right;">Disc (₹)</th>
                <th style="width: 100px; text-align: right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
                <tr>
                    <td style="text-align: center; color: #64748B;">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->item_description }}</strong>
                    </td>
                    <td style="text-align: center; color: #64748B;">{{ $item->hsn_sac_code ?: '—' }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ $item->discount_amount > 0 ? number_format($item->discount_amount, 2) : '—' }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Financial summary & Bank Box -->
    <div class="summary-wrapper">
        <div class="bank-box">
            <div style="font-weight: 800; color: #0F172A; margin-bottom: 6px; text-transform: uppercase; font-size: 11px;">
                <i class="fa-solid fa-building-columns"></i> Bank Account for Remittance:
            </div>
            <div>Bank: <strong>{{ $invoice->bank_name ?: ($invoice->firm->bank_name ?? '—') }}</strong></div>
            <div>A/C No: <strong>{{ $invoice->bank_account_no ?: ($invoice->firm->bank_account_no ?? '—') }}</strong></div>
            <div>IFSC Code: <strong>{{ $invoice->bank_ifsc ?: ($invoice->firm->bank_ifsc ?? '—') }}</strong></div>
            @if($invoice->bank_branch || ($invoice->firm && $invoice->firm->bank_branch))
                <div>Branch: {{ $invoice->bank_branch ?: $invoice->firm->bank_branch }}</div>
            @endif
        </div>

        <div>
            <table class="calc-table">
                <tr>
                    <td>Subtotal:</td>
                    <td style="text-align: right; font-weight: 700;">₹{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td>Discount:</td>
                        <td style="text-align: right; color: #D97706;">-₹{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->tax_type === 'gst_intra' && $invoice->tax_amount > 0)
                    <tr>
                        <td>CGST ({{ $invoice->tax_percent / 2 }}%):</td>
                        <td style="text-align: right;">₹{{ number_format($invoice->cgst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>SGST ({{ $invoice->tax_percent / 2 }}%):</td>
                        <td style="text-align: right;">₹{{ number_format($invoice->sgst_amount, 2) }}</td>
                    </tr>
                @elseif($invoice->tax_type === 'gst_inter' && $invoice->tax_amount > 0)
                    <tr>
                        <td>IGST ({{ $invoice->tax_percent }}%):</td>
                        <td style="text-align: right;">₹{{ number_format($invoice->igst_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->round_off != 0)
                    <tr>
                        <td>Round Off:</td>
                        <td style="text-align: right;">{{ $invoice->round_off > 0 ? '+' : '' }}₹{{ number_format($invoice->round_off, 2) }}</td>
                    </tr>
                @endif
                <tr class="total">
                    <td>Total Amount:</td>
                    <td style="text-align: right;">₹{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: #059669; font-weight: 700;">Amount Paid:</td>
                    <td style="text-align: right; color: #059669; font-weight: 700;">₹{{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="color: {{ $invoice->balance_amount > 0 ? '#DC2626' : '#64748B' }}; font-weight: 800;">Balance Due:</td>
                    <td style="text-align: right; color: {{ $invoice->balance_amount > 0 ? '#DC2626' : '#64748B' }}; font-weight: 800;">
                        ₹{{ number_format($invoice->balance_amount, 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer Terms & Signatory -->
    <div class="footer-grid">
        <div>
            <div style="font-weight: 800; color: #0F172A; margin-bottom: 4px; text-transform: uppercase; font-size: 11px;">
                Terms &amp; Conditions:
            </div>
            <div class="terms-text">{{ $invoice->terms_conditions ?: '1. Payment due within specified period. 2. Subject to local jurisdiction.' }}</div>
        </div>

        <div class="sign-box">
            <div>For <strong>{{ $invoice->firm->firm_name ?? 'Delawala Management' }}</strong></div>
            <div style="margin-top: 30px;">Authorized Signatory</div>
        </div>
    </div>
</div>

</body>
</html>
