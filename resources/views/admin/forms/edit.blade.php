@extends('admin.layouts.app')

@section('title', 'Edit Form')
@section('page-title', 'Form Builder')

@section('content')
<style>
    .crud-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
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

    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 576px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .form-label span {
        color: #EF4444;
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        transition: var(--transition);
        background-color: #FFFFFF;
    }

    .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-light);
    }

    .text-error {
        color: #EF4444;
        font-size: 12.5px;
        margin-top: 6px;
        font-weight: 500;
    }

    .builder-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .builder-table th {
        padding: 12px;
        background: #F9FAFB;
        border-bottom: 2px solid var(--border-color);
        font-size: 12.5px;
        color: var(--text-secondary);
        font-weight: 600;
        text-align: left;
    }

    .builder-table td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .btn-gold {
        background-color: var(--gold);
        color: #FFFFFF;
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
    }

    .btn-gold:hover {
        background-color: #B58D1B;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(212, 175, 55, 0.3);
    }

    .btn-outline {
        border: 1px solid var(--border-color);
        background: transparent;
        color: var(--text-secondary);
        padding: 11px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: var(--transition);
    }

    .btn-outline:hover {
        background: #F9FAFB;
        color: var(--text-primary);
        border-color: #D1D5DB;
    }

    .btn-danger-icon {
        background: none;
        border: none;
        color: #EF4444;
        cursor: pointer;
        font-size: 16px;
        transition: var(--transition);
    }

    .btn-danger-icon:hover {
        color: #B91C1C;
        transform: scale(1.1);
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Dynamic Form</h2>
        <p>Modify form properties and restructure dynamic input fields.</p>
    </div>
</div>

<form method="POST" action="{{ route('forms.update', $form->id) }}" id="form-builder-form">
    @csrf
    @method('PUT')

    <div class="card-box">
        @include('admin.components.firm-select', ['model' => $form])
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="form_name">Form Name <span>*</span></label>
                <input type="text" name="form_name" id="form_name" value="{{ old('form_name', $form- class="@error('form_name') is-invalid @enderror">form_name) }}" class="form-control" placeholder="e.g. Tenant Background Check" required>
                @error('form_name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="form_type">Form Type <span>*</span></label>
                <input type="text" name="form_type" id="form_type" value="{{ old('form_type', $form- class="@error('form_type') is-invalid @enderror">form_type) }}" class="form-control" placeholder="e.g. Verification, Inquiry, Survey" required>
                @error('form_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Add information about the form's purpose">{{ old('description', $form->description) }}</textarea>
                @error('description') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="status">Status <span>*</span></label>
            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active" {{ old('status', $form->status) == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $form->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="text-error">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="card-box" style="overflow-x: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary);">Form Fields Builder</h3>
            <button type="button" class="btn-gold" id="add-field-btn" style="padding: 6px 14px; font-size: 12.5px;">
                <i class="fa-solid fa-plus"></i> Add Field
            </button>
        </div>

        @error('fields') <div class="text-error" style="margin-bottom:15px;">{{ $message }}</div> @enderror

        <table class="builder-table" id="fields-table">
            <thead>
                <tr>
                    <th style="width: 180px;">Label <span>*</span></th>
                    <th style="width: 150px;">Field Name <span>*</span></th>
                    <th style="width: 130px;">Type <span>*</span></th>
                    <th style="width: 90px; text-align: center;">Required</th>
                    <th>Options (comma-separated)</th>
                    <th style="width: 90px;">Sort Order</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 50px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody id="fields-container">
                {{-- Dynamic rows loaded via JS --}}
            </tbody>
        </table>
    </div>

    <div class="form-actions" style="margin-top: 20px; padding: 20px 0; display: flex; gap: 15px; max-width: 800px;">
        <button type="submit" class="btn-gold">
            <i class="fa-solid fa-check"></i> Update Form &amp; Fields
        </button>
        <a href="{{ route('forms.index') }}" class="btn-outline">
            Back to Form Management
        </a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let fieldIndex = 0;

        function addFieldRow(data = null) {
            const container = document.getElementById('fields-container');
            const row = document.createElement('tr');
            row.setAttribute('data-index', fieldIndex);

            const labelVal = data ? data.label : '';
            const nameVal = data ? data.field_name : '';
            const typeVal = data ? data.field_type : 'text';
            const isReq = data ? (data.is_required ? 'checked' : '') : '';
            const optVal = data ? (data.options ? data.options : '') : '';
            const sortVal = data ? data.sort_order : (fieldIndex * 10);
            const statusActive = data ? (data.status === 'active' ? 'selected' : '') : 'selected';
            const statusInactive = data ? (data.status === 'inactive' ? 'selected' : '') : '';

            const isOptionsDisabled = !(typeVal === 'select' || typeVal === 'radio' || typeVal === 'checkbox');

            row.innerHTML = `
                <td>
                    <input type="text" name="fields[${fieldIndex}][label]" value="${labelVal}" class="form-control field-label-input @error('fields') is-invalid @enderror" placeholder="e.g. Phone Number" required style="padding: 7px 10px; font-size: 13px;">
                </td>
                <td>
                    <input type="text" name="fields[${fieldIndex}][field_name]" value="${nameVal}" class="form-control field-name-input @error('fields') is-invalid @enderror" placeholder="e.g. phone_number" required style="padding: 7px 10px; font-size: 13px;">
                </td>
                <td>
                    <select name="fields[${fieldIndex}][field_type]" class="form-control field-type-select @error('fields') is-invalid @enderror" required style="padding: 7px 10px; font-size: 13px;">
                        <option value="text" ${typeVal === 'text' ? 'selected' : ''}>Text</option>
                        <option value="number" ${typeVal === 'number' ? 'selected' : ''}>Number</option>
                        <option value="email" ${typeVal === 'email' ? 'selected' : ''}>Email</option>
                        <option value="date" ${typeVal === 'date' ? 'selected' : ''}>Date</option>
                        <option value="textarea" ${typeVal === 'textarea' ? 'selected' : ''}>Textarea</option>
                        <option value="select" ${typeVal === 'select' ? 'selected' : ''}>Select Dropdown</option>
                        <option value="radio" ${typeVal === 'radio' ? 'selected' : ''}>Radio Option</option>
                        <option value="checkbox" ${typeVal === 'checkbox' ? 'selected' : ''}>Checkbox Option</option>
                        <option value="file" ${typeVal === 'file' ? 'selected' : ''}>File Upload</option>
                    </select>
                </td>
                <td style="text-align: center;">
                    <input type="checkbox" name="fields[${fieldIndex}][is_required]" value="1" ${isReq} style="transform: scale(1.2); cursor: pointer;" class="@error('fields') is-invalid @enderror">
                </td>
                <td>
                    <input type="text" name="fields[${fieldIndex}][options]" value="${optVal}" class="form-control field-options-input @error('fields') is-invalid @enderror" placeholder="Option A, Option B" ${isOptionsDisabled ? 'disabled' : ''} style="padding: 7px 10px; font-size: 13px;">
                </td>
                <td>
                    <input type="number" name="fields[${fieldIndex}][sort_order]" value="${sortVal}" class="form-control @error('fields') is-invalid @enderror" required style="padding: 7px 10px; font-size: 13px;">
                </td>
                <td>
                    <select name="fields[${fieldIndex}][status]" class="form-control @error('fields') is-invalid @enderror" required style="padding: 7px 10px; font-size: 13px;">
                        <option value="active" ${statusActive}>Active</option>
                        <option value="inactive" ${statusInactive}>Inactive</option>
                    </select>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn-danger-icon remove-row-btn"><i class="fa-regular fa-trash-can"></i></button>
                </td>
            `;

            container.appendChild(row);

            // Set up event listeners for the row
            const labelInput = row.querySelector('.field-label-input');
            const nameInput = row.querySelector('.field-name-input');
            const typeSelect = row.querySelector('.field-type-select');
            const optionsInput = row.querySelector('.field-options-input');
            const removeBtn = row.querySelector('.remove-row-btn');

            labelInput.addEventListener('input', function() {
                nameInput.value = labelInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9_]+/g, '_')
                    .replace(/^_+|_+$/g, '');
            });

            typeSelect.addEventListener('change', function() {
                const val = typeSelect.value;
                if (val === 'select' || val === 'radio' || val === 'checkbox') {
                    optionsInput.removeAttribute('disabled');
                    optionsInput.setAttribute('required', 'required');
                    optionsInput.placeholder = 'e.g. Yes, No, Maybe';
                } else {
                    optionsInput.setAttribute('disabled', 'disabled');
                    optionsInput.removeAttribute('required');
                    optionsInput.placeholder = '';
                    optionsInput.value = '';
                }
            });

            removeBtn.addEventListener('click', function() {
                row.remove();
            });

            fieldIndex++;
        }

        document.getElementById('add-field-btn').addEventListener('click', () => addFieldRow());

        // Load existing fields
        const existingFields = {!! json_encode($fields) !!};
        if (existingFields && existingFields.length > 0) {
            existingFields.forEach(field => {
                addFieldRow(field);
            });
        } else {
            addFieldRow();
        }
    });
</script>
@endsection
