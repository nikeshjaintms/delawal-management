@extends('admin.layouts.app')

@section('title', 'Properties')
@section('page-title', 'Property Master')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
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
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}

.filter-bar {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px;
    background: rgba(255, 255, 255, 0.04) !important; padding: 14px 18px !important;
    border-radius: 14px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
}
.search-form { display: flex; gap: 10px; flex: 1; max-width: 560px; }
.search-input {
    flex: 1; padding: 10px 16px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
}
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 14px; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; background: rgba(16, 22, 34, 0.70) !important; border-radius: 18px; border: 1px solid rgba(255, 255, 255, 0.10); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 14px 16px; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: .8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap;
}
.premium-table td {
    padding: 14px 16px; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.prop-thumb { width: 48px; height: 40px; border-radius: 6px; object-fit: cover; border: 1px solid rgba(255, 255, 255, 0.15); }
.prop-thumb-placeholder {
    width: 48px; height: 40px; border-radius: 6px; background: rgba(59, 130, 246, 0.15);
    border: 1px solid rgba(96, 165, 250, 0.3); display: flex; align-items: center;
    justify-content: center; color: #60A5FA; font-size: 16px;
}
.badge { display: inline-block; padding: 4px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
    .badge-available { background: rgba(34,197,94,0.1);  color: #16803D; }
    .badge-booked    { background: rgba(234,179,8,0.12);  color: #92710A; }
    .badge-sold      { background: rgba(239,68,68,0.1);   color: #B91C1C; }
    .badge-rented    { background: rgba(59,130,246,0.1);  color: #1D4ED8; }
    .price-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--gold-light);
        color: #92710A;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        border: 1px solid rgba(212,175,55,0.25);
    }
    .action-links { display: flex; gap: 12px; align-items: center; }
    .action-link { color: var(--text-secondary); text-decoration: none; font-size: 13px; transition: var(--transition); display: inline-flex; align-items: center; gap: 4px; }
    .action-link.view:hover { color: #0EA5E9; }
    .action-link.edit:hover { color: var(--gold); }
    .action-link.delete-btn { background: none; border: none; cursor: pointer; color: var(--text-secondary); font-family: var(--font-primary); font-size: 13px; padding: 0; }
    .action-link.delete-btn:hover { color: #EF4444; }
    .alert-success {
        background: rgba(34,197,94,0.08);
        border: 1px solid rgba(34,197,94,0.2);
        color: #16803D;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }

    /* Excel Import Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(4px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .modal-card {
        background: #FFFFFF;
        border-radius: 14px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 920px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: modalFadeIn 0.25s ease-out;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: translateY(-12px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .modal-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: #F8FAFC;
    }
    .modal-title { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 0; }
    .modal-subtitle { font-size: 13px; color: var(--text-secondary); margin-top: 4px; margin-bottom: 0; }
    .modal-close {
        background: transparent;
        border: none;
        font-size: 24px;
        color: var(--text-secondary);
        cursor: pointer;
        line-height: 1;
        padding: 0 4px;
    }
    .modal-close:hover { color: #EF4444; }
    .modal-body { padding: 24px; overflow-y: auto; flex: 1; }
    
    .btn-template-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(212, 175, 55, 0.1);
        color: #92710A;
        border: 1px solid rgba(212, 175, 55, 0.3);
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }
    .btn-template-download:hover { background: var(--gold); color: #FFFFFF; }
    
    .upload-dropzone {
        border: 2px dashed #CBD5E1;
        border-radius: 12px;
        padding: 32px 20px;
        text-align: center;
        background: #F9FAFB;
        transition: var(--transition);
        cursor: pointer;
    }
    .upload-dropzone:hover, .upload-dropzone.dragover { border-color: var(--gold); background: rgba(212,175,55,0.03); }
    .btn-browse {
        background: var(--text-primary);
        color: #FFFFFF;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-browse:hover { background: #1E293B; }
    .badge-valid { background: rgba(34,197,94,0.12); color: #15803D; border: 1px solid rgba(34,197,94,0.25); display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .badge-invalid { background: rgba(239,68,68,0.12); color: #B91C1C; border: 1px solid rgba(239,68,68,0.25); display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
</style>

{{-- Breadcrumb --}}
@php
    $selectedProject = request('project_id') ? \App\Models\Project::with('propertyMaster')->find(request('project_id')) : null;
@endphp

<div style="font-size: 13px; color: var(--text-secondary); margin-bottom: 15px;">
    Property Management &nbsp;&gt;&nbsp; 
    @if($selectedProject && $selectedProject->propertyMaster)
        <a href="{{ route('property-masters.show', $selectedProject->propertyMaster->id) }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">{{ $selectedProject->propertyMaster->property_name }}</a> &nbsp;&gt;&nbsp; 
    @endif
    @if($selectedProject)
        <a href="{{ route('projects.show', $selectedProject->id) }}" style="color: var(--gold); text-decoration: none; font-weight: 600;">{{ $selectedProject->project_name }}</a> &nbsp;&gt;&nbsp; 
    @endif
    <span style="color: var(--text-primary); font-weight: 600;">Bulk Management</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>
            @if($selectedProject)
                Bulk Records - {{ $selectedProject->project_name }}
            @else
                Bulk Management
            @endif
        </h2>
        <p>Manage bulk property entries, Excel import/upload, and unit listings.</p>
    </div>
    <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <button type="button" id="btn-bulk-delete" disabled onclick="openBulkDeleteModal()" style="opacity: 0.5; cursor: not-allowed; min-height: 42px; padding: 0 18px; background: #DC2626; border: none; border-radius: 8px; color: #FFFFFF; font-weight: 600; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px; font-family: var(--font-primary); box-shadow: 0 4px 10px rgba(220,38,38,0.2); transition: var(--transition);">
            <i class="fa-solid fa-trash"></i>
            <span>Bulk Delete (<span id="bulk-delete-count">0</span>)</span>
        </button>
        <button type="button" class="btn-gold" id="btn-open-import-modal" onclick="openImportModal()" style="background-color: #1E293B; box-shadow: 0 4px 10px rgba(30,41,59,0.2);">
            <i class="fa-solid fa-file-import"></i>
            <span>📥 Import Excel</span>
        </button>
        <a href="{{ route('properties.create', request('project_id') ? ['project_id' => request('project_id')] : []) }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i>
            <span>Add Bulk Record</span>
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('properties.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 180px;">
                    <option value="">All Firms</option>
                    @foreach(\App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get() as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="project_id" class="search-input" onchange="this.form.submit()" style="max-width: 180px;">
                <option value="">All Projects</option>
                @foreach($projects as $proj)
                    <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>
                        {{ $proj->project_name }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name, code, location, city..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id') || request('project_id'))
                <a href="{{ route('properties.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">
                        <input type="checkbox" id="select-all-properties" style="width: 16px; height: 16px; cursor: pointer; accent-color: #3B82F6;" title="Select All">
                    </th>
                    <th>No</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Image</th>
                    <th>Code</th>
                    <th>Property Name</th>
                    <th>Project</th>
                    <th>Type</th>
                    <th>City</th>
                    <th>Size</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th style="width: 160px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $key => $property)
                    <tr id="property-row-{{ $property->id }}">
                        <td style="text-align: center;">
                            <input type="checkbox" class="property-select-cb" value="{{ $property->id }}" style="width: 16px; height: 16px; cursor: pointer; accent-color: #3B82F6;">
                        </td>
                        <td>{{ $properties->firstItem() + $key }}</td>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <td><strong>{{ $property->firm->firm_name ?? 'N/A' }}</strong></td>
                        @endif
                        <td>
                            @if($property->main_image)
                                <img src="{{ asset('storage/' . $property->main_image) }}"
                                     alt="{{ $property->property_name }}" class="prop-thumb">
                            @else
                                <div class="prop-thumb-placeholder">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                            @endif
                        </td>
                        <td>{{ $property->property_code ?? '-' }}</td>
                        <td><strong>{{ $property->property_name }}</strong></td>
                        <td>
                            @if($property->project)
                                <a href="{{ route('projects.show', $property->project->id) }}" style="color: var(--gold); font-weight: 600; text-decoration: none;">
                                    {{ $property->project->project_name }}
                                </a>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>{{ $property->propertyType->name ?? '-' }}</td>
                        <td>{{ $property->city ?? '-' }}</td>
                        <td>
                            @if($property->size)
                                {{ $property->size }} {{ $property->size_unit }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($property->price !== null)
                                <span class="price-chip">
                                    ₹{{ number_format($property->price, 2) }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('properties.show', $property->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="{{ route('properties.edit', $property->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('properties.destroy', $property->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Are you sure you want to delete this property?')"
                                        class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ (auth()->user() && auth()->user()->isAdmin()) ? 13 : 12 }}" align="center" style="padding: 30px; color: var(--text-secondary);">
                            No properties found for this firm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $properties->appends(request()->query())->links() }}
    </div>
</div>

<!-- Import Excel Modal -->
<div id="importExcelModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <div>
                <h3 class="modal-title"><i class="fa-solid fa-file-excel" style="color: #10B981; margin-right: 8px;"></i> Import Properties from Excel</h3>
                <p class="modal-subtitle">Upload your property data file (.xlsx, .xls) to bulk import properties into Property Master.</p>
            </div>
            <button type="button" class="modal-close" onclick="closeImportModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <!-- Step 1: Upload Form -->
            <form id="importExcelForm" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; background: #F1F5F9; padding: 12px 16px; border-radius: 8px; flex-wrap: wrap; gap: 10px;">
                    <div style="font-size: 13px; color: var(--text-primary); font-weight: 500;">
                        <i class="fa-solid fa-circle-info" style="color: var(--gold); margin-right: 6px;"></i> Download template with predefined headers & instructions:
                    </div>
                    <a href="{{ route('properties.import.template') }}" class="btn-template-download" target="_blank">
                        <i class="fa-solid fa-download"></i> Download Excel Template
                    </a>
                </div>

                <div class="upload-dropzone" id="dropzone-area">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 32px; color: var(--gold); margin-bottom: 10px;"></i>
                    <p style="font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Choose an Excel file or drag & drop</p>
                    <p style="font-size: 12.5px; color: var(--text-secondary); margin-bottom: 14px;">Supported formats: <strong>.xlsx, .xls, .csv</strong> (Max size: 10MB)</p>
                    <input type="file" name="excel_file" id="excel_file_input" accept=".xlsx,.xls,.csv" required style="display: none;">
                    <button type="button" class="btn-browse" onclick="document.getElementById('excel_file_input').click()">Select Excel File</button>
                    <div id="selected-file-name" style="margin-top: 12px; font-weight: 600; font-size: 13.5px; color: var(--gold); display: none;"></div>
                </div>

                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed var(--border-color);">
                    <label class="form-label" style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">Optional Property Images Archive (ZIP file):</label>
                    <input type="file" name="image_archive" id="image_archive_input" accept=".zip" class="search-input" style="width: 100%; max-width: 100%; box-sizing: border-box;">
                    <div style="font-size: 12px; color: var(--text-secondary); margin-top: 5px;">
                        If your Excel file contains an <strong>Image Filename</strong> column (e.g. <code>plot-001.jpg</code>), upload a ZIP archive containing those image files.
                    </div>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" class="btn-outline" onclick="closeImportModal()">Cancel</button>
                    <button type="submit" class="btn-gold" id="btn-validate-upload">
                        <i class="fa-solid fa-check-circle"></i> Upload & Validate
                    </button>
                </div>
            </form>

            <!-- Loading Spinner -->
            <div id="import-loader" style="display: none; text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size: 38px; color: var(--gold); margin-bottom: 16px;"></i>
                <h4 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px;">Reading and Validating Excel Data...</h4>
                <p style="font-size: 13px; color: var(--text-secondary);">Verifying property codes, duplicate protection, project and type mappings.</p>
            </div>

            <!-- Step 2: Validation Preview -->
            <div id="validation-preview-section" style="display: none;">
                <div id="preview-summary-bar" style="margin-bottom: 16px;"></div>

                <div class="table-container" style="max-height: 320px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 8px;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Row</th>
                                <th>Action</th>
                                <th>Code</th>
                                <th>Property Name</th>
                                <th>Project</th>
                                <th>Property Type</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Validation Status</th>
                            </tr>
                        </thead>
                        <tbody id="preview-table-body">
                        </tbody>
                    </table>
                </div>

                <div id="error-summary-box" style="display: none; margin-top: 16px; background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.25); border-radius: 8px; padding: 14px 18px;">
                    <h5 style="color: #DC2626; font-size: 13.5px; font-weight: 700; margin-top: 0; margin-bottom: 8px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Validation Error Details (<span id="failed-rows-count">0</span> invalid rows)
                    </h5>
                    <ul id="error-list" style="margin: 0; padding-left: 20px; font-size: 12.5px; color: #B91C1C; line-height: 1.6; max-height: 160px; overflow-y: auto;">
                    </ul>
                </div>

                <div style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" class="btn-outline" onclick="resetImportModal()">
                        <i class="fa-solid fa-arrow-left"></i> Re-upload File
                    </button>
                    <button type="button" class="btn-gold" id="btn-final-import" onclick="submitFinalImport()" style="background-color: #16A34A; box-shadow: 0 4px 10px rgba(22,163,74,0.25);">
                        <i class="fa-solid fa-file-import"></i> <span id="import-btn-text">Import Properties</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div id="bulkDeleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-card" style="max-width: 480px;">
        <div class="modal-header" style="background: rgba(239,68,68,0.06); border-bottom-color: rgba(239,68,68,0.2);">
            <div>
                <h3 class="modal-title" style="color: #DC2626;"><i class="fa-solid fa-triangle-exclamation"></i> Delete Selected Properties?</h3>
                <p class="modal-subtitle">You are about to permanently delete <strong id="delete-modal-count">0</strong> properties.</p>
            </div>
            <button type="button" class="modal-close" onclick="closeBulkDeleteModal()">&times;</button>
        </div>
        <div class="modal-body" style="padding: 20px 24px;">
            <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                This action cannot be undone. All selected property records, images, and documents will be permanently removed from the database.
            </p>
        </div>
        <div style="padding: 16px 24px; background: #F8FAFC; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" class="btn-outline" id="btn-cancel-bulk-delete" onclick="closeBulkDeleteModal()">Cancel</button>
            <button type="button" class="btn-delete" id="btn-confirm-bulk-delete" onclick="confirmBulkDelete()" style="background: #DC2626; color: #FFF;">
                <i class="fa-solid fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>

<script>
    let currentBatchId = null;
    let currentValidCount = 0;

    // Bulk Delete Selection Management
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCb = document.getElementById('select-all-properties');
        const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
        const bulkDeleteCount = document.getElementById('bulk-delete-count');

        function updateBulkDeleteState() {
            const rowCbs = document.querySelectorAll('.property-select-cb');
            const checkedCbs = document.querySelectorAll('.property-select-cb:checked');
            const count = checkedCbs.length;

            if (bulkDeleteCount) bulkDeleteCount.innerText = count;

            if (count > 0) {
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.style.opacity = '1';
                bulkDeleteBtn.style.cursor = 'pointer';
            } else {
                bulkDeleteBtn.disabled = true;
                bulkDeleteBtn.style.opacity = '0.5';
                bulkDeleteBtn.style.cursor = 'not-allowed';
            }

            if (selectAllCb) {
                selectAllCb.checked = (rowCbs.length > 0 && checkedCbs.length === rowCbs.length);
            }
        }

        if (selectAllCb) {
            selectAllCb.addEventListener('change', function() {
                const rowCbs = document.querySelectorAll('.property-select-cb');
                rowCbs.forEach(cb => {
                    cb.checked = selectAllCb.checked;
                });
                updateBulkDeleteState();
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('property-select-cb')) {
                updateBulkDeleteState();
            }
        });
    });

    function openBulkDeleteModal() {
        const checkedCbs = document.querySelectorAll('.property-select-cb:checked');
        const count = checkedCbs.length;

        if (count === 0) {
            alert('Please select at least one property to delete.');
            return;
        }

        document.getElementById('delete-modal-count').innerText = count;
        document.getElementById('bulkDeleteModal').style.display = 'flex';
    }

    function closeBulkDeleteModal() {
        document.getElementById('bulkDeleteModal').style.display = 'none';
    }

    function confirmBulkDelete() {
        const checkedCbs = document.querySelectorAll('.property-select-cb:checked');
        const ids = Array.from(checkedCbs).map(cb => parseInt(cb.value)).filter(id => !isNaN(id));

        if (ids.length === 0) {
            alert('Please select at least one property to delete.');
            closeBulkDeleteModal();
            return;
        }

        const confirmBtn = document.getElementById('btn-confirm-bulk-delete');
        const cancelBtn = document.getElementById('btn-cancel-bulk-delete');

        confirmBtn.disabled = true;
        cancelBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Deleting...';

        fetch('{{ route("properties.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Delete';

            if (data.success) {
                closeBulkDeleteModal();
                alert(data.message || `${ids.length} properties deleted successfully.`);
                window.location.reload();
            } else {
                alert(data.message || 'Unable to delete selected properties. No records were deleted.');
            }
        })
        .catch(err => {
            confirmBtn.disabled = false;
            cancelBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fa-solid fa-trash"></i> Delete';
            alert('Unable to delete selected properties. No records were deleted.');
        });
    }

    function openImportModal() {
        resetImportModal();
        document.getElementById('importExcelModal').style.display = 'flex';
    }

    function closeImportModal() {
        document.getElementById('importExcelModal').style.display = 'none';
        resetImportModal();
    }

    function resetImportModal() {
        document.getElementById('importExcelForm').reset();
        document.getElementById('importExcelForm').style.display = 'block';
        document.getElementById('import-loader').style.display = 'none';
        document.getElementById('validation-preview-section').style.display = 'none';
        document.getElementById('selected-file-name').style.display = 'none';
        document.getElementById('selected-file-name').innerText = '';
        currentBatchId = null;
        currentValidCount = 0;
    }

    document.getElementById('excel_file_input').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const fileName = e.target.files[0].name;
            const fileSize = (e.target.files[0].size / (1024 * 1024)).toFixed(2);
            document.getElementById('selected-file-name').innerText = `📄 Selected File: ${fileName} (${fileSize} MB)`;
            document.getElementById('selected-file-name').style.display = 'block';
        }
    });

    // Handle Excel File Dropzone
    const dropzone = document.getElementById('dropzone-area');
    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.add('dragover'); }, false);
    });
    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.remove('dragover'); }, false);
    });
    dropzone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            document.getElementById('excel_file_input').files = files;
            const fileName = files[0].name;
            const fileSize = (files[0].size / (1024 * 1024)).toFixed(2);
            document.getElementById('selected-file-name').innerText = `📄 Selected File: ${fileName} (${fileSize} MB)`;
            document.getElementById('selected-file-name').style.display = 'block';
        }
    });

    // Step 1: Upload & Validate Form Submit
    document.getElementById('importExcelForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('excel_file_input');
        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Please select an Excel file to upload.');
            return;
        }

        const formData = new FormData(this);

        document.getElementById('importExcelForm').style.display = 'none';
        document.getElementById('import-loader').style.display = 'block';

        fetch('{{ route("properties.import.validate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('import-loader').style.display = 'none';
            if (!data.success) {
                alert(data.message || 'Validation failed. Please check your Excel file.');
                document.getElementById('importExcelForm').style.display = 'block';
                return;
            }

            currentBatchId = data.batch_id;
            currentValidCount = data.valid_count;

            renderPreviewResults(data);
        })
        .catch(err => {
            document.getElementById('import-loader').style.display = 'none';
            document.getElementById('importExcelForm').style.display = 'block';
            alert('An error occurred while uploading and parsing the file. Please ensure it is a valid Excel document.');
        });
    });

    function renderPreviewResults(data) {
        const summaryBar = document.getElementById('preview-summary-bar');
        const tbody = document.getElementById('preview-table-body');
        const errorBox = document.getElementById('error-summary-box');
        const errorList = document.getElementById('error-list');
        const importBtn = document.getElementById('btn-final-import');
        const importBtnText = document.getElementById('import-btn-text');

        const newCount = data.new_count || 0;
        const updateCount = data.update_count || 0;
        const invalidCount = data.invalid_count || 0;

        // Summary Bar styling
        if (invalidCount === 0) {
            summaryBar.innerHTML = `
                <div style="background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: #15803D; padding: 12px 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 18px;"></i>
                    <span><strong>${data.total_rows} Properties Found:</strong> ${newCount} New / ${updateCount} Updates / 0 Errors</span>
                </div>
            `;
            errorBox.style.display = 'none';
        } else {
            summaryBar.innerHTML = `
                <div style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3); color: #92710A; padding: 12px 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 18px;"></i>
                    <span><strong>${data.total_rows} Properties Found:</strong> ${newCount} New / ${updateCount} Updates / <strong style="color: #B91C1C;">${invalidCount} Errors</strong>. Invalid rows will be excluded.</span>
                </div>
            `;
            
            // Build error list
            errorList.innerHTML = '';
            let failedCount = 0;
            data.preview.forEach(row => {
                if (!row.is_valid && row.errors && row.errors.length > 0) {
                    failedCount++;
                    row.errors.forEach(err => {
                        const li = document.createElement('li');
                        li.innerHTML = `<strong>Row ${row.row}</strong> (${row.property_code || 'N/A'}): ${err}`;
                        errorList.appendChild(li);
                    });
                }
            });
            document.getElementById('failed-rows-count').innerText = failedCount;
            errorBox.style.display = 'block';
        }

        // Render Rows
        tbody.innerHTML = '';
        data.preview.forEach(row => {
            const tr = document.createElement('tr');
            if (!row.is_valid) {
                tr.style.backgroundColor = 'rgba(239,68,68,0.04)';
            }

            const actionBadge = row.action === 'update'
                ? '<span class="badge-update" style="background: rgba(59,130,246,0.12); color: #1D4ED8; border: 1px solid rgba(59,130,246,0.3); display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-rotate"></i> UPDATE</span>'
                : '<span class="badge-new" style="background: rgba(34,197,94,0.12); color: #15803D; border: 1px solid rgba(34,197,94,0.3); display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;"><i class="fa-solid fa-plus"></i> NEW</span>';

            tr.innerHTML = `
                <td>${row.row}</td>
                <td>${actionBadge}</td>
                <td><strong>${row.property_code || '-'}</strong></td>
                <td>${row.property_name || '-'}</td>
                <td>${row.project_name || '-'}</td>
                <td>${row.property_type_name || '-'}</td>
                <td>${row.city || '-'}</td>
                <td><span class="badge badge-${row.status}">${row.status.toUpperCase()}</span></td>
                <td>
                    ${row.is_valid 
                        ? '<span class="badge-valid"><i class="fa-solid fa-check"></i> Valid</span>' 
                        : `<span class="badge-invalid" title="${row.errors.join('; ')}"><i class="fa-solid fa-xmark"></i> Invalid (${row.errors.length})</span>`}
                </td>
            `;
            tbody.appendChild(tr);
        });

        importBtnText.innerText = `Import ${data.valid_count} ${data.valid_count === 1 ? 'Property' : 'Properties'}`;
        if (data.valid_count === 0) {
            importBtn.disabled = true;
            importBtn.style.opacity = '0.5';
            importBtn.style.cursor = 'not-allowed';
        } else {
            importBtn.disabled = false;
            importBtn.style.opacity = '1';
            importBtn.style.cursor = 'pointer';
        }

        document.getElementById('validation-preview-section').style.display = 'block';
    }

    function submitFinalImport() {
        if (!currentBatchId || currentValidCount === 0) return;

        const importBtn = document.getElementById('btn-final-import');
        importBtn.disabled = true;
        importBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Importing...';

        fetch('{{ route("properties.import.process") }}', {
            method: 'POST',
            body: JSON.stringify({ batch_id: currentBatchId }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message || `${currentValidCount} properties imported successfully.`);
                window.location.reload();
            } else {
                alert(data.message || 'Import failed. Please try again.');
                importBtn.disabled = false;
                importBtn.innerHTML = `<i class="fa-solid fa-file-import"></i> Import ${currentValidCount} Properties`;
            }
        })
        .catch(err => {
            alert('A database error occurred during import. Transaction rolled back safely.');
            importBtn.disabled = false;
            importBtn.innerHTML = `<i class="fa-solid fa-file-import"></i> Import ${currentValidCount} Properties`;
        });
    }
</script>
@endsection

