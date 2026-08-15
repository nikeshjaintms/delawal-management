@extends('admin.layouts.app')
@section('title','Reports')
@section('page-title','Reports')
@section('content')
<style>
.rpt-hub-header { margin-bottom: 28px; }
.rpt-hub-header h2 { font-size: 26px !important; font-weight: 800 !important; color: #FFFFFF !important; margin-bottom: 6px !important; text-shadow: 0 2px 14px rgba(0, 0, 0, 0.7) !important; }
.rpt-hub-header h2 i { color: #60A5FA !important; }
.rpt-hub-header p { font-size: 14.5px !important; color: rgba(255, 255, 255, 0.85) !important; text-shadow: 0 1px 6px rgba(0, 0, 0, 0.5) !important; }

.rpt-section-title {
    font-size: 14px !important; font-weight: 800 !important; color: #FFFFFF !important; text-transform: uppercase !important;
    letter-spacing: 1.6px !important; margin: 36px 0 18px !important; display: flex !important; align-items: center !important; gap: 10px !important;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6) !important;
}
.rpt-section-title i { color: #60A5FA !important; font-size: 16px !important; }
.rpt-section-title::after { content: ''; flex: 1; height: 1.5px; background: rgba(255, 255, 255, 0.22); }

.rpt-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

.rpt-card {
    background: rgba(15, 20, 32, 0.70) !important;
    backdrop-filter: blur(28px) !important;
    -webkit-backdrop-filter: blur(28px) !important;
    border: 1px solid rgba(255, 255, 255, 0.22) !important; border-radius: 18px !important;
    padding: 24px 22px 22px !important; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.45) !important;
    display: flex; flex-direction: column; gap: 16px; justify-content: space-between;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease !important;
    animation: cardIn .35s cubic-bezier(.4,0,.2,1) both;
}
.rpt-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 18px 48px rgba(0, 0, 0, 0.55), 0 0 24px rgba(255, 255, 255, 0.15) !important;
    border-color: rgba(255, 255, 255, 0.45) !important;
}
@keyframes cardIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.rpt-card:nth-child(1){animation-delay:.03s} .rpt-card:nth-child(2){animation-delay:.06s}
.rpt-card:nth-child(3){animation-delay:.09s} .rpt-card:nth-child(4){animation-delay:.12s}
.rpt-card:nth-child(5){animation-delay:.15s} .rpt-card:nth-child(6){animation-delay:.18s}
.rpt-card:nth-child(7){animation-delay:.21s} .rpt-card:nth-child(8){animation-delay:.24s}

.rpt-card-top { display: flex; align-items: flex-start; gap: 14px; }
.rpt-icon {
    width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center;
    justify-content: center; font-size: 19px; flex-shrink: 0;
    background: rgba(59, 130, 246, 0.18) !important;
    color: #60A5FA !important;
    border: 1.5px solid rgba(59, 130, 246, 0.40) !important;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25) !important;
}

.rpt-card-info h3 { font-size: 16px !important; font-weight: 800 !important; color: #FFFFFF !important; margin-bottom: 6px !important; text-shadow: 0 1px 4px rgba(0, 0, 0, 0.4) !important; }
.rpt-card-info p { font-size: 13px !important; color: rgba(255, 255, 255, 0.80) !important; line-height: 1.55 !important; }

.rpt-sub-links {
    display: flex; flex-direction: column; gap: 5px; padding-top: 8px;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
}
.rpt-sub-link {
    display: flex; align-items: center; gap: 8px; padding: 6px 10px;
    border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;
    color: rgba(255, 255, 255, 0.90); transition: all .18s ease;
}
.rpt-sub-link i { font-size: 12px; color: rgba(255, 255, 255, 0.70); width: 14px; text-align: center; }
.rpt-sub-link:hover { background: rgba(255, 255, 255, 0.12); color: #FFFFFF; }
.rpt-sub-link:hover i { color: #60A5FA; }

.rpt-open-btn {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255, 255, 255, 0.14) !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important; color: #FFFFFF !important; padding: 9px 18px; border-radius: 10px;
    font-size: 13px; font-weight: 700; text-decoration: none; margin-top: 4px; align-self: flex-start;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.30) !important; transition: all .2s ease; backdrop-filter: blur(8px) !important;
}
.rpt-open-btn:hover {
    background: rgba(255, 255, 255, 0.25) !important; border-color: rgba(255, 255, 255, 0.60) !important;
    transform: translateY(-2px) !important; box-shadow: 0 6px 24px rgba(0, 0, 0, 0.40) !important; color: #FFFFFF !important;
}
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

