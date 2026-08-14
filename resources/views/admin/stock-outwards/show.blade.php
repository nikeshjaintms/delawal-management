<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outward Gate Pass - {{ isset($outwardGroup) ? $outwardGroup->outward_number : 'Manual Outward' }}</title>
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
        <a href="{{ route('stock-outwards.index') }}" class="btn-outline">← Back to List</a>
        <button onclick="window.print()" class="btn-outline" style="background:#059669; color:#FFF; border-color:#059669;">Print Gate Pass</button>
    </div>

    <!-- Header -->
    <div class="report-header">
        <div class="company-block">
            <div class="company-name">{{ isset($outwardGroup) ? ($outwardGroup->firm->firm_name ?? 'Delawala') : ($stockOutward->firm->firm_name ?? 'Delawala') }}</div>
            <div class="company-sub">Stock Outward / Dispatch Gate Pass</div>
        </div>
        <div class="report-meta">
            <div class="report-title">{{ isset($outwardGroup) ? ($outwardGroup->outward_number ?: 'SO-'.$outwardGroup->id) : ('SO-'.$stockOutward->id) }}</div>
            <div class="report-date">Date: {{ isset($outwardGroup) ? $outwardGroup->outward_date->format('d M Y') : $stockOutward->outward_date->format('d M Y') }}</div>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-box">
            <div class="info-title">Reference &amp; Destination</div>
            <div class="info-row"><span class="info-label">Inward Reference:</span><span class="info-value">{{ isset($outwardGroup) ? ($outwardGroup->stock_inward_number ?: 'Manual') : 'Manual' }}</span></div>
            <div class="info-row"><span class="info-label">Destination Project:</span><span class="info-value">{{ isset($outwardGroup) ? ($outwardGroup->project->project_name ?? ($outwardGroup->property->property_name ?? 'General')) : ($stockOutward->project->project_name ?? ($stockOutward->property->property_name ?? 'General')) }}</span></div>
        </div>
        <div class="info-box">
            <div class="info-title">Vehicle &amp; Transport Info</div>
            <div class="info-row"><span class="info-label">Vehicle Number:</span><span class="info-value"><strong>{{ isset($outwardGroup) ? ($outwardGroup->vehicle_no ?: '—') : ($stockOutward->vehicle_no ?? '—') }}</strong></span></div>
            <div class="info-row"><span class="info-label">Driver Name:</span><span class="info-value">{{ isset($outwardGroup) ? ($outwardGroup->driver_name ?: '—') : ($stockOutward->driver_name ?? '—') }}</span></div>
            <div class="info-row"><span class="info-label">LR Number / Trans:</span><span class="info-value">{{ isset($outwardGroup) ? (($outwardGroup->lr_no ?: '—') . ' (' . ($outwardGroup->transport_name ?: '—') . ')') : '—' }}</span></div>
        </div>
    </div>

    @if(isset($outwards))
    <table>
        <thead>
            <tr>
                <th style="width:10%;">#</th>
                <th>Material Description</th>
                <th class="num" style="width:40%;">Dispatched Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($outwards as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $item->material->material_name ?? '-' }}</strong></td>
                <td class="num" style="font-weight:700; color:#059669;">{{ number_format($item->quantity, 2) }} {{ $item->material->unit ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:10%;">#</th>
                <th>Material Description</th>
                <th class="num" style="width:40%;">Dispatched Qty</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td><strong>{{ $stockOutward->material->material_name ?? '-' }}</strong></td>
                <td class="num" style="font-weight:700; color:#059669;">{{ number_format($stockOutward->quantity, 2) }} {{ $stockOutward->material->unit ?? '' }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(isset($outwardGroup) && $outwardGroup->remarks)
    <div class="remarks-box">
        <div class="remarks-title">Gate Pass Instructions / Notes</div>
        <p style="white-space:pre-wrap; line-height:1.5;">{{ $outwardGroup->remarks }}</p>
    </div>
    @elseif(isset($stockOutward) && $stockOutward->remarks)
    <div class="remarks-box">
        <div class="remarks-title">Gate Pass Instructions / Notes</div>
        <p style="white-space:pre-wrap; line-height:1.5;">{{ $stockOutward->remarks }}</p>
    </div>
    @endif

    <div class="report-footer" style="margin-top:60px;">
        <span>Security Guard Sign: __________________________</span>
        <span>Driver Signature: __________________________</span>
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
