@php
    $activeFirm = $firm ?? ($reportFirm ?? (session('login_type') === 'firm' && session('firm_id') ? \App\Models\Firm::find(session('firm_id')) : \App\Models\Firm::first()));
    $firmDisplayName = $activeFirm->firm_name ?? 'Delawala Infrastructure & Properties';
    
    // Check specific signature passed in or fallback to firm digital signature
    $sigUrl = $signatureUrl ?? ($activeFirm?->digital_signature_url ?? null);
    $hasSig = !empty($sigUrl) && ($hasSignature ?? ($activeFirm?->hasDigitalSignature() ?? false));
    
    $leftRole = $leftRole ?? ($leftTitle ?? 'Prepared By / In-Charge');
    $rightRole = $rightRole ?? ($rightTitle ?? ('Authorized Signatory / ' . $firmDisplayName));
@endphp

<div class="auth-block delawala-signature-block" style="margin-top: 22px; page-break-inside: avoid;">
    <div class="auth-col">
        <div style="min-height: 48px; max-height: 52px;"></div>
        <div class="auth-line">{{ $leftRole }}</div>
    </div>
    <div class="auth-col">
        <div id="delawalaSigWrapper" class="delawala-sig-wrapper" style="min-height: 48px; max-height: 52px; display: flex; align-items: flex-end; justify-content: center; margin-bottom: 3px;">
            <img id="delawalaSigImg" class="delawala-sig-img" src="{{ $sigUrl ?? '' }}" alt="Authorized Signature"
                 style="max-height: 46px; max-width: 140px; object-fit: contain; {{ $hasSig ? 'display:block;' : 'display:none;' }}; filter: grayscale(100%) contrast(120%) !important; -webkit-filter: grayscale(100%) contrast(120%) !important;">
        </div>
        <div class="auth-line">{{ $rightRole }}</div>
    </div>
</div>
