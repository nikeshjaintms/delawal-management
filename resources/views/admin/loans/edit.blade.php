@extends('admin.layouts.app')
@section('title','Edit Loan')
@section('page-title','Loan Management')
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
        border-radius: 24px !important;
        padding: 32px !important;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
        max-width: 980px;
        margin: 0 auto 30px auto;
    }

    /* ── Loan Nature Switcher (Taken vs Given) ── */
    .loan-nature-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 28px;
    }
    @media(max-width: 640px) { .loan-nature-grid { grid-template-columns: 1fr; } }

    .nature-card {
        position: relative;
        display: block;
        cursor: pointer;
        user-select: none;
    }
    .nature-card input[type="radio"] { display: none; }
    .nature-card-body {
        padding: 18px 20px;
        border-radius: 18px;
        background: rgba(16, 22, 34, 0.85);
        border: 2px solid rgba(255, 255, 255, 0.12);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .nature-card:hover .nature-card-body {
        border-color: rgba(255, 255, 255, 0.30);
        transform: translateY(-2px);
    }
    .nature-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }
    .nature-icon.taken {
        background: rgba(59, 130, 246, 0.18);
        color: #60A5FA;
        border: 1px solid rgba(59, 130, 246, 0.35);
    }
    .nature-icon.given {
        background: rgba(16, 185, 129, 0.18);
        color: #34D399;
        border: 1px solid rgba(16, 185, 129, 0.35);
    }
    .nature-title {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF;
        margin-bottom: 4px;
        letter-spacing: -0.2px;
    }
    .nature-desc {
        font-size: 12px;
        color: #94A3B8;
        line-height: 1.35;
    }
    .nature-check {
        margin-left: auto;
        font-size: 20px;
        color: rgba(255, 255, 255, 0.20);
        transition: all 0.25s ease;
    }

    /* Selected state */
    .nature-card input[type="radio"]:checked + .nature-card-body {
        border-color: #3B82F6 !important;
        background: rgba(37, 99, 235, 0.14) !important;
        box-shadow: 0 0 24px rgba(37, 99, 235, 0.35);
    }
    .nature-card.given-card input[type="radio"]:checked + .nature-card-body {
        border-color: #10B981 !important;
        background: rgba(16, 185, 129, 0.14) !important;
        box-shadow: 0 0 24px rgba(16, 185, 129, 0.35);
    }
    .nature-card input[type="radio"]:checked + .nature-card-body .nature-check {
        color: #60A5FA !important;
    }
    .nature-card.given-card input[type="radio"]:checked + .nature-card-body .nature-check {
        color: #34D399 !important;
    }

    .section-title {
        font-size: 12.5px;
        font-weight: 800;
        color: #60A5FA !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 18px;
        padding-bottom: 8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.10);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-section { margin-bottom: 28px; }
    .form-group { margin-bottom: 20px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    @media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
    @media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #CBD5E1 !important;
        margin-bottom: 7px;
    }
    .form-label span.req { color: #F87171 !important; }
    .form-label .opt { color: #94A3B8 !important; font-weight: 400; font-size: 12px; }

    .form-control {
        width: 100%;
        padding: 11px 14px !important;
        border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
        border-radius: 10px !important;
        font-size: 14px;
        font-family: var(--font-primary);
        color: #FFFFFF !important;
        outline: none;
        transition: all 0.2s ease;
        background: rgba(16, 22, 34, 0.85) !important;
        box-sizing: border-box;
    }
    .form-control::placeholder { color: #94A3B8 !important; }
    .form-control:focus {
        border-color: #3B82F6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
    }
    select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
    textarea.form-control { resize: vertical; min-height: 85px; }

    .text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
    .form-hint { font-size: 12px; color: #94A3B8 !important; margin-top: 5px; }

    .form-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.10);
        flex-wrap: wrap;
    }

    .btn-gold {
        background: #2563EB !important;
        color: #FFFFFF !important;
        padding: 11px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #3B82F6 !important;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .25s ease;
        box-shadow: 0 4px 18px rgba(37, 99, 235, 0.38);
        text-decoration: none !important;
    }
    .btn-gold:hover {
        background: #1D4ED8 !important;
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(37, 99, 235, 0.52);
        color: #FFFFFF !important;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 22px;
        background: rgba(255, 255, 255, 0.08) !important;
        color: #FFFFFF !important;
        font-size: 13.5px;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 10px;
        text-decoration: none !important;
        transition: all .25s ease;
        cursor: pointer;
    }
    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #FFFFFF !important;
        transform: translateY(-2px);
    }

    /* ── Luxury Dark EMI Toggle Switch Card ── */
    .emi-toggle-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 22px;
        background: rgba(16, 22, 34, 0.75) !important;
        border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 16px !important;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }
    .emi-toggle-card:hover {
        border-color: rgba(59, 130, 246, 0.50) !important;
        background: rgba(24, 33, 51, 0.90) !important;
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }
    .emi-toggle-card input[type="checkbox"] {
        display: none;
    }
    .toggle-title {
        font-size: 15px;
        font-weight: 800;
        color: #FFFFFF !important;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.2px;
    }
    .toggle-title i {
        color: #60A5FA !important;
        font-size: 16px;
    }
    .toggle-desc {
        font-size: 12.5px;
        color: #94A3B8 !important;
        margin-top: 5px;
        font-weight: 500;
        line-height: 1.4;
    }
    .switch-ui {
        width: 52px;
        height: 28px;
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        border-radius: 30px;
        position: relative;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        flex-shrink: 0;
    }
    .switch-ui::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: #CBD5E1;
        border-radius: 50%;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
    }
    .emi-toggle-card input[type="checkbox"]:checked ~ .switch-ui {
        background: #2563EB !important;
        border-color: #3B82F6 !important;
        box-shadow: 0 0 14px rgba(37, 99, 235, 0.55);
    }
    .emi-toggle-card input[type="checkbox"]:checked ~ .switch-ui::after {
        transform: translateX(24px);
        background: #FFFFFF !important;
    }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2 id="page_heading">Edit Loan</h2>
        <p id="page_subheading">Update loan and financial details.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('loans.update', $loan->id) }}" id="loanForm">
        @csrf
        @method('PUT')

        {{-- Section 0: Select Loan Nature (Taken vs Given) --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-arrows-split-up-and-left"></i> 1. Select Loan Category / Nature</div>
            <div class="loan-nature-grid">
                <!-- Option A: Loan Taken -->
                <label class="nature-card" id="card_taken">
                    <input type="radio" name="loan_nature" id="nature_taken" value="taken" {{ old('loan_nature', $loan->loan_nature ?? 'taken') === 'taken' ? 'checked' : '' }} onchange="handleNatureChange('taken')">
                    <div class="nature-card-body">
                        <div class="nature-icon taken"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <div class="nature-title">Loan Taken (Borrowed)</div>
                            <div class="nature-desc">We borrowed money from a Bank or Individual Person (Liability)</div>
                        </div>
                        <div class="nature-check"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                </label>

                <!-- Option B: Loan Given -->
                <label class="nature-card given-card" id="card_given">
                    <input type="radio" name="loan_nature" id="nature_given" value="given" {{ old('loan_nature', $loan->loan_nature ?? 'taken') === 'given' ? 'checked' : '' }} onchange="handleNatureChange('given')">
                    <div class="nature-card-body">
                        <div class="nature-icon given"><i class="fa-solid fa-handshake-angle"></i></div>
                        <div>
                            <div class="nature-title">Loan Given (Lent)</div>
                            <div class="nature-desc">We gave/lent money to Customer, Employee, Relative or Party (Receivable)</div>
                        </div>
                        <div class="nature-check"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                </label>
            </div>
            @error('loan_nature')<div class="text-error">{{ $message }}</div>@enderror
        </div>

        {{-- Section 1: Party & Loan Info --}}
        <div class="form-section">
            <div class="section-title" id="party_section_title"><i class="fa-solid fa-landmark"></i> 2. Party & Loan Information</div>
            @include('admin.components.firm-select', ['selectedFirmId' => $loan->firm_id])
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" id="loan_type_label">Loan Type <span class="req">*</span></label>
                    <select name="loan_type" id="loan_type" class="form-control @error('loan_type') is-invalid @enderror" onchange="toggleLoanType()">
                        <!-- Options for Loan Taken -->
                        <option value="Business Loan" class="opt-taken" {{ old('loan_type', $loan->loan_type) == 'Business Loan' ? 'selected' : '' }}>Business Loan (Bank / NBFC)</option>
                        <option value="Personal Loan" class="opt-taken" {{ old('loan_type', $loan->loan_type) == 'Personal Loan' ? 'selected' : '' }}>Personal Loan (From Individual / Relative)</option>
                        <!-- Options for Loan Given -->
                        <option value="Given to Customer" class="opt-given" {{ old('loan_type', $loan->loan_type) == 'Given to Customer' ? 'selected' : '' }}>Loan to Customer (Given)</option>
                        <option value="Given to Person / Party" class="opt-given" {{ old('loan_type', $loan->loan_type) == 'Given to Person / Party' ? 'selected' : '' }}>Personal Lending / To Party</option>
                        <option value="Employee Loan" class="opt-given" {{ old('loan_type', $loan->loan_type) == 'Employee Loan' ? 'selected' : '' }}>Employee Loan</option>
                        <option value="Other Loan Given" class="opt-given" {{ old('loan_type', $loan->loan_type) == 'Other Loan Given' ? 'selected' : '' }}>Other Loan Given</option>
                    </select>
                    @error('loan_type')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Bank Name --}}
                <div class="form-group field-bank-name">
                    <label class="form-label">Bank Name <span class="req">*</span></label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $loan->bank_name) }}" class="form-control @error('bank_name') is-invalid @enderror" placeholder="e.g. SBI, HDFC Bank, ICICI">
                    @error('bank_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                {{-- Person / Borrower Name --}}
                <div class="form-group field-person-name" style="display:none;">
                    <label class="form-label" id="person_name_label">Person / Borrower Name <span class="req">*</span></label>
                    <input type="text" name="person_name" id="person_name" value="{{ old('person_name', $loan->person_name) }}" class="form-control @error('person_name') is-invalid @enderror" placeholder="Enter person's name">
                    @error('person_name')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group field-customer">
                    <label class="form-label" id="customer_label">Customer <span class="opt">(optional)</span></label>
                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror">
                        <option value="">— Select Customer —</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id', $loan->customer_id)==$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->mobile }}{{ $c->alternate_mobile ? ' / ' . $c->alternate_mobile : '' }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Property <span class="opt">(optional)</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror">
                        <option value="">— Select Property —</option>
                        @foreach($properties as $p)
                            <option value="{{ $p->id }}" data-project="{{ $p->project->project_name ?? ($p->project->propertyMaster->property_name ?? 'No Project Assigned') }}" {{ old('property_id', $loan->property_id)==$p->id?'selected':'' }}>{{ $p->property_name }}{{ $p->property_code?' ('.$p->property_code.')':'' }}</option>
                        @endforeach
                    </select>
                    @error('property_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group field-project">
                    <label class="form-label" for="project_display">Project</label>
                    <input type="text" id="project_display" class="form-control" readonly placeholder="Auto-determined" style="background:rgba(16, 22, 34, 0.65) !important; cursor:not-allowed; opacity: 0.85;">
                </div>

                <div class="form-group field-mobile" style="display:none;">
                    <label class="form-label">Mobile Number <span class="opt">(optional)</span></label>
                    <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $loan->mobile_number) }}" class="form-control @error('mobile_number') is-invalid @enderror" placeholder="Enter mobile number">
                    @error('mobile_number')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row field-relationship" style="display:none;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label" id="relationship_label">Relationship / Purpose <span class="opt">(optional)</span></label>
                    <input type="text" name="relationship" id="relationship" value="{{ old('relationship', $loan->relationship) }}" class="form-control @error('relationship') is-invalid @enderror" placeholder="e.g. Friend, Brother, Business Partner">
                    @error('relationship')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- EMI Toggle Section --}}
        <div class="form-section">
            <label class="emi-toggle-card" for="has_emi">
                <input type="checkbox" name="has_emi" id="has_emi" value="1" {{ old('has_emi', $loan->has_emi ? '1' : '0') == '1' ? 'checked' : '' }} onchange="toggleEmiSection()">
                <div class="toggle-content">
                    <div class="toggle-title"><i class="fa-solid fa-calculator"></i> Enable EMI Schedule</div>
                    <div class="toggle-desc" id="emi_toggle_desc">Tick this if the loan has monthly EMI installments. Uncheck if you prefer manual / lumpsum repayment without EMI schedule.</div>
                </div>
                <div class="switch-ui"></div>
            </label>
        </div>

        {{-- Section 3: Financial Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> 3. Financial Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" id="loan_amount_label">Loan Amount (₹) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="loan_amount" id="loan_amount" value="{{ old('loan_amount', $loan->loan_amount) }}" class="form-control @error('loan_amount') is-invalid @enderror" placeholder="0.00" oninput="calcEmi()">
                    @error('loan_amount')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Mode <span class="opt">(optional)</span></label>
                    <select name="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror">
                        <option value="">— Select Payment Mode —</option>
                        @foreach($paymentModes as $pm)
                            <option value="{{ $pm->id }}" {{ old('payment_mode_id', $loan->payment_mode_id) == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode_id')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Loan Status <span class="req">*</span></label>
                    <select name="loan_status" class="form-control @error('loan_status') is-invalid @enderror">
                        @foreach(['Active','Completed','Closed','Cancelled'] as $s)
                            <option value="{{ $s }}" {{ old('loan_status', $loan->loan_status)==$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    @error('loan_status')<div class="text-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" id="start_date_label">Loan Start Date <span class="req">*</span></label>
                    <input type="date" name="loan_start_date" id="loan_start_date" value="{{ old('loan_start_date', $loan->loan_start_date) }}" class="form-control @error('loan_start_date') is-invalid @enderror" onchange="calcEmi()">
                    @error('loan_start_date')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- EMI Specific Fields --}}
            <div id="emi_fields_wrapper">
                <div class="form-row-3">
                    <div class="form-group">
                        <label class="form-label">Interest Rate (% p.a.) <span class="opt">(optional)</span></label>
                        <input type="number" step="0.01" name="interest_rate" id="interest_rate" value="{{ old('interest_rate', $loan->interest_rate) }}" class="form-control @error('interest_rate') is-invalid @enderror" placeholder="e.g. 8.5" oninput="calcEmi()">
                        @error('interest_rate')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Total EMI Months <span class="req emi-req">*</span></label>
                        <input type="number" name="total_emi_months" id="total_emi_months" value="{{ old('total_emi_months', $loan->total_emi_months) }}" class="form-control @error('total_emi_months') is-invalid @enderror" placeholder="e.g. 120" oninput="calcEmi()">
                        @error('total_emi_months')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">EMI Amount (₹) <span class="req emi-req">*</span></label>
                        <input type="number" step="0.01" name="emi_amount" id="emi_amount" value="{{ old('emi_amount', $loan->emi_amount) }}" class="form-control @error('emi_amount') is-invalid @enderror" placeholder="Auto-calculated or enter manually">
                        <div class="form-hint">Auto-calculated based on amount, rate & tenure. You can override.</div>
                        @error('emi_amount')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Loan End Date <span class="opt">(optional)</span></label>
                        <input type="date" name="loan_end_date" id="loan_end_date" value="{{ old('loan_end_date', $loan->loan_end_date) }}" class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="display:flex; align-items:flex-end;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#FBBF24; cursor:pointer; padding-bottom:12px;">
                            <input type="checkbox" name="regenerate_emi" value="1" style="accent-color:#F59E0B; width:16px; height:16px;">
                            <span><i class="fa-solid fa-arrows-rotate"></i> Regenerate full EMI schedule on update</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Remarks --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-note-sticky"></i> 4. Remarks & Notes</div>
            <div class="form-group">
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Any notes about this loan...">{{ old('remarks', $loan->remarks) }}</textarea>
                @error('remarks')<div class="text-error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold" id="submit_btn"><i class="fa-solid fa-check"></i> Update Loan</button>
            <a href="{{ route('loans.show', $loan->id) }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Cancel</a>
        </div>
    </form>
</div>

<script>
function getSelectedNature() {
    const radios = document.getElementsByName('loan_nature');
    for (let r of radios) {
        if (r.checked) return r.value;
    }
    return 'taken';
}

function handleNatureChange(nature) {
    const isGiven = (nature === 'given');
    const optsTaken = document.querySelectorAll('.opt-taken');
    const optsGiven = document.querySelectorAll('.opt-given');
    const loanTypeSelect = document.getElementById('loan_type');
    const pageHeading = document.getElementById('page_heading');
    const pageSub = document.getElementById('page_subheading');
    const partyTitle = document.getElementById('party_section_title');
    const emiDesc = document.getElementById('emi_toggle_desc');
    const loanAmtLabel = document.getElementById('loan_amount_label');

    if (isGiven) {
        pageHeading.innerText = 'Edit Given Loan';
        pageSub.innerText = 'Update details of money given/lent to customer or party.';
        partyTitle.innerHTML = '<i class="fa-solid fa-user-check"></i> 2. Borrower Information';
        emiDesc.innerText = 'Tick this if borrower will pay monthly EMI installments back to you. Uncheck for direct repayments.';
        loanAmtLabel.innerHTML = 'Given Loan Amount (₹) <span class="req">*</span>';

        optsTaken.forEach(o => o.style.display = 'none');
        optsGiven.forEach(o => o.style.display = 'block');

        if (loanTypeSelect.value === 'Business Loan' || loanTypeSelect.value === 'Personal Loan') {
            loanTypeSelect.value = 'Given to Customer';
        }
    } else {
        pageHeading.innerText = 'Edit Borrowed Loan';
        pageSub.innerText = 'Update details of money borrowed from bank or person.';
        partyTitle.innerHTML = '<i class="fa-solid fa-landmark"></i> 2. Bank / Lender Information';
        emiDesc.innerText = 'Tick this if the loan has monthly EMI installments. Uncheck if you prefer manual / lumpsum repayment without EMI schedule.';
        loanAmtLabel.innerHTML = 'Borrowed Loan Amount (₹) <span class="req">*</span>';

        optsTaken.forEach(o => o.style.display = 'block');
        optsGiven.forEach(o => o.style.display = 'none');

        if (loanTypeSelect.value.startsWith('Given') || loanTypeSelect.value.includes('Employee')) {
            loanTypeSelect.value = 'Business Loan';
        }
    }

    toggleLoanType();
}

function toggleLoanType() {
    const nature = getSelectedNature();
    const loanType = document.getElementById('loan_type').value;
    const bankNameField = document.querySelector('.field-bank-name');
    const personNameField = document.querySelector('.field-person-name');
    const mobileField = document.querySelector('.field-mobile');
    const relField = document.querySelector('.field-relationship');
    const personNameLabel = document.getElementById('person_name_label');
    const startDateLabel = document.getElementById('start_date_label');

    if (nature === 'given') {
        bankNameField.style.display = 'none';
        personNameField.style.display = 'block';
        mobileField.style.display = 'block';
        relField.style.display = 'grid';
        personNameLabel.innerHTML = 'Borrower / Person Name <span class="opt">(or select Customer above)</span>';
        startDateLabel.innerHTML = 'Loan Given Date <span class="req">*</span>';
    } else {
        if (loanType === 'Personal Loan') {
            bankNameField.style.display = 'none';
            personNameField.style.display = 'block';
            mobileField.style.display = 'block';
            relField.style.display = 'grid';
            personNameLabel.innerHTML = 'Lender / Person Name <span class="req">*</span>';
            startDateLabel.innerHTML = 'Loan Taken Date <span class="req">*</span>';
        } else {
            bankNameField.style.display = 'block';
            personNameField.style.display = 'none';
            mobileField.style.display = 'none';
            relField.style.display = 'none';
            startDateLabel.innerHTML = 'Loan Start Date <span class="req">*</span>';
        }
    }
}

function toggleEmiSection() {
    const hasEmi = document.getElementById('has_emi').checked;
    const emiWrapper = document.getElementById('emi_fields_wrapper');
    const emiInputs = emiWrapper.querySelectorAll('input');

    if (hasEmi) {
        emiWrapper.style.display = 'block';
        emiInputs.forEach(i => i.disabled = false);
    } else {
        emiWrapper.style.display = 'none';
        emiInputs.forEach(i => i.disabled = true);
    }
}

function calcEmi() {
    const hasEmi = document.getElementById('has_emi').checked;
    if (!hasEmi) return;

    const P = parseFloat(document.getElementById('loan_amount').value) || 0;
    const annualRate = parseFloat(document.getElementById('interest_rate').value) || 0;
    const n = parseInt(document.getElementById('total_emi_months').value) || 0;

    if (P > 0 && annualRate > 0 && n > 0) {
        const r = annualRate / 12 / 100;
        const emi = P * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
        document.getElementById('emi_amount').value = emi.toFixed(2);
    } else if (P > 0 && n > 0 && annualRate === 0) {
        const emi = P / n;
        document.getElementById('emi_amount').value = emi.toFixed(2);
    }

    const startDate = document.getElementById('loan_start_date').value;
    if (startDate && n > 0) {
        const d = new Date(startDate);
        d.setMonth(d.getMonth() + n);
        document.getElementById('loan_end_date').value = d.toISOString().split('T')[0];
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
    handleNatureChange(getSelectedNature());
    toggleEmiSection();
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
