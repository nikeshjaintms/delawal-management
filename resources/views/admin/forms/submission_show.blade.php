@extends('admin.layouts.app')

@section('title', 'Submission Detail')
@section('page-title', 'Form Management')

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
        max-width: 800px;
        margin: 0 auto;
    }

    .detail-grid {
        display: flex;
        flex-direction: column;
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-item {
        border-bottom: 1px solid #F1F5F9;
        padding-bottom: 15px;
    }

    .detail-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.5;
    }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
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
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Submission details</h2>
        <p>Submitted Form: {{ $submission->form->form_name ?? 'Deleted Form' }} | Received: {{ $submission->created_at ? $submission->created_at->format('d M Y, h:i A') : '-' }}</p>
    </div>
</div>

<div class="card-box">
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $submission->firm->firm_name ?? 'Not set' }}</div>
        </div>
        @php
            $submitted = $submission->submitted_data ?? [];
            // Map fields by field_name for quick label lookups
            $fieldsMap = $submission->form && $submission->form->fields 
                ? $submission->form->fields->pluck('label', 'field_name')->toArray() 
                : [];
        @endphp

        @forelse($submitted as $key => $val)
            <div class="detail-item">
                <div class="detail-label">{{ $fieldsMap[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}</div>
                <div class="detail-value">
                    @if(is_array($val) && isset($val['type']) && $val['type'] === 'file')
                        <a href="{{ asset('storage/' . $val['value']) }}" target="_blank" style="color: var(--gold); text-decoration: underline; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-file-arrow-down"></i>
                            <span>{{ $val['original_name'] }}</span>
                        </a>
                    @elseif(is_array($val))
                        {{ implode(', ', $val) }}
                    @elseif($val === null || $val === '')
                        <span style="color: var(--text-secondary); font-weight: 400; font-style: italic;">(Empty)</span>
                    @else
                        {!! nl2br(e($val)) !!}
                    @endif
                </div>
            </div>
        @empty
            <p style="color: var(--text-secondary);">No data was submitted.</p>
        @endforelse
    </div>

    <div class="form-actions">
        <form action="{{ route('form-submissions.destroy', $submission->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this submission?')" class="btn-gold" style="background-color: #EF4444; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);">
                <i class="fa-regular fa-trash-can"></i> Delete Submission
            </button>
        </form>
        <a href="{{ route('form-submissions.index') }}" class="btn-outline">
            Back to List
        </a>
    </div>
</div>
@endsection
