@extends('admin.layouts.app')
@section('title','Property Documents')
@section('page-title','Property Documents')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.crud-title p{font-size:13.5px;color:var(--text-secondary)}
.filter-bar{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:flex-end}
.filter-group{display:flex;flex-direction:column;gap:4px}
.filter-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px}
.filter-control,.search-input{padding:9px 12px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13px;font-family:var(--font-primary);color:var(--text-primary);outline:none;background:#fff;transition:border-color .18s;min-width:140px}
.filter-control:focus,.search-input:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
.search-input{min-width:200px}
.btn-search{background:var(--text-primary);color:#fff;padding:9px 16px;border-radius:8px;border:none;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;align-self:flex-end}
.btn-reset{padding:9px 12px;color:var(--text-secondary);text-decoration:none;font-size:13px;font-weight:500;align-self:flex-end;display:inline-flex;align-items:center;gap:5px}
.table-container{width:100%;overflow-x:auto}
.premium-table{width:100%;border-collapse:collapse;font-size:13.5px}
.premium-table th{padding:12px 16px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid var(--border-color);font-size:11px;text-transform:uppercase;letter-spacing:.8px;white-space:nowrap}
.premium-table td{padding:13px 16px;border-bottom:1px solid #F1F5F9;vertical-align:middle;color:var(--text-primary)}
.premium-table tbody tr:hover{background:#F0F7FF}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.badge-expired{background:rgba(239,68,68,.12);color:#B91C1C}
.badge-expiring{background:rgba(245,158,11,.12);color:#B45309}
.doc-type-chip{display:inline-block;background:rgba(59,130,246,.08);color:#1D4ED8;font-size:11px;font-weight:600;border-radius:6px;padding:3px 9px;border:1px solid rgba(59,130,246,.15)}
.file-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#F4F8FF;color:#1E5AA8 !important;border:1px solid rgba(30,90,168,.2);border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none !important;transition:all .2s ease}
.file-btn:hover{background:#1E5AA8;color:#fff !important}
.alert-success{background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);color:#065F46;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.alert-danger-box{background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);color:#991B1B;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13.5px;display:flex;align-items:center;gap:8px}
.pagination-wrap{margin-top:20px;display:flex;justify-content:center}
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
        <div class="filter-group">
            <span class="filter-label">Search</span>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input @error('search') is-invalid @enderror" placeholder="Title, number, type…">
        </div>
        <div class="filter-group">
            <span class="filter-label">Property</span>
            <select name="property_id" class="filter-control @error('property_id') is-invalid @enderror">
                <option value="">All Properties</option>
                @foreach($properties as $prop)
                    <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>
                        {{ $prop->property_name }}
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
        @if(request()->hasAny(['search','property_id','document_type','status']))
            <a href="{{ route('property-documents.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Property</th>
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
                    <td>{{ $documents->firstItem() + $i }}</td>
                    <td><strong>{{ $doc->property->property_name ?? '—' }}</strong></td>
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
                        <a href="{{ Storage::url($doc->document_file) }}" target="_blank" class="file-btn">
                            <i class="fa-solid fa-file-arrow-down"></i> View
                        </a>
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
                    <td colspan="9" style="text-align:center;padding:32px;color:var(--text-secondary)">
                        No documents found. <a href="{{ route('property-documents.create') }}" style="color:var(--blue)">Add one</a>.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $documents->links() }}</div>
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
