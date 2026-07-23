<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::first();
if (!$user) { echo "No user found\n"; exit; }
echo "User: " . $user->name . "\n";
echo "Firm ID: " . $user->firm_id . "\n";
echo "Customers (firm): " . App\Models\Customer::where('firm_id', $user->firm_id)->count() . "\n";
echo "Properties (firm): " . App\Models\Property::where('firm_id', $user->firm_id)->count() . "\n";
echo "Payments (firm): " . App\Models\Payment::where('firm_id', $user->firm_id)->count() . "\n";
echo "Expenses (firm): " . App\Models\Expense::where('firm_id', $user->firm_id)->count() . "\n";
echo "AuditLogs total: " . App\Models\AuditLog::count() . "\n";
echo "PropertySales (firm): " . App\Models\PropertySale::where('firm_id', $user->firm_id)->count() . "\n";

$firstLog = App\Models\AuditLog::latest()->first();
if ($firstLog) {
    echo "Latest Log: [{$firstLog->action_type}] {$firstLog->module_name} - {$firstLog->description}\n";
}
