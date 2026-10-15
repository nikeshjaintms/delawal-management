<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Sheet (Statement of Financial Position) - Delawala Management</title>
    @include('admin.components.pdf-styles', ['orientation' => 'landscape'])
</head>
<body>
@include('admin.components.pdf-action-bar', [
    'title' => 'Balance Sheet Statement',
    'orientation' => 'landscape',
    'backUrl' => route('reports.balance-sheet')
])

@include('admin.components.pdf-header', [
    'title' => 'Balance Sheet (Statement of Financial Position)',
    'subtitle' => 'Assets, Liabilities & Net Capital Statement (Standard Accounting Model)'
])

{{-- ── KPI Balance Summary ── --}}
<div class="stat-row">
    <div class="stat-box s-green">
        <div class="s-label">Total Assets (A)</div>
        <div class="s-value">{{ formatIndianCurrency($totalAssets ?? 0) }}</div>
    </div>
    <div class="stat-box s-red">
        <div class="s-label">Total External Liabilities (B)</div>
        <div class="s-value">{{ formatIndianCurrency($totalLiabilities ?? 0) }}</div>
    </div>
    <div class="stat-box s-blue">
        <div class="s-label">Net Worth / Capital Equity (A − B)</div>
        <div class="s-value">{{ formatIndianCurrency($netWorth ?? 0) }}</div>
    </div>
    <div class="stat-box s-purple">
        <div class="s-label">Liquid Cash &amp; Bank</div>
        <div class="s-value">{{ formatIndianCurrency($totalCashAndBank ?? 0) }}</div>
    </div>
    <div class="stat-box s-gold">
        <div class="s-label">Sundry Debtors (Receivables)</div>
        <div class="s-value">{{ formatIndianCurrency($receivables ?? 0) }}</div>
    </div>
</div>

{{-- ── TWO-COLUMN ACCOUNTING T-FORMAT BALANCE SHEET ── --}}
<div style="display:flex; gap:14px; width:100%; box-sizing:border-box;">
    {{-- ── LEFT COLUMN: CAPITAL & LIABILITIES ── --}}
    <div style="flex:1; min-width:0;">
        <div class="section-label">
            <i class="fa-solid fa-scale-unbalanced" style="color:#DC2626;"></i> Capital &amp; Liabilities (Equities &amp; Dues)
        </div>
        <table>
            <thead>
                <tr>
                    <th>Capital &amp; Liability Particulars</th>
                    <th class="c" style="width:75px;">Schedule</th>
                    <th class="r" style="width:115px;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Group 1: Capital & Reserves --}}
                <tr style="background:#F8FAFC; font-weight:800;">
                    <td colspan="3"><i class="fa-solid fa-circle-dot" style="font-size:7px; color:#2563EB;"></i> I. CAPITAL &amp; RESERVES</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Proprietor's Capital / Partner's Equity</strong>
                        <div style="font-size:7.5px; color:#64748B;">Retained Earnings, Net Surplus &amp; Reserve Fund</div>
                    </td>
                    <td class="c"><span class="badge">Equity</span></td>
                    <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($netWorth ?? 0) }}</td>
                </tr>

                {{-- Group 2: Non-Current Liabilities (Loans) --}}
                <tr style="background:#F8FAFC; font-weight:800;">
                    <td colspan="3"><i class="fa-solid fa-circle-dot" style="font-size:7px; color:#D97706;"></i> II. SECURED &amp; BORROWED LOANS</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Bank &amp; Financial Institution Loans</strong>
                        <div style="font-size:7.5px; color:#64748B;">
                            Total Sanctioned: {{ formatIndianCurrency($loanTotal ?? 0) }} &bull; Repaid: {{ formatIndianCurrency($loanPaid ?? 0) }}
                        </div>
                    </td>
                    <td class="c"><span class="badge">Long-Term</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($loanOutstanding ?? 0) }}</td>
                </tr>

                {{-- Group 3: Current Liabilities --}}
                <tr style="background:#F8FAFC; font-weight:800;">
                    <td colspan="3"><i class="fa-solid fa-circle-dot" style="font-size:7px; color:#DC2626;"></i> III. CURRENT LIABILITIES &amp; PAYABLES</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Sundry Creditors / Unpaid Vouchers</strong>
                        <div style="font-size:7.5px; color:#64748B;">Pending Invoices, Site &amp; Vendor Payables</div>
                    </td>
                    <td class="c"><span class="badge">Creditors</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($unpaidExpenses ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Credit Notes / Customer Refund Dues</strong>
                        <div style="font-size:7.5px; color:#64748B;">Approved Claims &amp; Adjustments</div>
                    </td>
                    <td class="c"><span class="badge">Payables</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($creditNotePayable ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Tenant Security Deposits Held</strong>
                        <div style="font-size:7.5px; color:#64748B;">Refundable Leases Caution Money</div>
                    </td>
                    <td class="c"><span class="badge">Deposits</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($securityDeposits ?? 0) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="double-total">
                    <td colspan="2" class="r" style="font-size:9.5px;">TOTAL CAPITAL &amp; LIABILITIES:</td>
                    <td class="r" style="font-size:10.5px; color:#0F172A;">
                        {{ formatIndianCurrency(($totalLiabilities ?? 0) + ($netWorth ?? 0)) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ── RIGHT COLUMN: ASSETS & APPLICATIONS ── --}}
    <div style="flex:1; min-width:0;">
        <div class="section-label">
            <i class="fa-solid fa-vault" style="color:#059669;"></i> Assets &amp; Applications of Funds
        </div>
        <table>
            <thead>
                <tr>
                    <th>Asset Classification &amp; Holdings</th>
                    <th class="c" style="width:75px;">Schedule</th>
                    <th class="r" style="width:115px;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Group 1: Fixed Assets / Real Estate Inventory --}}
                <tr style="background:#F8FAFC; font-weight:800;">
                    <td colspan="3"><i class="fa-solid fa-circle-dot" style="font-size:7px; color:#059669;"></i> I. PROPERTY &amp; LAND INVENTORY</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Unsold Properties &amp; Land Parcels</strong>
                        <div style="font-size:7.5px; color:#64748B;">Stock of Available &amp; Booked Units at Valuation Cost</div>
                    </td>
                    <td class="c"><span class="badge">Inventory</span></td>
                    <td class="r" style="font-weight:700; color:#059669;">{{ formatIndianCurrency($propertyValue ?? 0) }}</td>
                </tr>

                {{-- Group 2: Current Assets --}}
                <tr style="background:#F8FAFC; font-weight:800;">
                    <td colspan="3"><i class="fa-solid fa-circle-dot" style="font-size:7px; color:#2563EB;"></i> II. CURRENT ASSETS &amp; ADVANCES</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Raw Materials &amp; Construction Stock</strong>
                        <div style="font-size:7.5px; color:#64748B;">Warehouse Inward Valuation (Cement, Steel, Bricks, etc.)</div>
                    </td>
                    <td class="c"><span class="badge">Materials</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($materialStockValue ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Sundry Debtors / Outstanding Receivables</strong>
                        <div style="font-size:7.5px; color:#64748B;">Customer Installments &amp; Remaining Sales Balances</div>
                    </td>
                    <td class="c"><span class="badge">Debtors</span></td>
                    <td class="r" style="font-weight:700; color:#B45309;">{{ formatIndianCurrency($receivables ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Cash in Hand &amp; Bank Accounts</strong>
                        <div style="font-size:7.5px; color:#64748B;">
                            Customer Receipts ({{ formatIndianCurrency($cashReceived ?? 0) }}) &bull; Rentals ({{ formatIndianCurrency($rentalCashReceived ?? 0) }})
                        </div>
                    </td>
                    <td class="c"><span class="badge">Liquid Cash</span></td>
                    <td class="r" style="font-weight:700; color:#2563EB;">{{ formatIndianCurrency($totalCashAndBank ?? 0) }}</td>
                </tr>
                <tr>
                    <td style="padding-left:16px;">
                        <strong>Security Deposits Held with Landlords/Tenants</strong>
                        <div style="font-size:7.5px; color:#64748B;">Tenancy Escrow Accounts</div>
                    </td>
                    <td class="c"><span class="badge">Deposit</span></td>
                    <td class="r" style="font-weight:700;">{{ formatIndianCurrency($securityDeposits ?? 0) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="double-total">
                    <td colspan="2" class="r" style="font-size:9.5px;">TOTAL ASSETS:</td>
                    <td class="r" style="font-size:10.5px; color:#0F172A;">
                        {{ formatIndianCurrency($totalAssets ?? 0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- ── Accounting Notes & Reconciliation Statement ── --}}
<div style="margin-top:10px; background:#F8FAFC; border:1px solid #E2E8F0; border-radius:4px; padding:7px 11px; font-size:8px; color:#475569;">
    <strong>Accounting Notes to Balance Sheet:</strong>
    <ol style="margin-left:16px; margin-top:3px; line-height:1.4;">
        <li>Property and land stock are valued at cost of acquisition or net realizable value, consistent with Real Estate Developer Accounting Principles.</li>
        <li>Liquid Cash and Bank balance reflects cumulative inflow receipts from property bookings, sales conveyances, and tenancy collections less recognized outflows.</li>
        <li>Sundry Debtors represents installments due on executed agreements and confirmed customer bookings as of {{ request('as_on_date') ? \Carbon\Carbon::parse(request('as_on_date'))->format('d M Y') : date('d M Y') }}.</li>
    </ol>
</div>

{{-- ── Signatures Block ── --}}
<div class="auth-block" style="margin-top:18px; page-break-inside:avoid;">
    <div class="auth-col">
        <div class="auth-line">Chartered Accountant / Accounts Head</div>
    </div>
    <div class="auth-col">
        <div class="auth-line">For Delawala Properties &amp; Management (Authorized Signatory)</div>
    </div>
</div>

@include('admin.components.pdf-footer', ['note' => 'Statutory Balance Sheet • Verified from General Ledgers & Books of Account'])
</body>
</html>
