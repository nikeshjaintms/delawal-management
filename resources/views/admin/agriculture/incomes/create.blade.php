@extends('admin.layouts.app')
@section('title', 'Record Agriculture Income / Crop Sale')
@section('page-title', 'Agriculture Management')
@section('content')
<style>
/* ── Luxury Dark Glass Form System ── */
.form-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 24px; flex-wrap: wrap; gap: 15px; }
.form-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.form-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-back {
    background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 10px 20px;
    border-radius: 10px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    transition: all .25s ease;
}
.btn-back:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }

.card-box {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.94) 0%, rgba(20, 28, 48, 0.97) 100%) !important;
    backdrop-filter: blur(28px) saturate(200%) !important;
    -webkit-backdrop-filter: blur(28px) saturate(200%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 24px !important; padding: 32px !important;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.14) !important; margin-bottom: 30px;
}
.card-box:hover {
    background: linear-gradient(145deg, rgba(15, 23, 42, 0.94) 0%, rgba(20, 28, 48, 0.97) 100%) !important;
    border-color: rgba(255, 255, 255, 0.18) !important;
    transform: none !important;
}

.section-divider {
    font-size: 13px; font-weight: 800; color: #34D399 !important; text-transform: uppercase;
    letter-spacing: 1px; margin: 28px 0 18px 0; display: flex; align-items: center; gap: 10px;
}
.section-divider::after { content: ''; flex: 1; height: 1px; background: rgba(255, 255, 255, 0.10); }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group.full-width { grid-column: 1 / -1; }

.form-label { font-size: 13px; font-weight: 700; color: #E2E8F0 !important; display: flex; align-items: center; gap: 4px; }
.form-label .req { color: #F87171; font-weight: 800; }

.form-control-glass {
    width: 100%; padding: 12px 16px !important; background: rgba(10, 15, 28, 0.90) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.16) !important; border-radius: 12px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .25s ease;
    box-sizing: border-box;
}
.form-control-glass:focus {
    border-color: #34D399 !important; background: rgba(10, 15, 28, 0.98) !important;
    box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.25) !important;
}
select.form-control-glass option { background: #0F172A !important; color: #FFFFFF !important; }

.calc-summary-banner {
    background: rgba(34, 197, 94, 0.10); border: 1px solid rgba(52, 211, 153, 0.30);
    border-radius: 16px; padding: 20px 24px; margin-top: 24px; display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: center;
}
.calc-stat { display: flex; flex-direction: column; gap: 4px; }
.calc-stat span { font-size: 11px; font-weight: 800; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.8px; }
.calc-stat strong { font-size: 20px; font-weight: 800; }

.btn-submit {
    background: #10B981 !important; color: #FFFFFF !important; padding: 14px 32px;
    border-radius: 12px; font-size: 15px; font-weight: 800; border: 1px solid #34D399 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 6px 20px rgba(16, 185, 129, 0.40);
    display: inline-flex; align-items: center; gap: 10px;
}
.btn-submit:hover { background: #059669 !important; transform: translateY(-2px); box-shadow: 0 8px 26px rgba(16, 185, 129, 0.55); }
</style>

<div class="form-header">
    <div class="form-title">
        <h2><i class="fa-solid fa-wheat-awn" style="color: #34D399;"></i> Record Agriculture Income / Crop Sale</h2>
        <p>Log harvested crop sales, APMC mandi transactions, by-product sales, subsidies, or lease income.</p>
    </div>
    <a href="{{ route('agriculture.incomes.index') }}" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to Incomes</a>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('agriculture.incomes.store') }}" enctype="multipart/form-data">
        @csrf

        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="section-divider"><i class="fa-solid fa-building"></i> Company / Multi-Firm Setup</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label class="form-label">Firm Ownership <span class="req">*</span></label>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    @foreach($firms as $f)
                        <label style="display: flex; align-items: center; gap: 8px; color: #FFFFFF; font-size: 14px; cursor: pointer;">
                            <input type="checkbox" name="firm_ids[]" value="{{ $f->id }}" 
                                {{ (is_array(old('firm_ids')) && in_array($f->id, old('firm_ids'))) || $loop->first ? 'checked' : '' }}
                                style="width: 17px; height: 17px; accent-color: #10B981;">
                            {{ $f->firm_name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="section-divider"><i class="fa-solid fa-tractor"></i> 1. Farm & Product Sale Details</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Select Farm / Land <span class="opt" style="font-weight: 500; color: #94A3B8; font-size: 12px;">(optional)</span></label>
                <select name="farm_id" id="farmSelect" class="form-control-glass" onchange="onFarmSelect()">
                    <option value="">-- None / General Farm (Optional) --</option>
                    @foreach($farms as $f)
                        <option value="{{ $f->id }}" data-crop="{{ $f->crop_activity }}" {{ old('farm_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->farm_name }} ({{ $f->village ?: 'Direct' }})
                        </option>
                    @endforeach
                </select>
                @error('farm_id')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Income Date <span class="req">*</span></label>
                <input type="date" name="income_date" value="{{ old('income_date', date('Y-m-d')) }}" class="form-control-glass" required>
                @error('income_date')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Income Type <span class="req">*</span></label>
                <select name="income_type" class="form-control-glass" required>
                    @foreach($incomeTypes as $it)
                        <option value="{{ $it }}" {{ old('income_type', 'Crop Sale') == $it ? 'selected' : '' }}>{{ $it }}</option>
                    @endforeach
                </select>
                @error('income_type')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Crop / Product Sold <span class="req">*</span></label>
                <input type="text" name="crop_product" id="cropProductInput" value="{{ old('crop_product') }}" class="form-control-glass" placeholder="e.g. Cotton, Wheat, Sugarcane, Mangoes..." required>
                @error('crop_product')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-user-tag"></i> 2. Buyer / Customer Information</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Registered Customer / Buyer</label>
                <select name="customer_id" class="form-control-glass">
                    <option value="">-- None / Select Customer --</option>
                    @foreach($customers as $cust)
                        <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }} ({{ $cust->phone }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Direct Buyer Name / Mandi Trader</label>
                <input type="text" name="buyer_name" value="{{ old('buyer_name') }}" class="form-control-glass" placeholder="e.g. APMC Trader, Shailesh Patel...">
            </div>

            <div class="form-group">
                <label class="form-label">Connected Project (Optional)</label>
                <select name="project_id" class="form-control-glass">
                    <option value="">-- None / Select Project --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->project_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="section-divider"><i class="fa-solid fa-calculator"></i> 3. Quantity, Rate & Payment Calculation</div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Quantity Sold <span class="req">*</span></label>
                <input type="number" step="0.01" min="0.01" name="quantity" id="qtyInput" value="{{ old('quantity', 1) }}" class="form-control-glass" placeholder="e.g. 50" required oninput="calcIncome()">
                @error('quantity')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Unit of Measure <span class="req">*</span></label>
                <select name="unit" class="form-control-glass" required>
                    @foreach($units as $u)
                        <option value="{{ $u }}" {{ old('unit', 'Quintal') == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
                @error('unit')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Rate per Unit (₹) <span class="req">*</span></label>
                <input type="number" step="0.01" min="0.01" name="rate" id="rateInput" value="{{ old('rate', 0) }}" class="form-control-glass" placeholder="e.g. 7200.00" required oninput="calcIncome()">
                @error('rate')<span style="color:#F87171;font-size:12px;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Payment Received (₹)</label>
                <input type="number" step="0.01" min="0" name="payment_received" id="receivedInput" value="{{ old('payment_received') }}" class="form-control-glass" placeholder="Leave empty for full payment" oninput="calcIncome()">
            </div>

            <div class="form-group">
                <label class="form-label">Payment Mode</label>
                <select name="payment_mode_id" class="form-control-glass">
                    <option value="">-- Cash / Select Mode --</option>
                    @foreach($paymentModes as $pm)
                        <option value="{{ $pm->id }}" {{ old('payment_mode_id') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Payment Status <span class="req">*</span></label>
                <select name="payment_status" id="statusSelect" class="form-control-glass" required>
                    <option value="Paid" {{ old('payment_status', 'Paid') == 'Paid' ? 'selected' : '' }}>Paid (Fully Received)</option>
                    <option value="Partial" {{ old('payment_status') == 'Partial' ? 'selected' : '' }}>Partial</option>
                    <option value="Pending" {{ old('payment_status') == 'Pending' ? 'selected' : '' }}>Pending (Unpaid)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Invoice No / Bill No</label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" class="form-control-glass" placeholder="e.g. APMC-2026-001">
            </div>

            <div class="form-group">
                <label class="form-label">Sale Invoice / Weight Slip Attachment</label>
                <input type="file" name="attachment" class="form-control-glass" accept="image/*,.pdf">
            </div>

            <div class="form-group full-width">
                <label class="form-label">Notes & Remarks</label>
                <textarea name="notes" rows="3" class="form-control-glass" placeholder="Add harvest notes, quality grade, Mandi deduction details...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Live Calculation Banner -->
        <div class="calc-summary-banner">
            <div class="calc-stat">
                <span>Calculated Total Billed</span>
                <strong id="dispTotal" style="color: #34D399;">₹0.00</strong>
            </div>
            <div class="calc-stat">
                <span>Payment Received</span>
                <strong id="dispReceived" style="color: #60A5FA;">₹0.00</strong>
            </div>
            <div class="calc-stat">
                <span>Pending Balance</span>
                <strong id="dispPending" style="color: #F87171;">₹0.00</strong>
            </div>
        </div>

        <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 14px;">
            <a href="{{ route('agriculture.incomes.index') }}" class="btn-back">Cancel</a>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-check-circle"></i> Save Crop Sale / Income</button>
        </div>
    </form>
</div>

<script>
function onFarmSelect() {
    const sel = document.getElementById('farmSelect');
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.crop && !document.getElementById('cropProductInput').value) {
        document.getElementById('cropProductInput').value = opt.dataset.crop;
    }
}

function calcIncome() {
    const qty = parseFloat(document.getElementById('qtyInput').value) || 0;
    const rate = parseFloat(document.getElementById('rateInput').value) || 0;
    const total = qty * rate;

    let recInput = document.getElementById('receivedInput');
    let rec = parseFloat(recInput.value);
    
    // If empty or NaN, default received is total
    if (isNaN(rec)) {
        rec = total;
    }

    const pending = Math.max(0, total - rec);

    document.getElementById('dispTotal').innerText = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispReceived').innerText = '₹' + rec.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('dispPending').innerText = '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Auto update status dropdown
    const statusSel = document.getElementById('statusSelect');
    if (total > 0 && rec >= total) {
        statusSel.value = 'Paid';
    } else if (rec > 0 && rec < total) {
        statusSel.value = 'Partial';
    } else if (rec === 0 && total > 0) {
        statusSel.value = 'Pending';
    }
}

document.addEventListener('DOMContentLoaded', calcIncome);
</script>
@endsection
