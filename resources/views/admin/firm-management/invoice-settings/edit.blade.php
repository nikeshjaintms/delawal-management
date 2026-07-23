@extends('admin.layouts.app')
@section('title','Edit Invoice Settings')
@section('page-title','Firm Management')

@section('content')
<style>
.btn-primary-custom,a.btn-primary-custom,button.btn-primary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:linear-gradient(135deg,#1E5AA8,#2F6FE4);color:#fff !important;font-size:14px;font-weight:600;line-height:1;border:none;border-radius:10px;text-decoration:none !important;box-shadow:0 8px 20px rgba(47,111,228,.25);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-primary-custom:hover{color:#fff !important;text-decoration:none !important;transform:translateY(-2px);box-shadow:0 12px 28px rgba(47,111,228,.35)}
.btn-secondary-custom,a.btn-secondary-custom,button.btn-secondary-custom{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 18px;min-height:42px;background:#fff;color:#1E5AA8 !important;font-size:14px;font-weight:600;line-height:1;border:1px solid rgba(30,90,168,.25);border-radius:10px;text-decoration:none !important;box-shadow:0 6px 16px rgba(30,90,168,.12);transition:all .25s ease;cursor:pointer;font-family:var(--font-primary)}
.btn-secondary-custom:hover{background:#EEF3FA;color:#10233F !important;text-decoration:none !important;transform:translateY(-2px)}
.btn-primary-custom i,.btn-secondary-custom i{font-size:14px;line-height:1}
.form-action-buttons{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color)}
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px}
.form-card{background:var(--card-bg);border:1px solid var(--border-color);border-radius:16px;padding:28px 32px;box-shadow:var(--card-shadow);margin-bottom:24px}
.section-heading{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--blue);margin-bottom:16px;padding-bottom:8px;border-bottom:2px solid var(--blue-light);display:flex;align-items:center;gap:8px}
.form-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
@media(max-width:768px){.form-grid,.form-grid-2{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:7px}
.form-label span{color:#EF4444}
.form-label small{font-weight:400;color:var(--text-secondary);font-size:11px;margin-left:4px}
.form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13.5px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:border-color .18s,box-shadow .18s;background:#fff}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-glow)}
.text-error{color:#EF4444;font-size:12px;margin-top:5px;font-weight:500}
.form-hint{font-size:11.5px;color:var(--text-secondary);margin-top:4px}
.preview-box{background:#F8FAFC;border:1px solid var(--border-color);border-radius:10px;padding:16px 20px;margin-top:20px}
.preview-box h4{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-secondary);margin-bottom:12px}
.preview-pills{display:flex;flex-wrap:wrap;gap:8px}
.preview-pill{background:var(--blue-light);color:var(--blue);font-size:12px;font-weight:700;border-radius:8px;padding:5px 12px;font-family:monospace}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Invoice Settings</h2>
        <p>Update invoice number series configuration.</p>
    </div>
    <a href="{{ route('invoice-settings.index') }}" class="btn-secondary-custom"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<form method="POST" action="{{ route('invoice-settings.update', $invoiceSetting) }}">
@csrf @method('PUT')

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-gear"></i> General Settings</div>
    <div class="form-grid-2">
        <div class="form-group">
            <label class="form-label" for="firm_ids">Firms <span>*</span></label>
            <select name="firm_ids[]" id="firm_ids" class="form-control select2-multi @error('firm_ids') is-invalid @enderror" multiple required data-placeholder="Search and select firm(s)...">
                @foreach($firms as $firm)
                    <option value="{{ $firm->id }}" {{ in_array($firm->id, old('firm_ids', $invoiceSetting->firms->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $firm->firm_name }}
                    </option>
                @endforeach
            </select>
            @error('firm_ids')<div class="text-error">{{ $message }}</div>@enderror
            @error('firm_ids.*')<div class="text-error">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
            <label class="form-label">Financial Year</label>
            <select name="financial_year_id" class="form-control @error('financial_year_id') is-invalid @enderror">
                <option value="">— None —</option>
                @foreach($financialYears as $fy)
                    <option value="{{ $fy->id }}" {{ old('financial_year_id',$invoiceSetting->financial_year_id)==$fy->id ? 'selected':'' }}>
                        {{ $fy->year_name }}{{ $fy->is_active ? ' (Active)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Status <span>*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                <option value="active"   {{ old('status',$invoiceSetting->status)=='active'   ? 'selected':'' }}>Active</option>
                <option value="inactive" {{ old('status',$invoiceSetting->status)=='inactive' ? 'selected':'' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Starting Number <span>*</span></label>
            <input type="number" name="starting_number" value="{{ old('starting_number',$invoiceSetting->starting_number) }}" class="form-control" min="1" required>
        </div>
        <div class="form-group">
            <label class="form-label">Current Number <span>*</span></label>
            <input type="number" name="current_number" value="{{ old('current_number',$invoiceSetting->current_number) }}" class="form-control" min="1" required>
            <div class="form-hint">⚠ Changing this affects the next generated invoice number</div>
        </div>
    </div>
</div>

<div class="form-card">
    <div class="section-heading"><i class="fa-solid fa-hashtag"></i> Invoice Prefixes</div>
    <div class="form-grid">
        @foreach([
            ['sales_prefix','Sales Invoice'],
            ['purchase_prefix','Purchase Invoice'],
            ['booking_prefix','Booking'],
            ['rental_prefix','Rental'],
            ['payment_prefix','Payment'],
            ['receipt_prefix','Receipt'],
            ['expense_prefix','Expense'],
            ['income_prefix','Income'],
            ['loan_prefix','Loan'],
        ] as [$field, $label])
        <div class="form-group">
            <label class="form-label">{{ $label }} <span>*</span> <small>prefix</small></label>
            <input type="text" name="{{ $field }}" value="{{ old($field, $invoiceSetting->$field) }}"
                class="form-control prefix-input" maxlength="10" style="text-transform:uppercase;font-family:monospace;font-weight:700" required>
        </div>
        @endforeach
    </div>
    <div class="preview-box">
        <h4><i class="fa-regular fa-eye" style="margin-right:6px"></i> Live Preview</h4>
        <div class="preview-pills" id="previewPills"></div>
    </div>
</div>

<div class="form-action-buttons">
    <button type="submit" class="btn-primary-custom"><i class="fa fa-save"></i> Update Settings</button>
    <a href="{{ route('invoice-settings.index') }}" class="btn-secondary-custom">Cancel</a>
</div>
</form>

<script>
const prefixFields=['sales_prefix','purchase_prefix','booking_prefix','rental_prefix','payment_prefix','receipt_prefix','expense_prefix','income_prefix','loan_prefix'];
const labels=['Sales','Purchase','Booking','Rental','Payment','Receipt','Expense','Income','Loan'];
function updatePreviews(){
    const pills=document.getElementById('previewPills');
    const yr=new Date().getFullYear();
    const num=document.querySelector('[name=current_number]')?.value||'1';
    const padded=String(num).padStart(4,'0');
    pills.innerHTML=prefixFields.map((f,i)=>{
        const val=(document.querySelector('[name='+f+']')?.value||'').toUpperCase()||labels[i].substring(0,3).toUpperCase();
        return`<span class="preview-pill">${val}-${yr}-${padded}</span>`;
    }).join('');
}
document.querySelectorAll('.prefix-input,[name=current_number]').forEach(el=>el.addEventListener('input',updatePreviews));
updatePreviews();
</script>
@endsection
