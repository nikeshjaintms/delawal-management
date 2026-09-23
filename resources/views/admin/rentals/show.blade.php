@extends('admin.layouts.app')

@section('title', 'View Rental')
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
    max-width: 920px; margin-left: auto; margin-right: auto;
}

.rental-hero {
    display: flex; align-items: center; gap: 20px; padding-bottom: 24px; margin-bottom: 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.rental-icon {
    width: 64px; height: 64px; border-radius: 16px;
    background: rgba(59, 130, 246, 0.20) !important; border: 2px solid #3B82F6 !important;
    display: flex; align-items: center; justify-content: center; font-size: 26px; color: #60A5FA !important; flex-shrink: 0;
}
.rental-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.rental-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 24px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
@media(max-width:768px){ .detail-grid-3{ grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3{ grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important; border-radius: 14px; transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.35) !important; background: rgba(16, 22, 34, 0.85) !important; }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase;
    letter-spacing: 0.8px; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 15px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B !important; font-weight: 400; font-style: italic; }
.detail-item-full { grid-column: 1 / -1; }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.badge-pending   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-partial   { background: rgba(59, 130, 246, 0.18) !important; color: #60A5FA !important; border: 1px solid rgba(59, 130, 246, 0.35) !important; }
.badge-paid      { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-active    { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-completed { background: rgba(148, 163, 184, 0.18) !important; color: #CBD5E1 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }
.badge-cancelled { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.amount-big { font-size: 18px; font-weight: 800; }
.due-chip { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.35) !important; display: inline-block; }

.meta-info { margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; gap: 24px; flex-wrap: wrap; }
.meta-item { font-size: 12.5px; color: #CBD5E1 !important; display: flex; align-items: center; gap: 6px; font-weight: 500; }
.meta-item i { color: #60A5FA !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; font-size: 14px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); text-decoration: none !important;
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
        <h2>Rental Details</h2>
        <p>Full record of this firm-wise rental agreement.</p>
    </div>
</div>

<div class="card-box">
    {{-- Hero --}}
    <div class="rental-hero">
        <div class="rental-icon"><i class="fa-solid fa-file-contract"></i></div>
        <div class="rental-hero-info">
            <h3>
                {{ $rental->tenant_name }}
                @if($rental->agreement_no)
                    <span style="font-size:14px;color:#60A5FA;font-weight:700;margin-left:8px;">#{{ $rental->agreement_no }}</span>
                @endif
            </h3>
            <p>
                {{ $rental->property->property_name ?? '' }}
                @if($rental->property?->property_code)
                    <span style="color:#60A5FA;font-weight:600;"> ({{ $rental->property->property_code }})</span>
                @endif
                @if($rental->property?->unit_no)
                    &nbsp;·&nbsp; Unit {{ $rental->property->unit_no }}
                @endif
                @if($rental->property?->project)
                    &nbsp;·&nbsp; {{ $rental->property->project->project_name }}
                @endif
            </p>
            <div class="hero-badges">
                <span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span>
                <span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span>
                <span style="font-size:15px;font-weight:800;color:#FFFFFF;">
                    ₹{{ number_format($rental->rent_amount, 0) }}<span style="font-size:12px;font-weight:400;color:#CBD5E1;">/mo</span>
                </span>
                @if($rental->agreement_document)
                    <a href="{{ asset('storage/' . $rental->agreement_document) }}" target="_blank" class="badge" style="background:rgba(37,99,235,0.3);color:#60A5FA;border:1px solid #3B82F6;text-decoration:none;">
                        <i class="fa-solid fa-file-pdf"></i> Agreement Document
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Property & Project --}}
    <div class="section-title"><i class="fa-solid fa-building"></i> Property & Firm Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $rental->firm->firm_name ?? '-' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-city"></i> Project</div>
            <div class="detail-value">{{ $rental->property?->project?->project_name ?? ($rental->property?->project?->propertyMaster?->property_name ?? '—') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property Name</div>
            <div class="detail-value">
                {{ $rental->property->property_name ?? '-' }}
                @if($rental->property?->property_code)
                    <span style="color:#60A5FA;font-size:13px;"> ({{ $rental->property->property_code }})</span>
                @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-door-open"></i> Unit / Plot No</div>
            <div class="detail-value">{{ $rental->property?->unit_no ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-layer-group"></i> Property Type</div>
            <div class="detail-value">{{ $rental->property?->propertyType?->name ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-location-dot"></i> City</div>
            <div class="detail-value">{{ $rental->property?->city ?? '—' }}</div>
        </div>
    </div>

    {{-- Tenant Information --}}
    <div class="section-title"><i class="fa-solid fa-user"></i> Tenant Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Tenant Name</div>
            <div class="detail-value">{{ $rental->tenant_name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-phone"></i> Primary Mobile</div>
            <div class="detail-value">{{ $rental->tenant_mobile }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-envelope"></i> Email</div>
            <div class="detail-value {{ $rental->tenant_email ? '' : 'empty' }}">{{ $rental->tenant_email ?? 'Not provided' }}</div>
        </div>
        @if($rental->tenant)
            <div class="detail-item">
                <div class="detail-label"><i class="fa-solid fa-id-card"></i> ID Proof</div>
                <div class="detail-value">{{ $rental->tenant->identity_type ?? '—' }} ({{ $rental->tenant->identity_number ?? '—' }})</div>
            </div>
        @endif
    </div>

    {{-- Rent & Financials --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Rent & Financials</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Monthly Rent</div>
            <div class="detail-value amount-big" style="color:#60A5FA;">₹{{ number_format($rental->rent_amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Security Deposit</div>
            <div class="detail-value amount-big">₹{{ number_format($rental->security_deposit ?? 0, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-screwdriver-wrench"></i> Maintenance Charges</div>
            <div class="detail-value" style="font-size: 13px; color: #94A3B8;">{{ ($rental->maintenance_amount && $rental->maintenance_amount > 0) ? '₹' . number_format($rental->maintenance_amount, 2) . '/mo' : 'Managed in Rent Collection' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-arrow-trend-up"></i> Annual Increment</div>
            <div class="detail-value">{{ $rental->escalation_percent ? $rental->escalation_percent.'%' : '—' }}</div>
        </div>
    </div>

    {{-- Rental Period & Terms --}}
    <div class="section-title"><i class="fa-solid fa-calendar-days"></i> Rental Period & Terms</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-plus"></i> Agreement Start Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($rental->rent_start_date)->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar-minus"></i> Agreement End Date</div>
            <div class="detail-value {{ $rental->rent_end_date ? '' : 'empty' }}">{{ $rental->rent_end_date ? \Carbon\Carbon::parse($rental->rent_end_date)->format('d M Y') : 'Open-ended' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-key"></i> Handover / Move-in Date</div>
            <div class="detail-value {{ $rental->handover_date ? '' : 'empty' }}">{{ $rental->handover_date ? \Carbon\Carbon::parse($rental->handover_date)->format('d M Y') : '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-clock"></i> Rent Due Day</div>
            <div class="detail-value"><span class="due-chip">Day {{ $rental->rent_due_date ?? 5 }} of month</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-lock"></i> Lock-in Period</div>
            <div class="detail-value">{{ $rental->lock_in_period ? $rental->lock_in_period.' Months' : '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-bell"></i> Notice Period</div>
            <div class="detail-value">{{ $rental->notice_period ? $rental->notice_period.' Days' : '—' }}</div>
        </div>
    </div>

    {{-- Utilities & Agreement Document --}}
    <div class="section-title"><i class="fa-solid fa-bolt"></i> Utilities & Agreement Document</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-gauge-high"></i> Starting Electricity Meter Reading</div>
            <div class="detail-value">{{ $rental->meter_reading ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-arrow-down"></i> Agreement Document</div>
            @if($rental->agreement_document)
                <div class="detail-value">
                    <a href="{{ asset('storage/' . $rental->agreement_document) }}" target="_blank" style="color:#60A5FA;text-decoration:underline;">
                        <i class="fa-solid fa-paperclip"></i> Download / View Attached Agreement
                    </a>
                </div>
            @else
                <div class="detail-value empty">No document attached</div>
            @endif
        </div>
    </div>

    {{-- Status --}}
    <div class="section-title"><i class="fa-solid fa-circle-dot"></i> Status</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-credit-card"></i> Payment Status</div>
            <div class="detail-value"><span class="badge badge-{{ $rental->payment_status }}">{{ ucfirst($rental->payment_status) }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-key"></i> Rental Status</div>
            <div class="detail-value"><span class="badge badge-{{ $rental->rental_status }}">{{ ucfirst($rental->rental_status) }}</span></div>
        </div>
    </div>

    {{-- Rental Expenses --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 28px; margin-bottom: 14px; border-bottom: 1px solid rgba(255,255,255,0.10); padding-bottom: 8px; flex-wrap: wrap; gap: 10px;">
        <div class="section-title" style="margin: 0; padding: 0; border: none; color: #38BDF8 !important;">
            <i class="fa-solid fa-receipt"></i> Rental Expenses
        </div>
        <a href="{{ route('expenses.create', ['type' => 'Rental', 'rental_id' => $rental->id, 'property_id' => $rental->property_id, 'tenant_id' => $rental->tenant_id]) }}"
           class="btn-gold" style="padding: 7px 15px; font-size: 12.5px; border-radius: 8px;">
            <i class="fa-solid fa-plus"></i> Add Rental Expense
        </a>
    </div>

    {{-- Rental Expenses KPI Row --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 18px;">
        <div style="background: rgba(16, 22, 34, 0.75); border: 1.5px solid rgba(245, 158, 11, 0.35); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #FBBF24; letter-spacing: 0.6px;">Total Expenses</div>
                <div style="font-size: 22px; font-weight: 800; color: #FFFFFF; margin-top: 4px;">₹{{ number_format($totalRentalExpense ?? 0, 2) }}</div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.30); display: flex; align-items: center; justify-content: center; color: #FBBF24; font-size: 18px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>

        <div style="background: rgba(16, 22, 34, 0.75); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; color: #60A5FA; letter-spacing: 0.6px;">Total Records</div>
                <div style="font-size: 22px; font-weight: 800; color: #FFFFFF; margin-top: 4px;">{{ isset($rentalExpenses) ? $rentalExpenses->count() : 0 }}</div>
            </div>
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.30); display: flex; align-items: center; justify-content: center; color: #60A5FA; font-size: 18px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>
    </div>

    {{-- Expense Ledger Table --}}
    <div style="width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.10); margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05); color: #94A3B8; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10);">
                    <th style="padding: 11px 14px;">Date</th>
                    <th style="padding: 11px 14px;">Category</th>
                    <th style="padding: 11px 14px;">Expense Title / Note</th>
                    <th style="padding: 11px 14px;">Payment Mode</th>
                    <th style="padding: 11px 14px;">Amount (₹)</th>
                    <th style="padding: 11px 14px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($rentalExpenses) && $rentalExpenses->isNotEmpty())
                    @foreach($rentalExpenses as $exp)
                        <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.06);">
                            <td style="padding: 11px 14px; color: #CBD5E1; font-weight: 600;">
                                {{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}
                            </td>
                            <td style="padding: 11px 14px;">
                                <span style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; padding: 3px 9px; border-radius: 6px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30);">
                                    {{ $exp->expense_category }}
                                </span>
                            </td>
                            <td style="padding: 11px 14px; color: #FFFFFF; font-weight: 600;">
                                {{ $exp->expense_title ?: ($exp->description ? \Illuminate\Support\Str::limit($exp->description, 35) : 'Rental Expense') }}
                            </td>
                            <td style="padding: 11px 14px; color: #94A3B8; font-size: 12px; font-weight: 500;">
                                {{ $exp->payment_mode ?: ($exp->paymentMode?->name ?? '—') }}
                            </td>
                            <td style="padding: 11px 14px; color: #F87171; font-weight: 800; font-size: 14px;">
                                ₹{{ number_format($exp->amount, 2) }}
                            </td>
                            <td style="padding: 11px 14px; text-align: center;">
                                <a href="{{ route('expenses.show', $exp->id) }}" style="color: #60A5FA; font-weight: 700; text-decoration: none; font-size: 12px; margin-right: 8px;" title="View Voucher">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <a href="{{ route('expenses.edit', $exp->id) }}" style="color: #FBBF24; font-weight: 700; text-decoration: none; font-size: 12px;" title="Edit Voucher">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background: rgba(255, 255, 255, 0.03); border-top: 1.5px solid rgba(255, 255, 255, 0.12);">
                        <td colspan="4" style="padding: 11px 14px; font-weight: 800; color: #CBD5E1; text-align: right; text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.5px;">
                            Total Rental Expenses:
                        </td>
                        <td style="padding: 11px 14px; font-weight: 900; color: #FBBF24; font-size: 15px;">
                            ₹{{ number_format($totalRentalExpense ?? 0, 2) }}
                        </td>
                        <td></td>
                    </tr>
                @else
                    <tr>
                        <td colspan="6" align="center" style="padding: 24px; color: #94A3B8; font-size: 12.5px;">
                            <i class="fa-solid fa-receipt" style="font-size: 20px; opacity: 0.3; margin-bottom: 4px; display: block;"></i>
                            No expenses logged for this rental agreement yet.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($rental->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks / Special Clauses</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;">{{ $rental->remarks }}</div>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i><span>Created: {{ $rental->created_at->format('d M Y, h:i A') }}</span></div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i><span>Updated: {{ $rental->updated_at->format('d M Y, h:i A') }}</span></div>
    </div>

    <div class="form-actions">
        <a href="{{ route('rentals.detail-pdf', $rental->id) }}" target="_blank" class="btn-gold" style="background: rgba(252,105,0,0.18) !important; border-color: rgba(252,105,0,0.45) !important; color: #FF8A3D !important; box-shadow: 0 4px 14px rgba(252,105,0,0.25);">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Agreement
        </a>
        <a href="{{ route('rentals.edit', $rental->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Rental
        </a>
        <a href="{{ route('rentals.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection
