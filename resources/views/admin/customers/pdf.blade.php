<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Master Registry - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Customer Master Registry',
    'orientation' => 'landscape',
    'backUrl' => route('customers.index')
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
    'title' => 'Customer Master Registry & Account Directory',
    'subtitle' => 'Enrolled Buyers, Allottees & Commercial Tenancy Contacts',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Registered Customers</div>
        <div class="s-value">{{ $totalCustomers ?? $customers->count() }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Active Customer Accounts</div>
        <div class="s-value">{{ $activeCustomers ?? $customers->where('status', 'active')->count() }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-users"></i> Enrolled Customer Directory</div>
<table>
    <thead>
        <tr>
            <th style="width:28px;" class="c">#</th>
            <th>Customer Name</th>
            <th style="width:95px;">Primary Mobile</th>
            <th style="width:95px;">Alternate Contact</th>
            <th>Email Address</th>
            <th>Firm Affiliation</th>
            <th>Category / Type</th>
            <th>City / Address</th>
            <th class="c" style="width:75px;">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($customers as $i => $item)
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td><strong>{{ $item->name }}</strong></td>
            <td>{{ $item->mobile ?: '—' }}</td>
            <td style="color:#64748B;">{{ $item->alternate_mobile ?: '—' }}</td>
            <td>{{ $item->email ?: '—' }}</td>
            <td>{{ $item->firm->firm_name ?? 'Delawala Management' }}</td>
            <td>{{ ucfirst($item->customer_type ?: 'Individual') }}</td>
            <td>{{ $item->city ?: ($item->address ?: '—') }}</td>
            <td class="c">
                @if($item->status === 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-neutral">{{ ucfirst($item->status ?: 'Inactive') }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="9" class="c" style="padding:24px; color:#64748B;"><em>No customer records found matching the filter parameters.</em></td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="8" class="r"><strong>TOTAL ACTIVE REGISTRATIONS:</strong></td>
            <td class="c"><strong>{{ $customers->count() }}</strong></td>
        </tr>
    </tfoot>
</table>

@include('admin.components.pdf-footer')
</body>
</html>
