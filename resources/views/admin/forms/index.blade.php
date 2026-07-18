@extends('admin.layouts.app')

@section('title', 'Form Management')
@section('page-title', 'Form Management')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .crud-title h2 {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    .crud-title p {
        font-size: 13.5px;
        color: var(--text-secondary);
    }
    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }
    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--soft-shadow);
    }
    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    .search-form {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 500px;
    }
    .search-input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
    }
    .search-input:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
    }
    .btn-search {
        background-color: var(--text-primary);
        color: #FFFFFF;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-search:hover { background-color: #1E293B; }
    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }
    .btn-reset:hover { color: var(--text-primary); }
    .table-container { width: 100%; overflow-x: auto; }
    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .premium-table th {
        padding: 14px 16px;
        background: #F9FAFB;
        color: var(--text-secondary);
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .premium-table td {
        padding: 16px;
        border-bottom: 1px solid #F1F5F9;
        color: var(--text-primary);
        vertical-align: middle;
    }
    .premium-table tr:last-child td { border-bottom: none; }
    .premium-table tbody tr:hover { background-color: #F9FAFB; }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }
    .badge-active { background: rgba(34,197,94,0.1); color: #16803D; }
    .badge-inactive { background: rgba(239,68,68,0.1); color: #B91C1C; }
    .action-links {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .action-link {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13px;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .action-link:hover { color: var(--text-primary); }
    .action-link.view:hover { color: #0EA5E9; }
    .action-link.edit:hover { color: #2563EB; }
    .action-link.submissions:hover { color: #8B5CF6; }
    .action-link.preview:hover { color: #F59E0B; }
    .action-link.delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        font-family: var(--font-primary);
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 0;
    }
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
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }

    /* ── Modal ── */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 1000;
        align-items: flex-start;
        justify-content: center;
        backdrop-filter: blur(2px);
        overflow-y: auto;
        padding: 30px 15px;
    }
    .modal-backdrop.active { display: flex; }
    .modal-box {
        background: var(--card-bg);
        border-radius: 14px;
        padding: 32px;
        width: 100%;
        max-width: 960px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.22);
        position: relative;
        animation: modalIn 0.22s cubic-bezier(0.4,0,0.2,1) both;
        margin: auto;
    }
    @keyframes modalIn {
        from { opacity:0; transform: scale(0.94) translateY(10px); }
        to   { opacity:1; transform: scale(1) translateY(0); }
    }
    .modal-close {
        position: absolute;
        top: 16px; right: 18px;
        background: none;
        border: none;
        font-size: 20px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: color 0.18s;
    }
    .modal-close:hover { color: #EF4444; }
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
    }
    .modal-subtitle {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 24px;
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    @media(max-width:576px) { .form-row { grid-template-columns: 1fr; gap: 0; } }
    .form-group { margin-bottom: 18px; }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 7px;
    }
    .form-label span { color: #EF4444; }
    .m-form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }
    .m-form-control:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-glow);
    }
    .text-error { color: #EF4444; font-size: 12px; margin-top: 5px; font-weight: 500; }
    .builder-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .builder-table th {
        padding: 10px 12px;
        background: #F9FAFB;
        border-bottom: 2px solid var(--border-color);
        font-size: 11.5px;
        color: var(--text-secondary);
        font-weight: 600;
        text-align: left;
    }
    .builder-table td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    .btn-add-field {
        background: var(--blue-light);
        color: var(--blue);
        border: 1px solid var(--blue);
        padding: 6px 14px;
        border-radius: 7px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }
    .btn-add-field:hover { background: var(--blue); color: #fff; }
    .btn-danger-icon {
        background: none;
        border: none;
        color: #EF4444;
        cursor: pointer;
        font-size: 15px;
        transition: var(--transition);
        padding: 0;
    }
    .btn-danger-icon:hover { color: #B91C1C; }
    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    .btn-cancel {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 10px 22px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-cancel:hover { background: #F9FAFB; color: var(--text-primary); border-color: #D1D5DB; }
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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, type..." class="search-input @error('search') is-invalid @enderror">
            <button type="submit" class="btn-search">Search</button>
            @if(request('search'))
                <a href="{{ route('forms.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Form Name</th>
                    <th>Form Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th style="width: 310px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($forms as $key => $form)
                    <tr>
                        <td>{{ $forms->firstItem() + $key }}</td>
                        <td><strong>{{ $form->form_name }}</strong></td>
                        <td>{{ $form->form_type }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($form->description, 60) ?: '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $form->status }}">
                                {{ ucfirst($form->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="table-action-buttons">
                                <a href="{{ route('forms.show', $form->id) }}" class="action-link preview">
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
                                        <button type="submit" class="action-link" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;display:inline-flex;align-items:center;gap:4px;padding:0;" title="Click to set Inactive">
                                            <i class="fa-solid fa-toggle-on" style="color:#16803D;"></i> Active
                                        </button>
                                    @else
                                        <button type="submit" class="action-link" style="background:none;border:none;cursor:pointer;color:var(--text-secondary);font-family:var(--font-primary);font-size:13px;display:inline-flex;align-items:center;gap:4px;padding:0;" title="Click to set Active">
                                            <i class="fa-solid fa-toggle-off" style="color:#94A3B8;"></i> Inactive
                                        </button>
                                    @endif
                                </form>
                                <a href="{{ route('form-submissions.index') }}?form_id={{ $form->id }}" class="action-link submissions">
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
                        <td colspan="6" align="center" style="padding: 30px; color: var(--text-secondary);">No forms found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $forms->appends(request()->query())->links() }}
    </div>
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
            <div style="overflow-x: auto; margin-top: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary);">Form Fields Builder</h3>
                    <button type="button" class="btn-add-field" id="mf-add-field-btn">
                        <i class="fa-solid fa-plus"></i> Add Field
                    </button>
                </div>

                @error('fields') <div class="text-error" style="margin-bottom:12px;">{{ $message }}</div> @enderror

                <table class="builder-table" id="mf-fields-table">
                    <thead>
                        <tr>
                            <th style="min-width:160px;">Label <span style="color:#EF4444;">*</span></th>
                            <th style="min-width:140px;">Field Name <span style="color:#EF4444;">*</span></th>
                            <th style="min-width:130px;">Type <span style="color:#EF4444;">*</span></th>
                            <th style="width:80px; text-align:center;">Required</th>
                            <th style="min-width:150px;">Options (comma-sep.)</th>
                            <th style="width:80px;">Sort</th>
                            <th style="width:100px;">Status</th>
                            <th style="width:45px; text-align:center;">Del</th>
                        </tr>
                    </thead>
                    <tbody id="mf-fields-container"></tbody>
                </table>
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
            <td><input type="text" name="${flds}[label]" value="${labelVal}" class="m-form-control mf-label-input @error('${') is-invalid @enderror" placeholder="e.g. Full Name" required style="padding:7px 10px;font-size:13px;min-width:130px;"></td>
            <td><input type="text" name="${flds}[field_name]" value="${nameVal}" class="m-form-control mf-name-input @error('${') is-invalid @enderror" placeholder="e.g. full_name" required style="padding:7px 10px;font-size:13px;min-width:120px;"></td>
            <td>
                <select name="${flds}[field_type]" class="m-form-control mf-type-select @error('${') is-invalid @enderror" required style="padding:7px 10px;font-size:13px;">
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
            <td style="text-align:center;"><input type="checkbox" name="${flds}[is_required]" value="1" ${isReq} style="transform:scale(1.2);cursor:pointer;" class="@error('${') is-invalid @enderror"></td>
            <td><input type="text" name="${flds}[options]" value="${optVal}" class="m-form-control mf-options-input @error('${') is-invalid @enderror" placeholder="A, B, C" ${optDisabled?'disabled':''} style="padding:7px 10px;font-size:13px;min-width:130px;"></td>
            <td><input type="number" name="${flds}[sort_order]" value="${sortVal}" class="m-form-control @error('${') is-invalid @enderror" required style="padding:7px 10px;font-size:13px;width:65px;"></td>
            <td>
                <select name="${flds}[status]" class="m-form-control @error('${') is-invalid @enderror" required style="padding:7px 10px;font-size:13px;">
                    <option value="active" ${stActive}>Active</option>
                    <option value="inactive" ${stInact}>Inactive</option>
                </select>
            </td>
            <td style="text-align:center;"><button type="button" class="btn-danger-icon mf-remove-btn"><i class="fa fa-trash"></i></button></td>
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

