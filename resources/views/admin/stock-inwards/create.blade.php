@extends('admin.layouts.app')
@section('title','Add Stock Inward')
@section('page-title','Inventory Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 11px 22px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 14px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 12px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important; max-width: 960px; margin: 0 auto 28px;
}

.section-title {
    font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
    color: #60A5FA !important; margin-bottom: 20px; padding-bottom: 10px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important; display: flex; align-items: center; gap: 8px;
}

.form-section { margin-bottom: 28px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){.form-row-3{grid-template-columns:1fr 1fr;}}
@media(max-width:576px){.form-row,.form-row-3{grid-template-columns:1fr;gap:0;}}

.form-label { display: block; font-size: 13.5px; font-weight: 700; color: #FFFFFF !important; margin-bottom: 8px; }
.form-label span { color: #F87171; }

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

.text-error { color: #F87171; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
.calc-hint { font-size: 11.5px; color: #60A5FA; margin-top: 5px; font-weight: 600; }
.form-actions { display: flex; align-items: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.type-switcher { display: flex; gap: 12px; margin-bottom: 25px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 15px; }
.type-btn {
    padding: 10px 20px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.15);
    background: rgba(16, 22, 34, 0.65); color: #CBD5E1; font-size: 13.5px; font-weight: 700; cursor: pointer; transition: all .2s ease;
}
.type-btn:hover { background: rgba(255, 255, 255, 0.10); color: #FFF; }
.type-btn.active { background: #2563EB !important; border-color: #3B82F6 !important; color: #FFF !important; box-shadow: 0 4px 16px rgba(37, 99, 235, 0.38); }

.table-container { width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); }
.items-table { width: 100%; border-collapse: collapse; margin-top: 0; font-size: 13.5px; }
.items-table th { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; padding: 12px 14px; font-weight: 800; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; }
.items-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); color: #E2E8F0 !important; vertical-align: middle; }

.summary-box { width: 300px; margin-left: auto; margin-top: 20px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; padding: 16px; background: rgba(16, 22, 34, 0.65); }
.summary-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 13px; color: #CBD5E1; }
.summary-row:last-child { border-bottom: none; font-weight: 700; font-size: 15px; color: #34D399; }
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
        <button type="button" class="type-btn active" id="btn-type-po" onclick="switchInwardType('po')">Receive Against Purchase Order</button>
        <button type="button" class="type-btn" id="btn-type-manual" onclick="switchInwardType('manual')">Manual Stock Inward</button>
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
                    <div class="form-group">
                        <label class="form-label">Contractor <small style="font-weight:400;">(optional)</small></label>
                        <select name="contractor_id" id="po_contractor_id" class="form-control">
                            <option value="">-- Select Contractor --</option>
                            @foreach($contractors as $c)
                                <option value="{{ $c->id }}">{{ $c->contractor_name }}</option>
                            @endforeach
                        </select>
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
                <div class="form-group">
                    <label class="form-label" for="contractor_id">Contractor <small style="font-weight:400;">(optional)</small></label>
                    <select name="contractor_id" id="contractor_id" class="form-control @error('contractor_id') is-invalid @enderror">
                        <option value="">-- Select Contractor --</option>
                        @foreach($contractors as $c)<option value="{{ $c->id }}" data-project-id="{{ $c->project_id ?? '' }}" {{ old('contractor_id')==$c->id?'selected':'' }}>{{ $c->contractor_name }}</option>@endforeach
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

    function switchInwardType(type) {
        const btnPo = document.getElementById('btn-type-po');
        const btnManual = document.getElementById('btn-type-manual');
        const formPo = document.getElementById('form-po-receive');
        const formManual = document.getElementById('form-manual-receive');

        if (!btnPo || !btnManual || !formPo || !formManual) return;

        if (type === 'manual') {
            btnManual.classList.add('active');
            btnPo.classList.remove('active');
            formPo.style.display = 'none';
            formManual.style.display = 'block';
            if (typeof syncManualContractors === 'function') {
                syncManualContractors();
            }
        } else {
            btnPo.classList.add('active');
            btnManual.classList.remove('active');
            formPo.style.display = 'block';
            formManual.style.display = 'none';
        }
    }

    function syncManualContractors() {
        const manualProjSelect = document.getElementById('project_id');
        const manualConSelect  = document.getElementById('contractor_id');
        if (!manualProjSelect || !manualConSelect) return;
        const pId = manualProjSelect.value;
        let firstMatch = '';
        let matchCount = 0;

        Array.from(manualConSelect.options).forEach(opt => {
            if (!opt.value) {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
                return;
            }
            const optPId = opt.getAttribute('data-project-id') || opt.dataset.projectId || '';
            if (!pId || !optPId || optPId === pId) {
                opt.hidden = false;
                opt.disabled = false;
                opt.style.display = '';
                if (pId && optPId === pId) {
                    matchCount++;
                    if (!firstMatch) firstMatch = opt.value;
                }
            } else {
                opt.hidden = true;
                opt.disabled = true;
                opt.style.display = 'none';
            }
        });

        const currentSelected = manualConSelect.selectedOptions ? manualConSelect.selectedOptions[0] : null;
        if (currentSelected && (currentSelected.hidden || currentSelected.disabled || currentSelected.style.display === 'none')) {
            manualConSelect.value = firstMatch || '';
        } else if (pId && matchCount > 0 && !manualConSelect.value) {
            manualConSelect.value = firstMatch;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnPo = document.getElementById('btn-type-po');
        const btnManual = document.getElementById('btn-type-manual');

        if (btnPo) {
            btnPo.addEventListener('click', function() { switchInwardType('po'); });
        }
        if (btnManual) {
            btnManual.addEventListener('click', function() { switchInwardType('manual'); });
        }

        // Project-to-Contractor cascading for Manual form
        const manualProjSelect = document.getElementById('project_id');
        const manualConSelect  = document.getElementById('contractor_id');

        if (manualProjSelect && manualConSelect) {
            manualProjSelect.addEventListener('change', syncManualContractors);
            manualConSelect.addEventListener('change', function() {
                const opt = this.selectedOptions ? this.selectedOptions[0] : null;
                const optPId = opt ? (opt.getAttribute('data-project-id') || opt.dataset.projectId) : null;
                if (optPId && (!manualProjSelect.value || manualProjSelect.value !== optPId)) {
                    manualProjSelect.value = optPId;
                }
            });
            syncManualContractors();
        }

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
                    if (data.contractor_id && document.getElementById('po_contractor_id')) {
                        document.getElementById('po_contractor_id').value = data.contractor_id;
                    }

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
