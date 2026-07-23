@extends('admin.layouts.app')
@section('title','Financial Year Setup')
@section('page-title','Firm Management')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-danger-custom,a.btn-danger-custom,button.btn-danger-custom{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 14px;min-height:38px;background:linear-gradient(135deg,#dc3545,#b02a37);color:#fff !important;font-size:13px;font-weight:600;line-height:1;border:none;border-radius:9px;text-decoration:none !important;box-shadow:0 6px 16px rgba(220,53,69,.2);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-danger-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-action-custom,a.btn-action-custom,button.btn-action-custom{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:7px 12px;min-height:34px;background:#F4F7FB;color:#1E5AA8 !important;font-size:13px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.18);border-radius:8px;text-decoration:none !important;transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-action-custom:hover{background:#1E5AA8;color:#fff !important;text-decoration:none !important}
.btn-activate-custom,button.btn-activate-custom{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:7px 12px;min-height:34px;background:#F0FDF4;color:#059669 !important;font-size:13px;font-weight:600;line-height:1;border:1px solid rgba(5,150,105,.25);border-radius:8px;text-decoration:none !important;transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-activate-custom:hover{background:#059669;color:#fff !important;text-decoration:none !important}
.btn-primary-custom i,.btn-danger-custom i,.btn-action-custom i,.btn-activate-custom i{font-size:13px;line-height:1}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.search-input{padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);outline:none;min-width:220px;transition:border-color .18s}
.search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
.filter-select{padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);outline:none;background:#fff}
.btn-search{background:var(--text-primary);color:#fff;padding:10px 18px;border-radius:8px;border:none;font-size:13.5px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
.btn-reset{padding:10px 14px;color:var(--text-secondary);text-decoration:none;font-size:13.5px;display:inline-flex;align-items:center;gap:5px}
.table-container{width:100%;overflow-x:auto}
.premium-table{width:100%;border-collapse:collapse;font-size:13.5px}
.premium-table th{padding:12px 16px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid var(--border-color);font-size:11px;text-transform:uppercase;letter-spacing:.8px}
.premium-table td{padding:14px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle}
.premium-table tbody tr:hover{background:#F0F7FF}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.badge-yes{background:rgba(59,130,246,.12);color:#1D4ED8}
.action-col{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.pagination-wrap{margin-top:20px;display:flex;justify-content:center}
.alert-success{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);color:#065F46;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.alert-danger-box{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#991B1B;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.active-indicator{display:inline-flex;align-items:center;gap:5px;background:rgba(59,130,246,.1);color:#1D4ED8;font-size:11px;font-weight:700;border-radius:20px;padding:3px 10px}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Financial Year Setup</h2>
        <p>Manage financial years. Only one year can be active at a time.</p>
    </div>
    <a href="{{ route('financial-years.create') }}" class="btn-primary-custom"><i class="fa fa-plus"></i> Add Year</a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger-box"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('financial-years.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search year name…" class="search-input @error('search') is-invalid @enderror">
        <select name="status" class="filter-select @error('status') is-invalid @enderror">
            <option value="">All Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('financial-years.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Year Name</th><th>Start Date</th>
                    <th>End Date</th><th>Active</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($years as $i => $year)
                <tr>
                    <td>{{ $years->firstItem() + $i }}</td>
                    <td>
                        <strong>{{ $year->year_name }}</strong>
                        @if($year->is_active)
                            <span class="active-indicator" style="margin-left:6px"><i class="fa-solid fa-circle-dot"></i> Current</span>
                        @endif
                    </td>
                    <td>{{ $year->start_date->format('d M Y') }}</td>
                    <td>{{ $year->end_date->format('d M Y') }}</td>
                    <td>
                        @if($year->is_active)
                            <span class="badge badge-yes"><i class="fa-solid fa-check"></i> Yes</span>
                        @else
                            <span style="color:var(--text-muted);font-size:12px">No</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $year->status }}">{{ ucfirst($year->status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('financial-years.show', $year) }}" class="btn-action-custom"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('financial-years.edit', $year) }}" class="btn-action-custom"><i class="fa fa-edit"></i> Edit</a>
                            @if(!$year->is_active)
                            <form action="{{ route('financial-years.set-active', $year) }}" method="POST" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-activate-custom"><i class="fa-solid fa-bolt"></i> Set Active</button>
                            </form>
                            <form action="{{ route('financial-years.destroy', $year) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($year->year_name) }}?')" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-custom"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--text-secondary)">No financial years found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $years->links() }}</div>
</div>
@endsection

