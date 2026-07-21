@extends('admin.layouts.app')
@section('title', 'View Expense')
@section('page-title', 'Expense Management')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:900px;margin:0 auto;}
    .exp-hero{display:flex;align-items:flex-start;gap:20px;padding-bottom:24px;margin-bottom:24px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
    .exp-icon{width:64px;height:64px;border-radius:12px;background:rgba(239,68,68,0.08);border:2px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;font-size:26px;color:#EF4444;flex-shrink:0;}
    .exp-hero-info h3{font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:5px;}
    .exp-hero-info p{font-size:13.5px;color:var(--text-secondary);margin-bottom:8px;}
    .hero-badges{display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:6px;}
    .section-title{font-size:12px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin-bottom:14px;margin-top:22px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .detail-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
    @media(max-width:768px){.detail-grid-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.detail-grid,.detail-grid-3{grid-template-columns:1fr;}}
    .detail-item{padding:14px 16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);background:#FFF;box-shadow:0 4px 12px rgba(15,31,53,0.04);}
    .detail-item-full{grid-column:1/-1;}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:7px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary);word-break:break-word;}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .amount-big{font-size:22px;font-weight:800;color:#B91C1C;}
    .cat-chip{background:var(--gold-light);color:#92710A;padding:4px 12px;border-radius:6px;font-size:13px;font-weight:700;border:1px solid rgba(212,175,55,0.25);display:inline-block;}
    .mode-chip{background:#F1F5F9;color:#475569;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:600;display:inline-block;}
    .bill-chip{background:rgba(59,130,246,0.08);color:#1D4ED8;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:600;display:inline-block;}
    .status-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;}
    .status-pending{background:rgba(245,158,11,0.1);color:#B45309;}
    .status-approved{background:rgba(34,197,94,0.1);color:#16803D;}
    .status-rejected{background:rgba(239,68,68,0.1);color:#DC2626;}
    .bill-file-box{display:flex;align-items:center;gap:12px;background:rgba(59,130,246,0.05);border:1px solid rgba(59,130,246,0.15);border-radius:10px;padding:14px;}
    .bill-file-box i{font-size:24px;color:#1D4ED8;}
    .bill-file-box a{color:#1D4ED8;font-size:13.5px;font-weight:600;text-decoration:none;}
    .bill-file-box a:hover{text-decoration:underline;}
    .meta-info{margin-top:22px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:28px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;border:none;cursor:pointer;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
    .btn-danger{border:1px solid rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);color:#DC2626;padding:11px 24px;border-radius:8px;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:var(--transition);font-family:var(--font-primary);}
    .btn-danger:hover{background:rgba(239,68,68,0.1);}
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
            <div class="detail-value">{{ $expense->firm->firm_name ?? '—' }}</div>
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
                        <span style="color:var(--gold);font-size:13px;"> ({{ $expense->property->property_code }})</span>
                    @endif
                </div>
            @else
                <div class="detail-value empty">General / Not property-specific</div>
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
                <div style="font-size:12px;color:var(--text-secondary);margin-bottom:3px;">Attached file</div>
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
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;">{{ $expense->remarks }}</div>
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
