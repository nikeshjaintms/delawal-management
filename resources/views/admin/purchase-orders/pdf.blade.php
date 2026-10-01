<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders Report - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => isset($purchaseOrder) ? 'portrait' : 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => isset($purchaseOrder) ? ('PO #' . $purchaseOrder->po_number) : 'Purchase Orders Report',
    'orientation' => isset($purchaseOrder) ? 'portrait' : 'landscape',
    'backUrl' => route('purchase-orders.index')
])

@if(isset($purchaseOrder))
    @include('admin.components.pdf-header', [
        'title' => 'PURCHASE ORDER',
        'subtitle' => 'Vendor Procurement Contract',
        'firm' => $purchaseOrder->firm ?? null,
        'docRef' => $purchaseOrder->po_number
    ])

    <div class="grid-2">
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-truck"></i> Vendor Particulars</div>
            <div class="info-row"><span class="info-label">Vendor Name:</span><span class="info-val">{{ $purchaseOrder->vendor->name ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">Mobile:</span><span class="info-val">{{ $purchaseOrder->vendor->mobile ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">GSTIN:</span><span class="info-val">{{ $purchaseOrder->vendor->gst_no ?? 'N/A' }}</span></div>
        </div>
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-file-lines"></i> Order Particulars</div>
            <div class="info-row"><span class="info-label">PO Date:</span><span class="info-val">{{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d M Y') : '—' }}</span></div>
            <div class="info-row"><span class="info-label">Project:</span><span class="info-val">{{ $purchaseOrder->project->project_name ?? 'General' }}</span></div>
            <div class="info-row"><span class="info-label">Status:</span><span class="info-val"><span class="badge badge-success">{{ ucfirst($purchaseOrder->status) }}</span></span></div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Ordered Materials</div>
    <table>
        <thead>
            <tr>
                <th style="width:28px;" class="c">#</th>
                <th>Material Description</th>
                <th class="r">Quantity</th>
                <th class="r">Rate (₹)</th>
                <th class="r">Line Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items ?? [] as $idx => $it)
            <tr>
                <td class="c">{{ $idx + 1 }}</td>
                <td><strong>{{ $it->material->material_name ?? 'Item' }}</strong></td>
                <td class="r">{{ number_format($it->qty ?? 0, 2) }} {{ $it->material->unit ?? '' }}</td>
                <td class="r">{{ number_format($it->rate ?? 0, 2) }}</td>
                <td class="r" style="font-weight:700;">₹{{ number_format($it->line_total ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#0F172A; color:#FFF;">
                <td colspan="4" class="r" style="color:#FFF;">Grand Total</td>
                <td class="r" style="color:#FFF; font-weight:800;">₹{{ number_format($purchaseOrder->grand_total ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
@else
    @include('admin.components.pdf-header', [
        'title' => 'Purchase Orders Report',
        'subtitle' => 'Vendor Orders & Procurement Registry'
    ])

    <div class="stat-row">
        <div class="stat-box s-blue">
            <div class="s-label">Total Purchase Orders</div>
            <div class="s-value">{{ $totalOrders ?? ($purchaseOrders ? $purchaseOrders->count() : 0) }}</div>
        </div>
        <div class="stat-box s-green">
            <div class="s-label">Total Procurement Value</div>
            <div class="s-value">₹{{ number_format($totalAmount ?? ($purchaseOrders ? $purchaseOrders->sum('grand_total') : 0), 2) }}</div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-truck"></i> Purchase Orders Ledger</div>
    <table>
        <thead>
            <tr>
                <th style="width:28px;" class="c">#</th>
                <th>PO Number</th>
                <th>Vendor / Supplier</th>
                <th>Project</th>
                <th>PO Date</th>
                <th>Delivery Date</th>
                <th class="r">Grand Total</th>
                <th class="c">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrders ?? [] as $i => $po)
            <tr>
                <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
                <td><strong>{{ $po->po_number }}</strong></td>
                <td>{{ $po->vendor->name ?? '—' }}</td>
                <td>{{ $po->project->project_name ?? '—' }}</td>
                <td>{{ $po->po_date ? $po->po_date->format('d M Y') : '—' }}</td>
                <td>{{ $po->delivery_date ? $po->delivery_date->format('d M Y') : '—' }}</td>
                <td class="r" style="font-weight:700;">₹{{ number_format($po->grand_total ?? 0, 2) }}</td>
                <td class="c"><span class="badge badge-success">{{ ucfirst($po->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" class="c" style="padding:20px;color:#64748B;">No purchase orders found.</td></tr>
            @endforelse
        </tbody>
    </table>
@endif

@include('admin.components.pdf-footer')
</body>
</html>