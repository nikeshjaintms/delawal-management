<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $propertyMaster->property_name }} - Property Dossier</title>
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Segoe UI',Arial,sans-serif; font-size:11.5px; color:#0F1F35; background:#fff; padding:24px; line-height:1.4; }

        /* ── Header ── */
        .rpt-header { display:flex; justify-content:space-between; align-items:flex-start; padding-bottom:16px; margin-bottom:18px; border-bottom:2.5px solid #fc6900ff; }
        .co-name  { font-size:22px; font-weight:800; color:#0F1F35; letter-spacing:0.4px; }
        .co-sub   { font-size:10px; color:#fc6900ff; font-weight:700; letter-spacing:2px; text-transform:uppercase; margin-top:3px; }
        .rpt-meta { text-align:right; }
        .rpt-meta .rpt-title { font-size:16px; font-weight:700; color:#0F1F35; margin-bottom:4px; }
        .rpt-meta .rpt-date  { font-size:11px; color:#64748B; }

        /* ── Title Banner ── */
        .prop-banner { background:linear-gradient(135deg, #0F172A 0%, #1E293B 100%); color:#fff; border-radius:8px; padding:16px 20px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
        .prop-banner h1 { font-size:18px; font-weight:700; color:#F8FAFC; margin-bottom:4px; }
        .prop-banner .code-badge { background:rgba(252,105,0,0.25); color:#FF8A3D; font-size:11px; font-weight:700; padding:3px 10px; border-radius:4px; border:1px solid rgba(252,105,0,0.4); display:inline-block; }

        /* ── Two Col Grid ── */
        .grid-2 { display:flex; gap:16px; margin-bottom:18px; }
        .grid-col { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:14px; background:#F8FAFC; }
        .col-heading { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:10px; border-bottom:1px solid #E2E8F0; padding-bottom:4px; }

        .info-row { display:flex; justify-content:space-between; padding:5px 0; border-bottom:1px dashed #E2E8F0; font-size:11px; }
        .info-row:last-child { border-bottom:none; }
        .info-label { color:#64748B; font-weight:500; }
        .info-value { color:#0F1F35; font-weight:700; text-align:right; }

        /* ── Financial Highlight Cards ── */
        .stat-row { display:flex; gap:10px; margin-bottom:20px; }
        .stat-box { flex:1; border:1px solid #E2E8F0; border-radius:8px; padding:10px 12px; background:#fff; text-align:center; }
        .stat-box .s-label { font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#64748B; margin-bottom:3px; }
        .stat-box .s-value { font-size:16px; font-weight:800; color:#0F1F35; }
        .stat-box.s-orange { border-color:rgba(252,105,0,0.3); background:rgba(252,105,0,0.03); }
        .stat-box.s-orange .s-value { color:#e05c00; }
        .stat-box.s-green { border-color:rgba(16,185,129,0.3); background:rgba(16,185,129,0.03); }
        .stat-box.s-green .s-value { color:#059669; }
        .stat-box.s-amber { border-color:rgba(245,158,11,0.3); background:rgba(245,158,11,0.03); }
        .stat-box.s-amber .s-value { color:#B45309; }

        /* ── Plots Table ── */
        .section-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#fc6900ff; margin-bottom:8px; margin-top:16px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }
        table { width:100%; border-collapse:collapse; font-size:10.5px; margin-bottom:20px; }
        thead tr { background:#0F172A; }
        thead th { padding:7px 8px; color:#FFF; font-weight:600; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:0.5px; }
        thead th.r { text-align:right; }
        thead th.c { text-align:center; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:6px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle; }
        tbody td.r { text-align:right; font-weight:700; color:#0F1F35; }
        tbody td.c { text-align:center; }
        
        .badge { display:inline-block; padding:2px 6px; border-radius:8px; font-size:8.5px; font-weight:700; text-transform:uppercase; }
        .badge-success { background:#ECFDF5; color:#059669; border:1px solid #A7F3D0; }
        .badge-warning { background:#FFFBEB; color:#D97706; border:1px solid #FDE68A; }
        .badge-danger  { background:#FEF2F2; color:#DC2626; border:1px solid #FECACA; }
        .badge-info    { background:#EFF6FF; color:#2563EB; border:1px solid #BFDBFE; }

        /* ── Signature / Auth Block ── */
        .auth-block { margin-top:30px; padding-top:14px; display:flex; justify-content:space-between; page-break-inside:avoid; }
        .auth-col { width:220px; text-align:center; }
        .auth-line { border-top:1.5px solid #0F1F35; margin-top:40px; padding-top:4px; font-size:10px; font-weight:700; }

        /* ── Footer ── */
        .rpt-footer { margin-top:20px; padding-top:10px; border-top:1px solid #E5E7EB; display:flex; justify-content:space-between; color:#9CA3AF; font-size:9px; }

        @media print { body { padding:10px; } @page { margin:8mm; } }
    </style>
</head>
<body>

<div class="rpt-header">
    <div>
        <div class="co-name">Delawala</div>
        <div class="co-sub">Properties &amp; Management</div>
    </div>
    <div class="rpt-meta">
        <div class="rpt-title">Property Master Dossier</div>
        <div class="rpt-date">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

<div class="prop-banner">
    <div>
        <h1>{{ $propertyMaster->property_name }}</h1>
        <div style="font-size:11px; color:#94A3B8;">
            Location: {{ $propertyMaster->location ?: 'N/A' }}{{ $propertyMaster->city ? ', '.$propertyMaster->city : '' }}
        </div>
    </div>
    <div style="text-align:right;">
        <span class="code-badge">{{ $propertyMaster->property_code }}</span><br>
        <span style="font-size:9.5px; color:#94A3B8; margin-top:4px; display:inline-block;">Firm: {{ $propertyMaster->firm->firm_name ?? '-' }}</span>
    </div>
</div>

<div class="stat-row">
    <div class="stat-box s-orange">
        <div class="s-label">Purchase Price</div>
        <div class="s-value">₹{{ number_format($propertyMaster->purchase_price, 2) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Paid Amount</div>
        <div class="s-value">₹{{ number_format($propertyMaster->paid_amount, 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Due / Outstanding</div>
        <div class="s-value">₹{{ number_format($propertyMaster->due_amount, 2) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Plots</div>
        <div class="s-value">{{ $propertyMaster->plots->count() }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading">&#9632; Property &amp; Land Details</div>
        <div class="info-row">
            <span class="info-label">Property Type:</span>
            <span class="info-value" style="color: #fc6900ff;">{{ $propertyMaster->property_type ?: 'General / Not Specified' }}</span>
        </div>
        @if($propertyMaster->unit_numbers_list || $propertyMaster->total_units_count)
        <div class="info-row">
            <span class="info-label">Units Purchased:</span>
            <span class="info-value" style="color:#059669;">
                {{ $propertyMaster->total_units_count ?: $propertyMaster->plots->count() }} Units
                @if($propertyMaster->unit_numbers_list)
                    <small style="color:#64748B; display:block; font-size:10px;">(Nos: {{ $propertyMaster->unit_numbers_list }})</small>
                @endif
            </span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Purchase Date:</span>
            <span class="info-value">{{ $propertyMaster->purchase_date ? \Carbon\Carbon::parse($propertyMaster->purchase_date)->format('d M Y') : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Land Area:</span>
            <span class="info-value">{{ $propertyMaster->total_area ? number_format($propertyMaster->total_area, 2) . ' ' . $propertyMaster->area_unit : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Purchase Rate:</span>
            <span class="info-value">{{ $propertyMaster->purchase_rate ? '₹' . number_format($propertyMaster->purchase_rate, 2) . ' / ' . $propertyMaster->area_unit : '-' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Full Address:</span>
            <span class="info-value">{{ $propertyMaster->address ?: ($propertyMaster->location ?: '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">State / Pincode:</span>
            <span class="info-value">{{ $propertyMaster->state ?: '-' }} {{ $propertyMaster->pincode ? '('.$propertyMaster->pincode.')' : '' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Master Status:</span>
            <span class="info-value">{{ ucfirst($propertyMaster->status) }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading">&#9632; Seller &amp; Payment Details</div>
        <div class="info-row">
            <span class="info-label">Vendor:</span>
            <span class="info-value">{{ $propertyMaster->seller_name ?: ($propertyMaster->vendor->name ?? '-') }}</span>
        </div>
        @if($propertyMaster->vendor)
        <div class="info-row">
            <span class="info-label">Vendor Link:</span>
            <span class="info-value">{{ $propertyMaster->vendor->name }} ({{ $propertyMaster->vendor->phone ?? '-' }})</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Payment Mode:</span>
            <span class="info-value">{{ ucfirst($propertyMaster->payment_mode ?: '-') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Status:</span>
            <span class="info-value">{{ ucfirst($propertyMaster->payment_status ?: 'Unpaid') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Broker / Agent:</span>
            <span class="info-value">
                @if($propertyMaster->broker)
                    {{ $propertyMaster->broker->name }} {{ ($propertyMaster->broker->phone || $propertyMaster->broker->mobile) ? '(' . ($propertyMaster->broker->phone ?: $propertyMaster->broker->mobile) . ')' : '' }}
                @elseif($propertyMaster->broker_name)
                    {{ $propertyMaster->broker_name }}
                @else
                    Direct (No Broker)
                @endif
            </span>
        </div>
        @if($propertyMaster->broker_commission_amount > 0)
        <div class="info-row">
            <span class="info-label">Broker Commission:</span>
            <span class="info-value">
                ₹{{ number_format($propertyMaster->broker_commission_amount, 2) }}
                @if($propertyMaster->broker_commission_type === 'percentage' && $propertyMaster->broker_commission_rate > 0)
                    ({{ $propertyMaster->broker_commission_rate }}%)
                @endif
                — Paid: ₹{{ number_format($propertyMaster->broker_commission_paid ?? 0, 2) }}, Due: ₹{{ number_format($propertyMaster->broker_commission_due ?? 0, 2) }}
            </span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Linked Projects:</span>
            <span class="info-value">
                @php $pList = $propertyMaster->all_projects ?? collect(); @endphp
                @if($pList->isNotEmpty())
                    {{ $pList->pluck('project_name')->implode(', ') }}
                @else
                    None
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Created At:</span>
            <span class="info-value">{{ $propertyMaster->created_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</div>

@if($propertyMaster->description)
<div style="background:#F8FAFC; border:1px solid #E2E8F0; border-radius:6px; padding:10px 14px; margin-bottom:18px; font-size:10.5px;">
    <strong style="color:#0F1F35;">Notes / Remarks:</strong> {{ $propertyMaster->description }}
</div>
@endif

<div class="section-label">&#9632; Units / Plots Breakdown ({{ $propertyMaster->plots->count() }} Total)</div>
<table>
    <thead>
        <tr>
            <th style="width:20px;">#</th>
            <th>Plot / Unit Name</th>
            <th>Unit No</th>
            <th>Type</th>
            <th>Assigned Project</th>
            <th>Size</th>
            <th>Facing</th>
            <th class="r">Rate (₹)</th>
            <th class="r">Price (₹)</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($propertyMaster->plots as $i => $plot)
        <tr>
            <td style="color:#9CA3AF;">{{ $i+1 }}</td>
            <td><strong>{{ $plot->property_name }}</strong></td>
            <td>{{ $plot->unit_no ?: '-' }}</td>
            <td>{{ $plot->propertyType->name ?? '-' }}</td>
            <td>{{ $plot->project->project_name ?? '-' }}</td>
            <td>{{ $plot->size ? number_format($plot->size, 2).' '.($plot->size_unit ?? '') : '-' }}</td>
            <td>{{ $plot->facing ?: '-' }}</td>
            <td class="r">₹{{ number_format($plot->purchase_rate ?: 0, 2) }}</td>
            <td class="r">₹{{ number_format($plot->price ?: 0, 2) }}</td>
            <td class="c">
                @if($plot->status === 'available')
                    <span class="badge badge-success">Available</span>
                @elseif($plot->status === 'booked')
                    <span class="badge badge-warning">Booked</span>
                @elseif($plot->status === 'sold')
                    <span class="badge badge-danger">Sold</span>
                @else
                    <span class="badge badge-info">{{ ucfirst($plot->status) }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;padding:14px;color:#64748B;">No plots generated under this property master yet.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Prepared By / Operations</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory / Delawala</div>
    </div>
</div>

<div class="rpt-footer">
    <span>Delawala Management System &nbsp;—&nbsp; Property Master Dossier &nbsp;—&nbsp; {{ $propertyMaster->property_code }}</span>
    <span>Page 1 of 1 &nbsp;|&nbsp; {{ now()->format('d M Y') }}</span>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
