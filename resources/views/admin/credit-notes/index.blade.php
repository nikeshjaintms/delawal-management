@extends('admin.layouts.app')
@section('title','Credit Notes')
@section('page-title','GST / Accounts')
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
    display: flex !important; gap: 12px !important; align-items: flex-end !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.filter-group { display: flex; flex-direction: column; gap: 5px; flex-shrink: 0; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; }

.filter-ctrl, .search-input {
    padding: 9px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; min-width: 140px;
}
select.filter-ctrl option { background: #101622 !important; color: #FFFFFF !important; }
.filter-ctrl::placeholder, .search-input::placeholder { color: #94A3B8 !important; }
.filter-ctrl:focus, .search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 18px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important; align-self: flex-end;
    display: inline-flex; align-items: center; gap: 6px;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; align-self: flex-end; display: inline-flex; align-items: center; gap: 5px; }
.btn-reset:hover { color: #FFFFFF !important; }

/* Summary Total Bar */
.total-bar {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1px solid rgba(16, 185, 129, 0.30) !important;
    border-radius: 16px; padding: 16px 22px; margin-bottom: 20px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.total-bar .tl { font-size: 12.5px; color: #CBD5E1 !important; font-weight: 600; }
.total-bar .tv { font-size: 21px; font-weight: 800; color: #34D399 !important; }
.total-bar .tc { font-size: 12.5px; color: #94A3B8 !important; margin-left: auto; font-weight: 600; }

/* Table */
.table-wrap { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

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

/* Badges */
.cn-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.cn-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.cn-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.cn-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex !important; gap: 8px !important; align-items: center !important; white-space: nowrap !important; }
.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Credit Notes</h2>
        <p>Manage all customer credit adjustments and return entries.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('reports.credit-note') }}" class="btn-outline" style="padding:10px 18px;font-size:13.5px;font-weight:600;">
            <i class="fa-solid fa-chart-bar"></i> Report
        </a>
        <a href="{{ route('credit-notes.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Credit Note
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('credit-notes.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <span class="filter-label">Firm</span>
            <select name="firm_id" class="filter-ctrl" onchange="this.form.submit()">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id')==$f->id?'selected':'' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Note no, invoice, reason...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer</span>
            <select name="filter_customer" class="filter-ctrl @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="filter_status" class="filter-ctrl @error('filter_status') is-invalid @enderror">
                <option value="">All Status</option>
                @foreach(['Pending','Approved','Rejected'] as $s)
                    <option value="{{ $s }}" {{ request('filter_status')==$s?'selected':'' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-ctrl @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-ctrl @error('to_date') is-invalid @enderror">
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','firm_id','filter_customer','filter_status','from_date','to_date']))
            <a href="{{ route('credit-notes.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="total-bar">
        <i class="fa-solid fa-circle-plus" style="color:#34D399;font-size:22px;"></i>
        <div><div class="tl">Total Credit Amount</div><div class="tv">₹{{ number_format($totalCredit,2) }}</div></div>
        <div class="tc"><i class="fa-solid fa-list-ul"></i> {{ $creditNotes->total() }} record{{ $creditNotes->total()!=1?'s':'' }}</div>
    </div>

    <div class="table-wrap">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Firm</th><th>Credit Note No</th><th>Date</th><th>Customer</th>
                    <th>Related Invoice</th><th>Reason</th>
                    <th style="text-align:right;">Taxable</th>
                    <th style="text-align:right;">Total GST</th>
                    <th style="text-align:right;">Credit Amt</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:200px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($creditNotes as $key => $cn)
                @php $badge = match($cn->status) {'Approved'=>'cn-approved','Rejected'=>'cn-rejected',default=>'cn-pending'}; @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ method_exists($creditNotes, 'firstItem') ? ($creditNotes->firstItem() + $key) : ($key + 1) }}</td>
                    <td><strong style="color: #FFFFFF !important;">{{ $cn->firm->firm_name ?? '—' }}</strong></td>
                    <td style="font-weight:700;color:#FFFFFF;">{{ $cn->credit_note_no ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;color:#E2E8F0;">{{ \Carbon\Carbon::parse($cn->credit_note_date)->format('d M Y') }}</td>
                    <td>
                        <div style="font-weight:700;font-size:13.5px;color:#FFFFFF;">{{ $cn->customer?->name ?? '—' }}</div>
                        @if($cn->customer?->mobile)<div style="font-size:11.5px;color:#CBD5E1;">{{ $cn->customer->mobile }}</div>@endif
                    </td>
                    <td style="font-size:13px;color:#CBD5E1;">{{ $cn->related_invoice_no ?? '—' }}</td>
                    <td style="font-size:13px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#CBD5E1;">
                        {{ $cn->reason ? \Illuminate\Support\Str::limit($cn->reason, 35) : '—' }}
                    </td>
                    <td style="text-align:right;font-size:13px;color:#E2E8F0;">₹{{ number_format($cn->taxable_amount,2) }}</td>
                    <td style="text-align:right;color:#F87171;font-weight:700;">₹{{ number_format($cn->total_gst,2) }}</td>
                    <td style="text-align:right;color:#34D399;font-weight:800;font-size:14.5px;">₹{{ number_format($cn->credit_amount,2) }}</td>
                    <td style="text-align:center;"><span class="cn-badge {{ $badge }}">{{ $cn->status }}</span></td>
                    <td>
                        <div class="action-buttons-wrap">
                            <a href="{{ route('credit-notes.show', $cn->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('credit-notes.edit', $cn->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('credit-notes.destroy', $cn->id) }}" method="POST" style="display:inline;" id="del-cn-{{ $cn->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDel({{ $cn->id }},'{{ addslashes($cn->credit_note_no ?? 'this note') }}')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="12" style="text-align:center;padding:44px;color:#94A3B8;">
                    <i class="fa-solid fa-circle-plus" style="font-size:32px;opacity:.2;display:block;margin-bottom:10px;"></i>
                    No credit notes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($creditNotes, 'links'))
        <div class="pagination-wrapper">{{ $creditNotes->appends(request()->query())->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id,no){
    Swal.fire({title:'Delete Credit Note?',html:'Delete <strong>'+no+'</strong>?<br><small style="color:#64748B;">This cannot be undone.</small>',
        icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',
        confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel',customClass:{popup:'swal-cn-popup'}})
    .then(r=>{if(r.isConfirmed)document.getElementById('del-cn-'+id).submit();});
}
</script>
<style>.swal-cn-popup{font-family:'Inter',sans-serif!important;border-radius:14px!important;}</style>
@endsection

