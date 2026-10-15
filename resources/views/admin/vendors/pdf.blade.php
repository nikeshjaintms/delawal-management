<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendors & Suppliers Master Directory - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Vendors & Suppliers Master Directory',
    'orientation' => 'landscape',
    'backUrl' => route('vendors.index')
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
    'title' => 'Vendors & Suppliers Master Directory',
    'subtitle' => 'Approved Material Vendors, Trade Creditors & Service Contractors',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Registered Vendors</div>
        <div class="s-value">{{ $totalVendors ?? $vendors->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Approved Vendors</div>
        <div class="s-value">{{ $activeVendors ?? $vendors->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-truck"></i> Registered Suppliers &amp; Vendors</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Vendor / Supplier Name</th>
            <th style="width:100px;">Mobile Number</th>
            <th style="width:120px;">GSTIN Number</th>
            <th>Email</th>
            <th>City / State</th>
            <th>Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendors as $i => $v)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $v->name }}</strong></td>
            <td>{{ $v->mobile ?: '—' }}</td>
            <td><strong style="color:#1E3A8A;">{{ $v->gst_no ?: 'Unregistered' }}</strong></td>
            <td>{{ $v->email ?: '—' }}</td>
            <td>{{ $v->city ?: 'Dahegam' }}</td>
            <td>{{ $v->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($v->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:24px; color:#64748B;"><em>No vendor records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="7" class="r"><strong>TOTAL ACTIVE VENDOR DIRECTORY:</strong></td>
            <td class="c"><strong>{{ $vendors->count() }}</strong></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>