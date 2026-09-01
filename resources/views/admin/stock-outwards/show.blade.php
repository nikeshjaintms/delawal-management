<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outward Gate Pass - {{ isset($outwardGroup) ? $outwardGroup->outward_number : 'Manual Outward' }}</title>
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
            @page { margin: 10mm; }
            .no-print { display: none !important; }
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
            <div class="info-row"><span class="info-label">Contractor:</span><span class="info-value">{{ isset($outwardGroup) ? ($outwardGroup->contractor->contractor_name ?? '—') : ($stockOutward->contractor->contractor_name ?? '—') }}</span></div>
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
