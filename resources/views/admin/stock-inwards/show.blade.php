<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Inward - {{ isset($inwardGroup) ? $inwardGroup->inward_number : 'Manual Inward' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Outfit', 'Segoe UI', Arial, sans-serif; font-size: 11.5px; color: #FFFFFF; background: #0D0D14; padding: 30px; }

        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid rgba(96, 165, 250, 0.40); }
        .company-block .company-name { font-size: 24px; font-weight: 800; color: #FFFFFF; letter-spacing: 0.5px; }
        .company-block .company-sub  { font-size: 11px; color: #60A5FA; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .report-meta { text-align: right; }
        .report-meta .report-title { font-size: 18px; font-weight: 800; color: #FFFFFF; margin-bottom: 4px; }
        .report-meta .report-date  { font-size: 11px; color: rgba(255, 255, 255, 0.70); }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { border: 1px solid #22222E; border-radius: 10px; padding: 16px 20px; background: #0D0D14; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4); }
        .info-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #60A5FA; margin-bottom: 10px; border-bottom: 1px dashed #2A2A38; padding-bottom: 6px;}
        .info-row { display: flex; margin-bottom: 6px; }
        .info-label { width: 130px; font-weight: 600; color: rgba(255, 255, 255, 0.65); }
        .info-value { flex: 1; color: #FFFFFF; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; background: #08080C; border: 1px solid #22222E; border-radius: 10px; overflow: hidden; }
        thead tr { background: #14141C; }
        thead th { padding: 12px 14px; color: #FFFFFF; font-weight: 700; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap; border-bottom: 1.5px solid #2A2A36; }
        thead th.num { text-align: right; }
        tbody tr { background: transparent; }
        tbody tr:nth-child(even) { background: rgba(255, 255, 255, 0.02); }
        tbody td { padding: 12px 14px; border-bottom: 1px solid #1E1E28; vertical-align: middle; color: #FFFFFF; }
        tbody td.num { text-align: right; }
        tbody tr:last-child td { border-bottom: none; }

        .summary-wrapper { display: flex; justify-content: flex-end; margin-top: 20px; }
        .summary-box { width: 320px; border: 1px solid #262636; border-radius: 12px; padding: 18px; background: #0D0D14; color: #FFFFFF; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.4); }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #1E1E2A; font-size: 12.5px; color: rgba(255, 255, 255, 0.75); }
        .summary-row span:last-child { color: #FFFFFF; font-weight: 700; }
        .summary-row:last-child { border-bottom: none; border-top: 1px dashed #3B82F6; margin-top: 4px; padding-top: 10px; font-weight: 800; font-size: 15px; }
        .summary-row:last-child span:first-child { color: #60A5FA; }
        .summary-row:last-child span:last-child { color: #34D399; font-size: 17px; }

        .remarks-box { margin-top: 30px; border: 1px solid #22222E; border-radius: 10px; padding: 16px; background: #0D0D14; color: #FFFFFF; }
        .remarks-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #60A5FA; margin-bottom: 6px; }

        .report-footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #22222E; display: flex; justify-content: space-between; color: rgba(255, 255, 255, 0.50); font-size: 10px; }

        .btn-outline { border:1px solid rgba(248,113,113,0.38); background:rgba(239,68,68,0.16); color:#FCA5A5; padding:10px 20px; border-radius:9px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:700; margin-bottom: 24px; transition: all 0.2s ease; }
        .btn-outline:hover { background:#DC2626; color:#FFFFFF; border-color:#EF4444; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220,38,38,0.45); }

        @media print {
            body { background: #fff !important; color: #0F1F35 !important; padding: 15px !important; }
            .company-block .company-name, .report-meta .report-title, .info-value { color: #0F1F35 !important; }
            .info-box, .remarks-box { background: #FAFAFA !important; border-color: #E5E7EB !important; color: #0F1F35 !important; }
            table { background: #fff !important; border-color: #E5E7EB !important; }
            thead tr { background: #0F1F35 !important; }
            tbody td { color: #0F1F35 !important; border-color: #E5E7EB !important; }
            .summary-box { background: #fff !important; color: #000 !important; border-color: #000 !important; }
            .summary-row { color: #000 !important; border-color: #eee !important; }
            .summary-row span:last-child { color: #000 !important; }
            @page { margin: 10mm; }
            .no-print { display: none !important; }
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
