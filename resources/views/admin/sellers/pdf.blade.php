<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Sellers Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Land Sellers Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('sellers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Land Sellers Directory Report',
    'subtitle' => 'Property Acquisition & Seller Registry'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Sellers</div>
        <div class="s-value">{{ $totalSellers ?? $sellers->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Sellers</div>
        <div class="s-value">{{ $activeSellers ?? $sellers->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Land Sellers Registry</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Seller Name</th>
            <th>Mobile Number</th>
            <th>Email Address</th>
            <th>Firm Affiliation</th>
            <th>City</th>
            <th>Address</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sellers as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->name }}</strong></td>
            <td>{{ $s->mobile ?: '—' }}</td>
            <td>{{ $s->email ?: '—' }}</td>
            <td>{{ $s->firm->firm_name ?? 'Delawala Infra Co.' }}</td>
            <td>{{ $s->city ?: 'Dahegam' }}</td>
            <td>{{ $s->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($s->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No land seller records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>