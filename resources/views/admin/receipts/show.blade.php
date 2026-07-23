@extends('admin.layouts.app')
@section('title', 'View Receipt')
@section('page-title', 'Receipt Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:860px;margin:0 auto;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    @media(max-width:576px){.detail-grid{grid-template-columns:1fr;}}
    .detail-item{padding:16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);box-shadow:0 4px 12px rgba(15,31,53,0.04);background:#FFF;}
    .detail-item-full{grid-column:1 / -1;}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:15px;font-weight:600;color:var(--text-primary);word-break:break-word;}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .badge{display:inline-block;padding:4px 12px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase;}
    .badge-active{background:rgba(16,185,129,0.1);color:#059669;}
    .badge-inactive{background:rgba(239,68,68,0.1);color:#DC2626;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:28px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Receipt Details</h2>
        <p>Full details for receipt: <strong>{{ $receipt->receipt_no ?? '' }}</strong></p>
    </div>
</div>

<div class="card-box">
    <div class="detail-grid">

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
            <div class="detail-value">{{ $receipt->firm_names }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Receipt No</div>
            <div class="detail-value">{{ $receipt->receipt_no ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Receipt Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d M Y') }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Received From</div>
            <div class="detail-value">{{ $receipt->received_from ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Amount</div>
            <div class="detail-value">₹{{ number_format($receipt->amount, 2) }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
            <div class="detail-value">{{ $receipt->payment_mode ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Reference No</div>
            <div class="detail-value">{{ $receipt->reference_no ?? '—' }}</div>
        </div>

        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $receipt->status ?? 'active' }}">
                    {{ ucfirst($receipt->status ?? 'active') }}
                </span>
            </div>
        </div>

        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
            @if($receipt->remarks)
                <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.6;">{{ $receipt->remarks }}</div>
            @else
                <div class="detail-value empty">No remarks provided</div>
            @endif
        </div>

    </div>

    <div class="form-actions">
        <a href="{{ route('receipts.edit', $receipt->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Receipt
        </a>
        <a href="{{ route('receipts.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
