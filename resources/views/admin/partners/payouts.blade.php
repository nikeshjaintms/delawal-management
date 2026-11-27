@extends('admin.layouts.app')
@section('title', 'All Partner Profit Payouts')
@section('page-title', 'Partner Profit Payouts')

@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; margin: 0; }
.btn-secondary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; min-height: 42px;
    background: rgba(255,255,255,0.08) !important; color: #FFFFFF !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid rgba(255,255,255,0.16) !important; border-radius: 10px; text-decoration: none !important;
}
.card-box {
    background: rgba(20, 27, 41, 0.60) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12) !important; border-radius: 20px !important;
    padding: 24px !important; box-shadow: 0 16px 40px rgba(0,0,0,0.35) !important;
}
.filter-bar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;
    background: rgba(255,255,255,0.04); padding: 14px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.10);
}
.filter-select, .search-input {
    padding: 9px 13px; background: rgba(16,22,34,0.65) !important; border: 1.5px solid rgba(255,255,255,0.15) !important;
    border-radius: 10px !important; font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.filter-select option { background: #111827 !important; color: #FFFFFF !important; }
.btn-search { background: #2563EB !important; color: #FFFFFF !important; padding: 9px 16px; border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700; cursor: pointer; }
.table-container { width: 100%; overflow-x: auto; background: rgba(16,22,34,0.70); border-radius: 18px; border: 1px solid rgba(255,255,255,0.10); }
.premium-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.premium-table th { padding: 12px 14px; background: rgba(255,255,255,0.05); color: #94A3B8; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255,255,255,0.10); white-space: nowrap; }
.premium-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.06); color: #FFFFFF; font-weight: 600; vertical-align: middle; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Partner Profit Payout Ledger</h2>
        <p>All profit distributions paid across Firms, Projects &amp; Properties (Total Filtered: <strong style="color:#34D399;">{{ formatIndianCurrency($totalPayoutAmount) }}</strong>)</p>
    </div>
    <a href="{{ route('partners.index') }}" class="btn-secondary-custom"><i class="fa-solid fa-handshake"></i> Back to Partners</a>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('partner-payouts.index') }}" class="filter-bar">
        <select name="partner_id" class="filter-select">
            <option value="">All Partners</option>
            @foreach($partners as $p)
                <option value="{{ $p->id }}" {{ request('partner_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}{{ $p->is_silent ? ' (Silent)' : '' }}</option>
            @endforeach
        </select>
        <select name="firm_id" class="filter-select">
            <option value="">All Firms</option>
            @foreach($firms as $f)
                <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
            @endforeach
        </select>
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="search-input">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="search-input">
        <button type="submit" class="btn-search"><i class="fa-solid fa-filter"></i> Apply</button>
        @if(request()->hasAny(['partner_id', 'firm_id', 'from_date', 'to_date']))
            <a href="{{ route('partner-payouts.index') }}" style="color:#CBD5E1; text-decoration:none; font-weight:700;">Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Partner</th>
                    <th>Firm</th>
                    <th>Profit Scope &amp; %</th>
                    <th>Mode / Ref</th>
                    <th>Amount Paid</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                    <tr>
                        <td>{{ $payout->payout_date?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('partners.show', $payout->partner_id) }}" style="color:#60A5FA; text-decoration:none; font-weight:800;">
                                {{ $payout->partner?->name ?? 'Partner' }}
                            </a>
                        </td>
                        <td>{{ $payout->firm?->firm_name ?? '—' }}</td>
                        <td>
                            @if($payout->share)
                                <span>{{ $payout->share->scope_label }}: <strong>{{ $payout->share->target_name }}</strong></span>
                                <span style="color:#FBBF24; font-size:12px;">({{ number_format((float)$payout->share->share_percentage, 2) }}%)</span>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div>{{ $payout->payment_mode ?: ($payout->paymentMode?->name ?? 'Cash') }}</div>
                            @if($payout->reference_no)
                                <div style="font-size:11px; color:#94A3B8;">Ref: {{ $payout->reference_no }}</div>
                            @endif
                        </td>
                        <td style="color:#34D399; font-weight:800;">{{ formatIndianCurrency($payout->amount) }}</td>
                        <td>
                            <a href="{{ route('partners.payouts.receipt-pdf', [$payout->partner_id, $payout->id]) }}" target="_blank" style="color:#60A5FA; text-decoration:none; font-weight:700;">
                                <i class="fa-solid fa-print"></i> Voucher PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:#94A3B8;">No partner payouts recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 18px;">
        {{ $payouts->links() }}
    </div>
</div>
@endsection
