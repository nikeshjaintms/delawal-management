<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Accounts & Billing Ledger - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Contractor Accounts & Billing Ledger',
    'orientation' => 'landscape',
    'backUrl' => route('contractors.index')
])

@php
    $filters = [];
    if(request('project_id')) {
        $p = \App\Models\Project::find(request('project_id'));
        if($p) $filters['Site / Project'] = $p->project_name ?? $p->name;
    }
    if(request('status')) {
        $filters['Status'] = ucfirst(request('status'));
    }
    if(request('search')) {
        $filters['Search Keyword'] = request('search');
    }

    $totContract = 0;
    $totPaid = 0;
    $totDue = 0;

    foreach($contractors as $c) {
        $totContract += (float)($c->contract_amount ?? 0);
        $totPaid += (float)($c->paid_amount ?? 0);
        $totDue += (float)($c->due_amount ?? max(0, ($c->contract_amount ?? 0) - ($c->paid_amount ?? 0)));
    }
@endphp

@include('admin.components.pdf-header', [
    'title' => 'Contractor Accounts, Work Contracts & Outstanding Payables Ledger',
    'subtitle' => 'Civil, Structural & Site Contractor Commitment, Disbursed Payments & Sundry Creditor Balance',
    'filters' => $filters
])

<div class="stat-row">
    <div class="stat-box s-blue">
        <div class="s-label">Total Contractors</div>
        <div class="s-value">{{ $totalContractors ?? $contractors->count() }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Total Contract Commitments</div>
        <div class="s-value">{{ formatIndianCurrency($totContract) }}</div>
    </div>
    <div class="stat-box s-green">
        <div class="s-label">Settled / Paid Amount</div>
        <div class="s-value">{{ formatIndianCurrency($totPaid) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Outstanding Payable Balance</div>
        <div class="s-value">{{ formatIndianCurrency($totDue) }}</div>
    </div>
</div>

<div class="section-label"><i class="fa-solid fa-helmet-safety"></i> Contractor Work Contracts &amp; Financial Ledger</div>
<table>
    <thead>
        <tr>
            <th style="width:24px;" class="c">#</th>
            <th>Contractor Name &amp; Contact</th>
            <th>Work Type / Trade</th>
            <th>Assigned Project / Site</th>
            <th>PAN / Bank Details</th>
            <th class="r" style="width:105px;">Contract Value (₹)</th>
            <th class="r" style="width:100px;">Paid to Date (₹)</th>
            <th class="r" style="width:105px;">Balance Due (₹)</th>
            <th class="c" style="width:75px;">Pay Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($contractors as $i => $c)
        @php
            $cAmount = (float)($c->contract_amount ?? 0);
            $pAmount = (float)($c->paid_amount ?? 0);
            $dAmount = (float)($c->due_amount ?? max(0, $cAmount - $pAmount));
            $pStatus = strtolower($c->payment_status ?: ($dAmount <= 0 && $cAmount > 0 ? 'paid' : ($pAmount > 0 ? 'partial' : 'unpaid')));
        @endphp
        <tr>
            <td class="c" style="color:#64748B;">{{ $i+1 }}</td>
            <td>
                <strong>{{ $c->contractor_name ?: ($c->name ?? 'Contractor') }}</strong>
                @if(!empty($c->mobile))
                    <div style="font-size:8px; color:#64748B;"><i class="fa-solid fa-phone"></i> {{ $c->mobile }}</div>
                @endif
            </td>
            <td>{{ $c->work_type ?: ($c->specialization ?: 'Civil Works') }}</td>
            <td>{{ $c->project->project_name ?? ($c->project->name ?? 'Head Office / Multi-Site') }}</td>
            <td style="font-size:8px; color:#475569;">
                @if(!empty($c->pan_no)) <div>PAN: <strong>{{ $c->pan_no }}</strong></div> @endif
                @if(!empty($c->bank_name)) <div>{{ $c->bank_name }} (A/C: {{ substr($c->account_number ?? '', -4) ? '...'.substr($c->account_number, -4) : '—' }})</div> @endif
            </td>
            <td class="r" style="font-weight:600; color:#1E3A8A;">{{ formatIndianCurrency($cAmount, 2, false) }}</td>
            <td class="r" style="font-weight:600; color:#059669;">{{ formatIndianCurrency($pAmount, 2, false) }}</td>
            <td class="r" style="font-weight:700; color:{{ $dAmount > 0 ? '#DC2626' : '#10B981' }};">{{ formatIndianCurrency($dAmount, 2, false) }}</td>
            <td class="c">
                @if($pStatus === 'paid')
                    <span class="badge badge-success">Settled</span>
                @elseif($pStatus === 'partial')
                    <span class="badge badge-warning">Partial</span>
                @else
                    <span class="badge badge-danger">Unpaid</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="c" style="padding:24px; color:#64748B;">
                <em>No contractor account records found matching the specified filter parameters.</em>
            </td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="double-total">
            <td colspan="5" class="r"><strong>GRAND TOTAL CONTRACTOR ACCOUNTS:</strong></td>
            <td class="r" style="color:#1E3A8A; font-size:11px;"><strong>{{ formatIndianCurrency($totContract) }}</strong></td>
            <td class="r" style="color:#059669; font-size:11px;"><strong>{{ formatIndianCurrency($totPaid) }}</strong></td>
            <td class="r" style="color:#DC2626; font-size:11px;"><strong>{{ formatIndianCurrency($totDue) }}</strong></td>
            <td class="c"></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top:12px; font-size:9px; color:#64748B; line-height:1.4;">
    <strong>Accounting Ledger Note:</strong> Outstanding balance due to contractors constitutes Trade Creditors / Sundry Payables in the Balance Sheet under Current Liabilities.
</div>

@include('admin.components.pdf-footer')
</body>
</html>