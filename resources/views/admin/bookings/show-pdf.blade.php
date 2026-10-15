@php
    $fAmt = (float)($booking->final_amount ?? 0);
    $bAmt = (float)($booking->booking_amount ?? 0);
    $rAmt = (float)($booking->remaining_amount ?? max(0, $fAmt - $bAmt));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Slip - {{ $booking->booking_code ?? ('BK-' . $booking->id) }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Booking Slip: ' . ($booking->booking_code ?? ('BK-' . $booking->id)),
    'orientation' => 'portrait',
    'backUrl' => route('bookings.show', $booking->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Official Booking Slip & Token Advance Receipt',
    'subtitle' => 'Property Allotment Reservation & Advance Token Agreement',
    'firm' => $booking->firm ?? null,
    'docRef' => $booking->booking_code ?? ('BK-' . $booking->id)
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
        <div class="info-row">
            <span class="info-label">Property Unit:</span>
            <span class="info-val"><strong>{{ $booking->property->property_name ?? '—' }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Project / Scheme:</span>
            <span class="info-val">{{ $booking->property?->project?->project_name ?? 'Standalone Scheme' }}</span>
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
    </div>
</div>

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

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Customer Signature</div>
    </div>
    <div class="auth-col">
        @php
            $bkFirm = $booking->firm ?: ($booking->property?->project?->firm);
            $bkSig = $bkFirm?->digital_signature ?? null;
            $hasBkSig = !empty($bkSig) && (
                \Illuminate\Support\Facades\Storage::disk('public')->exists($bkSig) ||
                file_exists(public_path('storage/' . $bkSig)) ||
                file_exists(public_path($bkSig))
            );
            $bkSigUrl = $hasBkSig ? (file_exists(public_path($bkSig)) ? asset($bkSig) : asset('storage/' . $bkSig)) : null;
        @endphp
        @if($bkSigUrl)
            <div style="min-height: 40px; margin-bottom: 2px;">
                <img src="{{ $bkSigUrl }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Authorized Signature">
            </div>
        @endif
        <div class="auth-line">Authorized Signatory / {{ $booking->firm->firm_name ?? 'Delawala Infrastructure' }}</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>