<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments & Collections Register (Cash/Bank Book) - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Payments & Collections Register',
    'orientation' => 'landscape',
    'backUrl' => route('reports.payments')
])

@include('admin.components.pdf-header', [
    'title' => 'Payments & Collections Register (Cash & Bank Book)',
    'subtitle' => 'Comprehensive Cash Flow Ledger — Customer Receipts, Rental Inflows & Outflow Vouchers'
])

{{-- ── KPI Summary Cards ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Received / Inflow (Cr.)</div>
        <div class="s-value">{{ formatIndianCurrency($totalReceived ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total Paid / Outflow (Dr.)</div>
        <div class="s-value">{{ formatIndianCurrency($totalPaid ?? 0) }}</div>
    </div>
    <div class="stat-box {{ ($netBalance ?? 0) >= 0 ? 's-blue' : 's-red' }}">
        <div class="s-label">Net Liquidity Balance (Cr − Dr)</div>
        <div class="s-value">{{ formatIndianCurrency($netBalance ?? 0) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Pending Due (Receivables)</div>
        <div class="s-value">{{ formatIndianCurrency($totalPending ?? 0) }}</div>
    </div>
    <div class="stat-box">
        <div class="s-label">Total Transactions</div>
        <div class="s-value">{{ $totalTransactions ?? $records->count() }} Entries</div>
    </div>
</div>

<div class="section-label">
    <i class="fa-solid fa-money-bill-transfer" style="color:#059669;"></i>
    Cash &amp; Bank Payments Register ({{ ucfirst($paymentType ?? 'All Transactions') }})
</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:70px;">Date</th>
            <th style="width:75px;">Ref / Vch</th>
            <th>Party (Customer / Payee)</th>
            <th>Property / Project</th>
            <th>Particulars &amp; Narration</th>
            <th class="c" style="width:70px;">Mode</th>
            <th class="r" style="width:105px;">Debit / Paid (₹)</th>
            <th class="r" style="width:105px;">Credit / Received (₹)</th>
            <th class="r" style="width:105px;">Running Balance (₹)</th>
            <th class="c" style="width:65px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @php $running = 0; @endphp
        @forelse($records as $i => $r)
        @php
            $isReceived = (($r->direction ?? '') === 'received');
            $amt = (float)($r->amount ?? 0);
            $running += $isReceived ? $amt : -$amt;
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="white-space:nowrap; font-weight:600;">
                {{ $r->payment_date ? \Carbon\Carbon::parse($r->payment_date)->format('d M Y') : '—' }}
            </td>
            <td style="font-size:8px;">
                <strong>{{ $r->id ?? '—' }}</strong>
                @if(!empty($r->reference))
                    <div style="font-size:7.5px; color:#64748B;">{{ $r->reference }}</div>
                @endif
            </td>
            <td>
                <strong>{{ $r->party_name ?? '—' }}</strong>
                @if(!empty($r->party_sub))
                    <div style="font-size:7.5px; color:#64748B;">{{ $r->party_sub }}</div>
                @endif
            </td>
            <td>{{ $r->property_name ?? '—' }}</td>
            <td>{{ $r->particular ?? '—' }}</td>
            <td class="c">
                <span class="badge">{{ ucfirst($r->payment_mode ?: 'Cash') }}</span>
            </td>
            {{-- Debit (Paid) --}}
            <td class="r" style="color:{{ !$isReceived ? '#DC2626' : '#94A3B8' }}; font-weight:{{ !$isReceived ? '700' : 'normal' }};">
                {{ !$isReceived ? formatIndianCurrency($amt) : '—' }}
            </td>
            {{-- Credit (Received) --}}
            <td class="r" style="color:{{ $isReceived ? '#059669' : '#94A3B8' }}; font-weight:{{ $isReceived ? '700' : 'normal' }};">
                {{ $isReceived ? formatIndianCurrency($amt) : '—' }}
            </td>
            {{-- Running Balance --}}
            <td class="r" style="font-weight:700; color:{{ $running >= 0 ? '#059669' : '#DC2626' }};">
                {{ formatIndianCurrency($running) }}
            </td>
            <td class="c">
                <span class="badge {{ ($r->direction ?? '') === 'received' ? 'badge-success' : 'badge-danger' }}">
                    {{ ucfirst($r->status ?? 'paid') }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="c" style="padding:24px; color:#64748B;">No payment transactions recorded for the selected filter criteria.</td>
        </tr>
        @endforelse
    </tbody>
    @if($records->count() > 0)
    <tfoot>
        <tr>
            <td colspan="7" class="r" style="font-weight:800;">PERIOD GRAND TOTALS:</td>
            <td class="r" style="color:#DC2626; font-size:9.5px; font-weight:800;">− {{ formatIndianCurrency($totalPaid ?? 0) }}</td>
            <td class="r" style="color:#059669; font-size:9.5px; font-weight:800;">+ {{ formatIndianCurrency($totalReceived ?? 0) }}</td>
            <td class="r" style="color:{{ ($netBalance ?? 0) >= 0 ? '#059669' : '#DC2626' }}; font-size:10px; font-weight:900;">
                {{ formatIndianCurrency($netBalance ?? 0) }}
            </td>
            <td></td>
        </tr>
        <tr class="double-total">
            <td colspan="7" class="r">NET FLOW SURPLUS / (DEFICIT):</td>
            <td colspan="3" class="r" style="font-size:10.5px; color:{{ ($netBalance ?? 0) >= 0 ? '#059669' : '#DC2626' }};">
                {{ formatIndianCurrency($netBalance ?? 0) }} &nbsp;<span style="font-size:8px; font-weight:600; color:#475569;">({{ ($netBalance ?? 0) >= 0 ? 'Net Inflow Surplus' : 'Net Outflow Deficit' }})</span>
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:20px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Cashier / Disbursing Officer</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Accounts Manager / Delawala Management</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Official Cash & Bank Register • Audited from System Payment Vouchers'])
</body>
</html>