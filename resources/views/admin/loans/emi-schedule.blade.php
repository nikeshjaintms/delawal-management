@extends('admin.layouts.app')
@section('title','EMI Schedule')
@section('page-title','Loan Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    .loan-info-bar{background:linear-gradient(135deg,rgba(212,175,55,0.06),rgba(212,175,55,0.02));border:1px solid rgba(212,175,55,0.2);border-radius:10px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
    .loan-info-bar .info-item{display:flex;flex-direction:column;gap:3px;}
    .loan-info-bar .info-label{font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;}
    .loan-info-bar .info-value{font-size:15px;font-weight:700;color:var(--text-primary);}
    .loan-info-bar .info-value.gold{color:var(--gold);}
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:13px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background:#F9FAFB;}
    .premium-table tbody tr.overdue{background:rgba(239,68,68,0.02);}
    .emi-status{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
    .es-pending{background:rgba(245,158,11,0.1);color:#B45309;}
    .es-paid{background:rgba(34,197,94,0.1);color:#16803D;}
    .es-partial{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .es-overdue{background:rgba(239,68,68,0.1);color:#DC2626;}
    .btn-pay{background:var(--gold);color:#FFF;padding:6px 12px;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px;transition:var(--transition);}
    .btn-pay:hover{background:#B58D1B;}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .modal{display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(15,31,53,0.5);z-index:9999;justify-content:center;align-items:center;}
    .modal.active{display:flex;}
    .modal-box{background:#FFF;border-radius:12px;padding:28px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);}
    .modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
    .modal-header h3{font-size:18px;font-weight:700;color:var(--text-primary);}
    .modal-close{background:none;border:none;font-size:24px;color:var(--text-secondary);cursor:pointer;padding:0;line-height:1;}
    .modal-close:hover{color:var(--text-primary);}
    .form-group{margin-bottom:18px;}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    textarea.form-control{resize:vertical;min-height:70px;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .modal-actions{display:flex;gap:12px;margin-top:22px;padding-top:18px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);font-family:var(--font-primary);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;}
    .btn-cancel{border:1px solid var(--border-color);background:#FFF;color:var(--text-secondary);padding:10px 20px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:var(--transition);font-family:var(--font-primary);}
    .btn-cancel:hover{background:#F9FAFB;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>EMI Schedule — {{ $loan->bank_name }}</h2>
        <p>Manage month-wise EMI payments and track status.</p>
    </div>
    <a href="{{ route('loans.show', $loan->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to Loan</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

{{-- Loan Summary Bar --}}
<div class="loan-info-bar">
    <div class="info-item">
        <div class="info-label">Loan Amount</div>
        <div class="info-value gold">₹{{ number_format($loan->loan_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">EMI / Month</div>
        <div class="info-value" style="color:#B91C1C;">₹{{ number_format($loan->emi_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Total EMIs</div>
        <div class="info-value">{{ $loan->total_emi_months }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Paid</div>
        <div class="info-value" style="color:#16803D;">₹{{ number_format($loan->paid_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Pending</div>
        <div class="info-value" style="color:#DC2626;">₹{{ number_format($loan->pending_amount,2) }}</div>
    </div>
</div>

<div class="card-box">
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Month-Year</th>
                    <th>EMI Date</th>
                    <th style="text-align:right;">EMI Amount</th>
                    <th style="text-align:right;">Paid</th>
                    <th style="text-align:right;">Pending</th>
                    <th>Payment Date</th>
                    <th>Mode</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loan->emiSchedules as $i => $emi)
                @php
                    $monthName = \Carbon\Carbon::createFromDate($emi->emi_year, $emi->emi_month, 1)->format('M Y');
                    $stClass = 'es-' . strtolower($emi->emi_status);
                    $isOverdue = $emi->emi_status === 'Overdue';
                @endphp
                <tr class="{{ $isOverdue ? 'overdue' : '' }}">
                    <td style="color:var(--text-secondary);">{{ $i+1 }}</td>
                    <td style="font-weight:600;">{{ $monthName }}</td>
                    <td style="font-size:13px;white-space:nowrap;">{{ \Carbon\Carbon::parse($emi->emi_date)->format('d M Y') }}</td>
                    <td style="text-align:right;font-weight:700;">₹{{ number_format($emi->emi_amount,2) }}</td>
                    <td style="text-align:right;color:#16803D;font-weight:700;">₹{{ number_format($emi->paid_amount,2) }}</td>
                    <td style="text-align:right;color:#DC2626;font-weight:700;">₹{{ number_format($emi->pending_amount,2) }}</td>
                    <td style="font-size:13px;">{{ $emi->payment_date ? \Carbon\Carbon::parse($emi->payment_date)->format('d M Y') : '—' }}</td>
                    <td style="font-size:12.5px;">{{ $emi->payment_mode ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="emi-status {{ $stClass }}">{{ $emi->emi_status }}</span>
                    </td>
                    <td>
                        @if(in_array($emi->emi_status, ['Pending','Partial','Overdue']))
                            <button type="button" class="btn-pay" onclick="openPayModal({{ $emi->id }},'{{ $monthName }}',{{ $emi->emi_amount }},{{ $emi->paid_amount }})">
                                <i class="fa-solid fa-wallet"></i> Pay
                            </button>
                        @else
                            <span style="color:#16803D;font-size:12px;font-weight:600;"><i class="fa-solid fa-circle-check"></i> Paid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:30px;color:var(--text-secondary);">No EMI schedule found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Payment Modal --}}
<div class="modal" id="payModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-wallet" style="color:var(--gold);"></i> Pay EMI</h3>
            <button type="button" class="modal-close" onclick="closePayModal()">&times;</button>
        </div>
        <form method="POST" id="payForm" action="">
            @csrf
            <div style="background:#F9FAFB;border:1px solid var(--border-color);border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:13px;color:var(--text-secondary);">
                <div style="font-weight:600;color:var(--text-primary);margin-bottom:5px;">EMI for <span id="modal_month"></span></div>
                <div>EMI Amount: <strong style="color:var(--text-primary);">₹<span id="modal_emi"></span></strong></div>
                <div>Already Paid: <strong style="color:#16803D;">₹<span id="modal_already_paid"></span></strong></div>
            </div>
            <div class="form-group">
                <label class="form-label">Paid Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label class="form-label">Payment Date <span>*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control @error('payment_date') is-invalid @enderror" required>
            </div>
            <div class="form-group">
                <label class="form-label">Payment Mode <span>*</span></label>
                <select name="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror" required>
                    <option value="">— Select Mode —</option>
                    @foreach(['Cash','Bank Transfer','UPI','Cheque','Other'] as $m)
                        <option value="{{ $m }}">{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Submit Payment</button>
                <button type="button" class="btn-cancel" onclick="closePayModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(emiId, month, emiAmt, alreadyPaid) {
    document.getElementById('modal_month').textContent = month;
    document.getElementById('modal_emi').textContent = emiAmt.toFixed(2);
    document.getElementById('modal_already_paid').textContent = alreadyPaid.toFixed(2);
    document.getElementById('paid_amount').value = '';
    document.getElementById('payForm').action = "{{ route('loans.emi-pay', [$loan->id, '__EMI__']) }}".replace('__EMI__', emiId);
    document.getElementById('payModal').classList.add('active');
}
function closePayModal() {
    document.getElementById('payModal').classList.remove('active');
}
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>
@endsection
