@extends('admin.layouts.app')
@section('title', 'Create Purchase Order')
@section('page-title', 'Create Purchase Order')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.form-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; margin-bottom: 24px;
}

.section-heading {
    font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}

.form-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px; }
@media(max-width:768px){ .form-grid { grid-template-columns: 1fr; } }
.form-group { margin-bottom: 0; }
.form-label { display: block; font-size: 13.5px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 8px; }

.form-control {
    width: 100% !important;
    padding: 11px 16px !important;
    background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    font-family: var(--font-primary) !important;
    color: #FFFFFF !important;
    outline: none !important;
    transition: all 0.2s ease !important;
    box-sizing: border-box !important;
}
input.form-control, select.form-control {
    height: 44px !important;
}
select.form-control option {
    background: #101622 !important;
    color: #FFFFFF !important;
}
textarea.form-control {
    min-height: 100px !important;
    height: auto !important;
    resize: vertical !important;
    background: rgba(16, 22, 34, 0.65) !important;
    color: #FFFFFF !important;
}
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(0.8) sepia(1) saturate(5) hue-rotate(185deg);
    cursor: pointer;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
input[type=number] {
    -moz-appearance: textfield !important;
}

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 20px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.table-container { width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); }
.items-table { width: 100%; border-collapse: collapse; margin-top: 0; font-size: 13.5px; }
.items-table th { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; padding: 12px 14px; font-weight: 800; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; }
.items-table td { padding: 10px 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0 !important; vertical-align: middle; }

.remove-row-btn {
    width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;
    background: rgba(239, 68, 68, 0.15) !important; color: #F87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.30) !important; border-radius: 8px;
    cursor: pointer; transition: all .2s ease;
}
.remove-row-btn:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-1px); }

.summary-box { width: 320px; margin-left: auto; margin-top: 24px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; padding: 20px; background: rgba(16, 22, 34, 0.65); }
.summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 13.5px; color: #CBD5E1; }
.summary-row:last-child { border-bottom: none; font-weight: 800; font-size: 16px; color: #34D399; }
</style>

<div class="crud-header" style="margin-bottom:20px;">
    <div class="crud-title">
        <h2>New Purchase Order</h2>
        <p>Fill out the form below to issue a new purchase contract.</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.30); padding:12px 16px; border-radius:10px; margin-bottom:20px;">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('purchase-orders.store') }}" method="POST" id="po-form" autocomplete="off">
    @csrf
    <div class="form-card">
        <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> General Information</div>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Firm <span style="color:#F87171;">*</span></label>
                <select name="firm_id" id="firm_id" class="form-control" required>
                    <option value="">Select Firm</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" data-state="{{ $firm->state ?? '' }}" {{ (old('firm_id') ?? session('firm_id')) == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
                @error('firm_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Vendor <span style="color:#F87171;">*</span></label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Select Vendor</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" data-state="{{ $vendor->state ?? '' }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @error('vendor_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="seller_id">Seller / Supplier Store <span style="color:#94A3B8; font-size:11.5px; font-weight:normal;">(Optional)</span></label>
                <select name="seller_id" id="seller_id" class="form-control">
                    <option value="">— Select Seller / Store (Optional) —</option>
                    @if(isset($sellers))
                        @foreach($sellers as $sel)
                            <option value="{{ $sel->id }}"
                                    data-name="{{ $sel->name }}"
                                    data-firm-id="{{ $sel->firm_id }}"
                                    {{ old('seller_id') == $sel->id ? 'selected' : '' }}>
                                {{ $sel->name }} {{ $sel->mobile ? '· ('.$sel->mobile.')' : '' }} {{ $sel->seller_type ? '· ['.$sel->seller_type.']' : '' }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('seller_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">PO Date <span style="color:#F87171;">*</span></label>
                <input type="date" name="po_date" id="po_date" class="form-control" value="{{ old('po_date', date('Y-m-d')) }}" required>
                @error('po_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-control" onchange="filterContractorsByProject()">
                    <option value="">Select Project (Optional)</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id', $selectedProjectId ?? request('project_id', '')) == $proj->id ? 'selected' : '' }}>{{ $proj->project_name }} ({{ $proj->propertyMaster->property_name ?? 'Property' }})</option>
                    @endforeach
                </select>
                @error('project_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Expected Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
                @error('delivery_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid" style="grid-template-columns: 1fr 2fr;">
            <div class="form-group">
                <label class="form-label">Status <span style="color:#F87171;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="Draft" {{ old('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ old('status', 'Approved') == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Ordered" {{ old('status') == 'Ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="Received" {{ old('status') == 'Received' ? 'selected' : '' }}>Received</option>
                    <option value="Cancelled" {{ old('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Multi-Contractor Selection Section --}}
        <div class="form-group" style="margin-top: 14px; margin-bottom: 22px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; flex-wrap: wrap; gap: 8px;">
                <div>
                    <label class="form-label" style="margin-bottom: 2px;">
                        <i class="fa-solid fa-helmet-safety" style="color:#60A5FA;"></i> Select Contractor(s) / Agency (Multiple Supported)
                    </label>
                    <div style="font-size: 12px; color: #94A3B8;">Click any contractor card below to link one or multiple contractors to this PO.</div>
                </div>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <input type="text" id="contractor_search_input" oninput="filterContractorCardsBySearch()" placeholder="🔍 Search contractor..." style="background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); color: #FFFFFF; font-size: 12px; border-radius: 8px; padding: 5px 12px; outline: none; width: 180px;">
                    <button type="button" onclick="selectAllVisibleContractors()" style="background: rgba(59, 130, 246, 0.18); border: 1px solid rgba(59, 130, 246, 0.4); color: #60A5FA; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-check-double"></i> Select All
                    </button>
                    <button type="button" onclick="clearSelectedContractors()" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #F87171; font-size: 11.5px; font-weight: 700; padding: 5px 10px; border-radius: 6px; cursor: pointer;">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </button>
                </div>
            </div>

            @php
                $selectedContractorIds = (array)(old('contractor_ids') ?: (old('contractor_id') ? [old('contractor_id')] : []));
            @endphp

            {{-- Hidden select keeping form sync --}}
            <select name="contractor_ids[]" id="contractor_id" multiple style="display: none;">
                @if(isset($contractors))
                    @foreach($contractors as $con)
                        <option value="{{ $con->id }}"
                                data-project-id="{{ $con->project_id }}"
                                data-firm-id="{{ $con->firm_id }}"
                                {{ in_array($con->id, $selectedContractorIds) ? 'selected' : '' }}>
                            {{ $con->contractor_name }}
                        </option>
                    @endforeach
                @endif
            </select>

            {{-- Visual Selection Cards Container --}}
            <div id="contractor_cards_scrollbox" style="max-height: 220px; overflow-y: auto; padding: 10px; border-radius: 12px; background: rgba(10, 15, 26, 0.75); border: 1.5px solid rgba(255, 255, 255, 0.12);">
                <div id="contractor_cards_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 8px;">
                    @if(isset($contractors))
                        @foreach($contractors as $con)
                            @php
                                $isSelected = in_array($con->id, $selectedContractorIds);
                            @endphp
                            <div class="contractor-card-item {{ $isSelected ? 'is-selected' : '' }}"
                                 id="contractor_card_{{ $con->id }}"
                                 data-id="{{ $con->id }}"
                                 data-project-id="{{ $con->project_id ?? '' }}"
                                 data-name="{{ strtolower($con->contractor_name ?? '') }}"
                                 data-mobile="{{ strtolower($con->mobile ?? '') }}"
                                 onclick="toggleContractorCardSelection({{ $con->id }})"
                                 style="cursor: pointer; user-select: none; padding: 8px 12px; border-radius: 10px; background: {{ $isSelected ? 'rgba(37, 99, 235, 0.22)' : 'rgba(20, 27, 41, 0.65)' }}; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.10)' }}; transition: all .2s ease; display: flex; align-items: center; gap: 10px; box-shadow: {{ $isSelected ? '0 0 12px rgba(59, 130, 246, 0.35)' : 'none' }};">
                                <div class="con-checkbox-circle" style="width: 20px; height: 20px; border-radius: 6px; border: 1.5px solid {{ $isSelected ? '#3B82F6' : 'rgba(255, 255, 255, 0.25)' }}; background: {{ $isSelected ? '#2563EB' : 'transparent' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #FFFFFF; font-size: 10px; transition: all .2s ease;">
                                    <i class="fa-solid fa-check" style="display: {{ $isSelected ? 'block' : 'none' }};"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div class="con-name-el" style="font-weight: 700; color: #FFFFFF; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $con->contractor_name }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                                        @if($con->project)
                                            <span style="font-size: 10px; font-weight: 600; background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #93C5FD; padding: 1px 5px; border-radius: 4px;">
                                                {{ $con->project->project_name }}
                                            </span>
                                        @endif
                                        @if($con->mobile)
                                            <span style="font-size: 11px; color: #94A3B8;">{{ $con->mobile }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <div id="no_contractors_msg" style="display: none; text-align: center; padding: 20px; color: #94A3B8; font-size: 13px;">
                    No contractors found matching search or project filter.
                </div>
            </div>

            {{-- Selected Summary Badge / Chips --}}
            <div id="contractor_summary_badge" style="display: none; margin-top: 8px; padding: 8px 12px; border-radius: 8px; background: rgba(37, 99, 235, 0.12); border: 1px solid rgba(59, 130, 246, 0.3); align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <div style="font-size: 12px; color: #93C5FD; font-weight: 700;" id="contractor_summary_text">
                    0 Contractors Selected
                </div>
                <div id="contractor_summary_pills" style="display: flex; flex-wrap: wrap; gap: 4px;"></div>
            </div>

            @error('contractor_ids') <span class="text-error show">{{ $message }}</span> @enderror
        </div>

        <div class="section-heading" style="margin-top:30px;"><i class="fa-solid fa-list"></i> Order Items</div>
        
        @php
            $oldItems = old('items');
            if (!is_array($oldItems) || empty($oldItems)) {
                $oldItems = [
                    [
                        'material_id' => '',
                        'qty' => 1,
                        'rate' => 0.00,
                        'discount_pct' => 0,
                        'gst_pct' => 18,
                        'gst_amount' => 0.00,
                        'line_total' => 0.00,
                    ]
                ];
            }
        @endphp

        <div class="table-container">
            <table class="items-table" id="items-table">
                <thead>
                    <tr>
                        <th style="width:30%;">Material <span style="color:#F87171;">*</span></th>
                        <th style="width:10%;">Qty <span style="color:#F87171;">*</span></th>
                        <th style="width:12%;">Rate <span style="color:#F87171;">*</span></th>
                        <th style="width:10%;">Disc %</th>
                        <th style="width:10%;">GST %</th>
                        <th style="width:12%;">GST Amount</th>
                        <th style="width:12%;">Line Total</th>
                        <th style="width:6%; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody id="item-rows">
                    @foreach($oldItems as $index => $item)
                    <tr class="item-row">
                        <td>
                            <select name="items[{{ $index }}][material_id]" class="form-control material-select" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $mat)
                                    <option value="{{ $mat->id }}"
                                            data-stock="{{ $mat->current_stock }}"
                                            data-needed="{{ $mat->opening_stock }}"
                                            data-rate="{{ $mat->unit_price ?? 0 }}"
                                            data-unit="{{ $mat->unit ?? '' }}"
                                            data-project-id="{{ $mat->project_id ?? '' }}"
                                            data-contractor-id="{{ $mat->contractor_id ?? '' }}"
                                            {{ (isset($item['material_id']) && $item['material_id'] == $mat->id) ? 'selected' : '' }}>
                                        {{ $mat->material_name }} (Stock: {{ number_format($mat->current_stock, 2) }} {{ $mat->unit }} | ₹{{ number_format($mat->unit_price ?? 0, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="stock-badge" style="font-size:11.5px; margin-top:4px; color:#60A5FA; font-weight:600;"></div>
                        </td>
                        <td><input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item['qty'] ?? 1 }}" step="0.01" min="0.01" autocomplete="off" required></td>
                        <td><input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item['rate'] ?? '0.00' }}" step="0.01" min="0.00" autocomplete="off" required></td>
                        <td><input type="number" name="items[{{ $index }}][discount_pct]" class="form-control discount-pct-input" value="{{ $item['discount_pct'] ?? 0 }}" step="0.01" min="0" max="100" autocomplete="off"></td>
                        <td><input type="number" name="items[{{ $index }}][gst_pct]" class="form-control gst-pct-input" value="{{ $item['gst_pct'] ?? 18 }}" step="0.01" min="0" max="100" autocomplete="off"></td>
                        <td><input type="text" class="form-control gst-amount-input" value="{{ $item['gst_amount'] ?? '0.00' }}" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
                        <td><input type="text" class="form-control line-total-input" value="{{ $item['line_total'] ?? '0.00' }}" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
                        <td style="text-align:center;"><button type="button" class="remove-row-btn" title="Remove Item"><i class="fa-solid fa-trash-can"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <button type="button" class="btn-outline" id="add-row-btn" style="margin-top:16px;"><i class="fa-solid fa-plus"></i> Add Item</button>

        <div class="summary-box">
            <div class="summary-row">
                <span>Sub Total:</span>
                <span>₹<span id="lbl-sub-total">0.00</span></span>
            </div>
            <div class="summary-row">
                <span>Discount:</span>
                <span>₹<span id="lbl-discount">0.00</span></span>
            </div>
            <div class="summary-row">
                <span>Taxable Value:</span>
                <span>₹<span id="lbl-taxable">0.00</span></span>
            </div>
            <div class="summary-row tax-intrastate">
                <span>CGST:</span>
                <span>₹<span id="lbl-cgst">0.00</span></span>
            </div>
            <div class="summary-row tax-intrastate">
                <span>SGST:</span>
                <span>₹<span id="lbl-sgst">0.00</span></span>
            </div>
            <div class="summary-row tax-interstate" style="display:none;">
                <span>IGST:</span>
                <span>₹<span id="lbl-igst">0.00</span></span>
            </div>
            <div class="summary-row">
                <span>Grand Total:</span>
                <span>₹<span id="lbl-grand-total">0.00</span></span>
            </div>
        </div>

        <div class="form-group" style="margin-top:24px;">
            <label class="form-label">Remarks / Terms &amp; Conditions</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any extra details or terms...">{{ old('remarks') }}</textarea>
        </div>

        <div style="margin-top:32px; display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('purchase-orders.index') }}" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Purchase Order</button>
        </div>
    </div>
</form>

<script>
    function toggleCustomSupplierMode() {
        const dropdownWrap = document.getElementById('seller_dropdown_wrapper');
        const customWrap = document.getElementById('custom_supplier_wrapper');
        const sellerSelect = document.getElementById('seller_id');
        const customInput = document.getElementById('supplier_name');

        if (!dropdownWrap || !customWrap) return;

        if (customWrap.style.display === 'none' || customWrap.style.display === '') {
            dropdownWrap.style.display = 'none';
            customWrap.style.display = 'block';
            if (sellerSelect) sellerSelect.value = '__custom__';
            if (customInput) customInput.focus();
        } else {
            switchToSellerDropdown();
        }
    }

    function switchToSellerDropdown() {
        const dropdownWrap = document.getElementById('seller_dropdown_wrapper');
        const customWrap = document.getElementById('custom_supplier_wrapper');
        const sellerSelect = document.getElementById('seller_id');
        const customInput = document.getElementById('supplier_name');

        if (dropdownWrap && customWrap) {
            customWrap.style.display = 'none';
            dropdownWrap.style.display = 'block';
            if (sellerSelect && sellerSelect.value === '__custom__') {
                sellerSelect.value = '';
            }
            if (customInput) customInput.value = '';
        }
    }

    function handleSellerSelect(select) {
        const customWrap = document.getElementById('custom_supplier_wrapper');
        const customInput = document.getElementById('supplier_name');

        if (select.value === '__custom__') {
            toggleCustomSupplierMode();
        } else if (select.value) {
            if (customWrap) customWrap.style.display = 'none';
            const opt = select.options[select.selectedIndex];
            if (customInput && opt) {
                customInput.value = opt.getAttribute('data-name') || '';
            }
        } else {
            if (customWrap) customWrap.style.display = 'none';
            if (customInput) customInput.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = {{ count($oldItems) }};
        const itemRows = document.getElementById('item-rows');
        const addRowBtn = document.getElementById('add-row-btn');
        const firmSelect = document.getElementById('firm_id');
        const vendorSelect = document.getElementById('vendor_id');

        function calculateRowAndTotals() {
            let subTotal = 0;
            let totalDiscount = 0;
            let totalTaxable = 0;
            let totalGst = 0;

            const firmOption = firmSelect.options[firmSelect.selectedIndex];
            const vendorOption = vendorSelect.options[vendorSelect.selectedIndex];
            
            const firmState = firmOption ? firmOption.getAttribute('data-state') || '' : '';
            const vendorState = vendorOption ? vendorOption.getAttribute('data-state') || '' : '';
            
            let isInterstate = false;
            if (firmState && vendorState && firmState.toLowerCase().trim() !== vendorState.toLowerCase().trim()) {
                isInterstate = true;
            }

            document.querySelectorAll('.item-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
                const discPct = parseFloat(row.querySelector('.discount-pct-input').value) || 0;
                const gstPct = parseFloat(row.querySelector('.gst-pct-input').value) || 0;

                const rowSub = qty * rate;
                const rowDisc = rowSub * (discPct / 100);
                const rowTaxable = rowSub - rowDisc;
                const rowGst = rowTaxable * (gstPct / 100);
                const rowTotal = rowTaxable + rowGst;

                row.querySelector('.gst-amount-input').value = rowGst.toFixed(2);
                row.querySelector('.line-total-input').value = rowTotal.toFixed(2);

                subTotal += rowSub;
                totalDiscount += rowDisc;
                totalTaxable += rowTaxable;
                totalGst += rowGst;
            });

            document.getElementById('lbl-sub-total').innerText = subTotal.toFixed(2);
            document.getElementById('lbl-discount').innerText = totalDiscount.toFixed(2);
            document.getElementById('lbl-taxable').innerText = totalTaxable.toFixed(2);

            if (isInterstate) {
                document.querySelectorAll('.tax-intrastate').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.tax-interstate').forEach(el => el.style.display = 'flex');
                document.getElementById('lbl-igst').innerText = totalGst.toFixed(2);
                document.getElementById('lbl-cgst').innerText = '0.00';
                document.getElementById('lbl-sgst').innerText = '0.00';
            } else {
                document.querySelectorAll('.tax-intrastate').forEach(el => el.style.display = 'flex');
                document.querySelectorAll('.tax-interstate').forEach(el => el.style.display = 'none');
                document.getElementById('lbl-cgst').innerText = (totalGst / 2).toFixed(2);
                document.getElementById('lbl-sgst').innerText = (totalGst / 2).toFixed(2);
                document.getElementById('lbl-igst').innerText = '0.00';
            }

            document.getElementById('lbl-grand-total').innerText = (totalTaxable + totalGst).toFixed(2);
        }

        // Add Row
        addRowBtn.addEventListener('click', function() {
            const template = `
                <tr class="item-row">
                    <td>
                        <select name="items[${rowCount}][material_id]" class="form-control material-select" required>
                            <option value="">Select Material</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}"
                                        data-stock="{{ $mat->current_stock }}"
                                        data-needed="{{ $mat->opening_stock }}"
                                        data-rate="{{ $mat->unit_price ?? 0 }}"
                                        data-unit="{{ $mat->unit ?? '' }}"
                                        data-project-id="{{ $mat->project_id ?? '' }}"
                                        data-contractor-id="{{ $mat->contractor_id ?? '' }}">
                                    {{ $mat->material_name }} (Stock: {{ number_format($mat->current_stock, 2) }} {{ $mat->unit }} | ₹{{ number_format($mat->unit_price ?? 0, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="stock-badge" style="font-size:11.5px; margin-top:4px; color:#60A5FA; font-weight:600;"></div>
                    </td>
                    <td><input type="number" name="items[${rowCount}][qty]" class="form-control qty-input" value="1" step="0.01" min="0.01" autocomplete="off" required></td>
                    <td><input type="number" name="items[${rowCount}][rate]" class="form-control rate-input" value="0.00" step="0.01" min="0.00" autocomplete="off" required></td>
                    <td><input type="number" name="items[${rowCount}][discount_pct]" class="form-control discount-pct-input" value="0" step="0.01" min="0" max="100" autocomplete="off"></td>
                    <td><input type="number" name="items[${rowCount}][gst_pct]" class="form-control gst-pct-input" value="18" step="0.01" min="0" max="100" autocomplete="off"></td>
                    <td><input type="text" class="form-control gst-amount-input" value="0.00" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
                    <td><input type="text" class="form-control line-total-input" value="0.00" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
                    <td style="text-align:center;"><button type="button" class="remove-row-btn" title="Remove Item"><i class="fa-solid fa-trash-can"></i></button></td>
                </tr>
            `;
            itemRows.insertAdjacentHTML('beforeend', template);
            rowCount++;
        });

        // Auto-fetch Stock & Rate when Material is selected
        itemRows.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select')) {
                const select = e.target;
                const row = select.closest('.item-row');
                const selectedOpt = select.options[select.selectedIndex];
                const rateInput = row.querySelector('.rate-input');
                const qtyInput = row.querySelector('.qty-input');
                const stockBadge = row.querySelector('.stock-badge');

                if (selectedOpt && selectedOpt.value) {
                    const rate = parseFloat(selectedOpt.getAttribute('data-rate')) || 0;
                    const stock = parseFloat(selectedOpt.getAttribute('data-stock')) || 0;
                    const needed = parseFloat(selectedOpt.getAttribute('data-needed')) || 0;
                    const unit = selectedOpt.getAttribute('data-unit') || '';

                    if (rate > 0) {
                        rateInput.value = rate.toFixed(2);
                    }
                    if (needed > 0 && (qtyInput.value == '1' || qtyInput.value == '0' || !qtyInput.value)) {
                        qtyInput.value = needed;
                    }
                    if (stockBadge) {
                        stockBadge.innerHTML = `<i class="fa-solid fa-boxes-stacked"></i> Stock: <strong>${stock} ${unit}</strong>${needed > 0 ? ' | Needed: <strong>' + needed + ' ' + unit + '</strong>' : ''}${rate > 0 ? ' | Rate: <strong>₹' + rate.toFixed(2) + '</strong>' : ''}`;
                    }
                } else {
                    if (stockBadge) stockBadge.innerHTML = '';
                }
                calculateRowAndTotals();
            }
        });

        // Event delegation for input changes
        itemRows.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || 
                e.target.classList.contains('rate-input') || 
                e.target.classList.contains('discount-pct-input') || 
                e.target.classList.contains('gst-pct-input')) {
                calculateRowAndTotals();
            }
        });

        // Remove Row
        itemRows.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-row-btn');
            if (removeBtn) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    removeBtn.closest('.item-row').remove();
                    calculateRowAndTotals();
                } else {
                    alert('You must have at least one item row.');
                }
            }
        });

        firmSelect.addEventListener('change', calculateRowAndTotals);
        vendorSelect.addEventListener('change', calculateRowAndTotals);

        // Multi-Contractor Selection Management
        window.toggleContractorCardSelection = function(id) {
            const conSelect = document.getElementById('contractor_id');
            const card = document.getElementById('contractor_card_' + id);
            if (!conSelect || !card) return;

            let opt = Array.from(conSelect.options).find(o => o.value == id);
            if (!opt) {
                opt = document.createElement('option');
                opt.value = id;
                opt.text = card.querySelector('.con-name-el')?.innerText || 'Contractor #' + id;
                conSelect.appendChild(opt);
            }

            opt.selected = !opt.selected;
            syncSingleContractorCardUI(id, opt.selected);
            updateContractorsSummary();
        };

        window.syncSingleContractorCardUI = function(id, isSelected) {
            const card = document.getElementById('contractor_card_' + id);
            if (!card) return;
            const checkCircle = card.querySelector('.con-checkbox-circle');
            const checkIcon = card.querySelector('.con-checkbox-circle i');

            if (isSelected) {
                card.classList.add('is-selected');
                card.style.background = 'rgba(37, 99, 235, 0.22)';
                card.style.borderColor = '#3B82F6';
                card.style.boxShadow = '0 0 12px rgba(59, 130, 246, 0.35)';
                if (checkCircle) {
                    checkCircle.style.borderColor = '#3B82F6';
                    checkCircle.style.background = '#2563EB';
                }
                if (checkIcon) checkIcon.style.display = 'block';
            } else {
                card.classList.remove('is-selected');
                card.style.background = 'rgba(20, 27, 41, 0.65)';
                card.style.borderColor = 'rgba(255, 255, 255, 0.10)';
                card.style.boxShadow = 'none';
                if (checkCircle) {
                    checkCircle.style.borderColor = 'rgba(255, 255, 255, 0.25)';
                    checkCircle.style.background = 'transparent';
                }
                if (checkIcon) checkIcon.style.display = 'none';
            }
        };

        window.syncAllContractorCardsFromSelect = function() {
            const conSelect = document.getElementById('contractor_id');
            if (!conSelect) return;
            Array.from(conSelect.options).forEach(opt => {
                if (opt.value) {
                    syncSingleContractorCardUI(opt.value, opt.selected);
                }
            });
            updateContractorsSummary();
        };

        window.selectAllVisibleContractors = function() {
            const conSelect = document.getElementById('contractor_id');
            const cards = document.querySelectorAll('.contractor-card-item');
            if (!conSelect) return;

            cards.forEach(card => {
                if (card.style.display !== 'none') {
                    const id = card.dataset.id;
                    let opt = Array.from(conSelect.options).find(o => o.value == id);
                    if (!opt) {
                        opt = document.createElement('option');
                        opt.value = id;
                        opt.text = card.querySelector('.con-name-el')?.innerText || 'Contractor #' + id;
                        conSelect.appendChild(opt);
                    }
                    opt.selected = true;
                    syncSingleContractorCardUI(id, true);
                }
            });
            updateContractorsSummary();
        };

        window.clearSelectedContractors = function() {
            const conSelect = document.getElementById('contractor_id');
            if (!conSelect) return;
            Array.from(conSelect.options).forEach(opt => {
                opt.selected = false;
                if (opt.value) syncSingleContractorCardUI(opt.value, false);
            });
            updateContractorsSummary();
        };

        window.filterContractorCardsBySearch = function() {
            const searchInput = document.getElementById('contractor_search_input');
            const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
            const projectSelect = document.getElementById('project_id');
            const selectedProjId = projectSelect ? projectSelect.value : '';
            const cards = document.querySelectorAll('.contractor-card-item');
            const noMsg = document.getElementById('no_contractors_msg');

            let visibleCount = 0;
            cards.forEach(card => {
                const projId = card.dataset.projectId || '';
                const name = card.dataset.name || '';
                const mobile = card.dataset.mobile || '';

                const matchesProj = (!selectedProjId || projId === selectedProjId || !projId);
                const matchesQuery = (!query || name.includes(query) || mobile.includes(query));

                if (matchesProj && matchesQuery) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noMsg) {
                noMsg.style.display = (visibleCount === 0) ? 'block' : 'none';
            }
        };

        window.filterContractorsByProject = function() {
            filterContractorCardsBySearch();
        };

        window.updateContractorsSummary = function() {
            const conSelect = document.getElementById('contractor_id');
            const badge = document.getElementById('contractor_summary_badge');
            const badgeText = document.getElementById('contractor_summary_text');
            const badgePills = document.getElementById('contractor_summary_pills');
            if (!conSelect || !badge) return;

            const selectedOpts = Array.from(conSelect.selectedOptions).filter(o => o.value);
            const count = selectedOpts.length;

            if (count > 0) {
                badge.style.display = 'flex';
                badgeText.innerHTML = `<strong>${count} Contractor(s) Selected</strong>`;
                let pillsHtml = '';
                selectedOpts.forEach(opt => {
                    const card = document.getElementById('contractor_card_' + opt.value);
                    const name = card ? card.querySelector('.con-name-el').innerText : opt.text.trim();
                    pillsHtml += `<span style="background: rgba(59, 130, 246, 0.25); border: 1px solid rgba(59, 130, 246, 0.45); padding: 2px 8px; border-radius: 6px; font-size: 11px; color: #E0F2FE;">${name}</span>`;
                });
                badgePills.innerHTML = pillsHtml;
            } else {
                badge.style.display = 'none';
            }
        };

        // Update stock badges on load for any restored rows
        document.querySelectorAll('.item-row').forEach(function(row) {
            const select = row.querySelector('.material-select');
            const stockBadge = row.querySelector('.stock-badge');
            if (select && select.value && stockBadge) {
                const selectedOpt = select.options[select.selectedIndex];
                if (selectedOpt) {
                    const rate = parseFloat(selectedOpt.getAttribute('data-rate')) || 0;
                    const stock = parseFloat(selectedOpt.getAttribute('data-stock')) || 0;
                    const needed = parseFloat(selectedOpt.getAttribute('data-needed')) || 0;
                    const unit = selectedOpt.getAttribute('data-unit') || '';
                    stockBadge.innerHTML = `<i class="fa-solid fa-boxes-stacked"></i> Stock: <strong>${stock} ${unit}</strong>${needed > 0 ? ' | Needed: <strong>' + needed + ' ' + unit + '</strong>' : ''}${rate > 0 ? ' | Rate: <strong>₹' + rate.toFixed(2) + '</strong>' : ''}`;
                }
            }
        });

        // Initialize state on DOM load
        syncAllContractorCardsFromSelect();
        filterContractorCardsBySearch();
        calculateRowAndTotals();
    });
</script>
@endsection
