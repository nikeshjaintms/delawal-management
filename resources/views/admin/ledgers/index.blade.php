@extends('admin.layouts.app')
@section('title','Ledger Management')
@section('page-title','GST / Accounts')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:24px;box-shadow:var(--soft-shadow);}
    /* summary cards */
    .sum-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;}
    .sum-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:18px 22px;box-shadow:var(--soft-shadow);display:flex;align-items:center;gap:14px;}
    .sum-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
    .sum-icon.red{background:rgba(239,68,68,0.1);color:#DC2626;}
    .sum-icon.green{background:rgba(34,197,94,0.1);color:#16803D;}
    .sum-icon.blue{background:rgba(59,130,246,0.1);color:#1D4ED8;}
    .sum-icon.gold{background:rgba(212,175,55,0.12);color:var(--gold);}
    .sum-body .s-label{font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:3px;}
    .sum-body .s-value{font-size:19px;font-weight:800;color:var(--text-primary);}
    /* filters */
    .filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
    .filter-group{display:flex;flex-direction:column;gap:5px;}
    .filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.6px;}
    .filter-control{padding:9px 12px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#FFF;transition:var(--transition);min-width:130px;}
    .filter-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .search-input{padding:9px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);outline:none;transition:var(--transition);min-width:200px;}
    .search-input:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    .btn-search{background-color:var(--text-primary);color:#FFF;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:var(--font-primary);align-self:flex-end;}
    .btn-reset{padding:9px 10px;color:var(--text-secondary);text-decoration:none;font-size:13px;align-self:flex-end;}
    /* table */
    .table-container{width:100%;overflow-x:auto;}
    .premium-table{width:100%;border-collapse:collapse;text-align:left;font-size:13.5px;}
    .premium-table th{padding:12px 14px;background:#F9FAFB;color:var(--text-secondary);font-weight:600;border-bottom:1px solid var(--border-color);font-size:11.5px;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;}
    .premium-table td{padding:13px 14px;border-bottom:1px solid #F1F5F9;color:var(--text-primary);vertical-align:middle;}
    .premium-table tr:last-child td{border-bottom:none;}
    .premium-table tbody tr:hover{background:#F9FAFB;}
    .type-chip{display:inline-block;padding:3px 9px;border-radius:6px;font-size:11.5px;font-weight:700;background:var(--gold-light);color:#92710A;border:1px solid rgba(212,175,55,0.2);}
    .mode-chip{background:#F1F5F9;color:#475569;padding:3px 8px;border-radius:5px;font-size:12px;font-weight:600;display:inline-block;}
    .debit-val{color:#DC2626;font-weight:700;}
    .credit-val{color:#16803D;font-weight:700;}
    .action-links{display:flex;gap:8px;align-items:center;white-space:nowrap;}
    .action-link{color:var(--text-secondary);text-decoration:none;font-size:13px;transition:var(--transition);display:inline-flex;align-items:center;gap:4px;}
    .action-link.view:hover{color:#0EA5E9;}
    .action-link.edit:hover{color:var(--gold);}
    .action-link.delete-btn{background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;padding:0;}
    .action-link.delete-btn:hover{color:#EF4444;}
    .alert-success{background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.2);color:#16803D;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
    .pagination-wrapper{margin-top:24px;display:flex;justify-content:center;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Ledger Management</h2>
        <p>Track all debit, credit, and financial transactions firm-wise.</p>
    </div>
    <a href="{{ route('ledgers.create') }}" class="btn-gold"><i class="fa-solid fa-plus"></i> Add Entry</a>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

{{-- Summary Cards --}}
<div class="sum-cards">
    <div class="sum-card">
        <div class="sum-icon red"><i class="fa-solid fa-arrow-up-right-from-square"></i></div>
        <div class="sum-body"><div class="s-label">Total Debit</div><div class="s-value" style="color:#DC2626;">₹{{ number_format($totalDebit,2) }}</div></div>
    </div>
    <div class="sum-card">
        <div class="sum-icon green"><i class="fa-solid fa-arrow-down-to-bracket"></i></div>
        <div class="sum-body"><div class="s-label">Total Credit</div><div class="s-value" style="color:#16803D;">₹{{ number_format($totalCredit,2) }}</div></div>
    </div>
    <div class="sum-card">
        <div class="sum-icon {{ $balance >= 0 ? 'blue' : 'red' }}"><i class="fa-solid fa-scale-balanced"></i></div>
        <div class="sum-body">
            <div class="s-label">Balance (Credit − Debit)</div>
            <div class="s-value" style="color:{{ $balance >= 0 ? '#1D4ED8' : '#DC2626' }};">
                {{ $balance >= 0 ? '' : '−' }}₹{{ number_format(abs($balance),2) }}
            </div>
        </div>
    </div>
    <div class="sum-card">
        <div class="sum-icon gold"><i class="fa-solid fa-book-open"></i></div>
        <div class="sum-body"><div class="s-label">Total Entries</div><div class="s-value">{{ $ledgers->total() }}</div></div>
    </div>
</div>

<div class="card-box">
    {{-- Filters --}}
    <form method="GET" action="{{ route('ledgers.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Title, ref no, party...">
        </div>
        <div class="filter-group">
            <span class="filter-label">From Date</span>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="filter-control @error('from_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">To Date</span>
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="filter-control @error('to_date') is-invalid @enderror">
        </div>
        <div class="filter-group">
            <span class="filter-label">Transaction Type</span>
            <select name="filter_type" class="filter-control @error('filter_type') is-invalid @enderror">
                <option value="">All Types</option>
                @foreach(['Sale','Payment Received','Expense','Purchase','Rent Received','Loan EMI','Other'] as $t)
                    <option value="{{ $t }}" {{ request('filter_type')==$t?'selected':'' }}>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="filter_property" class="filter-control @error('filter_property') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $p)
                    <option value="{{ $p->id }}" {{ request('filter_property')==$p->id?'selected':'' }}>{{ $p->property_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Customer</span>
            <select name="filter_customer" class="filter-control @error('filter_customer') is-invalid @enderror">
                <option value="">All Customers</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ request('filter_customer')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Vendor</span>
            <select name="filter_vendor" class="filter-control @error('filter_vendor') is-invalid @enderror">
                <option value="">All Vendors</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ request('filter_vendor')==$v->id?'selected':'' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Broker</span>
            <select name="filter_broker" class="filter-control @error('filter_broker') is-invalid @enderror">
                <option value="">All Brokers</option>
                @foreach($brokers as $b)
                    <option value="{{ $b->id }}" {{ request('filter_broker')==$b->id?'selected':'' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Payment Mode</span>
            <select name="filter_mode" class="filter-control @error('filter_mode') is-invalid @enderror">
                <option value="">All Modes</option>
                @foreach($paymentModes as $pm)
                    <option value="{{ $pm->name }}" {{ request('filter_mode')==$pm->name?'selected':'' }}>{{ $pm->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','from_date','to_date','filter_type','filter_property','filter_customer','filter_vendor','filter_broker','filter_mode']))
            <a href="{{ route('ledgers.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Transaction Title</th>
                    <th>Type</th>
                    <th>Party</th>
                    <th>Property</th>
                    <th style="text-align:right;">Debit (₹)</th>
                    <th style="text-align:right;">Credit (₹)</th>
                    <th>Mode</th>
                    <th>Ref No</th>
                    <th style="width:155px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ledgers as $key => $ledger)
                <tr>
                    <td style="color:var(--text-secondary);">{{ $ledgers->firstItem() + $key }}</td>
                    <td style="white-space:nowrap;font-size:13px;">{{ \Carbon\Carbon::parse($ledger->ledger_date)->format('d M Y') }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $ledger->transaction_title }}</div>
                        @if($ledger->remarks)
                            <div style="font-size:11.5px;color:var(--text-secondary);">{{ \Illuminate\Support\Str::limit($ledger->remarks,40) }}</div>
                        @endif
                    </td>
                    <td><span class="type-chip">{{ $ledger->transaction_type }}</span></td>
                    <td>
                        @if($ledger->customer)
                            <div style="font-size:13px;font-weight:600;">{{ $ledger->customer->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">Customer</div>
                        @elseif($ledger->vendor)
                            <div style="font-size:13px;font-weight:600;">{{ $ledger->vendor->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">Vendor</div>
                        @elseif($ledger->broker)
                            <div style="font-size:13px;font-weight:600;">{{ $ledger->broker->name }}</div>
                            <div style="font-size:11px;color:var(--text-secondary);">Broker</div>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $ledger->property?->property_name ?? '—' }}</td>
                    <td style="text-align:right;">
                        @if($ledger->debit_amount > 0)
                            <span class="debit-val">{{ number_format($ledger->debit_amount,2) }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;">
                        @if($ledger->credit_amount > 0)
                            <span class="credit-val">{{ number_format($ledger->credit_amount,2) }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($ledger->payment_mode)
                            <span class="mode-chip">{{ $ledger->payment_mode }}</span>
                        @else
                            <span style="color:var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;">{{ $ledger->reference_no ?? '—' }}</td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('ledgers.show', $ledger->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('ledgers.edit', $ledger->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('ledgers.destroy', $ledger->id) }}" method="POST" style="display:inline;" id="del-ldg-{{ $ledger->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete"
                                    onclick="confirmDel({{ $ledger->id }},'{{ addslashes($ledger->transaction_title) }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align:center;padding:40px;color:var(--text-secondary);">
                        <i class="fa-solid fa-book-open" style="font-size:28px;opacity:0.25;display:block;margin-bottom:8px;"></i>
                        No ledger entries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($ledgers->count() > 0)
            <tfoot>
                <tr style="background:#F9FAFB;">
                    <td colspan="6" style="padding:12px 14px;font-size:13px;font-weight:700;color:var(--text-primary);">
                        <i class="fa-solid fa-sigma" style="color:var(--gold);margin-right:6px;"></i> Total (filtered)
                    </td>
                    <td style="text-align:right;padding:12px 14px;font-size:14px;font-weight:800;color:#DC2626;">{{ number_format($totalDebit,2) }}</td>
                    <td style="text-align:right;padding:12px 14px;font-size:14px;font-weight:800;color:#16803D;">{{ number_format($totalCredit,2) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
    <div class="pagination-wrapper">{{ $ledgers->appends(request()->query())->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id, title) {
    Swal.fire({
        title: 'Delete Entry?',
        html: 'Delete <strong>' + title + '</strong>?<br><small style="color:#64748B;">This cannot be undone.</small>',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#EF4444', cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa fa-trash"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-ldg-popup' }
    }).then(r => { if (r.isConfirmed) document.getElementById('del-ldg-' + id).submit(); });
}
</script>
<style>.swal-ldg-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection

