<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Master Directory</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; margin: 0 0 4px 0; color: #0f172a; text-transform: uppercase; }
        .header p { margin: 0; color: #64748b; font-size: 10px; }
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
    <div class="header">
        <h1>Seller Master Directory</h1>
        <p>Generated on {{ date('d M, Y h:i A') }}</p>
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
