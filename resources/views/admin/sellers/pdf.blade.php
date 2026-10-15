<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Sellers Master Registry - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Land Sellers Master Registry',
    'orientation' => 'landscape',
    'backUrl' => route('sellers.index')
])

@php
    $filters = [];
    if(request('search')) {
        $filters['Search Keyword'] = request('search');
    }
    if(request('status')) {
        $filters['Status'] = ucfirst(request('status'));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Land Sellers Master Registry & Property Sourcing Directory',
    'subtitle' => 'Land Owners, Strategic Property Sellers & Acquisition Partners',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Land Sellers</div>
        <div class="s-value">{{ $totalSellers ?? $sellers->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Seller Accounts</div>
        <div class="s-value">{{ $activeSellers ?? $sellers->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Land Sellers Master Registry</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Seller Full Name</th>
            <th style="width:100px;">Mobile Number</th>
            <th style="width:110px;">PAN Number</th>
            <th>Email Address</th>
            <th>Acquiring Firm Link</th>
            <th>City / State</th>
            <th>Registered Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sellers as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $s->name }}</strong></td>
            <td>{{ $s->mobile ?: ($s->phone ?: '—') }}</td>
            <td><strong style="color:#1E3A8A;">{{ $s->pan_no ?: '—' }}</strong></td>
            <td>{{ $s->email ?: '—' }}</td>
            <td>{{ $s->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td>{{ $s->city ?: 'Dahegam' }}</td>
            <td>{{ $s->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($s->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No land seller records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="8" class="r"><strong>TOTAL ACTIVE SELLER DIRECTORY:</strong></td>
            <td class="c"><strong>{{ $sellers->count() }}</strong></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>