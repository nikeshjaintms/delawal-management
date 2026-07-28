<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Inward - {{ isset($inwardGroup) ? $inwardGroup->inward_number : 'Manual Inward' }}</title>
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

        .btn-outline { border:1px solid #C5A87E; background:#fff; color:#C5A87E; padding:8px 16px; border-radius:6px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:12px; font-weight:600; margin-bottom: 20px; }
        .btn-outline:hover { background:#FAFAFA; }

        @media print {
            body { padding: 15px; }
            @page { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="display: flex; gap: 10px; justify-content: space-between;">
        <a href="{{ route('stock-inwards.index') }}" class="btn-outline">← Back to List</a>
        <button onclick="window.print()" class="btn-outline" style="background:#059669; color:#FFF; border-color:#059669;">Print Document</button>
    </div>

    <!-- PO Document Header -->
    <div class="report-header">
        <div class="company-block">
            <div class="company-name">{{ isset($inwardGroup) ? ($inwardGroup->firm->firm_name ?? 'Delawala') : ($stockInward->firm->firm_name ?? 'Delawala') }}</div>
            <div class="company-sub">Inward Material Inspection Report (IMIR)</div>
        </div>
        <div class="report-meta">
            <div class="report-title">{{ isset($inwardGroup) ? ($inwardGroup->inward_number ?: 'IMIR-'.$inwardGroup->id) : ('IMIR-'.$stockInward->id) }}</div>
            <div class="report-date">Date: {{ isset($inwardGroup) ? $inwardGroup->inward_date->format('d M Y') : $stockInward->inward_date->format('d M Y') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-title">Supplier Details</div>
            <div class="info-row"><span class="info-label">Name:</span><span class="info-value">{{ isset($inwardGroup) ? ($inwardGroup->supplier_name ?: '—') : ($stockInward->supplier_name ?: '—') }}</span></div>
            <div class="info-row"><span class="info-label">Invoice / Bill No:</span><span class="info-value">{{ isset($inwardGroup) ? ($inwardGroup->bill_no ?: '—') : ($stockInward->bill_no ?: '—') }}</span></div>
            <div class="info-row"><span class="info-label">Challan Number:</span><span class="info-value">{{ isset($inwardGroup) ? ($inwardGroup->challan_no ?: '—') : ($stockInward->challan_no ?? '—') }}</span></div>
        </div>
        <div class="info-box">
            <div class="info-title">Delivery &amp; Shipment Details</div>
            <div class="info-row"><span class="info-label">Reference PO:</span><span class="info-value">{{ isset($inwardGroup) && $inwardGroup->purchaseOrder ? $inwardGroup->purchaseOrder->po_number : 'Manual Entry' }}</span></div>
            <div class="info-row"><span class="info-label">Vehicle Number:</span><span class="info-value">{{ isset($inwardGroup) ? ($inwardGroup->vehicle_no ?: '—') : ($stockInward->vehicle_no ?? '—') }}</span></div>
            <div class="info-row"><span class="info-label">Warehouse Name:</span><span class="info-value">{{ isset($inwardGroup) ? ($inwardGroup->warehouse ?: 'Main Warehouse') : ($stockInward->warehouse ?? 'Main Warehouse') }}</span></div>
        </div>
    </div>

    @if(isset($inwards))
    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th>Item Description</th>
                <th class="num" style="width:12%;">Ordered</th>
                <th class="num" style="width:12%;">Received</th>
                <th class="num" style="width:12%;">Damaged</th>
                <th class="num" style="width:12%;">Accepted</th>
                <th class="num" style="width:15%;">Unit Rate</th>
                <th class="num" style="width:15%;">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subTotal = 0;
                $discount = 0;
                $taxable = 0;
                $cgst = 0;
                $sgst = 0;
                $igst = 0;
                $grandTotal = 0;
            @endphp
            @foreach($inwards as $index => $item)
            @php
                $subTotal += ($item->quantity * $item->rate);
                $discount += $item->discount_amount;
                $taxable += ($item->quantity * $item->rate) - $item->discount_amount;
                if($item->gst_pct > 0) {
                    // Check if interstate
                    $po = $item->purchaseOrder;
                    $isInter = false;
                    if ($po && $po->vendor && $po->firm) {
                        $vState = strtolower(trim($po->vendor->state ?? ''));
                        $fState = strtolower(trim($po->firm->state ?? ''));
                        if (!empty($vState) && !empty($fState) && $vState !== $fState) {
                            $isInter = true;
                        }
                    }
                    if ($isInter) {
                        $igst += $item->gst_amount;
                    } else {
                        $cgst += ($item->gst_amount / 2);
                        $sgst += ($item->gst_amount / 2);
                    }
                }
                $grandTotal += $item->total_amount;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $item->material->material_name ?? '-' }}</strong></td>
                <td class="num">{{ number_format($item->qty_ordered, 2) }} {{ $item->material->unit ?? '' }}</td>
                <td class="num">{{ number_format($item->quantity, 2) }} {{ $item->material->unit ?? '' }}</td>
                <td class="num" style="color:{{ $item->qty_damaged > 0 ? '#DC2626' : '#0F1F35' }};">{{ number_format($item->qty_damaged, 2) }}</td>
                <td class="num" style="color:#059669; font-weight:600;">{{ number_format($item->quantity - $item->qty_damaged, 2) }}</td>
                <td class="num">₹{{ number_format($item->rate, 2) }}</td>
                <td class="num" style="font-weight:600;">₹{{ number_format($item->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-wrapper">
        <div class="summary-box">
            <div class="summary-row"><span>Sub Total:</span><span>₹{{ number_format($subTotal, 2) }}</span></div>
            <div class="summary-row"><span>Discount:</span><span>₹{{ number_format($discount, 2) }}</span></div>
            <div class="summary-row"><span>Taxable Value:</span><span>₹{{ number_format($taxable, 2) }}</span></div>
            @if($igst > 0)
                <div class="summary-row"><span>IGST:</span><span>₹{{ number_format($igst, 2) }}</span></div>
            @else
                <div class="summary-row"><span>CGST:</span><span>₹{{ number_format($cgst, 2) }}</span></div>
                <div class="summary-row"><span>SGST:</span><span>₹{{ number_format($sgst, 2) }}</span></div>
            @endif
            <div class="summary-row"><span>Grand Total:</span><span>₹{{ number_format($grandTotal, 2) }}</span></div>
        </div>
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:10%;">#</th>
                <th>Material Description</th>
                <th class="num" style="width:30%;">Quantity</th>
                <th class="num" style="width:30%;">Rate</th>
                <th class="num" style="width:30%;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><strong>{{ $stockInward->material->material_name ?? '-' }}</strong></td>
                <td class="num">{{ number_format($stockInward->quantity, 2) }} {{ $stockInward->material->unit ?? '' }}</td>
                <td class="num">₹{{ number_format($stockInward->rate, 2) }}</td>
                <td class="num" style="font-weight:700; color:#059669;">₹{{ number_format($stockInward->total_amount ?: ($stockInward->quantity * $stockInward->rate), 2) }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(isset($inwardGroup) && $inwardGroup->remarks)
    <div class="remarks-box">
        <div class="remarks-title">Notes / Remarks</div>
        <p style="white-space:pre-wrap; line-height:1.5;">{{ $inwardGroup->remarks }}</p>
    </div>
    @elseif(isset($stockInward) && $stockInward->remarks)
    <div class="remarks-box">
        <div class="remarks-title">Notes / Remarks</div>
        <p style="white-space:pre-wrap; line-height:1.5;">{{ $stockInward->remarks }}</p>
    </div>
    @endif

    <div class="report-footer">
        <span>Inward Material Inspection Report (IMIR) Log</span>
        <span>Authorized Signature: __________________________</span>
    </div>

    @if(isset($printMode) && $printMode)
        <script>
            window.onload = function() {
                window.print();
            }
        </script>
    @endif

</body>
</html>
