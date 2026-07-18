@extends('admin.layouts.app')

@section('title', 'View Property')
@section('page-title', 'Property Master')

@section('content')
<style>
    .crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
    .crud-title h2 { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .crud-title p  { font-size: 13.5px; color: var(--text-secondary); }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 900px;
        margin: 0 auto;
    }
    .property-hero {
        display: flex;
        gap: 24px;
        padding-bottom: 24px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
    }
    .property-hero-img {
        width: 160px;
        height: 120px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid var(--border-color);
        flex-shrink: 0;
    }
    .property-hero-placeholder {
        width: 160px;
        height: 120px;
        border-radius: 10px;
        background: var(--gold-light);
        border: 2px solid rgba(212,175,55,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-size: 40px;
        flex-shrink: 0;
    }
    .property-hero-info h3 { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
    .property-hero-info p  { font-size: 13.5px; color: var(--text-secondary); margin-bottom: 10px; }
    .hero-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--gold);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 16px;
        margin-top: 24px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
    @media (max-width: 768px) { .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 576px) { .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }
    .detail-item {
        padding: 14px 16px;
        background: #F9FAFB;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        transition: var(--transition);
    }
    .detail-item:hover { border-color: rgba(212,175,55,0.2); background: #FFFFFF; box-shadow: 0 4px 12px rgba(15,31,53,0.04); }
    .detail-label { font-size: 11px; font-weight: 700; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 7px; display: flex; align-items: center; gap: 6px; }
    .detail-label i { color: var(--gold); font-size: 12px; }
    .detail-value { font-size: 14.5px; font-weight: 600; color: var(--text-primary); word-break: break-word; }
    .detail-value.empty { color: #9CA3AF; font-weight: 400; font-style: italic; }
    .detail-item-full { grid-column: 1 / -1; }
    .badge { display: inline-block; padding: 4px 12px; font-size: 11px; font-weight: 600; border-radius: 20px; text-transform: uppercase; }
    .badge-available { background: rgba(34,197,94,0.1);  color: #16803D; }
    .badge-booked    { background: rgba(234,179,8,0.12);  color: #92710A; }
    .badge-sold      { background: rgba(239,68,68,0.1);   color: #B91C1C; }
    .badge-rented    { background: rgba(59,130,246,0.1);  color: #1D4ED8; }
    .price-chip { display: inline-flex; align-items: center; gap: 4px; background: var(--gold-light); color: #92710A; font-size: 14px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(212,175,55,0.25); }
    .doc-link { display: inline-flex; align-items: center; gap: 6px; color: var(--gold); font-size: 13.5px; font-weight: 600; text-decoration: none; padding: 6px 14px; border: 1px solid rgba(212,175,55,0.35); border-radius: 7px; background: var(--gold-light); transition: var(--transition); }
    .doc-link:hover { background: rgba(212,175,55,0.2); color: #B58D1B; }
    .meta-info { margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; gap: 24px; flex-wrap: wrap; }
    .meta-item { font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; }
    .meta-item i { color: var(--gold); }
    .form-actions { display: flex; align-items: center; gap: 15px; margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--border-color); }
    .btn-gold { background-color: var(--gold); color: #FFFFFF; padding: 11px 24px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; transition: var(--transition); box-shadow: 0 4px 10px rgba(212,175,55,0.2); }
    .btn-gold:hover { background-color: #B58D1B; transform: translateY(-1px); }
    .btn-outline { border: 1px solid var(--border-color); background: transparent; color: var(--text-secondary); padding: 11px 24px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: var(--transition); }
    .btn-outline:hover { background: #F9FAFB; color: var(--text-primary); border-color: #D1D5DB; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Property Details</h2>
        <p>Full profile view of this firm-wise property record.</p>
    </div>
</div>

<div class="card-box">
    {{-- Hero Section --}}
    <div class="property-hero">
        @if($property->main_image)
            <img src="{{ asset('storage/' . $property->main_image) }}"
                 alt="{{ $property->property_name }}" class="property-hero-img">
        @else
            <div class="property-hero-placeholder">
                <i class="fa-solid fa-building"></i>
            </div>
        @endif
        <div class="property-hero-info">
            <h3>{{ $property->property_name }}</h3>
            <p>
                {{ $property->propertyType->name ?? 'No Type' }}
                @if($property->property_code) &nbsp;·&nbsp; <span style="color:var(--gold);font-weight:600;">{{ $property->property_code }}</span> @endif
                @if($property->city) &nbsp;·&nbsp; {{ $property->city }} @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
                @if($property->price !== null)
                    <span class="price-chip">₹{{ number_format($property->price, 2) }}</span>
                @endif
                @if($property->size)
                    <span style="font-size:13px;color:var(--text-secondary);">
                        {{ $property->size }} {{ $property->size_unit }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Basic Info --}}
    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Basic Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property Name</div>
            <div class="detail-value">{{ $property->property_name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Property Type</div>
            @if($property->propertyType)
                <div class="detail-value">{{ $property->propertyType->name }}</div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Property Code</div>
            @if($property->property_code)
                <div class="detail-value">{{ $property->property_code }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-circle-dot"></i> Status</div>
            <div class="detail-value">
                <span class="badge badge-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
            </div>
        </div>
    </div>

    {{-- Location --}}
    <div class="section-title"><i class="fa-solid fa-location-dot"></i> Location Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-map-pin"></i> Location</div>
            @if($property->location)
                <div class="detail-value">{{ $property->location }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-city"></i> City</div>
            @if($property->city)
                <div class="detail-value">{{ $property->city }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item detail-item-full">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> Address</div>
            @if($property->address)
                <div class="detail-value" style="font-weight:400;">{{ $property->address }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
    </div>

    {{-- Property Details --}}
    <div class="section-title"><i class="fa-solid fa-ruler-combined"></i> Property Details</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-ruler"></i> Size</div>
            @if($property->size)
                <div class="detail-value">{{ $property->size }} {{ $property->size_unit }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Price</div>
            @if($property->price !== null)
                <div class="detail-value"><span class="price-chip">₹{{ number_format($property->price, 2) }}</span></div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-compass"></i> Facing</div>
            @if($property->facing)
                <div class="detail-value">{{ $property->facing }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-door-open"></i> Unit No</div>
            @if($property->unit_no)
                <div class="detail-value">{{ $property->unit_no }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Floor No</div>
            @if($property->floor_no)
                <div class="detail-value">{{ $property->floor_no }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-lines"></i> Document</div>
            @if($property->document_file)
                <div class="detail-value">
                    <a href="{{ asset('storage/' . $property->document_file) }}" target="_blank" class="doc-link">
                        <i class="fa-solid fa-file-arrow-down"></i> View Document
                    </a>
                </div>
            @else
                <div class="detail-value empty">No document</div>
            @endif
        </div>
    </div>

    @if($property->description)
        <div class="section-title"><i class="fa-solid fa-align-left"></i> Description</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400; font-size:14px; line-height:1.7;">
                {{ $property->description }}
            </div>
        </div>
    @endif

    {{-- Meta --}}
    <div class="meta-info">
        <div class="meta-item">
            <i class="fa-regular fa-calendar-plus"></i>
            <span>Created: {{ $property->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="meta-item">
            <i class="fa-regular fa-calendar-check"></i>
            <span>Last Updated: {{ $property->updated_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    {{-- Actions --}}
    <div class="form-actions">
        <a href="{{ route('properties.edit', $property->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Property
        </a>
        <a href="{{ route('properties.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

{{-- ── Property Documents Tab ─────────────────────────────────── --}}
<div class="card-box" style="max-width:900px;margin:24px auto 0;">
    <style>
        .docs-section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px}
        .docs-section-title{font-size:15px;font-weight:700;color:var(--text-primary);display:flex;align-items:center;gap:8px}
        .docs-section-title i{color:var(--blue)}
        .doc-count-badge{display:inline-flex;align-items:center;justify-content:center;background:var(--blue-light);color:var(--blue);font-size:12px;font-weight:700;border-radius:20px;padding:2px 10px;min-width:26px}
        .docs-add-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;min-height:36px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:13px;font-weight:600;border-radius:9px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.2);transition:all .25s ease}
        .docs-add-btn:hover{transform:translateY(-2px);box-shadow:0 10px 22px rgba(30,90,168,.3);color:#fff !important}
        .doc-table{width:100%;border-collapse:collapse;font-size:13px}
        .doc-table th{padding:10px 14px;background:#F8FAFC;color:#475569;font-weight:700;border-bottom:2px solid var(--border-color);font-size:11px;text-transform:uppercase;letter-spacing:.7px;white-space:nowrap}
        .doc-table td{padding:11px 14px;border-bottom:1px solid #F1F5F9;vertical-align:middle}
        .doc-table tbody tr:hover{background:#F0F7FF}
        .doc-type-chip{display:inline-block;background:rgba(59,130,246,.08);color:#1D4ED8;font-size:11px;font-weight:600;border-radius:6px;padding:3px 8px;border:1px solid rgba(59,130,246,.15)}
        .doc-file-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:#F4F8FF;color:#1E5AA8 !important;border:1px solid rgba(30,90,168,.18);border-radius:7px;font-size:12px;font-weight:600;text-decoration:none !important;transition:all .2s}
        .doc-file-btn:hover{background:#1E5AA8;color:#fff !important}
        .badge-expired-sm{background:rgba(239,68,68,.1);color:#B91C1C;font-size:10px;font-weight:700;border-radius:4px;padding:2px 7px}
        .badge-expiring-sm{background:rgba(245,158,11,.1);color:#B45309;font-size:10px;font-weight:700;border-radius:4px;padding:2px 7px}
        .doc-empty{text-align:center;padding:32px;color:var(--text-secondary);font-size:13.5px}
        .doc-empty i{font-size:32px;display:block;margin-bottom:10px;color:#CBD5E1}
    </style>

    <div class="docs-section-header">
        <div class="docs-section-title">
            <i class="fa-solid fa-folder-open"></i>
            Property Documents
            <span class="doc-count-badge">{{ $property->documents->count() }}</span>
        </div>
        <a href="{{ route('property-documents.create') }}?property_id={{ $property->id }}" class="docs-add-btn">
            <i class="fa fa-plus"></i> Add Document
        </a>
    </div>

    @if($property->documents->isEmpty())
        <div class="doc-empty">
            <i class="fa-solid fa-folder-open"></i>
            No documents attached to this property yet.
            <br><br>
            <a href="{{ route('property-documents.create') }}?property_id={{ $property->id }}" style="color:var(--blue);font-weight:600">
                + Add first document
            </a>
        </div>
    @else
        <div style="overflow-x:auto">
            <table class="doc-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Title</th>
                        <th>Doc Number</th>
                        <th>Expiry</th>
                        <th>Status</th>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($property->documents as $i => $doc)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><span class="doc-type-chip">{{ $doc->document_type }}</span></td>
                        <td><strong>{{ $doc->document_title }}</strong></td>
                        <td>{{ $doc->document_number ?? '—' }}</td>
                        <td>
                            @if($doc->expiry_date)
                                {{ $doc->expiry_date->format('d M Y') }}
                                @if($doc->isExpired())
                                    <span class="badge-expired-sm">Expired</span>
                                @elseif($doc->isExpiringSoon())
                                    <span class="badge-expiring-sm">Soon</span>
                                @endif
                            @else
                                <span style="color:var(--text-muted)">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $doc->status }}">{{ ucfirst($doc->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ Storage::url($doc->document_file) }}" target="_blank" class="doc-file-btn">
                                <i class="fa-solid fa-file-arrow-down"></i> View
                            </a>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('property-documents.show', $doc) }}" class="btn-view"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('property-documents.edit', $doc) }}" class="btn-edit"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('property-documents.destroy', $doc) }}" method="POST"
                                      id="del-pdoc-{{ $doc->id }}" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-delete"
                                        onclick="delPDoc({{ $doc->id }}, '{{ addslashes($doc->document_title) }}')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function delPDoc(id, title) {
    Swal.fire({
        title: 'Delete Document?',
        text: '"' + title + '" will be permanently removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC3545',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Yes, Delete'
    }).then(r => { if (r.isConfirmed) document.getElementById('del-pdoc-' + id).submit(); });
}
</script>
@endsection
