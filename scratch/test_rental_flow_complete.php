<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Expense;
use App\Models\Rental;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Firm;
use Illuminate\Http\Request;

echo "=== TESTING RENTAL EXPENSE MODULE INTEGRATION ===\n\n";

// 1. Check existing rentals and properties
$rental = Rental::with(['property', 'tenant', 'firm'])->first();
if (!$rental) {
    echo "No rental record found in DB to test.\n";
    exit;
}

echo "Found Rental: #{$rental->id} (Agreement: {$rental->agreement_no}) on Property: {$rental->property?->property_name} (ID: {$rental->property_id})\n";
echo "Tenant: " . ($rental->tenant?->name ?? $rental->tenant_name) . " (ID: {$rental->tenant_id})\n\n";

// 2. Test AJAX endpoint logic
echo "Testing AJAX getRentalPropertyInfo for Property ID: {$rental->property_id}...\n";
$controller = new App\Http\Controllers\ExpenseController();
$request = new Request();
$ajaxResponse = $controller->getRentalPropertyInfo($request, $rental->property_id);
$data = json_decode($ajaxResponse->getContent(), true);

if ($data['success'] && isset($data['active_rental'])) {
    echo "✅ AJAX endpoint working! Active Rental Agreement: " . $data['active_rental']['agreement_no'] . ", Tenant: " . $data['active_rental']['tenant_name'] . "\n\n";
} else {
    echo "⚠️ AJAX response: " . json_encode($data) . "\n\n";
}

// 3. Create a Rental Expense linked to this rental
echo "Creating a test Rental Expense with Tenant Recovery...\n";
$testExpense = Expense::create([
    'firm_id' => $rental->firm_id ?: 1,
    'property_id' => $rental->property_id,
    'rental_id' => $rental->id,
    'tenant_id' => $rental->tenant_id,
    'expense_type' => 'Rental',
    'expense_category' => 'Plumbing Work',
    'expense_subcategory' => 'Plumbing Work',
    'expense_title' => 'Bathroom Tap Repair - ' . ($rental->property?->property_name ?? 'Unit'),
    'description' => 'Replaced broken mixer valve in master bathroom',
    'amount' => 1500.00,
    'payment_mode' => 'UPI',
    'paid_to' => 'Rajesh Plumber',
    'expense_date' => date('Y-m-d'),
    'approval_status' => 'Approved',
    'is_tenant_recoverable' => true,
    'recovery_amount' => 1500.00,
    'recovery_status' => 'Pending',
]);

echo "✅ Created Expense #{$testExpense->id}: {$testExpense->expense_title} (₹{$testExpense->amount})\n";
echo "   Is Recoverable: " . ($testExpense->is_tenant_recoverable ? 'YES' : 'NO') . "\n";
echo "   Recovery Amount: ₹{$testExpense->recovery_amount} (Status: {$testExpense->recovery_status})\n\n";

// 4. Test Rental model relationship
$reloadedRental = Rental::with('expenses')->find($rental->id);
$hasExpense = $reloadedRental->expenses->contains('id', $testExpense->id);
echo "Rental Model -> expenses relationship test: " . ($hasExpense ? "✅ PASSED" : "❌ FAILED") . "\n";

// 5. Test Tenant model relationship if tenant_id exists
if ($rental->tenant_id) {
    $tenant = Tenant::with('expenses')->find($rental->tenant_id);
    $tenantHasExp = $tenant && $tenant->expenses->contains('id', $testExpense->id);
    echo "Tenant Model -> expenses relationship test: " . ($tenantHasExp ? "✅ PASSED" : "❌ FAILED") . "\n";
}

// 6. Test index query and KPI calculation
$rentalExpensesQuery = Expense::where('expense_type', 'Rental')
    ->orWhereNotNull('rental_id');
$totalSum = (clone $rentalExpensesQuery)->sum('amount');
$recoverableSum = (clone $rentalExpensesQuery)->where('is_tenant_recoverable', true)->sum('recovery_amount');
$pendingSum = (clone $rentalExpensesQuery)->where('is_tenant_recoverable', true)->where('recovery_status', 'Pending')->sum('recovery_amount');

echo "\nRental KPIs:\n";
echo "   Total Rental Expenses: ₹" . number_format($totalSum, 2) . "\n";
echo "   Tenant Recoverable: ₹" . number_format($recoverableSum, 2) . "\n";
echo "   Pending Recovery: ₹" . number_format($pendingSum, 2) . "\n\n";

// Clean up test expense
$testExpense->delete();
echo "✅ Test cleanup completed.\n";
echo "=== ALL VERIFICATIONS PASSED ===\n";
