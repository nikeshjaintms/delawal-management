@extends('admin.layouts.app')

@section('title', 'Property Sales')
@section('page-title', 'Property Sales')

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
}
.premium-table td strong { color: #FFFFFF !important; font-weight: 700 !important; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.prop-name { font-weight: 700; color: #FFFFFF !important; }
.prop-code { font-size: 11.5px; color: #60A5FA !important; font-weight: 600; }
.amount-cell { font-weight: 700; color: #34D399 !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-booked { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-sold { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }

.btn-pay-quick {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #FFFFFF !important; border: 1px solid #34D399 !important;
    padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    box-shadow: 0 2px 10px rgba(16, 185, 129, 0.35); transition: all .2s ease;
}
.btn-pay-quick:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-1px); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.50);
}

/* Modal Styling */
.modal-overlay {
    position: fixed; inset: 0; background: rgba(5, 10, 20, 0.85); backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; z-index: 9999; padding: 16px;
}
.modal-overlay.active { display: flex; }
.modal-content-card {
    background: #131B2E; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px;
    width: 100%; max-width: 540px; padding: 28px; box-shadow: 0 24px 60px rgba(0,0,0,0.6);
    position: relative; animation: modalFadeIn 0.25s ease-out;
}
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.1); }
.modal-header h3 { font-size: 18px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px; }
.modal-close-btn { background: transparent; border: none; color: #94A3B8; font-size: 20px; cursor: pointer; padding: 4px; border-radius: 6px; transition: color .2s ease; }
.modal-close-btn:hover { color: #F87171; }
.m-form-group { margin-bottom: 16px; }
.m-form-label { display: block; font-size: 12.5px; font-weight: 700; color: #CBD5E1; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.m-form-control {
    width: 100%; padding: 10px 14px; background: rgba(10, 15, 26, 0.85);
    border: 1.5px solid rgba(255, 255, 255, 0.15); border-radius: 10px;
    color: #FFFFFF; font-size: 14px; outline: none; transition: border-color .2s; box-sizing: border-box;
}
.m-form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25); }
.m-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Sell</h2>
        <p>Manage property bookings, multiple plots, and installment payments firm-wise.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('property-sales.pdf', request()->query()) }}" target="_blank" class="btn-gold" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('property-sales.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i>
            <span>Add Property Sell</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('property-sales.index') }}" class="search-form">
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
                   placeholder="Search by property, plot, customer, broker, firm, status..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('property-sales.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Firm</th>
                    <th>Property / Plot(s)</th>
                    <th>Customer</th>
                    <th>Broker</th>
                    <th>Sale Date</th>
                    <th>Sale Amount</th>
                    <th>Paid / Due</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th style="min-width:260px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($propertySales as $key => $sale)
                    @php
                        $assignedPlots = $sale->all_properties;
                        $plotCount = $assignedPlots->count();
                        $paymentsCount = $sale->payments->count();
                    @endphp
                    <tr>
                        <td>{{ method_exists($propertySales, 'firstItem') ? ($propertySales->firstItem() + $key) : ($key + 1) }}</td>
                        <td>
                            <strong style="color: #FFFFFF !important;">{{ $sale->firm->firm_name ?? '-' }}</strong>
                        </td>
                        <td>
                            @if($plotCount > 1)
                                <div style="margin-bottom: 5px;">
                                    <span class="badge" style="background: rgba(59, 130, 246, 0.20) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.40) !important; font-size: 11px; padding: 2px 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-layer-group"></i> {{ $plotCount }} Plots Selected
                                    </span>
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 5px; max-width: 280px;">
                                    @foreach($assignedPlots as $p)
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.35); border-radius: 6px; padding: 2px 7px; font-size: 11.5px; font-weight: 700; color: #FFFFFF;" title="{{ $p->property_name }} {{ $p->property_code ? '('.$p->property_code.')' : '' }}">
                                            <i class="fa-solid fa-cube" style="font-size: 9px; color: #60A5FA;"></i>
                                            <span>{{ $p->property_name }}</span>
                                            @if($p->property_code)
                                                <span style="font-size: 10px; color: #93C5FD; font-weight: 600; opacity: 0.9;">({{ $p->property_code }})</span>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @elseif($plotCount === 1)
                                @php $singleP = $assignedPlots->first(); @endphp
                                <div class="prop-name" style="font-weight: 700; color: #FFFFFF; font-size: 13.5px;">{{ $singleP->property_name }}</div>
                                @if($singleP->property_code)
                                    <div class="prop-code" style="font-size: 11.5px; color: #93C5FD; font-weight: 600; margin-top: 2px;">{{ $singleP->property_code }}</div>
                                @endif
                            @else
                                <div class="prop-name" style="font-weight: 700; color: #FFFFFF; font-size: 13.5px;">{{ $sale->property->property_name ?? '-' }}</div>
                                @if($sale->property?->property_code)
                                    <div class="prop-code" style="font-size: 11.5px; color: #93C5FD; font-weight: 600; margin-top: 2px;">{{ $sale->property->property_code }}</div>
                                @endif
                            @endif
                        </td>
                        <td>{{ $sale->customer->name ?? '-' }}</td>
                        <td>{{ $sale->broker->name ?? '-' }}</td>
                        <td>{{ $sale->sale_date ? \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($sale->sale_amount !== null)
                                <strong style="color: #60A5FA; font-size: 14px;">₹{{ number_format($sale->sale_amount, 2) }}</strong>
                            @else
                                <span style="color: #94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($sale->sale_amount > 0)
                                <div style="font-size: 12.5px; color: #34D399; font-weight: 700; display: flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-circle-check"></i> ₹{{ number_format($sale->booking_amount ?? 0, 2) }}
                                </div>
                                <div style="font-size: 12.5px; color: {{ ($sale->remaining_amount ?? 0) > 0 ? '#F87171' : '#94A3B8' }}; font-weight: 700; margin-top: 2px; display: flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-clock"></i> ₹{{ number_format($sale->remaining_amount ?? 0, 2) }}
                                </div>

                                {{-- Direct Listing of All Installments Made --}}
                                @if($paymentsCount > 0)
                                    <div style="margin-top: 6px; display: flex; flex-direction: column; gap: 3px; max-width: 250px;">
                                        @foreach($sale->payments->sortBy('payment_date')->values() as $idx => $pay)
                                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 6px; background: rgba(16, 185, 129, 0.09); border: 1px solid rgba(16, 185, 129, 0.22); border-radius: 6px; padding: 2px 6px; font-size: 11px;" title="{{ $pay->remarks ? $pay->remarks : ($pay->transaction_ref ? 'Ref: '.$pay->transaction_ref : '') }}">
                                                <div style="display: flex; align-items: center; gap: 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <span style="color: #94A3B8; font-size: 9.5px; font-weight: 800;">#{{ $idx + 1 }}</span>
                                                    <span style="color: #93C5FD; font-size: 10.5px; font-weight: 600;">
                                                        {{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M') : '—' }}
                                                    </span>
                                                    @if($pay->payment_mode)
                                                        <span style="color: #CBD5E1; font-size: 9.5px; background: rgba(255,255,255,0.08); padding: 0 4px; border-radius: 3px;">
                                                            {{ $pay->payment_mode }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <strong style="color: #34D399; font-size: 11px; font-weight: 800; white-space: nowrap;">
                                                    ₹{{ number_format($pay->payment_amount, 2) }}
                                                </strong>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div style="margin-top: 4px;">
                                        <button type="button" class="badge" style="background: rgba(139, 92, 246, 0.20) !important; color: #C4B5FD !important; border: 1px solid rgba(139, 92, 246, 0.40) !important; cursor: pointer; padding: 1px 7px; font-size: 10px;" onclick='openPaymentHistoryModal({{ $sale->id }}, "{{ addslashes($sale->customer->name ?? "Customer") }}", {{ $sale->sale_amount ?? 0 }}, {{ $sale->booking_amount ?? 0 }}, {{ $sale->remaining_amount ?? 0 }}, @json($sale->payments))'>
                                            <i class="fa-solid fa-receipt"></i> {{ $paymentsCount }} {{ $paymentsCount > 1 ? 'Installments' : 'Payment' }} · Manage
                                        </button>
                                    </div>
                                @endif
                            @else
                                <span style="color: #94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @php $salePayStatus = strtolower($sale->payment_status ?? 'pending'); @endphp
                            @if($salePayStatus === 'paid')
                                <span class="badge badge-paid">Paid</span>
                            @elseif($salePayStatus === 'partial')
                                <span class="badge badge-partial">Partial</span>
                            @else
                                <span class="badge badge-pending">Pending</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $sale->sale_status }}">{{ ucfirst($sale->sale_status) }}</span></td>
                        <td style="text-align:right;">
                            <div class="action-buttons-wrap" style="justify-content:flex-end;">
                                @if(($sale->remaining_amount ?? 0) > 0)
                                    <button type="button" class="btn-pay-quick" onclick='openRecordPaymentModal({{ $sale->id }}, "{{ addslashes($sale->customer->name ?? "Customer") }}", "{{ addslashes($sale->property_names) }}", {{ $sale->sale_amount ?? 0 }}, {{ $sale->booking_amount ?? 0 }}, {{ $sale->remaining_amount ?? 0 }})' title="Record 2nd / Next Installment Payment">
                                        <i class="fa-solid fa-circle-plus"></i> + Pay
                                    </button>
                                @endif
                                <a href="{{ route('property-sales.receipt-pdf', $sale->id) }}" target="_blank" class="btn-view" style="background: rgba(252,105,0,0.15) !important; color: #FF8A3D !important; border: 1px solid rgba(252,105,0,0.30) !important;" title="Print / PDF Agreement">
                                    <i class="fa fa-file-pdf"></i> PDF
                                </a>
                                <a href="{{ route('property-sales.show', $sale->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('property-sales.edit', $sale->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('property-sales.destroy', $sale->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this sale record? All associated plots will be returned to available status.')"
                                        class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" align="center" style="padding:30px; color:#CBD5E1;">
                            No property sale records found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($propertySales, 'links'))
        <div class="pagination-wrapper">
            {{ $propertySales->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- ── 1. Record Installment Payment Modal ── --}}
<div class="modal-overlay" id="recordPaymentModal">
    <div class="modal-content-card">
        <div class="modal-header">
            <h3><i class="fa-solid fa-hand-holding-dollar" style="color: #34D399;"></i> Record Installment Payment</h3>
            <button type="button" class="modal-close-btn" onclick="closeRecordPaymentModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="recordPaymentForm" method="POST" action="">
            @csrf
            <div style="background: rgba(37, 99, 235, 0.10); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 14px; margin-bottom: 18px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Customer:</span>
                    <strong style="color: #FFFFFF;" id="modal_customer_name">—</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Plot / Property:</span>
                    <strong style="color: #60A5FA;" id="modal_property_name">—</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Total Sale Value:</span>
                    <strong style="color: #CBD5E1;" id="modal_sale_amount">—</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px;">
                    <span style="color: #94A3B8;">Paid So Far:</span>
                    <strong style="color: #34D399;" id="modal_paid_amount">—</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; margin-top: 6px; padding-top: 6px; border-top: 1px dashed rgba(255,255,255,0.15);">
                    <span style="color: #F87171; font-weight: 700;">Remaining Due Balance:</span>
                    <strong style="color: #F87171; font-size: 15px;" id="modal_due_amount">—</strong>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Payment Amount (₹) <span style="color:#EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="payment_amount" id="modal_input_amount" class="m-form-control" required placeholder="0.00">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Payment Date <span style="color:#EF4444;">*</span></label>
                    <input type="date" name="payment_date" id="modal_input_date" class="m-form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Payment Mode <span style="color:#EF4444;">*</span></label>
                    <select name="payment_mode" class="m-form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT / RTGS</option>
                        <option value="Cheque">Cheque</option>
                        <option value="UPI">UPI / GPay / PhonePe</option>
                        @foreach($paymentModes as $pm)
                            @if(!in_array($pm->name, ['Cash', 'Bank Transfer', 'Cheque', 'UPI']))
                                <option value="{{ $pm->name }}">{{ $pm->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Ref No. / Cheque No.</label>
                    <input type="text" name="transaction_ref" class="m-form-control" placeholder="e.g. TXN987654 / CHQ-1002">
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Remarks / Notes</label>
                <input type="text" name="remarks" class="m-form-control" placeholder="Optional installment notes">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.1);">
                <button type="button" class="btn-reset" onclick="closeRecordPaymentModal()">Cancel</button>
                <button type="submit" class="btn-gold" style="background: #10B981 !important; border-color: #34D399 !important;">
                    <i class="fa-solid fa-check"></i> Save Payment
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── 2. Payment History / Installments Modal ── --}}
<div class="modal-overlay" id="paymentHistoryModal">
    <div class="modal-content-card" style="max-width: 680px;">
        <div class="modal-header">
            <h3><i class="fa-solid fa-receipt" style="color: #A78BFA;"></i> Payment Installment History</h3>
            <button type="button" class="modal-close-btn" onclick="closePaymentHistoryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="background: rgba(139, 92, 246, 0.10); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 12px; padding: 14px; margin-bottom: 18px; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div>
                <span style="font-size: 12px; color: #94A3B8; text-transform: uppercase;">Customer:</span>
                <div style="font-weight: 700; color: #FFFFFF;" id="hist_customer_name">—</div>
            </div>
            <div>
                <span style="font-size: 12px; color: #94A3B8; text-transform: uppercase;">Total Sale:</span>
                <div style="font-weight: 700; color: #60A5FA;" id="hist_total_sale">—</div>
            </div>
            <div>
                <span style="font-size: 12px; color: #94A3B8; text-transform: uppercase;">Total Paid:</span>
                <div style="font-weight: 700; color: #34D399;" id="hist_total_paid">—</div>
            </div>
            <div>
                <span style="font-size: 12px; color: #94A3B8; text-transform: uppercase;">Due Balance:</span>
                <div style="font-weight: 700; color: #F87171;" id="hist_total_due">—</div>
            </div>
        </div>

        <div style="max-height: 320px; overflow-y: auto;">
            <table class="premium-table" style="font-size: 12.5px;">
                <thead>
                    <tr>
                        <th style="padding: 10px 14px !important;">#</th>
                        <th style="padding: 10px 14px !important;">Date</th>
                        <th style="padding: 10px 14px !important;">Amount</th>
                        <th style="padding: 10px 14px !important;">Mode</th>
                        <th style="padding: 10px 14px !important;">Ref / Cheque</th>
                        <th style="padding: 10px 14px !important;">Remarks</th>
                    </tr>
                </thead>
                <tbody id="hist_table_body">
                    <!-- Populated dynamically via JS -->
                </tbody>
            </table>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.1);">
            <button type="button" class="btn-gold" onclick="closePaymentHistoryModal()">Close</button>
        </div>
    </div>
</div>

<script>
function openRecordPaymentModal(saleId, customerName, propertyName, saleAmount, paidAmount, dueAmount) {
    document.getElementById('recordPaymentForm').action = "/property-sales/" + saleId + "/payments";
    document.getElementById('modal_customer_name').textContent = customerName;
    document.getElementById('modal_property_name').textContent = propertyName;
    document.getElementById('modal_sale_amount').textContent = '₹ ' + parseFloat(saleAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('modal_paid_amount').textContent = '₹ ' + parseFloat(paidAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('modal_due_amount').textContent = '₹ ' + parseFloat(dueAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});
    
    document.getElementById('modal_input_amount').value = parseFloat(dueAmount).toFixed(2);
    document.getElementById('modal_input_amount').max = parseFloat(dueAmount).toFixed(2);
    document.getElementById('recordPaymentModal').classList.add('active');
}

function closeRecordPaymentModal() {
    document.getElementById('recordPaymentModal').classList.remove('active');
}

function openPaymentHistoryModal(saleId, customerName, saleAmount, paidAmount, dueAmount, payments) {
    document.getElementById('hist_customer_name').textContent = customerName;
    document.getElementById('hist_total_sale').textContent = '₹ ' + parseFloat(saleAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('hist_total_paid').textContent = '₹ ' + parseFloat(paidAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('hist_total_due').textContent = '₹ ' + parseFloat(dueAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});

    const tbody = document.getElementById('hist_table_body');
    tbody.innerHTML = '';

    if (!payments || payments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" align="center" style="padding: 20px; color: #94A3B8;">No payment records logged yet.</td></tr>';
    } else {
        payments.forEach((p, idx) => {
            const dateStr = p.payment_date ? new Date(p.payment_date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short', year: 'numeric'}) : '—';
            const amtStr = '₹ ' + parseFloat(p.payment_amount || 0).toLocaleString('en-IN', {minimumFractionDigits: 2});
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="padding: 10px 14px !important; color: #94A3B8;">${idx + 1}</td>
                <td style="padding: 10px 14px !important; font-weight: 700; color: #FFFFFF;">${dateStr}</td>
                <td style="padding: 10px 14px !important; color: #34D399; font-weight: 700;">${amtStr}</td>
                <td style="padding: 10px 14px !important; color: #CBD5E1;"><span class="badge" style="background: rgba(255,255,255,0.08);">${p.payment_mode || 'Cash'}</span></td>
                <td style="padding: 10px 14px !important; color: #93C5FD;">${p.transaction_ref || '—'}</td>
                <td style="padding: 10px 14px !important; color: #94A3B8; font-size: 11.5px;">${p.remarks || '—'}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('paymentHistoryModal').classList.add('active');
}

function closePaymentHistoryModal() {
    document.getElementById('paymentHistoryModal').classList.remove('active');
}

// Close on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRecordPaymentModal();
        closePaymentHistoryModal();
    }
});

// Close modal when clicking on overlay background
document.getElementById('recordPaymentModal').addEventListener('click', function(e) {
    if (e.target === this) closeRecordPaymentModal();
});
document.getElementById('paymentHistoryModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentHistoryModal();
});
</script>
@endsection
