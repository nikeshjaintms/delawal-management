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
    'title' => 'Official Booking Slip & Token Advance',
    'subtitle' => 'Property Reservation Agreement',
    'docRef' => $booking->booking_code ?? ('BK-' . $booking->id)
])

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Booking Code</div>
        <div class="s-value" style="font-size:14px;">{{ $booking->booking_code ?: ('BK-' . $booking->id) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Token Advance Paid</div>
        <div class="s-value">₹{{ number_format($booking->booking_amount, 2) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Final Agreed Amount</div>
        <div class="s-value">₹{{ number_format($booking->final_amount, 2) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-user"></i> Customer Details</div>
        <div class="info-row">
            <span class="info-label">Customer Name:</span>
            <span class="info-val">{{ $booking->customer->name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Mobile Number:</span>
            <span class="info-val">{{ $booking->customer->mobile ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-val">{{ $booking->customer->email ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">City:</span>
            <span class="info-val">{{ $booking->customer->city ?? 'Dahegam' }}</span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-building"></i> Property & Booking Info</div>
        <div class="info-row">
            <span class="info-label">Property:</span>
            <span class="info-val">{{ $booking->property->property_name ?? '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Project:</span>
            <span class="info-val">{{ $booking->property?->project?->project_name ?? 'Direct Property' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Booking Date:</span>
            <span class="info-val">{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') : '—' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-val"><span class="badge badge-success">{{ ucfirst($booking->status) }}</span></span>
        </div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-calculator"></i> Payment Summary</div>
<table>
    <thead>
        <tr>
            <th>Description</th>
            <th class="r">Amount (₹)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Total Agreed Sale / Booking Amount</td>
            <td class="r"><strong>₹{{ number_format($booking->final_amount, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Advance Token Paid</td>
            <td class="r" style="color:#059669; font-weight:700;">₹{{ number_format($booking->booking_amount, 2) }}</td>
        </tr>
        <tr>
            <td>Remaining Balance Payable</td>
            <td class="r" style="color:#B45309; font-weight:700;">₹{{ number_format($booking->remaining_amount, 2) }}</td>
        </tr>
    </tbody>
</table>

<div class="auth-block">
    <div class="auth-col">
        <div class="auth-line">Customer Signature</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">Authorized Signatory &amp; Stamp</div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>