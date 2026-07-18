@extends('admin.layouts.app')
@section('title','Debit Notes')
@section('page-title','GST / Accounts')
@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
.crud-title h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.crud-title p{font-size:13.5px;color:#64748B;}
.btn-gold{background:linear-gradient(135deg,#3B82F6,#2563EB);color:#FFF;padding:10px 20px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:all .22s ease;box-shadow:0 2px 8px rgba(59,130,246,0.3);}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(59,130,246,0.4);}
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 20px rgba(0,0,0,0.05);margin-bottom:20px;}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;}
.filter-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.6px;}
.filter-ctrl{padding:9px 12px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:#fff;transition:border-color .18s;min-width:140px;}
.filter-ctrl:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.search-input{padding:9px 14px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:13px;font-family:inherit;outline:none;transition:all .18s;min-width:210px;}
.search-input:focus{border-color:#3B82F6;box-shadow:0 0 0 3px rgba(59,130,246,0.12);}
.btn-search{background:#0F172A;color:#fff;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;align-self:flex-end;display:inline-flex;align-items:center;gap:6px;transition:background .18s;}
.btn-search:hover{background:#1E293B;}
.btn-reset{padding:9px 10px;color:#64748B;text-decoration:none;font-size:13px;align-self:flex-end;display:inline-flex;align-items:center;gap:5px;}
.btn-reset:hover{color:#0F172A;}
.total-bar{background:linear-gradient(135deg,rgba(239,68,68,0.06),rgba(239,68,68,0.02));border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.total-bar .tl{font-size:12.5px;color:#64748B;}
.total-bar .tv{font-size:19px;font-weight:800;color:#DC2626;}
.total-bar .tc{font-size:12.5px;color:#64748B;margin-left:auto;}
.table-wrap{width:100%;overflow-x:auto;}
.premium-table{width:100%;border-collapse:collapse;font-size:13.5px;}
.premium-table th{padding:11px 13px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid #E2E8F0;font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap;}
.premium-table td{padding:13px;border-bottom:1px solid #F1F5F9;color:#0F172A;vertical-align:middle;}
.premium-table tbody tr{transition:background .14s ease;}
.premium-table tbody tr:hover{background:#FFF5F5;}
.premium-table tr:last-child td{border-bottom:none;}
.dn-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;}
.dn-approved{background:rgba(16,185,129,0.1);color:#065F46;}
.dn-pending{background:rgba(245,158,11,0.1);color:#92400E;}
.dn-rejected{background:rgba(239,68,68,0.1);color:#991B1B;}
.action-links{display:flex;gap:9px;align-items:center;}
.action-link{color:#64748B;text-decoration:none;font-size:13px;display:inline-flex;align-items:center;gap:4px;transition:color .15s;}
.action-link.view:hover{color:#0EA5E9;}
.action-link.edit:hover{color:#3B82F6;}
.action-link.del-btn{background:none;border:none;cursor:pointer;color:#64748B;font-family:inherit;font-size:13px;padding:0;}
.action-link.del-btn:hover{color:#EF4444;}
.alert-success{background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);color:#065F46;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px;}
.pagination-wrapper{margin-top:22px;display:flex;justify-content:center;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Debit Notes</h2>
        <p>Manage all vendor debit adjustments and payable deduction entries.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="{{ route('reports.debit-note') }}" style="padding:10px 18px;border:1px solid #E2E8F0;border-radius:8px;font-size:13.5px;font-weight:600;color:#64748B;background:#FFF;text-decoration:none;display:inline-flex;align-items:center;gap:7px;transition:all .18s;">
            <i class="fa-solid fa-chart-bar"></i> Report
        </a>
        <a href="{{ route('debit-notes.create') }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Debit Note
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('debit-notes.index') }}" class="filter-bar">
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Note no, bill no, reason...">
        </div>
        <div class="filter-group">
            <span class="filter-label">Vendor</span>
            <select name="filter_vendor" class="filter-ctrl @error('filter_vendor') is-invalid @enderror">
                <option value="">All Vendors</option>
                @foreach($vendors as $v)
                    <option value="{{ $v->id }}" {{ request('filter_vendor')==$v->id?'selected':'' }}>{{ $v->name }}</option>
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
        @if(request()->hasAny(['search','filter_vendor','filter_status','from_date','to_date']))
            <a href="{{ route('debit-notes.index') }}" class="btn-reset"><i class="fa-solid fa-rotate-left"></i> Reset</a>
        @endif
    </form>

    <div class="total-bar">
        <i class="fa-solid fa-circle-minus" style="color:#EF4444;font-size:18px;"></i>
        <div><div class="tl">Total Debit Amount</div><div class="tv">₹{{ number_format($totalDebit,2) }}</div></div>
        <div class="tc"><i class="fa-solid fa-list-ul"></i> {{ $debitNotes->total() }} record{{ $debitNotes->total()!=1?'s':'' }}</div>
    </div>

    <div class="table-wrap">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Debit Note No</th><th>Date</th><th>Vendor / Supplier</th>
                    <th>Related Bill</th><th>Reason</th>
                    <th style="text-align:right;">Taxable</th>
                    <th style="text-align:right;">Total GST</th>
                    <th style="text-align:right;">Debit Amt</th>
                    <th style="text-align:center;">Status</th>
                    <th style="width:140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($debitNotes as $key => $dn)
                @php $badge = match($dn->status) {'Approved'=>'dn-approved','Rejected'=>'dn-rejected',default=>'dn-pending'}; @endphp
                <tr>
                    <td style="color:#94A3B8;font-size:12px;">{{ $debitNotes->firstItem() + $key }}</td>
                    <td style="font-weight:700;">{{ $dn->debit_note_no ?? '—' }}</td>
                    <td style="white-space:nowrap;font-size:13px;">{{ \Carbon\Carbon::parse($dn->debit_note_date)->format('d M Y') }}</td>
                    <td>
                        <div style="font-weight:600;font-size:13px;">{{ $dn->vendor?->name ?? '—' }}</div>
                        @if($dn->vendor?->mobile)<div style="font-size:11px;color:#64748B;">{{ $dn->vendor->mobile }}</div>@endif
                    </td>
                    <td style="font-size:13px;color:#64748B;">{{ $dn->related_bill_no ?? '—' }}</td>
                    <td style="font-size:13px;max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $dn->reason ? \Illuminate\Support\Str::limit($dn->reason, 35) : '—' }}
                    </td>
                    <td style="text-align:right;font-size:13px;">₹{{ number_format($dn->taxable_amount,2) }}</td>
                    <td style="text-align:right;color:#EF4444;font-weight:700;">₹{{ number_format($dn->total_gst,2) }}</td>
                    <td style="text-align:right;color:#DC2626;font-weight:800;font-size:14px;">₹{{ number_format($dn->debit_amount,2) }}</td>
                    <td style="text-align:center;"><span class="dn-badge {{ $badge }}">{{ $dn->status }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('debit-notes.show', $dn->id) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('debit-notes.edit', $dn->id) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('debit-notes.destroy', $dn->id) }}" method="POST" style="display:inline;" id="del-dn-{{ $dn->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="action-link del-btn"
                                    onclick="confirmDel({{ $dn->id }},'{{ addslashes($dn->debit_note_no ?? 'this note') }}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:44px;color:#94A3B8;">
                    <i class="fa-solid fa-circle-minus" style="font-size:32px;opacity:.2;display:block;margin-bottom:10px;"></i>
                    No debit notes found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $debitNotes->appends(request()->query())->links() }}</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id,no){
    Swal.fire({title:'Delete Debit Note?',html:'Delete <strong>'+no+'</strong>?<br><small style="color:#64748B;">This cannot be undone.</small>',
        icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',
        confirmButtonText:'Yes, Delete',cancelButtonText:'Cancel'})
    .then(r=>{if(r.isConfirmed)document.getElementById('del-dn-'+id).submit();});
}
</script>
@endsection

