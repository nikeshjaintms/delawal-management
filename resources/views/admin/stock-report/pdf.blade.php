<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Stock Report</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #0F1F35; background: #fff; padding: 30px; }

        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 2px solid #D4AF37; }
        .company-block .company-name { font-size: 22px; font-weight: 800; color: #0F1F35; letter-spacing: 0.5px; }
        .company-block .company-sub  { font-size: 11px; color: #D4AF37; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .report-meta { text-align: right; }
        .report-meta .report-title { font-size: 16px; font-weight: 700; color: #0F1F35; margin-bottom: 4px; }
        .report-meta .report-date  { font-size: 11px; color: #64748B; }

        .summary-row { display: flex; gap: 16px; margin-bottom: 24px; }
        .summary-card { flex: 1; border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; }
        .summary-card .s-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748B; }
        .summary-card .s-value { font-size: 22px; font-weight: 800; margin-top: 4px; color: #0F1F35; }
        .summary-card.s-warn   { border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.03); }
        .summary-card.s-warn .s-value { color: #DC2626; }
        .summary-card.s-ok    { border-color: rgba(34,197,94,0.3); background: rgba(34,197,94,0.03); }
        .summary-card.s-ok  .s-value { color: #16803D; }

        table { width: 100%; border-collapse: collapse; font-size: 11.5px; }
        thead tr { background: #0F1F35; }
        thead th { padding: 10px 10px; color: #FFFFFF; font-weight: 600; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        thead th.num { text-align: right; }
        thead th.ctr { text-align: center; }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        tbody tr.low-row { background: rgba(239,68,68,0.04); }
        tbody td { padding: 9px 10px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        tbody td.num { text-align: right; }
        tbody td.ctr { text-align: center; }
        tbody tr:last-child td { border-bottom: none; }

        .badge { display: inline-block; padding: 3px 9px; border-radius: 5px; font-size: 10px; font-weight: 700; }
        .badge-ok  { background: rgba(34,197,94,0.12); color: #16803D; }
        .badge-low { background: rgba(239,68,68,0.12); color: #DC2626; }

        .unit-chip { background: #F1F5F9; padding: 2px 7px; border-radius: 4px; font-size: 10px; font-weight: 600; color: #64748B; }

        .inward-val  { color: #16803D; font-weight: 600; }
        .outward-val { color: #DC2626; font-weight: 600; }
        .curr-val    { font-weight: 700; }
        .curr-low    { font-weight: 700; color: #DC2626; }

        .report-footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #E5E7EB; display: flex; justify-content: space-between; color: #9CA3AF; font-size: 10px; }

        @media print {
            body { padding: 15px; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="report-header">
        <div class="company-block">
            <div class="company-name">Delawala</div>
            <div class="company-sub">Properties &amp; Management</div>
        </div>
        <div class="report-meta">
            <div class="report-title">Current Stock Report</div>
            <div class="report-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    @php
        $total    = $materials->count();
        $lowCount = $materials->filter(fn($m) => $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0)->count();
        $okCount  = $total - $lowCount;
    @endphp

    <div class="summary-row">
        <div class="summary-card">
            <div class="s-label">Total Materials</div>
            <div class="s-value">{{ $total }}</div>
        </div>
        <div class="summary-card s-ok">
            <div class="s-label">Available</div>
            <div class="s-value">{{ $okCount }}</div>
        </div>
        <div class="summary-card s-warn">
            <div class="s-label">Low Stock Alerts</div>
            <div class="s-value">{{ $lowCount }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Material Name</th>
                <th>Category</th>
                <th class="ctr">Unit</th>
                <th class="num">Opening</th>
                <th class="num">Inward</th>
                <th class="num">Outward</th>
                <th class="num">Current Stock</th>
                <th class="num">Min. Stock</th>
                <th class="ctr">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materials as $i => $m)
            @php $isLow = $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0; @endphp
            <tr class="{{ $isLow ? 'low-row' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $m->material_name }}</strong></td>
                <td>{{ $m->materialCategory->category_name ?? '—' }}</td>
                <td class="ctr"><span class="unit-chip">{{ $m->unit ?? '—' }}</span></td>
                <td class="num">{{ number_format($m->opening_stock, 3) }}</td>
                <td class="num inward-val">+{{ number_format($m->total_inward, 3) }}</td>
                <td class="num outward-val">-{{ number_format($m->total_outward, 3) }}</td>
                <td class="num {{ $isLow ? 'curr-low' : 'curr-val' }}">{{ number_format($m->computed_stock, 3) }}</td>
                <td class="num">{{ number_format($m->minimum_stock, 3) }}</td>
                <td class="ctr">
                    @if($isLow)
                        <span class="badge badge-low">Low Stock</span>
                    @else
                        <span class="badge badge-ok">Available</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;padding:24px;color:#64748B;">No materials found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        <span>Delawala Management System</span>
        <span>Stock report as of {{ now()->format('d M Y') }} &nbsp;|&nbsp; Total: {{ $total }} materials &nbsp;|&nbsp; Low Stock: {{ $lowCount }}</span>
    </div>

    <script>window.onload = function(){ window.print(); }</script>
</body>
</html>
