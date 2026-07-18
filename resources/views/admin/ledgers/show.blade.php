@extends('admin.layouts.app')
@section('title','Ledger Entry Details')
@section('page-title','GST / Accounts')
@section('content')
<style>
    .crud-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:15px;}
    .crud-title h2{font-size:22px;font-weight:700;color:var(--text-primary);margin-bottom:4px;}
    .crud-title p{font-size:13.5px;color:var(--text-secondary);}
    .card-box{background:var(--card-bg);border:1px solid var(--border-color);border-radius:12px;padding:30px;box-shadow:var(--soft-shadow);max-width:960px;margin:0 auto;}
    .ldg-hero{display:flex;align-items:flex-start;gap:20px;padding-bottom:22px;margin-bottom:22px;border-bottom:1px solid var(--border-color);flex-wrap:wrap;}
    .ldg-icon{width:60px;height:60px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;}
    .ldg-icon.debit{background:rgba(239,68,68,0.1);border:2px solid rgba(239,68,68,0.25);color:#DC2626;}
    .ldg-icon.credit{background:rgba(34,197,94,0.1);border:2px solid rgba(34,197,94,0.25);color:#16803D;}
    .ldg-icon.both{background:var(--gold-light);border:2px solid rgba(212,175,55,0.3);color:var(--gold);}
    .ldg-hero-info h3{font-size:20px;font-weight:700;color:var(--text-primary);margin-bottom:5px;}
    .ldg-hero-info p{font-size:13.5px;color:var(--text-secondary);margin-bottom:8px;}
    .hero-amounts{display:flex;gap:16px;flex-wrap:wrap;margin-top:8px;}
    .amt-block{padding:8px 18px;border-radius:8px;display:flex;flex-direction:column;gap:2px;}
    .amt-block.deb{background:rgba(239,68,68,0.07);border:1px solid rgba(239,68,68,0.15);}
    .amt-block.crd{background:rgba(34,197,94,0.07);border:1px solid rgba(34,197,94,0.15);}
    .amt-block .alabel{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;}
    .amt-block.deb .alabel{color:#B91C1C;}
    .amt-block.crd .alabel{color:#16803D;}
    .amt-block .aval{font-size:18px;font-weight:800;}
    .amt-block.deb .aval{color:#DC2626;}
    .amt-block.crd .aval{color:#16803D;}
    .section-title{font-size:12px;font-weight:700;color:var(--gold);text-transform:uppercase;letter-spacing:1px;margin:20px 0 14px;padding-bottom:8px;border-bottom:1px solid var(--border-color);display:flex;align-items:center;gap:8px;}
    .detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .detail-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
    .detail-grid-4{display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:16px;}
    @media(max-width:900px){.detail-grid-4{grid-template-columns:1fr 1fr;}.detail-grid-3{grid-template-columns:1fr 1fr;}}
    @media(max-width:576px){.detail-grid,.detail-grid-3,.detail-grid-4{grid-template-columns:1fr;}}
    .detail-item{padding:14px 16px;background:#F9FAFB;border:1px solid var(--border-color);border-radius:10px;transition:var(--transition);}
    .detail-item:hover{border-color:rgba(212,175,55,0.2);background:#FFF;box-shadow:0 4px 12px rgba(15,31,53,0.04);}
    .detail-item-full{grid-column:1/-1;}
    .detail-label{font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:7px;display:flex;align-items:center;gap:6px;}
    .detail-label i{color:var(--gold);font-size:12px;}
    .detail-value{font-size:14.5px;font-weight:600;color:var(--text-primary);word-break:break-word;}
    .detail-value.empty{color:#9CA3AF;font-weight:400;font-style:italic;}
    .type-chip{background:var(--gold-light);color:#92710A;padding:4px 12px;border-radius:6px;font-size:13px;font-weight:700;border:1px solid rgba(212,175,55,0.25);display:inline-block;}
    .mode-chip{background:#F1F5F9;color:#475569;padding:4px 10px;border-radius:6px;font-size:13px;font-weight:600;display:inline-block;}
    .meta-info{margin-top:20px;padding-top:18px;border-top:1px solid var(--border-color);display:flex;gap:24px;flex-wrap:wrap;}
    .meta-item{font-size:12px;color:var(--text-secondary);display:flex;align-items:center;gap:6px;}
    .meta-item i{color:var(--gold);}
    .form-actions{display:flex;align-items:center;gap:15px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border-color);}
    .btn-gold{background-color:var(--gold);color:#FFF;padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);box-shadow:0 4px 10px rgba(212,175,55,0.2);}
    .btn-gold:hover{background-color:#B58D1B;transform:translateY(-1px);}
    .btn-outline{border:1px solid var(--border-color);background:transparent;color:var(--text-secondary);padding:11px 24px;border-radius:8px;text-decoration:none;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:var(--transition);}
    .btn-outline:hover{background:#F9FAFB;color:var(--text-primary);border-color:#D1D5DB;}
    .btn-danger{border:1px solid rgba(239,68,68,0.3);background:rgba(239,68,68,0.05);color:#DC2626;padding:11px 20px;border-radius:8px;font-size:14px;font-weight:600;display:inline-flex;align-items:center;gap:8px;cursor:pointer;transition:var(--transition);font-family:var(--font-primary);}
    .btn-danger:hover{background:rgba(239,68,68,0.1);}
</style>

<div class="crud-header">
    <div class="crud-title"><h2>Ledger Entry Details</h2><p>Full details of this transaction record.</p></div>
</div>

<div class="card-box">
    @php
        $hasDebit  = $ledger->debit_amount  > 0;
        $hasCredit = $ledger->credit_amount > 0;
        $iconClass = ($hasDebit && $hasCredit) ? 'both' : ($hasDebit ? 'debit' : 'credit');
        $iconName  = ($hasDebit && $hasCredit) ? 'fa-scale-balanced' : ($hasDebit ? 'fa-arrow-up-right-from-square' : 'fa-arrow-down-to-bracket');
    @endphp

    <div class="ldg-hero">
        <div class="ldg-icon {{ $iconClass }}"><i class="fa-solid {{ $iconName }}"></i></div>
        <div class="ldg-hero-info">
            <h3>{{ $ledger->transaction_title }}</h3>
            <p>{{ \Carbon\Carbon::parse($ledger->ledger_date)->format('d M Y') }} &nbsp;·&nbsp; <span class="type-chip" style="font-size:12px;padding:2px 8px;">{{ $ledger->transaction_type }}</span></p>
            <div class="hero-amounts">
                @if($hasDebit)
                    <div class="amt-block deb"><div class="alabel">Debit</div><div class="aval">₹{{ number_format($ledger->debit_amount,2) }}</div></div>
                @endif
                @if($hasCredit)
                    <div class="amt-block crd"><div class="alabel">Credit</div><div class="aval">₹{{ number_format($ledger->credit_amount,2) }}</div></div>
                @endif
            </div>
        </div>
    </div>

    <div class="section-title"><i class="fa-solid fa-circle-info"></i> Transaction Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-tag"></i> Transaction Title</div>
            <div class="detail-value">{{ $ledger->transaction_title }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-regular fa-calendar"></i> Ledger Date</div>
            <div class="detail-value">{{ \Carbon\Carbon::parse($ledger->ledger_date)->format('d M Y') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-tags"></i> Transaction Type</div>
            <div class="detail-value"><span class="type-chip">{{ $ledger->transaction_type }}</span></div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-wallet"></i> Payment Mode</div>
            @if($ledger->payment_mode)
                <div class="detail-value"><span class="mode-chip">{{ $ledger->payment_mode }}</span></div>
            @else
                <div class="detail-value empty">Not specified</div>
            @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-file-invoice"></i> Reference No</div>
            @if($ledger->reference_no)
                <div class="detail-value">{{ $ledger->reference_no }}</div>
            @else
                <div class="detail-value empty">Not provided</div>
            @endif
        </div>
    </div>

    <div class="section-title"><i class="fa-solid fa-indian-rupee-sign"></i> Amount Details</div>
    <div class="detail-grid">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-arrow-up"></i> Debit Amount</div>
            <div class="detail-value" style="font-size:18px;font-weight:800;color:#DC2626;">
                {{ $ledger->debit_amount > 0 ? '₹'.number_format($ledger->debit_amount,2) : '—' }}
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-arrow-down"></i> Credit Amount</div>
            <div class="detail-value" style="font-size:18px;font-weight:800;color:#16803D;">
                {{ $ledger->credit_amount > 0 ? '₹'.number_format($ledger->credit_amount,2) : '—' }}
            </div>
        </div>
    </div>

    <div class="section-title"><i class="fa-solid fa-link"></i> Linked Parties</div>
    <div class="detail-grid-4">
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-building"></i> Property</div>
            @if($ledger->property)
                <div class="detail-value" style="font-size:13.5px;">{{ $ledger->property->property_name }}</div>
            @else <div class="detail-value empty">Not linked</div> @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user"></i> Customer</div>
            @if($ledger->customer)
                <div class="detail-value" style="font-size:13.5px;">{{ $ledger->customer->name }}</div>
            @else <div class="detail-value empty">Not linked</div> @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-truck-field"></i> Vendor</div>
            @if($ledger->vendor)
                <div class="detail-value" style="font-size:13.5px;">{{ $ledger->vendor->name }}</div>
            @else <div class="detail-value empty">Not linked</div> @endif
        </div>
        <div class="detail-item">
            <div class="detail-label"><i class="fa-solid fa-user-tie"></i> Broker</div>
            @if($ledger->broker)
                <div class="detail-value" style="font-size:13.5px;">{{ $ledger->broker->name }}</div>
            @else <div class="detail-value empty">Not linked</div> @endif
        </div>
    </div>

    @if($ledger->remarks)
        <div class="section-title"><i class="fa-solid fa-note-sticky"></i> Remarks</div>
        <div class="detail-item">
            <div class="detail-value" style="font-weight:400;font-size:14px;line-height:1.7;">{{ $ledger->remarks }}</div>
        </div>
    @endif

    <div class="meta-info">
        <div class="meta-item"><i class="fa-regular fa-calendar-plus"></i> Created: {{ $ledger->created_at->format('d M Y, h:i A') }}</div>
        <div class="meta-item"><i class="fa-regular fa-calendar-check"></i> Updated: {{ $ledger->updated_at->format('d M Y, h:i A') }}</div>
    </div>

    <div class="form-actions">
        <a href="{{ route('ledgers.edit', $ledger->id) }}" class="btn-gold"><i class="fa-regular fa-pen-to-square"></i> Edit Entry</a>
        <a href="{{ route('ledgers.index') }}" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        <form action="{{ route('ledgers.destroy', $ledger->id) }}" method="POST" style="margin-left:auto;" id="del-show-{{ $ledger->id }}">
            @csrf @method('DELETE')
            <button type="button" class="btn-danger"
                onclick="confirmDel({{ $ledger->id }},'{{ addslashes($ledger->transaction_title) }}')">
                <i class="fa-regular fa-trash-can"></i> Delete
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDel(id, title) {
    Swal.fire({
        title:'Delete Entry?',
        html:'Delete <strong>'+title+'</strong>?<br><small style="color:#64748B;">This cannot be undone.</small>',
        icon:'warning', showCancelButton:true,
        confirmButtonColor:'#EF4444', cancelButtonColor:'#64748B',
        confirmButtonText:'<i class="fa-regular fa-trash-can"></i> Yes, Delete',
        cancelButtonText:'Cancel',
        customClass:{popup:'swal-ldg-popup'}
    }).then(r=>{if(r.isConfirmed)document.getElementById('del-show-'+id).submit();});
}
</script>
<style>.swal-ldg-popup{font-family:'Outfit',sans-serif!important;border-radius:14px!important;}</style>
@endsection
