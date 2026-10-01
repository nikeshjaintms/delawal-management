<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Projects Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('projects.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Projects Directory Report',
    'subtitle' => 'Real Estate & Infrastructure Master Projects'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Projects</div>
        <div class="s-value">{{ $totalProjects ?? $projects->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Sites</div>
        <div class="s-value">{{ $activeProjects ?? $projects->where('status', 'active')->count() }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Units / Plots</div>
        <div class="s-value">{{ $totalPlots ?? $projects->sum('total_plots') }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Available Units</div>
        <div class="s-value">{{ $availablePlots ?? $projects->sum('available_plots') }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-city"></i> Master Projects Registry</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Project Name</th>
            <th>Project Code</th>
            <th>Location / City</th>
            <th>Firm Affiliation</th>
            <th class="r">Total Units</th>
            <th class="r">Available</th>
            <th class="r">Booked / Sold</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($projects as $i => $p)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $p->project_name }}</strong></td>
            <td><span class="badge badge-info">{{ $p->project_code ?: '—' }}</span></td>
            <td>{{ $p->display_address ?: ($p->city ?: 'Dahegam, Bharuch') }}</td>
            <td>{{ $p->firm->firm_name ?? 'Delawala Properties' }}</td>
            <td class="r">{{ $p->total_plots ?? $p->total_units ?? 0 }}</td>
            <td class="r" style="color:#059669; font-weight:700;">{{ $p->available_plots ?? $p->available_units ?? 0 }}</td>
            <td class="r" style="color:#B45309; font-weight:700;">{{ ($p->booked_plots ?? 0) + ($p->sold_plots ?? 0) }}</td>
            <td class="c">
                @if(($p->status ?? 'active') === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-warning">{{ ucfirst($p->status ?: 'Completed') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:20px;color:#64748B;">No project records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>