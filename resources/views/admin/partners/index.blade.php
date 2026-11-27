@extends('admin.layouts.app')
@section('title', 'Partners & Profit Sharing')
@section('page-title', 'Partners & Profit Sharing')

@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 600 !important; margin: 0; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

.btn-primary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; min-height: 42px;
    background: #2563EB !important; color: #FFFFFF !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid #3B82F6 !important; border-radius: 10px; text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(37,99,235,0.35); transition: all .25s ease;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); }

.btn-secondary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; min-height: 42px;
    background: rgba(255,255,255,0.08) !important; color: #E2E8F0 !important; font-size: 13.5px; font-weight: 700;
    border: 1px solid rgba(255,255,255,0.16) !important; border-radius: 10px; text-decoration: none !important;
}
.btn-secondary-custom:hover { background: rgba(255,255,255,0.14) !important; color: #FFFFFF !important; }

.kpi-section { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
@media(max-width: 1024px) { .kpi-section { grid-template-columns: repeat(2, 1fr); } }
@media(max-width: 540px) { .kpi-section { grid-template-columns: 1fr; } }

.kpi-card {
    background: rgba(20, 27, 41, 0.65) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.12) !important; border-radius: 18px !important;
    padding: 18px 20px !important; display: flex; justify-content: space-between; align-items: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}
.kpi-info h4 { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
.kpi-info p { font-size: 21px; font-weight: 800; color: #FFFFFF !important; margin: 0; }
.kpi-icon-box { width: 46px; height: 46px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0; }
.bg-blue   { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.bg-amber  { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.bg-green  { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.bg-red    { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.12) !important; border-radius: 20px !important;
    padding: 24px !important; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
}
.filter-bar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;
    background: rgba(255,255,255,0.04); padding: 14px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.10);
}
.search-input, .filter-select {
    padding: 9px 13px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255,255,255,0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.filter-select option { background: #111827 !important; color: #FFFFFF !important; }
.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 9px 16px; border-radius: 10px;
    border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700; cursor: pointer;
}
.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 700; padding: 9px 10px; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border-radius: 18px; border: 1px solid rgba(255,255,255,0.10); }
.premium-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.premium-table th { padding: 12px 14px; background: rgba(255,255,255,0.05) !important; color: #94A3B8 !important; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255,255,255,0.10) !important; white-space: nowrap; }
.premium-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.06) !important; color: #FFFFFF !important; font-weight: 600; vertical-align: middle; }
.premium-table tbody tr:hover { background: rgba(255,255,255,0.04) !important; }

.badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 20px; text-transform: uppercase; white-space: nowrap; }
.badge-silent { background: rgba(139, 92, 246, 0.18); color: #C4B5FD; border: 1px solid rgba(139, 92, 246, 0.35); }
.badge-paper  { background: rgba(59, 130, 246, 0.18); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.35); }
.badge-active   { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.badge-inactive { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }
.share-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(245,158,11,0.14); color: #FCD34D; border: 1px solid rgba(245,158,11,0.30); border-radius: 8px; padding: 3px 8px; font-size: 11.5px; margin: 2px 3px 2px 0; }

.table-action-buttons { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
.table-action-buttons form { display: inline-flex; margin: 0; }
.btn-act { display: inline-flex; align-items: center; gap: 5px; padding: 6px 11px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none !important; cursor: pointer; border: 1px solid transparent; }
.btn-view { background: rgba(59,130,246,0.16); color: #60A5FA; border-color: rgba(59,130,246,0.35); }
.btn-edit { background: rgba(245,158,11,0.16); color: #FBBF24; border-color: rgba(245,158,11,0.35); }
.btn-del  { background: rgba(239,68,68,0.16); color: #F87171; border-color: rgba(239,68,68,0.35); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Partners &amp; Profit Sharing (Admin Only)</h2>
        <p>Manage percentage-based profit shares (including silent / off-paper partners) across Firms, Projects &amp; Properties.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('partner-payouts.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-money-bill-transfer"></i> All Profit Payouts
        </a>
        <a href="{{ route('partners.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i> Add Partner
        </a>
    </div>
</div>

<div class="kpi-section">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Partners ({{ $silentPartners }} Silent)</h4>
            <p>{{ $totalPartners }}</p>
        </div>
        <div class="kpi-icon-box bg-blue"><i class="fa-solid fa-handshake"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Realized Profit Share</h4>
            <p>{{ formatIndianCurrency($totalEntitled) }}</p>
        </div>
        <div class="kpi-icon-box bg-amber"><i class="fa-solid fa-chart-line"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Paid Out</h4>
            <p style="color:#34D399 !important;">{{ formatIndianCurrency($totalPaidOut) }}</p>
        </div>
        <div class="kpi-icon-box bg-green"><i class="fa-solid fa-circle-check"></i></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Balance Payable</h4>
            <p style="color:#F87171 !important;">{{ formatIndianCurrency($totalBalanceDue) }}</p>
        </div>
        <div class="kpi-icon-box bg-red"><i class="fa-solid fa-wallet"></i></div>
    </div>
</div>

<div class="card-box">
    <form method="GET" action="{{ route('partners.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search partner name, mobile, notes..." class="search-input" style="min-width: 240px;">
        <select name="firm_id" class="filter-select">
            <option value="">All Firms</option>
            @foreach($firms as $firm)
                <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
            @endforeach
        </select>
        <select name="partner_type" class="filter-select">
            <option value="">All Partner Types</option>
            <option value="silent" {{ request('partner_type') === 'silent' ? 'selected' : '' }}>Silent (Off-Paper)</option>
            <option value="on_paper" {{ request('partner_type') === 'on_paper' ? 'selected' : '' }}>On-Paper Partner</option>
        </select>
        <select name="status" class="filter-select">
            <option value="">All Status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search', 'firm_id', 'partner_type', 'status']))
            <a href="{{ route('partners.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Partner</th>
                    <th>Type</th>
                    <th>Assigned Percentage Shares</th>
                    <th>Realized Entitled</th>
                    <th>Paid So Far</th>
                    <th>Payable Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partners as $partner)
                    @php
                        $sum = $partnerSummaries[$partner->id] ?? ['realized_entitled' => 0, 'booked_entitled' => 0, 'paid_amount' => 0, 'balance_due' => 0, 'overdrawn_amount' => 0];
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight: 800; color: #FFFFFF;">{{ $partner->name }}</div>
                            @if($partner->mobile)
                                <div style="font-size: 11.5px; color: #94A3B8;">{{ $partner->mobile }}</div>
                            @endif
                        </td>
                        <td>
                            @if($partner->is_silent)
                                <span class="badge badge-silent"><i class="fa-solid fa-user-secret"></i> Silent</span>
                            @else
                                <span class="badge badge-paper"><i class="fa-solid fa-file-signature"></i> On-Paper</span>
                            @endif
                        </td>
                        <td>
                            @forelse($partner->shares as $sh)
                                <span class="share-pill">
                                    <strong>{{ number_format((float)$sh->share_percentage, 2) }}%</strong>
                                    <span>{{ $sh->scope_label }}: {{ $sh->target_name }}</span>
                                </span>
                            @empty
                                <span style="color: #64748B; font-size: 12px;">No shares assigned yet</span>
                            @endforelse
                        </td>
                        <td>
                            <div style="font-weight: 800; color: #FBBF24;">{{ formatIndianCurrency($sum['realized_entitled']) }}</div>
                            <div style="font-size: 11px; color: #94A3B8;">Booked: {{ formatIndianCurrency($sum['booked_entitled']) }}</div>
                        </td>
                        <td style="color: #34D399; font-weight: 800;">{{ formatIndianCurrency($sum['paid_amount']) }}</td>
                        <td>
                            @if($sum['overdrawn_amount'] > 0)
                                <span style="color: #FBBF24; font-weight: 800;">Overdrawn {{ formatIndianCurrency($sum['overdrawn_amount']) }}</span>
                            @else
                                <span style="color: {{ $sum['balance_due'] > 0 ? '#F87171' : '#34D399' }}; font-weight: 800;">
                                    {{ formatIndianCurrency($sum['balance_due']) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $partner->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ ucfirst($partner->status) }}
                            </span>
                        </td>
                        <td class="actions action-column">
                            <div class="table-action-buttons">
                                <a href="{{ route('partners.show', $partner->id) }}" class="btn-act btn-view" title="Manage Shares & Pay Profit">
                                    <i class="fa-solid fa-eye"></i> Manage / Pay
                                </a>
                                <a href="{{ route('partners.edit', $partner->id) }}" class="btn-act btn-edit" title="Edit Partner">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('partners.destroy', $partner->id) }}" onsubmit="return confirm('Delete partner {{ addslashes($partner->name) }} and all their share/payout history?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-act btn-del" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 36px; color: #94A3B8;">
                            No partners added yet. Click <strong>Add Partner</strong> to configure Firm, Project, or Property profit sharing.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $partners->links() }}
    </div>
</div>
@endsection
