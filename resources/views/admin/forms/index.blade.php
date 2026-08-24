@extends('admin.layouts.app')

@section('title', 'Form Management')
@section('page-title', 'Form Management')

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 22px;
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
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
}

.filter-bar {
    display: flex !important; gap: 12px !important; align-items: center !important; margin-bottom: 24px !important;
    background: rgba(255, 255, 255, 0.04) !important; padding: 16px 20px !important;
    border-radius: 16px !important; border: 1px solid rgba(255, 255, 255, 0.10) !important;
    width: 100% !important; flex-wrap: nowrap !important; overflow-x: auto !important;
}

.search-form { display: flex !important; gap: 12px !important; flex: 1 !important; width: 100% !important; align-items: center !important; flex-wrap: nowrap !important; }

.search-input {
    padding: 10px 14px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important; flex: 1 !important;
}
select.search-input option { background: #101622 !important; color: #FFFFFF !important; }
.search-input::placeholder { color: #94A3B8 !important; }
.search-input:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.btn-search {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px !important;
    border-radius: 10px; border: 1px solid #3B82F6 !important; font-size: 13.5px; font-weight: 700;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    flex-shrink: 0 !important; white-space: nowrap !important;
}
.btn-search:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50); }

.btn-reset { color: #CBD5E1 !important; text-decoration: none; font-size: 13.5px; font-weight: 600; padding: 10px 12px; flex-shrink: 0 !important; white-space: nowrap !important; transition: color .2s ease; }
.btn-reset:hover { color: #FFFFFF !important; }

.table-container { width: 100%; overflow-x: auto; border-radius: 16px; border: 1px solid rgba(255, 255, 255, 0.10); }

.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px; }
.premium-table th {
    padding: 16px 18px !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.9px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 16px 18px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13.5px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table td strong { color: #FFFFFF !important; font-weight: 700 !important; }
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.05) !important; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-active { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-inactive { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.action-buttons-wrap { display: flex; gap: 8px; align-items: center; white-space: nowrap !important; }

.btn-submission-chip {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px;
    background: rgba(139, 92, 246, 0.15) !important; color: #C4B5FD !important;
    border: 1px solid rgba(139, 92, 246, 0.30) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 600; text-decoration: none !important; transition: all .2s ease;
}
.btn-submission-chip:hover { background: #8B5CF6 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-toggle-status {
    display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px;
    background: rgba(255, 255, 255, 0.08) !important; color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important; border-radius: 8px;
    font-size: 12.5px; font-weight: 600; cursor: pointer; transition: all .2s ease;
}
.btn-toggle-status:hover { background: rgba(255, 255, 255, 0.15) !important; transform: translateY(-1px); }

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }

/* ── Modal Dark Glass System ── */
.modal-backdrop {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75);
    z-index: 1000; align-items: flex-start; justify-content: center;
    backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
    overflow-y: auto; padding: 30px 15px;
}
.modal-backdrop.active { display: flex; }
.modal-box {
    background: rgba(20, 27, 41, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 24px !important; padding: 32px !important;
    width: 95% !important; max-width: 1080px !important; box-shadow: 0 20px 50px rgba(0,0,0,0.6);
    position: relative; animation: modalIn 0.22s cubic-bezier(0.4,0,0.2,1) both; margin: auto;
}
@keyframes modalIn {
    from { opacity:0; transform: scale(0.94) translateY(10px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
.modal-close {
    position: absolute; top: 20px; right: 22px; background: none; border: none;
    font-size: 22px; color: #94A3B8; cursor: pointer; transition: color 0.18s;
}
.modal-close:hover { color: #F87171; }
.modal-title { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; }
.modal-subtitle { font-size: 13.5px; color: #CBD5E1 !important; margin-bottom: 24px; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:576px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
.form-group { margin-bottom: 18px; }
.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-label span { color: #F87171 !important; }

.m-form-control {
    width: 100% !important; padding: 10px 14px !important;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important; font-size: 13.5px !important;
    color: #FFFFFF !important; outline: none; transition: all .2s ease; box-sizing: border-box !important;
}
select.m-form-control option { background: #101622 !important; color: #FFFFFF !important; }
.m-form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }

.text-error { color: #F87171; font-size: 12px; margin-top: 5px; font-weight: 600; }
.builder-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.builder-table th {
    padding: 12px; background: rgba(255, 255, 255, 0.05) !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    font-size: 11px; color: #94A3B8 !important; font-weight: 800; text-transform: uppercase; text-align: left;
}
.builder-table td { padding: 10px 12px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); vertical-align: middle; }

.btn-add-field {
    background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important; padding: 7px 16px;
    border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: all .2s ease;
}
.btn-add-field:hover { background: #2563EB !important; color: #FFFFFF !important; transform: translateY(-1px); }

.btn-danger-icon {
    width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 8px;
    cursor: pointer; transition: all .2s ease;
}
.btn-danger-icon:hover { background: #DC2626 !important; color: #FFFFFF !important; }

.modal-actions {
    display: flex; gap: 12px; margin-top: 24px; padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.10); justify-content: flex-end;
}
.btn-cancel {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-cancel:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-1px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Form Management</h2>
        <p>Build dynamic questionnaires, property intake forms, and surveys.</p>
    </div>
    <button type="button" class="btn-gold" id="openAddFormModal">
        <i class="fa-solid fa-plus"></i>
        <span>Add Form</span>
    </button>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    <div class="filter-bar">
        <form method="GET" action="{{ route('forms.index') }}" class="search-form">
            @if(auth()->user() && auth()->user()->isAdmin())
                <select name="firm_id" class="search-input" onchange="this.form.submit()" style="max-width: 200px;">
                    <option value="">All Firms</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ request('firm_id') == $firm->id ? 'selected' : '' }}>
                            {{ $firm->firm_name }}
                        </option>
                    @endforeach
                </select>
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, type, firm..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search') || request('firm_id'))
                <a href="{{ route('forms.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    @if(auth()->user() && auth()->user()->isAdmin())
                        <th>Firm</th>
                    @endif
                    <th>Form Name</th>
                    <th>Form Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="width: 380px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $key => $form)
                    <tr>
                        <td>{{ method_exists($forms, 'firstItem') ? ($forms->firstItem() + $key) : ($key + 1) }}</td>
                        @if(auth()->user() && auth()->user()->isAdmin())
                            <td><strong style="color: #FFFFFF !important;">{{ $form->firm->firm_name ?? '-' }}</strong></td>
                        @endif
                        <td><strong>{{ $form->form_name }}</strong></td>
                        <td>{{ $form->form_type }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($form->description, 60) ?: '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $form->status }}">
                                {{ ucfirst($form->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons-wrap">
                                <a href="{{ route('forms.show', $form->id) }}" class="btn-view">
                                    <i class="fa fa-eye"></i> Preview
                                </a>
                                <a href="{{ route('forms.edit', $form->id) }}" class="btn-edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                {{-- Status Toggle --}}
                                <form action="{{ route('forms.toggle-status', $form->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    @if($form->status === 'active')
                                        <button type="submit" class="btn-toggle-status" title="Click to set Inactive">
                                            <i class="fa-solid fa-toggle-on" style="color:#34D399;"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="btn-toggle-status" title="Click to set Active">
                                            <i class="fa-solid fa-toggle-off" style="color:#94A3B8;"></i> Inactive
                                        </button>
                                    @endif
                                </form>
                                <a href="{{ route('form-submissions.index') }}?form_id={{ $form->id }}" class="btn-submission-chip">
                                    <i class="fa-solid fa-inbox"></i> Submissions
                                </a>
                                <form action="{{ route('forms.destroy', $form->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this form and all its fields/submissions?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" align="center" style="padding: 30px; color: #CBD5E1;">No forms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($forms, 'links'))
        <div class="pagination-wrapper">
            {{ $forms->appends(request()->query())->links() }}
        </div>
    @endif
</div>

{{-- ── Add Form Modal ── --}}
<div class="modal-backdrop {{ $errors->any() && old('_modal') === 'add_form' ? 'active' : '' }}" id="addFormModal">
    <div class="modal-box">
        <button type="button" class="modal-close" id="closeAddFormModal"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-title">Create New Form</div>
        <div class="modal-subtitle">Design a new dynamic form with custom inputs and validation logic.</div>

        <form method="POST" action="{{ route('forms.store') }}" id="modal-form-builder">
            @csrf
            <input type="hidden" name="_modal" value="add_form" class="@error('_modal') is-invalid @enderror">
            @include('admin.components.firm-select')

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="mf_form_name">Form Name <span>*</span></label>
                    <input type="text" name="form_name" id="mf_form_name" value="{{ old('form_name') }}" class="m-form-control @error('form_name') is-invalid @enderror" placeholder="e.g. Tenant Background Check" required>
                    @error('form_name') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="mf_form_type">Form Type <span>*</span></label>
                    <input type="text" name="form_type" id="mf_form_type" value="{{ old('form_type') }}" class="m-form-control @error('form_type') is-invalid @enderror" placeholder="e.g. Verification, Inquiry, Survey" required>
                    @error('form_type') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="mf_description">Description</label>
                <textarea name="description" id="mf_description" class="m-form-control @error('description') is-invalid @enderror" rows="2" placeholder="Add information about the form's purpose">{{ old('description') }}</textarea>
                @error('description') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group" style="max-width: 300px;">
                <label class="form-label" for="mf_status">Status <span>*</span></label>
                <select name="status" id="mf_status" class="m-form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            {{-- Fields Builder --}}
            <div style="margin-top: 14px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="font-size: 15px; font-weight: 800; color: #FFFFFF !important; margin: 0;">Form Fields Builder</h3>
                    <button type="button" class="btn-add-field" id="mf-add-field-btn">
                        <i class="fa-solid fa-plus"></i> Add Field
                    </button>
                </div>

                @error('fields') <div class="text-error" style="margin-bottom:12px;">{{ $message }}</div> @enderror

                <div style="width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.12); background: rgba(16, 22, 34, 0.60);">
                    <table class="builder-table" id="mf-fields-table" style="width: 100%; min-width: 860px; margin: 0;">
                        <thead>
                            <tr>
                                <th style="width: 180px; padding: 12px 14px;">Label <span style="color:#EF4444;">*</span></th>
                                <th style="width: 160px; padding: 12px 14px;">Field Name <span style="color:#EF4444;">*</span></th>
                                <th style="width: 130px; padding: 12px 14px;">Type <span style="color:#EF4444;">*</span></th>
                                <th style="width: 75px; text-align:center; padding: 12px 8px;">Required</th>
                                <th style="min-width: 150px; padding: 12px 14px;">Options (comma-sep.)</th>
                                <th style="width: 85px; padding: 12px 10px;">Sort</th>
                                <th style="width: 110px; padding: 12px 10px;">Status</th>
                                <th style="width: 50px; text-align:center; padding: 12px 8px;">Del</th>
                            </tr>
                        </thead>
                        <tbody id="mf-fields-container"></tbody>
                    </table>
                </div>
            </div>

            <div class="modal-actions">
                <button type="submit" class="btn-gold">
                    <i class="fa-solid fa-check"></i> Save &amp; Build Form
                </button>
                <button type="button" class="btn-cancel" id="cancelAddFormModal">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    // ── Modal open/close ──
    const addFormModal  = document.getElementById('addFormModal');
    const openBtn       = document.getElementById('openAddFormModal');
    const closeBtn      = document.getElementById('closeAddFormModal');
    const cancelBtn     = document.getElementById('cancelAddFormModal');

    openBtn.addEventListener('click', () => addFormModal.classList.add('active'));
    closeBtn.addEventListener('click', () => addFormModal.classList.remove('active'));
    cancelBtn.addEventListener('click', () => addFormModal.classList.remove('active'));
    addFormModal.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('active');
    });

    // ── Form field builder inside modal ──
    let fieldIndex = 0;

    function addFieldRow(data) {
        const container = document.getElementById('mf-fields-container');
        const row = document.createElement('tr');
        row.setAttribute('data-index', fieldIndex);

        const labelVal  = data ? data.label : '';
        const nameVal   = data ? data.field_name : '';
        const typeVal   = data ? data.field_type : 'text';
        const isReq     = data ? (data.is_required ? 'checked' : '') : '';
        const optVal    = data ? (data.options || '') : '';
        const sortVal   = data ? data.sort_order : (fieldIndex * 10);
        const stActive  = data ? (data.status === 'active' ? 'selected' : '') : 'selected';
        const stInact   = data ? (data.status === 'inactive' ? 'selected' : '') : '';
        const optDisabled = !(typeVal === 'select' || typeVal === 'radio' || typeVal === 'checkbox');

        const flds = `fields[${fieldIndex}]`;

        row.innerHTML = `
            <td style="padding: 8px 10px;"><input type="text" name="${flds}[label]" value="${labelVal}" class="m-form-control mf-label-input" placeholder="e.g. Full Name" required style="padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;"></td>
            <td style="padding: 8px 10px;"><input type="text" name="${flds}[field_name]" value="${nameVal}" class="m-form-control mf-name-input" placeholder="e.g. full_name" required style="padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;"></td>
            <td style="padding: 8px 10px;">
                <select name="${flds}[field_type]" class="m-form-control mf-type-select" required style="padding:8px 10px;font-size:13px;width:100%;box-sizing:border-box;">
                    <option value="text" ${typeVal==='text'?'selected':''}>Text</option>
                    <option value="number" ${typeVal==='number'?'selected':''}>Number</option>
                    <option value="email" ${typeVal==='email'?'selected':''}>Email</option>
                    <option value="date" ${typeVal==='date'?'selected':''}>Date</option>
                    <option value="textarea" ${typeVal==='textarea'?'selected':''}>Textarea</option>
                    <option value="select" ${typeVal==='select'?'selected':''}>Select</option>
                    <option value="radio" ${typeVal==='radio'?'selected':''}>Radio</option>
                    <option value="checkbox" ${typeVal==='checkbox'?'selected':''}>Checkbox</option>
                    <option value="file" ${typeVal==='file'?'selected':''}>File Upload</option>
                </select>
            </td>
            <td style="text-align:center; padding: 8px 6px;"><input type="checkbox" name="${flds}[is_required]" value="1" ${isReq} style="width:18px;height:18px;cursor:pointer;accent-color:#2563EB;"></td>
            <td style="padding: 8px 10px;"><input type="text" name="${flds}[options]" value="${optVal}" class="m-form-control mf-options-input" placeholder="A, B, C" ${optDisabled?'disabled':''} style="padding:8px 12px;font-size:13px;width:100%;box-sizing:border-box;"></td>
            <td style="padding: 8px 8px;"><input type="number" name="${flds}[sort_order]" value="${sortVal}" class="m-form-control" required style="padding:8px 10px;font-size:13px;width:100%;min-width:60px;box-sizing:border-box;"></td>
            <td style="padding: 8px 8px;">
                <select name="${flds}[status]" class="m-form-control" required style="padding:8px 10px;font-size:13px;width:100%;min-width:90px;box-sizing:border-box;">
                    <option value="active" ${stActive}>Active</option>
                    <option value="inactive" ${stInact}>Inactive</option>
                </select>
            </td>
            <td style="text-align:center; padding: 8px 6px;"><button type="button" class="btn-danger-icon mf-remove-btn" title="Delete Field"><i class="fa fa-trash"></i></button></td>
        `;

        container.appendChild(row);

        const labelInput   = row.querySelector('.mf-label-input');
        const nameInput    = row.querySelector('.mf-name-input');
        const typeSelect   = row.querySelector('.mf-type-select');
        const optionsInput = row.querySelector('.mf-options-input');
        const removeBtn    = row.querySelector('.mf-remove-btn');

        labelInput.addEventListener('input', function() {
            nameInput.value = labelInput.value
                .toLowerCase()
                .replace(/[^a-z0-9_]+/g, '_')
                .replace(/^_+|_+$/g, '');
        });

        typeSelect.addEventListener('change', function() {
            const v = typeSelect.value;
            if (v === 'select' || v === 'radio' || v === 'checkbox') {
                optionsInput.removeAttribute('disabled');
                optionsInput.setAttribute('required', 'required');
                optionsInput.placeholder = 'e.g. Yes, No, Maybe';
            } else {
                optionsInput.setAttribute('disabled', 'disabled');
                optionsInput.removeAttribute('required');
                optionsInput.placeholder = 'A, B, C';
                optionsInput.value = '';
            }
        });

        removeBtn.addEventListener('click', () => row.remove());
        fieldIndex++;
    }

    document.getElementById('mf-add-field-btn').addEventListener('click', () => addFieldRow(null));

    const formBuilder = document.getElementById('modal-form-builder');
    formBuilder.addEventListener('submit', function(e) {
        e.preventDefault();
        
        document.querySelectorAll('.text-error-ajax').forEach(el => el.remove());
        document.querySelectorAll('.m-form-control.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        const formData = new FormData(formBuilder);
        
        fetch(formBuilder.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status === 200) {
                addFormModal.classList.remove('active');
                window.location.reload();
            } else if (status === 422) {
                if (body.errors) {
                    for (const [field, messages] of Object.entries(body.errors)) {
                        let inputEl = formBuilder.querySelector(`[name="${field}"]`);
                        if (!inputEl) {
                            const parts = field.split('.');
                            if (parts.length > 1) {
                                let nameAttr = parts[0];
                                for (let i = 1; i < parts.length; i++) {
                                    nameAttr += `[${parts[i]}]`;
                                }
                                inputEl = formBuilder.querySelector(`[name="${nameAttr}"]`);
                            }
                        }
                        
                        if (inputEl) {
                            inputEl.classList.add('is-invalid');
                            const errDiv = document.createElement('div');
                            errDiv.className = 'text-error text-error-ajax';
                            errDiv.innerText = messages[0];
                            inputEl.parentNode.appendChild(errDiv);
                        }
                    }
                }
            } else {
                alert(body.message || 'Server error occurred.');
            }
        })
        .catch(err => {
            console.error('Submission error:', err);
            alert('An unexpected error occurred during submission.');
        });
    });

    // Add initial row on page load
    addFieldRow(null);
})();
</script>
@endsection

