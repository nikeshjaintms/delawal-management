<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agriculture & Farm Financial Statement - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Agriculture Financial Report',
    'orientation' => 'landscape',
    'backUrl' => route('agriculture.reports.index')
])

@php
    $rType = $reportType ?? 'profit_loss';
    $title = match($rType) {
        'income'      => 'Agriculture Crop Sales & Produce Income Report',
        'expense'     => 'Agriculture Farm Input & Operational Expenses Report',
        'labour'      => 'Agriculture Labour Wage & Attendance Statement',
        'farm'        => 'Farm-Wise Agricultural Costing & Harvest Summary',
        default       => 'Agricultural Trading, Harvest & Net Profit Statement',
    };
@endphp

@include('admin.components.pdf-header', [
    'title' => $title,
    'subtitle' => 'Farm Operations, Crop Yields, Labour Outlays & Agro-Financial Ledger'
])

{{-- ── 1. PROFIT & LOSS TYPE ── --}}
@if($rType === 'profit_loss')
    @php
        $totInc = (float)($reportData['totalIncome'] ?? 0);
        $totExp = (float)($reportData['totalExpense'] ?? 0);
        $labExp = (float)($reportData['labourExpense'] ?? 0);
        $netP   = (float)($reportData['netProfit'] ?? ($totInc - $totExp));
    @endphp
    <div class="stat-row">
        <div class="stat-box s-green">
            <div class="s-label">Total Crop Sales &amp; Incomes</div>
            <div class="s-value">{{ formatIndianCurrency($totInc) }}</div>
        </div>
        <div class="stat-box s-red">
            <div class="s-label">Total Farm Inputs &amp; Expenses</div>
            <div class="s-value">{{ formatIndianCurrency($totExp) }}</div>
        </div>
        <div class="stat-box s-amber">
            <div class="s-label">Labour &amp; Wage Costs</div>
            <div class="s-value">{{ formatIndianCurrency($labExp) }}</div>
        </div>
        <div class="stat-box {{ $netP >= 0 ? 's-blue' : 's-red' }}">
            <div class="s-label">Net Agricultural Surplus / (Deficit)</div>
            <div class="s-value">{{ formatIndianCurrency($netP) }}</div>
        </div>
    </div>

    {{-- Category & Income Stream Tables --}}
    <div style="display:flex; gap:14px; margin-top:10px;">
        {{-- Income Breakdown --}}
        <div style="flex:1;">
            <div class="section-label"><i class="fa-solid fa-seedling" style="color:#000000;"></i> Crop &amp; Produce Income Streams</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:28px;" class="c">#</th>
                        <th>Crop / Produce Head</th>
                        <th class="r">Total Realized (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $iIdx = 1; @endphp
                    @forelse($reportData['incomeBreakdown'] ?? [] as $iType => $iAmt)
                    <tr>
                        <td class="c">{{ $iIdx++ }}</td>
                        <td><strong>{{ $iType ?: 'Agricultural Produce' }}</strong></td>
                        <td class="r" style="color:#000000; font-weight:700;">{{ formatIndianCurrency($iAmt) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="c" style="padding:14px; color:#444444;">No crop income recorded.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="double-total">
                        <td colspan="2" class="r">TOTAL CROP REVENUE:</td>
                        <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($totInc) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Expense Breakdown --}}
        <div style="flex:1;">
            <div class="section-label"><i class="fa-solid fa-wheat-awn-circle-exclamation" style="color:#000000;"></i> Farm Input &amp; Labour Outlays</div>
            <table>
                <thead>
                    <tr>
                        <th style="width:28px;" class="c">#</th>
                        <th>Farming Expense Head</th>
                        <th class="r">Total Outlay (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $eIdx = 1; @endphp
                    @forelse($reportData['categoryBreakdown'] ?? [] as $eCat => $eAmt)
                    <tr>
                        <td class="c">{{ $eIdx++ }}</td>
                        <td><strong>{{ $eCat ?: 'General Input' }}</strong></td>
                        <td class="r" style="color:#000000; font-weight:700;">{{ formatIndianCurrency($eAmt) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="c" style="padding:14px; color:#444444;">No farm expenses recorded.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="double-total">
                        <td colspan="2" class="r">TOTAL FARM EXPENSES:</td>
                        <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($totExp) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

{{-- ── 2. CROP INCOME TYPE ── --}}
@elseif($rType === 'income')
    <div class="stat-row">
        <div class="stat-box">
            <div class="s-label">Total Crop Sales Billed</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['totalAmount'] ?? 0) }}</div>
        </div>
        <div class="stat-box">
            <div class="s-label">Payment Received</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['totalReceived'] ?? 0) }}</div>
        </div>
        <div class="stat-box">
            <div class="s-label">Pending Recovery</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['totalPending'] ?? 0) }}</div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-wheat-awn" style="color:#000000;"></i> Crop Sales &amp; Agricultural Incomes Register</div>
    <table>
        <thead>
            <tr>
                <th style="width:24px;" class="c">#</th>
                <th style="width:70px;">Date</th>
                <th>Crop / Produce Details</th>
                <th>Farm / Land Parcel</th>
                <th>Buyer / Trader</th>
                <th class="r" style="width:105px;">Billed Amount (₹)</th>
                <th class="r" style="width:105px;">Received (₹)</th>
                <th class="r" style="width:105px;">Pending (₹)</th>
                <th class="c" style="width:70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['incomes'] ?? [] as $i => $inc)
            <tr>
                <td class="c" style="color:#000000;">{{ $i+1 }}</td>
                <td style="white-space:nowrap;">{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d M Y') : '—' }}</td>
                <td>
                    <strong>{{ $inc->crop_name ?? ($inc->income_type ?? 'Agricultural Income') }}</strong>
                    @if(!empty($inc->quantity))
                        <div style="font-size:7.5px; color:#444444;">Qty: {{ $inc->quantity }} {{ $inc->unit ?? 'Kgs' }}</div>
                    @endif
                </td>
                <td>{{ $inc->farm->farm_name ?? '—' }}</td>
                <td>{{ $inc->customer->name ?? ($inc->buyer_name ?? '—') }}</td>
                <td class="r" style="font-weight:700; color:#000000;">{{ formatIndianCurrency($inc->total_amount ?? 0) }}</td>
                <td class="r" style="color:#000000; font-weight:700;">{{ formatIndianCurrency($inc->payment_received ?? 0) }}</td>
                <td class="r" style="color:#000000; font-weight:700;">
                    {{ ($inc->pending_amount ?? 0) > 0 ? formatIndianCurrency($inc->pending_amount) : '—' }}
                </td>
                <td class="c">
                    <span class="badge {{ ($inc->pending_amount ?? 0) <= 0 ? 'badge-success' : 'badge-warning' }}">
                        {{ ($inc->pending_amount ?? 0) <= 0 ? 'Paid' : 'Due' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="c" style="padding:24px; color:#444444;">No crop income records found.</td></tr>
            @endforelse
        </tbody>
        @if(isset($reportData['incomes']) && $reportData['incomes']->count() > 0)
        <tfoot>
            <tr class="double-total">
                <td colspan="5" class="r">CROP INCOME TOTALS:</td>
                <td class="r" style="font-size:10px; color:#000000;">{{ formatIndianCurrency($reportData['totalAmount'] ?? 0) }}</td>
                <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($reportData['totalReceived'] ?? 0) }}</td>
                <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($reportData['totalPending'] ?? 0) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

{{-- ── 3. EXPENSE TYPE ── --}}
@elseif($rType === 'expense')
    <div class="stat-row">
        <div class="stat-box">
            <div class="s-label">Total Farming Outlay</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['totalAmount'] ?? 0) }}</div>
        </div>
        <div class="stat-box">
            <div class="s-label">Settled / Paid Amount</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['paidAmount'] ?? 0) }}</div>
        </div>
        <div class="stat-box">
            <div class="s-label">Pending / Unpaid Bills</div>
            <div class="s-value">{{ formatIndianCurrency($reportData['pendingAmount'] ?? 0) }}</div>
        </div>
    </div>

    <div class="section-label"><i class="fa-solid fa-tractor" style="color:#000000;"></i> Agricultural Input &amp; Site Expenditure Ledger</div>
    <table>
        <thead>
            <tr>
                <th style="width:24px;" class="c">#</th>
                <th style="width:70px;">Date</th>
                <th>Expense Head / Purpose</th>
                <th>Category</th>
                <th>Farm / Parcel</th>
                <th>Supplier / Labour</th>
                <th class="c" style="width:65px;">Mode</th>
                <th class="r" style="width:115px;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData['expenses'] ?? [] as $i => $exp)
            <tr>
                <td class="c" style="color:#000000;">{{ $i+1 }}</td>
                <td style="white-space:nowrap;">{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d M Y') : '—' }}</td>
                <td><strong>{{ $exp->title ?? 'Farm Input' }}</strong></td>
                <td>{{ $exp->category ?: 'General' }}</td>
                <td>{{ $exp->farm->farm_name ?? '—' }}</td>
                <td>{{ $exp->vendor->name ?? ($exp->labour->name ?? ($exp->paid_to ?? '—')) }}</td>
                <td class="c"><span class="badge">{{ ucfirst($exp->payment_mode ?: 'Cash') }}</span></td>
                <td class="r" style="font-weight:700; color:#000000;">{{ formatIndianCurrency($exp->amount ?? 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="c" style="padding:24px; color:#444444;">No agriculture expense vouchers recorded.</td></tr>
            @endforelse
        </tbody>
        @if(isset($reportData['expenses']) && $reportData['expenses']->count() > 0)
        <tfoot>
            <tr class="double-total">
                <td colspan="7" class="r">TOTAL FARM EXPENDITURE:</td>
                <td class="r" style="color:#000000; font-size:10px;">{{ formatIndianCurrency($reportData['totalAmount'] ?? 0) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
@endif

@php $hasCustomAuthBlock = true; @endphp
@include('admin.components.pdf-signature', [
    'leftRole' => 'Farm Supervisor / In-Charge',
    'rightRole' => 'Agri Business Head / Managing Partner'
])

@include('admin.components.pdf-footer', ['note' => 'Agriculture Operations Statement • Compliant with Delawala Farm Accounting Systems'])
</body>
</html>