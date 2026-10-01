<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Master Directory</title>
    <style>
        .rpt-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2.5px solid #0f172a; padding-bottom: 14px; margin-bottom: 16px; }
        .co-name { font-size: 20px; font-weight: 800; color: #0f172a; }
        .co-sub { font-size: 9.5px; color: #d97706; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }
        .rpt-meta { text-align: right; }
        .rpt-meta .rpt-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
        .rpt-meta .rpt-date { font-size: 10px; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; color: #334155; font-size: 10px; font-weight: bold; text-transform: uppercase; padding: 7px 8px; border: 1px solid #cbd5e1; text-align: left; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 10.5px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .badge-active { background: #dcfce7; color: #15803d; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
@include('admin.components.pdf-action-bar', ['title' => 'Seller Master Directory', 'orientation' => 'landscape', 'backUrl' => route('sellers.index')])

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
        <div class="rpt-title" style="font-size:16px; font-weight:800; color:#0F172A; letter-spacing:0.2px; line-height:1.2;">Official Document</div>
        <div class="rpt-date" style="font-size:10px; color:#64748B; margin-top:3px;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-title">Seller Master Directory</div>
            <div class="rpt-date">Generated on {{ date('d M, Y h:i A') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Seller Name</th>
                <th>Contact</th>
                <th>City</th>
                <th>PAN</th>
                <th>Bank Name</th>
                <th>Account No</th>
                <th>IFSC</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sellers as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td>{{ $s->mobile ?: ($s->phone ?: '-') }}</td>
                    <td>{{ $s->city ?: '-' }}</td>
                    <td>{{ $s->pan_no ?: '-' }}</td>
                    <td>{{ $s->bank_name ?: '-' }}</td>
                    <td>{{ $s->account_number ?: '-' }}</td>
                    <td>{{ $s->ifsc_code ?: '-' }}</td>
                    <td><span class="badge badge-active">{{ ucfirst($s->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align: center;">No sellers found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Delawala Management System &bull; Confidential
    </div>
</body>
</html>
