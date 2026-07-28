@extends('admin.layouts.app')
@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')
@section('content')
<style>
    .form-card {background:var(--card-bg); border:1px solid var(--border-color); border-radius:16px; padding:28px 32px; box-shadow:var(--card-shadow); margin-bottom:24px;}
    .form-control {
        width: 100% !important;
        height: 42px !important;
        padding: 10px 14px !important;
        border: 1.5px solid var(--border-color) !important;
        border-radius: 8px !important;
        font-size: 13.5px !important;
        font-family: var(--font-primary) !important;
        color: var(--text-primary) !important;
        outline: none !important;
        transition: border-color 0.18s, box-shadow 0.18s !important;
        background: #fff !important;
        display: block !important;
    }
    .form-control:focus {
        border-color: var(--blue) !important;
        box-shadow: 0 0 0 3px var(--blue-glow) !important;
    }
    select.form-control {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px !important;
        padding-right: 36px !important;
    }
    textarea.form-control {
        height: auto !important;
        min-height: 90px !important;
    }
    .section-heading {font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:var(--blue); margin-bottom:16px; padding-bottom:8px; border-bottom:2px solid var(--blue-light); display:flex; align-items:center; gap:8px;}
    .form-grid {display:grid; grid-template-columns:1fr 1fr 1fr; gap:18px; margin-bottom: 20px;}
    @media(max-width:768px){.form-grid{grid-template-columns:1fr}}
    .form-group {margin-bottom:0}
    .form-label {display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:7px}
    .btn-gold {background-color:var(--gold); color:#FFF; padding:10px 20px; border-radius:8px; border:none; font-size:14px; font-weight:600; cursor:pointer;}
    .btn-gold:hover {background-color:#B58D1B;}
    .btn-outline {border:1px solid var(--border-color); background:#fff; color:var(--text-secondary); padding:10px 20px; border-radius:8px; text-decoration:none; cursor:pointer;}
    .btn-outline:hover {background:#f9fafb;}
    
    .items-table {width:100%; border-collapse:collapse; margin-top:15px; font-size:13px;}
    .items-table th {background:#F9FAFB; padding:10px; font-weight:600; border-bottom:1.5px solid var(--border-color); text-transform:uppercase; font-size:11px; letter-spacing:0.5px;}
    .items-table td {padding:10px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle;}
    
    .summary-box {width:320px; margin-left:auto; margin-top:20px; border:1px solid var(--border-color); border-radius:8px; padding:16px; background:#F9FAFB;}
    .summary-row {display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #E2E8F0; font-size:13px;}
    .summary-row:last-child {border-bottom:none; font-weight:700; font-size:15px; color:#059669;}
</style>

<div class="crud-header" style="margin-bottom:20px;">
    <div class="crud-title">
        <h2>Edit Purchase Order - {{ $purchaseOrder->po_number }}</h2>
        <p>Modify purchase details and status below.</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="background:#FEE2E2; color:#B91C1C; padding:12px; border-radius:8px; margin-bottom:20px;">
    {{ session('error') }}
</div>
@endif

<form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST" id="po-form">
    @csrf
    @method('PUT')
    <div class="form-card">
        <div class="section-heading"><i class="fa-solid fa-file-invoice"></i> General Information</div>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Firm <span style="color:red;">*</span></label>
                <select name="firm_id" id="firm_id" class="form-control" required>
                    <option value="">Select Firm</option>
                    @foreach($firms as $firm)
                        <option value="{{ $firm->id }}" data-state="{{ $firm->state ?? '' }}" {{ old('firm_id', $purchaseOrder->firm_id) == $firm->id ? 'selected' : '' }}>{{ $firm->firm_name }}</option>
                    @endforeach
                </select>
                @error('firm_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Supplier / Vendor <span style="color:red;">*</span></label>
                <select name="vendor_id" id="vendor_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" data-state="{{ $vendor->state ?? '' }}" {{ old('vendor_id', $purchaseOrder->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @error('vendor_id') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">PO Date <span style="color:red;">*</span></label>
                <input type="date" name="po_date" id="po_date" class="form-control" value="{{ old('po_date', $purchaseOrder->po_date ? $purchaseOrder->po_date->format('Y-m-d') : '') }}" required>
                @error('po_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Expected Delivery Date</label>
                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('Y-m-d') : '') }}">
                @error('delivery_date') <span class="text-error show">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span style="color:red;">*</span></label>
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
                        <th style="width:30%;">Material <span style="color:red;">*</span></th>
                        <th style="width:10%;">Qty <span style="color:red;">*</span></th>
                        <th style="width:12%;">Rate <span style="color:red;">*</span></th>
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
                        <td><input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" step="0.01" min="0.01" required></td>
                        <td><input type="number" name="items[{{ $index }}][rate]" class="form-control rate-input" value="{{ $item->rate }}" step="0.01" min="0.00" required></td>
                        <td><input type="number" name="items[{{ $index }}][discount_pct]" class="form-control discount-pct-input" value="{{ $item->discount_pct }}" step="0.01" min="0" max="100"></td>
                        <td><input type="number" name="items[{{ $index }}][gst_pct]" class="form-control gst-pct-input" value="{{ $item->gst_pct }}" step="0.01" min="0" max="100"></td>
                        <td><input type="text" class="form-control gst-amount-input" value="{{ $item->gst_amount }}" readonly></td>
                        <td><input type="text" class="form-control line-total-input" value="{{ $item->line_total }}" readonly></td>
                        <td style="text-align:center;"><button type="button" class="btn-delete remove-row-btn" style="color:red; background:none; border:none; cursor:pointer;"><i class="fa fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <button type="button" class="btn-outline" id="add-row-btn" style="margin-top:15px; padding:6px 12px; font-size:12px;"><i class="fa fa-plus"></i> Add Item</button>

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

        <div class="form-group" style="margin-top:20px;">
            <label class="form-label">Remarks / Terms &amp; Conditions</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Enter any extra details or terms...">{{ $purchaseOrder->remarks }}</textarea>
        </div>

        <div style="margin-top:30px; display:flex; gap:10px; justify-content:flex-end;">
            <a href="{{ route('purchase-orders.index') }}" class="btn-outline">Cancel</a>
            <button type="submit" class="btn-gold">Update Purchase Order</button>
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
                    <td><input type="number" name="items[${rowCount}][qty]" class="form-control qty-input" value="1" step="0.01" min="0.01" required></td>
                    <td><input type="number" name="items[${rowCount}][rate]" class="form-control rate-input" value="0.00" step="0.01" min="0.00" required></td>
                    <td><input type="number" name="items[${rowCount}][discount_pct]" class="form-control discount-pct-input" value="0" step="0.01" min="0" max="100"></td>
                    <td><input type="number" name="items[${rowCount}][gst_pct]" class="form-control gst-pct-input" value="18" step="0.01" min="0" max="100"></td>
                    <td><input type="text" class="form-control gst-amount-input" value="0.00" readonly></td>
                    <td><input type="text" class="form-control line-total-input" value="0.00" readonly></td>
                    <td style="text-align:center;"><button type="button" class="btn-delete remove-row-btn" style="color:red; background:none; border:none; cursor:pointer;"><i class="fa fa-trash"></i></button></td>
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
