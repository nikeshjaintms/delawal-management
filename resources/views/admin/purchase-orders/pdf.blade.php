<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders & Procurement Ledger - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => isset($purchaseOrder) ? 'portrait' : 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => isset($purchaseOrder) ? ('PO #' . $purchaseOrder->po_number) : 'Purchase Orders & Procurement Ledger',
    'orientation' => isset($purchaseOrder) ? 'portrait' : 'landscape',
    'backUrl' => route('purchase-orders.index')
])

@if(isset($purchaseOrder))
    @include('admin.components.pdf-header', [
        'title' => 'PURCHASE ORDER VOUCHER',
        'subtitle' => 'Vendor Procurement Contract & Supply Authorization',
        'firm' => $purchaseOrder->firm ?? null,
        'docRef' => $purchaseOrder->po_number
    ])

    <div class="grid-2">
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-truck"></i> Vendor / Supplier Particulars</div>
            <div class="info-row"><span class="info-label">Vendor Name:</span><span class="info-val"><strong>{{ $purchaseOrder->vendor->name ?? '—' }}</strong></span></div>
            <div class="info-row"><span class="info-label">Contact / Mobile:</span><span class="info-val">{{ $purchaseOrder->vendor->mobile ?? '—' }}</span></div>
            <div class="info-row"><span class="info-label">GSTIN:</span><span class="info-val">{{ $purchaseOrder->vendor->gst_no ?? 'Unregistered' }}</span></div>
            <div class="info-row"><span class="info-label">Address:</span><span class="info-val">{{ $purchaseOrder->vendor->address ?? '—' }}</span></div>
        </div>
        <div class="grid-col">
            <div class="col-heading"><i class="fa-solid fa-file-lines"></i> Order Particulars & Terms</div>
            <div class="info-row"><span class="info-label">PO Reference:</span><span class="info-val" style="color:#1E3A8A; font-weight:700;">{{ $purchaseOrder->po_number }}</span></div>
            <div class="info-row"><span class="info-label">PO Date:</span><span class="info-val">{{ $purchaseOrder->po_date ? $purchaseOrder->po_date->format('d M Y') : '—' }}</span></div>
            <div class="info-row"><span class="info-label">Target Site:</span><span class="info-val">{{ $purchaseOrder->project->project_name ?? 'Head Office / Central Store' }}</span></div>
            <div class="info-row"><span class="info-label">Delivery Date:</span><span class="info-val">{{ $purchaseOrder->delivery_date ? $purchaseOrder->delivery_date->format('d M Y') : 'Immediate' }}</span></div>
            <div class="info-row"><span class="info-label">Order Status:</span><span class="info-val"><span class="badge badge-success">{{ ucfirst($purchaseOrder->status) }}</span></span></div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-boxes-stacked"></i> Ordered Materials Schedule</div>
    <table>
        <thead>
            <tr>
                <th style="width:28px;" class="c">#</th>
                <th>Material Description</th>
                <th class="r" style="width:110px;">Quantity</th>
                <th class="r" style="width:110px;">Rate (₹)</th>
                <th class="r" style="width:130px;">Line Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items ?? [] as $idx => $it)
            <tr>
                <td class="c" style="color:#64748B;">{{ $idx + 1 }}</td>
                <td><strong>{{ $it->material->material_name ?? 'Item' }}</strong></td>
                <td class="r">{{ number_format($it->qty ?? 0, 2) }} {{ $it->material->unit ?? 'Units' }}</td>
                <td class="r">{{ formatIndianCurrency($it->rate ?? 0, 2, false) }}</td>
                <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($it->line_total ?? 0) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="double-total">
                <td colspan="4" class="r"><strong>GRAND TOTAL PURCHASE VALUE:</strong></td>
                <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($purchaseOrder->grand_total ?? 0) }}</strong></td>
            </tr>
        </tfoot>
    </table>
@else
    @php
        $poList = $purchaseOrders ?? collect([]);
        $totPOAmount = $totalAmount ?? $poList->sum('grand_total');
        $filters = [];
        if(request('from_date') || request('to_date')) {
            $filters['Period'] = (request('from_date') ? \Carbon\Carbon::parse(request('from_date'))->format('d M Y') : 'Start') . ' to ' . (request('to_date') ? \Carbon\Carbon::parse(request('to_date'))->format('d M Y') : 'Present');
        }
        if(request('project_id')) {
            $p = \App\Models\Project::find(request('project_id'));
            if($p) $filters['Site / Project'] = $p->project_name ?? $p->name;
        }
        if(request('vendor_id')) {
            $v = \App\Models\Vendor::find(request('vendor_id'));
            if($v) $filters['Vendor'] = $v->name;
        }
        if(request('status')) {
            $filters['Status'] = ucfirst(request('status'));
        }
    @endphp

    @include('admin.components.pdf-header', [
        'title' => 'Purchase Orders Summary & Procurement Register',
        'subtitle' => 'Material Procurement Orders, Supplier Commitments & Site Deliveries',
        'filters' => $filters
    ])

    <div class="stat-row">
        <div class="stat-box s-blue">
            <div class="s-label">Total Purchase Orders</div>
            <div class="s-value">{{ $totalOrders ?? $poList->count() }}</div>
        </div>
        <div class="stat-box s-gold">
            <div class="s-label">Total Procurement Value</div>
            <div class="s-value">{{ formatIndianCurrency($totPOAmount) }}</div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-truck"></i> Purchase Orders Register</div>
    <table>
        <thead>
            <tr>
                <th style="width:24px;" class="c">#</th>
                <th style="width:85px;">PO Number</th>
                <th>Vendor / Supplier</th>
                <th>Target Project / Site</th>
                <th style="width:75px;">PO Date</th>
                <th style="width:75px;">Delivery Due</th>
                <th class="r" style="width:110px;">Order Total (₹)</th>
                <th class="c" style="width:75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($poList as $i => $po)
            <tr>
                <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
                <td style="font-weight:700; color:#1E3A8A;">{{ $po->po_number }}</td>
                <td><strong>{{ $po->vendor->name ?? '—' }}</strong></td>
                <td>{{ $po->project->project_name ?? 'Head Office / Store' }}</td>
                <td style="white-space:nowrap; color:#475569;">{{ $po->po_date ? $po->po_date->format('d-M-Y') : '—' }}</td>
                <td style="white-space:nowrap; color:#475569;">{{ $po->delivery_date ? $po->delivery_date->format('d-M-Y') : '—' }}</td>
                <td class="r" style="font-weight:700; color:#0F172A;">{{ formatIndianCurrency($po->grand_total ?? 0, 2, false) }}</td>
                <td class="c">
                    @if(strtolower($po->status) === 'completed' || strtolower($po->status) === 'received')
                        <span class="badge badge-success">{{ ucfirst($po->status) }}</span>
                    @elseif(strtolower($po->status) === 'pending')
                        <span class="badge badge-warning">Pending</span>
                    @else
                        <span class="badge badge-neutral">{{ ucfirst($po->status) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="c" style="padding:24px; color:#64748B;"><em>No purchase orders found matching the filter criteria.</em></td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="double-total">
                <td colspan="6" class="r"><strong>GRAND TOTAL PURCHASE COMMITMENTS:</strong></td>
                <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totPOAmount) }}</strong></td>
                <td class="c"></td>
            </tr>
        </tfoot>
    </table>
@endif

@include('admin.components.pdf-footer')
</body>
</html>