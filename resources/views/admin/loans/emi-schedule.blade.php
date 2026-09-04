@extends('admin.layouts.app')
@section('title','EMI Schedule')
@section('page-title','Loan Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    text-decoration: none !important;
    font-size: 13.5px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    cursor: pointer;
}
.btn-outline:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
}

.alert-success {
    background: rgba(34,197,94,0.18) !important;
    border: 1px solid rgba(34,197,94,0.35) !important;
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

/* Summary Cards Bar */
.loan-info-bar {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.loan-info-bar .info-item {
    background: rgba(20, 27, 41, 0.65) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 16px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    transition: all .25s ease;
}
.loan-info-bar .info-item:hover {
    transform: translateY(-2px);
    border-color: rgba(59, 130, 246, 0.40) !important;
}
.loan-info-bar .info-label {
    font-size: 11px;
    font-weight: 800;
    color: #94A3B8 !important;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}
.loan-info-bar .info-value {
    font-size: 17px;
    font-weight: 800;
    color: #FFFFFF !important;
    word-break: break-word;
}
.loan-info-bar .info-value.val-loan { color: #60A5FA !important; }
.loan-info-bar .info-value.val-emi { color: #F87171 !important; }
.loan-info-bar .info-value.val-paid { color: #34D399 !important; }
.loan-info-bar .info-value.val-pending { color: #F87171 !important; }

/* Main Card Table Box */
.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 28px;
}

.table-container {
    width: 100%;
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.10);
}
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px;
    color: #E2E8F0 !important;
    font-weight: 500;
    vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tr:last-child td { border-bottom: none !important; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }
.premium-table tbody tr.overdue { background: rgba(239, 68, 68, 0.06) !important; }

.emi-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    white-space: nowrap !important;
}
.es-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.es-paid    { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.es-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.es-overdue { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.btn-pay {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all .2s ease;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.35);
}
.btn-pay:hover {
    background: #1D4ED8 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.50);
}

/* ── Modal ── */
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
    max-width: 500px;
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
.modal-header h3 {
    font-size: 18px;
    font-weight: 800;
    color: #FFFFFF !important;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}
.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: #94A3B8 !important;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    transition: color .2s ease;
}
.modal-close:hover { color: #FFFFFF !important; }

.modal-info-box {
    background: rgba(59, 130, 246, 0.12) !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 14px !important;
    padding: 14px 16px !important;
    margin-bottom: 20px !important;
    font-size: 13.5px;
    color: #CBD5E1 !important;
}
.modal-info-box .month-title {
    font-weight: 800;
    color: #60A5FA !important;
    font-size: 14px;
    margin-bottom: 6px;
}
.modal-info-box .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 4px;
}

.form-group { margin-bottom: 18px; }
.form-label {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: #CBD5E1 !important;
    margin-bottom: 7px;
}
.form-label span { color: #F87171 !important; }
.form-control {
    width: 100%;
    padding: 10px 14px !important;
    background: rgba(16, 22, 34, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 14px;
    color: #FFFFFF !important;
    outline: none;
    transition: all .2s ease;
    box-sizing: border-box;
}
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
textarea.form-control { resize: vertical; min-height: 75px; }
.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 500; }

.modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.10);
}
.btn-submit {
    background: #2563EB !important;
    color: #FFFFFF !important;
    padding: 10px 22px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all .25s ease;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-submit:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
}
.btn-cancel {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #CBD5E1 !important;
    padding: 10px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s ease;
}
.btn-cancel:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #FFFFFF !important;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>EMI Schedule — {{ $loan->loan_type === 'Personal Loan' ? $loan->person_name : $loan->bank_name }}</h2>
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
        <div class="info-label">Firm</div>
        <div class="info-value">{{ $loan->firm_names }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Loan Amount</div>
        <div class="info-value val-loan">₹{{ number_format($loan->loan_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">EMI / Month</div>
        <div class="info-value val-emi">₹{{ number_format($loan->emi_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Total EMIs</div>
        <div class="info-value">{{ $loan->total_emi_months }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Paid</div>
        <div class="info-value val-paid">₹{{ number_format($loan->paid_amount,2) }}</div>
    </div>
    <div class="info-item">
        <div class="info-label">Pending</div>
        <div class="info-value val-pending">₹{{ number_format($loan->pending_amount,2) }}</div>
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
                    <th style="width:100px;text-align:center;">Action</th>
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
                    <td style="color:#94A3B8;">{{ $i+1 }}</td>
                    <td style="font-weight:700;color:#FFFFFF;">{{ $monthName }}</td>
                    <td style="font-size:13px;white-space:nowrap;">{{ \Carbon\Carbon::parse($emi->emi_date)->format('d M Y') }}</td>
                    <td style="text-align:right;font-weight:700;color:#FFFFFF;">₹{{ number_format($emi->emi_amount,2) }}</td>
                    <td style="text-align:right;color:#34D399;font-weight:700;">₹{{ number_format($emi->paid_amount,2) }}</td>
                    <td style="text-align:right;color:#F87171;font-weight:700;">₹{{ number_format($emi->pending_amount,2) }}</td>
                    <td style="font-size:13px;">{{ !empty($emi->payment_date) ? \Carbon\Carbon::parse($emi->payment_date)->format('d M Y') : '—' }}</td>
                    <td style="font-size:12.5px;">{{ $emi->payment_mode ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span class="emi-status {{ $stClass }}">{{ $emi->emi_status }}</span>
                    </td>
                    <td style="text-align:center;">
                        @if(in_array($emi->emi_status, ['Pending','Partial','Overdue']))
                            <button type="button" class="btn-pay" onclick="openPayModal({{ $emi->id }},'{{ $monthName }}',{{ $emi->emi_amount }},{{ $emi->paid_amount }})">
                                <i class="fa-solid fa-wallet"></i> Pay
                            </button>
                        @else
                            <span style="color:#34D399;font-size:12.5px;font-weight:700;display:inline-flex;align-items:center;gap:5px;"><i class="fa-solid fa-circle-check"></i> Paid</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:30px;color:#94A3B8;">No EMI schedule found.</td>
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
            <h3><i class="fa-solid fa-wallet" style="color:#60A5FA;"></i> Pay EMI</h3>
            <button type="button" class="modal-close" onclick="closePayModal()">&times;</button>
        </div>
        <form method="POST" id="payForm" action="">
            @csrf
            <div class="modal-info-box">
                <div class="month-title">EMI for <span id="modal_month"></span></div>
                <div class="info-row">
                    <span>EMI Amount:</span>
                    <strong style="color:#FFFFFF;font-weight:800;">₹<span id="modal_emi"></span></strong>
                </div>
                <div class="info-row">
                    <span>Already Paid:</span>
                    <strong style="color:#34D399;font-weight:800;">₹<span id="modal_already_paid"></span></strong>
                </div>
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
                    @foreach($paymentModes as $pm)
                        <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Submit Payment</button>
                <button type="button" class="btn-cancel" onclick="closePayModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(emiId, month, emiAmt, alreadyPaid) {
    document.getElementById('modal_month').textContent = month;
    document.getElementById('modal_emi').textContent = Number(emiAmt).toFixed(2);
    document.getElementById('modal_already_paid').textContent = Number(alreadyPaid).toFixed(2);
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
