@extends('admin.layouts.app')
@section('title','Credit Note Details')
@section('page-title','GST / Accounts')
@section('content')
<style>
.crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
.crud-title h2{font-size:22px;font-weight:800;color:#0F172A;margin-bottom:4px;}
.crud-title p{font-size:13.5px;color:#64748B;}
.card-box{background:#fff;border:1px solid #E2E8F0;border-radius:16px;padding:30px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 20px rgba(0,0,0,0.05);max-width:900px;margin:0 auto;}
.cn-hero{display:flex;align-items:flex-start;gap:18px;padding-bottom:22px;margin-bottom:22px;border-bottom:1px solid #E2E8F0;flex-wrap:wrap;}
.cn-icon{width:58px;height:58px;border-radius:12px;background:rgba(16,185,129,0.08);border:2px solid rgba(16,185,129,0.2);display:flex;align-items:center;justify-content:center;font-size:24px;color:#10B981;flex-shrink:0;}
.cn-hero-info h3{font-size:19px;font-weight:800;color:#0F172A;margin-bottom:5px;}
.cn-hero-info p{font-size:13.5px;color:#64748B;margin-bottom:8px;}
.hero-badges{display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
.cn-badge{display:inline-block;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;}
.cn-approved{background:rgba(16,185,129,0.1);color:#065F46;}
.cn-pending{background:rgba(245,158,11,0.1);color:#92400E;}
.cn-rejected{background:rgba(239,68,68,0.1);color:#991B1B;}
.section-title{font-size:12px;font-weight:700;color:#3B82F6;text-transform:uppercase;letter-spacing:1px;margin:20px 0 14px;padding-bottom:8px;border-bottom:1px solid #E2E8F0;display:flex;align-items:center;gap:8px;}
.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.detail-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
@media(max-width:768px){.detail-grid-3{grid-template-columns:1fr 1fr;}}
@media(max-width:576px){.detail-grid,.detail-grid-3{grid-template-columns:1fr;}}
.detail-item{padding:13px 15px;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:10px;transition:all .18s;}
.detail-item:hover{border-color:#BFDBFE;background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.04);}
.detail-label{font-size:11px;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;display:flex;align-items:center;gap:5px;}
.detail-label i{color:#3B82F6;font-size:11px;}
.detail-value{font-size:14px;font-weight:600;color:#0F172A;}
.detail-value.empty{color:#CBD5E1;font-weight:400;font-style:italic;}
.gst-summary{background:linear-gradient(135deg,rgba(16,185,129,0.04),rgba(16,185,129,0.01));border:1px solid rgba(16,185,129,0.15);border-radius:12px;padding:18px 20px;}
.gst-row{display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(16,185,129,0.08);font-size:13.5px;}
.gst-row:last-child{border-bottom:none;padding-top:10px;margin-top:4px;}
.meta-info{margin-top:20px;padding-top:16px;border-top:1px solid #E2E8F0;display:flex;gap:20px;flex-wrap:wrap;}
.meta-item{font-size:12px;color:#64748B;display:flex;align-items:center;gap:6px;}
.meta-item i{color:#3B82F6;}
.form-actions{display:flex;align-items:center;gap:14px;margin-top:22px;padding-top:20px;border-top:1px solid #E2E8F0;}
.btn-gold{background:linear-gradient(135deg,#3B82F6,#2563EB);color:#FFF;padding:11px 22px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:all .22s;box-shadow:0 2px 8px rgba(59,130,246,0.3);}
.btn-gold:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(59,130,246,0.4);}
.btn-outline{border:1px solid #E2E8F0;background:transparent;color:#64748B;padding:11px 22px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:all .18s;}
.btn-outline:hover{border-color:#3B82F6;color:#3B82F6;}
.btn-danger{border:1px solid rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);color:#DC2626;padding:11px 20px;border-radius:8px;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:all .18s;font-family:inherit;margin-left:auto;}
.btn-danger:hover{background:rgba(239,68,68,0.1);}
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
                <span style="font-size:21px;font-weight:800;color:#059669;">₹{{ number_format($creditNote->credit_amount,2) }}</span>
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
            <span style="color:#64748B;">Taxable Amount</span>
            <span style="font-weight:700;">₹{{ number_format($creditNote->taxable_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#0EA5E9;">CGST {{ $creditNote->cgst_rate ? '('.$creditNote->cgst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#0EA5E9;">₹{{ number_format($creditNote->cgst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#14B8A6;">SGST {{ $creditNote->sgst_rate ? '('.$creditNote->sgst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#14B8A6;">₹{{ number_format($creditNote->sgst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#8B5CF6;">IGST {{ $creditNote->igst_rate ? '('.$creditNote->igst_rate.'%)' : '' }}</span>
            <span style="font-weight:700;color:#8B5CF6;">₹{{ number_format($creditNote->igst_amount,2) }}</span>
        </div>
        <div class="gst-row">
            <span style="color:#EF4444;font-weight:600;">Total GST</span>
            <span style="font-weight:700;color:#EF4444;">₹{{ number_format($creditNote->total_gst,2) }}</span>
        </div>
        <div class="gst-row" style="border-bottom:none;">
            <span style="font-size:15px;font-weight:800;color:#0F172A;">Credit Amount (Grand Total)</span>
            <span style="font-size:18px;font-weight:800;color:#059669;">₹{{ number_format($creditNote->credit_amount,2) }}</span>
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
