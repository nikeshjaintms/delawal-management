<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractors Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Contractors Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('contractors.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Contractors Directory Report',
    'subtitle' => 'Civil & Labor Contractors Registry'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Contractors</div>
        <div class="s-value">{{ $totalContractors ?? $contractors->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Contractors</div>
        <div class="s-value">{{ $activeContractors ?? $contractors->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-helmet-safety"></i> Civil &amp; Labor Contractors</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Contractor Name</th>
            <th>Mobile</th>
            <th>Trade / Specialization</th>
            <th>Firm Link</th>
            <th>City</th>
            <th>Address</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contractors as $i => $c)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $c->name }}</strong></td>
            <td>{{ $c->mobile ?: '—' }}</td>
            <td>{{ $c->specialization ?: ($c->type ?: 'General Contractor') }}</td>
            <td>{{ $c->firm->firm_name ?? 'Delawala Infra Co.' }}</td>
            <td>{{ $c->city ?: 'Dahegam' }}</td>
            <td>{{ $c->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($c->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No contractors found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>