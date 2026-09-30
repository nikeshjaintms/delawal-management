<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Master Report - Delawala Management</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #0F1F35; background: #fff; padding: 24px; }

        /* ── Header ── */
        .rpt-header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; margin-bottom: 20px; border-bottom: 2.5px solid #D97706; }
        .co-name  { font-size: 22px; font-weight: 800; color: #0F1F35; letter-spacing: 0.4px; }
        .co-sub   { font-size: 10px; color: #D97706; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .rpt-meta { text-align: right; }
        .rpt-meta .rpt-title { font-size: 16px; font-weight: 700; color: #0F1F35; margin-bottom: 4px; }
        .rpt-meta .rpt-date  { font-size: 11px; color: #64748B; }

        /* ── Stat Row ── */
        .stat-row { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .stat-box { flex: 1; min-width: 110px; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 12px; background: #F8FAFC; }
        .stat-box .s-label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: #64748B; }
        .stat-box .s-value { font-size: 16px; font-weight: 800; margin-top: 4px; color: #0F1F35; }
        .stat-box.s-gold { border-color: rgba(217, 119, 6, 0.3); background: rgba(217, 119, 6, 0.04); }
        .stat-box.s-gold .s-value { color: #B45309; }
        .stat-box.s-blue { border-color: rgba(37, 99, 235, 0.3); background: rgba(37, 99, 235, 0.04); }
        .stat-box.s-blue .s-value { color: #1D4ED8; }
        .stat-box.s-green { border-color: rgba(16, 185, 129, 0.3); background: rgba(16, 185, 129, 0.04); }
        .stat-box.s-green .s-value { color: #059669; }

        /* ── Table ── */
        .section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #D97706; margin-bottom: 8px; margin-top: 16px; padding-bottom: 4px; border-bottom: 1px solid #E5E7EB; }
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        thead tr { background: #0F172A; }
        thead th { padding: 8px 10px; color: #FFF; font-weight: 600; text-align: left; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }
        thead th.r { text-align: right; }
        thead th.c { text-align: center; }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        tbody td { padding: 8px 10px; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        tbody td.r { text-align: right; font-weight: 700; color: #0F1F35; }
        tbody td.c { text-align: center; }
        tbody tr:last-child td { border-bottom: none; }
        tfoot tr { background: #F1F5F9; }
        tfoot td { padding: 9px 10px; font-weight: 800; border-top: 2px solid #E5E7EB; }
        tfoot td.r { text-align: right; color: #1D4ED8; font-size: 12px; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .badge-active { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
        .badge-inactive { background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; }

        /* ── Footer ── */
        .rpt-footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #E5E7EB; display: flex; justify-content: space-between; color: #9CA3AF; font-size: 9.5px; }

        @media print { body { padding: 10px; } @page { margin: 8mm; size: landscape; } }
    </style>
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Material Master Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('materials.index')
])

<div class="rpt-header">
    <div style="display: flex; align-items: center; gap: 14px;">
        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height: 50px; width: auto; object-fit: contain;">
        <div>
            <div class="co-name">Delawala</div>
            <div class="co-sub">Properties &amp; Management</div>
        </div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Material Master Directory</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Materials</div>
        <div class="s-value">{{ $totalMaterialsCount }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Items</div>
        <div class="s-value">{{ $activeMaterialsCount }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Total Qty Needed</div>
        <div class="s-value">{{ number_format($totalStockQty, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Estimated Value</div>
        <div class="s-value">₹{{ number_format($totalEstimatedValue, 2) }}</div>
    </div>
</div>

<div class="section-label">&#9632; Material Items Listing</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;">#</th>
            <th>Material Name</th>
            <th>Specification / Size</th>
            <th>Category</th>
            <th>Project</th>
            <th>Contractor</th>
            <th>Unit</th>
            <th class="r">Qty Needed</th>
            <th class="r">Unit Price (₹)</th>
            <th class="r">Total Value (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $i => $mat)
        @php
            $matTotal = (float) ($mat->total_price ?? (($mat->opening_stock ?? 0) * ($mat->unit_price ?? 0)));
        @endphp
        <tr>
            <td style="color:#9CA3AF;">{{ $i + 1 }}</td>
            <td><strong>{{ $mat->material_name }}</strong></td>
            <td>{{ $mat->specification ?: '—' }}</td>
            <td>
                @if($mat->category)
                    <span style="font-weight:600; color:#1E40AF;">{{ $mat->category->category_name }}</span>
                @else
                    <span style="color:#9CA3AF;">—</span>
                @endif
            </td>
            <td>{{ $mat->project->project_name ?? '—' }}</td>
            <td>{{ $mat->contractor->contractor_name ?? '—' }}</td>
            <td>{{ $mat->unit ?: '—' }}</td>
            <td class="r">{{ number_format($mat->opening_stock, 2) }}</td>
            <td class="r">₹{{ number_format($mat->unit_price ?? 0, 2) }}</td>
            <td class="r" style="color:#1D4ED8;">₹{{ number_format($matTotal, 2) }}</td>
            <td class="c">
                @if($mat->status === 'active')
                    <span class="badge badge-active">Active</span>
                @else
                    <span class="badge badge-inactive">{{ ucfirst($mat->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" style="text-align:center;padding:26px;color:#64748B;">
                <div style="font-size:13px;font-weight:700;margin-bottom:4px;color:#0F1F35;">No material records found.</div>
                <div style="font-size:11px;color:#94A3B8;">No inventory materials match the specified filters.</div>
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($materials->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" style="font-size:11px;">Total ({{ $materials->count() }} Materials)</td>
            <td class="r">{{ number_format($totalStockQty, 2) }}</td>
            <td class="r">—</td>
            <td class="r">₹{{ number_format($totalEstimatedValue, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Material Master Report</span>
    <span>{{ $materials->count() }} items &nbsp;|&nbsp; Total: ₹{{ number_format($totalEstimatedValue, 2) }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

</body>
</html>
