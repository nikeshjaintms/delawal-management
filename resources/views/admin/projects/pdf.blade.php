<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Projects Directory & Development Scheme Register - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Master Projects Directory & Development Scheme Register',
    'orientation' => 'landscape',
    'backUrl' => route('projects.index')
])

@php
    $totUnits = 0;
    $totAvail = 0;
    $totBookedSold = 0;

    foreach($projects as $p) {
        $u = (int)($p->total_plots ?? $p->total_units ?? 0);
        $av = (int)($p->available_plots ?? $p->available_units ?? 0);
        $bs = (int)(($p->booked_plots ?? 0) + ($p->sold_plots ?? 0));

        $totUnits += $u;
        $totAvail += $av;
        $totBookedSold += $bs;
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Master Projects Directory & Development Scheme Register',
    'subtitle' => 'Real Estate Projects, Site Development Progress & Plot/Unit Allocation Status'
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Real Estate Schemes</div>
        <div class="s-value">{{ $totalProjects ?? $projects->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Sites Under Dev</div>
        <div class="s-value">{{ $activeProjects ?? $projects->where('status', 'active')->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Plotted / Built Units</div>
        <div class="s-value">{{ number_format($totUnits) }}</div>
    </div>
    <div class="stat-box s-emerald">
        <div class="s-label">Available for Allotment</div>
        <div class="s-value">{{ number_format($totAvail) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Booked & Sold Inventory</div>
        <div class="s-value">{{ number_format($totBookedSold) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-city"></i> Real Estate Projects &amp; Infrastructure Schemes</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Project Scheme Name</th>
            <th style="width:85px;">Scheme Code</th>
            <th>Location / Municipality</th>
            <th>Firm Affiliation</th>
            <th class="r" style="width:95px;">Total Units</th>
            <th class="r" style="width:95px;">Available</th>
            <th class="r" style="width:105px;">Booked / Sold</th>
            <th class="c" style="width:80px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $i => $p)
        @php
            $u = (int)($p->total_plots ?? $p->total_units ?? 0);
            $av = (int)($p->available_plots ?? $p->available_units ?? 0);
            $bs = (int)(($p->booked_plots ?? 0) + ($p->sold_plots ?? 0));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $p->project_name }}</strong></td>
            <td><span class="badge badge-info">{{ $p->project_code ?: '—' }}</span></td>
            <td>{{ $p->display_address ?: ($p->city ?: 'Dahegam, Bharuch') }}</td>
            <td>{{ $p->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td class="r" style="font-weight:600;">{{ number_format($u) }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ number_format($av) }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">{{ number_format($bs) }}</td>
            <td class="c">
                @if(($p->status ?? 'active') === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($p->status ?: 'Completed') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No project records found.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>CONSOLIDATED PORTFOLIO TOTALS:</strong></td>
            <td class="r" style="font-size:11px;"><strong>{{ number_format($totUnits) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ number_format($totAvail) }}</strong></td>
            <td class="r" style="color:#B45309; font-size:11px;"><strong>{{ number_format($totBookedSold) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>