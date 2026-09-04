@extends('admin.layouts.app')
@section('title','Invoice Number Series')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #FFFFFF !important; font-weight: 700 !important; margin: 0; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #1E293B !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #475569 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover { background: #334155 !important; color: #FFFFFF !important; transform: translateY(-2px); border-color: #64748B !important; }

.btn-danger-custom, a.btn-danger-custom, button.btn-danger-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 14px; min-height: 38px; background: #DC2626 !important;
    color: #FFFFFF !important; font-size: 13px; font-weight: 700; border: 1px solid #EF4444 !important;
    border-radius: 9px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(220,38,38,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-danger-custom:hover { background: #B91C1C !important; transform: translateY(-2px); }

.btn-action-custom, a.btn-action-custom, button.btn-action-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    padding: 7px 12px; min-height: 34px; background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important; font-size: 13px; font-weight: 700; border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 8px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-action-custom:hover { background: #2563EB !important; color: #FFFFFF !important; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}
.filter-bar {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; align-items: center;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.filter-select {
    padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.filter-select option { background: #101622 !important; color: #FFFFFF !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); }

.btn-reset { padding: 10px 14px; color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #E2E8F0 !important; font-weight: 500; vertical-align: middle; white-space: nowrap;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-block; padding: 4px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.prefix-pill { display: inline-block; background: rgba(59, 130, 246, 0.18); color: #60A5FA; font-size: 11.5px; font-weight: 700; border-radius: 6px; padding: 3px 8px; margin: 1px; border: 1px solid rgba(59, 130, 246, 0.30); }
.action-col { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Invoice Number Series</h2>
        <p>Configure invoice number prefixes and series for each module.</p>
    </div>
    <a href="{{ route('invoice-settings.create') }}" class="btn-primary-custom"><i class="fa fa-plus"></i> Add Settings</a>
</div>

@if(session('success'))
<div class="alert-success" style="background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #34D399; border-radius: 12px; padding: 14px 18px; margin-bottom: 20px; font-weight: 600;">
    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('invoice-settings.index') }}" class="filter-bar">
        <select name="status" class="filter-select @error('status') is-invalid @enderror">
            <option value="">All Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request('status'))
            <a href="{{ route('invoice-settings.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Firm Name</th>
                    <th>Financial Year</th>
                    <th>Booking / Sale</th>
                    <th>Receipt</th>
                    <th>Rental</th>
                    <th>Purchase</th>
                    <th>Credit / Debit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($settings as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong style="color: #FFFFFF;">{{ $s->firm->firm_name ?? 'All Firms' }}</strong></td>
                    <td>{{ $s->financialYear->year_name ?? '—' }}</td>
                    <td>
                        <span class="prefix-pill">B: {{ $s->booking_prefix }}</span>
                        <span class="prefix-pill">S: {{ $s->sale_prefix }}</span>
                    </td>
                    <td>
                        <span class="prefix-pill">R: {{ $s->receipt_prefix }}</span>
                        <span class="prefix-pill">P: {{ $s->payment_prefix }}</span>
                    </td>
                    <td>
                        <span class="prefix-pill">Rent: {{ $s->rental_prefix }}</span>
                    </td>
                    <td>
                        <span class="prefix-pill">PO: {{ $s->po_prefix }}</span>
                        <span class="prefix-pill">Pur: {{ $s->purchase_prefix }}</span>
                    </td>
                    <td>
                        <span class="prefix-pill">CN: {{ $s->credit_note_prefix }}</span>
                        <span class="prefix-pill">DN: {{ $s->debit_note_prefix }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span>
                    </td>
                    <td>
                        <div class="action-col">
                            <a href="{{ route('invoice-settings.show', $s) }}" class="btn-action-custom"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('invoice-settings.edit', $s) }}" class="btn-action-custom"><i class="fa fa-pen"></i> Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94A3B8; padding: 30px;">
                        No invoice series settings configured yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
