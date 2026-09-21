@extends('admin.layouts.app')
@section('title','Loan Details')
@section('page-title','Loan Management')
@section('content')
@php
    $isGiven = $loan->isGiven();
@endphp
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 28px;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.loan-hero {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding-bottom: 24px;
    margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
.loan-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    flex-shrink: 0;
}
.loan-icon.taken {
    background: rgba(59, 130, 246, 0.18) !important;
    border: 2px solid rgba(59, 130, 246, 0.40) !important;
    color: #60A5FA !important;
}
.loan-icon.given {
    background: rgba(16, 185, 129, 0.18) !important;
    border: 2px solid rgba(16, 185, 129, 0.40) !important;
    color: #34D399 !important;
}
.loan-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.loan-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.nature-badge-hero {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
}
.nb-taken { background: rgba(59, 130, 246, 0.20) !important; color: #60A5FA !important; border: 1.5px solid rgba(59, 130, 246, 0.45) !important; }
.nb-given { background: rgba(16, 185, 129, 0.20) !important; color: #34D399 !important; border: 1.5px solid rgba(16, 185, 129, 0.45) !important; }

.badge-emi-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
}
.bes-has-emi { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.bes-no-emi  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }

.loan-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    white-space: nowrap !important;
}
.ls-active    { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.ls-completed { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.ls-closed    { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.ls-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.section-title {
    font-size: 12px;
    font-weight: 800;
    color: #60A5FA !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 16px;
    margin-top: 24px;
    padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px !important;
    transition: all .25s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.40) !important; transform: translateY(-2px); }

.detail-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 7px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 14.5px; font-weight: 600; color: #FFFFFF !important; word-break: break-word; }
.detail-value.amount-big { font-size: 22px; font-weight: 800; color: #FBBF24 !important; }
.detail-value.empty { color: #94A3B8 !important; font-style: italic; font-weight: 400; }

.progress-wrap {
    width: 100%;
    background: rgba(255, 255, 255, 0.10);
    border-radius: 8px;
    height: 10px;
    overflow: hidden;
    margin-bottom: 12px;
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.progress-bar { height: 100%; background: linear-gradient(90deg, #3B82F6, #10B981); border-radius: 8px; }

.amt-paid { font-size: 18px; font-weight: 800; color: #34D399 !important; }
.amt-pending { font-size: 18px; font-weight: 800; color: #F87171 !important; }

.form-actions {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}

.btn-gold {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.38);
    text-decoration: none !important;
}
.btn-gold:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(37, 99, 235, 0.52);
    color: #FFFFFF !important;
}

.btn-green {
    background: #10B981 !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    border: 1px solid #34D399 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(16, 185, 129, 0.38);
    text-decoration: none !important;
}
.btn-green:hover {
    background: #059669 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(16, 185, 129, 0.52);
    color: #FFFFFF !important;
}

.btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 22px;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    font-size: 13.5px;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px;
    text-decoration: none !important;
    transition: all .25s ease;
    cursor: pointer;
}
.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
}

.alert-success {
    background: rgba(34, 197, 94, 0.18) !important;
    border: 1px solid rgba(34, 197, 94, 0.35) !important;
    color: #34D399 !important;
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 24px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ── Direct Pay Modal ── */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(8, 12, 22, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 20px;
}
.modal.active { display: flex; }
.modal-box {
    background: rgba(20, 27, 41, 0.95) !important;
    backdrop-filter: blur(28px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(28px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 20px !important;
    padding: 28px !important;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6) !important;
    color: #FFFFFF !important;
    animation: modalScaleIn 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes modalScaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF !important; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close { background: none; border: none; font-size: 24px; color: #94A3B8; cursor: pointer; line-height: 1; }
.modal-close:hover { color: #FFFFFF; }
.form-group-modal { margin-bottom: 16px; }
.form-label-modal { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; }
.form-label-modal span { color: #F87171; }
.form-control-modal {
    width: 100%; padding: 10px 14px;
    background: rgba(16, 22, 34, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15);
    border-radius: 10px; font-size: 14px; color: #FFFFFF; outline: none;
    box-sizing: border-box;
}
.form-control-modal:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
select.form-control-modal option { background: #101622; color: #FFFFFF; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); margin-top: 10px; }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.type-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }

.btn-delete {
    background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important; border-radius: 8px; width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center; font-size: 13px;
    cursor: pointer; transition: all .2s ease;
}
.btn-delete:hover { background: rgba(239, 68, 68, 0.35) !important; color: #FFFFFF !important; transform: scale(1.05); }

.meta-info { display: flex; gap: 20px; font-size: 12px; color: #94A3B8; margin-top: 24px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); flex-wrap: wrap; }
.meta-item { display: flex; align-items: center; gap: 6px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Loan Details</h2>
        <p>Complete loan record and repayment overview.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    <div class="loan-hero">
        <div class="loan-icon {{ $isGiven ? 'given' : 'taken' }}">
            <i class="fa-solid {{ $isGiven ? 'fa-handshake-angle' : 'fa-hand-holding-dollar' }}"></i>
        </div>
        <div class="loan-hero-info">
            <h3>{{ $loan->party_display_name }}</h3>
            <p>
                {{ $loan->loan_type }}
                @if($loan->has_emi && $loan->total_emi_months)
                    &nbsp;·&nbsp; {{ $loan->total_emi_months }} months EMI
                @endif
            </p>
            <div class="hero-badges">
                @if($isGiven)
                    <span class="nature-badge-hero nb-given"><i class="fa-solid fa-handshake-angle"></i> Loan Given</span>
                @else
                    <span class="nature-badge-hero nb-taken"><i class="fa-solid fa-hand-holding-dollar"></i> Loan Taken</span>
                @endif
                <span class="amount-big" style="font-size:18px;font-weight:800;color:#FBBF24;">₹{{ number_format($loan->loan_amount,2) }}</span>
                <span class="loan-status ls-{{ strtolower($loan->loan_status) }}">{{ $loan->loan_status }}</span>
                @if($loan->has_emi)
                    <span class="badge-emi-status bes-has-emi"><i class="fa-solid fa-calendar-check"></i> EMI Enabled</span>
                @else
                    <span class="badge-emi-status bes-no-emi"><i class="fa-solid fa-ban"></i> No EMI (Manual)</span>
                @endif
            </div>
        </div>
    </div>

    @if($isGiven)
        {{-- Loan Given Details --}}
        <div class="section-title"><i class="fa-solid fa-user-check"></i> Borrower Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
                <div class="detail-value">{{ $loan->firm_names }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-user"></i> Borrower / Customer</div>
                @if($loan->customer)
                    <div class="detail-value">{{ $loan->customer->name }} <span style="font-size:12px;color:#34D399;">(Registered Customer)</span><div style="font-size:12px;color:#94A3B8;margin-top:3px;">{{ $loan->customer->mobile }}</div></div>
                @elseif($loan->person_name)
                    <div class="detail-value">{{ $loan->person_name }}</div>
                @else
                    <div class="detail-value empty">—</div>
                @endif
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile Number</div>
                <div class="detail-value">{{ $loan->customer ? $loan->customer->mobile : ($loan->mobile_number ?? '—') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-people-arrows"></i> Relationship / Purpose</div>
                <div class="detail-value">{{ $loan->relationship ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Loan Type</div>
                <div class="detail-value">{{ $loan->loan_type }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Linked Property</div>
                @if($loan->property)
                    <div class="detail-value">{{ $loan->property->property_name }}{{ $loan->property->property_code?' ('.$loan->property->property_code.')':'' }}</div>
                @else
                    <div class="detail-value empty">Not linked</div>
                @endif
            </div>
        </div>
    @else
        {{-- Loan Taken Details --}}
        <div class="section-title"><i class="fa-solid fa-landmark"></i> Bank / Lender Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
                <div class="detail-value">{{ $loan->firm_names }}</div>
            </div>
            @if($loan->loan_type === 'Personal Loan')
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-user"></i> Lender Person Name</div>
                    <div class="detail-value">{{ $loan->person_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-people-arrows"></i> Relationship</div>
                    <div class="detail-value">{{ $loan->relationship ?? '—' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile Number</div>
                    <div class="detail-value">{{ $loan->mobile_number ?? '—' }}</div>
                </div>
            @else
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-landmark"></i> Bank Name</div>
                    <div class="detail-value">{{ $loan->bank_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Loan Type</div>
                    <div class="detail-value">{{ $loan->loan_type }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
                    @if($loan->customer)
                        <div class="detail-value">{{ $loan->customer->name }}<div style="font-size:12px;color:#94A3B8;margin-top:3px;">{{ $loan->customer->mobile }}</div></div>
                    @else
                        <div class="detail-value empty">Not linked</div>
                    @endif
                </div>
            @endif
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
                @if($loan->property)
                    <div class="detail-value">{{ $loan->property->property_name }}{{ $loan->property->property_code?' ('.$loan->property->property_code.')':'' }}</div>
                @else
                    <div class="detail-value empty">Not linked</div>
                @endif
            </div>
        </div>
    @endif

    {{-- Financial Details Section --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
    @if($loan->has_emi)
        <div class="detail-grid-3">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> {{ $isGiven ? 'Given Loan Amount' : 'Loan Amount' }}</div>
                <div class="detail-value amount-big">₹{{ number_format($loan->loan_amount,2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-percent"></i> Interest Rate</div>
                <div class="detail-value">{{ $loan->interest_rate ? $loan->interest_rate . '% p.a.' : '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-wallet"></i> Monthly EMI</div>
                <div class="detail-value" style="color:#F87171;font-size:16px;font-weight:700;">₹{{ number_format($loan->emi_amount,2)}} / month</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calendar-days"></i> Total EMI Months</div>
                <div class="detail-value">{{ $loan->total_emi_months }} months</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-regular fa-calendar"></i> Loan Duration</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }} @if($loan->loan_end_date) <span style="color:#94A3B8;font-weight:400;">to</span> {{ \Carbon\Carbon::parse($loan->loan_end_date)->format('d M Y') }} @endif</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Loan Status</div>
                <div class="detail-value"><span class="loan-status ls-{{ strtolower($loan->loan_status) }}">{{ $loan->loan_status }}</span></div>
            </div>
        </div>
    @else
        <div class="detail-grid-3">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> {{ $isGiven ? 'Given Loan Amount' : 'Total Loan Amount' }}</div>
                <div class="detail-value amount-big">₹{{ number_format($loan->loan_amount,2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calendar"></i> {{ $isGiven ? 'Given Date' : 'Loan Date' }}</div>
                <div class="detail-value">{{ $loan->loan_start_date ? \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') : '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
                <div class="detail-value">{{ $loan->paymentMode->name ?? '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calculator"></i> EMI Option</div>
                <div class="detail-value"><span class="badge-emi-status bes-no-emi">Disabled (Direct Repayment)</span></div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Loan Status</div>
                <div class="detail-value"><span class="loan-status ls-{{ strtolower($loan->loan_status) }}">{{ $loan->loan_status }}</span></div>
            </div>
        </div>
    @endif

    {{-- Payment Progress & Outstanding --}}
    <div class="section-title"><i class="fa-solid fa-chart-line"></i> {{ $isGiven ? 'Recovery Progress & Receivable Balance' : 'Payment Progress & Outstanding Balance' }}</div>
    @php $pct = $loan->loan_amount > 0 ? min(100, round(($loan->paid_amount / $loan->loan_amount) * 100)) : 0; @endphp
    <div class="detail-item" style="padding: 22px 24px;">
        <div class="progress-wrap">
            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-top:10px;">
            <div>
                <span class="amt-paid">₹{{ number_format($loan->paid_amount,2) }}</span>
                <span style="color:#94A3B8;font-size:13px;font-weight:600;"> {{ $isGiven ? 'Total Received / Recovered' : 'Total Paid' }}</span>
            </div>
            <div style="font-size:14px;font-weight:800;color:#60A5FA;">{{ $pct }}% {{ $isGiven ? 'Recovered' : 'Completed' }}</div>
            <div>
                <span class="amt-pending">₹{{ number_format($loan->pending_amount,2) }}</span>
                <span style="color:#F87171;font-size:13px;font-weight:700;"> {{ $isGiven ? 'Pending to Receive' : 'Total Pending Balance' }}</span>
            </div>
        </div>
    </div>

    @if($loan->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item"><div class="detail-value" style="font-weight:400;line-height:1.7;">{{ $loan->remarks }}</div></div>
    @endif

    {{-- Payment History --}}
    <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span><i class="fa-solid fa-clock-rotate-left"></i> {{ $isGiven ? 'Repayment Receipts History' : 'Payment History' }} ({{ $loan->payments->count() }})</span>
        @if($loan->pending_amount > 0)
        <button type="button" class="btn-green" style="padding:6px 14px;font-size:12.5px;border-radius:8px;" onclick="openDirectPayModal()">
            <i class="fa-solid fa-plus"></i> {{ $isGiven ? 'Record Payment Received' : 'Record Payment' }}
        </button>
        @endif
    </div>

    @if($loan->payments->count() > 0)
        <div class="table-container" style="margin-bottom:24px;">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Date</th>
                        <th style="text-align:right;">{{ $isGiven ? 'Amount Received' : 'Amount Paid' }}</th>
                        <th>Payment Mode</th>
                        <th>Reference / UTR No.</th>
                        <th>Remarks</th>
                        <th>Recorded By</th>
                        <th style="text-align:center;width:80px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loan->payments->sortByDesc('payment_date') as $idx => $payment)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="color:#FFFFFF;font-weight:600;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td style="text-align:right;color:#34D399;font-weight:800;font-size:14.5px;">₹{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="type-chip" style="color:#60A5FA;border-color:rgba(59,130,246,0.3);background:rgba(59,130,246,0.12);">
                                {{ $payment->paymentMode->name ?? $payment->payment_mode ?? 'Direct' }}
                            </span>
                        </td>
                        <td style="color:#CBD5E1;">{{ $payment->reference_no ?? '—' }}</td>
                        <td style="color:#CBD5E1;max-width:220px;white-space:normal;">{{ $payment->remarks ?? '—' }}</td>
                        <td style="color:#94A3B8;font-size:12px;">{{ $payment->creator->name ?? 'Admin' }}</td>
                        <td style="text-align:center;">
                            <form action="{{ route('loans.payments.destroy', [$loan->id, $payment->id]) }}" method="POST" id="del-payment-{{ $payment->id }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Payment Record" onclick="confirmDeletePayment({{ $payment->id }}, '{{ number_format($payment->amount, 2) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="detail-item" style="text-align:center;padding:28px 20px;color:#94A3B8;margin-bottom:24px;">
            <i class="fa-solid fa-receipt" style="font-size:28px;opacity:0.35;display:block;margin-bottom:10px;"></i>
            <span style="font-size:14px;font-weight:500;">No payment transactions recorded yet. Click <strong>"{{ $isGiven ? 'Record Payment Received' : 'Record Payment' }}"</strong> to record a transaction.</span>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> Created: {{ $loan->created_at->format('d M Y, h:i A') }}</div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> Updated: {{ $loan->updated_at->format('d M Y, h:i A') }}</div>
    </div>

    <div class="form-actions">
        @if($loan->has_emi && $loan->emiSchedules->count() > 0)
            <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="btn-gold"><i class="fa-solid fa-calendar-days"></i> View EMI Schedule</a>
        @endif
        @if($loan->pending_amount > 0)
            <button type="button" class="btn-green" onclick="openDirectPayModal()">
                <i class="fa-solid {{ $isGiven ? 'fa-hand-holding-dollar' : 'fa-money-bill-wave' }}"></i> {{ $isGiven ? 'Record Payment Received' : 'Record Payment' }}
            </button>
        @endif
        <a href="{{ route('loans.edit', $loan->id) }}" class="btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit Loan</a>
        <a href="{{ route('loans.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>

{{-- Direct Payment Modal --}}
<div class="modal" id="directPayModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-money-bill-wave" style="color:#34D399;"></i> {{ $isGiven ? 'Record Repayment Received' : 'Record Loan Payment' }}</h3>
            <button type="button" class="modal-close" onclick="closeDirectPayModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('loans.record-payment', $loan->id) }}">
            @csrf
            <div style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.3);border-radius:12px;padding:14px 16px;margin-bottom:18px;">
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;color:#CBD5E1;margin-bottom:4px;">
                    <span>{{ $isGiven ? 'Given Loan Amount:' : 'Total Loan Amount:' }}</span>
                    <strong style="color:#FFFFFF;">₹{{ number_format($loan->loan_amount, 2) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:13.5px;color:#CBD5E1;margin-bottom:4px;">
                    <span>{{ $isGiven ? 'Already Received:' : 'Already Paid:' }}</span>
                    <strong style="color:#34D399;">₹{{ number_format($loan->paid_amount, 2) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;font-size:14px;color:#CBD5E1;">
                    <span>{{ $isGiven ? 'Pending to Collect:' : 'Current Pending Balance:' }}</span>
                    <strong style="color:#F87171;">₹{{ number_format($loan->pending_amount, 2) }}</strong>
                </div>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">{{ $isGiven ? 'Received Amount (₹)' : 'Payment Amount (₹)' }} <span>*</span></label>
                <input type="number" step="0.01" name="paid_amount" class="form-control-modal" placeholder="0.00" max="{{ $loan->pending_amount }}" required>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Date <span>*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control-modal" required>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Mode</label>
                <select name="payment_mode_id" class="form-control-modal">
                    <option value="">— Select Payment Mode —</option>
                    @if(isset($paymentModes))
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}" {{ $loan->payment_mode_id == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Reference / UTR / Cheque No.</label>
                <input type="text" name="reference_no" class="form-control-modal" placeholder="Optional reference number">
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Remarks</label>
                <textarea name="remarks" class="form-control-modal" rows="2" placeholder="Payment notes..."></textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:22px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.1);">
                <button type="submit" class="btn-green" style="flex:1;justify-content:center;"><i class="fa-solid fa-check"></i> {{ $isGiven ? 'Save Receipt' : 'Submit Payment' }}</button>
                <button type="button" class="btn-outline" onclick="closeDirectPayModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openDirectPayModal() {
    document.getElementById('directPayModal').classList.add('active');
}
function closeDirectPayModal() {
    document.getElementById('directPayModal').classList.remove('active');
}
document.getElementById('directPayModal').addEventListener('click', function(e) {
    if (e.target === this) closeDirectPayModal();
});

function confirmDeletePayment(id, amount) {
    Swal.fire({
        title: 'Delete Payment?',
        html: 'Are you sure you want to delete payment of <strong>₹' + amount + '</strong>?<br><small style="color:#64748B;">The loan pending balance will be recalculated accordingly.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-payment-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
