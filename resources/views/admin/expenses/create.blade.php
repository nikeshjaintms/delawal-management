@extends('admin.layouts.app')
@section('title', 'Add Expense')
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
    max-width: 950px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 28px; }
.form-group { margin-bottom: 18px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span.req { color: #F87171 !important; }
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
textarea.form-control { resize: vertical; min-height: 80px; }

/* File Upload Glass Dropzone */
.file-upload-box {
    border: 2px dashed rgba(255, 255, 255, 0.20) !important;
    border-radius: 16px !important; padding: 24px !important; text-align: center;
    transition: all .25s ease; cursor: pointer;
    background: rgba(16, 22, 34, 0.50) !important;
}
.file-upload-box:hover {
    border-color: #3B82F6 !important;
    background: rgba(37, 99, 235, 0.12) !important;
}
.file-upload-box input[type="file"] { display: none; }
.file-upload-label { display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; margin: 0; }
.file-upload-label i { font-size: 28px; color: #60A5FA !important; }
.file-upload-label .upload-text { font-size: 14px; font-weight: 700; color: #FFFFFF !important; }
.file-upload-label .upload-hint { font-size: 12px; color: #94A3B8 !important; }
#file-name-display { margin-top: 8px; font-size: 13px; color: #94A3B8 !important; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

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


.btn-quick-add-cat {
    background: rgba(37, 99, 235, 0.20) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.50) !important;
    color: #60A5FA !important; padding: 10px 16px; border-radius: 10px;
    font-size: 13.5px; font-weight: 700; cursor: pointer; display: inline-flex;
    align-items: center; gap: 6px; white-space: nowrap; transition: all .2s ease;
    font-family: inherit; flex-shrink: 0;
}
.btn-quick-add-cat:hover {
    background: #2563EB !important; color: #FFFFFF !important;
    border-color: #3B82F6 !important; transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.40);
}

/* Category Quick Modal */
.cat-modal-backdrop {
    position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
    z-index: 99999; display: flex; align-items: center; justify-content: center;
    padding: 20px; box-sizing: border-box;
}
.cat-modal-box {
    background: #141B29 !important;
    border: 1px solid rgba(255, 255, 255, 0.16) !important;
    border-radius: 20px !important;
    width: 100%; max-width: 460px;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60);
    overflow: hidden; animation: catModalSlideIn .25s ease-out;
}
@keyframes catModalSlideIn {
    from { transform: translateY(20px) scale(0.96); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}
.cat-modal-header {
    padding: 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: space-between; align-items: center;
}
.cat-modal-icon {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(37, 99, 235, 0.20); color: #60A5FA;
    display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
}
.cat-modal-close {
    background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15);
    color: #94A3B8; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: all .2s;
}
.cat-modal-close:hover { background: rgba(239, 68, 68, 0.20); color: #F87171; border-color: rgba(239, 68, 68, 0.40); }
.cat-modal-body { padding: 24px; }
.cat-modal-footer {
    padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10);
    display: flex; justify-content: flex-end; gap: 10px; background: rgba(0, 0, 0, 0.20);
}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Expense</h2>
        <p>Record a new expense voucher with hierarchy and payment details.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expense-form">
        @csrf

        {{-- Section 1: Classification & Hierarchy --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-sitemap"></i> 1. Expense Classification &amp; Hierarchy (Firm &rarr; Project &rarr; Property)</div>
            
            {{-- Firm selector --}}
            @include('admin.components.firm-select')

            <div class="form-row">
                {{-- Expense Date --}}
                <div class="form-group">
                    <label class="form-label" for="expense_date">Expense Date <span class="req">*</span></label>
                    <input type="date" name="expense_date" id="expense_date"
                           value="{{ old('expense_date', date('Y-m-d')) }}"
                           class="form-control @error('expense_date') is-invalid @enderror" required>
                    @error('expense_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Expense Category with Dark Dropdown & Quick Add Button --}}
                <div class="form-group">
                    <label class="form-label" for="expense_category">Expense Category <span class="req">*</span></label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select name="expense_category" id="expense_category" class="form-control @error('expense_category') is-invalid @enderror" onchange="handleCategoryDropdownChange(this)" required>
                            <option value="">— Select Category —</option>
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ old('expense_category') == $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="__new__" style="color: #60A5FA; font-weight: 700; background: #1E293B;">➕ + Add New Category...</option>
                        </select>
                        <button type="button" class="btn-quick-add-cat" onclick="openCategoryModal()" title="Add New Category">
                            <i class="fa-solid fa-plus"></i> <span>Add</span>
                        </button>
                    </div>
                    @error('expense_category')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                {{-- Project --}}
                <div class="form-group">
                    <label class="form-label" for="project_id">Project <span class="opt">(Optional - Project-wise Expense / Filter)</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">— All Projects / General —</option>
                        @if(isset($projects))
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}"
                                        data-firm-id="{{ $proj->firm_id }}"
                                        {{ old('project_id', $selectedProjectId ?? '') == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->project_name }} {{ $proj->propertyMaster ? '('.$proj->propertyMaster->property_name.')' : '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('project_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Property / Unit Multi-Select --}}
                <div class="form-group">
                    <label class="form-label" for="property_ids">Property / Unit <span class="opt">(Optional - Multi-Select Enabled)</span></label>
                    @php
                        $selectedPropIds = (array) old('property_ids', request('property_ids') ?: (request('property_id') ? [request('property_id')] : (old('property_id') ? [old('property_id')] : ($selectedPropertyIds ?? ($selectedPropertyId ? [$selectedPropertyId] : [])))));
                    @endphp
                    <select name="property_ids[]" id="property_ids" class="form-control select2-multi @error('property_ids') is-invalid @enderror" multiple data-placeholder="Search and select property / unit(s)...">
                        @if(isset($properties))
                            @foreach($properties as $prop)
                                <option value="{{ $prop->id }}"
                                        data-project-id="{{ $prop->project_id }}"
                                        data-firm-id="{{ $prop->firm_id }}"
                                        data-project="{{ $prop->project->project_name ?? ($prop->project->propertyMaster->property_name ?? '') }}"
                                        {{ in_array($prop->id, $selectedPropIds) ? 'selected' : '' }}>
                                    {{ $prop->property_name }}{{ $prop->unit_no ? ' · Unit '.$prop->unit_no : '' }}
                                    @if(!$prop->project_id) [Direct Property] @elseif($prop->project) ({{ $prop->project->project_name }}) @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('property_ids')<div class="text-error">{{ $message }}</div>@enderror
                    @error('property_ids.*')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Amount & Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> 2. Amount &amp; Payment Details</div>
            
            <div class="form-row-3">
                {{-- Amount --}}
                <div class="form-group">
                    <label class="form-label" for="amount">Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount"
                           value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror"
                           placeholder="0.00" required>
                    @error('amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Mode --}}
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">— Select Mode —</option>
                        @foreach($paymentModes as $pm)
                            @php $pName = is_object($pm) ? $pm->name : $pm; @endphp
                            <option value="{{ $pName }}" {{ old('payment_mode', 'Cash') == $pName ? 'selected' : '' }}>{{ $pName }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Paid To / Vendor --}}
                <div class="form-group">
                    <label class="form-label" for="paid_to">Paid To / Vendor <span class="opt">(Payee Name or Vendor)</span></label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" name="paid_to" id="paid_to" value="{{ old('paid_to') }}"
                               class="form-control @error('paid_to') is-invalid @enderror"
                               placeholder="Vendor or payee name"
                               list="vendors-datalist">
                        <datalist id="vendors-datalist">
                            @if(isset($vendors))
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->name }}" data-id="{{ $ven->id }}">{{ $ven->name }} {{ $ven->mobile ? '('.$ven->mobile.')' : '' }}</option>
                                @endforeach
                            @endif
                        </datalist>
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ old('vendor_id') }}">
                    </div>
                    @error('paid_to')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row-3">
                {{-- Reference Number --}}
                <div class="form-group">
                    <label class="form-label" for="reference_no">Reference Number <span class="opt">(UPI / Cheque / Bank Ref)</span></label>
                    <input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no') }}"
                           class="form-control @error('reference_no') is-invalid @enderror" placeholder="e.g. UPI-98765432, CHQ-100234">
                    @error('reference_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Payment Account --}}
                <div class="form-group">
                    <label class="form-label" for="payment_account">Payment Account <span class="opt">(Bank / Account Source)</span></label>
                    <input type="text" name="payment_account" id="payment_account" value="{{ old('payment_account') }}"
                           class="form-control @error('payment_account') is-invalid @enderror" placeholder="e.g. HDFC Current A/c, Cash in Hand">
                    @error('payment_account')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Bill / Invoice No --}}
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No <span class="opt">(Optional)</span></label>
                    <input type="text" name="bill_no" id="bill_no" value="{{ old('bill_no') }}"
                           class="form-control @error('bill_no') is-invalid @enderror" placeholder="e.g. INV-2026-089">
                    @error('bill_no')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                {{-- Approval Status --}}
                <div class="form-group">
                    <label class="form-label" for="approval_status">Approval Status <span class="req">*</span></label>
                    <select name="approval_status" id="approval_status" class="form-control @error('approval_status') is-invalid @enderror">
                        @foreach(['Pending','Approved','Rejected'] as $s)
                            <option value="{{ $s }}" {{ old('approval_status', 'Pending') == $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('approval_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Expense Title (Optional - auto-generated if blank) --}}
                <div class="form-group">
                    <label class="form-label" for="expense_title">Expense Title <span class="opt">(Optional Short Title)</span></label>
                    <input type="text" name="expense_title" id="expense_title"
                           value="{{ old('expense_title') }}" class="form-control @error('expense_title') is-invalid @enderror"
                           placeholder="e.g. Site Maintenance Work, Cement Purchase (Auto-generated if empty)">
                    @error('expense_title')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Description & Notes --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-align-left"></i> 3. Description &amp; Notes</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="description">Description <span class="opt">(Explain the expense in detail)</span></label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                              placeholder="Enter details about what this expense was incurred for...">{{ old('description') }}</textarea>
                    @error('description')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="notes">Notes / Remarks <span class="opt">(Internal remarks)</span></label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                              placeholder="Add any additional notes, remarks or audit references...">{{ old('notes') }}</textarea>
                    @error('notes')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Attachment Upload --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-paperclip"></i> 4. Attachment (Bill / Invoice / Receipt)</div>
            <div class="form-group">
                <div class="file-upload-box" onclick="document.getElementById('bill_file').click()">
                    <label class="file-upload-label" for="bill_file">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span class="upload-text">Click to upload bill, invoice or receipt</span>
                        <span class="upload-hint">PDF, JPG, JPEG, PNG — max 5 MB</span>
                    </label>
                    <input type="file" name="bill_file" id="bill_file" accept=".pdf,.jpg,.jpeg,.png"
                           onchange="showFileName(this)" class="@error('bill_file') is-invalid @enderror">
                    <div id="file-name-display">No file selected</div>
                </div>
                @error('bill_file')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Expense
            </button>
            <a href="{{ route('expenses.index') }}" class="btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to List
            </a>
        </div>
    </form>
</div>

<script>
function showFileName(input) {
    const display = document.getElementById('file-name-display');
    if (input.files && input.files[0]) {
        display.textContent = '📎 ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
        display.style.color = '#60A5FA';
        display.style.fontWeight = '600';
    } else {
        display.textContent = 'No file selected';
        display.style.color = '#94A3B8';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const firmSelect    = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    const projectSelect = document.getElementById('project_id');
    const propSelect    = document.getElementById('property_ids') || document.getElementById('property_id');
    const paidToInput   = document.getElementById('paid_to');
    const vendorIdInput = document.getElementById('vendor_id');

    const allProjectOptions = projectSelect ? Array.from(projectSelect.querySelectorAll('option')).slice(1) : [];
    const allPropOptions    = propSelect ? Array.from(propSelect.querySelectorAll('option')) : [];

    // Initialize Select2 on property_ids if not already done
    if (propSelect && $.fn.select2) {
        $(propSelect).select2({
            placeholder: "Search and select property / unit(s)...",
            allowClear: true,
            width: '100%'
        });
    }

    // Paid to vendor auto-link
    if (paidToInput) {
        paidToInput.addEventListener('input', function() {
            const val = this.value.trim().toLowerCase();
            const datalist = document.getElementById('vendors-datalist');
            let matchedId = '';
            if (datalist && val) {
                Array.from(datalist.options).forEach(opt => {
                    if (opt.value.trim().toLowerCase() === val) {
                        matchedId = opt.getAttribute('data-id') || '';
                    }
                });
            }
            if (vendorIdInput) vendorIdInput.value = matchedId;
        });
    }

    function getSelectedFirmIds() {
        if (!firmSelect) return [];
        if (firmSelect.multiple) {
            return Array.from(firmSelect.selectedOptions).map(o => String(o.value)).filter(Boolean);
        } else if (firmSelect.value) {
            return [String(firmSelect.value)];
        }
        return [];
    }

    function filterProjectsAndProperties() {
        const selectedFirmIds = getSelectedFirmIds();
        const currentProjectVal = projectSelect ? projectSelect.value : '';

        // Filter Projects
        if (projectSelect) {
            projectSelect.innerHTML = '<option value="">— All Projects / General —</option>';
            let projCount = 0;
            allProjectOptions.forEach(opt => {
                const optFirmId = String(opt.getAttribute('data-firm-id') || '');
                if (selectedFirmIds.length === 0 || selectedFirmIds.includes(optFirmId) || !optFirmId) {
                    projectSelect.appendChild(opt.cloneNode(true));
                    projCount++;
                }
            });
            const projMatch = Array.from(projectSelect.options).some(o => o.value === currentProjectVal);
            projectSelect.value = projMatch ? currentProjectVal : '';
        }

        // Filter Properties
        filterPropertiesByProject();
    }

    function filterPropertiesByProject() {
        if (!propSelect) return;
        const selectedProjectId = projectSelect ? projectSelect.value : '';
        const selectedFirmIds   = getSelectedFirmIds();
        
        let currentPropVals = [];
        if ($.fn.select2 && $(propSelect).hasClass('select2-hidden-accessible')) {
            currentPropVals = $(propSelect).val() || [];
        } else {
            currentPropVals = Array.from(propSelect.selectedOptions).map(o => o.value);
        }
        if (!Array.isArray(currentPropVals)) {
            currentPropVals = currentPropVals ? [currentPropVals] : [];
        }

        propSelect.innerHTML = '';

        let validSelected = [];
        allPropOptions.forEach(opt => {
            const optProjId = String(opt.getAttribute('data-project-id') || '');
            const optFirmId = String(opt.getAttribute('data-firm-id') || '');

            const firmMatch = selectedFirmIds.length === 0 || selectedFirmIds.includes(optFirmId) || !optFirmId;
            const projMatch = !selectedProjectId || optProjId === String(selectedProjectId);

            if (firmMatch && projMatch) {
                const newOpt = opt.cloneNode(true);
                if (currentPropVals.includes(newOpt.value)) {
                    newOpt.selected = true;
                    validSelected.push(newOpt.value);
                }
                propSelect.appendChild(newOpt);
            }
        });

        if ($.fn.select2 && $(propSelect).hasClass('select2-hidden-accessible')) {
            $(propSelect).val(validSelected).trigger('change.select2');
        }
    }

    if (firmSelect) {
        $(firmSelect).on('change', filterProjectsAndProperties);
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', filterPropertiesByProject);
    }

    if (propSelect) {
        $(propSelect).on('change', function() {
            const selectedVals = $(this).val() || [];
            if (Array.isArray(selectedVals) && selectedVals.length === 1 && projectSelect && !projectSelect.value) {
                const firstVal = selectedVals[0];
                const matchedOpt = allPropOptions.find(o => o.value === firstVal);
                if (matchedOpt) {
                    const pId = matchedOpt.getAttribute('data-project-id');
                    if (pId) {
                        projectSelect.value = pId;
                        filterPropertiesByProject();
                    }
                }
            }
        });
    }

    // Initialize state
    if (projectSelect && projectSelect.value) {
        filterPropertiesByProject();
    }
});

// Quick Add Category Modal Functions
function openCategoryModal() {
    const modal = document.getElementById('quickCatModal');
    const input = document.getElementById('quick_cat_name');
    const err = document.getElementById('quick_cat_error');
    if (modal) {
        modal.style.display = 'flex';
        if (err) { err.style.display = 'none'; err.textContent = ''; }
        if (input) {
            input.value = '';
            setTimeout(() => input.focus(), 120);
        }
        const desc = document.getElementById('quick_cat_desc');
        if (desc) desc.value = '';
    }
}

function closeCategoryModal() {
    const modal = document.getElementById('quickCatModal');
    if (modal) modal.style.display = 'none';
    const select = document.getElementById('expense_category');
    if (select && select.value === '__new__') {
        select.value = '';
    }
}

function closeCategoryModalOnBackdrop(e) {
    if (e.target && e.target.id === 'quickCatModal') {
        closeCategoryModal();
    }
}

function handleCategoryDropdownChange(select) {
    if (select.value === '__new__') {
        openCategoryModal();
    }
}

function handleQuickCatKey(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        submitQuickCategory();
    }
}

function submitQuickCategory() {
    const nameInput = document.getElementById('quick_cat_name');
    const descInput = document.getElementById('quick_cat_desc');
    const errorEl   = document.getElementById('quick_cat_error');
    const saveBtn   = document.getElementById('btn-save-cat');
    const nameVal   = nameInput ? nameInput.value.trim() : '';
    const descVal   = descInput ? descInput.value.trim() : '';

    if (!nameVal) {
        if (errorEl) {
            errorEl.textContent = 'Please enter a category name.';
            errorEl.style.display = 'block';
        }
        if (nameInput) nameInput.focus();
        return;
    }

    if (errorEl) errorEl.style.display = 'none';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';
    }

    const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    let firmIds = [];
    if (firmSelect) {
        if (firmSelect.multiple) {
            firmIds = Array.from(firmSelect.selectedOptions).map(o => o.value).filter(Boolean);
        } else if (firmSelect.value) {
            firmIds = [firmSelect.value];
        }
    }

    fetch('{{ route("expense-categories.quick-store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            name: nameVal,
            description: descVal,
            firm_ids: firmIds,
        })
    })
    .then(res => res.json())
    .then(data => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Save Category';
        }
        if (data.success && data.category) {
            const select = document.getElementById('expense_category');
            if (select) {
                let opt = Array.from(select.options).find(o => o.value.toLowerCase() === data.category.name.toLowerCase());
                if (!opt) {
                    opt = document.createElement('option');
                    opt.value = data.category.name;
                    opt.textContent = data.category.name;
                    const newOption = select.querySelector('option[value="__new__"]');
                    if (newOption) {
                        select.insertBefore(opt, newOption);
                    } else {
                        select.appendChild(opt);
                    }
                }
                select.value = opt.value;
            }
            closeCategoryModal();
        } else {
            if (errorEl) {
                errorEl.textContent = data.message || 'Error saving category.';
                errorEl.style.display = 'block';
            }
        }
    })
    .catch(err => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fa-solid fa-check"></i> Save Category';
        }
        if (errorEl) {
            errorEl.textContent = 'Server error. Please try again.';
            errorEl.style.display = 'block';
        }
    });
}
</script>

{{-- Quick Add Category Modal --}}
<div id="quickCatModal" class="cat-modal-backdrop" style="display: none;" onclick="closeCategoryModalOnBackdrop(event)">
    <div class="cat-modal-box">
        <div class="cat-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="cat-modal-icon"><i class="fa-solid fa-tag"></i></div>
                <div>
                    <h3 style="margin: 0; color: #FFFFFF; font-size: 16.5px; font-weight: 800;">Add New Expense Category</h3>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Create a custom category for tracking expenses</p>
                </div>
            </div>
            <button type="button" class="cat-modal-close" onclick="closeCategoryModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="cat-modal-body">
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" for="quick_cat_name">Category Name <span class="req">*</span></label>
                <input type="text" id="quick_cat_name" class="form-control" placeholder="e.g. Paint, Hardware, Fuel, Transport" autocomplete="off" onkeydown="handleQuickCatKey(event)">
                <div id="quick_cat_error" class="text-error" style="display: none; margin-top: 5px;"></div>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="quick_cat_desc">Description <span class="opt">(Optional)</span></label>
                <textarea id="quick_cat_desc" class="form-control" rows="2" placeholder="Brief note about what this category covers"></textarea>
            </div>
        </div>
        <div class="cat-modal-footer">
            <button type="button" class="btn-outline" onclick="closeCategoryModal()" style="padding: 8px 18px; font-size: 13px;">Cancel</button>
            <button type="button" id="btn-save-cat" class="btn-gold" onclick="submitQuickCategory()" style="padding: 8px 20px; font-size: 13px;">
                <i class="fa-solid fa-check"></i> Save Category
            </button>
        </div>
    </div>
</div>
@endsection
