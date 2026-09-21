<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agriculture Report - Delawala Management ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Outfit', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: #f8fafc; color: #1e293b; padding: 30px; font-size: 13px; line-height: 1.5; }
        .print-container { max-width: 1000px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        
        .report-header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0f172a; padding-bottom: 20px; margin-bottom: 24px; }
        .company-info h1 { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.5px; }
        .company-info p { font-size: 12px; color: #64748b; font-weight: 500; }
        .report-meta { text-align: right; }
        .report-meta h2 { font-size: 18px; font-weight: 800; color: #2563eb; text-transform: uppercase; margin-bottom: 4px; }
        .report-meta .period { font-size: 12px; color: #475569; font-weight: 600; }

        .kpi-strip { display: flex; gap: 15px; margin-bottom: 24px; background: #f1f5f9; padding: 15px 20px; border-radius: 8px; }
        .kpi-item { flex: 1; border-right: 1px solid #cbd5e1; padding-right: 15px; }
        .kpi-item:last-child { border-right: none; }
        .kpi-item span { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .kpi-item strong { font-size: 17px; font-weight: 800; color: #0f172a; }

        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; font-size: 12.5px; }
        .report-table th { background: #0f172a; color: #ffffff; text-align: left; padding: 10px 12px; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .report-table td { padding: 9px 12px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .report-table tbody tr:nth-child(even) { background: #f8fafc; }
        .report-table .num { text-align: right; }
        .report-table tfoot th { background: #e2e8f0; color: #0f172a; font-weight: 800; padding: 10px 12px; }

        .breakdown-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .breakdown-box { border: 1px solid #cbd5e1; border-radius: 8px; padding: 16px; background: #ffffff; }
        .breakdown-box h3 { font-size: 12px; font-weight: 800; color: #0f172a; text-transform: uppercase; margin-bottom: 12px; border-bottom: 1.5px solid #e2e8f0; padding-bottom: 6px; }
        .breakdown-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 12px; }

        .signature-section { display: flex; justify-content: space-between; margin-top: 50px; padding-top: 20px; }
        .sign-box { width: 220px; text-align: center; border-top: 1.5px solid #0f172a; padding-top: 8px; font-size: 12px; font-weight: 700; color: #0f172a; }

        .no-print-bar { position: fixed; top: 15px; right: 20px; display: flex; gap: 10px; z-index: 999; }
        .btn-action { background: #2563eb; color: #ffffff; padding: 10px 20px; border-radius: 8px; border: none; font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-action:hover { background: #1d4ed8; }

        @media print {
            body { padding: 0; background: #ffffff; }
            .print-container { box-shadow: none; padding: 0; max-width: 100%; }
            .no-print-bar { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <button onclick="window.print()" class="btn-action">🖨️ Print / Save PDF</button>
    <button onclick="window.close()" class="btn-action" style="background:#64748b;">Close</button>
</div>

<div class="print-container">
    <div class="report-header">
        <div class="company-info">
            <h1>DELAWALA MANAGEMENT ERP</h1>
            <p>Agriculture Business Division &bull; Land, Crop, Labour & Financial Operations</p>
        </div>
        <div class="report-meta">
            <h2>
                @if($reportType === 'profit_loss') Profit & Loss Statement
                @elseif($reportType === 'income') Crop Sales & Revenue Report
                @elseif($reportType === 'expense') Agriculture Expense Statement
                @elseif($reportType === 'labour') Labour Wages & Advance Ledger
                @elseif($reportType === 'farm') Farm-Wise Operational Summary
                @endif
            </h2>
            <div class="period">
                Period: 
                @if($fromDate && $toDate) {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                @elseif($fromDate) From {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}
                @elseif($toDate) Up to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                @else All Dates (Till {{ date('d M Y') }})
                @endif
            </div>
            <div style="font-size:11px;color:#94A3B8;margin-top:2px;">Generated on {{ date('d M Y, h:i A') }}</div>
        </div>
    </div>

    {{-- REPORT BODY BASED ON TYPE --}}
    @if($reportType === 'profit_loss')
        <div class="kpi-strip">
            <div class="kpi-item">
                <span>Total Revenue</span>
                <strong style="color:#16a34a;">₹{{ number_format($reportData['totalIncome'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Total Expenses</span>
                <strong style="color:#dc2626;">₹{{ number_format($reportData['totalExpense'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Labour Expenditure</span>
                <strong>₹{{ number_format($reportData['labourExpense'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Net Farm Profit / (Loss)</span>
                <strong style="color: {{ ($reportData['netProfit'] ?? 0) >= 0 ? '#16a34a' : '#dc2626' }};">
                    {{ ($reportData['netProfit'] ?? 0) >= 0 ? '+' : '' }}₹{{ number_format($reportData['netProfit'] ?? 0, 2) }}
                </strong>
            </div>
        </div>

        <div class="breakdown-grid">
            <div class="breakdown-box">
                <h3>Expense Breakdown by Category</h3>
                @forelse($reportData['categoryBreakdown'] ?? [] as $cat => $sum)
                    <div class="breakdown-row">
                        <span>{{ $cat }}</span>
                        <strong>₹{{ number_format($sum, 2) }}</strong>
                    </div>
                @empty
                    <div style="color:#94a3b8;font-size:12px;">No expenses logged.</div>
                @endforelse
            </div>

            <div class="breakdown-box">
                <h3>Revenue by Income Type</h3>
                @forelse($reportData['incomeBreakdown'] ?? [] as $iType => $sum)
                    <div class="breakdown-row">
                        <span>{{ $iType }}</span>
                        <strong>₹{{ number_format($sum, 2) }}</strong>
                    </div>
                @empty
                    <div style="color:#94a3b8;font-size:12px;">No income logged.</div>
                @endforelse
            </div>
        </div>

    @elseif($reportType === 'income')
        <div class="kpi-strip">
            <div class="kpi-item">
                <span>Total Billed Amount</span>
                <strong style="color:#16a34a;">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Payment Received</span>
                <strong style="color:#2563eb;">₹{{ number_format($reportData['totalReceived'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Pending Receivables</span>
                <strong style="color:#dc2626;">₹{{ number_format($reportData['totalPending'] ?? 0, 2) }}</strong>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Farm</th>
                    <th>Crop / Product</th>
                    <th>Buyer</th>
                    <th class="num">Quantity</th>
                    <th class="num">Rate (₹)</th>
                    <th class="num">Total (₹)</th>
                    <th class="num">Received (₹)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['incomes'] ?? [] as $i => $inc)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $inc->income_date ? \Carbon\Carbon::parse($inc->income_date)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $inc->farm?->farm_name ?: '—' }}</td>
                    <td><strong>{{ $inc->crop_product }}</strong></td>
                    <td>{{ $inc->customer?->name ?: ($inc->buyer_name ?: 'Direct') }}</td>
                    <td class="num">{{ number_format($inc->quantity, 2) }} {{ $inc->unit }}</td>
                    <td class="num">₹{{ number_format($inc->rate, 2) }}</td>
                    <td class="num"><strong>₹{{ number_format($inc->total_amount, 2) }}</strong></td>
                    <td class="num">₹{{ number_format($inc->payment_received, 2) }}</td>
                    <td>{{ $inc->payment_status }}</td>
                </tr>
                @empty
                <tr><td colspan="10" style="text-align:center;color:#94a3b8;">No income records found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">Total</th>
                    <th class="num">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalReceived'] ?? 0, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

    @elseif($reportType === 'expense')
        <div class="kpi-strip">
            <div class="kpi-item">
                <span>Total Expenditure</span>
                <strong style="color:#dc2626;">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Total Vouchers</span>
                <strong>{{ count($reportData['expenses'] ?? []) }}</strong>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Farm</th>
                    <th>Category</th>
                    <th>Type / Purpose</th>
                    <th>Paid To</th>
                    <th>Bill / Ref</th>
                    <th class="num">Amount (₹)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['expenses'] ?? [] as $i => $exp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $exp->expense_date ? \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') : '—' }}</td>
                    <td>{{ $exp->farm?->farm_name ?: '—' }}</td>
                    <td><strong>{{ $exp->category }}</strong></td>
                    <td>{{ $exp->expense_type }}</td>
                    <td>{{ $exp->vendor?->name ?: ($exp->contractor?->name ?: ($exp->labour?->name ?: ($exp->description ?: '—'))) }}</td>
                    <td>{{ $exp->bill_no ?: ($exp->invoice_no ?: '—') }}</td>
                    <td class="num"><strong>₹{{ number_format($exp->amount, 2) }}</strong></td>
                    <td>{{ $exp->payment_status }}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#94a3b8;">No expense records found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7">Grand Total</th>
                    <th class="num">₹{{ number_format($reportData['totalAmount'] ?? 0, 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

    @elseif($reportType === 'labour')
        <div class="kpi-strip">
            <div class="kpi-item">
                <span>Total Paid</span>
                <strong style="color:#16a34a;">₹{{ number_format($reportData['totalPaid'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Outstanding Advance Balances</span>
                <strong style="color:#dc2626;">₹{{ number_format($reportData['totalAdvanceBalance'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Pending Wages</span>
                <strong style="color:#d97706;">₹{{ number_format($reportData['totalPending'] ?? 0, 2) }}</strong>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Labour Worker</th>
                    <th>Type</th>
                    <th>Farm</th>
                    <th class="num">Earned (₹)</th>
                    <th class="num">Advance (₹)</th>
                    <th class="num">Total Paid (₹)</th>
                    <th class="num">Adv Balance (₹)</th>
                    <th class="num">Pending (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['labours'] ?? [] as $i => $lab)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $lab->name }}</strong></td>
                    <td>{{ $lab->labour_type }}</td>
                    <td>{{ $lab->farm?->farm_name ?: 'General' }}</td>
                    <td class="num">₹{{ number_format($lab->total_earned, 2) }}</td>
                    <td class="num">₹{{ number_format($lab->total_advance, 2) }}</td>
                    <td class="num"><strong style="color:#16a34a;">₹{{ number_format($lab->total_paid, 2) }}</strong></td>
                    <td class="num" style="color:#dc2626;">₹{{ number_format($lab->advance_balance, 2) }}</td>
                    <td class="num" style="color:#d97706;">₹{{ number_format($lab->pending_amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#94a3b8;">No labour records found.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4">Summary Totals</th>
                    <th class="num">₹{{ number_format($reportData['totalEarned'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalAdvance'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalPaid'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalAdvanceBalance'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalPending'] ?? 0, 2) }}</th>
                </tr>
            </tfoot>
        </table>

    @elseif($reportType === 'farm')
        <div class="kpi-strip">
            <div class="kpi-item">
                <span>Total Cultivated Area</span>
                <strong>{{ number_format($reportData['totalArea'] ?? 0, 2) }} Acres</strong>
            </div>
            <div class="kpi-item">
                <span>Total Farm Revenue</span>
                <strong style="color:#16a34a;">₹{{ number_format($reportData['totalIncome'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Total Farm Expenses</span>
                <strong style="color:#dc2626;">₹{{ number_format($reportData['totalExpense'] ?? 0, 2) }}</strong>
            </div>
            <div class="kpi-item">
                <span>Net Farm Profit / (Loss)</span>
                <strong style="color: {{ ($reportData['netProfit'] ?? 0) >= 0 ? '#16a34a' : '#dc2626' }};">
                    {{ ($reportData['netProfit'] ?? 0) >= 0 ? '+' : '' }}₹{{ number_format($reportData['netProfit'] ?? 0, 2) }}
                </strong>
            </div>
        </div>

        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Farm Name</th>
                    <th>Location / Village</th>
                    <th>Survey No</th>
                    <th>Land Area</th>
                    <th>Current Crop</th>
                    <th class="num">Income (₹)</th>
                    <th class="num">Expense (₹)</th>
                    <th class="num">Net Profit (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportData['farmSummaries'] ?? [] as $i => $fs)
                @php $f = $fs['farm']; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $f->farm_name }}</strong></td>
                    <td>{{ $f->village ?: '—' }}</td>
                    <td>{{ $f->survey_no ?: '—' }}</td>
                    <td>{{ $fs['land_area'] }}</td>
                    <td>{{ $fs['crop'] }}</td>
                    <td class="num" style="color:#16a34a;">₹{{ number_format($fs['total_income'], 2) }}</td>
                    <td class="num" style="color:#dc2626;">₹{{ number_format($fs['total_expense'], 2) }}</td>
                    <td class="num">
                        <strong style="color: {{ $fs['net_profit'] >= 0 ? '#16a34a' : '#dc2626' }};">
                            {{ $fs['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($fs['net_profit'], 2) }}
                        </strong>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:#94a3b8;">No farm summaries available.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6">Grand Totals</th>
                    <th class="num">₹{{ number_format($reportData['totalIncome'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['totalExpense'] ?? 0, 2) }}</th>
                    <th class="num">₹{{ number_format($reportData['netProfit'] ?? 0, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="signature-section">
        <div class="sign-box">Prepared By / Accountant</div>
        <div class="sign-box">Farm Manager / Supervisor</div>
        <div class="sign-box">Authorized Signatory</div>
    </div>
</div>

</body>
</html>
