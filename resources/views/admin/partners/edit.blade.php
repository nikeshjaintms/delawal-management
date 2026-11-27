@extends('admin.layouts.app')
@section('title', 'Edit Partner - ' . $partner->name)
@section('page-title', 'Edit Partner')

@section('content')
<style>
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.crud-title h2 { font-size: 24px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 4px; }
.crud-title p { font-size: 13.5px; color: #CBD5E1 !important; margin: 0; }
.form-card {
    background: rgba(20, 27, 41, 0.65) !important; backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.12) !important; border-radius: 20px !important;
    padding: 28px 32px !important; max-width: 900px;
}
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
@media(max-width:768px) { .form-grid { grid-template-columns: 1fr; } }
.form-label { display: block; font-size: 13px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 7px; }
.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.70) !important;
    border: 1.5px solid rgba(255,255,255,0.15) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none;
}
.form-control option { background: #111827 !important; color: #FFFFFF !important; }
.silent-box {
    display: flex; align-items: center; gap: 10px; padding: 12px 14px;
    background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.30); border-radius: 10px;
}
.btn-primary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; min-height: 42px;
    background: #2563EB !important; color: #FFFFFF !important; font-size: 14px; font-weight: 700;
    border: 1px solid #3B82F6 !important; border-radius: 10px; cursor: pointer; text-decoration: none !important;
}
.btn-secondary-custom {
    display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; min-height: 42px;
    background: #1E293B !important; color: #FFFFFF !important; font-size: 14px; font-weight: 700;
    border: 1px solid #475569 !important; border-radius: 10px; text-decoration: none !important;
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Partner: {{ $partner->name }}</h2>
        <p>Update partner details and silent/on-paper status.</p>
    </div>
    <a href="{{ route('partners.show', $partner->id) }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('partners.update', $partner->id) }}">
    @csrf @method('PUT')
    <div class="form-card">
        <div class="form-grid">
            <div>
                <label class="form-label">Partner Name *</label>
                <input type="text" name="name" value="{{ old('name', $partner->name) }}" class="form-control" required>
            </div>
            <div>
                <label class="form-label">Default Firm (Optional)</label>
                <select name="firm_id" class="form-control">
                    <option value="">-- Any / Multi-Firm --</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" {{ old('firm_id', $partner->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile', $partner->mobile) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $partner->email) }}" class="form-control">
            </div>
            <div>
                <label class="form-label">Partner Type</label>
                <label class="silent-box" style="cursor: pointer;">
                    <input type="hidden" name="is_silent" value="0">
                    <input type="checkbox" name="is_silent" value="1" {{ old('is_silent', $partner->is_silent ? '1' : '0') == '1' ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <div>
                        <div style="font-weight: 700; color: #E2E8F0; font-size: 13.5px;">Silent Partner (Not on Paper)</div>
                        <div style="font-size: 11.5px; color: #94A3B8;">Hidden from Firm logins — visible only to Admin.</div>
                    </div>
                </label>
            </div>
            <div>
                <label class="form-label">Status *</label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $partner->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $partner->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">Private Notes</label>
                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $partner->notes) }}</textarea>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.10);">
            <button type="submit" class="btn-primary-custom"><i class="fa-solid fa-save"></i> Update Partner</button>
            <a href="{{ route('partners.show', $partner->id) }}" class="btn-secondary-custom">Cancel</a>
        </div>
    </div>
</form>
@endsection
