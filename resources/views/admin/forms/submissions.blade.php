@extends('admin.layouts.app')

@section('title', 'Form Submissions')
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

    .search-select {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 13.5px;
        font-family: var(--font-primary);
        color: var(--text-primary);
        outline: none;
        background-color: #FFFFFF;
        transition: var(--transition);
    }

    .search-select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px var(--gold-light);
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

    .btn-search:hover {
        background-color: #1E293B;
    }

    .btn-reset {
        padding: 10px 14px;
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-reset:hover {
        color: var(--text-primary);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

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

    .premium-table tr:last-child td {
        border-bottom: none;
    }

    .premium-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    .action-links {
        display: flex;
        gap: 12px;
        align-items: center;
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

    .action-link:hover {
        color: var(--text-primary);
    }

    .action-link.view:hover {
        color: #0EA5E9;
    }

    .action-link.delete-btn {
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        font-family: var(--font-primary);
    }

    .action-link.delete-btn:hover {
        color: #EF4444;
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

    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Form Submissions</h2>
        <p>Review submitted dynamic form data and upload packages.</p>
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
        <form method="GET" action="{{ route('form-submissions.index') }}" class="search-form">
            <select name="form_id" class="search-select @error('form_id') is-invalid @enderror">
                <option value="">Filter by Form Type</option>
                @foreach($forms as $form)
                    <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>{{ $form->form_name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-search">Filter</button>
            @if(request('form_id'))
                <a href="{{ route('form-submissions.index') }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container">
        <table class="premium-table">
            <thead>
                <tr>
                    <th style="width: 80px;">No</th>
                    <th>Form Name</th>
                    <th>Form Type</th>
                    <th>Submission Date &amp; Time</th>
                    <th style="width: 180px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $key => $sub)
                    <tr>
                        <td>{{ $submissions->firstItem() + $key }}</td>
                        <td><strong>{{ $sub->form->form_name ?? 'Deleted Form' }}</strong></td>
                        <td>{{ $sub->form->form_type ?? '-' }}</td>
                        <td>{{ $sub->created_at ? $sub->created_at->format('d M Y, h:i A') : '-' }}</td>
                        <td>
                            <div class="action-links">
                                <a href="{{ route('form-submissions.show', $sub->id) }}" class="action-link view">
                                    <i class="fa-regular fa-eye"></i> View Details
                                </a>
                                <form action="{{ route('form-submissions.destroy', $sub->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this submission?')" class="action-link delete-btn">
                                        <i class="fa-regular fa-trash-can"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" align="center" style="padding: 30px; color: var(--text-secondary);">No submissions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        {{ $submissions->appends(request()->query())->links() }}
    </div>
</div>
@endsection
