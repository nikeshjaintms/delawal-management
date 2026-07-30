@extends('admin.layouts.app')

@section('title', 'Edit Project')
@section('page-title', 'Project Master')

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

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
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

    .current-image-preview {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 10px;
    }

    .current-image-preview img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Project</h2>
        <p>Modify details of the selected project.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('projects.update', $project->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.components.firm-select', ['model' => $project])

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_name">Project Name <span>*</span></label>
                <input type="text" name="project_name" id="project_name" value="{{ old('project_name', $project->project_name) }}" class="form-control @error('project_name') is-invalid @enderror" placeholder="Enter project name" required>
                @error('project_name') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="project_code">Project Code (Unique) <span>*</span></label>
                <input type="text" name="project_code" id="project_code" value="{{ old('project_code', $project->project_code) }}" class="form-control @error('project_code') is-invalid @enderror" placeholder="Enter project code" required>
                @error('project_code') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="project_type">Project Type <span>*</span></label>
                <input type="text" name="project_type" id="project_type" value="{{ old('project_type', $project->project_type) }}" class="form-control @error('project_type') is-invalid @enderror" placeholder="e.g. Residential, Commercial" required>
                @error('project_type') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status <span>*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $project->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="address">Address</label>
            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Enter site address">{{ old('address', $project->address) }}</textarea>
            @error('address') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="city">City</label>
                <input type="text" name="city" id="city" value="{{ old('city', $project->city) }}" class="form-control @error('city') is-invalid @enderror" placeholder="Enter city">
                @error('city') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="state">State</label>
                <input type="text" name="state" id="state" value="{{ old('state', $project->state) }}" class="form-control @error('state') is-invalid @enderror" placeholder="Enter state">
                @error('state') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="country">Country</label>
                <input type="text" name="country" id="country" value="{{ old('country', $project->country) }}" class="form-control @error('country') is-invalid @enderror" placeholder="Enter country">
                @error('country') <div class="text-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="pincode">Pincode</label>
                <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $project->pincode) }}" class="form-control @error('pincode') is-invalid @enderror" placeholder="Enter pincode">
                @error('pincode') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Enter project description">{{ old('description', $project->description) }}</textarea>
            @error('description') <div class="text-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="project_image">Project Image</label>
            <input type="file" name="project_image" id="project_image" class="form-control @error('project_image') is-invalid @enderror" accept="image/*">
            @error('project_image') <div class="text-error">{{ $message }}</div> @enderror
            
            @if($project->project_image)
                <div class="current-image-preview">
                    <img src="{{ asset('storage/' . $project->project_image) }}" alt="Current Image">
                    <div>
                        <span style="font-size: 13px; font-weight: 500; display: block; color: var(--text-primary);">Current Project Image</span>
                        <span style="font-size: 12px; color: var(--text-secondary);">Uploading a new image will replace the current one.</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">Update Project</button>
            <a href="{{ route('projects.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>
@endsection
