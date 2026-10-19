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

.btn-quick-del-cat {
    background: rgba(239, 68, 68, 0.20) !important;
    border: 1.5px solid rgba(239, 68, 68, 0.50) !important;
    color: #F87171 !important; padding: 10px 14px; border-radius: 10px;
    font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex;
    align-items: center; gap: 6px; white-space: nowrap; transition: all .2s ease;
    font-family: inherit; flex-shrink: 0;
}
.btn-quick-del-cat:hover {
    background: #DC2626 !important; color: #FFFFFF !important;
    border-color: #EF4444 !important; transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.40);
}

.btn-quick-manage-cat {
    background: rgba(148, 163, 184, 0.15) !important;
    border: 1.5px solid rgba(148, 163, 184, 0.35) !important;
    color: #CBD5E1 !important; padding: 10px 14px; border-radius: 10px;
    font-size: 13.5px; font-weight: 600; cursor: pointer; display: inline-flex;
    align-items: center; justify-content: center; gap: 6px; transition: all .2s ease;
    font-family: inherit; flex-shrink: 0;
}
.btn-quick-manage-cat:hover {
    background: rgba(148, 163, 184, 0.28) !important; color: #FFFFFF !important;
    border-color: #94A3B8 !important; transform: translateY(-1px);
}

.btn-link-manage-cat {
    background: none; border: none; padding: 0;
    color: #60A5FA; font-size: 12px; font-weight: 600;
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    transition: color .2s;
}
.btn-link-manage-cat:hover {
    color: #93C5FD; text-decoration: underline;
}

.btn-cat-row-delete {
    background: rgba(239, 68, 68, 0.15) !important;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important; padding: 6px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px; transition: all .2s;
    flex-shrink: 0;
}
.btn-cat-row-delete:hover {
    background: #DC2626 !important; color: #FFFFFF !important;
    border-color: #EF4444 !important; transform: translateY(-1px);
}

/* ── 5 Distinct Expense Type Selector Bar ── */
.expense-type-bar {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}
@media (max-width: 992px) {
    .expense-type-bar { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 640px) {
    .expense-type-bar { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 420px) {
    .expense-type-bar { grid-template-columns: 1fr; }
}
.type-btn {
    padding: 13px 16px;
    border-radius: 14px;
    background: rgba(16, 22, 34, 0.65);
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    font-size: 13.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all .25s ease;
    font-family: inherit;
}
.type-btn:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.25);
}
.type-btn.active {
    background: linear-gradient(135deg, #2563EB, #1D4ED8) !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 18px rgba(37, 99, 235, 0.40);
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
        <p>Record a new expense voucher under the selected expense type.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="expense-form">
        @csrf

        {{-- Top Selector: 4 Distinct Expense Types --}}
        @php
            $currType = old('expense_type', $selectedType ?? 'Property');
        @endphp
        <input type="hidden" name="expense_type" id="expense_type_input" value="{{ $currType }}">

        <div class="expense-type-bar">
            <button type="button" class="type-btn {{ $currType === 'Property' ? 'active' : '' }}" onclick="selectExpenseType('Property')">
                <i class="fa-solid fa-building"></i> <span>Property Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'Project' ? 'active' : '' }}" onclick="selectExpenseType('Project')">
                <i class="fa-solid fa-city"></i> <span>Project Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'General' ? 'active' : '' }}" onclick="selectExpenseType('General')">
                <i class="fa-solid fa-briefcase"></i> <span>General Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'Rental' ? 'active' : '' }}" onclick="selectExpenseType('Rental')">
                <i class="fa-solid fa-house-user"></i> <span>Rental Expense</span>
            </button>
            <button type="button" class="type-btn {{ $currType === 'Personal' ? 'active' : '' }}" onclick="selectExpenseType('Personal')">
                <i class="fa-solid fa-user"></i> <span>Personal Expense</span>
            </button>
        </div>

        {{-- Category Quick Chips for Rental --}}
        <div id="rental-category-chips" style="{{ $currType === 'Rental' ? '' : 'display: none;' }} margin-bottom: 22px;">
            <label class="form-label" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <i class="fa-solid fa-bolt text-warning" style="color: #F59E0B !important;"></i>
                <span>Quick Rental Expense Categories:</span>
                <span class="opt">(Click to select)</span>
            </label>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php
                    $rentalChips = [
                        ['cat' => 'Property Maintenance', 'icon' => 'fa-screwdriver-wrench'],
                        ['cat' => 'Repairs & Renovation', 'icon' => 'fa-hammer'],
                        ['cat' => 'Electricity Bill', 'icon' => 'fa-bolt'],
                        ['cat' => 'Water Bill', 'icon' => 'fa-droplet'],
                        ['cat' => 'Society Maintenance', 'icon' => 'fa-building'],
                        ['cat' => 'Property Tax', 'icon' => 'fa-landmark'],
                        ['cat' => 'Plumbing Work', 'icon' => 'fa-faucet-drip'],
                        ['cat' => 'Painting & Whitewash', 'icon' => 'fa-paint-roller'],
                        ['cat' => 'Carpentry Work', 'icon' => 'fa-ruler-combined'],
                        ['cat' => 'Deep Cleaning', 'icon' => 'fa-broom'],
                        ['cat' => 'Agreement & Legal', 'icon' => 'fa-file-signature'],
                        ['cat' => 'Brokerage / Commission', 'icon' => 'fa-handshake'],
                        ['cat' => 'Security & Guard', 'icon' => 'fa-shield-halved'],
                        ['cat' => 'Pest Control', 'icon' => 'fa-bug'],
                        ['cat' => 'Appliance Repair', 'icon' => 'fa-tv'],
                    ];
                @endphp
                @foreach($rentalChips as $chip)
                    <button type="button" class="rental-chip-btn"
                            onclick="setRentalCategory('{{ $chip['cat'] }}')"
                            style="background: rgba(30, 41, 59, 0.70); border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; padding: 6px 12px; border-radius: 20px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .2s ease;">
                        <i class="fa-solid {{ $chip['icon'] }}" style="color: #60A5FA;"></i>
                        <span>{{ $chip['cat'] }}</span>
                    </button>
                @endforeach
            </div>
            <input type="hidden" name="expense_subcategory" id="expense_subcategory" value="{{ old('expense_subcategory') }}">
        </div>

        {{-- Category Quick Chips for Personal --}}
        <div id="personal-category-chips" style="{{ $currType === 'Personal' ? '' : 'display: none;' }} margin-bottom: 22px;">
            <label class="form-label" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <i class="fa-solid fa-user-tag text-purple" style="color: #A855F7 !important;"></i>
                <span>Quick Personal Expense Categories:</span>
                <span class="opt">(Click to directly fetch category &amp; title)</span>
            </label>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php
                    $personalChips = [
                        ['cat' => 'Personal Drawings / Partner Withdrawal', 'icon' => 'fa-wallet'],
                        ['cat' => 'Household & Groceries', 'icon' => 'fa-basket-shopping'],
                        ['cat' => 'Food & Dining', 'icon' => 'fa-utensils'],
                        ['cat' => 'Fuel & Travel', 'icon' => 'fa-gas-pump'],
                        ['cat' => 'Medical & Healthcare', 'icon' => 'fa-heart-pulse'],
                        ['cat' => 'Mobile & Internet Bills', 'icon' => 'fa-mobile-screen'],
                        ['cat' => 'Personal Shopping & Clothing', 'icon' => 'fa-bag-shopping'],
                        ['cat' => 'Vehicle Maintenance (Personal)', 'icon' => 'fa-car'],
                        ['cat' => 'Education & Fees', 'icon' => 'fa-graduation-cap'],
                        ['cat' => 'Insurance & Investments', 'icon' => 'fa-shield-halved'],
                        ['cat' => 'ATM / Cash Withdrawal', 'icon' => 'fa-money-bill-transfer'],
                        ['cat' => 'Home Maintenance & Utility', 'icon' => 'fa-house'],
                        ['cat' => 'Gifts, Donations & Charity', 'icon' => 'fa-hand-holding-heart'],
                        ['cat' => 'Other Personal Expenses', 'icon' => 'fa-user-tag'],
                    ];
                @endphp
                @foreach($personalChips as $chip)
                    <button type="button" class="personal-chip-btn"
                            onclick="setPersonalCategory('{{ $chip['cat'] }}')"
                            style="background: rgba(30, 41, 59, 0.70); border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; padding: 6px 12px; border-radius: 20px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .2s ease;">
                        <i class="fa-solid {{ $chip['icon'] }}" style="color: #C084FC;"></i>
                        <span>{{ $chip['cat'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Category Quick Chips for General --}}
        <div id="general-category-chips" style="{{ $currType === 'General' ? '' : 'display: none;' }} margin-bottom: 22px;">
            <label class="form-label" style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                <i class="fa-solid fa-briefcase text-emerald" style="color: #10B981 !important;"></i>
                <span>Quick General &amp; Office Expense Categories:</span>
                <span class="opt">(Click to directly fetch category &amp; title)</span>
            </label>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @php
                    $generalChips = [
                        ['cat' => 'Office Rent & Maintenance', 'icon' => 'fa-building-columns'],
                        ['cat' => 'Staff Salary & Wages', 'icon' => 'fa-users-gear'],
                        ['cat' => 'Tea, Coffee & Refreshments', 'icon' => 'fa-mug-hot'],
                        ['cat' => 'Electricity & Utility Bills', 'icon' => 'fa-bolt'],
                        ['cat' => 'Stationery & Office Supplies', 'icon' => 'fa-pen-ruler'],
                        ['cat' => 'Internet, Wi-Fi & IT Software', 'icon' => 'fa-network-wired'],
                        ['cat' => 'Legal & Professional Fees', 'icon' => 'fa-scale-balanced'],
                        ['cat' => 'Audit & Accounting Fees', 'icon' => 'fa-file-invoice-dollar'],
                        ['cat' => 'Advertising & Marketing', 'icon' => 'fa-bullhorn'],
                        ['cat' => 'Office Cleaning & Housekeeping', 'icon' => 'fa-broom'],
                        ['cat' => 'Courier & Postage', 'icon' => 'fa-paper-plane'],
                        ['cat' => 'Bank Charges & Fees', 'icon' => 'fa-receipt'],
                        ['cat' => 'Taxes, GST & Govt Fees', 'icon' => 'fa-landmark'],
                        ['cat' => 'Staff Travel & Conveyance', 'icon' => 'fa-car-side'],
                        ['cat' => 'Other General Expenses', 'icon' => 'fa-tags'],
                    ];
                @endphp
                @foreach($generalChips as $chip)
                    <button type="button" class="general-chip-btn"
                            onclick="setGeneralCategory('{{ $chip['cat'] }}')"
                            style="background: rgba(30, 41, 59, 0.70); border: 1px solid rgba(255, 255, 255, 0.12); color: #E2E8F0; padding: 6px 12px; border-radius: 20px; font-size: 12.5px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all .2s ease;">
                        <i class="fa-solid {{ $chip['icon'] }}" style="color: #34D399;"></i>
                        <span>{{ $chip['cat'] }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Section 1: Classification & Hierarchy --}}
        <div class="form-section" id="section-classification">
            <div class="section-title"><i class="fa-solid fa-sitemap"></i> <span id="section-classification-title">{{ $currType === 'Rental' ? '1. Rental Property & Agreement Allocation' : ($currType === 'Property' ? '1. Property Expense Classification' : '1. Expense Classification & Allocation') }}</span></div>
            
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

                {{-- Expense Category with Dark Dropdown & Quick Add / Remove / Manage Buttons --}}
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <label class="form-label" for="expense_category" style="margin-bottom: 0;">Expense Category <span class="req">*</span></label>
                        <button type="button" class="btn-link-manage-cat" onclick="openManageCategoriesModal()" title="Manage or Remove Categories">
                            <i class="fa-solid fa-list-check"></i> Manage Categories
                        </button>
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select name="expense_category" id="expense_category" class="form-control @error('expense_category') is-invalid @enderror" onchange="handleCategoryDropdownChange(this)" required>
                            <option value="">— Select Category —</option>
                            @if(isset($categories))
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" data-id="{{ $cat->id }}" {{ old('expense_category') == $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            @endif
                            <option value="__new__" style="color: #60A5FA; font-weight: 700; background: #1E293B;">➕ + Add New Category...</option>
                            <option value="__manage__" style="color: #F87171; font-weight: 700; background: #1E293B;">🗑️ Manage / Remove Categories...</option>
                        </select>
                        <button type="button" class="btn-quick-add-cat" onclick="openCategoryModal()" title="Add New Category">
                            <i class="fa-solid fa-plus"></i> <span>Add</span>
                        </button>
                        <button type="button" id="btn-quick-del-cat" class="btn-quick-del-cat" onclick="deleteCurrentSelectedCategory()" title="Remove Selected Category" style="display: none;">
                            <i class="fa-solid fa-trash-can"></i> <span>Remove</span>
                        </button>
                        <button type="button" class="btn-quick-manage-cat" onclick="openManageCategoriesModal()" title="Manage & Remove Categories">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    </div>
                    @error('expense_category')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-row" id="project-property-row" style="{{ in_array($currType, ['General', 'Personal']) ? 'display:none;' : '' }}">
                {{-- Project (Only shown for Project Expense, never for Property or Rental) --}}
                <div class="form-group" id="project-field-wrapper" style="{{ $currType === 'Project' ? '' : 'display:none;' }}">
                    <label class="form-label" for="project_id">Project <span class="opt">(Project-wise Expense)</span></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror" onchange="handleProjectChange(this)">
                        <option value="">— Select Project —</option>
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

                {{-- Property / Unit Multi-Select or Single Select --}}
                <div class="form-group" id="property-field-wrapper" style="{{ $currType === 'Project' ? '' : 'grid-column: 1 / -1;' }}">
                    <label class="form-label" for="property_ids" id="property-label">
                        <span id="property-label-text">{{ $currType === 'Rental' ? 'Rental Property' : ($currType === 'Project' ? 'Plot / Unit' : 'Property') }}</span>
                        <span class="opt" id="property-label-opt">{{ $currType === 'Rental' ? '(Selecting a property auto-fetches Tenant & Agreement)' : ($currType === 'Project' ? '(Filtered by Selected Project)' : '(Multi-Select Enabled)') }}</span>
                    </label>
                    @php
                        $selectedPropIds = (array) old('property_ids', request('property_ids') ?: (request('property_id') ? [request('property_id')] : (old('property_id') ? [old('property_id')] : ($selectedPropertyIds ?? ($selectedPropertyId ? [$selectedPropertyId] : [])))));
                        $rentedIds = $rentedPropertyIds ?? \App\Models\Property::where('status', 'rented')->pluck('id')->toArray();
                        $initPlaceholder = $currType === 'Rental' ? 'Search and select rental property...' : ($currType === 'Project' ? (!empty($selectedProjectId) ? 'Search and select plot / unit...' : '— Select a Project to view plots —') : 'Search and select property...');
                    @endphp
                    <select name="property_ids[]" id="property_ids" class="form-control select2-multi @error('property_ids') is-invalid @enderror" multiple data-placeholder="{{ $initPlaceholder }}">
                        @if(isset($properties))
                            @foreach($properties as $prop)
                                @php
                                    $cleanName = preg_replace('/\s*\(Entire Property\)$/i', '', $prop->property_name);
                                    $codeStr = $prop->property_code ? ' (' . $prop->property_code . ')' : '';
                                    $displayName = $cleanName . $codeStr;
                                    $firmSuffix = $prop->firm ? ' [' . $prop->firm->firm_name . ']' : '';
                                    $isRented = in_array($prop->id, $rentedIds) || $prop->status === 'rented';
                                @endphp
                                <option value="{{ $prop->id }}"
                                        data-project-id="{{ $prop->project_id ?? '' }}"
                                        data-firm-id="{{ $prop->firm_id ?? '' }}"
                                        data-is-rented="{{ $isRented ? '1' : '0' }}"
                                        data-is-pm="{{ $prop->property_master_id ? '1' : '0' }}"
                                        data-project="{{ $prop->project->project_name ?? ($prop->project->propertyMaster->property_name ?? '') }}"
                                        {{ in_array($prop->id, $selectedPropIds) ? 'selected' : '' }}>
                                    {{ $displayName }}{{ $firmSuffix }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('property_ids')<div class="text-error">{{ $message }}</div>@enderror
                    @error('property_ids.*')<div class="text-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Rental Connection & Auto-Fetch Details Card --}}
            <div id="rental-connection-card" style="{{ $currType === 'Rental' ? '' : 'display:none;' }} margin-top: 20px; background: rgba(15, 23, 42, 0.85); border: 1.5px solid rgba(59, 130, 246, 0.35); border-radius: 18px; padding: 22px; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(37,99,235,0.25); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            <i class="fa-solid fa-house-user"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #FFFFFF;">Rental Management Connection</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Auto-synced with active Lease Agreement &amp; Tenant Profile</p>
                        </div>
                    </div>
                    <div id="rental-sync-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.4); color: #93C5FD;">
                        <i class="fa-solid fa-circle-notch fa-spin" id="rental-spinner" style="display: none;"></i>
                        <span id="rental-status-text">Select a Property to Auto-Fetch Details</span>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 16px;">
                    {{-- Rental Agreement Selector --}}
                    <div class="form-group">
                        <label class="form-label" for="rental_id">Linked Rental Agreement <span class="opt">(Auto-Selected)</span></label>
                        <select name="rental_id" id="rental_id" class="form-control @error('rental_id') is-invalid @enderror" onchange="handleRentalAgreementChange(this)">
                            <option value="">— No Agreement / Direct Property Expense —</option>
                            @if(isset($rentals))
                                @foreach($rentals as $r)
                                    <option value="{{ $r->id }}"
                                            data-property-id="{{ $r->property_id }}"
                                            data-tenant-id="{{ $r->tenant_id }}"
                                            data-tenant-name="{{ $r->tenant_name ?? $r->tenant?->name }}"
                                            data-tenant-phone="{{ $r->tenant_mobile ?? $r->tenant?->phone }}"
                                            data-rent="{{ $r->rent_amount }}"
                                            data-deposit="{{ $r->security_deposit }}"
                                            data-status="{{ $r->rental_status }}"
                                            data-firm-id="{{ $r->firm_id }}"
                                            data-start="{{ $r->start_date ? (is_string($r->start_date) ? $r->start_date : $r->start_date->format('d M Y')) : '' }}"
                                            data-end="{{ $r->end_date ? (is_string($r->end_date) ? $r->end_date : $r->end_date->format('d M Y')) : '' }}"
                                            {{ old('rental_id', $selectedRentalId ?? '') == $r->id ? 'selected' : '' }}>
                                        {{ $r->agreement_no ?: 'AGR-'.$r->id }} ({{ $r->tenant_name ?? $r->tenant?->name ?? 'Tenant' }}) · {{ $r->property?->property_name }} [{{ ucfirst($r->rental_status) }}]
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('rental_id')<div class="text-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tenant Selector --}}
                    <div class="form-group">
                        <label class="form-label" for="tenant_id">Tenant <span class="opt">(Auto-Populated)</span></label>
                        <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                            <option value="">— Select Tenant (Optional) —</option>
                            @if(isset($tenants))
                                @foreach($tenants as $t)
                                    <option value="{{ $t->id }}"
                                            data-phone="{{ $t->phone }}"
                                            data-firm-id="{{ $t->firm_id }}"
                                            {{ old('tenant_id', $selectedTenantId ?? '') == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} {{ $t->phone ? '('.$t->phone.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tenant_id')<div class="text-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Visual Info Snapshot Box --}}
                <div id="rental-snapshot-box" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; background: rgba(0,0,0,0.30); border-radius: 12px; padding: 14px; border: 1px solid rgba(255,255,255,0.06);">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Active Tenant</div>
                        <div id="snap-tenant-name" style="font-size: 14px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">—</div>
                        <div id="snap-tenant-phone" style="font-size: 12px; color: #60A5FA;">—</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Agreement No</div>
                        <div id="snap-agreement-no" style="font-size: 14px; font-weight: 800; color: #38BDF8; margin-top: 2px;">—</div>
                        <div id="snap-rental-status" style="font-size: 11.5px; color: #10B981; font-weight: 700;">—</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Rent &amp; Deposit</div>
                        <div id="snap-rent-amt" style="font-size: 14px; font-weight: 800; color: #F59E0B; margin-top: 2px;">₹ 0.00 / mo</div>
                        <div id="snap-deposit-amt" style="font-size: 11.5px; color: #94A3B8;">Dep: ₹ 0.00</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #94A3B8; font-weight: 700; letter-spacing: 0.5px;">Lease Period</div>
                        <div id="snap-lease-period" style="font-size: 12.5px; font-weight: 600; color: #CBD5E1; margin-top: 3px;">—</div>
                    </div>
                </div>

                {{-- Tenant Recovery Configuration Box --}}
                <div style="margin-top: 18px; padding-top: 16px; border-top: 1px dashed rgba(255,255,255,0.12);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                            <input type="checkbox" name="is_tenant_recoverable" id="is_tenant_recoverable" value="1"
                                   {{ old('is_tenant_recoverable') ? 'checked' : '' }}
                                   onchange="toggleRecoveryFields(this.checked)"
                                   style="width: 18px; height: 18px; accent-color: #2563EB; cursor: pointer;">
                            <span style="font-size: 13.5px; font-weight: 700; color: #FFFFFF;">
                                <i class="fa-solid fa-hand-holding-dollar" style="color: #34D399; margin-right: 4px;"></i>
                                Billable / Recoverable from Tenant
                            </span>
                        </label>
                        <span style="font-size: 12px; color: #94A3B8;">(Tracks whether tenant must reimburse this expense or be deducted from deposit)</span>
                    </div>

                    <div id="recovery-fields-row" class="form-row" style="{{ old('is_tenant_recoverable') ? '' : 'display:none;' }} margin-top: 14px;">
                        <div class="form-group">
                            <label class="form-label" for="recovery_amount">Recoverable Amount (₹) <span class="req">*</span></label>
                            <input type="number" step="0.01" min="0" name="recovery_amount" id="recovery_amount"
                                   value="{{ old('recovery_amount') }}" class="form-control"
                                   placeholder="0.00 (Defaults to expense amount)">
                            <span class="form-hint">Amount tenant is expected to pay back.</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="recovery_status">Recovery Status <span class="req">*</span></label>
                            <select name="recovery_status" id="recovery_status" class="form-control">
                                @foreach(['Pending', 'Recovered', 'Partially Recovered', 'Waived', 'Deducted from Deposit'] as $st)
                                    <option value="{{ $st }}" {{ old('recovery_status', 'Pending') == $st ? 'selected' : '' }}>{{ $st }}</option>
                                @endforeach
                            </select>
                            <span class="form-hint">Current reimbursement stage.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Personal Connection & Direct Fetch Card --}}
            <div id="personal-connection-card" style="{{ $currType === 'Personal' ? '' : 'display:none;' }} margin-top: 20px; background: rgba(15, 23, 42, 0.85); border: 1.5px solid rgba(168, 85, 247, 0.35); border-radius: 18px; padding: 22px; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(168, 85, 247, 0.25); color: #C084FC; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #FFFFFF;">Personal Account &amp; Payee Allocation</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Directly auto-fetched from logged-in user profile &amp; partner accounts</p>
                        </div>
                    </div>
                    <div id="personal-sync-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(168, 85, 247, 0.15); border: 1px solid rgba(168, 85, 247, 0.4); color: #D8B4FE;">
                        <i class="fa-solid fa-circle-check" style="color: #A855F7;"></i>
                        <span id="personal-status-text">Directly Auto-Fetched: {{ auth()->user() ? auth()->user()->name : 'Personal' }}</span>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 0;">
                    {{-- Person / Payee Auto-Select --}}
                    <div class="form-group">
                        <label class="form-label" for="personal_person_select">Personal Payee / Account <span class="opt">(Auto-Fetched)</span></label>
                        <select id="personal_person_select" class="form-control" onchange="handlePersonalPersonChange(this)">
                            @if(auth()->check())
                                <option value="{{ auth()->user()->name }}" selected>
                                    👤 {{ auth()->user()->name }} (Current User / Self)
                                </option>
                            @endif
                            @foreach(\App\Models\User::where('status', 'active')->where('id', '!=', auth()->id())->get() as $u)
                                <option value="{{ $u->name }}">
                                    👤 {{ $u->name }} (Partner / Staff)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Optional Property Allocation --}}
                    <div class="form-group">
                        <label class="form-label" for="personal_property_id">Linked Property <span class="opt">(Optional - Direct Property Link)</span></label>
                        <select name="personal_property_id" id="personal_property_id" class="form-control" onchange="handlePersonalPropertyChange(this)">
                            <option value="">— No Property / Personal Direct Expense —</option>
                            @if(isset($properties))
                                @foreach($properties->whereNotNull('property_master_id') as $p)
                                    @php
                                        $pClean = preg_replace('/\s*\(Entire Property\)$/i', '', $p->property_name);
                                    @endphp
                                    <option value="{{ $p->id }}" data-firm-id="{{ $p->firm_id }}" data-name="{{ $pClean }}">
                                        🏢 {{ $pClean }} {{ $p->property_code ? '('.$p->property_code.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            {{-- General Connection & Direct Fetch Card --}}
            <div id="general-connection-card" style="{{ $currType === 'General' ? '' : 'display:none;' }} margin-top: 20px; background: rgba(15, 23, 42, 0.85); border: 1.5px solid rgba(16, 185, 129, 0.35); border-radius: 18px; padding: 22px; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 34px; height: 34px; border-radius: 10px; background: rgba(16, 185, 129, 0.25); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 15px; font-weight: 800; color: #FFFFFF;">General Office &amp; Firm Allocation</h4>
                            <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Directly auto-fetched from company overhead &amp; office cost centers</p>
                        </div>
                    </div>
                    <div id="general-sync-badge" style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #6EE7B7;">
                        <i class="fa-solid fa-circle-check" style="color: #10B981;"></i>
                        <span id="general-status-text">Directly Auto-Fetched: General Overhead</span>
                    </div>
                </div>

                <div class="form-row" style="margin-bottom: 0;">
                    {{-- Department / Cost Center Auto-Select --}}
                    <div class="form-group">
                        <label class="form-label" for="general_department_select">Office Department / Cost Center <span class="opt">(Auto-Fetched)</span></label>
                        <select id="general_department_select" class="form-control" onchange="handleGeneralDeptChange(this)">
                            <option value="Head Office / Main Branch" selected>🏢 Head Office / Main Branch</option>
                            <option value="Accounts & Finance">💼 Accounts &amp; Finance</option>
                            <option value="Administration & HR">👥 Administration &amp; HR</option>
                            <option value="Sales & Marketing">📈 Sales &amp; Marketing</option>
                            <option value="Legal & Compliance">⚖️ Legal &amp; Compliance</option>
                            <option value="Site Operations Support">🛠️ Site Operations Support</option>
                            <option value="Pantry & Hospitality">☕ Pantry &amp; Hospitality</option>
                        </select>
                    </div>

                    {{-- Optional Property / Office Location Allocation --}}
                    <div class="form-group">
                        <label class="form-label" for="general_property_id">Office Location / Property <span class="opt">(Optional - Direct Link)</span></label>
                        <select name="general_property_id" id="general_property_id" class="form-control" onchange="handleGeneralPropertyChange(this)">
                            <option value="">— Central Office / No Property —</option>
                            @if(isset($properties))
                                @foreach($properties->whereNotNull('property_master_id') as $p)
                                    @php
                                        $pClean = preg_replace('/\s*\(Entire Property\)$/i', '', $p->property_name);
                                    @endphp
                                    <option value="{{ $p->id }}" data-firm-id="{{ $p->firm_id }}" data-name="{{ $pClean }}">
                                        🏢 {{ $pClean }} {{ $p->property_code ? '('.$p->property_code.')' : '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
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
                           placeholder="0.00" oninput="handleAmountInput(this.value)" required>
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
                        <input type="text" name="paid_to" id="paid_to" value="{{ old('paid_to', $selectedPaidTo ?? request('paid_to')) }}"
                                class="form-control @error('paid_to') is-invalid @enderror"
                                placeholder="Vendor, contractor, or tenant name"
                                list="vendors-datalist">
                        <datalist id="vendors-datalist">
                            @if(isset($vendors))
                                @foreach($vendors as $ven)
                                    <option value="{{ $ven->name }}" data-id="{{ $ven->id }}">{{ $ven->name }} {{ $ven->mobile ? '('.$ven->mobile.')' : '' }}</option>
                                @endforeach
                            @endif
                        </datalist>
                        <input type="hidden" name="vendor_id" id="vendor_id" value="{{ old('vendor_id', $selectedVendorId ?? request('vendor_id')) }}">
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
                           placeholder="e.g. Society Maintenance, AC Repair (Auto-generated if empty)">
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

function handleAmountInput(val) {
    const recCheck = document.getElementById('is_tenant_recoverable');
    const recAmt = document.getElementById('recovery_amount');
    if (recCheck && recCheck.checked && recAmt && (!recAmt.value || recAmt.dataset.userEdited !== 'true')) {
        recAmt.value = val;
    }
}

function toggleRecoveryFields(checked) {
    const row = document.getElementById('recovery-fields-row');
    const recAmt = document.getElementById('recovery_amount');
    const amt = document.getElementById('amount');
    if (row) {
        row.style.display = checked ? 'grid' : 'none';
    }
    if (checked && recAmt && !recAmt.value && amt && amt.value) {
        recAmt.value = amt.value;
    }
}

function setRentalCategory(catName) {
    const catSelect = document.getElementById('expense_category');
    const subInput = document.getElementById('expense_subcategory');
    const titleInput = document.getElementById('expense_title');
    
    if (subInput) subInput.value = catName;

    if (catSelect) {
        let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === catName.toLowerCase());
        if (!opt) {
            opt = document.createElement('option');
            opt.value = catName;
            opt.textContent = catName;
            const newOpt = catSelect.querySelector('option[value="__new__"]');
            if (newOpt) catSelect.insertBefore(opt, newOpt);
            else catSelect.appendChild(opt);
        }
        catSelect.value = opt.value;
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const propSelect = document.getElementById('property_ids');
        let propName = '';
        if (propSelect) {
            const selectedOpt = propSelect.options[propSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                propName = selectedOpt.textContent.trim().split('·')[0].split('[')[0].trim();
            }
        }
        titleInput.value = propName ? `${catName} - ${propName}` : `${catName} Expense`;
    }

    // Highlight selected chip
    document.querySelectorAll('.rental-chip-btn').forEach(btn => {
        if (btn.innerText.includes(catName)) {
            btn.style.background = '#2563EB';
            btn.style.borderColor = '#3B82F6';
            btn.style.color = '#FFFFFF';
        } else {
            btn.style.background = 'rgba(30, 41, 59, 0.70)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.12)';
            btn.style.color = '#E2E8F0';
        }
    });
}

function setPersonalCategory(catName) {
    const catSelect = document.getElementById('expense_category');
    const subInput = document.getElementById('expense_subcategory');
    const titleInput = document.getElementById('expense_title');
    const paidToInput = document.getElementById('paid_to');
    const personSelect = document.getElementById('personal_person_select');
    const propSelect = document.getElementById('personal_property_id');

    if (subInput) subInput.value = catName;

    // Direct auto-select category
    if (catSelect) {
        // Look for Personal category first or match catName
        let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === 'personal' || o.value.toLowerCase() === catName.toLowerCase());
        if (!opt) {
            opt = document.createElement('option');
            opt.value = catName;
            opt.textContent = catName;
            const newOpt = catSelect.querySelector('option[value="__new__"]');
            if (newOpt) catSelect.insertBefore(opt, newOpt);
            else catSelect.appendChild(opt);
        }
        catSelect.value = opt.value;
        if (window.updateCategoryDeleteButton) updateCategoryDeleteButton();
    }

    const currentPerson = (personSelect && personSelect.value) ? personSelect.value : '{{ auth()->user() ? auth()->user()->name : "Self" }}';
    
    // Auto populate Paid To if empty
    if (paidToInput && !paidToInput.value) {
        paidToInput.value = currentPerson;
    }

    // Auto generate informative title
    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        let propName = '';
        if (propSelect && propSelect.value) {
            const opt = propSelect.options[propSelect.selectedIndex];
            propName = opt ? (opt.getAttribute('data-name') || opt.textContent.trim()) : '';
        }
        titleInput.value = propName ? `${catName} - ${propName} (${currentPerson})` : `${catName} (${currentPerson})`;
    }

    // Highlight selected personal chip
    document.querySelectorAll('.personal-chip-btn').forEach(btn => {
        if (btn.innerText.includes(catName)) {
            btn.style.background = '#9333EA';
            btn.style.borderColor = '#C084FC';
            btn.style.color = '#FFFFFF';
        } else {
            btn.style.background = 'rgba(30, 41, 59, 0.70)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.12)';
            btn.style.color = '#E2E8F0';
        }
    });
}

function handlePersonalPersonChange(select) {
    if (!select) return;
    const person = select.value;
    const paidToInput = document.getElementById('paid_to');
    const badgeText = document.getElementById('personal-status-text');
    const titleInput = document.getElementById('expense_title');
    const subInput = document.getElementById('expense_subcategory');
    const propSelect = document.getElementById('personal_property_id');

    if (paidToInput) {
        paidToInput.value = person;
    }

    if (badgeText) {
        badgeText.textContent = 'Directly Auto-Fetched: ' + person;
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const catName = (subInput && subInput.value) ? subInput.value : 'Personal Expense';
        let propName = '';
        if (propSelect && propSelect.value) {
            const opt = propSelect.options[propSelect.selectedIndex];
            propName = opt ? (opt.getAttribute('data-name') || opt.textContent.trim()) : '';
        }
        titleInput.value = propName ? `${catName} - ${propName} (${person})` : `${catName} (${person})`;
    }
}

function handlePersonalPropertyChange(select) {
    if (!select) return;
    const selectedOpt = select.options[select.selectedIndex];
    const firmId = selectedOpt ? selectedOpt.getAttribute('data-firm-id') : null;
    const propName = selectedOpt ? (selectedOpt.getAttribute('data-name') || '') : '';
    const titleInput = document.getElementById('expense_title');
    const subInput = document.getElementById('expense_subcategory');
    const personSelect = document.getElementById('personal_person_select');

    // Auto sync firm if property has a linked firm
    if (firmId) {
        const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
        if (firmSelect) {
            if (firmSelect.multiple && window.jQuery && $.fn.select2) {
                $(firmSelect).val([String(firmId)]).trigger('change.select2');
            } else if (!firmSelect.value) {
                firmSelect.value = firmId;
                if (window.jQuery && $.fn.select2) $(firmSelect).trigger('change.select2');
            }
        }
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const catName = (subInput && subInput.value) ? subInput.value : 'Personal Expense';
        const person = (personSelect && personSelect.value) ? personSelect.value : '{{ auth()->user() ? auth()->user()->name : "Self" }}';
        titleInput.value = propName ? `${catName} - ${propName} (${person})` : `${catName} (${person})`;
    }
}

function setGeneralCategory(catName) {
    const catSelect = document.getElementById('expense_category');
    const subInput = document.getElementById('expense_subcategory');
    const titleInput = document.getElementById('expense_title');
    const deptSelect = document.getElementById('general_department_select');
    const propSelect = document.getElementById('general_property_id');

    if (subInput) subInput.value = catName;

    // Direct auto-select category
    if (catSelect) {
        let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === 'general' || o.value.toLowerCase() === catName.toLowerCase());
        if (!opt) {
            opt = document.createElement('option');
            opt.value = catName;
            opt.textContent = catName;
            const newOpt = catSelect.querySelector('option[value="__new__"]');
            if (newOpt) catSelect.insertBefore(opt, newOpt);
            else catSelect.appendChild(opt);
        }
        catSelect.value = opt.value;
        if (window.updateCategoryDeleteButton) updateCategoryDeleteButton();
    }

    const currentDept = (deptSelect && deptSelect.value) ? deptSelect.value : 'Head Office';
    
    // Auto generate informative title
    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        let propName = '';
        if (propSelect && propSelect.value) {
            const opt = propSelect.options[propSelect.selectedIndex];
            propName = opt ? (opt.getAttribute('data-name') || opt.textContent.trim()) : '';
        }
        titleInput.value = propName ? `${catName} - ${propName}` : `${catName} (${currentDept})`;
    }

    // Highlight selected general chip
    document.querySelectorAll('.general-chip-btn').forEach(btn => {
        if (btn.innerText.includes(catName)) {
            btn.style.background = '#059669';
            btn.style.borderColor = '#34D399';
            btn.style.color = '#FFFFFF';
        } else {
            btn.style.background = 'rgba(30, 41, 59, 0.70)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.12)';
            btn.style.color = '#E2E8F0';
        }
    });
}

function handleGeneralDeptChange(select) {
    if (!select) return;
    const dept = select.value;
    const badgeText = document.getElementById('general-status-text');
    const titleInput = document.getElementById('expense_title');
    const subInput = document.getElementById('expense_subcategory');
    const propSelect = document.getElementById('general_property_id');

    if (badgeText) {
        badgeText.textContent = 'Directly Auto-Fetched: ' + dept;
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const catName = (subInput && subInput.value) ? subInput.value : 'General Expense';
        let propName = '';
        if (propSelect && propSelect.value) {
            const opt = propSelect.options[propSelect.selectedIndex];
            propName = opt ? (opt.getAttribute('data-name') || opt.textContent.trim()) : '';
        }
        titleInput.value = propName ? `${catName} - ${propName}` : `${catName} (${dept})`;
    }
}

function handleGeneralPropertyChange(select) {
    if (!select) return;
    const selectedOpt = select.options[select.selectedIndex];
    const firmId = selectedOpt ? selectedOpt.getAttribute('data-firm-id') : null;
    const propName = selectedOpt ? (selectedOpt.getAttribute('data-name') || '') : '';
    const titleInput = document.getElementById('expense_title');
    const subInput = document.getElementById('expense_subcategory');
    const deptSelect = document.getElementById('general_department_select');

    // Auto sync firm if property has a linked firm
    if (firmId) {
        const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
        if (firmSelect) {
            if (firmSelect.multiple && window.jQuery && $.fn.select2) {
                $(firmSelect).val([String(firmId)]).trigger('change.select2');
            } else if (!firmSelect.value) {
                firmSelect.value = firmId;
                if (window.jQuery && $.fn.select2) $(firmSelect).trigger('change.select2');
            }
        }
    }

    if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
        const catName = (subInput && subInput.value) ? subInput.value : 'General Expense';
        const dept = (deptSelect && deptSelect.value) ? deptSelect.value : 'Head Office';
        titleInput.value = propName ? `${catName} - ${propName}` : `${catName} (${dept})`;
    }
}

function handleRentalAgreementChange(select) {
    const selectedOpt = select.options[select.selectedIndex];
    if (!selectedOpt || !selectedOpt.value) {
        updateRentalSnapshot(null);
        return;
    }

    const tId = selectedOpt.getAttribute('data-tenant-id');
    const tName = selectedOpt.getAttribute('data-tenant-name');
    const tPhone = selectedOpt.getAttribute('data-tenant-phone');
    const rent = selectedOpt.getAttribute('data-rent');
    const deposit = selectedOpt.getAttribute('data-deposit');
    const status = selectedOpt.getAttribute('data-status');
    const fId = selectedOpt.getAttribute('data-firm-id');
    const start = selectedOpt.getAttribute('data-start');
    const end = selectedOpt.getAttribute('data-end');
    const agrNo = selectedOpt.textContent.trim().split('(')[0].trim();

    // Auto set tenant
    const tenantSelect = document.getElementById('tenant_id');
    if (tenantSelect && tId) {
        tenantSelect.value = tId;
    }

    // Auto sync firm if available
    const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    if (firmSelect && fId) {
        if (firmSelect.multiple) {
            $(firmSelect).val([fId]).trigger('change');
        } else {
            firmSelect.value = fId;
        }
    }

    updateRentalSnapshot({
        tenant_name: tName,
        tenant_phone: tPhone,
        agreement_no: agrNo,
        rental_status: status,
        rent_amount: rent,
        security_deposit: deposit,
        lease_period: (start && end) ? `${start} → ${end}` : (start ? `From ${start}` : '—')
    });
}

function updateRentalSnapshot(info) {
    const nameEl = document.getElementById('snap-tenant-name');
    const phoneEl = document.getElementById('snap-tenant-phone');
    const agrEl = document.getElementById('snap-agreement-no');
    const stEl = document.getElementById('snap-rental-status');
    const rentEl = document.getElementById('snap-rent-amt');
    const depEl = document.getElementById('snap-deposit-amt');
    const leaseEl = document.getElementById('snap-lease-period');
    const badgeText = document.getElementById('rental-status-text');

    if (info) {
        if (nameEl) nameEl.textContent = info.tenant_name || '—';
        if (phoneEl) phoneEl.textContent = info.tenant_phone ? `📞 ${info.tenant_phone}` : '';
        if (agrEl) agrEl.textContent = info.agreement_no || '—';
        if (stEl) {
            stEl.textContent = (info.rental_status || 'Active').toUpperCase();
            stEl.style.color = (info.rental_status === 'active' || !info.rental_status) ? '#10B981' : '#F59E0B';
        }
        if (rentEl) rentEl.textContent = info.rent_amount ? `₹ ${parseFloat(info.rent_amount).toLocaleString('en-IN')} / mo` : '₹ 0.00 / mo';
        if (depEl) depEl.textContent = info.security_deposit ? `Dep: ₹ ${parseFloat(info.security_deposit).toLocaleString('en-IN')}` : 'Dep: ₹ 0.00';
        if (leaseEl) leaseEl.textContent = info.lease_period || '—';
        if (badgeText) badgeText.textContent = '✅ Connected to Rental Agreement';
    } else {
        if (nameEl) nameEl.textContent = '—';
        if (phoneEl) phoneEl.textContent = '—';
        if (agrEl) agrEl.textContent = '—';
        if (stEl) stEl.textContent = '—';
        if (rentEl) rentEl.textContent = '₹ 0.00 / mo';
        if (depEl) depEl.textContent = 'Dep: ₹ 0.00';
        if (leaseEl) leaseEl.textContent = '—';
        if (badgeText) badgeText.textContent = 'No Agreement Selected';
    }
}

function fetchRentalInfoForProperty(propertyId) {
    if (!propertyId) {
        updateRentalSnapshot(null);
        return;
    }

    const spinner = document.getElementById('rental-spinner');
    const badgeText = document.getElementById('rental-status-text');
    if (spinner) spinner.style.display = 'inline-block';
    if (badgeText) badgeText.textContent = 'Fetching Rental & Tenant Info...';

    const url = '{{ url("expenses/rental-property-info") }}/' + propertyId;

    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (spinner) spinner.style.display = 'none';

        if (data.success) {
            const rentalSelect = document.getElementById('rental_id');
            const tenantSelect = document.getElementById('tenant_id');

            // Populate rental agreements dropdown for this property
            if (rentalSelect && data.rentals && data.rentals.length > 0) {
                rentalSelect.innerHTML = '<option value="">— Select Agreement —</option>';
                data.rentals.forEach(r => {
                    const opt = document.createElement('option');
                    opt.value = r.id;
                    opt.textContent = `${r.agreement_no || 'AGR-' + r.id} (${r.tenant_name || 'Tenant'}) [${r.rental_status}]`;
                    opt.setAttribute('data-tenant-id', r.tenant_id || '');
                    opt.setAttribute('data-tenant-name', r.tenant_name || '');
                    opt.setAttribute('data-tenant-phone', r.tenant_phone || '');
                    opt.setAttribute('data-rent', r.rent_amount || '0');
                    opt.setAttribute('data-deposit', r.security_deposit || '0');
                    opt.setAttribute('data-status', r.rental_status || '');
                    opt.setAttribute('data-firm-id', r.firm_id || '');
                    opt.setAttribute('data-start', r.start_date || '');
                    opt.setAttribute('data-end', r.end_date || '');
                    rentalSelect.appendChild(opt);
                });

                if (data.active_rental) {
                    rentalSelect.value = data.active_rental.id;
                }
            }

            // Populate/select tenant
            if (data.active_rental && data.active_rental.tenant_id && tenantSelect) {
                tenantSelect.value = data.active_rental.tenant_id;
            }

            // Sync Firm
            if (data.property && data.property.firm_id) {
                const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
                if (firmSelect) {
                    if (firmSelect.multiple) {
                        $(firmSelect).val(data.property.firm_ids || [data.property.firm_id]).trigger('change');
                    } else {
                        firmSelect.value = data.property.firm_id;
                    }
                }
            }

            if (data.active_rental) {
                updateRentalSnapshot({
                    tenant_name: data.active_rental.tenant_name,
                    tenant_phone: data.active_rental.tenant_phone,
                    agreement_no: data.active_rental.agreement_no || 'AGR-' + data.active_rental.id,
                    rental_status: data.active_rental.rental_status,
                    rent_amount: data.active_rental.rent_amount,
                    security_deposit: data.active_rental.security_deposit,
                    lease_period: (data.active_rental.start_date && data.active_rental.end_date) ? `${data.active_rental.start_date} → ${data.active_rental.end_date}` : 'Active Agreement'
                });
            } else {
                updateRentalSnapshot(null);
                if (badgeText) badgeText.textContent = 'ℹ️ No active rental on this property';
            }
        }
    })
    .catch(err => {
        if (spinner) spinner.style.display = 'none';
        if (badgeText) badgeText.textContent = 'Error connecting to rental database';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const firmSelect    = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
    const projectSelect = document.getElementById('project_id');
    const propSelect    = document.getElementById('property_ids') || document.getElementById('property_id');
    const paidToInput   = document.getElementById('paid_to');
    const vendorIdInput = document.getElementById('vendor_id');
    const recoveryAmtInput = document.getElementById('recovery_amount');
    const titleInput = document.getElementById('expense_title');

    if (recoveryAmtInput) {
        recoveryAmtInput.addEventListener('input', function() {
            this.dataset.userEdited = 'true';
        });
    }
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            this.dataset.userEdited = 'true';
        });
    }

    const allProjectOptions = projectSelect ? Array.from(projectSelect.querySelectorAll('option')).slice(1) : [];
    window.allPropOptions = propSelect ? Array.from(propSelect.querySelectorAll('option')) : [];

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
        if (window.jQuery && $.fn.select2 && $(firmSelect).hasClass('select2-hidden-accessible')) {
            const vals = $(firmSelect).val() || [];
            return Array.isArray(vals) ? vals.map(String).filter(Boolean) : [String(vals)].filter(Boolean);
        }
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
            projectSelect.innerHTML = '<option value="">— Select Project —</option>';
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

    function handleProjectChange(select) {
        const projEl = select || document.getElementById('project_id');
        if (projEl && projEl.value) {
            const selectedOpt = projEl.options[projEl.selectedIndex];
            const projFirmId = selectedOpt ? selectedOpt.getAttribute('data-firm-id') : null;
            if (projFirmId) {
                const firmSelect = document.getElementById('firm_ids') || document.querySelector('[name="firm_id"]');
                if (firmSelect) {
                    if (firmSelect.multiple && window.jQuery && $.fn.select2) {
                        const curFirms = $(firmSelect).val() || [];
                        if (curFirms.length > 0 && !curFirms.includes(String(projFirmId))) {
                            $(firmSelect).val([...curFirms, String(projFirmId)]).trigger('change.select2');
                        }
                    }
                }
            }
        }
        filterPropertiesByProject();
    }
    window.handleProjectChange = handleProjectChange;

    function filterPropertiesByProject() {
        const propEl = document.getElementById('property_ids') || document.getElementById('property_id');
        if (!propEl) return;
        const projEl = document.getElementById('project_id');
        const selectedProjectId = projEl ? projEl.value : '';
        const selectedFirmIds   = getSelectedFirmIds();
        const typeInput         = document.getElementById('expense_type_input');
        const currentType       = typeInput ? typeInput.value : 'Property';
        const isPropertyExpense = (currentType === 'Property');
        const isRentalExpense   = (currentType === 'Rental');

        if (!window.allPropOptions || window.allPropOptions.length === 0) {
            window.allPropOptions = Array.from(propEl.querySelectorAll('option'));
        }
        const sourceOptions = window.allPropOptions;

        let currentPropVals = [];
        if (window.jQuery && $.fn.select2 && $(propEl).hasClass('select2-hidden-accessible')) {
            currentPropVals = $(propEl).val() || [];
        } else {
            currentPropVals = Array.from(propEl.selectedOptions).map(o => o.value);
        }
        if (!Array.isArray(currentPropVals)) {
            currentPropVals = currentPropVals ? [currentPropVals] : [];
        }

        // Destroy existing Select2 before updating DOM options
        if (window.jQuery && $.fn.select2 && $(propEl).hasClass('select2-hidden-accessible')) {
            $(propEl).select2('destroy');
        }

        propEl.innerHTML = '';

        let validSelected = [];
        sourceOptions.forEach(opt => {
            const optProjId = String(opt.getAttribute('data-project-id') || '');
            const optFirmId = String(opt.getAttribute('data-firm-id') || '');
            const isRented  = opt.getAttribute('data-is-rented') === '1';
            const isPm      = opt.getAttribute('data-is-pm') === '1';

            const firmMatch = selectedFirmIds.length === 0 || selectedFirmIds.includes(optFirmId) || !optFirmId;

            let typeMatch = false;
            if (isRentalExpense) {
                // Rental Expense: only Main Properties that are rented (no plots/units)
                typeMatch = isPm && isRented && firmMatch;
            } else if (isPropertyExpense) {
                // Property Expense: only Main Properties (no plots/units)
                typeMatch = isPm && firmMatch;
            } else {
                // Project Expense: Whichever project is selected, show its plots!
                if (selectedProjectId) {
                    typeMatch = !isPm && (optProjId === String(selectedProjectId));
                } else {
                    typeMatch = false;
                }
            }

            if (typeMatch) {
                const newOpt = opt.cloneNode(true);
                if (currentPropVals.includes(newOpt.value)) {
                    newOpt.selected = true;
                    validSelected.push(newOpt.value);
                }
                propEl.appendChild(newOpt);
            }
        });

        let ph = "Search and select property...";
        if (isRentalExpense) {
            ph = "Search and select rental property...";
        } else if (isPropertyExpense) {
            ph = "Search and select property...";
        } else {
            ph = selectedProjectId ? "Search and select plot / unit..." : "— Select a Project to view plots —";
        }

        if (window.jQuery && $.fn.select2) {
            $(propEl).select2({
                placeholder: ph,
                allowClear: true,
                width: '100%'
            });
            $(propEl).val(validSelected).trigger('change.select2');
        }
    }

    window.filterPropertiesByProject = filterPropertiesByProject;

    if (firmSelect) {
        $(firmSelect).on('change', filterProjectsAndProperties);
    }

    if (projectSelect) {
        $(projectSelect).on('change', function() { handleProjectChange(this); });
        projectSelect.addEventListener('change', function() { handleProjectChange(this); });
    }

    if (propSelect) {
        $(propSelect).on('change', function() {
            const selectedVals = $(this).val() || [];
            const typeInput = document.getElementById('expense_type_input');
            const isRental = typeInput && typeInput.value === 'Rental';

            if (Array.isArray(selectedVals) && selectedVals.length > 0) {
                const firstVal = selectedVals[0];
                if (isRental) {
                    fetchRentalInfoForProperty(firstVal);
                }
            } else if (isRental) {
                updateRentalSnapshot(null);
                const badgeText = document.getElementById('rental-status-text');
                if (badgeText) badgeText.textContent = 'Select a Property to Auto-Fetch Details';
            }
        });
    }

    // Auto load initial rental if provided
    const rentalSelect = document.getElementById('rental_id');
    if (rentalSelect && rentalSelect.value) {
        handleRentalAgreementChange(rentalSelect);
    } else if (propSelect) {
        const selProps = $(propSelect).val() || [];
        const typeInput = document.getElementById('expense_type_input');
        if (typeInput && typeInput.value === 'Rental' && Array.isArray(selProps) && selProps.length > 0) {
            fetchRentalInfoForProperty(selProps[0]);
        }
    }

    // Initialize dropdowns filter immediately
    filterPropertiesByProject();
});

// Quick Add Category Modal Functions
function selectExpenseType(type) {
    const input = document.getElementById('expense_type_input');
    if (input) input.value = type;

    document.querySelectorAll('.expense-type-bar .type-btn').forEach(btn => {
        if (btn.innerText.includes(type)) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });

    // Dynamically sync sidebar active item with selected tab
    try {
        const sidebarLinks = {
            'Property': document.querySelector('a[href*="expenses/property"]'),
            'Project': document.querySelector('a[href*="expenses/project-wise"]'),
            'General': document.querySelector('a[href*="expenses/general"]'),
            'Rental': document.querySelector('a[href*="expenses/rental"]'),
            'Personal': document.querySelector('a[href*="expenses/personal"]')
        };
        Object.values(sidebarLinks).forEach(link => {
            if (link) {
                link.classList.remove('active');
                const menuItem = link.closest('.menu-item');
                if (menuItem) {
                    const toggle = menuItem.querySelector('.submenu-toggle');
                    if (toggle) toggle.classList.remove('parent-active');
                    menuItem.classList.remove('open');
                    const subList = menuItem.querySelector('.submenu-list');
                    if (subList) subList.style.display = 'none';
                }
            }
        });
        const targetLink = sidebarLinks[type];
        if (targetLink) {
            targetLink.classList.add('active');
            const menuItem = targetLink.closest('.menu-item');
            if (menuItem) {
                const toggle = menuItem.querySelector('.submenu-toggle');
                if (toggle) toggle.classList.add('parent-active');
                menuItem.classList.add('open');
                const subList = menuItem.querySelector('.submenu-list');
                if (subList) subList.style.display = 'block';
            }
        }
    } catch(e) {}

    const row = document.getElementById('project-property-row');
    const projWrapper = document.getElementById('project-field-wrapper');
    const propWrapper = document.getElementById('property-field-wrapper');
    const rentalCard = document.getElementById('rental-connection-card');
    const rentalChips = document.getElementById('rental-category-chips');
    const personalCard = document.getElementById('personal-connection-card');
    const personalChips = document.getElementById('personal-category-chips');
    const generalCard = document.getElementById('general-connection-card');
    const generalChips = document.getElementById('general-category-chips');
    const titleEl = document.getElementById('section-classification-title');
    const propLabel = document.getElementById('property-label-text');
    const propOpt = document.getElementById('property-label-opt');
    const projSelect = document.getElementById('project_id');

    if (type === 'Rental') {
        if (row) row.style.display = 'grid';
        if (projWrapper) {
            projWrapper.style.display = 'none';
            if (projSelect) projSelect.value = '';
        }
        if (propWrapper) propWrapper.style.gridColumn = '1 / -1';
        if (rentalCard) rentalCard.style.display = 'block';
        if (rentalChips) rentalChips.style.display = 'block';
        if (personalCard) personalCard.style.display = 'none';
        if (personalChips) personalChips.style.display = 'none';
        if (generalCard) generalCard.style.display = 'none';
        if (generalChips) generalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. Rental Property & Agreement Allocation';
        if (propLabel) propLabel.textContent = 'Rental Property';
        if (propOpt) propOpt.textContent = '(Selecting a property auto-fetches Tenant & Agreement)';

        if (window.filterPropertiesByProject) {
            window.filterPropertiesByProject();
        }

        const propSelect = document.getElementById('property_ids');
        if (propSelect) {
            const selVal = $(propSelect).val();
            if (selVal && (Array.isArray(selVal) ? selVal[0] : selVal)) {
                fetchRentalInfoForProperty(Array.isArray(selVal) ? selVal[0] : selVal);
            }
        }
    } else if (type === 'Project') {
        if (row) row.style.display = 'grid';
        if (projWrapper) {
            projWrapper.style.display = 'block';
        }
        if (propWrapper) propWrapper.style.gridColumn = '';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (personalCard) personalCard.style.display = 'none';
        if (personalChips) personalChips.style.display = 'none';
        if (generalCard) generalCard.style.display = 'none';
        if (generalChips) generalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. Project Expense Classification';
        if (propLabel) propLabel.textContent = 'Plot / Unit';
        if (propOpt) propOpt.textContent = '(Filtered by Selected Project)';

        if (window.filterPropertiesByProject) {
            window.filterPropertiesByProject();
        }
    } else if (type === 'Property') {
        if (row) row.style.display = 'grid';
        if (projWrapper) {
            projWrapper.style.display = 'none';
            if (projSelect) projSelect.value = '';
        }
        if (propWrapper) propWrapper.style.gridColumn = '1 / -1';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (personalCard) personalCard.style.display = 'none';
        if (personalChips) personalChips.style.display = 'none';
        if (generalCard) generalCard.style.display = 'none';
        if (generalChips) generalChips.style.display = 'none';
        if (titleEl) titleEl.textContent = '1. Property Expense Classification';
        if (propLabel) propLabel.textContent = 'Property';
        if (propOpt) propOpt.textContent = '(Multi-Select Enabled)';

        if (window.filterPropertiesByProject) {
            window.filterPropertiesByProject();
        }
    } else if (type === 'General') {
        if (row) row.style.display = 'none';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (personalCard) personalCard.style.display = 'none';
        if (personalChips) personalChips.style.display = 'none';
        if (generalCard) generalCard.style.display = 'block';
        if (generalChips) generalChips.style.display = 'block';
        if (titleEl) titleEl.textContent = '1. General Expense Allocation & Classification';

        // Auto select 'General' category
        const catSelect = document.getElementById('expense_category');
        if (catSelect && !catSelect.value) {
            let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === 'general');
            if (opt) {
                catSelect.value = opt.value;
                if (window.updateCategoryDeleteButton) updateCategoryDeleteButton();
            }
        }

        // Auto set default title
        const titleInput = document.getElementById('expense_title');
        if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
            const deptSelect = document.getElementById('general_department_select');
            const dept = (deptSelect && deptSelect.value) ? deptSelect.value : 'Head Office';
            titleInput.value = 'General Expense - ' + dept;
        }
    } else if (type === 'Personal') {
        if (row) row.style.display = 'none';
        if (rentalCard) rentalCard.style.display = 'none';
        if (rentalChips) rentalChips.style.display = 'none';
        if (generalCard) generalCard.style.display = 'none';
        if (generalChips) generalChips.style.display = 'none';
        if (personalCard) personalCard.style.display = 'block';
        if (personalChips) personalChips.style.display = 'block';
        if (titleEl) titleEl.textContent = '1. Personal Expense Allocation & Classification';

        // Auto select 'Personal' category
        const catSelect = document.getElementById('expense_category');
        if (catSelect && !catSelect.value) {
            let opt = Array.from(catSelect.options).find(o => o.value.toLowerCase() === 'personal');
            if (opt) {
                catSelect.value = opt.value;
                if (window.updateCategoryDeleteButton) updateCategoryDeleteButton();
            }
        }

        // Auto populate Paid To with current user / default person
        const paidToInput = document.getElementById('paid_to');
        const personSelect = document.getElementById('personal_person_select');
        const defaultPerson = (personSelect && personSelect.value) ? personSelect.value : '{{ auth()->user() ? auth()->user()->name : "Self" }}';
        if (paidToInput && !paidToInput.value) {
            paidToInput.value = defaultPerson;
        }

        // Auto set default title
        const titleInput = document.getElementById('expense_title');
        if (titleInput && (!titleInput.value || titleInput.dataset.userEdited !== 'true')) {
            titleInput.value = 'Personal Expense - ' + defaultPerson;
        }
    }
}

function openCategoryModal() {
    const modal = document.getElementById('quickCatModal');
    const nameInput = document.getElementById('quick_cat_name');
    const descInput = document.getElementById('quick_cat_desc');
    const errorEl = document.getElementById('quick_cat_error');
    if (nameInput) nameInput.value = '';
    if (descInput) descInput.value = '';
    if (errorEl) errorEl.style.display = 'none';
    if (modal) modal.style.display = 'flex';
    setTimeout(() => { if (nameInput) nameInput.focus(); }, 150);
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

function updateCategoryDeleteButton() {
    const select = document.getElementById('expense_category');
    const delBtn = document.getElementById('btn-quick-del-cat');
    if (!select || !delBtn) return;
    const val = select.value;
    if (val && val !== '__new__' && val !== '__manage__' && val !== '') {
        delBtn.style.display = 'inline-flex';
        delBtn.title = 'Remove "' + val + '" Category';
    } else {
        delBtn.style.display = 'none';
    }
}

function handleCategoryDropdownChange(select) {
    if (select.value === '__new__') {
        openCategoryModal();
    } else if (select.value === '__manage__') {
        openManageCategoriesModal();
        select.value = '';
    }
    updateCategoryDeleteButton();
}

function deleteCurrentSelectedCategory() {
    const select = document.getElementById('expense_category');
    if (!select || !select.value) return;
    const val = select.value;
    if (val === '__new__' || val === '__manage__') return;

    const opt = select.options[select.selectedIndex];
    const catId = opt ? opt.getAttribute('data-id') : null;

    if (!confirm('Are you sure you want to permanently remove category "' + val + '" from the system?')) {
        return;
    }

    const delBtn = document.getElementById('btn-quick-del-cat');
    if (delBtn) {
        delBtn.disabled = true;
        delBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    }

    fetch('{{ route("expense-categories.quick-destroy") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ id: catId, name: val })
    })
    .then(res => res.json())
    .then(data => {
        if (delBtn) {
            delBtn.disabled = false;
            delBtn.innerHTML = '<i class="fa-solid fa-trash-can"></i> <span>Remove</span>';
        }

        if (data.success) {
            if (opt) opt.remove();
            select.value = '';
            updateCategoryDeleteButton();

            // Also remove from manage modal list if open
            const itemInList = document.querySelector(`.manage-cat-item[data-cat-name="${CSS.escape(val)}"]`);
            if (itemInList) itemInList.remove();

            showCategoryToast(data.message || 'Category removed successfully.', 'success');
        } else {
            alert(data.message || 'Error removing category.');
        }
    })
    .catch(err => {
        if (delBtn) {
            delBtn.disabled = false;
            delBtn.innerHTML = '<i class="fa-solid fa-trash-can"></i> <span>Remove</span>';
        }
        console.error(err);
        alert('Server error while removing category.');
    });
}

function openManageCategoriesModal() {
    const modal = document.getElementById('manageCatModal');
    const listContainer = document.getElementById('manageCatListContainer');
    const searchInput = document.getElementById('manage_cat_search');
    if (searchInput) searchInput.value = '';
    if (modal) modal.style.display = 'flex';

    if (listContainer) {
        listContainer.innerHTML = '<div style="padding: 30px; text-align: center; color: #94A3B8;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p style="margin-top: 10px; font-size: 13px;">Loading categories...</p></div>';
    }

    fetch('{{ route("expense-categories.quick-list") }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.categories) {
            renderManageCategoriesList(data.categories);
        } else {
            if (listContainer) {
                listContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #F87171;">Failed to load categories.</div>';
            }
        }
    })
    .catch(err => {
        if (listContainer) {
            listContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #F87171;">Server error loading categories.</div>';
        }
    });
}

function closeManageCategoriesModal() {
    const modal = document.getElementById('manageCatModal');
    if (modal) modal.style.display = 'none';
    const select = document.getElementById('expense_category');
    if (select && select.value === '__manage__') {
        select.value = '';
    }
    updateCategoryDeleteButton();
}

function renderManageCategoriesList(categories) {
    const listContainer = document.getElementById('manageCatListContainer');
    const countBadge = document.getElementById('manageCatCountBadge');
    if (countBadge) countBadge.textContent = categories.length + ' Total';

    if (!listContainer) return;
    if (categories.length === 0) {
        listContainer.innerHTML = '<div style="padding: 24px; text-align: center; color: #94A3B8; font-size: 13px;">No categories found.</div>';
        return;
    }

    let html = '';
    categories.forEach(cat => {
        const usageText = cat.usage_count > 0 ? `${cat.usage_count} vouchers linked` : 'Not in use';
        const usageBadge = cat.usage_count > 0 
            ? `<span style="font-size: 11px; padding: 2px 7px; border-radius: 4px; background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.3);">${usageText}</span>` 
            : `<span style="font-size: 11px; padding: 2px 7px; border-radius: 4px; background: rgba(148, 163, 184, 0.12); color: #94A3B8; border: 1px solid rgba(148, 163, 184, 0.2);">${usageText}</span>`;

        html += `
        <div class="manage-cat-item" data-cat-id="${cat.id}" data-cat-name="${escapeHtml(cat.name)}" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(255, 255, 255, 0.02); transition: background .15s;">
            <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37, 99, 235, 0.18); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #FFFFFF;" class="cat-name-display">${escapeHtml(cat.name)}</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 3px;">
                        ${usageBadge}
                        ${cat.description ? `<span style="font-size: 11.5px; color: #94A3B8; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(cat.description)}</span>` : ''}
                    </div>
                </div>
            </div>
            <button type="button" class="btn-cat-row-delete" onclick="deleteCategoryFromManageModal(${cat.id}, '${escapeHtml(cat.name)}', this)" title="Remove Category">
                <i class="fa-solid fa-trash-can"></i> Remove
            </button>
        </div>
        `;
    });

    listContainer.innerHTML = html;
}

function filterManageCategories(query) {
    const q = (query || '').toLowerCase().trim();
    const items = document.querySelectorAll('.manage-cat-item');
    items.forEach(item => {
        const name = (item.getAttribute('data-cat-name') || '').toLowerCase();
        if (!q || name.includes(q)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function deleteCategoryFromManageModal(id, name, btn) {
    if (!confirm(`Are you sure you want to permanently remove category "${name}"?`)) {
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

    fetch('{{ route("expense-categories.quick-destroy") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ id: id, name: name })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const row = btn.closest('.manage-cat-item');
            if (row) {
                row.style.transition = 'all .3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    row.remove();
                    const remaining = document.querySelectorAll('.manage-cat-item').length;
                    const countBadge = document.getElementById('manageCatCountBadge');
                    if (countBadge) countBadge.textContent = remaining + ' Total';
                }, 300);
            }

            const select = document.getElementById('expense_category');
            if (select) {
                const opt = Array.from(select.options).find(o => o.value.toLowerCase() === name.toLowerCase());
                if (opt) opt.remove();
                if (select.value.toLowerCase() === name.toLowerCase()) {
                    select.value = '';
                }
                updateCategoryDeleteButton();
            }

            showCategoryToast(data.message || `Category "${name}" removed.`, 'success');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash-can"></i> Remove';
            alert(data.message || 'Error removing category.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-trash-can"></i> Remove';
        console.error(err);
        alert('Server error while removing category.');
    });
}

function showCategoryToast(message, type = 'success') {
    let toast = document.getElementById('catActionToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'catActionToast';
        toast.style.cssText = 'position: fixed; bottom: 24px; right: 24px; z-index: 100000; padding: 12px 20px; border-radius: 12px; font-size: 13.5px; font-weight: 700; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: all .3s ease; opacity: 0; transform: translateY(15px); pointer-events: none;';
        document.body.appendChild(toast);
    }

    const isSuccess = type === 'success';
    toast.style.background = isSuccess ? '#064E3B' : '#7F1D1D';
    toast.style.border = isSuccess ? '1px solid #10B981' : '1px solid #EF4444';
    toast.style.color = isSuccess ? '#A7F3D0' : '#FECACA';
    toast.innerHTML = `<i class="fa-solid ${isSuccess ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> <span>${escapeHtml(message)}</span>`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';

    clearTimeout(window.__catToastTimer);
    window.__catToastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(15px)';
    }, 3500);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('DOMContentLoaded', function() {
    updateCategoryDeleteButton();
});

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
                    opt.setAttribute('data-id', data.category.id);
                    const newOption = select.querySelector('option[value="__new__"]');
                    if (newOption) {
                        select.insertBefore(opt, newOption);
                    } else {
                        select.appendChild(opt);
                    }
                }
                select.value = opt.value;
                updateCategoryDeleteButton();
            }
            closeCategoryModal();
            showCategoryToast(data.message || 'Category added successfully.', 'success');
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

{{-- Manage Categories Modal --}}
<div id="manageCatModal" class="cat-modal-backdrop" style="display: none;" onclick="if(event.target && event.target.id === 'manageCatModal') closeManageCategoriesModal()">
    <div class="cat-modal-box" style="max-width: 520px;">
        <div class="cat-modal-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div class="cat-modal-icon" style="background: rgba(239, 68, 68, 0.18); color: #F87171;">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <h3 style="margin: 0; color: #FFFFFF; font-size: 16.5px; font-weight: 800;">Manage Categories</h3>
                        <span id="manageCatCountBadge" style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 999px; background: rgba(255, 255, 255, 0.1); color: #CBD5E1; border: 1px solid rgba(255, 255, 255, 0.15);">0 Total</span>
                    </div>
                    <p style="margin: 2px 0 0; font-size: 12px; color: #94A3B8;">Remove unwanted or accidental categories from the system</p>
                </div>
            </div>
            <button type="button" class="cat-modal-close" onclick="closeManageCategoriesModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <div style="padding: 14px 20px 10px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); background: rgba(0, 0, 0, 0.15);">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #64748B; font-size: 13px;"></i>
                <input type="text" id="manage_cat_search" class="form-control" placeholder="Search categories to remove..." style="padding-left: 36px; height: 38px; font-size: 13px;" oninput="filterManageCategories(this.value)">
            </div>
        </div>

        <div id="manageCatListContainer" style="max-height: 340px; overflow-y: auto; padding: 0;">
            <!-- Rendered dynamically -->
        </div>

        <div class="cat-modal-footer" style="justify-content: space-between; align-items: center;">
            <button type="button" class="btn-quick-add-cat" onclick="closeManageCategoriesModal(); openCategoryModal();" style="font-size: 12.5px; padding: 8px 14px;">
                <i class="fa-solid fa-plus"></i> Add New Category
            </button>
            <button type="button" class="btn-outline" onclick="closeManageCategoriesModal()" style="padding: 8px 18px; font-size: 13px;">Done</button>
        </div>
    </div>
</div>
@endsection
