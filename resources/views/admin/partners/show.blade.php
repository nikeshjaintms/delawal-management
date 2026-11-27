@extends('admin.layouts.app')
@section('title', 'Partner Profit & Payouts - ' . $partner->name)
@section('page-title', 'Partner Profit & Payouts')

@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; margin: 0; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

.btn-primary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; min-height: 40px;
    background: #2563EB !important; color: #FFFFFF !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid #3B82F6 !important; border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-green-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; min-height: 40px;
    background: #059669 !important; color: #FFFFFF !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid #10B981 !important; border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-secondary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; min-height: 40px;
    background: rgba(255,255,255,0.08) !important; color: #FFFFFF !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid rgba(255,255,255,0.16) !important; border-radius: 10px; text-decoration: none !important;
}

.kpi-section { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width: 1024px) { .kpi-section { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 540px) { .kpi-section { grid-template-columns: 1fr; } }

.kpi-card {
    background: rgba(20, 27, 41, 0.65) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.12) !important; border-radius: 18px !important;
    padding: 18px 20px !important; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}
.kpi-card h4 { font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 6px; }
.kpi-card .val { font-size: 21px; font-weight: 800; color: #FFFFFF; }
.kpi-card .sub { font-size: 11.5px; color: #94A3B8; margin-top: 4px; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12) !important; border-radius: 20px !important;
    padding: 24px !important; margin-bottom: 24px; box-shadow: 0 16px 40px rgba(0,0,0,0.35) !important;
}
.section-heading {
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 16px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255,255,255,0.10); display: flex; justify-content: space-between; align-items: center; gap: 8px;
}

.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
@media(max-width: 960px) { .two-col { grid-template-columns: 1fr; } }

.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
.form-label { display: block; font-size: 12.5px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 6px; }
.form-control {
    width: 100%; padding: 9px 12px; background: rgba(16, 22, 34, 0.70) !important;
    border: 1.5px solid rgba(255,255,255,0.15) !important; border-radius: 10px !important;
    font-size: 13px; color: #FFFFFF !important; outline: none;
}
.form-control option { background: #111827 !important; color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16,22,34,0.70); border-radius: 16px; border: 1px solid rgba(255,255,255,0.10); }
.premium-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.premium-table th { padding: 11px 14px; background: rgba(255,255,255,0.05); color: #94A3B8; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: .7px; border-bottom: 1.5px solid rgba(255,255,255,0.10); white-space: nowrap; }
.premium-table td { padding: 11px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); color: #FFFFFF; font-weight: 600; vertical-align: middle; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; }
.badge-silent { background: rgba(139, 92, 246, 0.18); color: #C4B5FD; border: 1px solid rgba(139, 92, 246, 0.35); }
.badge-paper  { background: rgba(59, 130, 246, 0.18); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); }
.btn-mini { display: inline-flex; align-items: center; gap: 4px; padding: 5px 10px; border-radius: 7px; font-size: 11.5px; font-weight: 700; cursor: pointer; text-decoration: none !important; border: 1px solid transparent; }
.btn-mini-green { background: rgba(16,185,129,0.18); color: #34D399; border-color: rgba(16,185,129,0.35); }
.btn-mini-blue  { background: rgba(59,130,246,0.18); color: #60A5FA; border-color: rgba(59,130,246,0.35); }
.btn-mini-red   { background: rgba(239,68,68,0.18); color: #F87171; border-color: rgba(239,68,68,0.35); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>
            {{ $partner->name }}
            @if($partner->is_silent)
                <span class="badge badge-silent"><i class="fa-solid fa-user-secret"></i> Silent Partner (Off-Paper)</span>
            @else
                <span class="badge badge-paper"><i class="fa-solid fa-file-signature"></i> On-Paper Partner</span>
            @endif
        </h2>
        <p>
            {{ $partner->firm?->firm_name ? 'Default Firm: ' . $partner->firm->firm_name . ' • ' : '' }}
            {{ $partner->mobile ?: 'No Mobile' }}
            {{ $partner->notes ? ' • ' . $partner->notes : '' }}
        </p>
    </div>
    <div class="header-actions">
        <a href="{{ route('partners.detail-pdf', $partner->id) }}" target="_blank" class="btn-primary-custom">
            <i class="fa-solid fa-file-pdf"></i> Statement PDF
        </a>
        <a href="{{ route('partners.edit', $partner->id) }}" class="btn-secondary-custom">
            <i class="fa-solid fa-pen"></i> Edit Partner
        </a>
        <a href="{{ route('partners.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="kpi-section">
    <div class="kpi-card">
        <h4>{{ $partner->name }} Share ({{ number_format((float)$partner->shares->where('status','active')->sum('share_percentage'), 2) }}%)</h4>
        <div class="val" style="color:#FBBF24;">{{ formatIndianCurrency($totalRealizedEntitled) }}</div>
        <div class="sub">Partner's share of distributable cash ({{ formatIndianCurrency($totalPoolAvailable) }})</div>
    </div>
    <div class="kpi-card">
        <h4>Paid to {{ $partner->name }}</h4>
        <div class="val" style="color:#34D399;">{{ formatIndianCurrency($totalPaid) }}</div>
        <div class="sub">
            @if($totalOverdrawn > 0)
                <span style="color:#FBBF24;">Overdrawn by {{ formatIndianCurrency($totalOverdrawn) }}</span>
            @else
                Partner balance due: <strong style="color:{{ $totalBalanceDue > 0 ? '#F87171' : '#34D399' }};">{{ formatIndianCurrency($totalBalanceDue) }}</strong>
            @endif
        </div>
    </div>
    <div class="kpi-card">
        <h4>Total Paid (All Partners)</h4>
        <div class="val" style="color:#60A5FA;">{{ formatIndianCurrency($totalPoolPaidAll) }}</div>
        <div class="sub">Total distributed across all partners on these targets</div>
    </div>
    <div class="kpi-card">
        <h4>Pool Balance Pending</h4>
        <div class="val" style="color:{{ $totalPoolPending > 0 ? '#F87171' : '#34D399' }};">{{ formatIndianCurrency($totalPoolPending) }}</div>
        <div class="sub">Cash Pool ({{ formatIndianCurrency($totalPoolAvailable) }}) − All Paid ({{ formatIndianCurrency($totalPoolPaidAll) }})</div>
    </div>
</div>

{{-- Percentage Profit Shares Table --}}
<div class="card-box">
    <div class="section-heading">
        <span><i class="fa-solid fa-percent"></i> Assigned Percentage Profit Shares ({{ $partner->shares->count() }})</span>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Scope &amp; Target</th>
                    <th>Share %</th>
                    <th>Distributable Pool</th>
                    <th>This Partner Share</th>
                    <th>This Partner Paid</th>
                    <th>This Partner Due</th>
                    <th>Pool Pending (After All Paid)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partner->shares as $share)
                    @php
                        $m = $shareMetrics[$share->id] ?? [
                            'realized_inflow' => 0, 'realized_outflow' => 0, 'realized_profit' => 0,
                            'land_invested' => 0, 'booked_profit' => 0, 'realized_entitled' => 0,
                            'booked_entitled' => 0, 'paid_amount' => 0, 'balance_due' => 0,
                            'overdrawn_amount' => 0, 'pool_paid_all' => 0, 'pool_pending' => 0,
                        ];
                    @endphp
                    <tr>
                        <td>
                            <div style="font-size:11px; color:#60A5FA; text-transform:uppercase; font-weight:800;">{{ $share->scope_label }}</div>
                            <div style="font-weight:800; color:#FFFFFF;">{{ $share->target_name }}</div>
                            <div style="font-size:11px; color:#94A3B8;">Firm: {{ $share->firm?->firm_name ?? '—' }}</div>
                        </td>
                        <td>
                            <span style="font-size:15px; font-weight:800; color:#FBBF24;">{{ number_format((float)$share->share_percentage, 2) }}%</span>
                        </td>
                        <td>
                            <div style="font-weight:800; color:{{ $m['realized_profit'] >= 0 ? '#34D399' : '#F87171' }};">
                                {{ formatIndianCurrency($m['realized_profit']) }}
                            </div>
                            <div style="font-size:11px; color:#94A3B8;">
                                In: {{ formatIndianCurrency($m['realized_inflow']) }} | Exp: {{ formatIndianCurrency($m['realized_outflow']) }}
                            </div>
                            @if(($m['land_invested'] ?? 0) > 0)
                                <div style="font-size:10.5px; color:#A78BFA;">
                                    Land/Capital Invested: {{ formatIndianCurrency($m['land_invested']) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:800; color:#FBBF24;">{{ formatIndianCurrency($m['realized_entitled']) }}</div>
                            @if(($m['booked_entitled'] ?? 0) > 0)
                                <div style="font-size:11px; color:#94A3B8;">Booked Ref: {{ formatIndianCurrency($m['booked_entitled']) }}</div>
                            @endif
                        </td>
                        <td style="color:#34D399; font-weight:800;">{{ formatIndianCurrency($m['paid_amount']) }}</td>
                        <td>
                            @if($m['overdrawn_amount'] > 0)
                                <span style="color:#FBBF24; font-weight:800;">Overdrawn {{ formatIndianCurrency($m['overdrawn_amount']) }}</span>
                            @else
                                <span style="color:{{ $m['balance_due'] > 0 ? '#F87171' : '#34D399' }}; font-weight:800;">
                                    {{ formatIndianCurrency($m['balance_due']) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:800; color:{{ ($m['pool_pending'] ?? 0) > 0 ? '#FBBF24' : '#34D399' }};">
                                {{ formatIndianCurrency($m['pool_pending'] ?? 0) }}
                            </div>
                            <div style="font-size:11px; color:#94A3B8;">
                                All Paid: {{ formatIndianCurrency($m['pool_paid_all'] ?? 0) }}
                            </div>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:nowrap;">
                                <button type="button" class="btn-mini btn-mini-green"
                                    onclick="prefillPayout({{ $share->id }}, {{ $m['balance_due'] }})">
                                    <i class="fa-solid fa-indian-rupee-sign"></i> Pay Profit
                                </button>
                                <form method="POST" action="{{ route('partners.shares.destroy', [$partner->id, $share->id]) }}"
                                      onsubmit="return confirm('Remove this percentage share and its payout records?');" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-mini btn-mini-red" title="Remove Share">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:26px; color:#94A3B8;">
                            No percentage shares assigned yet. Use the form below to assign a Firm, Project, or Property share.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Two Side-by-Side Action Cards: Assign Percentage Share + Record Profit Payout --}}
<div class="two-col">
    {{-- Add Share Form --}}
    <div class="card-box">
        <div class="section-heading"><span><i class="fa-solid fa-plus-circle"></i> Assign Profit Percentage Share</span></div>
        <form method="POST" action="{{ route('partners.shares.store', $partner->id) }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label class="form-label">Scope Type *</label>
                    <select name="scope_type" id="share_scope_type" class="form-control" onchange="toggleNewShareScope()" required>
                        <option value="firm">Entire Firm</option>
                        <option value="project">Project</option>
                        <option value="property_master">Property / Land Master</option>
                        <option value="property">Individual Plot / Unit</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Share Percentage (%) *</label>
                    <input type="number" step="0.01" min="0.01" max="100" name="share_percentage" class="form-control" placeholder="e.g. 25" required>
                </div>
                <div id="wrap_new_firm">
                    <label class="form-label">Firm *</label>
                    <select name="firm_id" id="share_firm_id" class="form-control" required>
                        @foreach($firms as $firm)
                            <option value="{{ $firm->id }}" {{ $partner->firm_id == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="wrap_new_project" style="display:none;">
                    <label class="form-label">Project *</label>
                    <select name="project_id" class="form-control">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}">{{ $proj->project_name }} ({{ $proj->firm?->firm_name ?? 'Firm' }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="wrap_new_pm" style="display:none;">
                    <label class="form-label">Property Master *</label>
                    <select name="property_master_id" class="form-control">
                        <option value="">-- Select Property Master --</option>
                        @foreach($propertyMasters as $pm)
                            <option value="{{ $pm->id }}">{{ $pm->property_name }} ({{ $pm->firm?->firm_name ?? 'Firm' }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="wrap_new_prop" style="display:none;">
                    <label class="form-label">Individual Plot / Unit *</label>
                    <select name="property_id" class="form-control">
                        <option value="">-- Select Unit --</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->property_name }} {{ $prop->unit_no ? '(Unit #' . $prop->unit_no . ')' : '' }} — {{ $prop->firm?->firm_name ?? 'Firm' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Effective From</label>
                    <input type="date" name="effective_from" class="form-control">
                </div>
                <div style="grid-column: 1 / -1;">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Optional arrangement notes...">
                </div>
            </div>
            <div style="margin-top: 16px;">
                <button type="submit" class="btn-primary-custom"><i class="fa-solid fa-plus"></i> Add Share Rule</button>
            </div>
        </form>
    </div>

    {{-- Record Profit Payout Form --}}
    <div class="card-box" id="payoutFormCard">
        <div class="section-heading"><span><i class="fa-solid fa-money-bill-wave"></i> Pay Partner From Profit</span></div>
        @if($partner->shares->isEmpty())
            <p style="color:#94A3B8; font-size:13px;">Assign at least one Firm, Project, or Property profit percentage share first before recording a payout.</p>
        @else
            <form method="POST" action="{{ route('partners.payouts.store', $partner->id) }}">
                @csrf
                <div class="form-grid">
                    <div style="grid-column: 1 / -1;">
                        <label class="form-label">Profit Share Source *</label>
                        <select name="partner_share_id" id="payout_share_id" class="form-control" required>
                            @foreach($partner->shares as $sh)
                                @php $bal = $shareMetrics[$sh->id]['balance_due'] ?? 0; @endphp
                                <option value="{{ $sh->id }}">
                                    {{ $sh->scope_label }}: {{ $sh->target_name }} ({{ number_format((float)$sh->share_percentage, 2) }}% — Payable: {{ formatIndianCurrency($bal) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Payout Date *</label>
                        <input type="date" name="payout_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div>
                        <label class="form-label">Amount (₹) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="payout_amount" class="form-control" placeholder="0.00" required>
                    </div>
                    <div>
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode_id" class="form-control">
                            <option value="">Cash (Default)</option>
                            @foreach($paymentModes as $pm)
                                <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Reference / UTR / Cheque No</label>
                        <input type="text" name="reference_no" class="form-control" placeholder="Optional reference">
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control" placeholder="e.g., Profit payout against Project sales">
                    </div>
                </div>
                <div style="margin-top: 16px;">
                    <button type="submit" class="btn-green-custom"><i class="fa-solid fa-check"></i> Record Profit Payout</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Payout History --}}
<div class="card-box">
    <div class="section-heading"><span><i class="fa-solid fa-receipt"></i> Payout History ({{ $partner->payouts->count() }})</span></div>
    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Scope &amp; Target</th>
                    <th>Payment Mode</th>
                    <th>Reference</th>
                    <th>Remarks</th>
                    <th>Amount Paid</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partner->payouts as $payout)
                    <tr>
                        <td>{{ $payout->payout_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            @if($payout->share)
                                <span>{{ $payout->share->scope_label }}: <strong>{{ $payout->share->target_name }}</strong></span>
                                <span style="color:#FBBF24; font-size:11.5px;">({{ number_format((float)$payout->share->share_percentage, 2) }}%)</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $payout->payment_mode ?: ($payout->paymentMode?->name ?? 'Cash') }}</td>
                        <td>{{ $payout->reference_no ?: '—' }}</td>
                        <td>{{ $payout->remarks ?: '—' }}</td>
                        <td style="color:#34D399; font-weight:800;">{{ formatIndianCurrency($payout->amount) }}</td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('partners.payouts.receipt-pdf', [$partner->id, $payout->id]) }}" target="_blank" class="btn-mini btn-mini-blue">
                                    <i class="fa-solid fa-print"></i> Voucher PDF
                                </a>
                                <form method="POST" action="{{ route('partners.payouts.destroy', [$partner->id, $payout->id]) }}"
                                      onsubmit="return confirm('Delete this payout entry?');" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-mini btn-mini-red"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:26px; color:#94A3B8;">No profit payouts recorded for this partner yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleNewShareScope() {
    const scope = document.getElementById('share_scope_type').value;
    document.getElementById('wrap_new_project').style.display = scope === 'project' ? 'block' : 'none';
    document.getElementById('wrap_new_pm').style.display      = scope === 'property_master' ? 'block' : 'none';
    document.getElementById('wrap_new_prop').style.display    = scope === 'property' ? 'block' : 'none';
}
function prefillPayout(shareId, suggestedAmt) {
    const select = document.getElementById('payout_share_id');
    const amtInput = document.getElementById('payout_amount');
    if (select) select.value = shareId;
    if (amtInput && suggestedAmt > 0) amtInput.value = suggestedAmt.toFixed(2);
    document.getElementById('payoutFormCard')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
document.addEventListener('DOMContentLoaded', toggleNewShareScope);
</script>
@endsection
