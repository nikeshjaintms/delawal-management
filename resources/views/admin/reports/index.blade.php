@extends('admin.layouts.app')
@section('title','Reports')
@section('page-title','Reports')
@section('content')
<style>
.rpt-hub-header{margin-bottom:28px;}
.rpt-hub-header h2{font-size:24px;font-weight:800;color:#0F172A;margin-bottom:5px;}
.rpt-hub-header p{font-size:14px;color:#64748B;}

.rpt-section-title{font-size:11px;font-weight:700;color:#64748B;text-transform:uppercase;
    letter-spacing:1.5px;margin:28px 0 14px;display:flex;align-items:center;gap:8px;}
.rpt-section-title::after{content:'';flex:1;height:1px;background:#E2E8F0;}

.rpt-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px;}

.rpt-card{
    background:#fff;border:1px solid #E2E8F0;border-radius:16px;
    padding:22px 22px 20px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 18px rgba(0,0,0,0.05);
    display:flex;flex-direction:column;gap:14px;
    transition:transform .22s ease,box-shadow .22s ease,border-color .22s ease;
    animation:cardIn .35s cubic-bezier(.4,0,.2,1) both;
}
.rpt-card:hover{
    transform:translateY(-4px);
    box-shadow:0 4px 8px rgba(0,0,0,0.07),0 18px 40px rgba(0,0,0,0.10);
    border-color:#BFDBFE;
}
@keyframes cardIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
.rpt-card:nth-child(1){animation-delay:.03s} .rpt-card:nth-child(2){animation-delay:.06s}
.rpt-card:nth-child(3){animation-delay:.09s} .rpt-card:nth-child(4){animation-delay:.12s}
.rpt-card:nth-child(5){animation-delay:.15s} .rpt-card:nth-child(6){animation-delay:.18s}
.rpt-card:nth-child(7){animation-delay:.21s} .rpt-card:nth-child(8){animation-delay:.24s}

.rpt-card-top{display:flex;align-items:flex-start;gap:14px;}
.rpt-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;
    justify-content:center;font-size:19px;flex-shrink:0;}
.rpt-icon.blue  {background:rgba(59,130,246,0.1); color:#3B82F6;}
.rpt-icon.green {background:rgba(16,185,129,0.1); color:#10B981;}
.rpt-icon.amber {background:rgba(245,158,11,0.1); color:#F59E0B;}
.rpt-icon.purple{background:rgba(139,92,246,0.1); color:#8B5CF6;}
.rpt-icon.red   {background:rgba(239,68,68,0.1);  color:#EF4444;}
.rpt-icon.sky   {background:rgba(14,165,233,0.1); color:#0EA5E9;}
.rpt-icon.teal  {background:rgba(20,184,166,0.1); color:#14B8A6;}
.rpt-icon.orange{background:rgba(249,115,22,0.1); color:#F97316;}

.rpt-card-info h3{font-size:15px;font-weight:700;color:#0F172A;margin-bottom:4px;}
.rpt-card-info p{font-size:12.5px;color:#64748B;line-height:1.5;}

.rpt-sub-links{display:flex;flex-direction:column;gap:5px;padding-top:4px;
    border-top:1px solid #F1F5F9;}
.rpt-sub-link{display:flex;align-items:center;gap:8px;padding:6px 8px;
    border-radius:8px;text-decoration:none;font-size:13px;font-weight:500;
    color:#475569;transition:all .18s ease;}
.rpt-sub-link i{font-size:12px;color:#94A3B8;width:14px;text-align:center;}
.rpt-sub-link:hover{background:#EFF6FF;color:#2563EB;}
.rpt-sub-link:hover i{color:#3B82F6;}

.rpt-open-btn{display:inline-flex;align-items:center;gap:7px;
    background:linear-gradient(135deg,#3B82F6 0%,#2563EB 100%);
    color:#FFF;padding:9px 18px;border-radius:9px;font-size:13px;font-weight:600;
    text-decoration:none;margin-top:2px;align-self:flex-start;
    box-shadow:0 2px 8px rgba(59,130,246,0.3);transition:all .2s ease;}
.rpt-open-btn:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(59,130,246,0.4);}
</style>

<div class="rpt-hub-header">
    <h2><i class="fa-solid fa-chart-column" style="color:#3B82F6;margin-right:10px;"></i>Reports Centre</h2>
    <p>Select any report below to view filtered data, export PDF, or download Excel.</p>
</div>

{{-- ── GST Reports ── --}}
<div class="rpt-section-title"><i class="fa-solid fa-percent" style="color:#F59E0B;"></i> GST & Accounting Reports</div>
<div class="rpt-cards">

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon amber"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="rpt-card-info">
                <h3>GST Sales Report</h3>
                <p>Tax-wise outward supply report with HSN codes and GST breakup.</p>
            </div>
        </div>
        <a href="{{ route('reports.gst-sales') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon orange"><i class="fa-solid fa-cart-flatbed"></i></div>
            <div class="rpt-card-info">
                <h3>GST Purchase Report</h3>
                <p>Inward supply and input tax credit (ITC) report.</p>
            </div>
        </div>
        <a href="{{ route('reports.gst-purchase') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon green"><i class="fa-solid fa-circle-plus"></i></div>
            <div class="rpt-card-info">
                <h3>Credit Note</h3>
                <p>All issued credit notes with amount, party, and reason.</p>
            </div>
        </div>
        <a href="{{ route('reports.credit-note') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon red"><i class="fa-solid fa-circle-minus"></i></div>
            <div class="rpt-card-info">
                <h3>Debit Note</h3>
                <p>All issued debit notes with amount, party, and reason.</p>
            </div>
        </div>
        <a href="{{ route('reports.debit-note') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon purple"><i class="fa-solid fa-scale-balanced"></i></div>
            <div class="rpt-card-info">
                <h3>Profit &amp; Loss Statement</h3>
                <p>Income vs expense summary — net profit or loss for any period.</p>
            </div>
        </div>
        <a href="{{ route('reports.profit-loss') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon blue"><i class="fa-solid fa-sheet-plastic"></i></div>
            <div class="rpt-card-info">
                <h3>Balance Sheet</h3>
                <p>Assets, liabilities, and net worth snapshot of the firm.</p>
            </div>
        </div>
        <a href="{{ route('reports.balance-sheet') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon sky"><i class="fa-solid fa-water"></i></div>
            <div class="rpt-card-info">
                <h3>Cash Flow Report</h3>
                <p>Month-wise cash inflow and outflow with net balance.</p>
            </div>
        </div>
        <a href="{{ route('reports.cash-flow') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

</div>

{{-- ── Business Reports ── --}}
<div class="rpt-section-title"><i class="fa-solid fa-briefcase" style="color:#3B82F6;"></i> Business Reports</div>
<div class="rpt-cards">

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon green"><i class="fa-solid fa-handshake"></i></div>
            <div class="rpt-card-info">
                <h3>Sales Report</h3>
                <p>Property-wise sales report with customer, broker, and amount details.</p>
            </div>
        </div>
        <a href="{{ route('reports.sales') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon blue"><i class="fa-solid fa-money-bill-transfer"></i></div>
            <div class="rpt-card-info">
                <h3>Payment Report</h3>
                <p>All payment transactions with mode, amount, and status breakdown.</p>
            </div>
        </div>
        <a href="{{ route('reports.payments') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon teal"><i class="fa-solid fa-house-circle-check"></i></div>
            <div class="rpt-card-info">
                <h3>Rental Report</h3>
                <p>Rental agreements, tenants, rent amounts, and lease status.</p>
            </div>
        </div>
        <a href="{{ route('reports.rentals') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon amber"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="rpt-card-info">
                <h3>Inventory Report</h3>
                <p>Material stock levels, categories, and low-stock status overview.</p>
            </div>
        </div>
        <a href="{{ route('reports.inventory') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon red"><i class="fa-solid fa-receipt"></i></div>
            <div class="rpt-card-info">
                <h3>Expense Report</h3>
                <p>Category-wise expenses with approval status and payment mode details.</p>
            </div>
        </div>
        <a href="{{ route('expense-report.index') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

    <div class="rpt-card">
        <div class="rpt-card-top">
            <div class="rpt-icon purple"><i class="fa-solid fa-landmark"></i></div>
            <div class="rpt-card-info">
                <h3>Loan Report</h3>
                <p>Bank-wise and customer-wise loan summary with EMI progress.</p>
            </div>
        </div>
        <a href="{{ route('loan-report.index') }}" class="rpt-open-btn"><i class="fa-solid fa-arrow-right"></i> Open Report</a>
    </div>

</div>
@endsection

