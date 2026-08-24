@extends('admin.layouts.app')
@section('title','Credit Note Details')
@section('page-title','GST / Accounts')
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

.cn-hero {
    display: flex; align-items: flex-start; gap: 18px; padding-bottom: 22px; margin-bottom: 22px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); flex-wrap: wrap;
}
.cn-icon {
    width: 58px; height: 58px; border-radius: 14px;
    background: rgba(16, 185, 129, 0.15) !important; border: 1.5px solid rgba(16, 185, 129, 0.35) !important;
    display: flex; align-items: center; justify-content: center; font-size: 24px; color: #34D399 !important; flex-shrink: 0;
}
.cn-hero-info h3 { font-size: 21px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 5px; }
.cn-hero-info p { font-size: 13.5px; color: #CBD5E1 !important; margin-bottom: 8px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.cn-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; font-size: 11px; font-weight: 700; border-radius: 20px; text-transform: uppercase; white-space: nowrap !important; }
.cn-approved { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.cn-pending { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.cn-rejected { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }

.section-title {
    font-size: 12px; font-weight: 800; color: #60A5FA !important; text-transform: uppercase;
    letter-spacing: 1px; margin: 24px 0 14px; padding-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10); display: flex; align-items: center; gap: 8px;
}

.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
.detail-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
@media(max-width:768px){ .detail-grid-3 { grid-template-columns: 1fr 1fr; } }
@media(max-width:576px){ .detail-grid, .detail-grid-3 { grid-template-columns: 1fr; } }

.detail-item {
    padding: 15px 16px !important; background: rgba(16, 22, 34, 0.65) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 12px !important;
    transition: all .2s ease;
}
.detail-item:hover { border-color: rgba(59, 130, 246, 0.40) !important; background: rgba(22, 30, 46, 0.85) !important; }

.detail-label { font-size: 11px; font-weight: 800; color: #94A3B8 !important; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px; display: flex; align-items: center; gap: 5px; }
.detail-label i { color: #60A5FA !important; font-size: 11px; }
.detail-value { font-size: 14px; font-weight: 700; color: #FFFFFF !important; word-break: break-word; }
.detail-value.empty { color: #64748B !important; font-weight: 500; font-style: italic; }

.gst-summary {
    background: rgba(16, 22, 34, 0.75) !important;
    border: 1.5px solid rgba(16, 185, 129, 0.30) !important;
    border-radius: 16px; padding: 20px 22px;
}
.gst-row { display: flex; justify-content: space-between; align-items: center; padding: 9px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 13.5px; }
.gst-row:last-child { border-bottom: none; padding-top: 12px; margin-top: 4px; }

.meta-info { margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; gap: 20px; flex-wrap: wrap; }
.meta-item { font-size: 12px; color: #94A3B8 !important; display: flex; align-items: center; gap: 6px; }
.meta-item i { color: #60A5FA !important; }

.form-actions { display: flex; align-items: center; gap: 14px; margin-top: 28px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.10); }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 11px 24px;
    border-radius: 12px; text-decoration: none !important; font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #3B82F6 !important;
    cursor: pointer; transition: all .25s ease; box-shadow: 0 4px 18px rgba(37,99,235,0.38);
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
    color: #F87171 !important; padding: 10px 20px; border-radius: 10px; font-size: 13.5px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all .2s ease; margin-left: auto;
}
.btn-danger:hover { background: #DC2626 !important; color: #FFFFFF !important; transform: translateY(-2px); }
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Credit Note Details</h2><p>Full record of this credit adjustment.</p></div>
</div>

<div class="card-box">
    @php $badge = match($creditNote->status){'Approved'=>'cn-approved','Rejected'=>'cn-rejected',default=>'cn-pending'}; @endphp
    <div class="cn-hero">
        <div class="cn-icon"><i class="fa-solid fa-circle-plus"></i></div>
        <div class="cn-hero-info">
            <h3>{{ $creditNote->credit_note_no ?? 'Credit Note #'.$creditNote->id }}</h3>
            <p>{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d M Y') }}
               @if($creditNote->customer) &nbsp;·&nbsp; {{ $creditNote->customer->name }} @endif</p>
            <div class="hero-badges">
                <span style="font-size:21px;font-weight:800;color:#34D399;">₹{{ number_format($creditNote->credit_amount,2) }}</span>
                <span class="cn-badge {{ $badge }}">{{ $creditNote->status }}</span>
            </div>
        </div>
    </div>

    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Credit Note & Firm Information</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building-user"></i> Firm</div>
            <div class="detail-value">{{ $creditNote->firm->firm_name ?? 'Not set' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-hashtag"></i> Credit Note No</div>
            <div class="detail-value">{{ $creditNote->credit_note_no ?? '—' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($creditNote->credit_note_date)->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            @if($creditNote->customer)
                <div class="detail-value">{{ $creditNote->customer->name }}</div>
            @else
                <div class="detail-value empty">Not linked</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Related Invoice No</div>
            <div class="detail-value">{{ $creditNote->related_invoice_no ?? '—' }}</div>
        </div>
        @if($creditNote->reason)
        <div class="detail-item" style="grid-column:1/-1;">
            <div class="detail-label"><i class="fa-solid fa-comment"></i> Reason</div>
            <div class="detail-value" style="font-weight:400;line-height:1.6;">{{ $creditNote->reason }}</div>
        </div>
        @endif
    </div>

    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> GST & Amount Summary</div>
    <div class="gst-summary">
        <div class="gst-row">
            <span style="color:#CBD5E1;">Taxable Amount</span>
            <span style="font-weight:700;color:#FFFFFF;">₹{{ number_format($creditNote->taxable_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#60A5FA;">CGST {{ $creditNote->cgst_rate ? '('.$creditNote->cgst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#60A5FA;">₹{{ number_format($creditNote->cgst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#2DD4BF;">SGST {{ $creditNote->sgst_rate ? '('.$creditNote->sgst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#2DD4BF;">₹{{ number_format($creditNote->sgst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#C4B5FD;">IGST {{ $creditNote->igst_rate ? '('.$creditNote->igst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#C4B5FD;">₹{{ number_format($creditNote->igst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#F87171;font-weight:600;">Total GST</span>
            <span style="font-weight:700;color:#F87171;">₹{{ number_format($creditNote->total_gst,2) }}</span>
        </div>
        <div class="gst-row" style="border-bottom:none;">
            <span style="font-size:15px;font-weight:800;color:#FFFFFF;">Credit Amount (Grand Total)</span>
            <span style="font-size:18px;font-weight:800;color:#34D399;">₹{{ number_format($creditNote->credit_amount,2) }}</span>
        </div>
    </div>

    @if($creditNote->notes)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Notes</div>
        <div class="detail-item"><div class="detail-value" style="font-weight:400;line-height:1.7;">{{ $creditNote->notes }}</div></div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> Created: {{ $creditNote->created_at->format('d M Y, h:i A') }}</div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> Updated: {{ $creditNote->updated_at->format('d M Y, h:i A') }}</div>
    </div>

    <div class="form-actions">
        <a href="{{ route('credit-notes.edit', $creditNote->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
        <a href="{{ route('credit-notes.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        <form action="{{ route('credit-notes.destroy', $creditNote->id) }}" method="POST" id="del-cn-show">
            @csrf @method('DELETE')
            <button type="button" class="btn-danger"
                onclick="Swal.fire({title:'Delete?',html:'Delete <strong>{{ addslashes($creditNote->credit_note_no ?? 'this note') }}</strong>?',icon:'warning',showCancelButton:true,confirmButtonColor:'#EF4444',cancelButtonColor:'#64748B',confirmButtonText:'Yes, Delete'}).then(r=>{if(r.isConfirmed)document.getElementById('del-cn-show').submit();})">
                <i class="fa-regular fa-trash-can"></i> Delete
            </button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
