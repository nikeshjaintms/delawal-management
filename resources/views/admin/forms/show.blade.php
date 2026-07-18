@extends('admin.layouts.app')

@section('title', $form->form_name)
@section('page-title', 'Form Preview & Submission')

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
        max-width: 700px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
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

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .radio-checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: var(--text-primary);
        cursor: pointer;
    }

    .option-item input {
        width: 17px;
        height: 17px;
        cursor: pointer;
    }

    .text-error {
        color: #EF4444;
        font-size: 12.5px;
        margin-top: 6px;
        font-weight: 500;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
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

    .alert-success {
        background: rgba(34, 197, 94, 0.08);
        border: 1px solid rgba(34, 197, 94, 0.2);
        color: #16803D;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13.5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $form->form_name }}</h2>
        <p>Type: {{ $form->form_type }} | Preview and fill in details below.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-box">
    @if($form->description)
        <p style="margin-bottom: 24px; font-size: 14.5px; color: var(--text-secondary); border-left: 3px solid var(--gold); padding-left: 12px;">
            {{ $form->description }}
        </p>
    @endif

    <form method="POST" action="{{ route('forms.submit', $form->id) }}" enctype="multipart/form-data">
        @csrf

        @foreach($fields as $field)
            <div class="form-group">
                <label class="form-label" for="field_{{ $field->field_name }}">
                    {{ $field->label }}
                    @if($field->is_required) <span>*</span> @endif
                </label>

                {{-- Render Text Input --}}
                @if($field->field_type === 'text')
                    <input type="text" name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="form-control" placeholder="Enter {{ strtolower($field->label) }}" {{ $field->is_required ? 'required' : '' }}>

                {{-- Render Number Input --}}
                @elseif($field->field_type === 'number')
                    <input type="number" name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="form-control" placeholder="Enter number" {{ $field->is_required ? 'required' : '' }}>

                {{-- Render Email Input --}}
                @elseif($field->field_type === 'email')
                    <input type="email" name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="form-control" placeholder="e.g. name@domain.com" {{ $field->is_required ? 'required' : '' }}>

                {{-- Render Date Input --}}
                @elseif($field->field_type === 'date')
                    <input type="date" name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" value="{{ old($field->field_name) }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>

                {{-- Render Textarea Input --}}
                @elseif($field->field_type === 'textarea')
                    <textarea name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" class="form-control" placeholder="Enter details..." {{ $field->is_required ? 'required' : '' }}>{{ old($field->field_name) }}</textarea>

                {{-- Render Select Input --}}
                @elseif($field->field_type === 'select')
                    <select name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                        <option value="">Select Option</option>
                        @foreach(array_map('trim', explode(',', $field->options)) as $option)
                            <option value="{{ $option }}" {{ old($field->field_name) === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>

                {{-- Render Radio Input --}}
                @elseif($field->field_type === 'radio')
                    <div class="radio-checkbox-group">
                        @foreach(array_map('trim', explode(',', $field->options)) as $option)
                            <label class="option-item">
                                <input type="radio" name="{{ $field->field_name }}" value="{{ $option }}" {{ old($field->field_name) === $option ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}>
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>

                {{-- Render Checkbox Input --}}
                @elseif($field->field_type === 'checkbox')
                    <div class="radio-checkbox-group">
                        @foreach(array_map('trim', explode(',', $field->options)) as $option)
                            @php
                                $oldVals = old($field->field_name, []);
                                $checked = is_array($oldVals) && in_array($option, $oldVals) ? 'checked' : '';
                            @endphp
                            <label class="option-item">
                                <input type="checkbox" name="{{ $field->field_name }}[]" value="{{ $option }}" {{ $checked }}>
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>

                {{-- Render File Upload Input --}}
                @elseif($field->field_type === 'file')
                    <input type="file" name="{{ $field->field_name }}" id="field_{{ $field->field_name }}" class="form-control" {{ $field->is_required ? 'required' : '' }}>
                @endif

                @error($field->field_name)
                    <div class="text-error">{{ $message }}</div>
                @enderror
            </div>
        @endforeach

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-paper-plane"></i> Submit Form
            </button>
            <a href="{{ route('forms.index') }}" class="btn-outline">
                Back to Form Management
            </a>
        </div>
    </form>
</div>
@endsection
