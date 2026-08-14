@extends('admin.layouts.app')
@section('title','Add Stock Outward')
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
    
    .stock-info-bar{background:#FFFBEB;border:1px solid rgba(212,175,55,0.3);border-radius:8px;padding:10px 16px;margin-top:6px;font-size:13px;color:var(--text-primary);display:none;}
    .stock-info-bar.visible{display:block;}
    .stock-info-bar strong{color:var(--gold);}
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
    <form method="POST" action="{{ route('stock-outwards.store') }}" id="form-ref-dispatch">
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
    <form method="POST" action="{{ route('stock-outwards.store') }}" id="form-manual-dispatch" style="display:none;">
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
                    <input type="number" step="0.001" name="quantity" value="{{ old('quantity') }}" class="form-control @error('quantity') is-invalid @enderror" placeholder="Enter quantity">
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
