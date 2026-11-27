@php
$fAmt = (float)($booking->final_amount ?? 0);
$bAmt = (float)($booking->booking_amount ?? 0);
$rAmt = (float)($booking->remaining_amount ?? max(0, $fAmt - $bAmt));

$allProps = $booking->all_properties;
if ($allProps->isEmpty() && $booking->property) {
$allProps = collect([$booking->property]);
}

$projectNames = $allProps->map(fn($p) => $p->project?->project_name)->filter()->unique()->values();
if ($projectNames->isEmpty() && $booking->property?->project?->project_name) {
$projectNames = collect([$booking->property->project->project_name]);
}
$projectName = $projectNames->isNotEmpty() ? $projectNames->join(', ') : null;

$otherBookings = collect();
if ($booking->customer_id && $booking->relationLoaded('customer') && $booking->customer?->bookings) {
$otherBookings = $booking->customer->bookings
->where('id', '!=', $booking->id)
->whereNotIn('status', ['cancelled']);
} elseif ($booking->customer_id) {
$otherBookings = \App\Models\Booking::where('customer_id', $booking->customer_id)
->where('id', '!=', $booking->id)
->whereNotIn('status', ['cancelled'])
->with(['properties.propertyType', 'property.propertyType', 'properties.project', 'property.project'])
->get();
}
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Slip - {{ $booking->booking_code ?? ('BK-' . $booking->id) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
    <style>
        .prop-chip {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #EFF6FF;
            color: #1E3A8A;
            border: 1px solid #BFDBFE;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            line-height: 1.3;
        }

        .prop-chip strong {
            font-weight: 700;
            color: #1E3A8A;
        }

        @media print {
            .prop-chip {
                background: #F1F5F9 !important;
                border: 1px solid #000000 !important;
                color: #000000 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .prop-chip strong {
                color: #000000 !important;
            }
        }
    </style>
</head>

<body>
    @include('admin.components.pdf-action-bar', [
    'title' => 'Booking Slip: ' . ($booking->booking_code ?? ('BK-' . $booking->id)),
    'orientation' => 'portrait',
    'backUrl' => route('bookings.show', $booking->id)
    ])

    @include('admin.components.pdf-header', [
    'title' => 'Booking Slip & Token Advance Receipt',
    'subtitle' => 'Property Allotment Reservation & Advance Token Agreement',
    'firm' => $booking->firm ?? null,
    'docRef' => $booking->booking_code ?? ('BK-' . $booking->id),
    'projectName' => $projectName,
    'hideState' => true,
    'hideOwner' => true,
    ])

    <div class="stat-row">
        <div class="stat-box s-blue">
            <div class="s-label">Booking Reference</div>
            <div class="s-value" style="font-size:13px; color:#1E3A8A;">{{ $booking->booking_code ?: ('BK-' . $booking->id) }}</div>
        </div>
        <div class="stat-box s-purple">
            <div class="s-label">Agreed Final Value</div>
            <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($fAmt) }}</div>
        </div>
        <div class="stat-box s-green">
            <div class="s-label">Token Advance Realized</div>
            <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($bAmt) }}</div>
        </div>
        <div class="stat-box s-red">
            <div class="s-label">Remaining Balance Due</div>
            <div class="s-value" style="font-size:13px;">{{ formatIndianCurrency($rAmt) }}</div>
        </div>
    </div>

    <div class="grid-2">
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-user"></i> Customer &amp; Intermediary Details</div>
            <div class="info-row">
                <span class="info-label">Customer / Allottee:</span>
                <span class="info-val"><strong>{{ $booking->customer->name ?? '—' }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Mobile Number:</span>
                <span class="info-val">{{ $booking->customer->mobile ?: ($booking->customer->phone ?? '—') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Seller / Owner:</span>
                <span class="info-val">{{ $booking->effective_seller_name ?: 'Direct Property' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Sourcing Broker:</span>
                <span class="info-val">{{ $booking->effective_broker_name ?: 'Direct Deal' }}</span>
            </div>
        </div>

        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-building"></i> Property &amp; Reservation Info</div>
            <div class="info-row" style="align-items: flex-start;">
                <span class="info-label">{{ $allProps->count() > 1 ? 'Booked Units (' . $allProps->count() . '):' : 'Property Unit:' }}</span>
                <span class="info-val">
                    @if($allProps->count() > 1)
                    <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end;">
                        @foreach($allProps as $p)
                        <span class="prop-chip">
                            <strong>{{ $p->property_name }}</strong>
                        </span>
                        @endforeach
                    </div>
                    @else
                    <strong>{{ $allProps->first()->property_name ?? ($booking->property->property_name ?? '—') }}</strong>
                    @if(($allProps->first()->unit_no ?? $booking->property?->unit_no))
                    <span style="font-size: 10px; color: #6B7280;">(Unit: #{{ $allProps->first()->unit_no ?? $booking->property->unit_no }})</span>
                    @endif
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Booking Date:</span>
                <span class="info-val">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '—' }}</span>
            </div>
            @if($booking->agreement_date)
            <div class="info-row">
                <span class="info-label">Target Agreement Date:</span>
                <span class="info-val">{{ \Carbon\Carbon::parse($booking->agreement_date)->format('d M Y') }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Reservation Status:</span>
                <span class="info-val"><span class="badge badge-success">{{ ucfirst($booking->status) }}</span></span>
            </div>
            @if($otherBookings->isNotEmpty())
            @php
            $otherProps = collect();
            foreach ($otherBookings as $ob) {
            foreach ($ob->all_properties as $op) {
            $otherProps->push($op->property_name);
            }
            }
            @endphp
            @if($otherProps->isNotEmpty())
            <div class="info-row" style="align-items: flex-start; margin-top: 2px;">
                <span class="info-label" style="font-size: 8px; color: #64748B;">Other Booked Units:</span>
                <span class="info-val" style="font-size: 8.5px; color: #334155; font-weight: 600;">
                    {{ $otherProps->unique()->join(', ') }}
                </span>
            </div>
            @endif
            @endif
        </div>
    </div>

    @if($allProps->count() > 1)
    <div class="section-label"><i class="fa-solid fa-shapes"></i> Booked Units Schedule ({{ $allProps->count() }} Properties)</div>
    <table>
        <thead>
            <tr>
                <th class="c" style="width: 38px;">#</th>
                <th>Property / Unit Name</th>
                <th class="c" style="width: 80px;">Unit No</th>
                <th style="width: 90px;">Type</th>
                <th style="width: 100px;">Area / Size</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allProps as $idx => $p)
            <tr>
                <td class="c">{{ $idx + 1 }}</td>
                <td><strong>{{ $p->property_name }}</strong></td>
                <td class="c">{{ $p->unit_no ? ('#' . $p->unit_no) : '—' }}</td>
                <td>{{ $p->propertyType->name ?? 'Plot' }}</td>
                <td>{{ $p->size ? ($p->size . ' ' . ($p->size_unit ?: 'sq.ft')) : '—' }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    @endif

    <div class="section-label"><i class="fa-solid fa-calculator"></i> Consideration &amp; Advance Token Statement</div>
    <table>
        <thead>
            <tr>
                <th>Transaction Particulars</th>
                <th class="r" style="width:160px;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Agreed Sale / Booking Consideration</td>
                <td class="r" style="font-weight:700; color:#1E3A8A;">{{ formatIndianCurrency($fAmt) }}</td>
            </tr>
            <tr>
                <td>Advance Token Received to Date (Held as Deposit)</td>
                <td class="r" style="color:#059669; font-weight:700;">{{ formatIndianCurrency($bAmt) }}</td>
            </tr>
            <tr class="double-total">
                <td><strong>Remaining Balance Payable upon Conveyance Deed Execution</strong></td>
                <td class="r" style="color:{{ $rAmt > 0 ? '#DC2626' : '#10B981' }}; font-size:11px;"><strong>{{ formatIndianCurrency($rAmt) }}</strong></td>
            </tr>
        </tbody>
    </table>

    @php $hasCustomAuthBlock = true; @endphp
    @include('admin.components.pdf-signature', [
    'leftRole' => 'Customer Signature',
    'rightRole' => 'Authorized Signatory / ' . ($booking->firm->firm_name ?? 'Delawala Infrastructure'),
    'firm' => $booking->firm ?: ($booking->property?->project?->firm)
    ])

    @include('admin.components.pdf-footer', [
    'hideFooterNote' => true,
    'hideFooterFirm' => true,
    ])
</body>

</html>