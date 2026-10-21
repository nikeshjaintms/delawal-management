<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Sellers Master Registry - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
    <style>
        .seller-tfoot tr, .seller-tfoot td {
            background: #F8FAFC !important;
            border-top: 1.5px solid #000000 !important;
            border-bottom: 1.5px solid #000000 !important;
            vertical-align: middle !important;
        }
        .seller-tfoot td.tfoot-summary-left {
            border-right: none !important;
            padding: 7px 12px !important;
            text-align: left !important;
        }
        .seller-tfoot td.tfoot-summary-right {
            border-left: none !important;
            padding: 7px 12px !important;
            text-align: right !important;
        }
        .seller-count-pill {
            display: inline-block;
            min-width: 24px;
            text-align: center;
            padding: 2px 8px;
            background: #000000;
            color: #FFFFFF !important;
            border-radius: 3px;
            font-weight: 800;
            font-size: 9.5px;
            margin-left: 6px;
            line-height: 1.2;
        }
        @media print {
            .seller-count-pill {
                background: #000000 !important;
                color: #FFFFFF !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
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
    
    $totalCount = $sellers->count();
    $activeCount = $sellers->where('status', 'active')->count();
    $inactiveCount = $sellers->where('status', '!=', 'active')->count();
    $uniqueCities = $sellers->pluck('city')->filter()->unique()->count();
    $totalProperties = $sellers->sum(fn($s) => $s->propertyMasters ? $s->propertyMasters->count() : 0);
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Land Sellers Master Registry & Property Sourcing Directory',
    'subtitle' => 'Land Owners, Strategic Property Sellers & Acquisition Partners',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Land Sellers</div>
        <div class="s-value">{{ $totalCount }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Seller Accounts</div>
        <div class="s-value">{{ $activeCount }}</div>
    </div>
    <div class="stat-box {{ $inactiveCount > 0 ? 's-red' : 's-blue' }}">
        <div class="s-label">Inactive / Suspended</div>
        <div class="s-value">{{ $inactiveCount }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Sourcing Locations</div>
        <div class="s-value">{{ $uniqueCities > 0 ? $uniqueCities : 1 }} {{ Str::plural('City', $uniqueCities) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Linked Acquisitions</div>
        <div class="s-value">{{ $totalProperties }} {{ Str::plural('Deed', $totalProperties) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Land Sellers Master Registry</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th style="min-width:140px;">Seller Full Name</th>
            <th style="width:100px;">Mobile Number</th>
            <th style="width:95px;">PAN Number</th>
            <th style="min-width:125px;">Email Address</th>
            <th style="min-width:125px;">Acquiring Firm Link</th>
            <th style="width:90px;">City / State</th>
            <th>Registered Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sellers as $i => $s)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $s->name }}</strong>
                @if($s->propertyMasters && $s->propertyMasters->count() > 0)
                    <div style="font-size:7.5px; color:#475569; margin-top:1px;">
                        <i class="fa-solid fa-landmark" style="font-size:7px;"></i> {{ $s->propertyMasters->count() }} Property Link(s)
                    </div>
                @endif
            </td>
            <td>{{ $s->mobile ?: ($s->phone ?: '—') }}</td>
            <td><strong style="font-family:monospace; letter-spacing:0.4px;">{{ $s->pan_no ?: '—' }}</strong></td>
            <td>{{ $s->email ?: '—' }}</td>
            <td>{{ $s->firm->firm_name ?? 'Delawala Infrastructure' }}</td>
            <td>{{ $s->city ?: 'Dahegam' }}</td>
            <td>{{ $s->address ?: '—' }}</td>
            <td class="c">
                @if(strtolower($s->status ?? 'active') === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-danger">{{ ucfirst($s->status ?? 'Inactive') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No land seller records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot class="seller-tfoot">
        <tr>
            <td colspan="5" class="tfoot-summary-left">
                <span style="font-weight:800; font-size:8.5px; text-transform:uppercase; letter-spacing:0.4px; color:#000000;">
                    <i class="fa-solid fa-chart-pie" style="margin-right:4px;"></i> Directory Summary:
                </span>
                <span style="margin-left:8px; font-size:8.5px; color:#333333;">Total Registered: <strong style="color:#000000;">{{ $totalCount }}</strong></span>
                <span style="margin:0 6px; color:#94A3B8;">&bull;</span>
                <span style="font-size:8.5px; color:#333333;">Active Accounts: <strong style="color:#000000;">{{ $activeCount }}</strong></span>
                @if($inactiveCount > 0)
                    <span style="margin:0 6px; color:#94A3B8;">&bull;</span>
                    <span style="font-size:8.5px; color:#333333;">Inactive: <strong style="color:#000000;">{{ $inactiveCount }}</strong></span>
                @endif
            </td>
            <td colspan="4" class="tfoot-summary-right">
                <span style="font-weight:800; font-size:8.5px; text-transform:uppercase; letter-spacing:0.4px; color:#000000;">
                    TOTAL ACTIVE SELLER DIRECTORY:
                </span>
                <span class="seller-count-pill">
                    {{ $activeCount }}
                </span>
            </td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>