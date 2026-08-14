@extends('admin.layouts.app')
@section('title','Add Stock Inward')
@section('page-title','Inventory Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:960px;margin:0 auto;}
    .section-title{font-size:13px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border-color);}
    .form-section{margin-bottom:28px;}
    .form-group{margin-bottom:20px;}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;}
    @media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}
    .form-label{display:block;font-size:13.5px;font-weight:600;color:var(--text-primary);margin-bottom:8px;}
    .form-label span{color:#EF4444;}
    
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
        border-color: var(--gold) !important;
        box-shadow: 0 0 0 3px var(--gold-light) !important;
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

    textarea.form-control{resize:vertical;min-height:80px;height:auto !important;}
    .text-error{color:#EF4444;font-size:12.5px;margin-top:6px;font-weight:500;}
    .calc-hint{font-size:11.5px;color:var(--gold);margin-top:5px;font-weight:600;}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:30px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);font-family:var(--font-primary);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);color:#FFF;}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;transition:var(--transition);display:inline-flex;}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}

    .type-switcher {display:flex; gap:15px; margin-bottom:25px; border-bottom:1.5px solid var(--border-color); padding-bottom:15px;}
    .type-btn {padding:8px 18px; border-radius:8px; border:1px solid var(--border-color); background:#fff; font-size:13.5px; font-weight:600; cursor:pointer;}
    .type-btn.active {background:var(--gold); border-color:var(--gold); color:#fff;}

    .items-table {width:100%; border-collapse:collapse; margin-top:15px; font-size:13px;}
    .items-table th {background:#F9FAFB; padding:10px; font-weight:600; border-bottom:1.5px solid var(--border-color); text-transform:uppercase; font-size:11px; letter-spacing:0.5px;}
    .items-table td {padding:10px 8px; border-bottom:1px solid #F1F5F9; vertical-align:middle;}
    
    .summary-box {width:300px; margin-left:auto; margin-top:20px; border:1px solid var(--border-color); border-radius:8px; padding:16px; background:#F9FAFB;}
    .summary-row {display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #E2E8F0; font-size:13px;}
    .summary-row:last-child {border-bottom:none; font-weight:700; font-size:14.5px; color:#059669;}
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Stock Inward</h2>
        <p>Record a new material purchase or stock receipt.</p>
    </div>
</div>

@if(session('error'))
<div class="alert alert-danger" style="background:#FEE2E2; color:#B91C1C; padding:12px; border-radius:8px; margin-bottom:20px;">
    {{ session('error') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger" style="background:#FEE2E2; color:#B91C1C; padding:12px; border-radius:8px; margin-bottom:20px;">
    <ul style="margin:0; padding-left:20px;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card-box">
    <div class="type-switcher">
        <button type="button" class="type-btn active" id="btn-type-po">Receive Against Purchase Order</button>
        <button type="button" class="type-btn" id="btn-type-manual">Manual Stock Inward</button>
    </div>

    <!-- PO FORM -->
    <form method="POST" action="{{ route('stock-inwards.store') }}" id="form-po-receive">
        @csrf
        
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-invoice"></i> Select Purchase Order</div>
            <div class="form-row" style="grid-template-columns: 1fr 2fr;">
                <div class="form-group">
                    <label class="form-label">PO Number <span>*</span></label>
                    <select name="purchase_order_id" id="purchase_order_id" class="form-control">
                        <option value="">Select PO</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ request('purchase_order_id') == $po->id ? 'selected' : '' }}>{{ $po->po_number }} ({{ $po->vendor->name ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="vendor-info-block" style="display:none; align-self:center; font-size:13px; color:var(--text-secondary); margin-top:18px;">
                    Supplier: <strong id="lbl-supplier-name" style="color:var(--text-primary);"></strong> &nbsp;|&nbsp; PO Date: <strong id="lbl-po-date" style="color:var(--text-primary);"></strong>
                </div>
            </div>
        </div>

        <div id="po-fields-container" style="display:none;">
            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-arrow-down-to-bracket"></i> Receipt Details</div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Receive Date <span>*</span></label>
                        <input type="date" name="inward_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bill / Invoice No</label>
                        <input type="text" name="bill_no" class="form-control" placeholder="Enter invoice number...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Challan Number</label>
                        <input type="text" name="challan_no" class="form-control" placeholder="Enter challan number...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" name="vehicle_no" class="form-control" placeholder="Enter vehicle number...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Warehouse Name / Location</label>
                        <input type="text" name="warehouse" class="form-control" placeholder="Enter warehouse name...">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-list"></i> Order Items</div>
                
                <div class="table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width:25%;">Material Description</th>
                                <th style="width:10%; text-align:right;">Ordered</th>
                                <th style="width:10%; text-align:right;">Already Rec.</th>
                                <th style="width:10%; text-align:right;">Pending</th>
                                <th style="width:12%;">Rec. Qty <span>*</span></th>
                                <th style="width:10%;">Dmg. Qty</th>
                                <th style="width:11%; text-align:right;">Unit Rate</th>
                                <th style="width:12%; text-align:right;">Line Total</th>
                            </tr>
                        </thead>
                        <tbody id="po-items-rows">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="summary-box">
                    <div class="summary-row"><span>Sub Total:</span><span>₹<span id="lbl-sub-total">0.00</span></span></div>
                    <div class="summary-row"><span>Discount:</span><span>₹<span id="lbl-discount">0.00</span></span></div>
                    <div class="summary-row"><span>Taxable Value:</span><span>₹<span id="lbl-taxable">0.00</span></span></div>
                    <div class="summary-row tax-intrastate"><span>CGST:</span><span>₹<span id="lbl-cgst">0.00</span></span></div>
                    <div class="summary-row tax-intrastate"><span>SGST:</span><span>₹<span id="lbl-sgst">0.00</span></span></div>
                    <div class="summary-row tax-interstate" style="display:none;"><span>IGST:</span><span>₹<span id="lbl-igst">0.00</span></span></div>
                    <div class="summary-row"><span>Grand Total:</span><span>₹<span id="lbl-grand-total">0.00</span></span></div>
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" placeholder="Enter receipt notes..."></textarea>
                </div>
            </div>

            <div class="form-actions" style="margin-top:20px;">
                <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Inward</button>
                <a href="{{ route('stock-inwards.index') }}" class="btn-outline">Back</a>
            </div>
        </div>
    </form>

    <!-- MANUAL FORM -->
    <form method="POST" action="{{ route('stock-inwards.store') }}" id="form-manual-receive" style="display:none;">
        @csrf
        @include('admin.components.firm-select')

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-arrow-down-to-bracket"></i> Inward Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_id">Material <span>*</span></label>
                    <select name="material_id" class="form-control @error('material_id') is-invalid @enderror">
                        <option value="">-- Select Material --</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}" {{ old('material_id')==$m->id?'selected':'' }}>{{ $m->material_name }} ({{ $m->unit }}) — Stock: {{ number_format($m->current_stock,2) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="inward_date">Inward Date <span>*</span></label>
                    <input type="date" name="inward_date" value="{{ old('inward_date',date('Y-m-d')) }}" class="form-control @error('inward_date') is-invalid @enderror">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project <small style="font-weight:400;">(optional)</small></label>
                    <select name="project_id" id="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id', $selectedProjectId ?? '')==$p->id?'selected':'' }}>{{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Quantity & Rate</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity <span>*</span></label>
                    <input type="number" step="0.001" name="quantity" id="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" placeholder="Enter quantity" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label" for="rate">Rate per Unit (₹)</label>
                    <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate') }}" class="form-control @error('rate') is-invalid @enderror" placeholder="Enter rate" oninput="calcTotal()">
                </div>
                <div class="form-group">
                    <label class="form-label">Total Amount (₹)</label>
                    <input type="text" id="total_display" class="form-control" style="background:#F9FAFB;cursor:default;" readonly placeholder="Auto-calculated">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Qty × Rate</div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-truck"></i> Supplier & Bill</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="supplier_name">Supplier Name</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" class="form-control" placeholder="Enter supplier name">
                </div>
                <div class="form-group">
                    <label class="form-label" for="bill_no">Bill / Invoice No</label>
                    <input type="text" name="bill_no" value="{{ old('bill_no') }}" class="form-control" placeholder="Enter bill number">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" class="form-control" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Inward</button>
            <a href="{{ route('stock-inwards.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
    function calcTotal(){
        const q=parseFloat(document.getElementById('quantity').value)||0;
        const r=parseFloat(document.getElementById('rate').value)||0;
        document.getElementById('total_display').value = q&&r ? '₹'+(q*r).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}) : '';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnPo = document.getElementById('btn-type-po');
        const btnManual = document.getElementById('btn-type-manual');
        const formPo = document.getElementById('form-po-receive');
        const formManual = document.getElementById('form-manual-receive');

        btnPo.addEventListener('click', function() {
            btnPo.classList.add('active');
            btnManual.classList.remove('active');
            formPo.style.display = 'block';
            formManual.style.display = 'none';
        });

        btnManual.addEventListener('click', function() {
            btnManual.classList.add('active');
            btnPo.classList.remove('active');
            formManual.style.display = 'block';
            formPo.style.display = 'none';
        });

        // PO selection dynamic loader
        const poSelect = document.getElementById('purchase_order_id');
        const vendorInfoBlock = document.getElementById('vendor-info-block');
        const poFieldsContainer = document.getElementById('po-fields-container');
        const itemsRows = document.getElementById('po-items-rows');
        let poData = null;

        function fetchPoDetails(poId) {
            if (!poId) {
                vendorInfoBlock.style.display = 'none';
                poFieldsContainer.style.display = 'none';
                return;
            }

            fetch(`/purchase-orders/${poId}/pending-items`)
                .then(res => res.json())
                .then(data => {
                    poData = data;
                    document.getElementById('lbl-supplier-name').innerText = data.vendor_name;
                    document.getElementById('lbl-po-date').innerText = data.po_date;

                    vendorInfoBlock.style.display = 'inline-block';
                    poFieldsContainer.style.display = 'block';
                    itemsRows.innerHTML = '';

                    let rowIndex = 0;
                    data.items.forEach(item => {
                        if (item.qty_pending > 0) {
                            const tr = `
                                <tr class="item-row">
                                    <td>
                                        <strong>${item.material_name}</strong>
                                        <input type="hidden" name="items[${rowIndex}][material_id]" value="${item.material_id}">
                                        <input type="hidden" name="items[${rowIndex}][qty_ordered]" value="${item.qty_ordered}">
                                        <input type="hidden" name="items[${rowIndex}][discount_pct]" class="discount-pct-input" value="${item.discount_pct}">
                                        <input type="hidden" name="items[${rowIndex}][gst_pct]" class="gst-pct-input" value="${item.gst_pct}">
                                        <input type="hidden" name="items[${rowIndex}][rate]" class="rate-input" value="${item.rate}">
                                    </td>
                                    <td style="text-align:right;">${item.qty_ordered} ${item.unit}</td>
                                    <td style="text-align:right;">${item.qty_received} ${item.unit}</td>
                                    <td style="text-align:right; font-weight:600; color:#2563EB;">${item.qty_pending} ${item.unit}</td>
                                    <td>
                                        <input type="number" name="items[${rowIndex}][qty_received]" class="form-control qty-rec-input" value="${item.qty_pending}" step="0.001" min="0.001" max="${item.qty_pending}" required style="height:34px !important; padding:4px 8px !important;">
                                    </td>
                                    <td>
                                        <input type="number" name="items[${rowIndex}][qty_damaged]" class="form-control qty-dmg-input" value="0" step="0.001" min="0" required style="height:34px !important; padding:4px 8px !important;">
                                    </td>
                                    <td style="text-align:right;">₹${item.rate}</td>
                                    <td style="text-align:right; font-weight:700;"><span class="line-total-span">₹0.00</span></td>
                                </tr>
                            `;
                            itemsRows.insertAdjacentHTML('beforeend', tr);
                            rowIndex++;
                        }
                    });

                    calculateTotals();
                });
        }

        function calculateTotals() {
            let subTotal = 0;
            let totalDiscount = 0;
            let totalTaxable = 0;
            let totalGst = 0;

            document.querySelectorAll('#po-items-rows .item-row').forEach(row => {
                const qtyRec = parseFloat(row.querySelector('.qty-rec-input').value) || 0;
                const qtyDmg = parseFloat(row.querySelector('.qty-dmg-input').value) || 0;
                const rate = parseFloat(row.querySelector('.rate-input').value) || 0;
                const discPct = parseFloat(row.querySelector('.discount-pct-input').value) || 0;
                const gstPct = parseFloat(row.querySelector('.gst-pct-input').value) || 0;

                if (qtyDmg > qtyRec) {
                    row.querySelector('.qty-dmg-input').value = qtyRec;
                }

                const rowSub = qtyRec * rate;
                const rowDisc = rowSub * (discPct / 100);
                const rowTaxable = rowSub - rowDisc;
                const rowGst = rowTaxable * (gstPct / 100);
                const rowTotal = rowTaxable + rowGst;

                row.querySelector('.line-total-span').innerText = '₹' + rowTotal.toFixed(2);

                subTotal += rowSub;
                totalDiscount += rowDisc;
                totalTaxable += rowTaxable;
                totalGst += rowGst;
            });

            document.getElementById('lbl-sub-total').innerText = subTotal.toFixed(2);
            document.getElementById('lbl-discount').innerText = totalDiscount.toFixed(2);
            document.getElementById('lbl-taxable').innerText = totalTaxable.toFixed(2);

            const isInterstate = poData ? poData.is_interstate : false;

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

        poSelect.addEventListener('change', function() {
            fetchPoDetails(this.value);
        });

        itemsRows.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-rec-input') || e.target.classList.contains('qty-dmg-input')) {
                calculateTotals();
            }
        });

        if (poSelect.value) {
            fetchPoDetails(poSelect.value);
        }
    });
</script>
@endsection
