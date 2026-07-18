@extends('admin.layouts.app')
@section('title', $doc->document_title . ' — Document Details')
@section('page-title','Property Documents')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.header-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.detail-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.detail-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
@media(max-width:768px){.detail-grid{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.detail-grid{grid-template-columns:1fr}}
.detail-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:5px}
.detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary)}
.badge{display:inline-block;padding:4px 10px;font-size:11px;font-weight:600;border-radius:20px;text-transform:uppercase}
.badge-active{background:rgba(16,185,129,.1);color:#059669}
.badge-inactive{background:rgba(239,68,68,.1);color:#DC2626}
.badge-expired{background:rgba(239,68,68,.12);color:#B91C1C}
.badge-expiring{background:rgba(245,158,11,.12);color:#B45309}
.doc-type-chip{display:inline-block;background:rgba(59,130,246,.08);color:#1D4ED8;font-size:12px;font-weight:600;border-radius:6px;padding:4px 12px;border:1px solid rgba(59,130,246,.15)}
.download-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none !important;box-shadow:0 6px 18px rgba(30,90,168,.25);transition:all .25s ease}
.download-btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(30,90,168,.35);color:#fff !important}
.prop-link{color:var(--blue) !important;text-decoration:none;font-weight:600}
.prop-link:hover{text-decoration:underline}
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
                    <span style="color:var(--text-muted);font-weight:400">No expiry</span>
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
            <div class="detail-value" style="font-weight:400;font-size:14px">{{ $doc->remarks }}</div>
        </div>
        @endif
        <div>
            <div class="detail-label">Uploaded By</div>
            <div class="detail-value">{{ $doc->creator->name ?? '—' }}</div>
        </div>
        <div>
            <div class="detail-label">Added On</div>
            <div class="detail-value" style="font-weight:400;font-size:14px">{{ $doc->created_at->format('d M Y, h:i A') }}</div>
        </div>
    </div>
</div>

<div class="detail-card">
    <div class="section-heading"><i class="fa-solid fa-file-arrow-down"></i> Document File</div>
    @php
        $ext = strtolower(pathinfo($doc->document_file, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg','jpeg','png']);
        $fileUrl = Storage::url($doc->document_file);
    @endphp

    @if($isImage)
        <img src="{{ $fileUrl }}" alt="Document" style="max-width:100%;max-height:500px;border-radius:10px;border:1px solid var(--border-color);display:block;margin-bottom:16px">
    @else
        <div style="padding:24px;background:#F8FAFC;border:1px dashed var(--border-color);border-radius:12px;text-align:center;margin-bottom:16px">
            <i class="fa-solid fa-file-pdf" style="font-size:48px;color:#DC2626;margin-bottom:12px;display:block"></i>
            <div style="font-size:14px;color:var(--text-secondary)">PDF Document — click below to view</div>
        </div>
    @endif

    <a href="{{ $fileUrl }}" target="_blank" class="download-btn">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        {{ $isImage ? 'View Full Image' : 'Open / Download PDF' }}
    </a>
</div>
@endsection
