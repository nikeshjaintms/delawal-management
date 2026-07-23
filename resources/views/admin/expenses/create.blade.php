@extends('admin.layouts.app')
@section('title', 'Add Expense')
@section('page-title', 'Expense Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:900px;margin:0 auto;}
    .section-title{font-size:13px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .form-section{margin-bottom:28px;}
    .form-group{margin-bottom:20px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
    @media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    .form-label .opt{color:var(--text-secondary);font-weight:400;font-size:12px;}
    .form-control{width:100%;padding:10px 14px;border:1px solid var(--border-color);border-radius:8px;font-size:14px;font-family:var(--font-primary);color:var(--text-primary);outline:none;transition:var(--transition);background:#FFF;}
    .form-control:focus{border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-light);}
    textarea.form-control{resize:vertical;min-height:90px;}
    .file-upload-box{border:2px dashed var(--border-color);border-radius:10px;padding:20px;text-align:center;transition:var(--transition);cursor:pointer;background:#FAFAFA;}
    .file-upload-box:hover{border-color:var(--gold);background:var(--gold-light);}
    .file-upload-box input[type="file"]{display:none;}
    .file-upload-label{display:flex;flex-direction:column;align-items:center;gap:8px;cursor:pointer;}
    .file-upload-label i{font-size:24px;color:var(--gold);}
    .file-upload-label .upload-text{font-size:13.5px;font-weight:600;color:var(--text-primary);}
    .file-upload-label .upload-hint{font-size:12px;color:var(--text-secondary);}
    #file-name-display{margin-top:8px;font-size:12.5px;color:var(--text-secondary);}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .form-hint{font-size:12px;color:var(--text-secondary);margin-top:5px;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Expense</h2>
        <p>Record a new expense entry under your active firm.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Section 1: Expense Info --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-info"></i> Expense Information</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="expense_title">Expense Title <span>*</span></label>
                    <input type="text" name="expense_title" id="expense_title"
                           value="{{ old('expense_title') }}" class="form-control @error('expense_title') is-invalid @enderror"
                           placeholder="e.g. Site Maintenance Work, Office Supplies">
                    @error('expense_title')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="expense_date">Expense Date <span>*</span></label>
                    <input type="date" name="expense_date" id="expense_date"
                           value="{{ old('expense_date', date('Y-m-d')) }}" class="form-control @error('expense_date') is-invalid @enderror">
                    @error('expense_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="expense_category_id">Expense Category <span class="opt">(optional)</span></label>
                    <select name="expense_category_id" id="expense_category_id" class="form-control @error('expense_category_id') is-invalid @enderror">
                        <option value="">— Select Category —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
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
                            <option value="{{ $prop->id }}" {{ old('property_id') == $prop->id ? 'selected' : '' }}>
                                {{ $prop->property_name }}{{ $prop->property_code ? ' ('.$prop->property_code.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
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
                           value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror" placeholder="0.00">
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach(\App\Models\PaymentMode::whereHas('firms', function($q) { $q->where('firms.id', Auth::user()->firm_id); })->where('status', 'active')->orderBy('name')->get() as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode') == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="paid_to">Paid To</label>
                    <input type="text" name="paid_to" id="paid_to" value="{{ old('paid_to') }}"
                           class="form-control @error('paid_to') is-invalid @enderror" placeholder="Vendor / person name">
                    @error('paid_to')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No</label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no') }}"
                           class="form-control @error('bill_no') is-invalid @enderror" placeholder="Enter bill or invoice number">
                    @error('bill_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="approval_status">Approval Status <span>*</span></label>
                    <select name="approval_status" id="approval_status" class="form-control @error('approval_status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('approval_status', 'Pending') == $s ? 'selected' : '' }}>{{ $s }}</option>
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
                <div class="file-upload-box" onclick="document.getElementById('bill_file').click()">
                    <label class="file-upload-label" for="bill_file">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="upload-text">Click to upload bill or receipt</span>
                        <span class="upload-hint">PDF, JPG, JPEG, PNG — max 5 MB</span>
                    </label>
                    <input type="file" name="bill_file" id="bill_file" accept=".pdf,.jpg,.jpeg,.png"
                           onchange="showFileName(this)" class="@error('bill_file') is-invalid @enderror">
                    <div id="file-name-display">No file selected</div>
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
                          placeholder="Add any notes or details about this expense...">{{ old('remarks') }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Expense
            </button>
            <a href="{{ route('expenses.index') }}" class="btn-outline">
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
        display.textContent = 'No file selected';
        display.style.color = 'var(--text-secondary)';
    }
}
</script>
@endsection
