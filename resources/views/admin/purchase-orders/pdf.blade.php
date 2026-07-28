<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ isset($purchaseOrder) ? $purchaseOrder->po_number : 'Report' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11.5px; color: #0F1F35; background: #fff; padding: 30px; }

        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #C5A87E; }
        .company-block .company-name { font-size: 24px; font-weight: 800; color: #0F1F35; letter-spacing: 0.5px; }
        .company-block .company-sub  { font-size: 11px; color: #C5A87E; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .report-meta { text-align: right; }
        .report-meta .report-title { font-size: 18px; font-weight: 800; color: #0F1F35; margin-bottom: 4px; }
        .report-meta .report-date  { font-size: 11px; color: #64748B; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; background: #FAFAFA; }
        .info-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748B; margin-bottom: 8px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 4px;}
        .info-row { display: flex; margin-bottom: 4px; }
        .info-label { width: 110px; font-weight: 600; color: #64748B; }
        .info-value { flex: 1; color: #0F1F35; font-weight: 500; }

        table { width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px; }
        thead tr { background: #0F1F35; }
        thead th { padding: 8px 10px; color: #FFFFFF; font-weight: 600; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        thead th.num { text-align: right; }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        tbody td.num { text-align: right; }
        tbody tr:last-child td { border-bottom: none; }

        .summary-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }
        .summary-box { width: 280px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px; background: #FAFAFA; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #E5E7EB; font-size: 11px; }
        .summary-row:last-child { border-bottom: none; font-weight: 700; font-size: 13px; color: #059669; }

        .remarks-box { margin-top: 30px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px; background: #FAFAFA; }
        .remarks-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748B; margin-bottom: 6px; }

        .report-footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #E5E7EB; display: flex; justify-content: space-between; color: #9CA3AF; font-size: 9.5px; }

        @media print {
            body { padding: 15px; }
            @page { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    @if(isset($listMode) && $listMode)
        <!-- List Report Header -->
        <div class="report-header">
            <div class="company-block">
                <div class="company-name">Delawala</div>
                <div class="company-sub">Properties &amp; Management</div>
            </div>
            <div class="report-meta">
                <div class="report-title">Purchase Orders Report</div>
                <div class="report-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Firm</th>
                    <th>Supplier</th>
                    <th>PO Date</th>
                    <th>Delivery Date</th>
                    <th>Status</th>
                    <th class="num">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrders as $po)
                <tr>
                    <td><strong>{{ $po->po_number }}</strong></td>
                    <td>{{ $po->firm->firm_name ?? '-' }}</td>
                    <td>{{ $po->vendor->name ?? '-' }}</td>
                    <td>{{ $po->po_date ? $po->po_date->format('d M Y') : '-' }}</td>
                    <td>{{ $po->delivery_date ? $po->delivery_date->format('d M Y') : '—' }}</td>
                    <td>{{ $po->status }}</td>
                    <td class="num" style="font-weight:600;">₹{{ number_format($po->grand_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="report-footer">
            <span>Delawala Management System</span>
            <span>Page 1 of 1</span>
        </div>
    @else
        <!-- Single PO Document -->
        <div class="report-header">
            <div class="company-block">
                <div class="company-name">{{ $purchaseOrder->firm->firm_name ?? 'Delawala' }}</div>
                <div class="company-sub">Purchase Order Contract</div>
            </div>
            <div class="report-meta">
                <div class="report-title">{{ $purchaseOrder->po_number }}</div>
                <div class="report-date">Date: {{ $purchaseOrder->po_date->format('d M Y') }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <div class="info-title">Supplier Details</div>
                <div class="info-row"><span class="info-label">Name:</span><span class="info-value">{{ $purchaseOrder->vendor->name ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">GSTIN:</span><span class="info-value">{{ $purchaseOrder->vendor->gst_no ?? 'N/A' }}</span></div>
                <div class="info-row"><span class="info-label">Mobile:</span><span class="info-value">{{ $purchaseOrder->vendor->mobile ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">Address:</span><span class="info-value">{{ $purchaseOrder->vendor->address ?? '-' }}, {{ $purchaseOrder->vendor->city ?? '' }}</span></div>
            </div>
            <div class="info-box">
                <div class="info-title">Delivery &amp; Status</div>
                <div class="info-row"><span class="info-label">Order Number:</span><span class="info-value"><strong>{{ $purchaseOrder->po_number }}</strong></span></div>
                <div class="info-row"><span class="info-label">Expected Date:</span><span class="info-value">{{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d M Y') : '—' }}</span></div>
                <div class="info-row"><span class="info-label">Order Status:</span><span class="info-value">{{ $purchaseOrder->status }}</span></div>
                <div class="info-row"><span class="info-label">Created By:</span><span class="info-value">{{ $purchaseOrder->creator->name ?? '-' }}</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Item Description</th>
                    <th class="num" style="width:12%;">Qty</th>
                    <th class="num" style="width:15%;">Rate</th>
                    <th class="num" style="width:10%;">Disc %</th>
                    <th class="num" style="width:10%;">GST %</th>
                    <th class="num" style="width:15%;">GST Amount</th>
                    <th class="num" style="width:15%;">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->material->material_name ?? '-' }}</strong></td>
                    <td class="num">{{ number_format($item->qty, 2) }} {{ $item->material->unit ?? '' }}</td>
                    <td class="num">₹{{ number_format($item->rate, 2) }}</td>
                    <td class="num">{{ number_format($item->discount_pct, 2) }}%</td>
                    <td class="num">{{ number_format($item->gst_pct, 2) }}%</td>
                    <td class="num">₹{{ number_format($item->gst_amount, 2) }}</td>
                    <td class="num" style="font-weight:600;">₹{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="summary-wrapper">
            <div class="summary-box">
                <div class="summary-row"><span>Sub Total:</span><span>₹{{ number_format($purchaseOrder->sub_total, 2) }}</span></div>
                <div class="summary-row"><span>Discount:</span><span>₹{{ number_format($purchaseOrder->discount_amount, 2) }}</span></div>
                <div class="summary-row"><span>Taxable Value:</span><span>₹{{ number_format($purchaseOrder->taxable_amount, 2) }}</span></div>
                @if($purchaseOrder->igst_amount > 0)
                    <div class="summary-row"><span>IGST:</span><span>₹{{ number_format($purchaseOrder->igst_amount, 2) }}</span></div>
                @else
                    <div class="summary-row"><span>CGST:</span><span>₹{{ number_format($purchaseOrder->cgst_amount, 2) }}</span></div>
                    <div class="summary-row"><span>SGST:</span><span>₹{{ number_format($purchaseOrder->sgst_amount, 2) }}</span></div>
                @endif
                <div class="summary-row"><span>Grand Total:</span><span>₹{{ number_format($purchaseOrder->grand_total, 2) }}</span></div>
            </div>
        </div>

        @if($purchaseOrder->remarks)
        <div class="remarks-box">
            <div class="remarks-title">Terms, Conditions &amp; Notes</div>
            <p style="white-space:pre-wrap; line-height:1.5;">{{ $purchaseOrder->remarks }}</p>
        </div>
        @endif

        <div class="report-footer">
            <span>Delawala Management Purchase Order System</span>
            <span>Authorized Signature: __________________________</span>
        </div>
    @endif

    @if(isset($printMode) && $printMode)
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif

</body>
</html>
