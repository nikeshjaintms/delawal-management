<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brokers Directory Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Brokers Directory Report',
    'orientation' => 'landscape',
    'backUrl' => route('brokers.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Brokers Directory Report',
    'subtitle' => 'Channel Partners & Commission Ledger'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Brokers</div>
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
            <th>Broker Name</th>
            <th>Mobile Number</th>
            <th>Email</th>
            <th>Firm Affiliation</th>
            <th>Commission Rate</th>
            <th>City</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brokers as $i => $b)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $b->name }}</strong></td>
            <td>{{ $b->mobile ?: '—' }}</td>
            <td>{{ $b->email ?: '—' }}</td>
            <td>{{ $b->firm->firm_name ?? 'Delawala Infra Co.' }}</td>
            <td>{{ $b->commission_rate ? ($b->commission_rate . '%') : 'Standard' }}</td>
            <td>{{ $b->city ?: 'Dahegam' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($b->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No brokers found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>