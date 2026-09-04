@extends('admin.layouts.app')
@section('title','Property Documents')
@section('page-title','Property Documents')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 28px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 22px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 12px; text-decoration: none !important; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 24px; align-items: flex-end;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: 0.8px; }

.filter-control, .search-input {
    padding: 11px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: border-color .18s; min-width: 150px;
}
.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-control:focus, .search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.search-input { min-width: 220px; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 22px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; align-self: flex-end;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35); transition: all .25s ease;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; align-self: flex-end; padding: 11px 14px; display: inline-flex; align-items: center; gap: 5px; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; font-size: 14px; text-align: left; }
.premium-table th {
    padding: 16px 22px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11.5px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 18px 22px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 14px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-block; padding: 5px 14px; font-size: 11.5px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-expired { display: inline-flex; align-items: center; gap: 6px; background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; border-radius: 20px; padding: 5px 14px; font-weight: 700; font-size: 11.5px; white-space: nowrap !important; }
.badge-expiring { display: inline-flex; align-items: center; gap: 6px; background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; border-radius: 20px; padding: 5px 14px; font-weight: 700; font-size: 11.5px; white-space: nowrap !important; }

.doc-type-chip {
    display: inline-block; background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    font-size: 12px; font-weight: 700; border-radius: 8px; padding: 5px 12px;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; white-space: nowrap !important;
}

.file-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important; border-radius: 10px;
    font-size: 13px; font-weight: 700; text-decoration: none !important; transition: all .2s ease;
    white-space: nowrap !important;
}
.file-btn:hover { background: #2563EB !important; border-color: #3B82F6 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.table-action-buttons {
    display: flex !important; flex-direction: row !important; align-items: center !important;
    gap: 10px !important; flex-wrap: nowrap !important; white-space: nowrap !important; justify-content: flex-end;
}
.table-action-buttons form { display: inline-flex !important; margin: 0 !important; padding: 0 !important; }

.btn-view, a.btn-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(96, 165, 250, 0.30) !important; border-radius: 10px; font-size: 13px !important;
    font-weight: 700 !important; text-decoration: none !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-view:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40); }

.btn-edit, a.btn-edit {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important;
    border: 1px solid rgba(245, 158, 11, 0.30) !important; border-radius: 10px; font-size: 13px !important;
    font-weight: 700 !important; text-decoration: none !important; transition: all .2s ease; white-space: nowrap !important;
}
.btn-edit:hover { background: #D97706 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(217, 119, 6, 0.40); }

.btn-delete, button.btn-delete {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 8px 14px !important;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 10px; font-size: 13px !important;
    font-weight: 700 !important; text-decoration: none !important; transition: all .2s ease; cursor: pointer; white-space: nowrap !important;
}
.btn-delete:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40); }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.alert-danger-box { background: rgba(239, 68, 68, 0.15) !important; border: 1px solid rgba(239, 68, 68, 0.30) !important; color: #F87171 !important; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrap { margin-top: 20px; display: flex; justify-content: center; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Documents</h2>
        <p>Manage all property-related legal and compliance documents.</p>
    </div>
    <a href="{{ route('property-documents.create') }}" class="btn-primary-custom">
        <i class="fa fa-plus"></i> Add Document
    </a>
</div>

@if(session('success'))
<div class="alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-danger-box"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card-box">
    <form method="GET" action="{{ route('property-documents.index') }}" class="filter-bar">
        @if(auth()->user() && auth()->user()->isAdmin())
            <div class="filter-group">
                <span class="filter-label">Firm</span>
                <select name="firm_id" class="filter-control @error('firm_id') is-invalid @enderror" onchange="this.form.submit()">
                    <option value="">All Firms</option>
                    @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Title, number, type…">
        </div>
        <div class="filter-group">
            <span class="filter-label">Land Property</span>
            <select name="property_master_id" class="filter-control @error('property_master_id') is-invalid @enderror">
                <option value="">All Land Properties</option>
                @foreach($propertyMasters as $pm)
                    <option value="{{ $pm->id }}" {{ (request('property_master_id') == $pm->id || request('property_id') == $pm->id) ? 'selected' : '' }}>
                        {{ $pm->property_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Document Type</span>
            <select name="document_type" class="filter-control @error('document_type') is-invalid @enderror">
                <option value="">All Types</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type }}" {{ request('document_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-group">
            <span class="filter-label">Status</span>
            <select name="status" class="filter-control @error('status') is-invalid @enderror">
                <option value="">All</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
        @if(request()->hasAny(['search','property_master_id','property_id','document_type','status','firm_id']))
            <a href="{{ route('property-documents.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-responsive-wrapper">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Land Property</th>
                    <th>Document Type</th>
                    <th>Document Title</th>
                    <th>Doc Number</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($documents as $i => $doc)
                <tr>
                    <td>{{ method_exists($documents, 'firstItem') ? ($documents->firstItem() + $i) : ($i + 1) }}</td>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <td><strong>{{ $doc->firm->firm_name ?? 'N/A' }}</strong></td>
                    @endif
                    <td><strong style="color:#FFFFFF;">{{ $doc->target_name }}</strong></td>
                    <td><span class="doc-type-chip">{{ $doc->document_type }}</span></td>
                    <td>{{ $doc->document_title }}</td>
                    <td>{{ $doc->document_number ?? '—' }}</td>
                    <td>
                        @if($doc->expiry_date)
                            @if($doc->isExpired())
                                <span class="badge badge-expired">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Expired {{ $doc->expiry_date->format('d M Y') }}
                                </span>
                            @elseif($doc->isExpiringSoon())
                                <span class="badge badge-expiring">
                                    <i class="fa-solid fa-clock"></i>
                                    {{ $doc->expiry_date->format('d M Y') }}
                                </span>
                            @else
                                {{ $doc->expiry_date->format('d M Y') }}
                            @endif
                        @else
                            <span style="color:var(--text-muted)">—</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $doc->status }}">{{ ucfirst($doc->status) }}</span></td>
                    <td>
                        @if($doc->document_file)
                            <a href="{{ Storage::url($doc->document_file) }}" target="_blank" class="file-btn">
                                <i class="fa-solid fa-file-arrow-down"></i> View
                            </a>
                        @else
                            <span style="color:#64748B">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-action-buttons">
                            <a href="{{ route('property-documents.show', $doc) }}" class="btn-view"><i class="fa fa-eye"></i> View</a>
                            <a href="{{ route('property-documents.edit', $doc) }}" class="btn-edit"><i class="fa fa-edit"></i> Edit</a>
                            <form action="{{ route('property-documents.destroy', $doc) }}" method="POST" id="del-doc-{{ $doc->id }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-delete" onclick="confirmDelete({{ $doc->id }}, '{{ addslashes($doc->document_title) }}')">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:32px;color:var(--text-secondary)">
                        No documents found. <a href="{{ route('property-documents.create') }}" style="color:var(--blue)">Add one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($documents, 'links'))
        <div class="pagination-wrap">{{ $documents->links() }}</div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Delete Document?',
        text: '"' + title + '" will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-doc-' + id).submit(); });
}
</script>
@endsection
