@extends('admin.layouts.app')
@section('title','Add Stock Outward')
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

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
input[type=number] {
    -moz-appearance: textfield !important;
}

.text-error { color: #F87171; font-size: 12.5px; margin-top: 6px; font-weight: 500; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }
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

.stock-info-bar { background: rgba(59, 130, 246, 0.12); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 8px; padding: 10px 16px; margin-top: 8px; font-size: 13px; color: #E2E8F0; display: none; }
.stock-info-bar.visible { display: block; }
.stock-info-bar strong { color: #60A5FA; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Stock Outward</h2>
        <p>Record material usage or stock issue from inventory.</p>
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
        <button type="button" class="type-btn active" id="btn-type-ref">Dispatch Against Stock Inward</button>
        <button type="button" class="type-btn" id="btn-type-manual">Manual Stock Outward</button>
    </div>

    <!-- REFERENCED DISPATCH FORM -->
    <form method="POST" action="{{ route('stock-outwards.store') }}" id="form-ref-dispatch" autocomplete="off">
        @csrf
        
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-file-invoice"></i> Select Reference IMIR</div>
            <div class="form-row" style="grid-template-columns: 1fr 2fr;">
                <div class="form-group">
                    <label class="form-label">IMIR Number <span>*</span></label>
                    <select name="stock_inward_number" id="stock_inward_number" class="form-control">
                        <option value="">Select IMIR Number</option>
                        @foreach($inwardNumbers as $num)
                            <option value="{{ $num }}" {{ request('stock_inward_number') == $num ? 'selected' : '' }}>{{ $num }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="si-info-block" style="display:none; align-self:center; font-size:13px; color:var(--text-secondary); margin-top:18px;">
                    Supplier: <strong id="lbl-si-supplier" style="color:var(--text-primary);"></strong> &nbsp;|&nbsp; PO Number: <strong id="lbl-si-po" style="color:var(--text-primary);"></strong> &nbsp;|&nbsp; IMIR Date: <strong id="lbl-si-date" style="color:var(--text-primary);"></strong> &nbsp;|&nbsp; Warehouse: <strong id="lbl-si-warehouse" style="color:var(--text-primary);"></strong>
                </div>
            </div>
        </div>

        <div id="ref-fields-container" style="display:none;">
            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-truck-dispatch"></i> Dispatch Details</div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Destination Project <span>*</span></label>
                        <select name="project_id" class="form-control">
                            <option value="">Select Project</option>
                            @foreach($projects as $prop)
                                <option value="{{ $prop->id }}" {{ old('project_id', $selectedProjectId ?? '')==$prop->id?'selected':'' }}>{{ $prop->project_name }} ({{ $prop->propertyMaster->property_name ?? 'Property' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Dispatch Date <span>*</span></label>
                        <input type="date" name="outward_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle Number</label>
                        <input type="text" name="vehicle_no" class="form-control" placeholder="Enter vehicle number...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Driver Name</label>
                        <input type="text" name="driver_name" class="form-control" placeholder="Enter driver name...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">LR Number (Lorry Receipt)</label>
                        <input type="text" name="lr_no" class="form-control" placeholder="Enter LR number...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Transport Name</label>
                        <input type="text" name="transport_name" class="form-control" placeholder="Enter transport company...">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-title"><i class="fa-solid fa-list"></i> Dispatch Quantity Validation</div>
                
                <div class="table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width:35%;">Material Name</th>
                                <th style="width:15%; text-align:right;">Accepted Qty</th>
                                <th style="width:15%; text-align:right;">Already Disp.</th>
                                <th style="width:15%; text-align:right;">Pending Disp.</th>
                                <th style="width:20%;">Dispatch Qty <span>*</span></th>
                            </tr>
                        </thead>
                        <tbody id="si-items-rows">
                            <!-- Loaded dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="form-group" style="margin-top:20px;">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" placeholder="Enter dispatch notes..."></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Outward</button>
                <a href="{{ route('stock-outwards.index') }}" class="btn-outline">Back</a>
            </div>
        </div>
    </form>

    <!-- MANUAL OUTWARD FORM -->
    <form method="POST" action="{{ route('stock-outwards.store') }}" id="form-manual-dispatch" style="display:none;" autocomplete="off">
        @csrf
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-arrow-up-from-bracket"></i> Outward Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="material_id">Material <span>*</span></label>
                    <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror" onchange="showStock(this)">
                        <option value="">-- Select Material --</option>
                        @foreach($materials as $m)
                            <option value="{{ $m->id }}"
                                data-stock="{{ $m->current_stock }}"
                                data-unit="{{ $m->unit }}"
                                data-min="{{ $m->minimum_stock }}"
                                {{ old('material_id')==$m->id?'selected':'' }}>
                                {{ $m->material_name }} ({{ $m->unit }}) — Stock: {{ number_format($m->current_stock,2) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="stock-info-bar" id="stockInfoBar">
                        Available: <strong id="stockVal">-</strong>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="outward_date">Outward Date <span>*</span></label>
                    <input type="date" name="outward_date" value="{{ old('outward_date',date('Y-m-d')) }}" class="form-control @error('outward_date') is-invalid @enderror">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="quantity">Quantity to Issue <span>*</span></label>
                    <input type="number" step="0.001" name="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" placeholder="Enter quantity" autocomplete="off">
                    <div style="font-size:12px;color:var(--text-secondary);margin-top:5px;">Cannot exceed available stock.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="project_id">Project <small style="font-weight:400;">(optional)</small></label>
                    <select name="project_id" class="form-control @error('project_id') is-invalid @enderror">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $p)<option value="{{ $p->id }}" {{ old('project_id', $selectedProjectId ?? '')==$p->id?'selected':'' }}>{{ $p->project_name }} ({{ $p->propertyMaster->property_name ?? 'Property' }})</option>@endforeach
                    </select>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-clipboard-list"></i> Usage Details</div>
            <div class="form-group">
                <label class="form-label" for="used_for">Used For</label>
                <input type="text" name="used_for" value="{{ old('used_for') }}" class="form-control @error('used_for') is-invalid @enderror" placeholder="e.g. Foundation work at Block A, Plastering 2nd Floor">
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" placeholder="Additional notes...">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-check"></i> Save Outward</button>
            <a href="{{ route('stock-outwards.index') }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
    function showStock(sel){
        const opt = sel.options[sel.selectedIndex];
        const bar = document.getElementById('stockInfoBar');
        const val = document.getElementById('stockVal');
        if(sel.value){
            const stock = parseFloat(opt.dataset.stock)||0;
            const unit  = opt.dataset.unit||'';
            const min   = parseFloat(opt.dataset.min)||0;
            val.textContent = stock.toLocaleString('en-IN',{minimumFractionDigits:3})+' '+unit;
            val.className   = stock <= min && min > 0 ? 'stock-low-warn' : '';
            bar.classList.add('visible');
        } else { bar.classList.remove('visible'); }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const btnRef = document.getElementById('btn-type-ref');
        const btnManual = document.getElementById('btn-type-manual');
        const formRef = document.getElementById('form-ref-dispatch');
        const formManual = document.getElementById('form-manual-dispatch');

        btnRef.addEventListener('click', function() {
            btnRef.classList.add('active');
            btnManual.classList.remove('active');
            formRef.style.display = 'block';
            formManual.style.display = 'none';
        });

        btnManual.addEventListener('click', function() {
            btnManual.classList.add('active');
            btnRef.classList.remove('active');
            formManual.style.display = 'block';
            formRef.style.display = 'none';
        });

        // SI selection dynamic loader
        const siSelect = document.getElementById('stock_inward_number');
        const siInfoBlock = document.getElementById('si-info-block');
        const refFieldsContainer = document.getElementById('ref-fields-container');
        const itemsRows = document.getElementById('si-items-rows');

        function fetchSiDetails(siNum) {
            if (!siNum) {
                siInfoBlock.style.display = 'none';
                refFieldsContainer.style.display = 'none';
                return;
            }

            fetch(`/stock-inwards/${siNum}/pending-outward-items`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('lbl-si-supplier').innerText = data.supplier_name;
                    document.getElementById('lbl-si-po').innerText = data.po_number;
                    document.getElementById('lbl-si-date').innerText = data.inward_date;
                    document.getElementById('lbl-si-warehouse').innerText = data.warehouse;

                    siInfoBlock.style.display = 'inline-block';
                    refFieldsContainer.style.display = 'block';
                    itemsRows.innerHTML = '';

                    let rowIndex = 0;
                    data.items.forEach(item => {
                        if (item.qty_pending > 0) {
                            const maxDispatch = Math.min(item.qty_pending, item.available_stock);

                            const tr = `
                                <tr class="item-row">
                                    <td>
                                        <strong>${item.material_name}</strong>
                                        <input type="hidden" name="items[${rowIndex}][material_id]" value="${item.material_id}">
                                    </td>
                                    <td style="text-align:right;">${item.qty_received} ${item.unit}</td>
                                    <td style="text-align:right;">${item.qty_dispatched} ${item.unit}</td>
                                    <td style="text-align:right; font-weight:600; color:#2563EB;">${item.qty_pending} ${item.unit} <br><small style="color:#64748B;">Inv. Avail: ${item.available_stock}</small></td>
                                    <td>
                                        <input type="number" name="items[${rowIndex}][qty_dispatch]" class="form-control qty-dispatch-input" value="${maxDispatch}" step="0.001" min="0.001" max="${maxDispatch}" required style="height:34px !important; padding:4px 8px !important;">
                                    </td>
                                </tr>
                            `;
                            itemsRows.insertAdjacentHTML('beforeend', tr);
                            rowIndex++;
                        }
                    });

                    // Add validation event listeners
                    document.querySelectorAll('.qty-dispatch-input').forEach(input => {
                        input.addEventListener('input', function() {
                            const max = parseFloat(this.max) || 0;
                            const val = parseFloat(this.value) || 0;
                            if (val > max) {
                                alert('Dispatch quantity cannot exceed available stock or pending inward quantity.');
                                this.value = max;
                            }
                        });
                    });
                });
        }

        siSelect.addEventListener('change', function() {
            fetchSiDetails(this.value);
        });

        if (siSelect.value) {
            fetchSiDetails(siSelect.value);
        }

        const sel = document.getElementById('material_id');
        if(sel && sel.value) showStock(sel);
    });
</script>
@endsection
