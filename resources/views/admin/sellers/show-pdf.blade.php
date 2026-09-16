<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Dossier - {{ $seller->name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 24px; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 12px; margin-bottom: 18px; }
        .header h1 { font-size: 20px; margin: 0 0 4px 0; color: #0f172a; }
        .header p { margin: 0; color: #64748b; font-size: 11px; }
        .section-title { font-size: 12px; font-weight: bold; color: #0f172a; text-transform: uppercase; border-bottom: 1px solid #cbd5e1; padding-bottom: 4px; margin: 16px 0 10px 0; }
        .grid { display: table; width: 100%; margin-bottom: 12px; }
        .row { display: table-row; }
        .cell { display: table-cell; padding: 4px 8px; width: 50%; vertical-align: top; }
        .label { font-size: 9.5px; font-weight: bold; color: #64748b; text-transform: uppercase; }
        .value { font-size: 11px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; color: #334155; font-size: 9.5px; font-weight: bold; text-transform: uppercase; padding: 6px 8px; border: 1px solid #cbd5e1; text-align: left; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 10px; }
        .footer { margin-top: 30px; font-size: 9px; color: #94a3b8; text-align: right; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Seller Dossier: {{ $seller->name }}</h1>
        <p>Landowner / Property Seller Profile &bull; Generated on {{ date('d M, Y h:i A') }}</p>
    </div>

    <div class="section-title">1. Personal &amp; Legal Identity</div>
    <div class="grid">
        <div class="row">
            <div class="cell"><span class="label">Seller Name:</span><div class="value">{{ $seller->name }}</div></div>
            <div class="cell"><span class="label">Seller Type:</span><div class="value">{{ ucfirst(str_replace('_', ' ', $seller->seller_type ?? 'Individual')) }}</div></div>
        </div>
        <div class="row">
            <div class="cell"><span class="label">Mobile Number:</span><div class="value">{{ $seller->mobile ?: ($seller->phone ?: '-') }}</div></div>
            <div class="cell"><span class="label">Email:</span><div class="value">{{ $seller->email ?: '-' }}</div></div>
        </div>
        <div class="row">
            <div class="cell"><span class="label">PAN Number:</span><div class="value">{{ $seller->pan_no ?: '-' }}</div></div>
            <div class="cell"><span class="label">Aadhaar Number:</span><div class="value">{{ $seller->aadhaar_no ?: '-' }}</div></div>
        </div>
        <div class="row">
            <div class="cell"><span class="label">Address:</span><div class="value">{{ $seller->address ?: '-' }} ({{ $seller->city ?: '-' }})</div></div>
            <div class="cell"><span class="label">Status:</span><div class="value">{{ ucfirst($seller->status) }}</div></div>
        </div>
    </div>

    <div class="section-title">2. Bank Payment Coordinates</div>
    <div class="grid">
        <div class="row">
            <div class="cell"><span class="label">Bank Name:</span><div class="value">{{ $seller->bank_name ?: '-' }}</div></div>
            <div class="cell"><span class="label">Account Number:</span><div class="value">{{ $seller->account_number ?: '-' }}</div></div>
        </div>
        <div class="row">
            <div class="cell"><span class="label">IFSC Code:</span><div class="value">{{ $seller->ifsc_code ?: '-' }}</div></div>
            <div class="cell"><span class="label">Branch Name:</span><div class="value">{{ $seller->branch_name ?: '-' }}</div></div>
        </div>
    </div>

    <div class="section-title">3. Linked Property Acquisitions ({{ $seller->propertyMasters->count() }})</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Property Master</th>
                <th>Code</th>
                <th>Acquired Date</th>
                <th>Total Area</th>
                <th>Deal Amount (₹)</th>
                <th>Paid Amount (₹)</th>
                <th>Due Balance (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($seller->propertyMasters as $idx => $pm)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $pm->property_name }}</strong></td>
                    <td>{{ $pm->property_code }}</td>
                    <td>{{ $pm->purchase_date ? date('d M, Y', strtotime($pm->purchase_date)) : '-' }}</td>
                    <td>{{ number_format($pm->total_area, 2) }} {{ $pm->area_unit ?? 'Sq.Ft' }}</td>
                    <td>₹{{ number_format($pm->purchase_price, 2) }}</td>
                    <td>₹{{ number_format($pm->paid_amount, 2) }}</td>
                    <td>₹{{ number_format($pm->due_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align: center;">No property acquisitions linked.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Delawala Management System &bull; Confidential
    </div>
</body>
</html>
