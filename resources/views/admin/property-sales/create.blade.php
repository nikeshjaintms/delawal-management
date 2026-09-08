@extends('admin.layouts.app')
@section('title', 'Add Property Sale')
@section('page-title', 'Property Sales')
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
    max-width: 960px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 13px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 24px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3 { grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
.form-hint { font-size: 12px; color: #94A3B8 !important; margin-top: 5px; }

.form-control {
    width: 100%; padding: 10px 14px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important; border-radius: 10px !important;
    font-size: 14px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
select.form-control option { background: #101622 !important; color: #FFFFFF !important; }
.form-control::placeholder { color: #94A3B8 !important; }
.form-control:focus { border-color: #3B82F6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important; }
.form-control[readonly] { background: rgba(16, 22, 34, 0.40) !important; color: #94A3B8 !important; border-color: rgba(255, 255, 255, 0.08) !important; cursor: not-allowed; }
textarea.form-control { resize: vertical; min-height: 85px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.calc-hint { font-size: 11.5px; color: #FBBF24 !important; margin-top: 5px; font-weight: 600; }

.form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }
.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 10px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 16px rgba(37,99,235,0.35);
    display: inline-flex; align-items: center; gap: 8px; text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; transform: translateY(-2px); box-shadow: 0 6px 22px rgba(37,99,235,0.50); }
.btn-outline {
    border: 1px solid rgba(255, 255, 255, 0.15) !important; background: rgba(255, 255, 255, 0.05) !important;
    color: #CBD5E1 !important; padding: 11px 24px; border-radius: 10px; text-decoration: none !important;
    font-size: 14px; font-weight: 600; transition: all .2s ease;
}
.btn-outline:hover { background: rgba(255, 255, 255, 0.10) !important; color: #FFFFFF !important; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Add Property Sale</h2>
        <p>Record a direct property sale or project unit booking transaction.</p>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('property-sales.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Parties --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-handshake"></i> Sale Parties & Property</div>
            @include('admin.components.firm-select')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project / Scheme</label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">— All Properties (Direct & Projects) —</option>
                        <option value="direct" {{ old('project_id') == 'direct' ? 'selected' : '' }}>📌 Standalone / Direct Properties (No Project)</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Filter by project or select direct standalone properties.</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="property_id">Property / Unit <span>*</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                        <option value="">-- Select Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}"
                                    data-project-id="{{ $property->project_id ?? '' }}"
                                    data-project="{{ $property->project->project_name ?? ($property->project->propertyMaster->property_name ?? '') }}"
                                    data-price="{{ $property->price ?? '' }}"
                                    {{ old('property_id', request('property_id')) == $property->id ? 'selected' : '' }}>
                                {{ $property->property_name }}
                                @if(!$property->project_id) [Direct Property / Standalone] @endif
                                @if($property->unit_no) (Unit: {{ $property->unit_no }}) @endif
                                @if($property->property_code) [{{ $property->property_code }}] @endif
                                — {{ ucfirst($property->status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="customer_id">Customer <span>*</span></label>
                    <select name="customer_id" id="customer_id" class="form-control @error('customer_id') is-invalid @enderror" required>
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} — {{ $customer->mobile }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_date">Sale Date</label>
                    <input type="date" name="sale_date" id="sale_date"
                           value="{{ old('sale_date', date('Y-m-d')) }}" class="form-control @error('sale_date') is-invalid @enderror">
                    @error('sale_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="broker_id">Broker</label>
                    <select name="broker_id" id="broker_id" class="form-control @error('broker_id') is-invalid @enderror">
                        <option value="">-- Select Broker (Optional) --</option>
                        @foreach($brokers as $broker)
                            <option value="{{ $broker->id }}" {{ old('broker_id') == $broker->id ? 'selected' : '' }}>
                                {{ $broker->name }} — {{ $broker->mobile }}
                            </option>
                        @endforeach
                    </select>
                    @error('broker_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Details</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="sale_amount">Total Sale Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="sale_amount" id="sale_amount"
                           value="{{ old('sale_amount') }}" class="form-control @error('sale_amount') is-invalid @enderror"
                           placeholder="Enter total sale amount" oninput="calcRemaining()" required>
                    @error('sale_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="booking_amount">Paid / Booking Amount (₹)</label>
                    <input type="number" step="0.01" name="booking_amount" id="booking_amount"
                           value="{{ old('booking_amount', '0.00') }}" class="form-control @error('booking_amount') is-invalid @enderror"
                           placeholder="Enter advance / booking amount" oninput="calcRemaining()">
                    @error('booking_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="remaining_amount">Remaining Amount (₹)</label>
                    <input type="number" step="0.01" name="remaining_amount" id="remaining_amount"
                           value="{{ old('remaining_amount') }}" class="form-control @error('remaining_amount') is-invalid @enderror"
                           placeholder="Auto-calculated" readonly>
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> Auto = Sale − Paid</div>
                    @error('remaining_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status & Documents</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_status">Payment Status <span>*</span></label>
                    <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                        @foreach(['pending' => 'Pending', 'partial' => 'Partial', 'paid' => 'Paid'] as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_status', 'pending') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="sale_status">Sale Status <span>*</span></label>
                    <select name="sale_status" id="sale_status" class="form-control @error('sale_status') is-invalid @enderror">
                        @foreach(['booked' => 'Booked', 'sold' => 'Sold', 'cancelled' => 'Cancelled'] as $val => $label)
                            <option value="{{ $val }}" {{ old('sale_status', 'sold') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-hint">Changing this will also update the property status.</div>
                    @error('sale_status') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="agreement_file">Agreement / Document</label>
                    <input type="file" name="agreement_file" id="agreement_file" class="form-control @error('agreement_file') is-invalid @enderror">
                    <div class="form-hint">Upload sale agreement (PDF, DOC, JPG, etc.).</div>
                    @error('agreement_file') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="note">Note</label>
                <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror"
                          placeholder="Add any additional notes about this sale or booking...">{{ old('note') }}</textarea>
                @error('note') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Save Property Sale
            </button>
            <a href="{{ route('property-sales.index') }}" class="btn-outline">Cancel</a>
        </div>
    </form>
</div>

<script>
function calcRemaining() {
    const sale = parseFloat(document.getElementById('sale_amount').value) || 0;
    const booking = parseFloat(document.getElementById('booking_amount').value) || 0;
    const remaining = Math.max(0, sale - booking);
    document.getElementById('remaining_amount').value = remaining.toFixed(2);

    const paymentStatus = document.getElementById('payment_status');
    if (sale > 0 && booking >= sale) {
        paymentStatus.value = 'paid';
    } else if (booking > 0) {
        paymentStatus.value = 'partial';
    } else {
        paymentStatus.value = 'pending';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const projSelect = document.getElementById('project_id');
    const propSelect = document.getElementById('property_id');
    const saleAmountInput = document.getElementById('sale_amount');

    function filterPropertiesByProject() {
        if (!projSelect || !propSelect) return;
        const selectedProjId = projSelect.value;

        Array.from(propSelect.options).forEach(opt => {
            if (!opt.value) {
                opt.hidden = false;
                opt.disabled = false;
                return;
            }
            const optProjId = opt.dataset.projectId || '';
            if (!selectedProjId) {
                // All (direct and project properties)
                opt.hidden = false;
                opt.disabled = false;
            } else if (selectedProjId === 'direct') {
                // Direct standalone properties only
                if (!optProjId) {
                    opt.hidden = false;
                    opt.disabled = false;
                } else {
                    opt.hidden = true;
                    opt.disabled = true;
                }
            } else if (optProjId === selectedProjId) {
                opt.hidden = false;
                opt.disabled = false;
            } else {
                opt.hidden = true;
                opt.disabled = true;
            }
        });

        const currentSelected = propSelect.selectedOptions[0];
        if (currentSelected && currentSelected.hidden) {
            propSelect.value = '';
        }
    }

    if (projSelect && propSelect) {
        projSelect.addEventListener('change', filterPropertiesByProject);

        propSelect.addEventListener('change', function() {
            const opt = this.selectedOptions[0];
            if (opt && opt.dataset.projectId && (!projSelect.value || projSelect.value !== opt.dataset.projectId)) {
                projSelect.value = opt.dataset.projectId;
            } else if (opt && !opt.dataset.projectId && projSelect.value && projSelect.value !== 'direct') {
                projSelect.value = 'direct';
            }

            if (opt && opt.dataset.price) {
                const price = parseFloat(opt.dataset.price);
                if (price > 0 && (!saleAmountInput.value || saleAmountInput.value === '0')) {
                    saleAmountInput.value = price.toFixed(2);
                    calcRemaining();
                }
            }
        });

        // Trigger on load if pre-selected
        if (propSelect.value) {
            const currentOpt = propSelect.selectedOptions[0];
            if (currentOpt && currentOpt.dataset.price && !saleAmountInput.value) {
                saleAmountInput.value = parseFloat(currentOpt.dataset.price).toFixed(2);
                calcRemaining();
            }
        }

        if (projSelect.value) {
            filterPropertiesByProject();
        }
    }
});
</script>
@endsection
