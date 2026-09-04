@extends('admin.layouts.app')
@section('title', 'View Expense')
@section('page-title', 'Expense Management')
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
    max-width: 900px; margin-left: auto; margin-right: auto;
}

.exp-hero { display: flex; align-items: flex-start; gap: 20px; padding-bottom: 24px; margin-bottom: 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap; }
.exp-icon { width: 64px; height: 64px; border-radius: 16px; background: rgba(239, 68, 68, 0.18) !important; border: 2px solid rgba(239, 68, 68, 0.40) !important; display: flex; align-items: center; justify-content: center; font-size: 26px; color: #F87171 !important; flex-shrink: 0; }
.exp-hero-info h3 { font-size: 22px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.exp-hero-info p { font-size: 14px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 8px; }

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin-bottom: 16px; margin-top: 24px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 18px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 16px 18px; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 16px !important;
    transition: all .25s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.40) !important; transform: translateY(-2px); }
.detail-item-full { grid-column: 1 / -1; }

.detail-label {
    font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase;
    letter-spacing: 0.8px; margin-bottom: 7px; display: flex; align-items: center; gap: 6px;
}
.detail-label i { color: #60A5FA !important; font-size: 12px; }

.detail-value { font-size: 14.5px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #94A3B8 !important; font-weight: 400; font-style: italic; }

.amount-big { font-size: 22px; font-weight: 800; color: #F87171 !important; }

.cat-chip { background: rgba(245, 158, 11, 0.15) !important; color: #FBBF24 !important; padding: 4px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30) !important; display: inline-block; white-space: nowrap; }
.mode-chip { background: rgba(255, 255, 255, 0.08) !important; color: #E2E8F0 !important; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-block; border: 1px solid rgba(255, 255, 255, 0.10); white-space: nowrap !important; }
.bill-chip { background: rgba(59, 130, 246, 0.15) !important; color: #60A5FA !important; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600; display: inline-block; border: 1px solid rgba(59, 130, 246, 0.30); white-space: nowrap !important; }

.status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; white-space: nowrap !important; }
.status-pending  { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.status-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.status-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.bill-file-box {
    display: flex; align-items: center; gap: 14px;
    background: rgba(59, 130, 246, 0.12) !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    border-radius: 14px !important; padding: 16px 20px !important;
}
.bill-file-box i { font-size: 26px; color: #60A5FA !important; }
.bill-file-box a { color: #60A5FA !important; font-size: 14px; font-weight: 700; text-decoration: none !important; }
.bill-file-box a:hover { text-decoration: underline !important; color: #93C5FD !important; }

.meta-info { margin-top: 24px; padding-top: 18px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; gap: 24px; flex-wrap: wrap; }
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

.btn-danger {
    border: 1px solid rgba(239, 68, 68, 0.35) !important; background: rgba(239, 68, 68, 0.15) !important;
    color: #F87171 !important; padding: 10px 22px; border-radius: 10px; font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all .25s ease;
}
.btn-danger:hover { background: rgba(239, 68, 68, 0.25) !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Expense Details</h2>
        <p>Full record of this expense entry.</p>
    </div>
</div>

<div class="card-box">
    {{-- Hero --}}
    <div class="exp-hero">
        <div class="exp-icon"><i class="fa-solid fa-receipt"></i></div>
        <div class="exp-hero-info">
            <h3>{{ $expense->expense_title }}</h3>
            <p>
                {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                @if($expense->expense_category) &nbsp;·&nbsp; {{ $expense->expense_category }} @endif
            </p>
            <div class="hero-badges">
                <span class="amount-big">₹{{ number_format($expense->amount, 2) }}</span>
                @php $st = $expense->approval_status ?? 'Pending'; @endphp
                <span class="status-badge status-{{ strtolower($st) }}">
                    @if($st == 'Approved') <i class="fa-solid fa-circle-check"></i>
                    @elseif($st == 'Rejected') <i class="fa-solid fa-circle-xmark"></i>
                    @else <i class="fa-solid fa-clock"></i>
                    @endif
                    {{ $st }}
                </span>
                @if($expense->payment_mode)
                    <span class="mode-chip">{{ $expense->payment_mode }}</span>
                @endif
                @if($expense->expense_category)
                    <span class="cat-chip">{{ $expense->expense_category }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Expense Info --}}
    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Expense Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Firm</div>
            <div class="detail-value">{{ $expense->firm_names }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-tag"></i> Expense Title</div>
            <div class="detail-value">{{ $expense->expense_title }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Expense Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-tags"></i> Category</div>
            @if($expense->expense_category)
                <div class="detail-value"><span class="cat-chip">{{ $expense->expense_category }}</span></div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
            @if($expense->property)
                <div class="detail-value">
                    {{ $expense->property->property_name }}
                    @if($expense->property->property_code)
                        <span style="color:#60A5FA;font-size:13px;"> ({{ $expense->property->property_code }})</span>
                    @endif
                </div>
            @else
                <div class="detail-value empty">General / Not property-specific</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-city"></i> Project</div>
            @if($expense->project ?? $expense->property?->project)
                @php $proj = $expense->project ?? $expense->property->project; @endphp
                <div class="detail-value">
                    {{ $proj->project_name }}
                    @if($proj->propertyMaster)
                        <span style="color:#60A5FA;font-size:13px;"> ({{ $proj->propertyMaster->property_name }})</span>
                    @endif
                </div>
            @else
                <div class="detail-value empty">General / Standalone</div>
            @endif
        </div>
    </div>

    {{-- Payment Details --}}
    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Payment Details</div>
    <div class="detail-grid-3">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-indian-rupee-sign"></i> Amount</div>
            <div class="detail-value amount-big">₹{{ number_format($expense->amount, 2) }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
            @if($expense->payment_mode)
                <div class="detail-value"><span class="mode-chip">{{ $expense->payment_mode }}</span></div>
            @else
                <div class="detail-value empty">Not set</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Paid To</div>
            @if($expense->paid_to)
                <div class="detail-value">{{ $expense->paid_to }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Bill / Invoice No</div>
            @if($expense->bill_no)
                <div class="detail-value"><span class="bill-chip">{{ $expense->bill_no }}</span></div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-shield-halved"></i> Approval Status</div>
            <div class="detail-value">
                @php $st = $expense->approval_status ?? 'Pending'; @endphp
                <span class="status-badge status-{{ strtolower($st) }}">{{ $st }}</span>
            </div>
        </div>
    </div>

    {{-- Bill File --}}
    @if($expense->bill_file)
        <div class="section-title"><i class="fa-solid fa-paperclip"></i> Attached Bill / Receipt</div>
        <div class="bill-file-box">
            @php $ext = pathinfo($expense->bill_file, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                <i class="fa-regular fa-image"></i>
            @else
                <i class="fa-solid fa-file-pdf"></i>
            @endif
            <div>
                <div style="font-size:12px;color:#94A3B8;margin-bottom:3px;">Attached file</div>
                <a href="{{ asset('storage/'.$expense->bill_file) }}" target="_blank">
                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:11px;"></i>
                    View / Download Bill
                </a>
            </div>
        </div>
    @endif

    {{-- Remarks --}}
    @if($expense->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;color:#CBD5E1 !important;">{{ $expense->remarks }}</div>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> Created: {{ $expense->created_at->format('d M Y, h:i A') }}</div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> Updated: {{ $expense->updated_at->format('d M Y, h:i A') }}</div>
    </div>

    <div class="form-actions">
        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn-gold">
            <i class="fa-regular fa-pen-to-square"></i> Edit Expense
        </a>
        <a href="{{ route('expenses.index') }}" class="btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to List
        </a>
        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
              style="margin-left:auto;" id="del-show-{{ $expense->id }}">
            @csrf @method('DELETE')
            <button type="button" class="btn-danger"
                onclick="confirmDelete({{ $expense->id }}, '{{ addslashes($expense->expense_title) }}')">
                <i class="fa-regular fa-trash-can"></i> Delete
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(id, title) {
    Swal.fire({
        title: 'Delete Expense?',
        html: 'Delete <strong>' + title + '</strong>?<br><small style="color:#64748B;">This action cannot be undone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#64748B',
        confirmButtonText: '<i class="fa-regular fa-trash-can"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        customClass: { popup: 'swal-exp-popup' }
    }).then(r => { if (r.isConfirmed) document.getElementById('del-show-' + id).submit(); });
}
</script>
<style>.swal-exp-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
