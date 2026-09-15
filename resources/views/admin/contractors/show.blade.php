@extends('admin.layouts.app')
@section('title', 'View Contractor - ' . $contractor->contractor_name)
@section('page-title', 'Contractor Details')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    max-width: 1000px;
    margin: 0 auto 28px;
}

.hero-box {
    display: flex; align-items: center; justify-content: space-between; gap: 20px;
    padding-bottom: 24px; margin-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    flex-wrap: wrap;
}
.hero-left { display: flex; align-items: center; gap: 18px; }
.hero-icon {
    width: 64px; height: 64px; border-radius: 18px;
    background: rgba(59, 130, 246, 0.18); border: 1.5px solid rgba(59, 130, 246, 0.35);
    display: flex; align-items: center; justify-content: center; font-size: 28px; color: #60A5FA;
    flex-shrink: 0;
}
.hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; letter-spacing: -0.3px; }
.hero-info p { font-size: 14px; color: #94A3B8 !important; font-weight: 600; margin: 0; }

/* Financial KPI Banner */
.finance-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}
@media(max-width: 900px) {
    .finance-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media(max-width: 480px) {
    .finance-kpi-grid { grid-template-columns: 1fr; }
}

.kpi-card {
    background: rgba(15, 23, 42, 0.65);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 16px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}
.kpi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.kpi-total::before { background: #3B82F6; }
.kpi-paid::before  { background: #10B981; }
.kpi-due::before   { background: #EF4444; }
.kpi-status::before{ background: #F59E0B; }

.kpi-title { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #94A3B8; letter-spacing: 0.5px; }
.kpi-value { font-size: 20px; font-weight: 800; color: #FFFFFF; margin-top: 6px; }

.section-title {
    font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin: 26px 0 16px; padding-bottom: 8px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; justify-content: space-between; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:768px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-card {
    padding: 16px 18px; background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.10); border-radius: 14px;
    transition: all .2s ease;
}
.detail-card:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(96, 165, 250, 0.30); }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important;
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
    display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA; font-size: 12px; }
.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B; font-weight: 500; font-style: italic; }

.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; font-size: 12px; font-weight: 800; border-radius: 20px; text-transform: uppercase; letter-spacing: .4px; }
.badge-active   { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-paid     { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-partial  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-unpaid   { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.btn-pc {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 40px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 12px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-pc:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-green {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 8px 18px; min-height: 38px; background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #FFFFFF !important; font-size: 13px; font-weight: 700; border: 1px solid #34D399 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(16,185,129,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-green:hover { background: #059669 !important; color: #FFFFFF !important; transform: translateY(-2px); }

.btn-sc {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 40px; background: rgba(255, 255, 255, 0.08) !important;
    color: #CBD5E1 !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 12px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-sc:hover { background: rgba(255, 255, 255, 0.14) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.table-wrap {
    overflow-x: auto;
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 14px;
    background: rgba(10, 15, 26, 0.65);
    margin-top: 12px;
}
.pay-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.pay-table th { background: rgba(255,255,255,0.06); color: #94A3B8; font-weight: 700; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.10); text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
.pay-table td { padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.06); color: #E2E8F0; vertical-align: middle; }
.pay-table tr:hover td { background: rgba(255,255,255,0.03); }

.form-actions {
    display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}

/* Modal Styles */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.75); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 9999; padding: 20px;
}
.modal-overlay.show { display: flex; }
.modal-card {
    background: #111827; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 20px;
    width: 100%; max-width: 620px; max-height: 90vh; overflow-y: auto; padding: 26px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);
}
.modal-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.10); padding-bottom: 12px; }
.modal-head h3 { font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; }
.modal-close { background: none; border: none; font-size: 20px; color: #94A3B8; cursor: pointer; transition: all .2s; }
.modal-close:hover { color: #FFFFFF; }
.form-grid-modal { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width: 550px) { .form-grid-modal { grid-template-columns: 1fr; } }
.modal-card .form-label { font-size: 12px; font-weight: 700; color: #CBD5E1; margin-bottom: 5px; display: block; }
.modal-card .form-control {
    width: 100%; padding: 9px 12px; background: rgba(20,27,41,0.85); border: 1.5px solid rgba(255,255,255,0.15);
    border-radius: 10px; color: #FFFFFF; font-size: 13.5px;
}
.modal-card .form-control:focus { border-color: #3B82F6; outline: none; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Contractor Details</h2>
        <p>Contractor profile, financial status, and installment payment history.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" class="btn-green" onclick="openPaymentModal()">
            <i class="fa-solid fa-plus-circle"></i> Add Payment
        </button>
        <a href="{{ route('contractors.index') }}" class="btn-sc"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>
</div>

@if(session('success'))
<div style="background: rgba(16, 185, 129, 0.16); border: 1px solid rgba(16, 185, 129, 0.38); color: #34D399; border-radius: 12px; padding: 14px 18px; margin-bottom: 22px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="card-box">
    @php
        $assignedProjs = $contractor->relationLoaded('projects') && $contractor->projects->isNotEmpty()
            ? $contractor->projects
            : ($contractor->project ? collect([$contractor->project]) : collect());
    @endphp
    <div class="hero-box">
        <div class="hero-left">
            <div class="hero-icon"><i class="fa-solid fa-helmet-safety"></i></div>
            <div class="hero-info">
                <h3>{{ $contractor->contractor_name }}</h3>
                <p>
                    <i class="fa-solid fa-city" style="color: #60A5FA;"></i>
                    {{ $assignedProjs->pluck('project_name')->implode(', ') ?: 'No Project Assigned' }}
                    @if($contractor->work_type)
                        · <span style="color: #FBBF24; font-weight: 700;">{{ $contractor->work_type }}</span>
                    @endif
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <span class="badge badge-{{ $contractor->status }}">
                <i class="fa-solid fa-circle" style="font-size: 7px;"></i> {{ ucfirst($contractor->status) }}
            </span>
            <span class="badge badge-{{ $contractor->payment_status ?? 'unpaid' }}">
                <i class="fa-solid fa-money-bill-wave"></i> Payment: {{ ucfirst($contractor->payment_status ?? 'unpaid') }}
            </span>
        </div>
    </div>

    {{-- Financial KPI Cards --}}
    <div class="finance-kpi-grid">
        <div class="kpi-card kpi-total">
            <div class="kpi-title"><i class="fa-solid fa-file-invoice-dollar" style="color: #60A5FA;"></i> Total Contract</div>
            <div class="kpi-value">₹{{ number_format($contractor->contract_amount ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card kpi-paid">
            <div class="kpi-title"><i class="fa-solid fa-circle-check" style="color: #34D399;"></i> Paid (Installments)</div>
            <div class="kpi-value" style="color: #34D399;">₹{{ number_format($contractor->paid_amount ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card kpi-due">
            <div class="kpi-title"><i class="fa-solid fa-clock" style="color: #F87171;"></i> Remaining Due</div>
            <div class="kpi-value" style="color: #F87171;">₹{{ number_format($contractor->due_amount ?? 0, 2) }}</div>
        </div>
        <div class="kpi-card kpi-status">
            <div class="kpi-title"><i class="fa-solid fa-layer-group" style="color: #FBBF24;"></i> Total Payments</div>
            <div class="kpi-value" style="color: #FBBF24;">{{ $contractor->payments->count() }} <span style="font-size: 13px; font-weight: 600; color: #94A3B8;">entry(ies)</span></div>
        </div>
    </div>

    {{-- Work & Contract Details --}}
    <div class="section-title">
        <span><i class="fa-solid fa-briefcase"></i> Work & Contract Agreement</span>
        @if($contractor->contract_date)
            <span style="font-size: 12px; color: #94A3B8; text-transform: none; font-weight: 600;">
                <i class="fa-solid fa-calendar-days"></i> Agreement Date: <strong style="color: #FFFFFF;">{{ \Carbon\Carbon::parse($contractor->contract_date)->format('d M, Y') }}</strong>
            </span>
        @endif
    </div>
    <div class="detail-grid">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-hammer"></i> Work Type / Category</div>
            <div class="detail-value {{ $contractor->work_type ? '' : 'empty' }}">{{ $contractor->work_type ?: 'General Construction / Not specified' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-calendar-check"></i> Contract Start / Work Date</div>
            <div class="detail-value {{ $contractor->contract_date ? '' : 'empty' }}">
                {{ $contractor->contract_date ? \Carbon\Carbon::parse($contractor->contract_date)->format('d M, Y') : 'Not specified' }}
            </div>
        </div>
        @if($contractor->contract_notes)
        <div class="detail-card" style="grid-column: 1 / -1;">
            <div class="detail-label"><i class="fa-solid fa-file-lines"></i> Work Scope & Contract Notes</div>
            <div class="detail-value" style="white-space: pre-line; font-size: 13.5px; font-weight: 500; color: #CBD5E1 !important;">{{ $contractor->contract_notes }}</div>
        </div>
        @endif
    </div>

    {{-- Payment History & Installments --}}
    <div class="section-title" style="margin-top: 32px;">
        <span><i class="fa-solid fa-receipt"></i> Payment Installments & History ({{ $contractor->payments->count() }})</span>
        <button type="button" class="btn-green" style="padding: 5px 14px; min-height: 32px; font-size: 12px;" onclick="openPaymentModal()">
            <i class="fa-solid fa-plus"></i> Add Payment Installment
        </button>
    </div>

    <div class="table-wrap">
        <table class="pay-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Type</th>
                    <th>Amount (₹)</th>
                    <th>Payment Mode</th>
                    <th>Ref / Cheque No</th>
                    <th>Bill / Voucher</th>
                    <th>Receipt Doc</th>
                    <th>Recorded By</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contractor->payments as $idx => $payment)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #FFFFFF;">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') : '—' }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 11px; font-weight: 700; background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.35); padding: 2px 7px; border-radius: 4px; color: #93C5FD;">
                                {{ $payment->payment_type ?: 'Part Payment' }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #34D399; font-size: 14.5px;">₹{{ number_format($payment->amount, 2) }}</strong>
                        </td>
                        <td>
                            <span style="color: #E2E8F0;"><i class="fa-solid fa-credit-card" style="font-size: 11px; color: #60A5FA; margin-right: 4px;"></i> {{ $payment->payment_mode }}</span>
                            @if($payment->bank_name)
                                <div style="font-size: 11px; color: #94A3B8;">{{ $payment->bank_name }}</div>
                            @endif
                        </td>
                        <td>{{ $payment->reference_no ?: '—' }}</td>
                        <td>{{ $payment->bill_no ?: '—' }}</td>
                        <td>
                            @if($payment->document_file)
                                <a href="{{ asset('storage/' . $payment->document_file) }}" target="_blank" style="color: #60A5FA; text-decoration: none; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-paperclip"></i> View Doc
                                </a>
                            @else
                                <span style="color: #64748B;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 12px; color: #94A3B8;">{{ $payment->creator->name ?? 'System' }}</span>
                        </td>
                        <td style="text-align: right;">
                            <form method="POST" action="{{ route('contractors.payments.destroy', [$contractor->id, $payment->id]) }}" onsubmit="return confirm('Are you sure you want to delete this payment of ₹{{ number_format($payment->amount, 2) }}?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #F87171; border-radius: 6px; padding: 4px 8px; font-size: 11.5px; cursor: pointer;" title="Delete Payment">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @if($payment->remarks)
                    <tr>
                        <td colspan="10" style="background: rgba(0,0,0,0.25); padding: 6px 16px; font-size: 12px; color: #94A3B8; border-bottom: 1px solid rgba(255,255,255,0.08);">
                            <i class="fa-solid fa-comment-dots" style="color: #60A5FA; margin-right: 4px;"></i> <strong>Note:</strong> {{ $payment->remarks }}
                        </td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 32px; color: #94A3B8;">
                            <div style="font-size: 28px; color: #475569; margin-bottom: 6px;"><i class="fa-solid fa-money-bill-transfer"></i></div>
                            <div style="font-weight: 700; color: #CBD5E1; margin-bottom: 4px;">No Payments Recorded Yet</div>
                            <div style="font-size: 12.5px; margin-bottom: 12px;">Click "+ Add Payment" to record the first installment or advance for this contractor.</div>
                            <button type="button" class="btn-green" onclick="openPaymentModal()">
                                <i class="fa-solid fa-plus-circle"></i> Add First Payment
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Project Section --}}
    <div class="section-title" style="margin-top: 32px;"><i class="fa-solid fa-city"></i> Assigned Project(s) ({{ $assignedProjs->count() }})</div>
    <div class="detail-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
        @forelse($assignedProjs as $proj)
            <div class="detail-card">
                <div class="detail-label"><i class="fa-solid fa-building"></i> Project Name</div>
                <div class="detail-value">
                    <a href="{{ route('projects.show', $proj->id) }}" style="color: #60A5FA; text-decoration: none; font-weight: 800;">
                        {{ $proj->project_name }} <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 11px;"></i>
                    </a>
                </div>
                @if($proj->propertyMaster)
                    <div style="font-size: 12px; color: #94A3B8; margin-top: 6px;">
                        <i class="fa-solid fa-landmark"></i> Property: <strong style="color: #E2E8F0;">{{ $proj->propertyMaster->property_name }}</strong>
                    </div>
                @endif
            </div>
        @empty
            <div class="detail-card" style="grid-column: 1 / -1;">
                <div class="detail-value" style="color: #94A3B8;">No projects currently assigned.</div>
            </div>
        @endforelse
    </div>

    {{-- Specific Assigned Plots / Units Section --}}
    @if($contractor->properties && $contractor->properties->isNotEmpty())
        <div class="section-title" style="color: #34D399;"><i class="fa-solid fa-shapes"></i> Specific Assigned Plot(s) / Unit(s) ({{ $contractor->properties->count() }})</div>
        <div class="detail-grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px;">
            @foreach($contractor->properties as $prop)
                <div class="detail-card" style="border-color: rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.04);">
                    <div class="detail-label" style="color: #34D399;"><i class="fa-solid fa-cube"></i> Unit / Plot</div>
                    <div class="detail-value" style="color: #FFFFFF; font-size: 14px;">
                        {{ $prop->property_name }}
                        @if($prop->property_code)
                            <span style="font-size: 11px; color: #94A3B8;">[{{ $prop->property_code }}]</span>
                        @endif
                    </div>
                    <div style="font-size: 11.5px; color: #94A3B8; margin-top: 4px;">
                        <i class="fa-solid fa-city" style="color: #60A5FA;"></i> {{ $prop->project->project_name ?? '—' }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Identity & Contact Information --}}
    <div class="section-title"><i class="fa-solid fa-id-card"></i> Identity & Contact Details</div>
    <div class="detail-grid-3">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-phone"></i> Mobile Number</div>
            <div class="detail-value {{ $contractor->mobile ? '' : 'empty' }}">{{ $contractor->mobile ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-address-card"></i> Aadhar Card</div>
            <div class="detail-value {{ $contractor->aadhar_no ? '' : 'empty' }}">{{ $contractor->aadhar_no ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-receipt"></i> PAN Card</div>
            <div class="detail-value {{ $contractor->pan_no ? '' : 'empty' }}">{{ $contractor->pan_no ?: 'Not provided' }}</div>
        </div>
    </div>

    {{-- Bank Details --}}
    <div class="section-title"><i class="fa-solid fa-building-columns"></i> Bank Details</div>
    <div class="detail-grid">
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-bank"></i> Bank Name</div>
            <div class="detail-value {{ $contractor->bank_name ? '' : 'empty' }}">{{ $contractor->bank_name ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Account Number</div>
            <div class="detail-value {{ $contractor->account_number ? '' : 'empty' }}" style="letter-spacing: 0.5px;">{{ $contractor->account_number ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> IFSC Code</div>
            <div class="detail-value {{ $contractor->ifsc_code ? '' : 'empty' }}">{{ $contractor->ifsc_code ?: 'Not provided' }}</div>
        </div>
        <div class="detail-card">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Branch / City</div>
            <div class="detail-value {{ $contractor->branch_name ? '' : 'empty' }}">{{ $contractor->branch_name ?: 'Not provided' }}</div>
        </div>
    </div>

    {{-- Address --}}
    @if($contractor->address)
    <div class="section-title"><i class="fa-solid fa-location-arrow"></i> Address</div>
    <div class="detail-card">
        <div class="detail-value">{{ $contractor->address }}</div>
    </div>
    @endif

    <div class="form-actions">
        <button type="button" class="btn-green" onclick="openPaymentModal()">
            <i class="fa-solid fa-plus-circle"></i> Add Payment Installment
        </button>
        <a href="{{ route('contractors.detail-pdf', $contractor->id) }}" target="_blank" class="btn-pc" style="background: rgba(252,105,0,0.18) !important; border-color: rgba(252,105,0,0.45) !important; color: #FF8A3D !important; box-shadow: 0 4px 14px rgba(252,105,0,0.25);">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Dossier
        </a>
        <a href="{{ route('contractors.edit', $contractor) }}" class="btn-pc">
            <i class="fa-solid fa-pen-to-square"></i> Edit Contractor
        </a>
        <a href="{{ route('contractors.index') }}" class="btn-sc">Back to List</a>
    </div>
</div>

{{-- Add Payment Modal --}}
<div id="paymentModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-head">
            <h3><i class="fa-solid fa-money-bill-wave" style="color: #34D399; margin-right: 8px;"></i> Record Contractor Payment</h3>
            <button type="button" class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>

        <form method="POST" action="{{ route('contractors.payments.store', $contractor->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-grid-modal">
                <div class="form-group">
                    <label class="form-label" for="pay_amount">Payment Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="pay_amount" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_date">Payment Date <span>*</span></label>
                    <input type="date" name="payment_date" id="pay_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_mode">Payment Mode <span>*</span></label>
                    <select name="payment_mode" id="pay_mode" class="form-control" required>
                        @if(isset($paymentModes) && $paymentModes->count() > 0)
                            @foreach($paymentModes as $pm)
                                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                            @endforeach
                        @else
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer / NEFT / RTGS</option>
                            <option value="Cheque">Cheque</option>
                            <option value="UPI">UPI / GooglePay / PhonePe</option>
                            <option value="Online">Online</option>
                        @endif
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_type">Payment Type</label>
                    <select name="payment_type" id="pay_type" class="form-control">
                        <option value="Part Payment">Part Payment / Installment</option>
                        <option value="Running Bill">Running Bill (RA Bill)</option>
                        <option value="Advance">Advance Payment</option>
                        <option value="Stage Payment">Stage / Milestone Payment</option>
                        <option value="Final Settlement">Final Settlement</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_ref">Reference / Cheque / UTR No</label>
                    <input type="text" name="reference_no" id="pay_ref" class="form-control" placeholder="e.g. CHQ-100234 or UTR-99882">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_bank">Bank Name (Optional)</label>
                    <input type="text" name="bank_name" id="pay_bank" class="form-control" value="{{ $contractor->bank_name }}" placeholder="e.g. HDFC Bank">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_bill">Bill / Voucher No</label>
                    <input type="text" name="bill_no" id="pay_bill" class="form-control" placeholder="e.g. VOUCHER-001">
                </div>

                <div class="form-group">
                    <label class="form-label" for="pay_project">Related Project (Optional)</label>
                    <select name="project_id" id="pay_project" class="form-control">
                        <option value="">— Select Project —</option>
                        @foreach($assignedProjs as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->project_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" for="pay_doc">Receipt / Voucher Attachment (PDF / Image)</label>
                    <input type="file" name="document_file" id="pay_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label class="form-label" for="pay_remarks">Remarks / Notes</label>
                    <textarea name="remarks" id="pay_remarks" class="form-control" rows="2" placeholder="Notes about this installment..."></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.10); padding-top: 16px;">
                <button type="button" class="btn-sc" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn-green">
                    <i class="fa-solid fa-check"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.add('show');
        const amtInput = document.getElementById('pay_amount');
        if (amtInput) amtInput.focus();
    }
}
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.remove('show');
    }
}
window.addEventListener('click', function(e) {
    const modal = document.getElementById('paymentModal');
    if (e.target === modal) {
        closePaymentModal();
    }
});
</script>
@endsection
