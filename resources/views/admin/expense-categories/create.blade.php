@extends('admin.layouts.app')

@section('title', 'Add Expense Category')
@section('page-title', 'Expense Category Master')

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
    .crud-title p { font-size: 13.5px; color: var(--text-secondary); }
    .card-box {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        box-shadow: var(--soft-shadow);
        max-width: 800px;
        margin: 0 auto;
    }
    .form-group { margin-bottom: 20px; }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    @media (max-width: 576px) {
        .form-row { grid-template-columns: 1fr; gap: 0; }
    }
    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }
    .form-label span { color: #EF4444; }
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
    textarea.form-control { resize: vertical; min-height: 110px; }
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
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        box-shadow: 0 4px 10px rgba(212, 175, 55, 0.2);
        font-family: var(--font-primary);
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
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Expense Category</h2>
        <p>Create and manage categories for business and project expenses.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expense-categories.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="firm_ids">Firms <span>*</span></label>
            <select name="firm_ids[]" id="firm_ids" class="form-control select2-multi @error('firm_ids') is-invalid @enderror" multiple required data-placeholder="Search and select firm(s)...">
                @foreach($firms as $firm)
                    <option value="{{ $firm->id }}" {{ in_array($firm->id, old('firm_ids', [Auth::user()->firm_id])) ? 'selected' : '' }}>
                        {{ $firm->firm_name }}
                    </option>
                @endforeach
            </select>
            @error('firm_ids') <div class="text-error">{{ $message }}</div> @enderror
            @error('firm_ids.*') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="name">Expense Category Name <span>*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="form-control @error('name') is-invalid @enderror" autocomplete="off"
                       placeholder="e.g. Material, Salary, Maintenance, Legal, Marketing, Office Expense...">
                @error('name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="active"   {{ old('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                      placeholder="Describe what type of expenses will be tracked under this category">{{ old('description') }}</textarea>
            @error('description') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Expense Category
            </button>
            <a href="{{ route('expense-categories.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
