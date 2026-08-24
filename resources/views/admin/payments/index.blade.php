@extends('admin.layouts.app')

@section('title', 'Payments')
@section('page-title', 'Payment Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: center !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.search-form { display: flex !important; gap: 12px !important; flex: 1 !important; width: 100% !important; align-items: center !important; flex-wrap: nowrap !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; flex: 1 !important;
}
select.search-input option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.pay-id { font-size: 12px; font-weight: 700; color: #60A5FA !important; background: rgba(59, 130, 246, 0.15) !important; border: 1px solid rgba(59, 130, 246, 0.30) !important; padding: 4px 10px; border-radius: 6px; display: inline-block; }
.amount-col { font-weight: 700; color: #FFFFFF !important; }
.pending-red { color: #F87171 !important; }
.paid-green { color: #34D399 !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }

.mode-chip { display: inline-flex; align-items: center; gap: 4px; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.30) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Payment Management</h2>
        <p>Track and manage all property sale payments firm-wise.</p>
    </div>
    <a href="{{ route('payments.create') }}" class="btn-gold">
        <i class="fa-solid fa-plus"></i>
        <span>Add Payment</span>
    </a>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('payments.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width:200px;">
                    <option value="">-- All Firms --</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by customer, property, firm, mode, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('payments.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Pay ID</th>
                    <th>Firm</th>
                    <th>Customer</th>
                    <th>Property / Unit</th>
                    <th>Booking ID</th>
                    <th>Total Amount</th>
                    <th>Paid Amount</th>
                    <th>Pending Amount</th>
                    <th>Payment Mode</th>
                    <th>Pay Date</th>
                    <th>Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><span class="pay-id">#{{ $payment->id }}</span></td>
                        <td>
                            <strong style="color:#FFFFFF !important;">{{ $payment->firm->firm_name ?? '-' }}</strong>
                        </td>
                        <td>
                            <strong style="color:#FFFFFF !important;">{{ $payment->customer->name ?? '-' }}</strong>
                            @if($payment->customer?->mobile)
                                <div style="font-size:11.5px;color:#CBD5E1;">{{ $payment->customer->mobile }}</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:700;color:#FFFFFF;">{{ $payment->property->property_name ?? '-' }}</div>
                            @if($payment->property?->property_code)
                                <div style="font-size:11.5px;color:#CBD5E1;">{{ $payment->property->property_code }}</div>
                            @endif
                            @if($payment->property?->unit_no)
                                <div style="font-size:11.5px;color:#60A5FA;font-weight:600;">Unit: {{ $payment->property->unit_no }}</div>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('property-sales.show', $payment->property_sale_id) }}"
                               style="color:#60A5FA;font-weight:700;font-size:12.5px;text-decoration:none;">
                                #{{ $payment->property_sale_id }}
                            </a>
                        </td>
                        <td class="amount-col">₹{{ number_format($payment->total_amount, 0) }}</td>
                        <td class="amount-col paid-green">₹{{ number_format($payment->paid_amount, 0) }}</td>
                        <td class="amount-col {{ $payment->pending_amount > 0 ? 'pending-red' : 'paid-green' }}">
                            ₹{{ number_format($payment->pending_amount, 0) }}
                        </td>
                        <td>
                            @if($payment->payment_mode)
                                <span class="mode-chip">{{ $payment->payment_mode }}</span>
                            @else -
                            @endif
                        </td>
                        <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '-' }}</td>
                        <td><span class="badge badge-{{ $payment->status }}">{{ ucfirst($payment->status) }}</span></td>
                        <td>
                            <div class="action-buttons-wrap">
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Delete this payment? This will recalculate booking amounts.')"
                                        class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" align="center" style="padding:30px;color:#CBD5E1;">
                            No payment records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $payments->appends(request()->query())->links() }}
    </div>
</div>
@endsection

