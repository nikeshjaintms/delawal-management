<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Bookings & Advance Receipts Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Property Bookings & Advance Receipts Register',
    'orientation' => 'landscape',
    'backUrl' => route('bookings.index')
])

@php
    $filters = [];
    if(request('from_date') || request('to_date') || request('booking_date_from') || request('booking_date_to')) {
        $f = request('from_date') ?: request('booking_date_from');
        $t = request('to_date') ?: request('booking_date_to');
        $filters['Period'] = ($f ? \Carbon\Carbon::parse($f)->format('d M Y') : 'Start') . ' to ' . ($t ? \Carbon\Carbon::parse($t)->format('d M Y') : 'Present');
    }
    if(request('project_id')) {
        $p = \App\Models\Project::find(request('project_id'));
        if($p) $filters['Site / Project'] = $p->project_name ?? $p->name;
    }
    if(request('status')) {
        $filters['Status'] = ucfirst(request('status'));
    }

    $totFinal = 0;
    $totAdvance = 0;
    $totPending = 0;

    foreach($bookings as $b) {
        $fAmt = (float)($b->final_amount ?? 0);
        $aAmt = (float)($b->booking_amount ?? 0);
        $pAmt = (float)($b->remaining_amount ?? max(0, $fAmt - $aAmt));

        $totFinal += $fAmt;
        $totAdvance += $aAmt;
        $totPending += $pAmt;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Property Bookings & Advance Receipts Register',
    'subtitle' => 'Token Advances, Allotment Commitments & Sundry Customer Receivables',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Booking Orders</div>
        <div class="s-value">{{ $totalCount ?? $bookings->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Gross Agreed Consideration</div>
        <div class="s-value">{{ formatIndianCurrency($totFinal) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Advance Tokens Realized</div>
        <div class="s-value">{{ formatIndianCurrency($totAdvance) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Unsettled Deal Balances</div>
        <div class="s-value">{{ formatIndianCurrency($totPending) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-bookmark"></i> Property Bookings &amp; Advance Register</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th style="width:85px;">Booking Code</th>
            <th>Customer / Allottee</th>
            <th>Property / Unit</th>
            <th>Sourcing Broker</th>
            <th style="width:75px;">Booking Date</th>
            <th class="r" style="width:105px;">Agreed Value (₹)</th>
            <th class="r" style="width:105px;">Token Advance (₹)</th>
            <th class="r" style="width:105px;">Pending Due (₹)</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $i => $b)
        @php
            $fAmt = (float)($b->final_amount ?? 0);
            $aAmt = (float)($b->booking_amount ?? 0);
            $pAmt = (float)($b->remaining_amount ?? max(0, $fAmt - $aAmt));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td style="font-weight:700; color:#1E3A8A;">{{ $b->booking_code ?: ('BK-' . $b->id) }}</td>
            <td><strong>{{ $b->customer->name ?? '—' }}</strong></td>
            <td>{{ $b->property->property_name ?? ($b->property->property_code ?? '—') }}</td>
            <td style="color:#7C3AED;">{{ $b->effective_broker_name ?: 'Direct Deal' }}</td>
            <td style="white-space:nowrap; color:#475569;">{{ $b->booking_date ? \Carbon\Carbon::parse($b->booking_date)->format('d-M-Y') : '—' }}</td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($fAmt, 2, false) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($aAmt, 2, false) }}</td>
            <td class="r" style="color:{{ $pAmt > 0 ? '#DC2626' : '#10B981' }}; font-weight:700;">{{ formatIndianCurrency($pAmt, 2, false) }}</td>
            <td class="c">
                @if(strtolower($b->status ?? '') === 'confirmed' || strtolower($b->status ?? '') === 'active')
                    <span class="badge badge-success">{{ ucfirst($b->status) }}</span>
                @elseif(strtolower($b->status ?? '') === 'cancelled')
                    <span class="badge badge-danger">Cancelled</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($b->status ?? 'Active') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="c" style="padding:24px; color:#64748B;">
                <em>No booking records found matching the specified parameters.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="6" class="r"><strong>GRAND TOTAL BOOKING ACCRUALS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totFinal) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totAdvance) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totPending) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:12px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Token advances received are held as Unearned Revenue / Customer Advances under Current Liabilities until conveyance deeds are executed and possession is delivered.
</div>

@include('admin.components.pdf-footer')
</body>
</html>