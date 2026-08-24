@extends('admin.layouts.app')
@section('title', 'Edit Expense')
@section('page-title', 'Expense Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 28px;
    max-width: 900px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
.form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.form-control-readonly { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; border: 1px solid rgba(255, 255, 255, 0.10) !important; cursor: default; }
textarea.form-control { resize: vertical; min-height: 90px; }

/* File Upload Glass Dropzone */
.file-upload-box {
    border: 2px dashed rgba(255, 255, 255, 0.20) !important;
    border-radius: 16px !important; padding: 28px !important; text-align: center;
    transition: all .25s ease; cursor: pointer;
    background: rgba(16, 22, 34, 0.50) !important;
}
.file-upload-box:hover {
    border-color: #3B82F6 !important;
    background: rgba(37, 99, 235, 0.12) !important;
}
.file-upload-box input[type="file"] { display: none; }
.file-upload-label { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; }
.file-upload-label i { font-size: 28px; color: #60A5FA !important; }
.file-upload-label .upload-text { font-size: 14px; font-weight: 700; color: #FFFFFF !important; }
.file-upload-label .upload-hint { font-size: 12px; color: #94A3B8 !important; }

.current-file {
    display: flex; align-items: center; gap: 12px;
    background: rgba(59, 130, 246, 0.12) !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 12px !important; padding: 12px 16px !important; margin-bottom: 12px;
}
.current-file i { color: #60A5FA !important; font-size: 20px; }
.current-file a { color: #60A5FA !important; font-size: 13.5px; font-weight: 700; text-decoration: none !important; }
.current-file a:hover { text-decoration: underline !important; color: #93C5FD !important; }

#file-name-display { margin-top: 8px; font-size: 13px; color: #94A3B8 !important; }
.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }

/* Select2 Glass Styling Overrides */
.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    background-color: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important; min-height: 42px !important; padding: 4px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(37, 99, 235, 0.35) !important;
    border: 1px solid rgba(59, 130, 246, 0.50) !important;
    color: #FFFFFF !important; border-radius: 6px !important; font-weight: 600; padding: 3px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #F87171 !important; margin-right: 6px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #FFFFFF !important; line-height: 32px !important;
}
.select2-dropdown { background-color: #101622 !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }
.select2-results__option { color: #CBD5E1 !important; }
.select2-results__option--highlighted[aria-selected] { background-color: #2563EB !important; color: #FFFFFF !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); font-family: inherit;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Expense</h2>
        <p>Update expense — <strong>{{ $expense->expense_title }}</strong></p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expenses.update', $expense->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Section 1: Expense Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Expense Information</div>
            @include('admin.components.firm-select', ['model' => $expense])
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="expense_title">Expense Title <span>*</span></label>
                    <input type="text" name="expense_title" id="expense_title"
                           value="{{ old('expense_title', $expense->expense_title) }}"
                           class="form-control" placeholder="e.g. Site Maintenance Work">
                    @error('expense_title')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="expense_date">Expense Date <span>*</span></label>
                    <input type="date" name="expense_date" id="expense_date"
                           value="{{ old('expense_date', \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d')) }}"
                           class="form-control">
                    @error('expense_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div> 
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="expense_category_id">Expense Category <span class="opt">(optional)</span></label>
                    <select name="expense_category_id" id="expense_category_id" class="form-control @error('expense_category_id') is-invalid @enderror">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_id">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— General / Not property-specific —</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}"
                                data-project="{{ $prop->project->project_name ?? ($prop->project->propertyMaster->property_name ?? 'No Project Assigned') }}"
                                {{ old('property_id', $expense->property_id) == $prop->id ? 'selected' : '' }}>
                                {{ $prop->property_name }}{{ $prop->property_code ? ' ('.$prop->property_code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_display">Project</label>
                    <input type="text" id="project_display" class="form-control form-control-readonly" readonly placeholder="Auto-determined">
                </div>
            </div>
        </div>

        {{-- Section 2: Amount & Payment --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount & Payment</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="amount" id="amount"
                           value="{{ old('amount', $expense->amount) }}"
                           class="form-control" placeholder="0.00">
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()->firm_id); })->where('status', 'active')->orderBy('name')->get() as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode', $expense->payment_mode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="paid_to">Paid To</label>
                    <input type="text" name="paid_to" id="paid_to"
                           value="{{ old('paid_to', $expense->paid_to) }}"
                           class="form-control" placeholder="Vendor / person name">
                    @error('paid_to')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No</label>
                    <input type="text" name="bill_no" id="bill_no"
                           value="{{ old('bill_no', $expense->bill_no) }}"
                           class="form-control" placeholder="Enter bill or invoice number">
                    @error('bill_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="approval_status">Approval Status <span>*</span></label>
                    <select name="approval_status" id="approval_status" class="form-control @error('approval_status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}"
                                {{ old('approval_status', $expense->approval_status ?? 'Pending') == $s ? 'selected' : '' }}>
                                {{ $s }}
                            </option>
                        @endforeach
                    </select>
                    @error('approval_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Bill Upload --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-paperclip"></i> Bill / Receipt Upload</div>
            <div class="form-group">
                @if($expense->bill_file)
                    <div class="current-file">
                        <i class="fa-solid fa-file-lines"></i>
                        <div>
                            <div style="font-size:12px;color:#94A3B8;margin-bottom:2px;">Current file:</div>
                            <a href="{{ asset('storage/'.$expense->bill_file) }}" target="_blank">
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i>
                                View Current Bill
                            </a>
                        </div>
                    </div>
                    <div style="font-size:12px;color:#CBD5E1;margin:8px 0 10px;">Upload a new file below to replace the existing one.</div>
                @endif
                <div class="file-upload-box" onclick="document.getElementById('bill_file').click()">
                    <label class="file-upload-label">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="upload-text">{{ $expense->bill_file ? 'Replace bill file' : 'Click to upload bill or receipt' }}</span>
                        <span class="upload-hint">PDF, JPG, JPEG, PNG — max 5 MB</span>
                    </label>
                    <input type="file" name="bill_file" id="bill_file" accept=".pdf,.jpg,.jpeg,.png"
                           onchange="showFileName(this)" class="@error('bill_file') is-invalid @enderror">
                    <div id="file-name-display">No new file selected</div>
                </div>
                @error('bill_file')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Section 4: Remarks --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Additional Notes</div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any notes...">{{ old('remarks', $expense->remarks) }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-floppy-disk"></i> Update Expense
            </button>
            <a href="{{ route('expenses.show', $expense->id) }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </form>
</div>

<script>
function showFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.textContent = '📎 ' + input.files[0].name;
        display.style.color = 'var(--gold)';
        display.style.fontWeight = '600';
    } else {
        display.textContent = 'No new file selected';
        display.style.color = 'var(--text-secondary)';
    }
}

function updateProjectMapping() {
    const select = document.getElementById('property_id');
    if (!select) return;
    const selectedOption = select.options[select.selectedIndex];
    const projectDisplay = document.getElementById('project_display');
    if (projectDisplay) {
        if (!select.value || !selectedOption) {
            projectDisplay.value = 'Auto-determined';
        } else {
            const projName = selectedOption.getAttribute('data-project');
            projectDisplay.value = projName || 'No Project Assigned';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const propSelect = document.getElementById('property_id');
    if (propSelect) {
        propSelect.addEventListener('change', updateProjectMapping);
        if (window.jQuery) {
            jQuery('#property_id').on('change select2:select select2:unselect', updateProjectMapping);
        }
        updateProjectMapping();
    }
});
</script>
@endsection
