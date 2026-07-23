@extends('admin.layouts.app')
@section('title','Loan Details')
@section('page-title','Loan Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:1000px;margin:0 auto;}
    .loan-hero{display:flex;align-items:flex-start;gap:20px;padding-bottom:24px;margin-bottom:24px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
    .loan-icon{width:64px;height:64px;border-radius:12px;background:rgba(212,175,55,0.1);border:2px solid rgba(212,175,55,0.3);display:flex;align-items:center;justify-content:center;font-size:26px;color:var(--gold);flex-shrink:0;}
    .loan-hero-info h3{font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:5px;}
    .loan-hero-info p{font-size:13.5px;color:var(--text-secondary);margin-bottom:8px;}
    .hero-badges{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
    .loan-status{display:inline-block;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;}
    .ls-active{background:rgba(34,197,94,0.1);color:#16803D;}
    .ls-completed{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .ls-closed{background:rgba(100,116,139,0.1);color:#475569;}
    .ls-cancelled{background:rgba(239,68,68,0.1);color:#DC2626;}
    .section-title{font-size:12px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;margin-top:22px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .detail-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
    @media(max-width:768px){.detail-grid-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.detail-grid,.detail-grid-3{grid-template-columns:1fr;}}
    .detail-item{padding:14px 16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);background:#FFF;box-shadow:0 4px 12px rgba(15,31,53,0.04);}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:7px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary);word-break:break-word;}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .amount-big{font-size:22px;font-weight:800;color:var(--gold);}
    .amt-paid{font-size:16px;font-weight:700;color:#16803D;}
    .amt-pending{font-size:16px;font-weight:700;color:#DC2626;}
    .progress-wrap{width:100%;background:#F1F5F9;border-radius:6px;height:8px;overflow:hidden;margin-bottom:8px;}
    .progress-bar{height:100%;border-radius:6px;background:var(--gold);}
    .meta-info{margin-top:22px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:28px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
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
                    <div class="detail-value">{{ $loan->customer->name }}<div style="font-size:12px;color:var(--text-secondary);margin-top:3px;">{{ $loan->customer->mobile }}</div></div>
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
                <div class="detail-value" style="color:#B91C1C;font-size:16px;font-weight:700;">₹{{ number_format($loan->emi_amount,2)}} / month</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-calendar-days"></i> Total EMI Months</div>
                <div class="detail-value">{{ $loan->total_emi_months }} months</div>
            </div>
            <div class="detail-item">
                <div class="detail-label"><i class="fa-regular fa-calendar"></i> Loan Duration</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($loan->loan_start_date)->format('d M Y') }} <span style="color:var(--text-secondary);font-weight:400;">to</span> {{ \Carbon\Carbon::parse($loan->loan_end_date)->format('d M Y') }}</div>
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
            <div><span class="amt-paid">₹{{ number_format($loan->paid_amount,2) }}</span> <span style="color:var(--text-secondary);font-size:13px;">paid</span></div>
            <div style="font-size:13px;font-weight:700;color:var(--text-secondary);">{{ $pct }}%</div>
            <div><span class="amt-pending">₹{{ number_format($loan->pending_amount,2) }}</span> <span style="color:var(--text-secondary);font-size:13px;">pending</span></div>
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
