<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Seller Dossier - {{ $seller->name }}</title>
    <style>
        .rpt-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2.5px solid #0f172a; padding-bottom: 14px; margin-bottom: 16px; }
        .co-name { font-size: 20px; font-weight: 800; color: #0f172a; }
        .co-sub { font-size: 9.5px; color: #d97706; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }
        .rpt-meta { text-align: right; }
        .rpt-meta .rpt-title { font-size: 16px; font-weight: 800; color: #0f172a; margin-bottom: 3px; }
        .rpt-meta .rpt-date { font-size: 10px; color: #64748b; }
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
@include('admin.components.pdf-action-bar', ['title' => 'Seller Dossier: ' . $seller->name, 'orientation' => 'portrait', 'backUrl' => route('sellers.show', $seller->id)])

    <div class="rpt-header">
        <div style="display: flex; align-items: center; gap: 14px;">
            <img src="{{ asset('images/logo.png') }}" alt="Delawala Properties" style="height: 50px; width: auto; object-fit: contain;">
            <div>
                <div class="co-name">Delawala</div>
                <div class="co-sub">Properties &amp; Management</div>
            </div>
        </div>
        <div class="rpt-meta">
            <div class="rpt-title">Seller Dossier: {{ $seller->name }}</div>
            <div class="rpt-date">Landowner Profile &bull; {{ date('d M, Y h:i A') }}</div>
        </div>
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
