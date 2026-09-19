@extends('admin.layouts.app')
@section('title', 'Project Expenses')
@section('page-title', 'Expense Management')
@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.crud-header { display: flex; justify-content: space-between; align-items: center; margin-top: 4px; margin-bottom: 22px; flex-wrap: wrap; gap: 15px; }
.crud-title h2 { font-size: 26px; font-weight: 800; color: #FFFFFF !important; margin-bottom: 6px; letter-spacing: -0.3px; }
.crud-title p { font-size: 14px; color: #CBD5E1 !important; font-weight: 500; margin: 0; }

.btn-gold {
    background: #2563EB !important; color: #FFFFFF !important; padding: 10px 20px;
    border-radius: 12px; font-size: 13.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all .25s ease;
    box-shadow: 0 4px 18px rgba(37,99,235,0.38); text-decoration: none !important;
}
.btn-gold:hover { background: #1D4ED8 !important; color: #FFFFFF !important; transform: translateY(-2px); box-shadow: 0 6px 24px rgba(37,99,235,0.52); }

.btn-outline-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 8px 16px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13px; font-weight: 600; border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-radius: 10px; text-decoration: none !important; transition: all .25s ease; cursor: pointer;
}
.btn-outline-custom:hover { background: rgba(255, 255, 255, 0.15) !important; color: #FFFFFF !important; transform: translateY(-2px); }

/* Prominent Project Selector Box */
.project-selector-box {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.15) 0%, rgba(20, 27, 41, 0.70) 100%) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.35) !important;
    border-radius: 20px !important; padding: 22px 26px !important;
    margin-bottom: 24px; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35) !important;
}
.selector-header {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 14px; margin-bottom: 14px;
}
.selector-label {
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 800; color: #FFFFFF; text-transform: uppercase; letter-spacing: 0.8px;
}
.selector-label i { font-size: 18px; color: #60A5FA; }

.project-hero-select {
    width: 100%; padding: 13px 18px; background: rgba(16, 22, 34, 0.85) !important;
    border: 1.5px solid rgba(59, 130, 246, 0.50) !important; border-radius: 12px !important;
    font-size: 15px; font-weight: 700; color: #FFFFFF !important; outline: none; transition: all .25s ease;
    box-shadow: 0 4px 16px rgba(0,0,0,0.30); cursor: pointer;
}
.project-hero-select:focus { border-color: #60A5FA !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.30) !important; }
.project-hero-select option { background: #101622 !important; color: #FFFFFF !important; padding: 10px; }

/* Quick Project Filter Pills */
.project-quick-pills {
    display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-top: 14px;
}
.quick-pill {
    padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;
    text-decoration: none !important; transition: all .2s ease;
    background: rgba(255, 255, 255, 0.06); color: #CBD5E1; border: 1px solid rgba(255, 255, 255, 0.12);
    display: inline-flex; align-items: center; gap: 6px;
}
.quick-pill:hover { background: rgba(59, 130, 246, 0.20); color: #FFFFFF; border-color: rgba(59, 130, 246, 0.40); }
.quick-pill.active {
    background: #2563EB !important; color: #FFFFFF !important; border-color: #3B82F6 !important;
    box-shadow: 0 4px 14px rgba(37,99,235,0.40);
}

/* Global Financial Summary Cards */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 14px; margin-bottom: 22px; }
.kpi-card {
    background: rgba(16, 22, 34, 0.70) !important;
    backdrop-filter: blur(16px) saturate(150%) !important;
    -webkit-backdrop-filter: blur(16px) saturate(150%) !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    border-radius: 16px !important; padding: 13px 16px !important;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25) !important;
    transition: all .25s ease; position: relative; overflow: hidden;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0, 0, 0, 0.35) !important; }

.kpi-total {
    border-color: rgba(245, 158, 11, 0.30) !important;
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-po {
    border-color: rgba(139, 92, 246, 0.30) !important;
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-direct {
    border-color: rgba(59, 130, 246, 0.30) !important;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-contractor {
    border-color: rgba(244, 63, 94, 0.30) !important;
    background: linear-gradient(135deg, rgba(244, 63, 94, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-broker {
    border-color: rgba(168, 85, 247, 0.30) !important;
    background: linear-gradient(135deg, rgba(168, 85, 247, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-land {
    border-color: rgba(20, 184, 166, 0.30) !important;
    background: linear-gradient(135deg, rgba(20, 184, 166, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}
.kpi-approved {
    border-color: rgba(16, 185, 129, 0.30) !important;
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 22, 34, 0.75) 100%) !important;
}

.kpi-icon-box {
    width: 38px; height: 38px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.kpi-total .kpi-icon-box { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.kpi-po .kpi-icon-box { background: rgba(139, 92, 246, 0.18); color: #C4B5FD; border: 1px solid rgba(139, 92, 246, 0.35); }
.kpi-direct .kpi-icon-box { background: rgba(59, 130, 246, 0.18); color: #60A5FA; border: 1px solid rgba(59, 130, 246, 0.35); }
.kpi-contractor .kpi-icon-box { background: rgba(244, 63, 94, 0.18); color: #FB7185; border: 1px solid rgba(244, 63, 94, 0.35); }
.kpi-broker .kpi-icon-box { background: rgba(168, 85, 247, 0.18); color: #D8B4FE; border: 1px solid rgba(168, 85, 247, 0.35); }
.kpi-land .kpi-icon-box { background: rgba(20, 184, 166, 0.18); color: #5EEAD4; border: 1px solid rgba(20, 184, 166, 0.35); }
.kpi-approved .kpi-icon-box { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }

.kpi-content { flex: 1; min-width: 0; }
.kpi-label { font-size: 10.5px; font-weight: 700; color: #CBD5E1 !important; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
.kpi-value { font-size: 17px !important; font-weight: 800 !important; line-height: 1.25; letter-spacing: -0.2px; margin: 1px 0; color: #FFFFFF !important; text-shadow: 0 1px 6px rgba(0,0,0,0.45) !important; }
.kpi-sub { font-size: 11px; color: #94A3B8; margin-top: 1px; }

/* Filter Bar */
.filter-card {
    background: rgba(20, 27, 41, 0.55) !important;
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    border-radius: 18px; padding: 18px 20px; margin-bottom: 24px;
}
.filter-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; align-items: flex-end; }
.filter-group { display: flex; flex-direction: column; gap: 6px; }
.filter-label { font-size: 11.5px; font-weight: 700; color: #CBD5E1; text-transform: uppercase; letter-spacing: 0.5px; }
.filter-control {
    width: 100%; padding: 9px 12px; background: rgba(16, 22, 34, 0.70) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.12) !important; border-radius: 10px !important;
    font-size: 13.5px; color: #FFFFFF !important; outline: none; transition: all .2s ease;
    box-sizing: border-box !important;
}
.filter-control:focus { border-color: #3B82F6 !important; }
select.filter-control option { background: #101622 !important; color: #FFFFFF !important; }
.filter-actions { display: flex; gap: 8px; align-items: center; }
.btn-filter {
    background: #2563EB !important; color: #FFFFFF !important; padding: 9px 18px;
    border-radius: 10px; font-size: 13px; font-weight: 700; border: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-filter:hover { background: #1D4ED8 !important; }
.btn-reset {
    background: rgba(255, 255, 255, 0.08); color: #CBD5E1; padding: 9px 14px;
    border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.12); display: inline-flex; align-items: center; gap: 6px;
}
.btn-reset:hover { background: rgba(255, 255, 255, 0.15); color: #FFFFFF; }

/* Project Expense Box */
.project-expense-card {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important; padding: 24px !important;
    margin-bottom: 24px; box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35) !important;
    transition: all .25s ease;
}
.project-expense-card:hover { border-color: rgba(59, 130, 246, 0.35) !important; }

.project-card-header {
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: 16px; padding-bottom: 18px;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10);
}
.project-header-left { display: flex; align-items: center; gap: 14px; }
.project-avatar {
    width: 48px; height: 48px; border-radius: 14px;
    background: rgba(59, 130, 246, 0.18); border: 1.5px solid rgba(59, 130, 246, 0.35);
    color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
}
.project-title h3 { font-size: 19px; font-weight: 800; color: #FFFFFF !important; margin: 0 0 4px 0; }
.project-title p { font-size: 12.5px; color: #94A3B8 !important; margin: 0; display: flex; gap: 12px; flex-wrap: wrap; }

.project-cost-badge {
    background: rgba(245, 158, 11, 0.15); border: 1.5px solid rgba(245, 158, 11, 0.35);
    border-radius: 14px; padding: 8px 20px; text-align: right;
}
.project-cost-badge .badge-label { font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase; }
.project-cost-badge .badge-amount { font-size: 22px; font-weight: 800; color: #FBBF24; line-height: 1.2; }

/* Financial Breakdown Ribbon */
.breakdown-ribbon {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px; margin-top: 18px; margin-bottom: 18px;
}
.breakdown-item {
    background: rgba(16, 22, 34, 0.50); border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px; padding: 10px 14px; display: flex; align-items: center; gap: 10px;
}
.breakdown-item i { font-size: 16px; }
.breakdown-item .b-label { font-size: 10.5px; font-weight: 700; color: #CBD5E1; text-transform: uppercase; }
.breakdown-item .b-val { font-size: 15px; font-weight: 800; margin-top: 2px; }

/* Category Pills Bar */
.category-pills-bar {
    display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
    margin-bottom: 18px; padding: 12px 14px;
    background: rgba(16, 22, 34, 0.40); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.06);
}
.cat-pill {
    background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.25);
    color: #FBBF24; padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 6px;
}
.cat-pill span.c-count {
    background: rgba(245, 158, 11, 0.25); color: #FFFFFF; font-size: 10px; padding: 1px 5px; border-radius: 4px;
}
.cat-pill.cat-contractor {
    background: rgba(244, 63, 94, 0.12); border-color: rgba(244, 63, 94, 0.30); color: #FB7185;
}
.cat-pill.cat-contractor span.c-count {
    background: rgba(244, 63, 94, 0.30);
}
.cat-pill.cat-broker {
    background: rgba(168, 85, 247, 0.12); border-color: rgba(168, 85, 247, 0.30); color: #D8B4FE;
}
.cat-pill.cat-broker span.c-count {
    background: rgba(168, 85, 247, 0.30);
}
.cat-pill.cat-land {
    background: rgba(20, 184, 166, 0.12); border-color: rgba(20, 184, 166, 0.30); color: #5EEAD4;
}
.cat-pill.cat-land span.c-count {
    background: rgba(20, 184, 166, 0.30);
}

/* Tabs System */
.project-tab-nav {
    display: flex; gap: 8px; margin-bottom: 14px; border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding-bottom: 10px; flex-wrap: wrap;
}
.tab-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #CBD5E1;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all .2s ease;
}
.tab-btn:hover {
    background: rgba(255, 255, 255, 0.10);
    color: #FFFFFF;
}
.tab-btn.active {
    background: #2563EB !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}
.tab-btn.tab-contractor.active {
    background: linear-gradient(135deg, #E11D48 0%, #BE123C 100%) !important;
    border-color: #FB7185 !important;
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.40);
}
.tab-btn.tab-broker.active {
    background: linear-gradient(135deg, #9333EA 0%, #7E22CE 100%) !important;
    border-color: #C084FC !important;
    box-shadow: 0 4px 14px rgba(147, 51, 234, 0.40);
}
.tab-btn.tab-land.active {
    background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
    border-color: #2DD4BF !important;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.40);
}
.project-tab-pane.hidden { display: none !important; }

/* Ledger Table */
.table-responsive-wrapper { width: 100%; overflow-x: auto; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.08); }
.premium-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 13px; }
.premium-table th {
    padding: 12px 14px !important; background: rgba(255, 255, 255, 0.04) !important;
    color: #94A3B8 !important; font-weight: 800; font-size: 11px;
    text-transform: uppercase; letter-spacing: 0.8px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.08) !important;
    white-space: nowrap !important;
}
.premium-table td {
    padding: 12px 14px !important; border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    font-size: 13px; color: #E2E8F0 !important; font-weight: 500; vertical-align: middle;
    white-space: nowrap !important;
}
.premium-table tbody tr:hover { background: rgba(255, 255, 255, 0.04) !important; }

.cat-badge { background: rgba(245, 158, 11, 0.15); color: #FBBF24; padding: 3px 8px; border-radius: 5px; font-size: 11.5px; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.30); }
.status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 10.5px; font-weight: 700; border-radius: 14px; text-transform: uppercase; }
.status-approved { background: rgba(16, 185, 129, 0.18); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.35); }
.status-pending  { background: rgba(245, 158, 11, 0.18); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.35); }
.status-rejected { background: rgba(239, 68, 68, 0.18); color: #F87171; border: 1px solid rgba(239, 68, 68, 0.35); }

.btn-action-expense {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(59, 130, 246, 0.16) !important;
    border: 1px solid rgba(59, 130, 246, 0.40) !important;
    color: #60A5FA !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-expense:hover {
    background: rgba(59, 130, 246, 0.30) !important;
    border-color: #60A5FA !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
}

.btn-action-po {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(139, 92, 246, 0.16) !important;
    border: 1px solid rgba(139, 92, 246, 0.40) !important;
    color: #C4B5FD !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-po:hover {
    background: rgba(139, 92, 246, 0.30) !important;
    border-color: #C4B5FD !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.25);
}

.btn-action-contractor {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(244, 63, 94, 0.16) !important;
    border: 1px solid rgba(244, 63, 94, 0.40) !important;
    color: #FB7185 !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-contractor:hover {
    background: rgba(244, 63, 94, 0.30) !important;
    border-color: #FB7185 !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(244, 63, 94, 0.25);
}

.btn-action-broker {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(168, 85, 247, 0.16) !important;
    border: 1px solid rgba(168, 85, 247, 0.40) !important;
    color: #D8B4FE !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-broker:hover {
    background: rgba(168, 85, 247, 0.30) !important;
    border-color: #D8B4FE !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(168, 85, 247, 0.25);
}

.btn-action-land {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(20, 184, 166, 0.16) !important;
    border: 1px solid rgba(20, 184, 166, 0.40) !important;
    color: #5EEAD4 !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-land:hover {
    background: rgba(20, 184, 166, 0.30) !important;
    border-color: #5EEAD4 !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(20, 184, 166, 0.25);
}

.btn-action-view {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 6px 14px; min-height: 34px; border-radius: 9px;
    font-size: 12.5px; font-weight: 700; text-decoration: none !important;
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    color: #E2E8F0 !important;
    transition: all 0.2s ease;
    cursor: pointer;
}
.btn-action-view:hover {
    background: rgba(255, 255, 255, 0.18) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    transform: translateY(-1px);
}

.project-footer-actions {
    display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;
    gap: 10px; margin-top: 16px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08);
}

.alert-success { background: rgba(16, 185, 129, 0.15) !important; border: 1px solid rgba(16, 185, 129, 0.30) !important; color: #34D399 !important; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13.5px; display: flex; align-items: center; gap: 8px; font-weight: 600; }
.empty-state { text-align: center; padding: 48px 20px; color: #94A3B8; }
.empty-state i { font-size: 42px; color: #60A5FA; margin-bottom: 12px; display: block; }
</style>

<div class="crud-header">
    <div class="crud-title">
        <h2>Project Expenses</h2>
        <p>Auto-aggregated project cost analysis, material procurement, contractor payments &amp; ledger breakdown.</p>
    </div>
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <a href="{{ route('expenses.project-wise.pdf', request()->query()) }}" target="_blank" class="btn-gold" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%) !important; border: 1px solid #F87171 !important; color: #FFFFFF !important; box-shadow: 0 4px 16px rgba(239, 68, 68, 0.40);">
            <i class="fa-solid fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('expenses.create', request('project_id') ? ['project_id' => request('project_id')] : []) }}" class="btn-gold">
            <i class="fa-solid fa-plus"></i> Add Expense
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- 🏢 Interactive Project Selector --}}
<div class="project-selector-box">
    <form method="GET" action="{{ route('expenses.project-wise') }}" id="projectSelectForm">
        {{-- Keep other filters intact --}}
        @if(request('firm_id')) <input type="hidden" name="firm_id" value="{{ request('firm_id') }}"> @endif
        @if(request('filter_category')) <input type="hidden" name="filter_category" value="{{ request('filter_category') }}"> @endif
        @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
        @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif
        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

        <div class="selector-header">
            <div class="selector-label">
                <i class="fa-solid fa-city"></i>
                <span>Select Project to View Expenses:</span>
            </div>
            @if(request('project_id'))
                <a href="{{ route('expenses.project-wise') }}" style="color: #60A5FA; font-size: 12.5px; font-weight: 700; text-decoration: none;">
                    <i class="fa-solid fa-layer-group"></i> View All Projects
                </a>
            @endif
        </div>

        <select name="project_id" class="project-hero-select" onchange="this.form.submit()">
            <option value="">📁 — All Projects (Auto-Fetched Overview) —</option>
            @foreach($projectsList as $pr)
                <option value="{{ $pr->id }}" {{ request('project_id') == $pr->id ? 'selected' : '' }}>
                    🏢 {{ $pr->project_name }} {{ $pr->firm ? '('.$pr->firm->firm_name.')' : '' }} {{ $pr->propertyMaster ? '• '.$pr->propertyMaster->property_name : '' }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Quick Project Badges --}}
    <div class="project-quick-pills">
        <span style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Quick Select:</span>
        <a href="{{ route('expenses.project-wise', array_merge(request()->except('project_id'))) }}" class="quick-pill {{ !request('project_id') ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group"></i> All Projects
        </a>
        @foreach($projectsList->take(8) as $pr)
            <a href="{{ route('expenses.project-wise', array_merge(request()->except('project_id'), ['project_id' => $pr->id])) }}"
               class="quick-pill {{ request('project_id') == $pr->id ? 'active' : '' }}">
                {{ $pr->project_name }}
            </a>
        @endforeach
    </div>
</div>

{{-- Global / Selected Financial Summary KPIs --}}
<div class="kpi-grid">
    <div class="kpi-card kpi-total">
        <div class="kpi-icon-box"><i class="fa-solid fa-calculator"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">{{ request('project_id') ? 'Selected Project Total' : 'Grand Total Expenses' }}</div>
            <div class="kpi-value">₹{{ number_format($grandTotal, 2) }}</div>
            <div class="kpi-sub">{{ request('project_id') ? 'Single Project Ledger' : 'Across '.$totalProjectsWithExpenses.' Active Projects' }}</div>
        </div>
    </div>

    <div class="kpi-card kpi-po">
        <div class="kpi-icon-box"><i class="fa-solid fa-file-invoice"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">PO &amp; Procurement Cost</div>
            <div class="kpi-value">₹{{ number_format($grandPoTotal, 2) }}</div>
            <div class="kpi-sub">Materials &amp; Purchase Orders</div>
        </div>
    </div>

    <div class="kpi-card kpi-direct">
        <div class="kpi-icon-box"><i class="fa-solid fa-receipt"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Direct Site Expenses</div>
            <div class="kpi-value">₹{{ number_format($grandDirectTotal, 2) }}</div>
            <div class="kpi-sub">Labour, Fuel, Operations &amp; Site</div>
        </div>
    </div>

    <div class="kpi-card kpi-contractor">
        <div class="kpi-icon-box"><i class="fa-solid fa-helmet-safety"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Contractor Payments</div>
            <div class="kpi-value">₹{{ number_format($grandContractorTotal, 2) }}</div>
            <div class="kpi-sub">Contractor Work &amp; Installments</div>
        </div>
    </div>

    <div class="kpi-card kpi-broker">
        <div class="kpi-icon-box"><i class="fa-solid fa-handshake"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Broker Commissions</div>
            <div class="kpi-value">₹{{ number_format($grandBrokerTotal, 2) }}</div>
            <div class="kpi-sub">Agent Sales Payouts</div>
        </div>
    </div>

    <div class="kpi-card kpi-land">
        <div class="kpi-icon-box"><i class="fa-solid fa-mountain-sun"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Land &amp; Acquisition</div>
            <div class="kpi-value">₹{{ number_format($grandLandTotal, 2) }}</div>
            <div class="kpi-sub">Seller &amp; Registry Payments</div>
        </div>
    </div>

    <div class="kpi-card kpi-approved">
        <div class="kpi-icon-box"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-content">
            <div class="kpi-label">Approved &amp; Paid</div>
            <div class="kpi-value">₹{{ number_format($grandApprovedTotal, 2) }}</div>
            <div class="kpi-sub">
                @if($grandPendingTotal > 0)
                    <span style="color: #F87171; font-weight: 600;">Pending: ₹{{ number_format($grandPendingTotal, 2) }}</span>
                @else
                    <span style="color: #34D399; font-weight: 600;">All Approved &amp; Settled</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="filter-card">
    <form method="GET" action="{{ route('expenses.project-wise') }}" class="filter-form">
        @if(request('project_id'))
            <input type="hidden" name="project_id" value="{{ request('project_id') }}">
        @endif

        @if(auth()->user() && auth()->user()->isAdmin())
        <div class="filter-group">
            <label class="filter-label">Firm</label>
            <select name="firm_id" class="filter-control">
                <option value="">All Firms</option>
                @foreach($firms as $f)
                    <option value="{{ $f->id }}" {{ request('firm_id') == $f->id ? 'selected' : '' }}>{{ $f->firm_name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="filter-group">
            <label class="filter-label">Category</label>
            <select name="filter_category" class="filter-control">
                <option value="">All Categories</option>
                @if(isset($categories))
                    @foreach($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('filter_category') == $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Approval Status</label>
            <select name="filter_status" class="filter-control">
                <option value="">All Status</option>
                <option value="Pending" {{ request('filter_status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ request('filter_status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ request('filter_status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-label">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-control">
        </div>

        <div class="filter-group">
            <label class="filter-label">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-control">
        </div>

        <div class="filter-group">
            <label class="filter-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title, contractor, ref, PO #..." class="filter-control">
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('expenses.project-wise', request('project_id') ? ['project_id' => request('project_id')] : []) }}" class="btn-reset">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        </div>
    </form>
</div>

{{-- Projects List with Auto-Aggregated Expenses & All Payments --}}
@forelse($projectsData as $idx => $pData)
    @php
        $p = $pData['project'];
        $expList = $pData['expenses'];
        $cpList = $pData['contractorPayments'];
        $bcList = $pData['brokerCommissions'];
        $lpList = $pData['landPayments'];
        $hasExpenses = $expList->isNotEmpty();
        $hasContractors = $cpList->isNotEmpty();
        $hasBrokers = $bcList->isNotEmpty();
        $hasLand = $lpList->isNotEmpty();

        // Default active tab
        $defaultTab = 'expenses';
        if (!$hasExpenses && $hasContractors) {
            $defaultTab = 'contractors';
        } elseif (!$hasExpenses && !$hasContractors && $hasBrokers) {
            $defaultTab = 'brokers';
        } elseif (!$hasExpenses && !$hasContractors && !$hasBrokers && $hasLand) {
            $defaultTab = 'land';
        }
    @endphp
    <div class="project-expense-card" id="project-card-{{ $p->id }}">
        <div class="project-card-header">
            <div class="project-header-left">
                <div class="project-avatar">
                    <i class="fa-solid fa-city"></i>
                </div>
                <div class="project-title">
                    <h3>{{ $p->project_name }}</h3>
                    <p>
                        @if($p->firm) <span><i class="fa-solid fa-building" style="color: #60A5FA;"></i> {{ $p->firm->firm_name }}</span> @endif
                        @if($p->propertyMaster) <span><i class="fa-solid fa-map-location-dot" style="color: #FBBF24;"></i> {{ $p->propertyMaster->property_name }}</span> @endif
                        <span><i class="fa-solid fa-receipt" style="color: #34D399;"></i> {{ $pData['expensesCount'] }} Direct/PO Expenses</span>
                        <span><i class="fa-solid fa-helmet-safety" style="color: #FB7185;"></i> {{ $pData['contractorsCount'] }} Contractor Payments</span>
                        <span><i class="fa-solid fa-handshake" style="color: #D8B4FE;"></i> {{ $pData['brokersCount'] }} Broker Commissions</span>
                        <span><i class="fa-solid fa-mountain-sun" style="color: #5EEAD4;"></i> {{ $pData['landCount'] }} Land Payments</span>
                    </p>
                </div>
            </div>

            <div class="project-cost-badge">
                <div class="badge-label">Project Total Expense</div>
                <div class="badge-amount">₹{{ number_format($pData['totalCost'], 2) }}</div>
            </div>
        </div>

        {{-- Financial Breakdown Ribbon --}}
        <div class="breakdown-ribbon">
            <div class="breakdown-item" style="border-color: rgba(139, 92, 246, 0.25);">
                <i class="fa-solid fa-file-invoice" style="color: #C4B5FD;"></i>
                <div>
                    <div class="b-label">PO &amp; Materials</div>
                    <div class="b-val" style="color: #C4B5FD;">₹{{ number_format($pData['poCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(59, 130, 246, 0.25);">
                <i class="fa-solid fa-receipt" style="color: #60A5FA;"></i>
                <div>
                    <div class="b-label">Direct Site Expense</div>
                    <div class="b-val" style="color: #60A5FA;">₹{{ number_format($pData['directCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(244, 63, 94, 0.25);">
                <i class="fa-solid fa-helmet-safety" style="color: #FB7185;"></i>
                <div>
                    <div class="b-label">Contractor Payments</div>
                    <div class="b-val" style="color: #FB7185;">₹{{ number_format($pData['contractorCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(168, 85, 247, 0.25);">
                <i class="fa-solid fa-handshake" style="color: #D8B4FE;"></i>
                <div>
                    <div class="b-label">Broker Commission</div>
                    <div class="b-val" style="color: #D8B4FE;">₹{{ number_format($pData['brokerCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(20, 184, 166, 0.25);">
                <i class="fa-solid fa-mountain-sun" style="color: #5EEAD4;"></i>
                <div>
                    <div class="b-label">Land Acquisition</div>
                    <div class="b-val" style="color: #5EEAD4;">₹{{ number_format($pData['landCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(16, 185, 129, 0.25);">
                <i class="fa-solid fa-circle-check" style="color: #34D399;"></i>
                <div>
                    <div class="b-label">Approved / Settled</div>
                    <div class="b-val" style="color: #34D399;">₹{{ number_format($pData['approvedCost'], 2) }}</div>
                </div>
            </div>

            <div class="breakdown-item" style="border-color: rgba(245, 158, 11, 0.25);">
                <i class="fa-solid fa-clock" style="color: #FBBF24;"></i>
                <div>
                    <div class="b-label">Pending Approval</div>
                    <div class="b-val" style="color: #FBBF24;">₹{{ number_format($pData['pendingCost'], 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Category Breakdown Pills --}}
        @if(!empty($pData['categories']))
        <div class="category-pills-bar">
            <span style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase; margin-right: 4px;">
                <i class="fa-solid fa-tags" style="color: #FBBF24;"></i> Cost Breakdown:
            </span>
            @foreach($pData['categories'] as $catName => $cDetails)
                @php
                    $catClass = '';
                    if ($catName === 'Contractor Payments') $catClass = 'cat-contractor';
                    elseif ($catName === 'Broker Commission') $catClass = 'cat-broker';
                    elseif ($catName === 'Land & Acquisition Payment') $catClass = 'cat-land';
                @endphp
                <div class="cat-pill {{ $catClass }}">
                    <span>{{ $catName }}: <strong>₹{{ number_format($cDetails['total'], 2) }}</strong></span>
                    <span class="c-count">{{ $cDetails['count'] }}</span>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Project Multi-Tabs Navigation --}}
        <div class="project-tab-nav">
            <button type="button" class="tab-btn {{ $defaultTab === 'expenses' ? 'active' : '' }}" onclick="switchProjectTab({{ $p->id }}, 'expenses')">
                <i class="fa-solid fa-receipt"></i> Direct &amp; PO Expenses ({{ $pData['expensesCount'] }})
            </button>
            <button type="button" class="tab-btn tab-contractor {{ $defaultTab === 'contractors' ? 'active' : '' }}" onclick="switchProjectTab({{ $p->id }}, 'contractors')">
                <i class="fa-solid fa-helmet-safety"></i> Contractor Payments ({{ $pData['contractorsCount'] }})
            </button>
            <button type="button" class="tab-btn tab-broker {{ $defaultTab === 'brokers' ? 'active' : '' }}" onclick="switchProjectTab({{ $p->id }}, 'brokers')">
                <i class="fa-solid fa-handshake"></i> Broker Commissions ({{ $pData['brokersCount'] }})
            </button>
            <button type="button" class="tab-btn tab-land {{ $defaultTab === 'land' ? 'active' : '' }}" onclick="switchProjectTab({{ $p->id }}, 'land')">
                <i class="fa-solid fa-mountain-sun"></i> Land &amp; Seller Payments ({{ $pData['landCount'] }})
            </button>
        </div>

        {{-- Tab 1: Expense Ledger Table --}}
        <div id="tab-expenses-{{ $p->id }}" class="project-tab-pane {{ $defaultTab === 'expenses' ? '' : 'hidden' }}">
            @if($hasExpenses)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Expense Title / Details</th>
                            <th>Source / Type</th>
                            <th>Category</th>
                            <th>Paid To / Vendor</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Ref No</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expList as $eIdx => $exp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($eIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <strong style="color: #FFFFFF; font-weight: 700;">{{ $exp->expense_title ?? $exp->description }}</strong>
                                @if($exp->relationLoaded('properties') && $exp->properties->isNotEmpty())
                                    <div style="font-size: 11px; color: #60A5FA; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                        @foreach($exp->properties as $ep)
                                             <span style="background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.30); padding: 1px 6px; border-radius: 4px; font-size: 10.5px;">
                                                <i class="fa-solid fa-house"></i> {{ $ep->property_name }} {{ $ep->unit_no ? '· Unit '.$ep->unit_no : '' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif($exp->property)
                                    <div style="font-size: 11px; color: #60A5FA; margin-top: 2px;">
                                        <i class="fa-solid fa-house"></i> {{ $exp->property->property_name }} {{ $exp->property->unit_no ? '· Unit '.$exp->property->unit_no : '' }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($exp->purchase_order_id && $exp->purchaseOrder)
                                    <a href="{{ route('purchase-orders.show', $exp->purchase_order_id) }}" style="background: rgba(139, 92, 246, 0.20); color: #C4B5FD; border: 1px solid rgba(139, 92, 246, 0.40); padding: 2px 7px; border-radius: 6px; font-size: 11px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-file-invoice"></i> PO #{{ $exp->purchaseOrder->po_number }}
                                    </a>
                                @else
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 2px 7px; border-radius: 6px; font-size: 11px; font-weight: 600;">
                                        Direct Site Expense
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="cat-badge">
                                    {{ $exp->expense_category ?: ($exp->expenseCategory->name ?? 'General') }}
                                </span>
                            </td>
                            <td style="color: #CBD5E1;">
                                {{ $exp->paid_to ?: ($exp->vendor->name ?? ($exp->purchaseOrder?->vendor?->name ?? '—')) }}
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($exp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $exp->payment_mode ?: 'Cash' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $exp->reference_no ?: ($exp->bill_no ?: '—') }}
                            </td>
                            <td style="text-align: center;">
                                @php $st = strtolower($exp->approval_status ?? 'pending'); @endphp
                                <span class="status-badge status-{{ $st }}">
                                    {{ ucfirst($exp->approval_status ?? 'Pending') }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    <a href="{{ route('expenses.show', $exp->id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;" title="View Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $exp->id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;" title="Edit Expense">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this expense voucher?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px; background: rgba(239, 68, 68, 0.15) !important; border-color: rgba(239, 68, 68, 0.35) !important; color: #F87171 !important;" title="Delete Expense">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding: 16px; background: rgba(16, 22, 34, 0.30); border-radius: 10px; color: #94A3B8; font-size: 13px; text-align: center;">
                <i class="fa-solid fa-circle-info" style="color: #60A5FA; margin-right: 6px;"></i> No direct or PO expenses recorded for this project yet.
            </div>
            @endif
        </div>

        {{-- Tab 2: Contractor Payments Ledger Table --}}
        <div id="tab-contractors-{{ $p->id }}" class="project-tab-pane {{ $defaultTab === 'contractors' ? '' : 'hidden' }}">
            @if($hasContractors)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Contractor Name</th>
                            <th>Work Type / Scope</th>
                            <th>Assigned Plot / Unit</th>
                            <th>Amount Paid</th>
                            <th>Payment Mode &amp; Bank</th>
                            <th>Ref / Bill No</th>
                            <th>Type / Remarks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cpList as $cIdx => $cp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($cIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $cp->payment_date ? \Carbon\Carbon::parse($cp->payment_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                @if($cp->contractor)
                                    <a href="{{ route('contractors.show', $cp->contractor_id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-helmet-safety" style="color: #FB7185; font-size: 12px;"></i>
                                        {{ $cp->contractor->contractor_name }}
                                    </a>
                                @else
                                    <span style="color: #CBD5E1; font-weight: 700;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="cat-badge" style="background: rgba(244, 63, 94, 0.15); color: #FDA4AF; border-color: rgba(244, 63, 94, 0.30);">
                                    {{ $cp->contractor->work_type ?? 'Contractor Work' }}
                                </span>
                            </td>
                            <td>
                                @if($cp->property)
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 1px 6px; border-radius: 4px; font-size: 11px;">
                                        <i class="fa-solid fa-house"></i> {{ $cp->property->property_name }}
                                    </span>
                                @elseif($cp->contractor && $cp->contractor->relationLoaded('properties') && $cp->contractor->properties->isNotEmpty())
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 1px 6px; border-radius: 4px; font-size: 11px;">
                                        <i class="fa-solid fa-house"></i> {{ $cp->contractor->properties->pluck('property_name')->first() }}
                                    </span>
                                @else
                                    <span style="color: #94A3B8;">Project-Wide</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($cp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $cp->payment_mode ?: 'Cash' }} {{ $cp->bank_name ? '· ' . $cp->bank_name : '' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $cp->reference_no ?: ($cp->bill_no ?: '—') }}
                            </td>
                            <td>
                                <span style="background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; font-weight: 600;">
                                    {{ $cp->payment_type ?: 'Paid' }}
                                </span>
                                @if($cp->remarks)
                                    <div style="font-size: 10.5px; color: #94A3B8; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($cp->remarks, 35) }}</div>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    @if($cp->contractor_id)
                                    <a href="{{ route('contractors.show', $cp->contractor_id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;" title="View Contractor Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    @endif
                                    @if($cp->document_file)
                                    <a href="{{ asset('storage/' . $cp->document_file) }}" target="_blank" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px; color: #60A5FA !important;" title="View Receipt Document">
                                        <i class="fa-solid fa-paperclip"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding: 16px; background: rgba(16, 22, 34, 0.30); border-radius: 10px; color: #94A3B8; font-size: 13px; text-align: center;">
                <i class="fa-solid fa-circle-info" style="color: #FB7185; margin-right: 6px;"></i> No contractor payments recorded for this project yet.
            </div>
            @endif
        </div>

        {{-- Tab 3: Broker Commissions Ledger Table --}}
        <div id="tab-brokers-{{ $p->id }}" class="project-tab-pane {{ $defaultTab === 'brokers' ? '' : 'hidden' }}">
            @if($hasBrokers)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Broker Name</th>
                            <th>Property / Booking</th>
                            <th>Commission Type &amp; Rate</th>
                            <th>Commission Amount</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bcList as $bIdx => $bc)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($bIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $bc->payment_date ? \Carbon\Carbon::parse($bc->payment_date)->format('d M Y') : ($bc->created_at ? $bc->created_at->format('d M Y') : '—') }}
                            </td>
                            <td>
                                @if($bc->broker)
                                    <a href="{{ route('brokers.show', $bc->broker_id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-handshake" style="color: #D8B4FE; font-size: 12px;"></i>
                                        {{ $bc->broker->name }}
                                    </a>
                                    @if($bc->broker->phone)
                                        <div style="font-size: 10.5px; color: #94A3B8;">{{ $bc->broker->phone }}</div>
                                    @endif
                                @else
                                    <span style="color: #CBD5E1; font-weight: 700;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($bc->property)
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 1px 6px; border-radius: 4px; font-size: 11px;">
                                        <i class="fa-solid fa-house"></i> {{ $bc->property->property_name }}
                                    </span>
                                @elseif($bc->booking && $bc->booking->property)
                                    <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 1px 6px; border-radius: 4px; font-size: 11px;">
                                        <i class="fa-solid fa-house"></i> {{ $bc->booking->property->property_name }}
                                    </span>
                                @else
                                    <span style="color: #94A3B8;">Project Sale</span>
                                @endif
                            </td>
                            <td>
                                <span class="cat-badge" style="background: rgba(168, 85, 247, 0.15); color: #E9D5FF; border-color: rgba(168, 85, 247, 0.30);">
                                    {{ $bc->commission_type ?: 'Percentage' }} {{ $bc->commission_value ? '('.$bc->commission_value.'%)' : '' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($bc->commission_amount, 2) }}</strong>
                            </td>
                            <td>
                                @php $bSt = strtolower($bc->payment_status ?: 'paid'); @endphp
                                <span class="status-badge {{ $bSt === 'paid' ? 'status-approved' : 'status-pending' }}">
                                    {{ ucfirst($bc->payment_status ?: 'Paid') }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $bc->remarks ?: '—' }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    @if($bc->broker_id)
                                    <a href="{{ route('brokers.show', $bc->broker_id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;" title="View Broker Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding: 16px; background: rgba(16, 22, 34, 0.30); border-radius: 10px; color: #94A3B8; font-size: 13px; text-align: center;">
                <i class="fa-solid fa-circle-info" style="color: #D8B4FE; margin-right: 6px;"></i> No broker commission payments recorded for this project yet.
            </div>
            @endif
        </div>

        {{-- Tab 4: Land Acquisition / Seller Payments Ledger Table --}}
        <div id="tab-land-{{ $p->id }}" class="project-tab-pane {{ $defaultTab === 'land' ? '' : 'hidden' }}">
            @if($hasLand)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Land / Property Master</th>
                            <th>Seller / Landowner</th>
                            <th>Amount Paid</th>
                            <th>Payment Mode &amp; Bank</th>
                            <th>Ref No</th>
                            <th>Remarks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lpList as $lIdx => $lp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($lIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $lp->payment_date ? \Carbon\Carbon::parse($lp->payment_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                @if($lp->propertyMaster)
                                    <a href="{{ route('property-masters.show', $lp->property_master_id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-mountain-sun" style="color: #5EEAD4; font-size: 12px;"></i>
                                        {{ $lp->propertyMaster->property_name }}
                                    </a>
                                @else
                                    <span style="color: #CBD5E1; font-weight: 700;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: #CBD5E1; font-weight: 600;">
                                    {{ $lp->propertyMaster?->seller_name ?? ($lp->propertyMaster?->seller?->name ?? '—') }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($lp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $lp->payment_mode ?: 'Cash' }} {{ $lp->bank_name ? '· ' . $lp->bank_name : '' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $lp->reference_no ?: '—' }}
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $lp->remarks ?: 'Land Installment' }}
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    @if($lp->property_master_id)
                                    <a href="{{ route('property-masters.show', $lp->property_master_id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;" title="View Land Dossier">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding: 16px; background: rgba(16, 22, 34, 0.30); border-radius: 10px; color: #94A3B8; font-size: 13px; text-align: center;">
                <i class="fa-solid fa-circle-info" style="color: #5EEAD4; margin-right: 6px;"></i> No land acquisition payments recorded for this project yet.
            </div>
            @endif
        </div>

        {{-- Card Footer Quick Links --}}
        <div class="project-footer-actions">
            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('expenses.create', ['project_id' => $p->id]) }}" class="btn-action-expense">
                    <i class="fa-solid fa-plus"></i> Add Expense
                </a>
                <a href="{{ route('purchase-orders.create', ['project_id' => $p->id]) }}" class="btn-action-po">
                    <i class="fa-solid fa-file-invoice"></i> Create PO
                </a>
                <a href="{{ route('contractors.index') }}" class="btn-action-contractor">
                    <i class="fa-solid fa-helmet-safety"></i> Contractors
                </a>
                <a href="{{ route('brokers.index') }}" class="btn-action-broker">
                    <i class="fa-solid fa-handshake"></i> Brokers
                </a>
                <a href="{{ route('property-masters.index') }}" class="btn-action-land">
                    <i class="fa-solid fa-mountain-sun"></i> Land Acquisition
                </a>
            </div>
            <div>
                <a href="{{ route('projects.show', $p->id) }}" class="btn-action-view">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> View Project Dossier
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="empty-state">
        <i class="fa-solid fa-city"></i>
        <h3 style="color: #FFFFFF; font-size: 18px; margin-bottom: 6px;">No Projects Found</h3>
        <p style="margin: 0;">Try adjusting your search or filter parameters to see project expenses.</p>
    </div>
@endforelse

{{-- Unallocated / General Expenses & Payments --}}
@if(!request('project_id') && ((isset($unallocatedExpenses) && $unallocatedExpenses->isNotEmpty()) || (isset($unallocatedContractorPayments) && $unallocatedContractorPayments->isNotEmpty()) || (isset($unallocatedBrokerCommissions) && $unallocatedBrokerCommissions->isNotEmpty()) || (isset($unallocatedLandPayments) && $unallocatedLandPayments->isNotEmpty())))
    @php
        $hasUnallocExp = isset($unallocatedExpenses) && $unallocatedExpenses->isNotEmpty();
        $hasUnallocCp = isset($unallocatedContractorPayments) && $unallocatedContractorPayments->isNotEmpty();
        $hasUnallocBc = isset($unallocatedBrokerCommissions) && $unallocatedBrokerCommissions->isNotEmpty();
        $hasUnallocLp = isset($unallocatedLandPayments) && $unallocatedLandPayments->isNotEmpty();

        $unallocDefaultTab = 'expenses';
        if (!$hasUnallocExp && $hasUnallocCp) {
            $unallocDefaultTab = 'contractors';
        } elseif (!$hasUnallocExp && !$hasUnallocCp && $hasUnallocBc) {
            $unallocDefaultTab = 'brokers';
        } elseif (!$hasUnallocExp && !$hasUnallocCp && !$hasUnallocBc && $hasUnallocLp) {
            $unallocDefaultTab = 'land';
        }
    @endphp
    <div class="project-expense-card" id="project-card-unallocated" style="border-color: rgba(148, 163, 184, 0.25) !important;">
        <div class="project-card-header">
            <div class="project-header-left">
                <div class="project-avatar" style="background: rgba(148, 163, 184, 0.18); border-color: rgba(148, 163, 184, 0.35); color: #CBD5E1;">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <div class="project-title">
                    <h3>General &amp; Unallocated Expenses</h3>
                    <p>
                        <span><i class="fa-solid fa-receipt" style="color: #34D399;"></i> {{ $unallocatedExpenses ? $unallocatedExpenses->count() : 0 }} General Expenses</span>
                        <span><i class="fa-solid fa-helmet-safety" style="color: #FB7185;"></i> {{ $unallocatedContractorPayments ? $unallocatedContractorPayments->count() : 0 }} Contractor Payments</span>
                        <span><i class="fa-solid fa-handshake" style="color: #D8B4FE;"></i> {{ $unallocatedBrokerCommissions ? $unallocatedBrokerCommissions->count() : 0 }} Broker Commissions</span>
                        <span><i class="fa-solid fa-mountain-sun" style="color: #5EEAD4;"></i> {{ $unallocatedLandPayments ? $unallocatedLandPayments->count() : 0 }} Land Payments</span>
                    </p>
                </div>
            </div>

            <div class="project-cost-badge">
                <div class="badge-label">General Total Expense</div>
                <div class="badge-amount">₹{{ number_format($unallocatedTotal, 2) }}</div>
            </div>
        </div>

        @if(!empty($unallocatedCategories))
        <div class="category-pills-bar" style="margin-top: 18px;">
            <span style="font-size: 11px; font-weight: 800; color: #CBD5E1; text-transform: uppercase; margin-right: 4px;">
                <i class="fa-solid fa-tags" style="color: #FBBF24;"></i> Cost Breakdown:
            </span>
            @foreach($unallocatedCategories as $catName => $cDetails)
                @php
                    $catClass = '';
                    if ($catName === 'Contractor Payments') $catClass = 'cat-contractor';
                    elseif ($catName === 'Broker Commission') $catClass = 'cat-broker';
                    elseif ($catName === 'Land & Acquisition Payment') $catClass = 'cat-land';
                @endphp
                <div class="cat-pill {{ $catClass }}">
                    <span>{{ $catName }}: <strong>₹{{ number_format($cDetails['total'], 2) }}</strong></span>
                    <span class="c-count">{{ $cDetails['count'] }}</span>
                </div>
            @endforeach
        </div>
        @endif

        <div class="project-tab-nav" style="margin-top: 14px;">
            <button type="button" class="tab-btn {{ $unallocDefaultTab === 'expenses' ? 'active' : '' }}" onclick="switchProjectTab('unallocated', 'expenses')">
                <i class="fa-solid fa-receipt"></i> General Expenses ({{ $unallocatedExpenses ? $unallocatedExpenses->count() : 0 }})
            </button>
            <button type="button" class="tab-btn tab-contractor {{ $unallocDefaultTab === 'contractors' ? 'active' : '' }}" onclick="switchProjectTab('unallocated', 'contractors')">
                <i class="fa-solid fa-helmet-safety"></i> Contractor Payments ({{ $unallocatedContractorPayments ? $unallocatedContractorPayments->count() : 0 }})
            </button>
            <button type="button" class="tab-btn tab-broker {{ $unallocDefaultTab === 'brokers' ? 'active' : '' }}" onclick="switchProjectTab('unallocated', 'brokers')">
                <i class="fa-solid fa-handshake"></i> Broker Commissions ({{ $unallocatedBrokerCommissions ? $unallocatedBrokerCommissions->count() : 0 }})
            </button>
            <button type="button" class="tab-btn tab-land {{ $unallocDefaultTab === 'land' ? 'active' : '' }}" onclick="switchProjectTab('unallocated', 'land')">
                <i class="fa-solid fa-mountain-sun"></i> Land Payments ({{ $unallocatedLandPayments ? $unallocatedLandPayments->count() : 0 }})
            </button>
        </div>

        <div id="tab-expenses-unallocated" class="project-tab-pane {{ $unallocDefaultTab === 'expenses' ? '' : 'hidden' }}">
            @if($hasUnallocExp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Expense Title / Details</th>
                            <th>Category</th>
                            <th>Paid To / Vendor</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th>Ref No</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unallocatedExpenses as $eIdx => $exp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($eIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <strong style="color: #FFFFFF; font-weight: 700;">{{ $exp->expense_title ?? $exp->description }}</strong>
                            </td>
                            <td>
                                <span class="cat-badge">
                                    {{ $exp->expense_category ?: ($exp->expenseCategory->name ?? 'General') }}
                                </span>
                            </td>
                            <td style="color: #CBD5E1;">
                                {{ $exp->paid_to ?: ($exp->vendor->name ?? '—') }}
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($exp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $exp->payment_mode ?: 'Cash' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $exp->reference_no ?: ($exp->bill_no ?: '—') }}
                            </td>
                            <td style="text-align: center;">
                                @php $st = strtolower($exp->approval_status ?? 'pending'); @endphp
                                <span class="status-badge status-{{ $st }}">
                                    {{ ucfirst($exp->approval_status ?? 'Pending') }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    <a href="{{ route('expenses.show', $exp->id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('expenses.edit', $exp->id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('expenses.destroy', $exp->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this expense voucher?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px; background: rgba(239, 68, 68, 0.15) !important; border-color: rgba(239, 68, 68, 0.35) !important; color: #F87171 !important;">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div id="tab-contractors-unallocated" class="project-tab-pane {{ $unallocDefaultTab === 'contractors' ? '' : 'hidden' }}">
            @if($hasUnallocCp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Contractor Name</th>
                            <th>Work Type</th>
                            <th>Amount Paid</th>
                            <th>Payment Mode &amp; Bank</th>
                            <th>Ref / Bill No</th>
                            <th>Remarks</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unallocatedContractorPayments as $cIdx => $cp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($cIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $cp->payment_date ? \Carbon\Carbon::parse($cp->payment_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                @if($cp->contractor)
                                    <a href="{{ route('contractors.show', $cp->contractor_id) }}" style="color: #FFFFFF; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-helmet-safety" style="color: #FB7185; font-size: 12px;"></i>
                                        {{ $cp->contractor->contractor_name }}
                                    </a>
                                @else
                                    <span style="color: #CBD5E1; font-weight: 700;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="cat-badge" style="background: rgba(244, 63, 94, 0.15); color: #FDA4AF; border-color: rgba(244, 63, 94, 0.30);">
                                    {{ $cp->contractor->work_type ?? 'Contractor Work' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($cp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $cp->payment_mode ?: 'Cash' }} {{ $cp->bank_name ? '· ' . $cp->bank_name : '' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $cp->reference_no ?: ($cp->bill_no ?: '—') }}
                            </td>
                            <td>
                                <span style="background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; font-weight: 600;">
                                    {{ $cp->payment_type ?: 'Paid' }}
                                </span>
                                @if($cp->remarks)
                                    <div style="font-size: 10.5px; color: #94A3B8; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($cp->remarks, 35) }}</div>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 5px; align-items: center;">
                                    @if($cp->contractor_id)
                                    <a href="{{ route('contractors.show', $cp->contractor_id) }}" class="btn-outline-custom" style="padding: 4px 8px; font-size: 11.5px;">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div id="tab-brokers-unallocated" class="project-tab-pane {{ $unallocDefaultTab === 'brokers' ? '' : 'hidden' }}">
            @if($hasUnallocBc)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Broker Name</th>
                            <th>Commission Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unallocatedBrokerCommissions as $bIdx => $bc)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($bIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $bc->payment_date ? \Carbon\Carbon::parse($bc->payment_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <strong style="color: #FFFFFF;">{{ $bc->broker?->name ?? '—' }}</strong>
                            </td>
                            <td>
                                <span class="cat-badge" style="background: rgba(168, 85, 247, 0.15); color: #E9D5FF; border-color: rgba(168, 85, 247, 0.30);">
                                    {{ $bc->commission_type ?: 'Commission' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($bc->commission_amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="status-badge status-approved">
                                    {{ ucfirst($bc->payment_status ?: 'Paid') }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $bc->remarks ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <div id="tab-land-unallocated" class="project-tab-pane {{ $unallocDefaultTab === 'land' ? '' : 'hidden' }}">
            @if($hasUnallocLp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Date</th>
                            <th>Land / Property Master</th>
                            <th>Amount Paid</th>
                            <th>Mode &amp; Bank</th>
                            <th>Ref No</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unallocatedLandPayments as $lIdx => $lp)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 11.5px;">{{ str_pad($lIdx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $lp->payment_date ? \Carbon\Carbon::parse($lp->payment_date)->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <strong style="color: #FFFFFF;">{{ $lp->propertyMaster?->property_name ?? '—' }}</strong>
                            </td>
                            <td>
                                <strong style="color: #F87171; font-size: 14px;">₹{{ number_format($lp->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11px;">
                                    {{ $lp->payment_mode ?: 'Cash' }}
                                </span>
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $lp->reference_no ?: '—' }}
                            </td>
                            <td style="color: #94A3B8; font-size: 11.5px;">
                                {{ $lp->remarks ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
@endif

<script>
function switchProjectTab(projectId, tab) {
    const card = document.getElementById('project-card-' + projectId);
    if (!card) return;
    
    const nav = card.querySelector('.project-tab-nav');
    if (nav) {
        const btns = nav.querySelectorAll('.tab-btn');
        btns.forEach(btn => btn.classList.remove('active'));
        if (tab === 'expenses' && btns[0]) btns[0].classList.add('active');
        if (tab === 'contractors' && btns[1]) btns[1].classList.add('active');
        if (tab === 'brokers' && btns[2]) btns[2].classList.add('active');
        if (tab === 'land' && btns[3]) btns[3].classList.add('active');
    }
    
    const expPane = document.getElementById('tab-expenses-' + projectId);
    const cpPane = document.getElementById('tab-contractors-' + projectId);
    const bcPane = document.getElementById('tab-brokers-' + projectId);
    const lpPane = document.getElementById('tab-land-' + projectId);
    
    [expPane, cpPane, bcPane, lpPane].forEach(p => {
        if (p) p.classList.add('hidden');
    });

    if (tab === 'expenses' && expPane) expPane.classList.remove('hidden');
    if (tab === 'contractors' && cpPane) cpPane.classList.remove('hidden');
    if (tab === 'brokers' && bcPane) bcPane.classList.remove('hidden');
    if (tab === 'land' && lpPane) lpPane.classList.remove('hidden');
}
</script>

@endsection

