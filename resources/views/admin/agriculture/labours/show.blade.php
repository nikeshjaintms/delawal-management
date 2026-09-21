@extends('admin.layouts.app')
@section('title', 'Labour Ledger - ' . $labour->name)
@section('page-title', 'Agriculture Management')
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
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.labour-hero {
    display: flex; align-items: flex-start; gap: 22px; padding-bottom: 24px;
    margin-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.labour-icon-box {
    width: 68px; height: 68px; border-radius: 18px; background: rgba(168, 85, 247, 0.18) !important;
    border: 2px solid rgba(168, 85, 247, 0.40) !important; display: flex; align-items: center;
    justify-content: center; font-size: 28px; color: #C084FC !important; flex-shrink: 0;
}
.labour-hero-info h3 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; }
.labour-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 10px; }
.hero-badges-row { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.status-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; }
.st-active { background: rgba(34, 197, 94, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(34, 197, 94, 0.35) !important; }
.st-inact  { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.type-chip {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important;
    padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.10);
}

/* KPI Summary Cards */
.stat-mini-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-mini-card {
    padding: 18px 20px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px;
}
.stat-mini-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.stat-mini-val { font-size: 19px; font-weight: 800; color: #FFFFFF !important; }

.section-title {
    font-size: 12.5px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 24px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; margin-bottom: 24px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px !important;
}
.detail-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); margin-bottom: 24px; }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px; text-transform: uppercase;
    letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
}
.premium-table td {
    padding: 14px 16px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-green {
    background: #10B981 !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #34D399 !important;
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important; cursor: pointer;
}
.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 22px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important; font-size: 13.5px;
    font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px;
    text-decoration: none !important;
}

/* Modal */
.modal {
    display: none; position: fixed; inset: 0; width: 100vw; height: 100vh;
    background: rgba(8, 12, 22, 0.75); backdrop-filter: blur(12px);
    z-index: 9999; justify-content: center; align-items: center; padding: 20px;
}
.modal.active { display: flex; }
.modal-box {
    background: rgba(20, 27, 41, 0.95) !important; backdrop-filter: blur(28px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 20px !important;
    padding: 28px !important; max-width: 520px; width: 100%; color: #FFFFFF !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6) !important;
}
.modal-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;
    padding-bottom: 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF !important; margin: 0; }
.modal-close { background: none; border: none; font-size: 24px; color: #94A3B8; cursor: pointer; }
.form-group-modal { margin-bottom: 16px; }
.form-label-modal { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; }
.form-label-modal span { color: #F87171; }
.form-control-modal {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 10px;
    font-size: 14px; color: #FFFFFF; outline: none; box-sizing: border-box;
}
.form-control-modal:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
select.form-control-modal option { background: #101622; color: #FFFFFF; }
.btn-delete {
    background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important; border-radius: 8px; width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center; font-size: 13px;
    cursor: pointer; transition: all .2s ease;
}
.btn-delete:hover { background: rgba(239, 68, 68, 0.35) !important; color: #FFFFFF !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Labour Ledger: {{ $labour->name }}</h2>
        <p>Complete wage records, daily wage logs, advances, and payment history.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <button type="button" class="btn-green" onclick="openPaymentModal()"><i class="fa-solid fa-money-bill-wave"></i> Record Payment / Advance</button>
        <a href="{{ route('agriculture.labours.edit', $labour->id) }}" class="btn-gold"><i class="fa fa-edit"></i> Edit Profile</a>
        <a href="{{ route('agriculture.labours.index') }}" class="btn-outline"><i class="fa fa-arrow-left"></i> Back to Labours</a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    {{-- Hero Profile --}}
    <div class="labour-hero">
        <div class="labour-icon-box"><i class="fa-solid fa-user-gear"></i></div>
        <div class="labour-hero-info">
            <h3>{{ $labour->name }}</h3>
            <p>
                <i class="fa-solid fa-phone" style="color:#60A5FA;"></i> {{ $labour->mobile_number ?: 'No mobile' }}
                &nbsp;·&nbsp; Farm: <strong style="color:#FFFFFF;">{{ $labour->farm?->farm_name ?? 'General' }}</strong>
                &nbsp;·&nbsp; Firm: <strong style="color:#FFFFFF;">{{ $labour->firm_names }}</strong>
            </p>
            <div class="hero-badges-row">
                <span class="status-badge {{ $labour->status === 'Active' ? 'st-active' : 'st-inact' }}">{{ $labour->status }}</span>
                <span class="type-chip">{{ $labour->labour_type }}</span>
                <span style="color:#FFFFFF;font-weight:700;">
                    @if($labour->labour_type === 'Normal Labour')
                        ₹{{ number_format($labour->daily_wage, 2) }} / day
                    @else
                        ₹{{ number_format($labour->fixed_salary, 2) }} / mo
                    @endif
                </span>
                <span style="color:#CBD5E1;font-size:12.5px;">Joined: {{ $labour->joining_date ? \Carbon\Carbon::parse($labour->joining_date)->format('d M Y') : '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Financial KPI Cards for this Labour --}}
    <div class="stat-mini-grid">
        <div class="stat-mini-card">
            <div class="stat-mini-label">Total Earned Wages</div>
            <div class="stat-mini-val" style="color:#60A5FA;">₹{{ number_format($labour->total_earned, 2) }}</div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Calculated earned salary/wages</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Total Advance Given</div>
            <div class="stat-mini-val" style="color:#FBBF24;">₹{{ number_format($labour->total_advance, 2) }}</div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Total cash advance disbursed</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Outstanding Advance Balance</div>
            <div class="stat-mini-val" style="color:#FBBF24;">₹{{ number_format($labour->advance_balance, 2) }}</div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Remaining advance to be adjusted</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Total Net Paid</div>
            <div class="stat-mini-val" style="color:#34D399;">₹{{ number_format($labour->total_paid, 2) }}</div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Actual money paid out</div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-label">Pending Wage Payment</div>
            <div class="stat-mini-val" style="color: {{ $labour->pending_amount > 0 ? '#F87171' : '#34D399' }};">
                ₹{{ number_format($labour->pending_amount, 2) }}
            </div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Unpaid balance</div>
        </div>
    </div>

    {{-- Details Grid --}}
    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Labour & Work Information</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label">Labour Type</div>
            <div class="detail-value">{{ $labour->labour_type }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Assigned Farm</div>
            <div class="detail-value">{{ $labour->farm?->farm_name ?? 'General Farm Labour' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Field / Crop Activity</div>
            <div class="detail-value">{{ $labour->field_crop ?: 'General' }}</div>
        </div>
    </div>

    @if($labour->notes)
        <div class="detail-item" style="margin-bottom:24px;">
            <div class="detail-label">Notes & Remarks</div>
            <div class="detail-value" style="font-weight:400;line-height:1.6;">{{ $labour->notes }}</div>
        </div>
    @endif

    {{-- Payment & Wage History Table --}}
    <div class="section-title" style="display:flex;justify-content:space-between;align-items:center;">
        <span><i class="fa-solid fa-clock-rotate-left"></i> Payment & Wage History ({{ $labour->payments->count() }})</span>
        <button type="button" class="btn-green" style="padding:6px 14px;font-size:12px;border-radius:8px;" onclick="openPaymentModal()">
            <i class="fa-solid fa-plus"></i> Record Payment
        </button>
    </div>

    @if($labour->payments->count() > 0)
        <div class="table-container">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Payment Date</th>
                        <th>Type</th>
                        <th>Days / Rate</th>
                        <th style="text-align:right;">Gross Wages</th>
                        <th style="text-align:right;">Advance Deducted</th>
                        <th style="text-align:right;">Net Amount Paid</th>
                        <th>Payment Mode</th>
                        <th>Ref / Notes</th>
                        <th style="text-align:center;width:70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labour->payments->sortByDesc('payment_date') as $idx => $pmt)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td style="color:#FFFFFF;font-weight:600;">{{ \Carbon\Carbon::parse($pmt->payment_date)->format('d M Y') }}</td>
                        <td>
                            <span class="type-chip" style="font-size:11.5px;">{{ $pmt->payment_type }}</span>
                        </td>
                        <td>
                            @if($pmt->payment_type === 'Daily Wage' && (float)$pmt->working_days > 0)
                                {{ $pmt->working_days }} days @ ₹{{ number_format($pmt->daily_wage_rate, 2) }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="text-align:right;color:#60A5FA;font-weight:700;">₹{{ number_format($pmt->gross_amount, 2) }}</td>
                        <td style="text-align:right;color:#FBBF24;font-weight:700;">₹{{ number_format($pmt->advance_deducted, 2) }}</td>
                        <td style="text-align:right;color:#34D399;font-weight:800;font-size:14px;">₹{{ number_format($pmt->amount, 2) }}</td>
                        <td>{{ $pmt->payment_mode ?: 'Cash' }}</td>
                        <td>
                            <div style="font-size:12px;color:#CBD5E1;">{{ $pmt->reference_no ? 'Ref: ' . $pmt->reference_no : '' }}</div>
                            <div style="font-size:11px;color:#94A3B8;">{{ $pmt->notes }}</div>
                        </td>
                        <td style="text-align:center;">
                            <form action="{{ route('agriculture.labour-payments.destroy', $pmt->id) }}" method="POST" id="del-pmt-{{ $pmt->id }}" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" title="Delete Payment" onclick="confirmDeletePmt({{ $pmt->id }}, '{{ number_format($pmt->amount, 2) }}')">
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
        <div class="detail-item" style="text-align:center;padding:28px 20px;color:#94A3B8;">
            <i class="fa-solid fa-receipt" style="font-size:28px;opacity:0.35;display:block;margin-bottom:8px;"></i>
            No payment or wage transactions logged yet. Click <strong>"Record Payment / Advance"</strong> to add an entry.
        </div>
    @endif
</div>

{{-- Quick Payment Modal --}}
<div class="modal" id="labourPayModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-money-bill-wave" style="color:#34D399;"></i> Record Labour Payment / Advance</h3>
            <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('agriculture.labour-payments.store') }}">
            @csrf
            <input type="hidden" name="labour_id" value="{{ $labour->id }}">
            <input type="hidden" name="farm_id" value="{{ $labour->farm_id }}">

            {{-- Summary Box --}}
            <div style="background:rgba(59,130,246,0.12);border:1px solid rgba(59,130,246,0.3);border-radius:12px;padding:12px 16px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#CBD5E1;margin-bottom:3px;">
                    <span>Worker:</span> <strong style="color:#FFFFFF;">{{ $labour->name }} ({{ $labour->labour_type }})</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#CBD5E1;margin-bottom:3px;">
                    <span>Advance Balance:</span> <strong style="color:#FBBF24;">₹{{ number_format($labour->advance_balance, 2) }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:13px;color:#CBD5E1;">
                    <span>Pending Wage:</span> <strong style="color:#F87171;">₹{{ number_format($labour->pending_amount, 2) }}</strong>
                </div>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Type <span>*</span></label>
                <select name="payment_type" id="modal_payment_type" class="form-control-modal" onchange="onModalTypeChange(this.value)" required>
                    <option value="Daily Wage" {{ $labour->labour_type === 'Normal Labour' ? 'selected' : '' }}>Daily Wage (Normal)</option>
                    <option value="Salary" {{ $labour->labour_type === 'Fixed Labour' ? 'selected' : '' }}>Fixed Monthly Salary</option>
                    <option value="Advance Given">Advance Given</option>
                    <option value="Advance Deduction">Advance Deduction / Adjustment</option>
                    <option value="Bonus">Bonus / Extra Incentive</option>
                </select>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Payment Date <span>*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control-modal" required>
            </div>

            {{-- Dynamic Daily Wage calculation fields --}}
            <div id="modal_calc_row" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label class="form-label-modal">Working Days</label>
                    <input type="number" step="0.5" name="working_days" id="modal_working_days" class="form-control-modal" placeholder="e.g. 7" oninput="calcModalPay()">
                </div>
                <div>
                    <label class="form-label-modal">Daily Wage Rate (₹)</label>
                    <input type="number" step="0.01" name="daily_wage_rate" id="modal_daily_rate" value="{{ $labour->daily_wage }}" class="form-control-modal" oninput="calcModalPay()">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label class="form-label-modal">Gross Amount (₹)</label>
                    <input type="number" step="0.01" name="gross_amount" id="modal_gross_amount" class="form-control-modal" placeholder="0.00" oninput="calcModalNet()">
                </div>
                <div>
                    <label class="form-label-modal">Deduct From Advance (₹)</label>
                    <input type="number" step="0.01" name="advance_deducted" id="modal_advance_deduct" max="{{ $labour->advance_balance }}" value="0.00" class="form-control-modal" oninput="calcModalNet()">
                </div>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Net Paid Amount (₹) <span>*</span></label>
                <input type="number" step="0.01" name="amount" id="modal_net_amount" class="form-control-modal" placeholder="0.00" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label class="form-label-modal">Payment Mode</label>
                    <select name="payment_mode_id" class="form-control-modal">
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label-modal">Payment Status</label>
                    <select name="payment_status" class="form-control-modal">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>

            <div class="form-group-modal">
                <label class="form-label-modal">Ref No / Notes</label>
                <input type="text" name="notes" class="form-control-modal" placeholder="e.g. Week 3 payment, cash in hand">
            </div>

            <div style="display:flex;gap:12px;margin-top:20px;padding-top:14px;border-top:1px solid rgba(255,255,255,0.1);">
                <button type="submit" class="btn-green" style="flex:1;justify-content:center;"><i class="fa-solid fa-check"></i> Save Transaction</button>
                <button type="button" class="btn-outline" onclick="closePaymentModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openPaymentModal() {
    document.getElementById('labourPayModal').classList.add('active');
    onModalTypeChange(document.getElementById('modal_payment_type').value);
}
function closePaymentModal() {
    document.getElementById('labourPayModal').classList.remove('active');
}
document.getElementById('labourPayModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});

function onModalTypeChange(type) {
    const calcRow = document.getElementById('modal_calc_row');
    if (type === 'Daily Wage') {
        calcRow.style.display = 'grid';
        calcModalPay();
    } else if (type === 'Salary') {
        calcRow.style.display = 'none';
        document.getElementById('modal_gross_amount').value = "{{ $labour->fixed_salary }}";
        calcModalNet();
    } else if (type === 'Advance Given') {
        calcRow.style.display = 'none';
        document.getElementById('modal_gross_amount').value = 0;
        document.getElementById('modal_advance_deduct').value = 0;
    } else {
        calcRow.style.display = 'none';
    }
}

function calcModalPay() {
    const days = parseFloat(document.getElementById('modal_working_days').value) || 0;
    const rate = parseFloat(document.getElementById('modal_daily_rate').value) || 0;
    const gross = (days * rate).toFixed(2);
    document.getElementById('modal_gross_amount').value = gross;
    calcModalNet();
}

function calcModalNet() {
    const gross = parseFloat(document.getElementById('modal_gross_amount').value) || 0;
    const deduct = parseFloat(document.getElementById('modal_advance_deduct').value) || 0;
    const net = Math.max(0, gross - deduct).toFixed(2);
    document.getElementById('modal_net_amount').value = net;
}

function confirmDeletePmt(id, amount) {
    Swal.fire({
        title: 'Delete Payment?',
        html: 'Delete payment record of <strong>₹' + amount + '</strong>?<br><small style="color:#64748B;">Labour balances and auto-linked expense entries will be recalculated.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-loan-popup' }
    }).then(r => {
        if (r.isConfirmed) {
            document.getElementById('del-pmt-' + id).submit();
        }
    });
}
</script>
<style>.swal-loan-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
