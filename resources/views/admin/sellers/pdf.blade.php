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

    <div class="rpt-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height: 50px; width: auto; object-fit: contain;">
            <div>
                <div class="co-name">Delawala</div>
                <div class="co-sub">Properties &amp; Management</div>
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
