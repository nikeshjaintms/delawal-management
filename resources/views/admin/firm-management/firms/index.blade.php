@extends('admin.layouts.app')
@section('title','Firm Profile & GST Details')
@section('page-title','Firm Management')

@section('content')
<style>
/* ── Common button system ── */
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-danger-custom,a.btn-danger-custom,button.btn-danger-custom{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:8px 14px;min-height:38px;background:linear-gradient(135deg,#dc3545,#b02a37);color:#fff !important;font-size:13px;font-weight:600;line-height:1;border:none;border-radius:9px;text-decoration:none !important;box-shadow:0 6px 16px rgba(220,53,69,.2);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-danger-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-action-custom,a.btn-action-custom,button.btn-action-custom{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:7px 12px;min-height:34px;background:#F4F7FB;color:#1E5AA8 !important;font-size:13px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.18);border-radius:8px;text-decoration:none !important;transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-action-custom:hover{background:#1E5AA8;color:#fff !important;text-decoration:none !important}
.btn-primary-custom i,.btn-secondary-custom i,.btn-danger-custom i,.btn-action-custom i{font-size:14px;line-height:1}
/* ── Layout ── */
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.filter-bar{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;align-items:center}
.search-input{padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;min-width:240px;transition:border-color .18s}
.search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
.filter-select{padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#fff}
.btn-search{background:var(--text-primary);color:#fff;padding:10px 18px;border-radius:8px;border:none;font-size:13.5px;font-weight:600;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;gap:6px}
.btn-search:hover{background:#1E293B}
.btn-reset{padding:10px 14px;color:var(--text-secondary);text-decoration:none;font-size:13.5px;font-weight:500;display:inline-flex;align-items:center;gap:5px}
.btn-reset:hover{color:var(--text-primary)}
.table-container{width:100%;overflow-x:auto}
.premium-table{width:100%;border-collapse:collapse;font-size:13.5px}
.premium-table th{padding:12px 16px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid var(--border-color);font-size:11px;text-transform:uppercase;letter-spacing:.8px}
.premium-table td{padding:14px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;color:var(--text-primary)}
.premium-table tbody tr:hover{background:#F0F7FF}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.action-col{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.firm-logo-sm{width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid var(--border-color)}
.pagination-wrap{margin-top:20px;display:flex;justify-content:center}
.alert-success{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);color:#065F46;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.alert-danger{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#991B1B;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Firm Profile &amp; GST Details</h2>
        <p>Manage your company profiles, GST details and bank information.</p>
    </div>
    <a href="{{ route('firm-master.create') }}" class="btn-primary-custom">
        <i class="fa fa-plus"></i> Add Firm
    </a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('firm-master.index') }}" class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search firm, owner, email, GST…" class="search-input @error('search') is-invalid @enderror">
        <select name="status" class="filter-select @error('status') is-invalid @enderror">
            <option value="">All Status</option>
            <option value="active"   {{ request('status')=='active'   ? 'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive' ? 'selected':'' }}>Inactive</option>
        </select>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
        @if(request('search') || request('status'))
            <a href="{{ route('firm-master.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th><th>Logo</th><th>Firm Name</th><th>Owner</th>
                    <th>Email</th><th>Mobile</th><th>GST No</th><th>City / State</th>
                    <th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($firms as $i => $firm)
                <tr>
                    <td>{{ $firms->firstItem() + $i }}</td>
                    <td>
                        @if($firm->firm_logo)
                            <img src="{{ Storage::url($firm->firm_logo) }}" class="firm-logo-sm" alt="Logo">
                        @else
                            <div style="width:36px;height:36px;background:var(--blue-light);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--blue)">
                                {{ strtoupper(substr($firm->firm_name,0,1)) }}
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $firm->firm_name }}</strong></td>
                    <td>{{ $firm->owner_name ?? '—' }}</td>
                    <td>{{ $firm->email ?? '—' }}</td>
                    <td>{{ $firm->mobile ?? '—' }}</td>
                    <td>{{ $firm->gst_no ?? '—' }}</td>
                    <td>{{ implode(', ', array_filter([$firm->city, $firm->state])) ?: '—' }}</td>
                    <td><span class="badge badge-{{ $firm->status }}">{{ ucfirst($firm->status) }}</span></td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('firm-master.show', $firm) }}" class="btn-action-custom"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('firm-master.edit', $firm) }}" class="btn-action-custom"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('firm-master.destroy', $firm) }}" method="POST" onsubmit="return confirm('Delete firm \'{{ addslashes($firm->firm_name) }}\'? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-custom"><i class="fa fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;padding:30px;color:var(--text-secondary)">No firms found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $firms->links() }}</div>
</div>
@endsection

