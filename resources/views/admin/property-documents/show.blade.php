@extends('admin.layouts.app')
@section('title', $doc->document_title . ' — Document Details')
@section('page-title','Property Documents')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    transition: all .25s ease; cursor: pointer;
}
.btn-primary-custom:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50);
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; min-height: 42px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-secondary-custom:hover {
    background: rgba(255, 255, 255, 0.16) !important; color: #FFFFFF !important; transform: translateY(-2px);
}

.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }
.header-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.detail-card {
    background: rgba(15, 23, 42, 0.55) !important;
    backdrop-filter: blur(14px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(14px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 28px 32px !important;
    box-shadow: 0 12px 36px rgba(0, 0, 0, 0.30) !important;
    margin-bottom: 24px;
}

.section-heading {
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .9px;
    color: #60A5FA !important;
    margin-bottom: 22px;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media(max-width:768px){ .detail-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px){ .detail-grid { grid-template-columns: 1fr; } }

.detail-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: #94A3B8 !important;
    margin-bottom: 6px;
}
.detail-value {
    font-size: 15px;
    font-weight: 700;
    color: #FFFFFF !important;
}

.prop-link {
    color: #60A5FA !important;
    text-decoration: none;
    font-weight: 700;
    font-size: 15px;
    transition: color 0.2s ease;
}
.prop-link:hover {
    color: #93C5FD !important;
    text-decoration: underline;
}

.doc-type-chip {
    display: inline-block;
    background: rgba(59, 130, 246, 0.20) !important;
    color: #60A5FA !important;
    font-size: 12.5px;
    font-weight: 800;
    border-radius: 8px;
    padding: 5px 14px;
    border: 1px solid rgba(96, 165, 250, 0.40) !important;
    letter-spacing: 0.3px;
}

.badge { display: inline-block; padding: 5px 14px; font-size: 11.5px; font-weight: 800; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-expired { display: inline-flex; align-items: center; gap: 6px; background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-expiring { display: inline-flex; align-items: center; gap: 6px; background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }

.download-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    background: #2563EB !important;
    color: #FFFFFF !important;
    border: 1px solid #3B82F6 !important;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
    transition: all .25s ease;
}
.download-btn:hover {
    background: #1D4ED8 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(37, 99, 235, 0.50);
    color: #FFFFFF !important;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $doc->document_title }}</h2>
        <p>Property document details.</p>
    </div>
    <div class="header-actions">
        <a href="{{ route('property-documents.edit', $doc) }}" class="btn-primary-custom"><i class="fa fa-edit"></i> Edit</a>
        <a href="{{ route('property-documents.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-file-lines"></i> Document Details</div>
    <div class="detail-grid">
        <div>
            <div class="detail-label">Property</div>
            <div class="detail-value">
                <a href="{{ route('properties.show', $doc->property_id) }}" class="prop-link">
                    {{ $doc->property->property_name ?? '—' }}
                </a>
            </div>
        </div>
        <div>
            <div class="detail-label">Document Type</div>
            <div class="detail-value"><span class="doc-type-chip">{{ $doc->document_type }}</span></div>
        </div>
        <div>
            <div class="detail-label">Document Title</div>
            <div class="detail-value">{{ $doc->document_title }}</div>
        </div>
        <div>
            <div class="detail-label">Document Number</div>
            <div class="detail-value">{{ $doc->document_number ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Expiry Date</div>
            <div class="detail-value">
                @if($doc->expiry_date)
                    @if($doc->isExpired())
                        <span class="badge badge-expired"><i class="fa-solid fa-triangle-exclamation"></i> Expired {{ $doc->expiry_date->format('d M Y') }}</span>
                    @elseif($doc->isExpiringSoon())
                        <span class="badge badge-expiring"><i class="fa-solid fa-clock"></i> {{ $doc->expiry_date->format('d M Y') }} (Expiring soon)</span>
                    @else
                        {{ $doc->expiry_date->format('d M Y') }}
                    @endif
                @else
                    <span style="color: #94A3B8; font-weight: 500;">No expiry</span>
                @endif
            </div>
        </div>
        <div>
            <div class="detail-label">Status</div>
            <div class="detail-value"><span class="badge badge-{{ $doc->status }}">{{ ucfirst($doc->status) }}</span></div>
        </div>
        @if($doc->remarks)
        <div style="grid-column:1/-1">
            <div class="detail-label">Remarks</div>
            <div class="detail-value" style="font-weight: 500; font-size: 14px; color: #E2E8F0;">{{ $doc->remarks }}</div>
        </div>
        @endif
        <div>
            <div class="detail-label">Uploaded By</div>
            <div class="detail-value">{{ $doc->creator->name ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Added On</div>
            <div class="detail-value" style="font-weight: 600; font-size: 14px; color: #E2E8F0;">{{ $doc->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-file-arrow-down"></i> Document File</div>
    @php
        $ext = strtolower(pathinfo($doc->document_file, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg','jpeg','png','webp']);
        $fileUrl = Storage::url($doc->document_file);
    @endphp

    @if($isImage)
        <img src="{{ $fileUrl }}" alt="Document" style="max-width: 100%; max-height: 500px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.15); display: block; margin-bottom: 18px;">
    @else
        <div style="padding: 28px; background: rgba(255, 255, 255, 0.04); border: 1px dashed rgba(255, 255, 255, 0.15); border-radius: 14px; text-align: center; margin-bottom: 18px;">
            <i class="fa-solid fa-file-pdf" style="font-size: 48px; color: #EF4444; margin-bottom: 12px; display: block;"></i>
            <div style="font-size: 14.5px; font-weight: 600; color: #CBD5E1;">PDF Document — click below to view</div>
        </div>
    @endif

    <a href="{{ $fileUrl }}" target="_blank" class="download-btn">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        {{ $isImage ? 'View Full Image' : 'Open / Download PDF' }}
    </a>
</div>
@endsection
