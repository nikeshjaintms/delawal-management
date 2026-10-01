<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendors & Suppliers Directory - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Vendors & Suppliers Directory',
    'orientation' => 'landscape',
    'backUrl' => route('vendors.index')
])

@include('admin.components.pdf-header', [
    'title' => 'Vendors & Suppliers Directory',
    'subtitle' => 'Procurement & Material Vendor Ledger'
])

<div class="stat-row">
    <div class="stat-box">
        <div class="s-label">Total Vendors</div>
        <div class="s-value">{{ $totalVendors ?? $vendors->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Suppliers</div>
        <div class="s-value">{{ $activeVendors ?? $vendors->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-truck"></i> Vendors &amp; Material Suppliers</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Vendor Name</th>
            <th>Mobile Number</th>
            <th>GSTIN Number</th>
            <th>Email</th>
            <th>City</th>
            <th>Address</th>
            <th class="c">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vendors as $i => $v)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $v->name }}</strong></td>
            <td>{{ $v->mobile ?: '—' }}</td>
            <td><span class="badge badge-info">{{ $v->gst_no ?: 'Unregistered' }}</span></td>
            <td>{{ $v->email ?: '—' }}</td>
            <td>{{ $v->city ?: 'Dahegam' }}</td>
            <td>{{ $v->address ?: '—' }}</td>
            <td class="c"><span class="badge badge-success">{{ ucfirst($v->status ?? 'Active') }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No vendor records found.</td></tr>
        @endforelse
    </tbody>
</table>

@include('admin.components.pdf-footer')
</body>
</html>