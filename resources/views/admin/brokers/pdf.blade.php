<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brokers & Channel Partners Master Registry - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Brokers & Channel Partners Master Registry',
    'orientation' => 'landscape',
    'backUrl' => route('brokers.index')
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
    'title' => 'Brokers & Channel Partners Master Registry',
    'subtitle' => 'Real Estate Agents, Sourcing Partners & Commission Directory',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Channel Partners</div>
        <div class="s-value">{{ $totalBrokers ?? $brokers->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Channel Partners</div>
        <div class="s-value">{{ $activeBrokers ?? $brokers->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-user-tie"></i> Brokers &amp; Channel Partners Directory</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Broker Full Name</th>
            <th style="width:100px;">Mobile Number</th>
            <th>Email</th>
            <th>Firm Affiliation</th>
            <th class="r" style="width:110px;">Commission Rate</th>
            <th>City / Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brokers as $i => $b)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $b->name }}</strong></td>
            <td>{{ $b->mobile ?: '—' }}</td>
            <td>{{ $b->email ?: '—' }}</td>
            <td>{{ $b->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td class="r" style="font-weight:700; color:#1E3A8A;">{{ $b->commission_rate ? ($b->commission_rate . '%') : 'Standard %' }}</td>
            <td>{{ $b->city ?: ($b->address ?: 'Dahegam') }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($b->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:24px; color:#64748B;"><em>No broker records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="7" class="r"><strong>TOTAL REGISTERED BROKER PARTNERS:</strong></td>
            <td class="c"><strong>{{ $brokers->count() }}</strong></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>