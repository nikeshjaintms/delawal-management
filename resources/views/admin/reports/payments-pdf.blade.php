<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments & Collections Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Payments & Collections Register',
    'orientation' => 'landscape',
    'backUrl' => route('reports.payments')
])

@include('admin.components.pdf-header', [
    'title' => 'Payments & Collections Register',
    'subtitle' => 'Receipts, Inflows & Cash Flow Statement'
])

<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Received</div>
        <div class="s-value">₹{{ number_format($totalReceived ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Paid</div>
        <div class="s-value">₹{{ number_format($totalPaid ?? 0, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Net Balance</div>
        <div class="s-value">{{ ($netBalance ?? 0) < 0 ? '− ' : '' }}₹{{ number_format(abs($netBalance ?? 0), 2) }}</div>
    </div>
    <div class="stat-box s-amber">
        <div class="s-label">Total Transactions</div>
        <div class="s-value">{{ $totalTransactions ?? $records->count() }}</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-money-bill-transfer"></i>
    Payment &amp; Cash Flow Register ({{ ucfirst($paymentType ?? 'All') }})
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th class="c" style="width:75px;">Type</th>
            <th style="width:75px;">Date</th>
            <th>Party Name (Customer / Payee)</th>
            <th>Property / Project</th>
            <th>Particular &amp; Ref</th>
            <th>Mode</th>
            <th class="r" style="width:100px;">Amount (₹)</th>
            <th class="c" style="width:70px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($records as $i => $r)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td class="c">
                @if(($r->direction ?? '') === 'received')
                    <span class="badge badge-success">Received</span>
                @else
                    <span class="badge badge-danger" style="background:#FEE2E2;color:#991B1B;">Paid</span>
                @endif
            </td>
            <td>{{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}</td>
            <td>
                <strong>{{ $r->party_name ?? '—' }}</strong>
                @if(!empty($r->party_sub))
                    <div style="font-size:9px;color:#64748B;">{{ $r->party_sub }}</div>
                @endif
            </td>
            <td>{{ $r->property_name ?? '—' }}</td>
            <td>
                {{ $r->particular ?? '—' }}
                @if(!empty($r->reference))
                    <div style="font-size:9px;color:#64748B;">Ref: {{ $r->reference }}</div>
                @endif
            </td>
            <td>{{ ucfirst($r->payment_mode ?: 'Cash') }}</td>
            <td class="r" style="font-weight:800;{{ ($r->direction ?? '') === 'received' ? 'color:#059669;' : 'color:#DC2626;' }}">
                {{ ($r->direction ?? '') === 'received' ? '+' : '−' }} ₹{{ number_format($r->amount ?? 0, 2) }}
            </td>
            <td class="c">
                <span class="badge badge-secondary">{{ ucfirst($r->status ?? 'paid') }}</span>
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No payment transactions recorded.</td></tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr style="background:#F8FAFC;font-weight:bold;">
            <td colspan="7" style="text-align:right;">Net Flow Balance:</td>
            <td class="r" style="font-weight:800;{{ ($netBalance ?? 0) >= 0 ? 'color:#059669;' : 'color:#DC2626;' }}">
                {{ ($netBalance ?? 0) < 0 ? '− ' : '' }}₹{{ number_format(abs($netBalance ?? 0), 2) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

@include('admin.components.pdf-footer')
</body>
</html>