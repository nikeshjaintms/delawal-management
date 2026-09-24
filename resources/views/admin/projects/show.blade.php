@extends('admin.layouts.app')

@section('title', $project->project_name . ' - Project Details')
@section('page-title', 'Project Master')
@php
    $user = Auth::user();
    if (!$user && session('login_type') === 'firm' && session('firm_id')) {
        $authUser = new class {
            public function isAdmin()        { return true; }
            public function hasPermission($p){ return true; }
            public $role = null;
            public $name = '';
            public $firm_id = null;
        };
        $authUser->name = session('firm_name', 'Firm');
        $authUser->firm_id = session('firm_id');
    } else {
        $authUser = $user;
    }
@endphp

@section('content')
<style>
/* ── Luxury Dark Glass System ── */
.breadcrumb-nav {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: rgba(20, 27, 41, 0.60);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.10);
    border-radius: 12px;
    font-size: 13px;
    color: #94A3B8;
    font-weight: 600;
    margin-bottom: 20px;
}
.breadcrumb-nav a {
    color: #60A5FA;
    text-decoration: none;
    font-weight: 700;
    transition: color 0.15s;
}
.breadcrumb-nav a:hover { color: #93C5FD; }
.breadcrumb-nav .separator { font-size: 10px; color: #64748B; }
.breadcrumb-nav .active { color: #FFFFFF; font-weight: 700; }

.crud-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 15px;
}

.crud-title h2 {
    font-size: 28px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.crud-title p {
    font-size: 14px;
    color: #CBD5E1 !important;
    font-weight: 600 !important;
    margin: 0;
}

.card-box {
    background: rgba(20, 27, 41, 0.60) !important;
    backdrop-filter: blur(20px) saturate(160%) !important;
    -webkit-backdrop-filter: blur(20px) saturate(160%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 20px !important;
    padding: 24px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35) !important;
    margin-bottom: 24px;
}

/* ── Top Hero Card Layout ── */
.project-hero-grid {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 24px;
    align-items: center;
}
@media (max-width: 992px) {
    .project-hero-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
}

.project-hero-avatar {
    width: 120px;
    height: 120px;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.30) 0%, rgba(139, 92, 246, 0.25) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #60A5FA;
    font-size: 42px;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(0,0,0,0.30);
}
.project-hero-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.project-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
}
.pm-item {
    display: flex;
    flex-direction: column;
}
.pm-label {
    font-size: 11px;
    font-weight: 700;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 3px;
}
.pm-value {
    font-size: 14px;
    font-weight: 700;
    color: #FFFFFF;
}

/* ── Quick KPI Strip ── */
.project-kpi-strip {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-top: 20px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
@media (max-width: 1400px) {
    .project-kpi-strip {
        grid-template-columns: repeat(4, 1fr);
    }
}
@media (max-width: 992px) {
    .project-kpi-strip {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 540px) {
    .project-kpi-strip {
        grid-template-columns: 1fr;
    }
}
.pk-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.pk-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
}
.pk-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.pk-blue   { background: rgba(59, 130, 246, 0.20); color: #60A5FA; }
.pk-purple { background: rgba(167, 139, 250, 0.20); color: #C4B5FD; }
.pk-green  { background: rgba(16, 185, 129, 0.20); color: #34D399; }
.pk-amber  { background: rgba(245, 158, 11, 0.20); color: #FBBF24; }
.pk-red    { background: rgba(239, 68, 68, 0.20); color: #F87171; }
.pk-rose   { background: rgba(244, 63, 94, 0.20); color: #FB7185; }

.pk-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
    overflow: hidden;
}
.pk-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
}
.pk-val {
    font-size: 15.5px;
    font-weight: 800;
    color: #FFFFFF !important;
    line-height: 1.25;
    margin-top: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.2px;
}

/* ── Buttons & Badges ── */
.btn-primary-custom, a.btn-primary-custom, button.btn-primary-custom, .btn-gold {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 18px; min-height: 40px; background: #2563EB !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid #3B82F6 !important;
    border-radius: 10px; text-decoration: none !important; box-shadow: 0 4px 14px rgba(37,99,235,0.35);
    cursor: pointer;
}
.btn-primary-custom:hover, .btn-gold:hover {
    background: #1D4ED8 !important; color: #FFFFFF !important;
}

.btn-secondary-custom, a.btn-secondary-custom, button.btn-secondary-custom, .btn-outline {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 10px 18px; min-height: 40px; background: rgba(255, 255, 255, 0.08) !important;
    color: #FFFFFF !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-secondary-custom:hover, .btn-outline:hover {
    background: rgba(255, 255, 255, 0.16) !important; color: #FFFFFF !important; border-color: rgba(255, 255, 255, 0.30) !important;
}

.btn-excel-custom {
    display: inline-flex; align-items: center; justify-content: center; gap: 7px;
    padding: 10px 18px; min-height: 40px; background: rgba(16, 185, 129, 0.18) !important;
    color: #34D399 !important; font-size: 13.5px; font-weight: 700; border: 1px solid rgba(16, 185, 129, 0.35) !important;
    border-radius: 10px; text-decoration: none !important; cursor: pointer;
}
.btn-excel-custom:hover {
    background: #10B981 !important; color: #FFFFFF !important;
}

.btn-action-expense {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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
    padding: 7px 14px; min-height: 36px; border-radius: 9px;
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

.tbl-actions-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.btn-tbl-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 32px;
    padding: 0 14px;
    background: #2563EB !important;
    color: #FFFFFF !important;
    font-size: 12.5px;
    font-weight: 700;
    border: 1px solid #3B82F6 !important;
    border-radius: 8px;
    text-decoration: none !important;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
    transition: background 0.15s ease;
}
.btn-tbl-edit:hover {
    background: #1D4ED8 !important;
    color: #FFFFFF !important;
}

.btn-solid-blue {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid #3B82F6 !important;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    text-decoration: none !important;
    transition: all 0.2s;
}
.btn-solid-blue:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.50);
}

.btn-solid-purple {
    background: linear-gradient(135deg, #9333EA 0%, #7E22CE 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid #A855F7 !important;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(147, 51, 234, 0.35);
    text-decoration: none !important;
    transition: all 0.2s;
}
.btn-solid-purple:hover {
    background: linear-gradient(135deg, #7E22CE 0%, #6B21A8 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(147, 51, 234, 0.50);
}

.btn-solid-emerald {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid #34D399 !important;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    text-decoration: none !important;
    transition: all 0.2s;
}
.btn-solid-emerald:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.50);
}

.btn-solid-slate {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #CBD5E1 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    padding: 8px 16px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.2s;
}
.btn-solid-slate:hover {
    background: rgba(255, 255, 255, 0.16) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.30) !important;
    transform: translateY(-1px);
}

.filter-tabs {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.filter-tab-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.12);
    color: #94A3B8;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s ease;
}
.filter-tab-btn:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #FFFFFF;
}
.filter-tab-btn.active {
    background: #2563EB;
    border-color: #3B82F6;
    color: #FFFFFF;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}

.search-input-wrap {
    position: relative;
    min-width: 260px;
}
.search-input-wrap i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94A3B8;
    font-size: 13px;
}
.search-input-box {
    width: 100%;
    padding: 8px 12px 8px 36px;
    background: rgba(10, 15, 26, 0.85) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 9px;
    color: #FFFFFF !important;
    font-size: 13px;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.search-input-box:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}

.tbl-actions-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    vertical-align: middle;
}

.btn-tbl-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 32px;
    padding: 0 12px;
    background: rgba(37, 99, 235, 0.20) !important;
    color: #60A5FA !important;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid rgba(59, 130, 246, 0.40) !important;
    border-radius: 8px;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    text-decoration: none !important;
    transition: all 0.18s ease;
}
.btn-tbl-edit:hover {
    background: #2563EB !important;
    color: #FFFFFF !important;
    border-color: #3B82F6 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}

.btn-tbl-view {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 32px;
    padding: 0 12px;
    background: rgba(255, 255, 255, 0.08) !important;
    color: #E2E8F0 !important;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 8px;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    text-decoration: none !important;
    transition: all 0.18s ease;
}
.btn-tbl-view:hover {
    background: rgba(255, 255, 255, 0.18) !important;
    color: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.35) !important;
    transform: translateY(-1px);
}

.btn-tbl-del {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 32px;
    height: 32px;
    padding: 0;
    background: rgba(239, 68, 68, 0.15) !important;
    color: #F87171 !important;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid rgba(239, 68, 68, 0.35) !important;
    border-radius: 8px;
    cursor: pointer;
    box-sizing: border-box;
    line-height: 1;
    transition: all 0.18s ease;
}
.btn-tbl-del:hover {
    background: #EF4444 !important;
    color: #FFFFFF !important;
    border-color: #EF4444 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
}

.btn-tbl-del-placeholder {
    display: inline-block;
    width: 32px;
    height: 32px;
    visibility: hidden;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    text-transform: uppercase;
}
.badge-active, .badge-available { background: rgba(16, 185, 129, 0.18) !important; color: #34D399 !important; border: 1px solid rgba(16, 185, 129, 0.35) !important; }
.badge-booked   { background: rgba(245, 158, 11, 0.18) !important; color: #FBBF24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; }
.badge-sold     { background: rgba(239, 68, 68, 0.18) !important; color: #F87171 !important; border: 1px solid rgba(239, 68, 68, 0.35) !important; }
.badge-rented   { background: rgba(168, 85, 247, 0.18) !important; color: #D8B4FE !important; border: 1px solid rgba(168, 85, 247, 0.35) !important; }
.badge-inactive, .badge-blocked { background: rgba(148, 163, 184, 0.18) !important; color: #94A3B8 !important; border: 1px solid rgba(148, 163, 184, 0.35) !important; }

.section-title {
    font-size: 18px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin-bottom: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

/* ── Full Width Table Styling ── */
.table-wrapper {
    width: 100%;
    overflow-x: auto;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, 0.10);
    background: rgba(10, 14, 23, 0.65);
}

.properties-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}

.properties-table th {
    padding: 13px 18px;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    text-align: left;
    white-space: nowrap;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}

.properties-table td {
    padding: 13px 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    color: #E2E8F0 !important;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
}

.properties-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04);
}

.properties-table tbody tr:last-child td {
    border-bottom: none;
}

.code-chip {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60A5FA !important;
    border: 1px solid rgba(59, 130, 246, 0.30) !important;
    padding: 3px 8px !important;
    border-radius: 6px !important;
    font-weight: 700 !important;
    font-size: 12.5px !important;
    font-family: monospace !important;
}

/* ── Modal Styling ── */
.modal-backdrop-custom {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background: rgba(0, 0, 0, 0.75) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    z-index: 9999 !important;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}
.modal-backdrop-custom.active {
    display: flex !important;
}
.modal-box-custom {
    background: #101622 !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 20px !important;
    width: 100% !important;
    max-width: 680px !important;
    max-height: 90vh !important;
    overflow-y: auto !important;
    padding: 28px !important;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.60) !important;
    position: relative !important;
    box-sizing: border-box;
}
.modal-header-custom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
}
.modal-header-custom h3 {
    font-size: 20px;
    font-weight: 800;
    color: #FFFFFF !important;
    margin: 0;
}
.modal-close-btn {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #94A3B8;
    cursor: pointer;
    line-height: 1;
    transition: color .2s;
}
.modal-close-btn:hover { color: #FFFFFF; }

.m-form-group { margin-bottom: 16px; }
.m-form-label { display: block; font-size: 13px; font-weight: 700; color: #E2E8F0; margin-bottom: 6px; }
.m-form-label span { color: #EF4444; }
.m-form-control {
    width: 100%;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1.5px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 10px !important;
    color: #FFFFFF !important;
    font-size: 13.5px;
    outline: none;
    box-sizing: border-box;
    transition: border-color .2s;
}
.m-form-control:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25) !important;
}
select.m-form-control option { background: #101622; color: #FFFFFF; }
.m-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 580px) {
    .m-form-row { grid-template-columns: 1fr; gap: 0; }
}
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb-nav">
    <span><i class="fa-solid fa-city" style="color: #60A5FA; margin-right: 6px;"></i>Property Management</span>
    <i class="fa-solid fa-chevron-right separator"></i>
    <a href="{{ route('projects.index') }}">Projects</a>
    <i class="fa-solid fa-chevron-right separator"></i>
    <span class="active">{{ $project->project_name }}</span>
</div>

<div class="crud-header">
    <div class="crud-title">
        <h2>{{ $project->project_name }}</h2>
        <p>Project details, associated Property Masters &amp; Plot Inventory</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('invoices.create', ['project_id' => $project->id]) }}" class="btn-gold" style="padding: 9px 18px; font-size: 13.5px;">
            <i class="fa-solid fa-file-invoice-dollar"></i> Generate Invoice
        </a>
        <a href="{{ route('projects.detail-pdf', $project->id) }}" target="_blank" class="btn-secondary-custom" style="background: rgba(252, 105, 0, 0.18) !important; border-color: rgba(252, 105, 0, 0.45) !important; color: #FF8A3D !important;">
            <i class="fa-solid fa-file-pdf"></i> Print / PDF Dossier
        </a>
        @if($authUser && $authUser->hasPermission('project_edit'))
            <a href="{{ route('projects.edit', $project->id) }}" class="btn-primary-custom">
                <i class="fa-regular fa-pen-to-square"></i> Edit Project
            </a>
        @endif
        <a href="{{ route('projects.index') }}" class="btn-secondary-custom">
            <i class="fa-solid fa-arrow-left"></i> Back to Projects
        </a>
    </div>
</div>

@php
    $totalPlots = $project->properties->count();
    $availPlots = $project->properties->where('status', 'available')->count();
    $bookedPlots = $project->properties->where('status', 'booked')->count();
    $soldPlots = $project->properties->where('status', 'sold')->count();
    $linkedMasters = $project->propertyMasters->isNotEmpty() ? $project->propertyMasters : ($project->propertyMaster ? collect([$project->propertyMaster]) : collect([]));
@endphp

<!-- ================================================================
     TOP SECTION: FULL WIDTH PROJECT OVERVIEW & METADATA CARD
================================================================ -->
<div class="card-box">
    <div class="project-hero-grid">
        <!-- Hero Avatar / Image -->
        <div class="project-hero-avatar">
            @if($project->project_image)
                <img src="{{ asset('storage/' . $project->project_image) }}" alt="{{ $project->project_name }}">
            @else
                <i class="fa-solid fa-city"></i>
            @endif
        </div>

        <!-- Meta Grid -->
        <div class="project-meta-grid">
            <div class="pm-item" style="grid-column: span 2;">
                <span class="pm-label">Property Master(s)</span>
                <span class="pm-value">
                    @if($linkedMasters->isNotEmpty())
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; margin-top: 2px;">
                            @foreach($linkedMasters as $pm)
                                <a href="{{ route('property-masters.show', $pm->id) }}" style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 3px 10px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-city" style="font-size: 11px;"></i> {{ $pm->property_name }} ({{ $pm->property_code }})
                                </a>
                            @endforeach
                        </div>
                    @else
                        -
                    @endif
                </span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Project Code</span>
                <span class="pm-value"><code class="code-chip">{{ $project->project_code }}</code></span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Firm</span>
                <span class="pm-value" style="color: #93C5FD;">{{ $project->firm->firm_name ?? '-' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Project Type</span>
                <span class="pm-value" style="text-transform: capitalize;">{{ $project->project_type ?: 'Plotted Development' }}</span>
            </div>
            <div class="pm-item">
                <span class="pm-label">Status</span>
                <span class="pm-value">
                    <span class="badge badge-{{ $project->status === 'active' ? 'active' : 'inactive' }}">
                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($project->status) }}
                    </span>
                </span>
            </div>
            <div class="pm-item" style="grid-column: span 2;">
                <span class="pm-label">Location / Address</span>
                <span class="pm-value" style="font-size: 13.5px; font-weight: 600; color: #CBD5E1;">
                    <i class="fa-solid fa-location-dot" style="color: #60A5FA; margin-right: 5px;"></i>
                    {{ $project->display_address }}
                </span>
            </div>
        </div>

        <!-- Right Quick Actions -->
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <a href="{{ route('projects.edit', $project->id) }}" class="btn-primary-custom" style="padding: 8px 16px; min-height: 38px; font-size: 13px;">
                <i class="fa-solid fa-shapes"></i> Manage Plot Allocations
            </a>
            <a href="{{ route('properties.index', ['project_id' => $project->id]) }}" class="btn-excel-custom" style="padding: 8px 16px; min-height: 38px; font-size: 13px;">
                <i class="fa-solid fa-file-excel"></i> Bulk Plots Table
            </a>
        </div>
    </div>

    <!-- KPI Summary Strip -->
    <div class="project-kpi-strip">
        <div class="pk-card" title="Total Plots: {{ $totalPlots }}">
            <div class="pk-icon pk-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div class="pk-info">
                <span class="pk-label">Total Plots</span>
                <span class="pk-val">{{ $totalPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card" title="Combined Land Properties: {{ $linkedMasters->count() }}">
            <div class="pk-icon pk-purple"><i class="fa-solid fa-city"></i></div>
            <div class="pk-info">
                <span class="pk-label">Properties</span>
                <span class="pk-val">{{ $linkedMasters->count() }} Linked</span>
            </div>
        </div>
        <div class="pk-card" title="Available Plots: {{ $availPlots }}">
            <div class="pk-icon pk-green"><i class="fa-solid fa-circle-check"></i></div>
            <div class="pk-info">
                <span class="pk-label">Available</span>
                <span class="pk-val">{{ $availPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card" title="Booked Plots: {{ $bookedPlots }}">
            <div class="pk-icon pk-amber"><i class="fa-solid fa-handshake"></i></div>
            <div class="pk-info">
                <span class="pk-label">Booked</span>
                <span class="pk-val">{{ $bookedPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card" title="Sold Plots: {{ $soldPlots }}">
            <div class="pk-icon pk-red"><i class="fa-solid fa-circle-check"></i></div>
            <div class="pk-info">
                <span class="pk-label">Sold</span>
                <span class="pk-val">{{ $soldPlots }} Plots</span>
            </div>
        </div>
        <div class="pk-card" style="border-color: rgba(16, 185, 129, 0.35); background: rgba(16, 185, 129, 0.08);" title="Total Inflow Collected: ₹{{ number_format($grandTotalProjectIncome ?? 0, 2) }}">
            <div class="pk-icon pk-emerald"><i class="fa-solid fa-arrow-trend-up"></i></div>
            <div class="pk-info">
                <span class="pk-label" style="color: #6EE7B7;">Total Inflow</span>
                <span class="pk-val" style="color: #34D399 !important;">₹{{ number_format($grandTotalProjectIncome ?? 0, 2) }}</span>
            </div>
        </div>
        <div class="pk-card" style="border-color: rgba(244, 63, 94, 0.35); background: rgba(244, 63, 94, 0.08);" title="Total Project Outflows: ₹{{ number_format($grandTotalProjectCost ?? ($totalExpenses ?? 0), 2) }}">
            <div class="pk-icon pk-rose"><i class="fa-solid fa-receipt"></i></div>
            <div class="pk-info">
                <span class="pk-label" style="color: #FDA4AF;">Total Outflows</span>
                <span class="pk-val" style="color: #FB7185 !important;">₹{{ number_format($grandTotalProjectCost ?? ($totalExpenses ?? 0), 2) }}</span>
            </div>
        </div>
        <div class="pk-card" style="border-color: rgba(56, 189, 248, 0.35); background: rgba(56, 189, 248, 0.08);" title="Net Profit / Balance: ₹{{ number_format($netProjectProfit ?? 0, 2) }}">
            <div class="pk-icon pk-cyan"><i class="fa-solid fa-chart-line"></i></div>
            <div class="pk-info">
                <span class="pk-label" style="color: #7DD3FC;">Net Margin</span>
                <span class="pk-val" style="color: {{ ($netProjectProfit ?? 0) >= 0 ? '#38BDF8' : '#F87171' }} !important;">{{ ($netProjectProfit ?? 0) >= 0 ? '+₹' : '-₹' }}{{ number_format(abs($netProjectProfit ?? 0), 2) }}</span>
            </div>
        </div>
    </div>

    @if($project->description)
        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.08); font-size: 13.5px; color: #CBD5E1;">
            <strong style="color: #94A3B8; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 3px;">Project Description:</strong>
            {{ $project->description }}
        </div>
    @endif
</div>

<!-- ================================================================
     BOTTOM SECTION: FULL WIDTH PROJECT PLOTS & UNITS INVENTORY TABLE
================================================================ -->
<div class="card-box">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 14px;">
        <div>
            <h3 style="font-size: 19px; font-weight: 800; color: #FFFFFF; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shapes" style="color: #60A5FA;"></i>
                Project Plots &amp; Units Inventory ({{ $totalPlots }})
            </h3>
            <p style="font-size: 13px; color: #94A3B8; margin: 3px 0 0 0;">
                Directly manage project plots, generate unit sequences, or import directly from Excel.
            </p>
        </div>
        <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('projects.plots.template') }}" class="btn-solid-slate" style="padding: 8px 14px; font-size: 13px;" title="Download Excel format template">
                <i class="fa-solid fa-file-excel" style="color: #34D399;"></i> Template
            </a>
            <button type="button" class="btn-solid-blue" style="padding: 8px 14px; font-size: 13px;" onclick="openModal('importProjectExcelModal')">
                <i class="fa-solid fa-file-import"></i> Import Excel
            </button>
            <button type="button" class="btn-solid-purple" style="padding: 8px 14px; font-size: 13px;" onclick="openModal('bulkProjectPlotsModal')">
                <i class="fa-solid fa-bolt"></i> Bulk Generate
            </button>
            <button type="button" class="btn-solid-emerald" style="padding: 8px 14px; font-size: 13px;" onclick="openModal('addProjectPlotModal')">
                <i class="fa-solid fa-plus"></i> Add Plot
            </button>
        </div>
    </div>

    <!-- Filter & Search Strip -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
        <div class="filter-tabs">
            <button type="button" class="filter-tab-btn active" onclick="filterProjectPlots('all', this)">All ({{ $totalPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterProjectPlots('available', this)">Available Free ({{ $availPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterProjectPlots('booked', this)">Booked ({{ $bookedPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterProjectPlots('sold', this)">Sold ({{ $soldPlots }})</button>
            <button type="button" class="filter-tab-btn" onclick="filterProjectPlots('rented', this)">Rented ({{ $project->properties->where('status', 'rented')->count() }})</button>
        </div>

        <div class="search-input-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="projectPlotSearchInput" class="search-input-box" placeholder="Search plot name, unit, code..." oninput="searchProjectPlots(this.value)">
        </div>
    </div>

    <div class="table-wrapper">
        <table class="properties-table" id="projectPlotsTable">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Plot Name</th>
                    <th>Code</th>
                    <th>Selling Price</th>
                    <th>Size (Area)</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project->properties as $index => $property)
                    @php
                        $filterCat = 'available';
                        if ($property->status === 'booked' || $property->status === 'sold' || $property->status === 'rented' || $property->status === 'reserved') {
                            $filterCat = $property->status;
                        }
                    @endphp
                    <tr class="project-plot-row" data-category="{{ $filterCat }}" data-search="{{ strtolower($property->property_name . ' ' . $property->property_code . ' ' . $property->unit_no . ' ' . ($property->propertyType->name ?? '')) }}">
                        <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <a href="{{ route('properties.show', $property->id) }}" style="color: #FFFFFF; font-weight: 800; text-decoration: none; font-size: 14px;">
                                {{ $property->property_name }}
                            </a>
                        </td>
                        <td><code class="code-chip">{{ $property->property_code }}</code></td>
                        <td>
                            <strong style="color: #34D399; font-size: 13.5px;">₹{{ number_format($property->price ?: ($property->purchase_rate ?: 0), 2) }}</strong>
                        </td>
                        <td>
                            @if($property->size)
                                <span style="color: #2DD4BF; font-weight: 700;">{{ $property->formatted_size }}</span>
                            @else
                                <span style="color: #94A3B8;">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $st = strtolower(trim($property->status ?? 'available'));
                                $activeB = ($property->relationLoaded('bookings') ? $property->bookings->where('status', '!=', 'cancelled')->first() : null)
                                    ?: ($property->relationLoaded('bookingsList') ? $property->bookingsList->where('status', '!=', 'cancelled')->first() : null)
                                    ?: $property->active_booking;
                                $activeSale = ($property->relationLoaded('sales') ? $property->sales->where('sale_status', '!=', 'cancelled')->first() : null)
                                    ?: ($property->relationLoaded('sales') && $property->sales->isNotEmpty() ? $property->sales->first() : null);
                                $activeRental = ($property->relationLoaded('rentals') ? $property->rentals->where('rental_status', 'active')->first() : null)
                                    ?: $property->active_rental;
                            @endphp

                            @if($st === 'sold' || $activeSale)
                                <span class="badge badge-sold">
                                    <i class="fa-solid fa-circle-check"></i> Sold
                                </span>
                                @if($activeSale && $activeSale->customer)
                                    <div style="font-size: 11px; color: #F87171; font-weight: 600; margin-top: 3px;" title="Sold to {{ $activeSale->customer->name }}">
                                        <i class="fa-solid fa-user-tag" style="font-size: 10px;"></i> {{ $activeSale->customer->name }}
                                    </div>
                                @elseif($activeB && $activeB->customer)
                                    <div style="font-size: 11px; color: #F87171; font-weight: 600; margin-top: 3px;" title="Buyer: {{ $activeB->customer->name }}">
                                        <i class="fa-solid fa-user-check" style="font-size: 10px;"></i> {{ $activeB->customer->name }}
                                    </div>
                                @endif
                            @elseif($st === 'booked' || $activeB)
                                <span class="badge badge-booked">
                                    <i class="fa-solid fa-handshake"></i> Booked
                                </span>
                                @if($activeB && $activeB->customer)
                                    <div style="font-size: 11px; color: #FBBF24; font-weight: 600; margin-top: 3px;" title="Booked by {{ $activeB->customer->name }}">
                                        <i class="fa-solid fa-user-check" style="font-size: 10px;"></i> {{ $activeB->customer->name }}
                                    </div>
                                @endif
                            @elseif($st === 'rented' || $activeRental)
                                <span class="badge badge-rented">
                                    <i class="fa-solid fa-key"></i> Rented
                                </span>
                                @if($activeRental && ($activeRental->tenant_name || $activeRental->tenant))
                                    <div style="font-size: 11px; color: #D8B4FE; font-weight: 600; margin-top: 3px;" title="Tenant: {{ $activeRental->tenant_name ?? $activeRental->tenant->name }}">
                                        <i class="fa-solid fa-user-tag" style="font-size: 10px;"></i> {{ $activeRental->tenant_name ?? $activeRental->tenant->name }}
                                    </div>
                                @endif
                            @elseif($st === 'blocked' || $st === 'inactive')
                                <span class="badge badge-inactive">
                                    <i class="fa-solid fa-ban"></i> {{ ucfirst($st) }}
                                </span>
                            @else
                                <span class="badge badge-available">
                                    <i class="fa-solid fa-circle-dot"></i> Available
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap; width: 190px;">
                            <div class="tbl-actions-wrap">
                                <button type="button" class="btn-tbl-edit" onclick="openQuickEditPlotModal({{ $property->id }}, '{{ addslashes($property->property_name) }}', '{{ addslashes($property->property_code) }}', '{{ $property->size }}', '{{ $property->size_unit }}', '{{ $property->facing }}', '{{ $property->purchase_rate }}', '{{ $property->price }}', '{{ $property->status }}', '{{ addslashes($property->description ?? '') }}')">
                                    <i class="fa-regular fa-pen-to-square"></i> Edit
                                </button>
                                <a href="{{ route('properties.show', $property->id) }}" class="btn-tbl-view">
                                    <i class="fa-regular fa-eye"></i> View
                                </a>
                                @if(!in_array($property->status, ['booked', 'sold']))
                                    <form action="{{ route('projects.plots.destroy', [$project->id, $property->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove plot \'{{ addslashes($property->property_name) }}\' from this project?');" style="display: inline; margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-tbl-del" title="Delete Plot">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="btn-tbl-del-placeholder"></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #94A3B8; padding: 36px 0;">
                            <i class="fa-solid fa-boxes-stacked" style="font-size: 32px; color: #60A5FA; margin-bottom: 8px; display: block;"></i>
                            No plots added to this project yet. Click <strong>Import Excel</strong>, <strong>Bulk Generate</strong>, or <strong>Add Plot</strong> above to start adding plots!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================================================================
     PROJECT INCOMES & CUSTOMER COLLECTIONS SECTION
================================================================ -->
<div class="card-box" style="margin-top: 24px;">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.20); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 17px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin: 0;">Project Incomes &amp; Customer Collections</h3>
                <p style="font-size: 12.5px; color: #94A3B8; margin: 2px 0 0 0;">Auto-aggregated inflow ledger of property sale installments, booking advances, rental collections, and direct project revenues ({{ $allProjectIncomes->count() }} total transactions recorded).</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('payments.create') }}" class="btn-action-income-receipt" title="Record customer sales installment or advance">
                <i class="fa-solid fa-receipt"></i> Record Payment / Receipt
            </a>
            <a href="{{ route('incomes.create') }}" class="btn-action-income-direct" title="Add other direct revenues or deposits">
                <i class="fa-solid fa-circle-plus"></i> Add Direct Income
            </a>
            <a href="{{ route('property-sales.index') }}" class="btn-action-sales-ledger" title="View Property Sales Invoices">
                <i class="fa-solid fa-file-invoice-dollar"></i> Sales Ledger
            </a>
            <a href="{{ route('bookings.index') }}" class="btn-action-bookings" title="View Property Bookings">
                <i class="fa-solid fa-calendar-check"></i> Bookings
            </a>
            <a href="{{ route('rentals.index') }}" class="btn-action-rentals" title="View Active Rental Agreements">
                <i class="fa-solid fa-key"></i> Rentals
            </a>
        </div>
    </div>

    <!-- Income Financial Breakdown KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: rgba(16, 185, 129, 0.10); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16, 185, 129, 0.20); color: #34D399; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Total Inflow Collected</div>
                <div style="font-size: 17px; font-weight: 800; color: #34D399 !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($grandTotalProjectIncome ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(59, 130, 246, 0.10); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Sales Installments</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($paymentsCollectedTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(168, 85, 247, 0.10); border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(168, 85, 247, 0.20); color: #C084FC; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Booking Advances</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($bookingAdvanceTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(245, 158, 11, 0.10); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.20); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Rental Income</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($rentalIncomeTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(20, 184, 166, 0.10); border: 1px solid rgba(20, 184, 166, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(20, 184, 166, 0.20); color: #5EEAD4; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Direct / Misc Incomes</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($generalIncomeTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(99, 102, 241, 0.10); border: 1px solid rgba(99, 102, 241, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99, 102, 241, 0.20); color: #818CF8; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Total Sales Booked</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($totalSalesValue ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    @php
        $hasIncomes = isset($allProjectIncomes) && $allProjectIncomes->isNotEmpty();
        $hasPay = isset($projectPayments) && $projectPayments->isNotEmpty();
        $hasBk = isset($projectBookings) && $projectBookings->isNotEmpty();
        $hasRp = isset($projectRentalPayments) && $projectRentalPayments->isNotEmpty();
        $hasGi = isset($projectGeneralIncomes) && $projectGeneralIncomes->isNotEmpty();
    @endphp

    {{-- Income Tabs Navigation --}}
    <div class="project-outflows-tabbar">
        <button type="button" id="btn-tab-inc-all" class="tab-pill-btn active tab-emerald" onclick="toggleProjectIncomeTab('all')">
            <i class="fa-solid fa-list-check"></i>
            <span>All Inflows &amp; Receipts</span>
            <span class="tab-badge-count">{{ $allProjectIncomes->count() }}</span>
        </button>
        <button type="button" id="btn-tab-inc-sales" class="tab-pill-btn" onclick="toggleProjectIncomeTab('sales')">
            <i class="fa-solid fa-money-bill-transfer"></i>
            <span>Sale Payments / Receipts</span>
            <span class="tab-badge-count">{{ isset($projectPayments) ? $projectPayments->count() : 0 }}</span>
        </button>
        <button type="button" id="btn-tab-inc-bookings" class="tab-pill-btn" onclick="toggleProjectIncomeTab('bookings')">
            <i class="fa-solid fa-handshake"></i>
            <span>Booking Advances</span>
            <span class="tab-badge-count">{{ isset($projectBookings) ? $projectBookings->count() : 0 }}</span>
        </button>
        <button type="button" id="btn-tab-inc-rentals" class="tab-pill-btn" onclick="toggleProjectIncomeTab('rentals')">
            <i class="fa-solid fa-key"></i>
            <span>Rental Collections</span>
            <span class="tab-badge-count">{{ isset($projectRentalPayments) ? $projectRentalPayments->count() : 0 }}</span>
        </button>
        <button type="button" id="btn-tab-inc-direct" class="tab-pill-btn" onclick="toggleProjectIncomeTab('direct')">
            <i class="fa-solid fa-coins"></i>
            <span>Direct Incomes</span>
            <span class="tab-badge-count">{{ isset($projectGeneralIncomes) ? $projectGeneralIncomes->count() : 0 }}</span>
        </button>
    </div>

    {{-- Tab 1: All Inflows & Receipts --}}
    <div id="pane-project-income-all">
        @if($hasIncomes)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Date</th>
                            <th style="white-space: nowrap;">Income Type / Source</th>
                            <th style="min-width: 150px;">Unit / Property</th>
                            <th style="min-width: 160px;">Customer / Payer</th>
                            <th style="white-space: nowrap;">Amount Received</th>
                            <th style="white-space: nowrap;">Payment Mode</th>
                            <th style="white-space: nowrap;">Ref / Trx No</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 100px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allProjectIncomes as $idx => $inc)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $inc->date ? $inc->date->format('d M Y') : '—' }}</td>
                                <td style="white-space: nowrap;">
                                    @if($inc->type_badge === 'sale')
                                        <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30);">
                                            <i class="fa-solid fa-money-bill-transfer" style="font-size: 10px; margin-right: 4px;"></i> Sale Payment
                                        </span>
                                    @elseif($inc->type_badge === 'booking')
                                        <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #D8B4FE; border: 1px solid rgba(168, 85, 247, 0.30);">
                                            <i class="fa-solid fa-handshake" style="font-size: 10px; margin-right: 4px;"></i> Booking Advance
                                        </span>
                                    @elseif($inc->type_badge === 'rent')
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.30);">
                                            <i class="fa-solid fa-key" style="font-size: 10px; margin-right: 4px;"></i> Rental Income
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(20, 184, 166, 0.15); color: #5EEAD4; border: 1px solid rgba(20, 184, 166, 0.30);">
                                            <i class="fa-solid fa-coins" style="font-size: 10px; margin-right: 4px;"></i> {{ $inc->category }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 13px;">{{ $inc->unit }}</strong>
                                </td>
                                <td>
                                    <span style="color: #E2E8F0; font-weight: 600; font-size: 13px;">{{ $inc->party }}</span>
                                    @if($inc->remarks && $inc->remarks !== 'Sale Installment' && $inc->remarks !== 'Booking Token' && $inc->remarks !== 'Direct Income')
                                        <div style="font-size: 11px; color: #94A3B8; margin-top: 1px;">{{ \Illuminate\Support\Str::limit($inc->remarks, 40) }}</div>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #34D399 !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($inc->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $inc->mode }}
                                    </span>
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px; white-space: nowrap;">
                                    {{ $inc->ref_no ?: '—' }}
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge badge-active" style="padding: 3px 8px; font-size: 11px;">
                                        {{ ucfirst($inc->status) }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        @if($inc->view_url)
                                            <a href="{{ $inc->view_url }}" class="btn-action-icon btn-action-view" title="View Details">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endif
                                        @if($inc->edit_url)
                                            <a href="{{ $inc->edit_url }}" class="btn-action-icon btn-action-edit" title="Edit Entry">
                                                <i class="fa-solid fa-pen-to-square"></i>
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
            <div style="text-align: center; color: #94A3B8; padding: 36px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-hand-holding-dollar" style="font-size: 32px; color: #34D399; margin-bottom: 8px; display: block;"></i>
                No income or customer receipts recorded for this project yet.
                <div style="margin-top: 14px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ route('payments.create') }}" class="btn-action-income-receipt">
                        <i class="fa-solid fa-plus"></i> Record Customer Payment
                    </a>
                    <a href="{{ route('incomes.create') }}" class="btn-action-income-direct">
                        <i class="fa-solid fa-plus"></i> Add Direct Income
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 2: Property Sale Installments --}}
    <div id="pane-project-income-sales" style="display: none;">
        @if($hasPay)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Payment Date</th>
                            <th style="white-space: nowrap;">Invoice / Sale</th>
                            <th style="min-width: 150px;">Unit / Property</th>
                            <th style="min-width: 160px;">Customer Name</th>
                            <th style="white-space: nowrap;">Total Deal</th>
                            <th style="white-space: nowrap;">Amount Paid</th>
                            <th style="white-space: nowrap;">Pending Balance</th>
                            <th style="white-space: nowrap;">Payment Mode</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 100px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectPayments as $idx => $pay)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $pay->payment_date ? \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') : $pay->created_at->format('d M Y') }}</td>
                                <td>
                                    @if($pay->propertySale)
                                        <a href="{{ route('property-sales.show', $pay->property_sale_id) }}" style="color: #93C5FD; font-weight: 700; text-decoration: none;">
                                            {{ $pay->propertySale->invoice_no ?: ('Sale #' . $pay->property_sale_id) }}
                                        </a>
                                    @else
                                        <span style="color: #94A3B8;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 13px;">
                                        {{ $pay->property?->property_name ?: ($pay->propertySale?->property_names ?: '—') }}
                                    </strong>
                                </td>
                                <td>
                                    <span style="color: #E2E8F0; font-weight: 600;">
                                        {{ $pay->customer?->name ?: ($pay->propertySale?->customer?->name ?: '—') }}
                                    </span>
                                </td>
                                <td style="color: #CBD5E1; white-space: nowrap;">
                                    ₹{{ number_format($pay->total_amount ?: ($pay->propertySale?->grand_total ?: 0), 2) }}
                                </td>
                                <td>
                                    <strong style="color: #34D399 !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($pay->payment_amount, 2) }}</strong>
                                </td>
                                <td style="color: #FDA4AF; font-weight: 700; white-space: nowrap;">
                                    ₹{{ number_format($pay->pending_amount ?: 0, 2) }}
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $pay->payment_mode ?: 'Cash' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge badge-active" style="padding: 3px 8px; font-size: 11px;">
                                        {{ ucfirst($pay->status ?: 'Received') }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ route('payments.show', $pay->id) }}" class="btn-action-icon btn-action-view" title="View Receipt">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <a href="{{ route('payments.edit', $pay->id) }}" class="btn-action-icon btn-action-edit" title="Edit Payment">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-money-bill-transfer" style="font-size: 32px; color: #60A5FA; margin-bottom: 8px; display: block;"></i>
                No sale installment receipts recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('payments.create') }}" class="btn-action-income-receipt">
                        <i class="fa-solid fa-plus"></i> Record Sale Payment
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 3: Booking Advances --}}
    <div id="pane-project-income-bookings" style="display: none;">
        @if($hasBk)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Booking Date</th>
                            <th style="min-width: 150px;">Unit / Property</th>
                            <th style="min-width: 160px;">Customer Name</th>
                            <th style="white-space: nowrap;">Deal Value</th>
                            <th style="white-space: nowrap;">Booking / Token Paid</th>
                            <th style="white-space: nowrap;">Pending Balance</th>
                            <th style="white-space: nowrap;">Payment Mode</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 80px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectBookings as $idx => $bk)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $bk->booking_date ? \Carbon\Carbon::parse($bk->booking_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 13px;">
                                        {{ $bk->property?->property_name ?: ($bk->properties->pluck('property_name')->implode(', ') ?: '—') }}
                                    </strong>
                                </td>
                                <td>
                                    <span style="color: #E2E8F0; font-weight: 600;">
                                        {{ $bk->customer?->name ?: '—' }}
                                    </span>
                                </td>
                                <td style="color: #CBD5E1; white-space: nowrap;">
                                    ₹{{ number_format($bk->final_amount ?: ($bk->total_amount ?: 0), 2) }}
                                </td>
                                <td>
                                    <strong style="color: #34D399 !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($bk->booking_amount ?: 0, 2) }}</strong>
                                </td>
                                <td style="color: #FDA4AF; font-weight: 700; white-space: nowrap;">
                                    ₹{{ number_format($bk->remaining_amount ?: 0, 2) }}
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $bk->payment_mode ?: ($bk->paymentMode?->name ?: 'Cash') }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge badge-active" style="padding: 3px 8px; font-size: 11px;">
                                        {{ ucfirst($bk->status ?: 'Booked') }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ route('bookings.show', $bk->id) }}" class="btn-action-icon btn-action-view" title="View Booking">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-handshake" style="font-size: 32px; color: #C084FC; margin-bottom: 8px; display: block;"></i>
                No bookings recorded for this project yet.
            </div>
        @endif
    </div>

    {{-- Tab 4: Rental Collections --}}
    <div id="pane-project-income-rentals" style="display: none;">
        @if($hasRp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Payment Date</th>
                            <th style="white-space: nowrap;">Period / Month</th>
                            <th style="min-width: 150px;">Unit / Property</th>
                            <th style="min-width: 160px;">Tenant Name</th>
                            <th style="white-space: nowrap;">Rent Due</th>
                            <th style="white-space: nowrap;">Paid Amount</th>
                            <th style="white-space: nowrap;">Pending</th>
                            <th style="white-space: nowrap;">Mode</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 80px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectRentalPayments as $idx => $rp)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $rp->payment_date ? \Carbon\Carbon::parse($rp->payment_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    <span style="color: #FCD34D; font-weight: 700; font-size: 12.5px;">
                                        {{ $rp->payment_month ? $rp->payment_month . ' ' . $rp->payment_year : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 13px;">
                                        {{ $rp->property?->property_name ?: ($rp->rental?->property?->property_name ?: '—') }}
                                    </strong>
                                </td>
                                <td>
                                    <span style="color: #E2E8F0; font-weight: 600;">
                                        {{ $rp->rental?->tenant?->tenant_name ?: '—' }}
                                    </span>
                                </td>
                                <td style="color: #CBD5E1; white-space: nowrap;">
                                    ₹{{ number_format($rp->rent_amount ?: 0, 2) }}
                                </td>
                                <td>
                                    <strong style="color: #34D399 !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($rp->paid_amount ?: 0, 2) }}</strong>
                                </td>
                                <td style="color: #FDA4AF; font-weight: 700; white-space: nowrap;">
                                    ₹{{ number_format($rp->pending_amount ?: 0, 2) }}
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $rp->payment_mode ?: 'Cash' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge badge-active" style="padding: 3px 8px; font-size: 11px;">
                                        {{ ucfirst($rp->payment_status ?: 'Paid') }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        @if($rp->rental_id)
                                            <a href="{{ route('rentals.show', $rp->rental_id) }}" class="btn-action-icon btn-action-view" title="View Agreement">
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
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-key" style="font-size: 32px; color: #FBBF24; margin-bottom: 8px; display: block;"></i>
                No rental payment collections recorded for this project yet.
            </div>
        @endif
    </div>

    {{-- Tab 5: Direct & Misc Incomes --}}
    <div id="pane-project-income-direct" style="display: none;">
        @if($hasGi)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Date</th>
                            <th style="white-space: nowrap;">Income Type</th>
                            <th style="min-width: 150px;">Unit / Property</th>
                            <th style="min-width: 160px;">Received From</th>
                            <th style="white-space: nowrap;">Amount</th>
                            <th style="white-space: nowrap;">Payment Mode</th>
                            <th style="white-space: nowrap;">Ref / Trx No</th>
                            <th style="min-width: 130px;">Description</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 100px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectGeneralIncomes as $idx => $inc)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    <span class="badge" style="background: rgba(20, 184, 166, 0.15); color: #5EEAD4; border: 1px solid rgba(20, 184, 166, 0.30); white-space: nowrap;">
                                        {{ $inc->income_type ?: 'Direct Income' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 13px;">
                                        {{ $inc->property?->property_name ?: '—' }}
                                    </strong>
                                </td>
                                <td>
                                    <span style="color: #E2E8F0; font-weight: 600;">
                                        {{ $inc->received_from ?: '—' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #34D399 !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($inc->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $inc->paymentMode?->name ?: 'Cash' }}
                                    </span>
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px; white-space: nowrap;">
                                    {{ $inc->reference_no ?: '—' }}
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px;">
                                    {{ $inc->description ?: '—' }}
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <span class="badge badge-active" style="padding: 3px 8px; font-size: 11px;">
                                        {{ ucfirst($inc->status ?: 'Active') }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ route('incomes.show', $inc->id) }}" class="btn-action-icon btn-action-view" title="View Details">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <a href="{{ route('incomes.edit', $inc->id) }}" class="btn-action-icon btn-action-edit" title="Edit Income">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-coins" style="font-size: 32px; color: #5EEAD4; margin-bottom: 8px; display: block;"></i>
                No direct incomes recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('incomes.create') }}" class="btn-action-income-direct">
                        <i class="fa-solid fa-plus"></i> Add Direct Income
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleProjectIncomeTab(tab) {
    const tabs = ['all', 'sales', 'bookings', 'rentals', 'direct'];
    const themes = {
        all: 'tab-emerald',
        sales: 'tab-blue',
        bookings: 'tab-purple',
        rentals: 'tab-amber',
        direct: 'tab-teal'
    };

    tabs.forEach(t => {
        const btn = document.getElementById('btn-tab-inc-' + t);
        const pane = document.getElementById('pane-project-income-' + t);
        if (btn) {
            btn.classList.remove('active', 'tab-emerald', 'tab-blue', 'tab-purple', 'tab-amber', 'tab-teal');
        }
        if (pane) {
            pane.style.display = 'none';
        }
    });

    const activeBtn = document.getElementById('btn-tab-inc-' + tab);
    const activePane = document.getElementById('pane-project-income-' + tab);
    if (activeBtn) {
        activeBtn.classList.add('active', themes[tab] || 'tab-emerald');
    }
    if (activePane) {
        activePane.style.display = 'block';
    }
}
</script>

<!-- ================================================================
     PROJECT INVOICES & BILLING SECTION
================================================================ -->
<div class="card-box" style="margin-top: 24px;">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 17px;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin: 0;">Project Invoices &amp; Billing Statements</h3>
                <p style="font-size: 12.5px; color: #94A3B8; margin: 2px 0 0 0;">All sales, rental, contractor, purchase and custom invoices generated specifically for this project ({{ $projectInvoices->count() }} invoices).</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('invoices.create', ['project_id' => $project->id]) }}" class="btn-gold" style="padding: 8px 16px; font-size: 13px;">
                <i class="fa-solid fa-plus"></i> Generate Invoice for this Project
            </a>
            <a href="{{ route('invoices.index', ['project_id' => $project->id]) }}" class="btn-secondary-custom" style="padding: 8px 14px; font-size: 13px;">
                <i class="fa-solid fa-list"></i> View All in Invoices Module
            </a>
        </div>
    </div>

    <!-- Quick Mini KPI strip -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: rgba(15, 23, 42, 0.60); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Total Invoiced</span>
            <div style="font-size: 17px; font-weight: 800; color: #60A5FA; margin-top: 2px;">₹{{ number_format($projectInvoicesTotal, 2) }}</div>
        </div>
        <div style="background: rgba(15, 23, 42, 0.60); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Collected / Paid</span>
            <div style="font-size: 17px; font-weight: 800; color: #34D399; margin-top: 2px;">₹{{ number_format($projectInvoicesPaid, 2) }}</div>
        </div>
        <div style="background: rgba(15, 23, 42, 0.60); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Outstanding Balance</span>
            <div style="font-size: 17px; font-weight: 800; color: #FBBF24; margin-top: 2px;">₹{{ number_format($projectInvoicesBalance, 2) }}</div>
        </div>
        <div style="background: rgba(15, 23, 42, 0.60); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 12px; padding: 12px 16px;">
            <span style="font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Invoice Count</span>
            <div style="font-size: 17px; font-weight: 800; color: #FFFFFF; margin-top: 2px;">{{ $projectInvoices->count() }} Invoices</div>
        </div>
    </div>

    @if($projectInvoices->isNotEmpty())
        <div class="table-responsive-wrapper">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">#</th>
                        <th>Invoice No</th>
                        <th>Date</th>
                        <th>Recipient / Party</th>
                        <th>Category</th>
                        <th style="text-align: right;">Total Amount</th>
                        <th style="text-align: right;">Paid</th>
                        <th style="text-align: right;">Balance</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: right; width: 100px; padding-right: 18px !important;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($projectInvoices as $idx => $inv)
                        <tr>
                            <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $inv->id) }}" style="color: #60A5FA; font-weight: 800; text-decoration: none;">
                                    <i class="fa-solid fa-file-invoice" style="font-size: 11px; margin-right: 4px;"></i>{{ $inv->invoice_no }}
                                </a>
                            </td>
                            <td style="color: #CBD5E1; white-space: nowrap;">
                                {{ $inv->invoice_date->format('d M Y') }}
                            </td>
                            <td>
                                <strong style="color: #FFFFFF;">{{ $inv->recipient_name }}</strong>
                                @if($inv->recipient_phone)
                                    <div style="font-size: 11px; color: #94A3B8;">{{ $inv->recipient_phone }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge-inv {{ $inv->type_badge_class }}" style="font-size: 10.5px; padding: 3px 8px; border-radius: 6px;">
                                    {{ $inv->type_label }}
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #FFFFFF;">
                                ₹{{ number_format($inv->total_amount, 2) }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #34D399;">
                                ₹{{ number_format($inv->paid_amount, 2) }}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: {{ $inv->balance_amount > 0 ? '#F87171' : '#94A3B8' }};">
                                ₹{{ number_format($inv->balance_amount, 2) }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-inv {{ $inv->payment_badge_class }}" style="font-size: 10.5px; padding: 3px 8px; border-radius: 6px;">
                                    {{ str_replace('_', ' ', $inv->payment_status) }}
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="btn-action-icon btn-action-view" title="View Details">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn-action-icon btn-action-pdf" title="Print / PDF Invoice">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $inv->id) }}" class="btn-action-icon btn-action-edit" title="Edit Invoice">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; color: #94A3B8; padding: 36px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
            <i class="fa-solid fa-file-invoice-dollar" style="font-size: 36px; color: #60A5FA; margin-bottom: 10px; display: block;"></i>
            <div style="font-size: 14.5px; font-weight: 700; color: #FFFFFF; margin-bottom: 4px;">No Invoices Generated for {{ $project->project_name }}</div>
            <p style="font-size: 12.5px; color: #94A3B8; margin-bottom: 14px;">Generate plot sales, rental bills, contractor claims or material purchase invoices directly for this project.</p>
            <a href="{{ route('invoices.create', ['project_id' => $project->id]) }}" class="btn-gold" style="font-size: 13px; display: inline-flex;">
                <i class="fa-solid fa-plus"></i> Generate First Project Invoice
            </a>
        </div>
    @endif
</div>

<!-- ================================================================
     PROJECT EXPENSES & ALL OUTFLOWS SECTION
================================================================ -->
<div class="card-box" style="margin-top: 24px;">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: gap; margin-bottom: 20px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(245, 158, 11, 0.20); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 17px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #FFFFFF; margin: 0;">Project Expenses, Procurement &amp; Outgoing Payments</h3>
                <p style="font-size: 12.5px; color: #94A3B8; margin: 2px 0 0 0;">Auto-aggregated financial ledger of site expenses, materials, contractor payments, broker commissions, and land acquisition ({{ $projectExpenses->count() }} direct/PO &bull; {{ isset($contractorPayments) ? $contractorPayments->count() : 0 }} contractors &bull; {{ isset($brokerCommissions) ? $brokerCommissions->count() : 0 }} brokers &bull; {{ isset($landPayments) ? $landPayments->count() : 0 }} land payments).</p>
            </div>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('expenses.create', ['project_id' => $project->id]) }}" class="btn-action-expense">
                <i class="fa-solid fa-plus"></i> Add Expense
            </a>
            <a href="{{ route('purchase-orders.create', ['project_id' => $project->id]) }}" class="btn-action-po">
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
            <a href="{{ route('expenses.project-wise', ['project_id' => $project->id]) }}" class="btn-action-view">
                <i class="fa-solid fa-layer-group"></i> Project-wise View
            </a>
        </div>
    </div>

    <!-- Expense Financial Breakdown KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <div style="background: rgba(245, 158, 11, 0.10); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.20); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Total Project Cost</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($grandTotalProjectCost ?? $totalExpenses, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(139, 92, 246, 0.10); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(139, 92, 246, 0.20); color: #C4B5FD; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">PO &amp; Materials</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($poExpensesTotal, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(59, 130, 246, 0.10); border: 1px solid rgba(59, 130, 246, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(59, 130, 246, 0.20); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Direct Site Expenses</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($directExpensesTotal, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(244, 63, 94, 0.10); border: 1px solid rgba(244, 63, 94, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(244, 63, 94, 0.20); color: #FB7185; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Contractors</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($contractorPaymentsTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(168, 85, 247, 0.10); border: 1px solid rgba(168, 85, 247, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(168, 85, 247, 0.20); color: #D8B4FE; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Brokers</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($brokerCommissionsTotal ?? 0, 2) }}</div>
            </div>
        </div>

        <div style="background: rgba(20, 184, 166, 0.10); border: 1px solid rgba(20, 184, 166, 0.25); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(20, 184, 166, 0.20); color: #5EEAD4; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                <i class="fa-solid fa-mountain-sun"></i>
            </div>
            <div>
                <div style="font-size: 10.5px; font-weight: 800; color: #CBD5E1; text-transform: uppercase;">Land Acquisition</div>
                <div style="font-size: 17px; font-weight: 800; color: #FFFFFF !important; line-height: 1.2; margin-top: 2px;">₹{{ number_format($landPaymentsTotal ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    @php
        $hasExp = $projectExpenses->isNotEmpty();
        $hasCp = isset($contractorPayments) && $contractorPayments->isNotEmpty();
        $hasBc = isset($brokerCommissions) && $brokerCommissions->isNotEmpty();
        $hasLp = isset($landPayments) && $landPayments->isNotEmpty();

        $showDefaultTab = 'expenses';
        if (!$hasExp && $hasCp) {
            $showDefaultTab = 'contractors';
        } elseif (!$hasExp && !$hasCp && $hasBc) {
            $showDefaultTab = 'brokers';
        } elseif (!$hasExp && !$hasCp && !$hasBc && $hasLp) {
            $showDefaultTab = 'land';
        }
    @endphp

    {{-- Tabs Navigation --}}
    <div class="project-outflows-tabbar">
        <button type="button" id="btn-tab-project-exp" class="tab-pill-btn {{ $showDefaultTab === 'expenses' ? 'active tab-blue' : '' }}" onclick="toggleProjectShowTab('expenses')">
            <i class="fa-solid fa-receipt"></i>
            <span>Direct &amp; PO Expenses</span>
            <span class="tab-badge-count">{{ $projectExpenses->count() }}</span>
        </button>
        <button type="button" id="btn-tab-project-cp" class="tab-pill-btn {{ $showDefaultTab === 'contractors' ? 'active tab-rose' : '' }}" onclick="toggleProjectShowTab('contractors')">
            <i class="fa-solid fa-helmet-safety"></i>
            <span>Contractor Payments</span>
            <span class="tab-badge-count">{{ isset($contractorPayments) ? $contractorPayments->count() : 0 }}</span>
        </button>
        <button type="button" id="btn-tab-project-bc" class="tab-pill-btn {{ $showDefaultTab === 'brokers' ? 'active tab-purple' : '' }}" onclick="toggleProjectShowTab('brokers')">
            <i class="fa-solid fa-handshake"></i>
            <span>Broker Commissions</span>
            <span class="tab-badge-count">{{ isset($brokerCommissions) ? $brokerCommissions->count() : 0 }}</span>
        </button>
        <button type="button" id="btn-tab-project-lp" class="tab-pill-btn {{ $showDefaultTab === 'land' ? 'active tab-teal' : '' }}" onclick="toggleProjectShowTab('land')">
            <i class="fa-solid fa-mountain-sun"></i>
            <span>Land Payments</span>
            <span class="tab-badge-count">{{ isset($landPayments) ? $landPayments->count() : 0 }}</span>
        </button>
    </div>

    {{-- Tab 1: Direct & PO Expenses --}}
    <div id="pane-project-show-expenses" style="{{ $showDefaultTab === 'expenses' ? '' : 'display: none;' }}">
        @if($hasExp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Date</th>
                            <th style="min-width: 170px;">Expense Title / Details</th>
                            <th style="white-space: nowrap; min-width: 150px;">Source / Type</th>
                            <th style="white-space: nowrap;">Category</th>
                            <th style="white-space: nowrap;">Paid To / Vendor</th>
                            <th style="white-space: nowrap;">Amount</th>
                            <th style="white-space: nowrap;">Mode</th>
                            <th style="text-align: center; white-space: nowrap;">Status</th>
                            <th style="text-align: right; white-space: nowrap; width: 130px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projectExpenses as $idx => $exp)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') }}</td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-weight: 700; font-size: 13.5px;">{{ $exp->expense_title }}</strong>
                                    @if($exp->remarks)
                                        <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($exp->remarks, 50) }}</div>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    @if($exp->purchase_order_id && $exp->purchaseOrder)
                                        <a href="{{ route('purchase-orders.show', $exp->purchase_order_id) }}" 
                                           class="badge-po-link"
                                           title="View PO Details">
                                            <i class="fa-solid fa-file-invoice"></i> PO #{{ $exp->purchaseOrder->po_number }}
                                        </a>
                                    @else
                                        <span class="badge-direct-expense">
                                            <i class="fa-solid fa-receipt"></i> Direct Site Expense
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($exp->expenseCategory)
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.30); white-space: nowrap;">
                                            {{ $exp->expenseCategory->name }}
                                        </span>
                                    @elseif($exp->expense_category)
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #FBBF24; border: 1px solid rgba(245, 158, 11, 0.30); white-space: nowrap;">
                                            {{ $exp->expense_category }}
                                        </span>
                                    @else
                                        <span style="color: #64748B;">—</span>
                                    @endif
                                </td>
                                <td style="color: #CBD5E1; white-space: nowrap;">
                                    {{ $exp->paid_to ?: ($exp->purchaseOrder?->vendor?->vendor_name ?? '—') }}
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($exp->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; white-space: nowrap;">
                                        {{ $exp->payment_mode ?: 'Other' }}
                                    </span>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    @php $st = strtolower($exp->approval_status ?? 'pending'); @endphp
                                    <span class="badge badge-{{ $st === 'approved' ? 'active' : ($st === 'rejected' ? 'inactive' : 'booked') }}">
                                        <i class="fa-solid fa-circle-dot"></i> {{ ucfirst($exp->approval_status ?? 'Pending') }}
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        <a href="{{ route('expenses.detail-pdf', $exp->id) }}" target="_blank" class="btn-action-icon btn-action-pdf" title="Download Voucher PDF">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('expenses.show', $exp->id) }}" class="btn-action-icon btn-action-view" title="View Expense Details">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                        <a href="{{ route('expenses.edit', $exp->id) }}" class="btn-action-icon btn-action-edit" title="Edit Expense">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-receipt" style="font-size: 32px; color: #FBBF24; margin-bottom: 8px; display: block;"></i>
                No expenses or purchase orders recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('expenses.create', ['project_id' => $project->id]) }}" class="btn-action-expense">
                        <i class="fa-solid fa-plus"></i> Add Expense
                    </a>
                    <a href="{{ route('purchase-orders.create', ['project_id' => $project->id]) }}" class="btn-action-po">
                        <i class="fa-solid fa-file-invoice"></i> Create Purchase Order
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 2: Contractor Payments Ledger --}}
    <div id="pane-project-show-contractors" style="{{ $showDefaultTab === 'contractors' ? '' : 'display: none;' }}">
        @if($hasCp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Payment Date</th>
                            <th style="min-width: 170px;">Contractor Name</th>
                            <th style="white-space: nowrap;">Work Type</th>
                            <th style="white-space: nowrap;">Plot / Unit</th>
                            <th style="white-space: nowrap;">Amount Paid</th>
                            <th style="white-space: nowrap;">Mode &amp; Bank</th>
                            <th style="white-space: nowrap;">Ref / Bill No</th>
                            <th style="min-width: 130px;">Type / Remarks</th>
                            <th style="text-align: right; white-space: nowrap; width: 100px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contractorPayments as $idx => $cp)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ \Carbon\Carbon::parse($cp->payment_date)->format('d M Y') }}</td>
                                <td>
                                    @if($cp->contractor)
                                        <a href="{{ route('contractors.show', $cp->contractor_id) }}" style="color: #FFFFFF !important; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-helmet-safety" style="color: #FB7185; font-size: 12px;"></i>
                                            {{ $cp->contractor->contractor_name }}
                                        </a>
                                    @else
                                        <span style="color: #64748B;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(244, 63, 94, 0.15); color: #FDA4AF; border: 1px solid rgba(244, 63, 94, 0.30); white-space: nowrap;">
                                        {{ $cp->contractor->work_type ?? 'Contractor Work' }}
                                    </span>
                                </td>
                                <td>
                                    @if($cp->property)
                                        <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; white-space: nowrap;">
                                            <i class="fa-solid fa-house"></i> {{ $cp->property->property_name }}
                                        </span>
                                    @else
                                        <span style="color: #94A3B8; font-size: 11.5px;">Project-Wide</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($cp->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $cp->payment_mode ?: 'Cash' }} {{ $cp->bank_name ? '· ' . $cp->bank_name : '' }}
                                    </span>
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px; white-space: nowrap;">
                                    {{ $cp->reference_no ?: ($cp->bill_no ?: '—') }}
                                </td>
                                <td>
                                    <span style="background: rgba(16, 185, 129, 0.15); color: #34D399; border: 1px solid rgba(16, 185, 129, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; font-weight: 600; white-space: nowrap;">
                                        {{ $cp->payment_type ?: 'Paid' }}
                                    </span>
                                    @if($cp->remarks)
                                        <div style="font-size: 11px; color: #94A3B8; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($cp->remarks, 35) }}</div>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        @if($cp->contractor_id)
                                            <a href="{{ route('contractors.show', $cp->contractor_id) }}" class="btn-action-icon btn-action-view" title="Contractor Profile">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        @endif
                                        @if($cp->document_file)
                                            <a href="{{ asset('storage/' . $cp->document_file) }}" target="_blank" class="btn-action-icon btn-action-pdf" title="Receipt Document">
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
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-helmet-safety" style="font-size: 32px; color: #FB7185; margin-bottom: 8px; display: block;"></i>
                No contractor payments recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('contractors.index') }}" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px; background: #E11D48 !important; border-color: #FB7185 !important;">
                        <i class="fa-solid fa-helmet-safety"></i> Manage Contractors &amp; Payments
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 3: Broker Commissions Ledger --}}
    <div id="pane-project-show-brokers" style="{{ $showDefaultTab === 'brokers' ? '' : 'display: none;' }}">
        @if($hasBc)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Payment Date</th>
                            <th style="min-width: 170px;">Broker Name</th>
                            <th style="white-space: nowrap;">Property / Booking</th>
                            <th style="white-space: nowrap;">Commission Type</th>
                            <th style="white-space: nowrap;">Commission Amount</th>
                            <th style="white-space: nowrap;">Status</th>
                            <th style="min-width: 130px;">Remarks</th>
                            <th style="text-align: right; white-space: nowrap; width: 90px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brokerCommissions as $idx => $bc)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $bc->payment_date ? \Carbon\Carbon::parse($bc->payment_date)->format('d M Y') : ($bc->created_at ? $bc->created_at->format('d M Y') : '—') }}</td>
                                <td>
                                    @if($bc->broker)
                                        <a href="{{ route('brokers.show', $bc->broker_id) }}" style="color: #FFFFFF !important; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-handshake" style="color: #D8B4FE; font-size: 12px;"></i>
                                            {{ $bc->broker->name }}
                                        </a>
                                    @else
                                        <span style="color: #64748B;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($bc->property)
                                        <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; white-space: nowrap;">
                                            <i class="fa-solid fa-house"></i> {{ $bc->property->property_name }}
                                        </span>
                                    @elseif($bc->booking && $bc->booking->property)
                                        <span style="background: rgba(59, 130, 246, 0.15); color: #93C5FD; border: 1px solid rgba(59, 130, 246, 0.30); padding: 2px 7px; border-radius: 5px; font-size: 11px; white-space: nowrap;">
                                            <i class="fa-solid fa-house"></i> {{ $bc->booking->property->property_name }}
                                        </span>
                                    @else
                                        <span style="color: #94A3B8; font-size: 11.5px;">Project Sale</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #E9D5FF; border: 1px solid rgba(168, 85, 247, 0.30); white-space: nowrap;">
                                        {{ $bc->commission_type ?: 'Percentage' }} {{ $bc->commission_value ? '('.$bc->commission_value.'%)' : '' }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($bc->commission_amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-active">
                                        {{ ucfirst($bc->payment_status ?: 'Paid') }}
                                    </span>
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px;">
                                    {{ $bc->remarks ?: '—' }}
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        @if($bc->broker_id)
                                            <a href="{{ route('brokers.show', $bc->broker_id) }}" class="btn-action-icon btn-action-view" title="Broker Profile">
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
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-handshake" style="font-size: 32px; color: #D8B4FE; margin-bottom: 8px; display: block;"></i>
                No broker commissions recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('brokers.index') }}" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px; background: #9333EA !important; border-color: #C084FC !important;">
                        <i class="fa-solid fa-handshake"></i> Manage Brokers &amp; Commissions
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 4: Land Acquisition Payments Ledger --}}
    <div id="pane-project-show-land" style="{{ $showDefaultTab === 'land' ? '' : 'display: none;' }}">
        @if($hasLp)
            <div class="table-responsive-wrapper">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; white-space: nowrap;">#</th>
                            <th style="white-space: nowrap;">Payment Date</th>
                            <th style="min-width: 170px;">Land / Property Master</th>
                            <th style="white-space: nowrap;">Seller Name</th>
                            <th style="white-space: nowrap;">Amount Paid</th>
                            <th style="white-space: nowrap;">Payment Mode &amp; Bank</th>
                            <th style="white-space: nowrap;">Ref No</th>
                            <th style="min-width: 130px;">Remarks</th>
                            <th style="text-align: right; white-space: nowrap; width: 90px; padding-right: 18px !important;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($landPayments as $idx => $lp)
                            <tr>
                                <td style="color: #94A3B8; font-weight: 700; font-size: 12px;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                <td style="color: #CBD5E1; white-space: nowrap;">{{ $lp->payment_date ? \Carbon\Carbon::parse($lp->payment_date)->format('d M Y') : '—' }}</td>
                                <td>
                                    @if($lp->propertyMaster)
                                        <a href="{{ route('property-masters.show', $lp->property_master_id) }}" style="color: #FFFFFF !important; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                            <i class="fa-solid fa-mountain-sun" style="color: #5EEAD4; font-size: 12px;"></i>
                                            {{ $lp->propertyMaster->property_name }}
                                        </a>
                                    @else
                                        <span style="color: #64748B;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="color: #CBD5E1; font-weight: 600;">
                                        {{ $lp->propertyMaster?->seller_name ?? ($lp->propertyMaster?->seller?->name ?? '—') }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #FFFFFF !important; font-size: 14.5px; font-weight: 800; letter-spacing: -0.2px;">₹{{ number_format($lp->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span style="background: rgba(255, 255, 255, 0.08); color: #E2E8F0; padding: 2px 7px; border-radius: 5px; font-size: 11.5px; white-space: nowrap;">
                                        {{ $lp->payment_mode ?: 'Cash' }} {{ $lp->bank_name ? '· ' . $lp->bank_name : '' }}
                                    </span>
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px; white-space: nowrap;">
                                    {{ $lp->reference_no ?: '—' }}
                                </td>
                                <td style="color: #94A3B8; font-size: 11.5px;">
                                    {{ $lp->remarks ?: 'Land Payment' }}
                                </td>
                                <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                    <div style="display: inline-flex; gap: 6px; justify-content: flex-end; align-items: center;">
                                        @if($lp->property_master_id)
                                            <a href="{{ route('property-masters.show', $lp->property_master_id) }}" class="btn-action-icon btn-action-view" title="Land Master Profile">
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
            <div style="text-align: center; color: #94A3B8; padding: 32px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-mountain-sun" style="font-size: 32px; color: #5EEAD4; margin-bottom: 8px; display: block;"></i>
                No land acquisition payments recorded for this project yet.
                <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center;">
                    <a href="{{ route('property-masters.index') }}" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px; background: #0D9488 !important; border-color: #2DD4BF !important;">
                        <i class="fa-solid fa-mountain-sun"></i> Manage Land &amp; Property Masters
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleProjectShowTab(tab) {
    const btnExp = document.getElementById('btn-tab-project-exp');
    const btnCp = document.getElementById('btn-tab-project-cp');
    const btnBc = document.getElementById('btn-tab-project-bc');
    const btnLp = document.getElementById('btn-tab-project-lp');

    const paneExp = document.getElementById('pane-project-show-expenses');
    const paneCp = document.getElementById('pane-project-show-contractors');
    const paneBc = document.getElementById('pane-project-show-brokers');
    const paneLp = document.getElementById('pane-project-show-land');

    [btnExp, btnCp, btnBc, btnLp].forEach(b => {
        if (b) {
            b.classList.remove('active', 'tab-blue', 'tab-rose', 'tab-purple', 'tab-teal');
        }
    });

    [paneExp, paneCp, paneBc, paneLp].forEach(p => {
        if (p) p.style.display = 'none';
    });

    if (tab === 'expenses') {
        if (btnExp) btnExp.classList.add('active', 'tab-blue');
        if (paneExp) paneExp.style.display = 'block';
    } else if (tab === 'contractors') {
        if (btnCp) btnCp.classList.add('active', 'tab-rose');
        if (paneCp) paneCp.style.display = 'block';
    } else if (tab === 'brokers') {
        if (btnBc) btnBc.classList.add('active', 'tab-purple');
        if (paneBc) paneBc.style.display = 'block';
    } else if (tab === 'land') {
        if (btnLp) btnLp.classList.add('active', 'tab-teal');
        if (paneLp) paneLp.style.display = 'block';
    }
}
</script>

<!-- ================================================================
     PROJECT CONTRACTORS SECTION
=============================================================== -->
<div class="card-box" style="margin-top: 24px;">
    <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; border-bottom: 1.5px solid rgba(255, 255, 255, 0.10); padding-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.18); border: 1px solid rgba(245, 158, 11, 0.35); color: #FBBF24; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <div>
                <h3 style="font-size: 16.5px; font-weight: 800; color: #FFFFFF; margin: 0;">Assigned Project Contractors</h3>
                <p style="font-size: 12.5px; color: #94A3B8; margin: 2px 0 0 0;">Specialist agencies and contractors engaged on this project ({{ $project->contractors->count() }} active/registered).</p>
            </div>
        </div>
        <a href="{{ route('contractors.create', ['project_id' => $project->id]) }}" class="btn-gold" style="padding: 7px 18px; min-height: 38px; font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Add Contractor
        </a>
    </div>

    @if($project->contractors->count() > 0)
        <div class="table-responsive-wrapper">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th style="min-width: 180px;"><i class="fa-solid fa-user-gear" style="color: #60A5FA; margin-right: 5px;"></i>Contractor Name</th>
                        <th style="white-space: nowrap;"><i class="fa-solid fa-phone" style="color: #34D399; margin-right: 5px;"></i>Mobile</th>
                        <th style="white-space: nowrap;"><i class="fa-solid fa-id-card" style="color: #FBBF24; margin-right: 5px;"></i>Aadhar Card</th>
                        <th style="white-space: nowrap;"><i class="fa-solid fa-credit-card" style="color: #A78BFA; margin-right: 5px;"></i>PAN Card</th>
                        <th style="min-width: 160px;"><i class="fa-solid fa-building-columns" style="color: #38BDF8; margin-right: 5px;"></i>Bank Details</th>
                        <th style="white-space: nowrap; text-align: center;"><i class="fa-solid fa-circle-check" style="color: #10B981; margin-right: 5px;"></i>Status</th>
                        <th style="text-align: right; white-space: nowrap; width: 160px; padding-right: 18px !important;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->contractors as $con)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.30); color: #60A5FA; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                        <i class="fa-solid fa-user-gear"></i>
                                    </div>
                                    <div>
                                        <strong style="color: #FFFFFF !important; font-weight: 700; font-size: 13.5px; display: block;">{{ $con->contractor_name }}</strong>
                                        @if($con->address)
                                            <div style="font-size: 11.5px; color: #94A3B8; margin-top: 2px;">{{ $con->address }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="white-space: nowrap;">
                                @if($con->mobile)
                                    <span style="color: #E2E8F0; font-weight: 600; font-size: 12.5px;"><i class="fa-solid fa-phone" style="font-size: 10px; color: #60A5FA; margin-right: 4px;"></i>{{ $con->mobile }}</span>
                                @else
                                    <span style="color: #64748B;">—</span>
                                @endif
                            </td>
                            <td style="white-space: nowrap;">
                                @if($con->aadhar_no)
                                    <span style="background: rgba(245, 158, 11, 0.12); color: #FCD34D; border: 1px solid rgba(245, 158, 11, 0.25); padding: 3px 8px; border-radius: 5px; font-family: monospace; font-size: 11.5px; font-weight: 700;">{{ $con->aadhar_no }}</span>
                                @else
                                    <span style="color: #64748B;">—</span>
                                @endif
                            </td>
                            <td style="white-space: nowrap;">
                                @if($con->pan_no)
                                    <span style="background: rgba(16, 185, 129, 0.12); color: #6EE7B7; border: 1px solid rgba(16, 185, 129, 0.25); padding: 3px 8px; border-radius: 5px; font-family: monospace; font-size: 11.5px; font-weight: 700;">{{ $con->pan_no }}</span>
                                @else
                                    <span style="color: #64748B;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($con->bank_name || $con->account_number)
                                    <div style="color: #FFFFFF !important; font-weight: 600; font-size: 12.5px;">{{ $con->bank_name ?: 'Bank Account' }}</div>
                                    @if($con->account_number)
                                        <div style="font-size: 11.5px; color: #94A3B8; font-family: monospace; margin-top: 1px;">A/C: {{ $con->account_number }}</div>
                                    @endif
                                @else
                                    <span style="color: #64748B;">—</span>
                                @endif
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <span class="badge badge-{{ $con->status === 'active' ? 'active' : 'inactive' }}" style="padding: 4px 10px; font-size: 11px; font-weight: 800; border-radius: 6px; text-transform: uppercase;">
                                    <i class="fa-solid fa-circle" style="font-size: 6px;"></i> {{ ucfirst($con->status) }}
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap; padding-right: 18px !important;">
                                <div style="display: inline-flex; gap: 8px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('contractors.show', $con) }}" class="btn-action-icon btn-action-view" style="width: auto; height: 32px; padding: 0 12px; font-size: 12px; font-weight: 700; gap: 5px;" title="View Contractor Profile">
                                        <i class="fa-regular fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('contractors.edit', $con) }}" class="btn-action-icon btn-action-edit" style="width: auto; height: 32px; padding: 0 12px; font-size: 12px; font-weight: 700; gap: 5px;" title="Edit Contractor">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; color: #94A3B8; padding: 28px 0; background: rgba(255, 255, 255, 0.02); border-radius: 12px; border: 1px dashed rgba(255, 255, 255, 0.12);">
            <i class="fa-solid fa-helmet-safety" style="font-size: 28px; color: #FBBF24; margin-bottom: 8px; display: block;"></i>
            No contractors assigned to this project yet.
            <div style="margin-top: 10px;">
                <a href="{{ route('contractors.create', ['project_id' => $project->id]) }}" class="btn-gold" style="padding: 6px 16px; font-size: 12.5px; display: inline-flex;">
                    <i class="fa-solid fa-plus"></i> Assign Contractor
                </a>
            </div>
        </div>
    @endif
</div>

<!-- ================================================================
     MODAL 1: ADD SINGLE PLOT TO PROJECT
================================================================ -->
<div class="modal-backdrop-custom" id="addProjectPlotModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-plus-circle" style="color: #10B981;"></i> Add Single Plot to Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('addProjectPlotModal')">&times;</button>
        </div>
        <form action="{{ route('projects.add-plot', $project->id) }}" method="POST">
            @csrf
            <div class="modal-body-custom">
                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Name <span>*</span></label>
                        <input type="text" name="property_name" class="m-form-control" placeholder="e.g. Plot 15" required>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Unit / Plot Number</label>
                        <input type="text" name="unit_no" class="m-form-control" value="{{ $project->getNextPlotSequenceNumber() }}" placeholder="e.g. 15">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Property Type</label>
                        <select name="property_type_id" class="m-form-control">
                            @foreach($propertyTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Facing Direction</label>
                        <select name="facing" class="m-form-control">
                            <option value="">Select Direction</option>
                            <option value="East">East</option>
                            <option value="West">West</option>
                            <option value="North">North</option>
                            <option value="South">South</option>
                            <option value="North-East">North-East</option>
                            <option value="North-West">North-West</option>
                            <option value="South-East">South-East</option>
                            <option value="South-West">South-West</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Size</label>
                        <input type="number" step="0.01" name="size" class="m-form-control" placeholder="e.g. 1200">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Size Unit</label>
                        <select name="size_unit" class="m-form-control">
                            <option value="sq.ft" selected>sq.ft</option>
                            <option value="sq.yard">sq.yard</option>
                            <option value="sq.meter">sq.meter</option>
                            <option value="acre">acre</option>
                            <option value="bigha">bigha</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Purchase Rate (₹)</label>
                        <input type="number" step="0.01" name="purchase_rate" class="m-form-control" placeholder="e.g. 1500">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Selling / Asking Price (₹)</label>
                        <input type="number" step="0.01" name="price" class="m-form-control" placeholder="e.g. 2200">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Initial Status <span>*</span></label>
                        <select name="status" class="m-form-control" required>
                            <option value="available" selected>Available (Free)</option>
                            <option value="booked">Booked</option>
                            <option value="sold">Sold</option>
                            <option value="rented">Rented</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Description / Remarks</label>
                        <input type="text" name="description" class="m-form-control" placeholder="Optional notes...">
                    </div>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50); border-radius: 0 0 20px 20px;">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('addProjectPlotModal')">Cancel</button>
                <button type="submit" class="btn-solid-emerald"><i class="fa-solid fa-check"></i> Save Plot</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 2: BULK PLOTS GENERATOR FOR PROJECT
================================================================ -->
<div class="modal-backdrop-custom" id="bulkProjectPlotsModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-bolt" style="color: #A855F7;"></i> Bulk Generate Plots for Project</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('bulkProjectPlotsModal')">&times;</button>
        </div>
        <form action="{{ route('projects.bulk-generate-plots', $project->id) }}" method="POST">
            @csrf
            <div class="modal-body-custom">
                <!-- Specific Unit Numbers Range Option -->
                <div class="m-form-group" style="margin-bottom: 16px; background: rgba(245, 158, 11, 0.08); border: 1px dashed rgba(245, 158, 11, 0.35); padding: 12px 14px; border-radius: 10px;">
                    <label class="m-form-label" style="color: #FDE68A; margin-bottom: 4px;">
                        <i class="fa-solid fa-list-ol"></i> Specific Unit / Plot Numbers Range (Optional)
                    </label>
                    <input type="text" name="unit_numbers_list" class="m-form-control" placeholder="e.g. 1-10, 30, 35 or 1 to 10, 30, 35" style="border-color: rgba(245, 158, 11, 0.4);">
                    <small style="color: #CBD5E1; font-size: 11.5px; margin-top: 4px; display: block;">
                        Leave blank to generate sequentially, or enter ranges like <code>1-10, 30, 35</code> to generate specific plots.
                    </small>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Total Plots to Generate <span style="font-size: 11px; color:#94A3B8;">(if not using range)</span></label>
                        <input type="number" min="1" max="1000" name="total_plots" class="m-form-control" value="10">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Plot Name Prefix</label>
                        <input type="text" name="plot_prefix" class="m-form-control" value="Plot " placeholder="e.g. Plot ">
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Starting Unit Number</label>
                        <input type="number" min="1" name="start_number" class="m-form-control" value="{{ $project->getNextPlotSequenceNumber() }}" placeholder="e.g. 1">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Property Type</label>
                        <select name="property_type_id" class="m-form-control">
                            @foreach($propertyTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Default Size per Plot</label>
                        <input type="number" step="0.01" name="size" class="m-form-control" placeholder="e.g. 1200">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Size Unit</label>
                        <select name="size_unit" class="m-form-control">
                            <option value="sq.ft" selected>sq.ft</option>
                            <option value="sq.yard">sq.yard</option>
                            <option value="sq.meter">sq.meter</option>
                            <option value="acre">acre</option>
                            <option value="bigha">bigha</option>
                        </select>
                    </div>
                </div>

                <div class="m-form-row">
                    <div class="m-form-group">
                        <label class="m-form-label">Purchase Rate (₹)</label>
                        <input type="number" step="0.01" name="purchase_rate" class="m-form-control" placeholder="e.g. 1500">
                    </div>
                    <div class="m-form-group">
                        <label class="m-form-label">Selling Price (₹)</label>
                        <input type="number" step="0.01" name="price" class="m-form-control" placeholder="e.g. 2200">
                    </div>
                </div>

                <div class="m-form-group">
                    <label class="m-form-label">Facing Direction</label>
                    <select name="facing" class="m-form-control">
                        <option value="">Not Specified</option>
                        <option value="East">East</option>
                        <option value="West">West</option>
                        <option value="North">North</option>
                        <option value="South">South</option>
                    </select>
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50); border-radius: 0 0 20px 20px;">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('bulkProjectPlotsModal')">Cancel</button>
                <button type="submit" class="btn-solid-purple"><i class="fa-solid fa-bolt"></i> Generate Plots</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 3: EXCEL PLOTS IMPORT FOR PROJECT
================================================================ -->
<div class="modal-backdrop-custom" id="importProjectExcelModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-solid fa-file-excel" style="color: #3B82F6;"></i> Import Plots from Excel / CSV</h3>
            <button type="button" class="modal-close-btn" onclick="closeModal('importProjectExcelModal')">&times;</button>
        </div>
        <form action="{{ route('projects.import-plots', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body-custom">
                <div style="background: rgba(59, 130, 246, 0.10); border: 1px dashed rgba(59, 130, 246, 0.35); border-radius: 12px; padding: 16px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 6px 0; color: #93C5FD; font-size: 14px; font-weight: 700;">
                        <i class="fa-solid fa-circle-info"></i> Project Excel Import Instructions:
                    </h4>
                    <p style="margin: 0 0 10px 0; color: #CBD5E1; font-size: 12.5px; line-height: 1.5;">
                        Upload your spreadsheet (<code>.xlsx</code>, <code>.xls</code>, <code>.csv</code>). The system automatically detects and maps columns: <strong>Plot/Unit No, Plot Name, Size, Facing, Purchase Rate, Selling Price, Status</strong>.
                    </p>
                    <a href="{{ route('projects.plots.template') }}" class="btn-solid-slate" style="padding: 6px 12px; font-size: 12px;">
                        <i class="fa-solid fa-download"></i> Download Sample Excel Template
                    </a>
                </div>

                <div class="m-form-group">
                    <label class="m-form-label">Select Excel / CSV Spreadsheet <span>*</span></label>
                    <input type="file" name="excel_file" class="m-form-control" accept=".xlsx,.xls,.csv,.txt" required style="padding: 8px;">
                </div>
            </div>
            <div style="padding: 16px 24px; border-top: 1px solid rgba(255, 255, 255, 0.10); display: flex; justify-content: flex-end; gap: 10px; background: rgba(30, 41, 59, 0.50); border-radius: 0 0 20px 20px;">
                <button type="button" class="btn-secondary-custom" onclick="closeModal('importProjectExcelModal')">Cancel</button>
                <button type="submit" class="btn-solid-blue"><i class="fa-solid fa-upload"></i> Upload &amp; Import Plots</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     MODAL 4: QUICK EDIT PLOT
================================================================ -->
<div class="modal-backdrop-custom" id="quickEditPlotModal">
    <div class="modal-box-custom">
        <div class="modal-header-custom">
            <h3><i class="fa-regular fa-pen-to-square" style="color: #60A5FA; margin-right: 8px;"></i>Edit Plot Details</h3>
            <button type="button" class="modal-close-btn" onclick="closeQuickEditPlotModal()">&times;</button>
        </div>

        <form id="quickEditPlotForm" method="POST" onsubmit="return handlePlotSubmit(event, this)">
            @csrf
            @method('PUT')
            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Plot Name <span>*</span></label>
                    <input type="text" name="property_name" id="qe_property_name" class="m-form-control" required>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Plot Code <span>*</span></label>
                    <input type="text" name="property_code" id="qe_property_code" class="m-form-control" required>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Size (Area)</label>
                    <input type="text" name="size" id="qe_size" class="m-form-control" placeholder="e.g. 1200">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Size Unit</label>
                    <select name="size_unit" id="qe_size_unit" class="m-form-control">
                        <option value="sq.ft">sq.ft</option>
                        <option value="sq.yard">sq.yard</option>
                        <option value="sq.meter">sq.meter</option>
                        <option value="acre">acre</option>
                        <option value="bigha">bigha</option>
                    </select>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Facing Direction</label>
                    <select name="facing" id="qe_facing" class="m-form-control">
                        <option value="">Select Direction</option>
                        <option value="East">East</option>
                        <option value="West">West</option>
                        <option value="North">North</option>
                        <option value="South">South</option>
                        <option value="North-East">North-East</option>
                        <option value="North-West">North-West</option>
                        <option value="South-East">South-East</option>
                        <option value="South-West">South-West</option>
                    </select>
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Status <span>*</span></label>
                    <select name="status" id="qe_status" class="m-form-control" required>
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                        <option value="sold">Sold</option>
                        <option value="rented">Rented</option>
                        <option value="blocked">Blocked</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="m-form-row">
                <div class="m-form-group">
                    <label class="m-form-label">Purchase Rate (₹)</label>
                    <input type="number" step="0.01" name="purchase_rate" id="qe_purchase_rate" class="m-form-control">
                </div>
                <div class="m-form-group">
                    <label class="m-form-label">Selling / Asking Price (₹)</label>
                    <input type="number" step="0.01" name="price" id="qe_price" class="m-form-control">
                </div>
            </div>

            <div class="m-form-group">
                <label class="m-form-label">Notes / Description</label>
                <textarea name="description" id="qe_description" rows="2" class="m-form-control" placeholder="Optional notes..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 14px; border-top: 1px solid rgba(255, 255, 255, 0.10);">
                <button type="button" class="btn-secondary-custom" onclick="closeQuickEditPlotModal()">Cancel</button>
                <button type="submit" class="btn-solid-blue">
                    <i class="fa-solid fa-check"></i> Update Plot
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('active');
}

function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
}

function openQuickEditPlotModal(id, name, code, size, sizeUnit, facing, purchaseRate, price, status, desc) {
    document.getElementById('quickEditPlotForm').action = "/projects/{{ $project->id }}/plots/" + id;
    document.getElementById('qe_property_name').value = name;
    document.getElementById('qe_property_code').value = code;
    document.getElementById('qe_size').value = size || '';
    document.getElementById('qe_size_unit').value = sizeUnit || 'sq.ft';
    document.getElementById('qe_facing').value = facing || '';
    document.getElementById('qe_purchase_rate').value = purchaseRate || '';
    document.getElementById('qe_price').value = price || '';
    document.getElementById('qe_status').value = (status || 'available').toLowerCase();
    document.getElementById('qe_description').value = desc || '';
    document.getElementById('quickEditPlotModal').classList.add('active');
}

function closeQuickEditPlotModal() {
    document.getElementById('quickEditPlotModal').classList.remove('active');
}

function filterProjectPlots(category, btn) {
    document.querySelectorAll('.filter-tab-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    const rows = document.querySelectorAll('.project-plot-row');
    rows.forEach(row => {
        const cat = row.getAttribute('data-category');
        if (category === 'all' || cat === category) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function searchProjectPlots(query) {
    const q = query.toLowerCase().trim();
    const rows = document.querySelectorAll('.project-plot-row');
    rows.forEach(row => {
        const search = row.getAttribute('data-search') || '';
        if (!q || search.includes(q)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

let isSubmittingPlot = false;
function handlePlotSubmit(event, form) {
    if (isSubmittingPlot) {
        event.preventDefault();
        return false;
    }
    isSubmittingPlot = true;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';
    }
    return true;
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickEditPlotModal();
        closeModal('addProjectPlotModal');
        closeModal('bulkProjectPlotsModal');
        closeModal('importProjectExcelModal');
    }
});
</script>

<style>
/* ── Outflows Tabs Segmented Control ── */
.project-outflows-tabbar {
    display: inline-flex;
    flex-wrap: wrap;
    gap: 8px;
    background: rgba(10, 15, 26, 0.70);
    border: 1px solid rgba(255, 255, 255, 0.10);
    padding: 6px;
    border-radius: 14px;
    margin-bottom: 20px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
}
.tab-pill-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #94A3B8;
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
}
.tab-pill-btn:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(255, 255, 255, 0.12);
}
.tab-badge-count {
    background: rgba(255, 255, 255, 0.12);
    color: #FFFFFF;
    padding: 1px 7px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 800;
}
/* Active Tab Themes */
.tab-pill-btn.active.tab-emerald {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    border-color: #34D399 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(5, 150, 105, 0.45);
}
.tab-pill-btn.active.tab-amber {
    background: linear-gradient(135deg, #D97706 0%, #B45309 100%) !important;
    border-color: #FBBF24 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(217, 119, 6, 0.45);
}
.tab-pill-btn.active.tab-blue {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    border-color: #60A5FA !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.45);
}
.tab-pill-btn.active.tab-rose {
    background: linear-gradient(135deg, #E11D48 0%, #BE123C 100%) !important;
    border-color: #FB7185 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(225, 29, 72, 0.45);
}
.tab-pill-btn.active.tab-purple {
    background: linear-gradient(135deg, #9333EA 0%, #7E22CE 100%) !important;
    border-color: #C084FC !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(147, 51, 234, 0.45);
}
.tab-pill-btn.active.tab-teal {
    background: linear-gradient(135deg, #0D9488 0%, #0F766E 100%) !important;
    border-color: #2DD4BF !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 16px rgba(13, 148, 136, 0.45);
}

.pk-emerald { background: rgba(16, 185, 129, 0.20); color: #34D399; }
.pk-cyan    { background: rgba(6, 182, 212, 0.20); color: #22D3EE; }

.btn-action-income-receipt {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid #3B82F6 !important;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
    transition: all 0.2s ease;
}
.btn-action-income-receipt:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.50);
    color: #FFFFFF !important;
}

.btn-action-income-direct {
    background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid #10B981 !important;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    transition: all 0.2s ease;
}
.btn-action-income-direct:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.50);
    color: #FFFFFF !important;
}

.btn-action-sales-ledger {
    background: rgba(139, 92, 246, 0.16) !important;
    border: 1.5px solid rgba(168, 85, 247, 0.45) !important;
    color: #C084FC !important;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.20);
    transition: all 0.2s ease;
}
.btn-action-sales-ledger:hover {
    background: rgba(139, 92, 246, 0.32) !important;
    border-color: #A855F7 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(139, 92, 246, 0.40);
}

.btn-action-bookings {
    background: rgba(245, 158, 11, 0.16) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.45) !important;
    color: #FBBF24 !important;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.20);
    transition: all 0.2s ease;
}
.btn-action-bookings:hover {
    background: rgba(245, 158, 11, 0.32) !important;
    border-color: #F59E0B !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(245, 158, 11, 0.40);
}

.btn-action-rentals {
    background: rgba(6, 182, 212, 0.16) !important;
    border: 1.5px solid rgba(6, 182, 212, 0.45) !important;
    color: #22D3EE !important;
    padding: 7px 15px;
    border-radius: 9px;
    font-size: 12.5px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.20);
    transition: all 0.2s ease;
}
.btn-action-rentals:hover {
    background: rgba(6, 182, 212, 0.32) !important;
    border-color: #06B6D4 !important;
    color: #FFFFFF !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(6, 182, 212, 0.40);
}

/* ── Badges ── */
.badge-direct-expense {
    background: rgba(59, 130, 246, 0.15);
    color: #93C5FD;
    border: 1px solid rgba(59, 130, 246, 0.30);
    padding: 4px 9px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.badge-po-link {
    background: rgba(139, 92, 246, 0.18);
    color: #DDD6FE;
    border: 1px solid rgba(139, 92, 246, 0.38);
    padding: 4px 9px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
}
.badge-po-link:hover {
    background: rgba(139, 92, 246, 0.30);
    border-color: #C084FC;
    color: #FFFFFF;
    transform: translateY(-1px);
}

/* ── Premium Responsive Tables & Outflows ── */
.table-responsive-wrapper {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
    border-radius: 14px !important;
    border: 1px solid rgba(255, 255, 255, 0.10) !important;
    background: rgba(10, 14, 23, 0.65) !important;
    margin-bottom: 16px;
}

/* Custom subtle scrollbar */
.table-responsive-wrapper::-webkit-scrollbar {
    height: 6px;
}
.table-responsive-wrapper::-webkit-scrollbar-track {
    background: rgba(10, 14, 23, 0.50);
    border-radius: 4px;
}
.table-responsive-wrapper::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.20);
    border-radius: 4px;
}
.table-responsive-wrapper::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.35);
}

.premium-table {
    width: 100% !important;
    min-width: 960px;
    border-collapse: collapse !important;
    text-align: left;
    font-size: 13px !important;
}

.premium-table th {
    padding: 13px 16px !important;
    background: rgba(255, 255, 255, 0.05) !important;
    color: #94A3B8 !important;
    font-weight: 800 !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
    border-bottom: 1.5px solid rgba(255, 255, 255, 0.10) !important;
    white-space: nowrap !important;
}

.premium-table td {
    padding: 13px 16px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
    font-size: 13px !important;
    color: #E2E8F0 !important;
    font-weight: 500;
    vertical-align: middle !important;
    white-space: nowrap;
}

.premium-table th:last-child,
.premium-table td:last-child {
    padding-right: 20px !important;
    text-align: right !important;
}

.premium-table td strong {
    color: #FFFFFF !important;
    font-weight: 700 !important;
}

.premium-table tbody tr:hover {
    background: rgba(255, 255, 255, 0.04) !important;
}

.premium-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* ── Modern Table Action Buttons ── */
.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12.5px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid transparent;
}
.btn-action-icon:hover {
    transform: translateY(-2px);
}
.btn-action-pdf {
    background: rgba(239, 68, 68, 0.15) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
    color: #F87171 !important;
}
.btn-action-pdf:hover {
    background: rgba(239, 68, 68, 0.28) !important;
    border-color: #EF4444 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35);
}
.btn-action-view {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
    color: #34D399 !important;
}
.btn-action-view:hover {
    background: rgba(16, 185, 129, 0.28) !important;
    border-color: #10B981 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
}
.btn-action-edit {
    background: rgba(59, 130, 246, 0.15) !important;
    border-color: rgba(59, 130, 246, 0.35) !important;
    color: #60A5FA !important;
}
.btn-action-edit:hover {
    background: rgba(59, 130, 246, 0.28) !important;
    border-color: #3B82F6 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
}
</style>
@endsection
