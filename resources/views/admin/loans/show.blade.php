@extends('admin.layouts.app')
@section('title','Loan Details')
@section('page-title','Loan Management')
@section('content')
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
    background: rgba(59, 130, 246, 0.18) !important;
    border: 2px solid rgba(59, 130, 246, 0.40) !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: #60A5FA !important;
    flex-shrink: 0;
}
.loan-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.loan-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

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

.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #94A3B8 !important; font-weight: 400; font-style: italic; }

.amount-big { font-size: 22px; font-weight: 800; color: #60A5FA !important; }
.amt-paid { font-size: 16px; font-weight: 800; color: #34D399 !important; }
.amt-pending { font-size: 16px; font-weight: 800; color: #F87171 !important; }

.progress-wrap {
    width: 100%;
    background: rgba(255, 255, 255, 0.10);
    border-radius: 8px;
    height: 10px;
    overflow: hidden;
    margin-bottom: 12px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.progress-bar { height: 100%; border-radius: 8px; background: linear-gradient(90deg, #3B82F6, #10B981); }

.meta-info {
    margin-top: 24px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}
.meta-item {
    font-size: 12.5px;
    color: #CBD5E1 !important;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}
.meta-item i { color: #60A5FA !important; }

.form-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}

.btn-gold {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 11px 24px;
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
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Loan Details</h2><p>Complete loan record overview.</p></div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    <div class="loan-hero">
        <div class="loan-icon"><i class="fa-solid fa-landmark"></i></div>
        <div class="loan-hero-info">
            <h3>{{ $loan->loan_type === 'Personal Loan' ? $loan->person_name : $loan->bank_name }}</h3>
            <p>{{ $loan->loan_type }} @if($loan->loan_type === 'Business Loan') &nbsp;·&nbsp; {{ $loan->total_emi_months }} months @endif</p>
            <div class="hero-badges">
                <span class="amount-big">₹{{ number_format($loan->loan_amount,2) }}</span>
                <span class="loan-status ls-{{ strtolower($loan->loan_status) }}">{{ $loan->loan_status }}</span>
            </div>
        </div>
    </div>

    @if($loan->loan_type === 'Personal Loan')
        <div class="section-title"><i class="fa-solid fa-circle-info"></i> Loan Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
                <div class="detail-value">{{ $loan->firm_names }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-user"></i> Person Name</div>
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
        </div>

        <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
        <div class="detail-grid-3">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Loan Amount</div>
                <div class="detail-value amount-big">₹{{ number_format($loan->loan_amount,2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calendar"></i> Loan Date</div>
                <div class="detail-value">{{ $loan->loan_start_date ? \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') : '—' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
                <div class="detail-value">{{ $loan->paymentMode->name ?? '—' }}</div>
            </div>
        </div>
    @else
        <div class="section-title"><i class="fa-solid fa-circle-info"></i> Loan Information</div>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
                <div class="detail-value">{{ $loan->firm_names }}</div>
            </div>
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
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
                @if($loan->property)
                    <div class="detail-value">{{ $loan->property->property_name }}{{ $loan->property->property_code?' ('.$loan->property->property_code.')':'' }}</div>
                @else
                    <div class="detail-value empty">Not linked</div>
                @endif
            </div>
        </div>

        <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Financial Details</div>
        <div class="detail-grid-3">
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Loan Amount</div>
                <div class="detail-value amount-big">₹{{ number_format($loan->loan_amount,2) }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-percent"></i> Interest Rate</div>
                <div class="detail-value">{{ $loan->interest_rate }}% p.a.</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-wallet"></i> EMI Amount</div>
                <div class="detail-value" style="color:#F87171;font-size:16px;font-weight:700;">₹{{ number_format($loan->emi_amount,2)}} / month</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calendar-days"></i> Total EMI Months</div>
                <div class="detail-value">{{ $loan->total_emi_months }} months</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-regular fa-calendar"></i> Loan Duration</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }} <span style="color:#94A3B8;font-weight:400;">to</span> {{ \Carbon\Carbon::parse($loan->loan_end_date)->format('d M Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Loan Status</div>
                <div class="detail-value"><span class="loan-status ls-{{ strtolower($loan->loan_status) }}">{{ $loan->loan_status }}</span></div>
            </div>
        </div>
    @endif

    <div class="section-title"><i class="fa-solid fa-chart-line"></i> Payment Progress</div>
    @php $pct = $loan->loan_amount > 0 ? round(($loan->paid_amount / $loan->loan_amount) * 100) : 0; @endphp
    <div class="detail-item">
        <div class="progress-wrap">
            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div><span class="amt-paid">₹{{ number_format($loan->paid_amount,2) }}</span> <span style="color:#94A3B8;font-size:13px;">paid</span></div>
            <div style="font-size:13px;font-weight:700;color:#60A5FA;">{{ $pct }}%</div>
            <div><span class="amt-pending">₹{{ number_format($loan->pending_amount,2) }}</span> <span style="color:#94A3B8;font-size:13px;">pending</span></div>
        </div>
    </div>

    @if($loan->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item"><div class="detail-value" style="font-weight:400;line-height:1.7;">{{ $loan->remarks }}</div></div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> Created: {{ $loan->created_at->format('d M Y, h:i A') }}</div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> Updated: {{ $loan->updated_at->format('d M Y, h:i A') }}</div>
    </div>

    <div class="form-actions">
        @if($loan->loan_type === 'Business Loan')
            <a href="{{ route('loans.emi-schedule', $loan->id) }}" class="btn-gold"><i class="fa-solid fa-calendar-days"></i> View EMI Schedule</a>
        @endif
        <a href="{{ route('loans.edit', $loan->id) }}" class="btn-outline"><i class="fa-regular fa-pen-to-square"></i> Edit Loan</a>
        <a href="{{ route('loans.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
    </div>
</div>
@endsection
