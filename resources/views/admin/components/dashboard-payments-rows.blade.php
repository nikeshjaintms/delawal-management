@forelse($recentPayments as $payment)
    <tr>
        <td style="white-space: nowrap;">
            <div style="font-weight: 700; color: #FFFFFF;">
                {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}
            </div>
            @if($payment->created_at)
                <small style="color: #94A3B8; font-size: 11px; display: inline-flex; align-items: center; gap: 3px; margin-top: 3px;" title="Recorded in system on {{ $payment->created_at->format('d M Y, h:i A') }}">
                    <i class="fa-regular fa-clock" style="font-size: 10px; color: #38BDF8;"></i>
                    Rec: <strong style="color: #CBD5E1;">{{ $payment->created_at->format('d M') }}</strong>
                </small>
            @endif
        </td>
        <td>
            <strong style="color: #F8FAFC;">{{ $payment->customer->name ?? '-' }}</strong>
            @if(!empty($payment->customer->phone))
                <div style="font-size: 11px; color: #94A3B8;">{{ $payment->customer->phone }}</div>
            @endif
        </td>
        <td>
            <div style="color: #CBD5E1; font-weight: 600;">
                {{ $payment->property->name ?? $payment->property->property_name ?? '-' }}
            </div>
            @if(!empty($payment->property->plot_number))
                <small style="color: #64748B; font-size: 11px;">Plot #{{ $payment->property->plot_number }}</small>
            @endif
        </td>
        <td class="amt-strong amt-green" style="font-size: 13.5px; font-weight: 800; white-space: nowrap;">
            ₹{{ number_format($payment->payment_amount, 2) }}
        </td>
        <td>
            @php
                $mode = strtoupper($payment->payment_mode ?? 'DIRECT');
                $badgeClass = 'info';
                if (str_contains($mode, 'CASH')) {
                    $badgeClass = 'info';
                } elseif (str_contains($mode, 'CHEQUE')) {
                    $badgeClass = 'warning';
                } elseif (str_contains($mode, 'ONLINE') || str_contains($mode, 'NEFT') || str_contains($mode, 'RTGS') || str_contains($mode, 'UPI')) {
                    $badgeClass = 'success';
                }
            @endphp
            <span class="ds-badge {{ $badgeClass }}">{{ $payment->payment_mode ?? 'Direct' }}</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" style="text-align: center; padding: 26px 16px; color: #94A3B8;">
            <i class="fa-solid fa-calendar-xmark" style="font-size: 24px; color: #F59E0B; margin-bottom: 8px; display: block;"></i>
            <div style="font-size: 13px; font-weight: 700; color: #E2E8F0;">No payments found for the selected date range.</div>
            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 4px;">Try selecting different dates or view all recent payments directly.</div>
            <div style="margin-top: 10px;">
                <button type="button" class="pdf-btn-reset" onclick="resetPaymentDateFilter()" style="font-size: 11.5px; padding: 5px 12px; display: inline-flex;">
                    <i class="fa-solid fa-rotate-left"></i> View All Recent (Direct)
                </button>
            </div>
        </td>
    </tr>
@endforelse

@if(!empty($paymentIsFiltered) && $recentPayments->count() > 0)
    <tr style="background: rgba(16, 185, 129, 0.08); font-weight: 800; border-top: 2px solid rgba(16, 185, 129, 0.35);">
        <td colspan="3" style="color: #34D399; text-transform: uppercase; letter-spacing: 0.5px; font-size: 11.5px; padding: 10px 14px;">
            <i class="fa-solid fa-chart-line"></i> Total in Selected Period ({{ $recentPayments->count() }} Payments)
        </td>
        <td class="amt-strong" style="color: #10B981; font-size: 14px; padding: 10px 14px; white-space: nowrap;">
            ₹{{ number_format($paymentTotalAmount, 2) }}
        </td>
        <td style="padding: 10px 14px;">
            <span class="ds-badge success">Filtered</span>
        </td>
    </tr>
@endif
