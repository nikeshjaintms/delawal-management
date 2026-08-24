@extends('admin.layouts.app')
@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')
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
        <h2>Edit Purchase Order - {{ $purchaseOrder->po_number }}</h2>
        <p>Modify purchase details and status below.</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.30); padding:12px 16px; border-radius:10px; margin-bottom:20px;">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST" id="po-form" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="form-card">
        <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> General Information</div>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Firm <span style="color:#F87171;">*</span></label>
                <select name="firm_id" id="firm_id" class="form-control" required>
                    <option value="">Select Firm</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" data-state="{{ $firm->state ?? '' }}" {{ old('firm_id', $purchaseOrder->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
                @error('firm_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Supplier / Vendor <span style="color:#F87171;">*</span></label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" data-state="{{ $vendor->state ?? '' }}" {{ old('vendor_id', $purchaseOrder->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @error('vendor_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">PO Date <span style="color:#F87171;">*</span></label>
                <input type="date" name="po_date" id="po_date" class="form-control" value="{{ old('po_date', $purchaseOrder->po_date ? $purchaseOrder->po_date->format('Y-m-d') : '') }}" required>
                @error('po_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-control">
                    <option value="">Select Project</option>
                    @foreach($projects as $proj)
                        <option value="{{ $proj->id }}" {{ old('project_id', $purchaseOrder->project_id) == $proj->id ? 'selected' : '' }}>{{ $proj->project_name }} ({{ $proj->propertyMaster->property_name ?? 'Property' }})</option>
                    @endforeach
                </select>
                @error('project_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Expected Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}">
                @error('delivery_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:#F87171;">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="Draft" {{ old('status', $purchaseOrder->status) == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Pending" {{ old('status', $purchaseOrder->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ old('status', $purchaseOrder->status) == 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Ordered" {{ old('status', $purchaseOrder->status) == 'Ordered' ? 'selected' : '' }}>Ordered</option>
                    <option value="Received" {{ old('status', $purchaseOrder->status) == 'Received' ? 'selected' : '' }}>Received</option>
                    <option value="Cancelled" {{ old('status', $purchaseOrder->status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="section-heading" style="margin-top:30px;"><i class="fa-solid fa-list"></i> Order Items</div>
        
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
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr class="item-row">
                        <td>
                            <select name="items[{{ $index }}][material_id]" class="form-control material-select" required>
                                <option value="">Select Material</option>
                                @foreach($materials as $mat)
                                    <option value="{{ $mat->id }}" {{ $item->material_id == $mat->id ? 'selected' : '' }}>{{ $mat->material_name }} ({{ $mat->unit ?? 'Units' }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" step="0.01" min="0.01" autocomplete="off" required></td>
                        <td><input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item->rate }}" step="0.01" min="0.00" autocomplete="off" required></td>
                        <td><input type="number" name="items[{{ $index }}][discount_pct]" class="form-control discount-pct-input" value="{{ $item->discount_pct }}" step="0.01" min="0" max="100" autocomplete="off"></td>
                        <td><input type="number" name="items[{{ $index }}][gst_pct]" class="form-control gst-pct-input" value="{{ $item->gst_pct }}" step="0.01" min="0" max="100" autocomplete="off"></td>
                        <td><input type="text" class="form-control gst-amount-input" value="{{ $item->gst_amount }}" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
                        <td><input type="text" class="form-control line-total-input" value="{{ $item->line_total }}" style="background:rgba(255,255,255,0.06) !important; cursor:default;" readonly></td>
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
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any extra details or terms...">{{ $purchaseOrder->remarks }}</textarea>
        </div>

        <div style="margin-top:32px; display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('purchase-orders.index') }}" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> Update Purchase Order</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCount = {{ count($purchaseOrder->items) }};
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
                                <option value="{{ $mat->id }}">{{ $mat->material_name }} ({{ $mat->unit ?? 'Units' }})</option>
                            @endforeach
                        </select>
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

        // Run calculation once on load to populate the summary values
        calculateRowAndTotals();
    });
</script>
@endsection
