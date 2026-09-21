@extends('admin.layouts.app')
@section('title', 'Edit Agriculture Expense')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass Form System ── */
.form-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.form-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.form-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-back {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    transition: all .25s ease;
}
.btn-back:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }

.glass-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 30px;
}

.section-divider {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin: 28px 0 18px 0; display: flex; align-items: center; gap: 10px;
}
.section-divider::after { content: ''; flex: 1; height: 1px; background: rgba(255, 255, 255, 0.10); }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group.full-width { grid-column: 1 / -1; }

.form-label { font-size: 13px; font-weight: 700; color: #CBD5E1 !important; display: flex; align-items: center; gap: 4px; }
.form-label .req { color: #F87171; font-weight: 800; }

.form-control-glass {
    width: 100%; padding: 12px 16px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 12px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .25s ease;
    box-sizing: border-box;
}
.form-control-glass:focus {
    border-color: #3B82F6 !important; background: rgba(16, 22, 34, 0.85) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}
select.form-control-glass option { background: #101622 !important; color: #FFFFFF !important; }

.btn-submit {
    background: #2563EB !important; color: #FFFFFF !important; padding: 14px 32px;
    border-radius: 12px; font-size: 15px; font-weight: 800; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 6px 20px rgba(37, 99, 235, 0.40);
    display: inline-flex; align-items: center; gap: 10px;
}
.btn-submit:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 8px 26px rgba(37, 99, 235, 0.55); }
</style>

<div class="form-header">
    <div class="form-title">
        <h2><i class="fa-solid fa-pen-to-square" style="color: #60A5FA;"></i> Edit Agriculture Expense</h2>
        <p>Update expense record for {{ $expense->farm?->farm_name ?: 'Farm' }} (₹{{ number_format($expense->amount, 2) }}).</p>
    </div>
    <a href="{{ route('agriculture.expenses.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Expenses</a>
</div>

<div class="glass-card">
    <form method="POST" action="{{ route('agriculture.expenses.update', $expense->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="section-divider"><i class="fa-solid fa-building"></i> Company / Multi-Firm Setup</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Firm Ownership <span class="req">*</span></label>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    @php $expFirmIds = $expense->firms->pluck('id')->toArray(); @endphp
                    @foreach($firms as $f)
                        <label style="display: flex; align-items: center; gap: 8px; color: #FFFFFF; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" name="firm_ids[]" value="{{ $f->id }}" 
                                {{ (is_array(old('firm_ids')) && in_array($f->id, old('firm_ids'))) || in_array($f->id, $expFirmIds) ? 'checked' : '' }}
                                style="width: 17px; height: 17px; accent-color: #2563EB;">
                            {{ $f->firm_name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="section-divider"><i class="fa-solid fa-tractor"></i> 1. Farm & Categorization</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Select Farm / Land <span class="req">*</span></label>
                <select name="farm_id" class="form-control-glass" required>
                    <option value="">-- Choose Farm --</option>
                    @foreach($farms as $f)
                        <option value="{{ $f->id }}" {{ old('farm_id', $expense->farm_id) == $f->id ? 'selected' : '' }}>
                            {{ $f->farm_name }} ({{ $f->village ?: 'Direct' }})
                        </option>
                    @endforeach
                </select>
                @error('farm_id')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Expense Date <span class="req">*</span></label>
                <input type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') : '') }}" class="form-control-glass" required>
                @error('expense_date')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Expense Category <span class="req">*</span></label>
                <select name="category" id="catSelect" class="form-control-glass" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c }}" {{ old('category', $expense->category) == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                @error('category')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Expense Type <span class="req">*</span></label>
                <input type="text" name="expense_type" value="{{ old('expense_type', $expense->expense_type) }}" class="form-control-glass" placeholder="e.g. Cotton Hybrid Seeds..." required list="expenseTypesList">
                <datalist id="expenseTypesList">
                    @foreach($expenseTypes as $et)
                        <option value="{{ $et }}"></option>
                    @endforeach
                </datalist>
                @error('expense_type')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-users"></i> 2. Paid To / Entity Connection</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Vendor / Supplier</label>
                <select name="vendor_id" class="form-control-glass">
                    <option value="">-- None / Select Vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ old('vendor_id', $expense->vendor_id) == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Contractor</label>
                <select name="contractor_id" class="form-control-glass">
                    <option value="">-- None / Select Contractor --</option>
                    @foreach($contractors as $c)
                        <option value="{{ $c->id }}" {{ old('contractor_id', $expense->contractor_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Labour Worker</label>
                <select name="labour_id" class="form-control-glass">
                    <option value="">-- None / Select Labour --</option>
                    @foreach($labours as $l)
                        <option value="{{ $l->id }}" {{ old('labour_id', $expense->labour_id) == $l->id ? 'selected' : '' }}>{{ $l->name }} ({{ $l->labour_type }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Connected Project / Property</label>
                <select name="project_id" class="form-control-glass">
                    <option value="">-- None / Select Project --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id', $expense->project_id) == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-indian-rupee-sign"></i> 3. Payment & Billing Details</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Amount (₹) <span class="req">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $expense->amount) }}" class="form-control-glass" required style="font-size:16px;font-weight:800;color:#F87171 !important;">
                @error('amount')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode_id" class="form-control-glass">
                    <option value="">-- Cash / Select Mode --</option>
                    @foreach($paymentModes as $pm)
                        <option value="{{ $pm->id }}" {{ old('payment_mode_id', $expense->payment_mode_id) == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Status <span class="req">*</span></label>
                <select name="payment_status" class="form-control-glass" required>
                    <option value="Paid" {{ old('payment_status', $expense->payment_status) == 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Pending" {{ old('payment_status', $expense->payment_status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Bill No / Receipt No</label>
                <input type="text" name="bill_no" value="{{ old('bill_no', $expense->bill_no) }}" class="form-control-glass">
            </div>

            <div class="form-group">
                <label class="form-label">Invoice No</label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no', $expense->invoice_no) }}" class="form-control-glass">
            </div>

            <div class="form-group">
                <label class="form-label">Receipt / Bill Attachment</label>
                <input type="file" name="attachment" class="form-control-glass" accept="image/*,.pdf">
                @if($expense->attachment)
                    <div style="margin-top:6px;font-size:12px;">
                        Current: <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" style="color:#60A5FA;">View Existing Attachment</a>
                    </div>
                @endif
            </div>

            <div class="form-group full-width">
                <label class="form-label">Expense Description / Purpose</label>
                <input type="text" name="description" value="{{ old('description', $expense->description) }}" class="form-control-glass">
            </div>

            <div class="form-group full-width">
                <label class="form-label">Notes & Remarks</label>
                <textarea name="notes" rows="3" class="form-control-glass">{{ old('notes', $expense->notes) }}</textarea>
            </div>
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 14px;">
            <a href="{{ route('agriculture.expenses.index') }}" class="btn-back">Cancel</a>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-check-circle"></i> Update Expense</button>
        </div>
    </form>
</div>
@endsection
