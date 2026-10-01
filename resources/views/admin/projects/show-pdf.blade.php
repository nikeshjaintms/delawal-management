<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Master - {{ $project->project_name }} - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'portrait'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Project Dossier: ' . $project->project_name,
    'orientation' => 'portrait',
    'backUrl' => route('projects.show', $project->id)
])

@include('admin.components.pdf-header', [
    'title' => 'Project Dossier & Site Profile',
    'subtitle' => 'Development Status & Unit Inventory',
    'firm' => $project->firm ?? null,
    'docRef' => $project->project_code ?: ('PROJ-' . $project->id)
])

<div class="stat-row">
    <div class="stat-box s-gold">
        <div class="s-label">Project Code</div>
        <div class="s-value" style="font-size:14px;">{{ $project->project_code ?: ('PROJ-' . $project->id) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Total Units / Plots</div>
        <div class="s-value">{{ $project->total_plots ?? ($totalPlots ?? 0) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Available Units</div>
        <div class="s-value">{{ $project->available_plots ?? ($availablePlots ?? 0) }}</div>
    </div>
</div>

<div class="grid-2">
    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-city"></i> Project Information</div>
        <div class="info-row">
            <span class="info-label">Project Name:</span>
            <span class="info-val">{{ $project->project_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Type:</span>
            <span class="info-val">{{ ucfirst($project->project_type ?: 'Plotted Development') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Firm:</span>
            <span class="info-val">{{ $project->firm->firm_name ?? 'Delawala Infra Co.' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-val"><span class="badge badge-success">{{ ucfirst($project->status ?: 'Active') }}</span></span>
        </div>
    </div>

    <div class="grid-col">
        <div class="col-heading"><i class="fa-solid fa-location-dot"></i> Location & Area</div>
        <div class="info-row">
            <span class="info-label">Site Address:</span>
            <span class="info-val">{{ $project->display_address ?: ($project->address ?: 'Dahegam Road') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">City / District:</span>
            <span class="info-val">{{ $project->city ?: 'Dahegam, Bharuch' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Area:</span>
            <span class="info-val">{{ $project->total_area ? ($project->total_area . ' ' . ($project->area_unit ?: 'Sq.Ft')) : '—' }}</span>
        </div>
    </div>
</div>

@include('admin.components.pdf-footer')
</body>
</html>