<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Dossier - {{ $material->material_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11.5px; color: #0F1F35; background: #fff; padding: 30px; }

        .report-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #C5A87E; }
        .company-block .company-name { font-size: 24px; font-weight: 800; color: #0F1F35; letter-spacing: 0.5px; }
        .company-block .company-sub  { font-size: 11px; color: #C5A87E; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-top: 3px; }
        .report-meta { text-align: right; }
        .report-meta .report-title { font-size: 18px; font-weight: 800; color: #0F1F35; margin-bottom: 4px; }
        .report-meta .report-date  { font-size: 11px; color: #64748B; }

        .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
        .stat-card { border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 16px; background: #FAFAFA; }
        .stat-card .label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-card .val { font-size: 18px; font-weight: 800; color: #0F1F35; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px 18px; background: #FAFAFA; }
        .info-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748B; margin-bottom: 10px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 4px; }
        .info-row { display: flex; margin-bottom: 7px; }
        .info-label { width: 130px; font-weight: 600; color: #64748B; font-size: 11px; }
        .info-value { flex: 1; color: #0F1F35; font-weight: 600; font-size: 11.5px; }

        .report-footer { margin-top: 40px; padding-top: 14px; border-top: 1px solid #E5E7EB; display: flex; justify-content: space-between; color: #9CA3AF; font-size: 9.5px; }

        @media print {
            body { padding: 15px; }
            @page { margin: 10mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Material Dossier: ' . $material->material_name,
    'orientation' => 'portrait',
    'backUrl' => route('materials.show', $material->id)
])

<div class="rpt-header" style="display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:14px; margin-bottom:18px; border-bottom:2.5px solid #D97706; gap:16px;">
    <div style="display:flex; align-items:center; gap:14px;">
        <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height:52px; width:auto; object-fit:contain;" onerror="this.style.display='none';">
        <div style="display:flex; flex-direction:column; gap:2px;">
            <div class="co-name" style="font-size:20px; font-weight:800; color:#0F172A; text-transform:uppercase; letter-spacing:0.3px; line-height:1.1;">Delawala Properties</div>
            <div class="co-sub" style="font-size:9.5px; color:#D97706; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;">Delawala Infra Co. &bull; Real Estate &amp; Management</div>
            <div style="font-size:10px; color:#475569; line-height:1.3; margin-top:1px;">
                <i class="fa-solid fa-location-dot" style="color:#D97706; font-size:9px; margin-right:3px;"></i>Ground Floor, F F SH No. 116, Aman Plazza, Dahegam Road, Dahegam, Bharuch - 392012
            </div>
            <div style="display:flex; align-items:center; gap:6px; margin-top:3px; flex-wrap:wrap;">
                <span style="display:inline-flex; align-items:center; background:#FEF3C7; color:#92400E; border:1px solid #FCD34D; padding:1px 6px; border-radius:4px; font-size:9.5px; font-weight:700; letter-spacing:0.3px;"><strong>GSTIN:</strong> 24CUBPD0770R1ZI</span>
                <span style="display:inline-flex; align-items:center; background:#F1F5F9; color:#334155; border:1px solid #CBD5E1; padding:1px 6px; border-radius:4px; font-size:9px;"><strong>Proprietor:</strong> Delawala Zafar</span>
            </div>
        </div>
    </div>
    <div class="rpt-meta" style="text-align:right; flex-shrink:0;">
        <div class="rpt-title" style="font-size:16px; font-weight:800; color:#0F172A; letter-spacing:0.2px; line-height:1.2;">Official Report</div>
        <div class="rpt-date" style="font-size:10px; color:#64748B; margin-top:3px;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>
    </div>
    <div class="report-meta">
        <div class="report-title">Material Dossier</div>
        <div class="report-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

@php
    $matTotal = (float) ($material->total_price ?? (($material->opening_stock ?? 0) * ($material->unit_price ?? 0)));
@endphp

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Quantity Needed</div>
        <div class="val">{{ number_format($material->opening_stock, 2) }} {{ $material->unit }}</div>
    </div>
    <div class="stat-card" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.04);">
        <div class="label">Unit Price</div>
        <div class="val" style="color:#059669;">₹{{ number_format($material->unit_price ?? 0, 2) }}</div>
    </div>
    <div class="stat-card" style="border-color: rgba(37, 99, 235, 0.4); background: rgba(37, 99, 235, 0.04);">
        <div class="label">Total Estimated Value</div>
        <div class="val" style="color:#1D4ED8;">₹{{ number_format($matTotal, 2) }}</div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box">
        <div class="info-title">Material Information</div>
        <div class="info-row"><span class="info-label">Material Name:</span><span class="info-value">{{ $material->material_name }}</span></div>
        <div class="info-row"><span class="info-label">Specification:</span><span class="info-value">{{ $material->specification ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Category:</span><span class="info-value">{{ $material->category->category_name ?? 'Uncategorized' }}</span></div>
        <div class="info-row"><span class="info-label">Unit of Measure:</span><span class="info-value">{{ $material->unit ?: '—' }}</span></div>
        <div class="info-row"><span class="info-label">Status:</span><span class="info-value">{{ ucfirst($material->status) }}</span></div>
    </div>

    <div class="info-box">
        <div class="info-title">Project &amp; Contractor Link</div>
        <div class="info-row"><span class="info-label">Project:</span><span class="info-value">{{ $material->project->project_name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Assigned Contractor:</span><span class="info-value">{{ $material->contractor->contractor_name ?? '—' }}</span></div>
        <div class="info-row"><span class="info-label">Minimum Stock Alert:</span><span class="info-value">{{ number_format($material->minimum_stock ?? 0, 2) }} {{ $material->unit }}</span></div>
        <div class="info-row"><span class="info-label">Created At:</span><span class="info-value">{{ $material->created_at->format('d M Y') }}</span></div>
        <div class="info-row"><span class="info-label">Last Updated:</span><span class="info-value">{{ $material->updated_at->format('d M Y') }}</span></div>
    </div>
</div>

<div class="report-footer">
    <span>Delawala Management Inventory System</span>
    <span>Authorized Signature: __________________________</span>
</div>

</body>
</html>
