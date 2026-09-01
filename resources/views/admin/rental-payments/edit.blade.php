@extends('admin.layouts.app')

@section('title', 'Edit Rental Payment')
@section('page-title', 'Rental Management')

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
    max-width: 880px; margin-left: auto; margin-right: auto;
}

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 18px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.form-section { margin-bottom: 30px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
@media(max-width:768px){ .form-row-3{ grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .form-row, .form-row-3{ grid-template-columns: 1fr; gap: 0; } }

.form-label { display: block; font-size: 13px; font-weight: 700; color: #CBD5E1 !important; margin-bottom: 8px; }
.form-label span { color: #F87171 !important; }
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
.form-control-readonly { background: rgba(255, 255, 255, 0.05) !important; color: #94A3B8 !important; border: 1px solid rgba(255, 255, 255, 0.10) !important; cursor: default; }
textarea.form-control { resize: vertical; min-height: 90px; }

.text-error { color: #F87171 !important; font-size: 12.5px; margin-top: 6px; font-weight: 600; }
.form-hint { font-size: 12px; color: #CBD5E1 !important; margin-top: 5px; }
.calc-hint { font-size: 11.5px; color: #FBBF24 !important; margin-top: 5px; font-weight: 700; }

/* Rental summary bar */
.rental-bar {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 16px !important; padding: 18px 22px !important;
    margin-bottom: 24px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.30);
}
.rental-bar-icon { font-size: 22px; color: #60A5FA !important; }
.rental-bar-info p { margin: 0; font-size: 14px; color: #FFFFFF !important; font-weight: 600; }
.rental-bar-info p strong { color: #60A5FA !important; font-weight: 800; }

/* Select2 Glass Styling Overrides */
.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    background-color: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important; min-height: 42px !important; padding: 4px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: rgba(37, 99, 235, 0.35) !important;
    border: 1px solid rgba(59, 130, 246, 0.50) !important;
    color: #FFFFFF !important; border-radius: 6px !important; font-weight: 600; padding: 3px 8px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: #F87171 !important; margin-right: 6px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #FFFFFF !important; line-height: 32px !important;
}
.select2-dropdown { background-color: #101622 !important; border: 1px solid rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; }
.select2-results__option { color: #CBD5E1 !important; }
.select2-results__option--highlighted[aria-selected] { background-color: #2563EB !important; color: #FFFFFF !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

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
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Edit Rental Payment</h2>
        <p>Update monthly rent payment details.</p>
    </div>
</div>

<div style="max-width:880px;margin:0 auto;">
    {{-- Rental summary bar --}}
    <div class="rental-bar">
        <div class="rental-bar-icon"><i class="fa-solid fa-key"></i></div>
        <div class="rental-bar-info">
            <p>
                <strong>{{ $rental->tenant_name }}</strong>
                &nbsp;·&nbsp; {{ $rental->tenant_mobile }}
                &nbsp;·&nbsp; {{ $rental->property->property_name ?? '' }}
                @if($rental->property?->unit_no) — Unit {{ $rental->property->unit_no }} @endif
                &nbsp;·&nbsp; Monthly Rent: <strong>₹{{ number_format($rental->rent_amount, 0) }}</strong>
            </p>
        </div>
    </div>
</div>

<div class="card-box">
    <form method="POST" action="{{ route('rental-payments.update', [$rental->id, $rentalPayment->id]) }}">
        @csrf
        @method('PUT')
        @include('admin.components.firm-select', ['model' => $rental])

        {{-- Property Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-building"></i> Property Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="project_id">Project <span class="opt">(Select project to filter properties)</span></label>
                    <select name="project_id" id="project_id" class="form-control">
                        <option value="">-- All / Select Project --</option>
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
                </div>

                <div class="form-group">
                    <label class="form-label" for="property_id">Property <span>*</span></label>
                    <select name="property_id" id="property_id" class="form-control @error('property_id') is-invalid @enderror" required>
                        <option value="">-- Select Property --</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}"
                                    data-project-id="{{ $property->project_id }}"
                                    data-firm-id="{{ $property->firm_id }}"
                                    data-project="{{ $property->project->project_name ?? ($property->project->propertyMaster->property_name ?? 'No Project Assigned') }}"
                                    {{ old('property_id', $rentalPayment->property_id) == $property->id ? 'selected' : '' }}>
                                {{ $property->property_code ? $property->property_code . ' - ' : '' }}{{ $property->property_name }}{{ $property->propertyType ? ' - ' . $property->propertyType->name : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Period --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Payment Period</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_month">Payment Month <span>*</span></label>
                    <select name="payment_month" id="payment_month" class="form-control @error('payment_month') is-invalid @enderror" required>
                        <option value="">-- Select Month --</option>
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ old('payment_month', $rentalPayment->payment_month) == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('payment_month') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_year">Payment Year <span>*</span></label>
                    <select name="payment_year" id="payment_year" class="form-control @error('payment_year') is-invalid @enderror" required>
                        <option value="">-- Select Year --</option>
                        @for($y = date('Y') + 1; $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ old('payment_year', $rentalPayment->payment_year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('payment_year') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Payment Amount</div>
            <div class="form-row-3">
                <div class="form-group">
                    <label class="form-label" for="rent_amount">Rent Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="rent_amount" id="rent_amount"
                           value="{{ old('rent_amount', $rentalPayment->rent_amount) }}"
                           class="form-control @error('rent_amount') is-invalid @enderror" placeholder="Enter rent amount"
                           oninput="calcPending()" required>
                    @error('rent_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="paid_amount">Paid Amount (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                           value="{{ old('paid_amount', $rentalPayment->paid_amount) }}"
                           class="form-control @error('paid_amount') is-invalid @enderror" placeholder="Enter amount paid"
                           oninput="calcPending()" required>
                    @error('paid_amount') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="pending_display">Pending Amount (₹)</label>
                    <input type="text" id="pending_display" class="form-control form-control-readonly"
                           readonly placeholder="Auto-calculated">
                    <div class="calc-hint"><i class="fa-solid fa-calculator" style="font-size:10px;"></i> = Rent − Paid</div>
                </div>
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="form-section">
            <div class="section-title"><i class="fa-solid fa-wallet"></i> Payment Details</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="payment_mode">Payment Mode</label>
                    <select name="payment_mode" id="payment_mode" class="form-control @error('payment_mode') is-invalid @enderror">
                        <option value="">-- Select Mode --</option>
                        @php
                            $firmId = Auth::user()?->firm_id ?? session('firm_id');
                            $pModes = \App\Models\PaymentMode::where('status', 'active')
                                ->when($firmId, function($q) use ($firmId) {
                                    $q->where(function($sub) use ($firmId) {
                                        $sub->whereHas('firms', fn($f) => $f->where('firms.id', $firmId))
                                            ->orWhereDoesntHave('firms');
                                    });
                                })
                                ->orderBy('name')
                                ->get();
                            if ($pModes->isEmpty()) {
                                $pModes = \App\Models\PaymentMode::where('status', 'active')->orderBy('name')->get();
                            }
                        @endphp
                        @foreach($pModes as $pm)
                            <option value="{{ $pm->name }}" {{ old('payment_mode', $rentalPayment->payment_mode) == $pm->name ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_mode') <div class="text-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="payment_date">Payment Date</label>
                    <input type="date" name="payment_date" id="payment_date"
                           value="{{ old('payment_date', $rentalPayment->payment_date) }}" class="form-control @error('payment_date') is-invalid @enderror">
                    @error('payment_date') <div class="text-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror"
                          placeholder="Add any notes about this payment...">{{ old('remarks', $rentalPayment->remarks) }}</textarea>
                @error('remarks') <div class="text-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold">
                <i class="fa-solid fa-check"></i> Update Payment
            </button>
            <a href="{{ route('rental-payments.index', $rental->id) }}" class="btn-outline">Back</a>
        </div>
    </form>
</div>

<script>
function calcPending() {
    const rent   = parseFloat(document.getElementById('rent_amount').value)   || 0;
    const paid   = parseFloat(document.getElementById('paid_amount').value)   || 0;
    const pending = Math.max(0, rent - paid);
    document.getElementById('pending_display').value =
        '₹' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

document.addEventListener('DOMContentLoaded', function() {
    calcPending();

    const projectSelect = document.getElementById('project_id');
    const propSelect = document.getElementById('property_id');
    const allPropOptions = propSelect ? Array.from(propSelect.querySelectorAll('option')).slice(1) : [];

    function filterPropertiesByProject() {
        if (!propSelect) return;
        const selectedProjectId = projectSelect ? projectSelect.value : '';
        const currentPropVal = propSelect.value;

        propSelect.innerHTML = '<option value="">-- Select Property --</option>';

        let visibleCount = 0;
        allPropOptions.forEach(opt => {
            const optProjId = opt.getAttribute('data-project-id');
            if (!selectedProjectId || String(optProjId) === String(selectedProjectId)) {
                propSelect.appendChild(opt.cloneNode(true));
                visibleCount++;
            }
        });

        if (visibleCount === 0 && selectedProjectId) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.textContent = '— No properties found for this project —';
            propSelect.appendChild(noOpt);
        }

        propSelect.value = currentPropVal;
    }

    if (projectSelect) {
        projectSelect.addEventListener('change', filterPropertiesByProject);
    }

    if (propSelect) {
        propSelect.addEventListener('change', function() {
            const selectedOpt = propSelect.options[propSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const projId = selectedOpt.getAttribute('data-project-id');
                if (projectSelect && projId && projectSelect.value !== projId) {
                    projectSelect.value = projId;
                }
            }
        });
    }

    if (projectSelect && projectSelect.value) {
        filterPropertiesByProject();
    }
});
</script>
@endsection
