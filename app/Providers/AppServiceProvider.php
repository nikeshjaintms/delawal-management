<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ── 1. Resolve Auth User Proxy for Firm Login Sessions ──
        \Illuminate\Support\Facades\Auth::resolveUsersUsing(function ($guard = null) {
            static $resolving = false;
            if ($resolving) {
                return null;
            }

            if (session('login_type') === 'firm' && session('firm_id')) {
                $user = new \App\Models\User();
                $user->id = session('firm_id');
                $user->firm_id = session('firm_id');
                $user->name = session('firm_name');
                $user->email = session('firm_email');
                $user->status = 'active';

                $role = new \App\Models\Role();
                $role->id = 999999;
                $role->role_name = 'Firm Owner';
                $role->name = 'Firm Owner';

                $user->setRelation('role', $role);

                return new class($user) extends \App\Models\User {
                    public function __construct($user) {
                        parent::__construct();
                        $this->id = $user->id;
                        $this->firm_id = $user->firm_id;
                        $this->name = $user->name;
                        $this->email = $user->email;
                        $this->status = $user->status;
                        $this->setRelation('role', $user->getRelation('role'));
                    }
                    public function isAdmin(): bool {
                        return false;
                    }
                    public function hasPermission(string $permissionKey): bool {
                        return true;
                    }
                };
            }

            $resolving = true;
            $guard = $guard ?: \Illuminate\Support\Facades\Auth::getDefaultDriver();
            try {
                $resolvedUser = \Illuminate\Support\Facades\Auth::guard($guard)->user();
            } catch (\Exception $e) {
                $resolvedUser = null;
            } finally {
                $resolving = false;
            }
            return $resolvedUser;
        });

        $models = [
            \App\Models\Customer::class,
            \App\Models\Broker::class,
            \App\Models\Vendor::class,
            \App\Models\Tenant::class,
            \App\Models\PropertyType::class,
            \App\Models\PaymentMode::class,
            \App\Models\ExpenseCategory::class,
            \App\Models\Property::class,
            \App\Models\PropertySale::class,
            \App\Models\Payment::class,
            \App\Models\Rental::class,
            \App\Models\RentalPayment::class,
            \App\Models\Expense::class,
            \App\Models\MaterialCategory::class,
            \App\Models\Material::class,
            \App\Models\StockInward::class,
            \App\Models\StockOutward::class,
            \App\Models\Loan::class,
            \App\Models\Ledger::class,
            \App\Models\CreditNote::class,
            \App\Models\DebitNote::class,
            \App\Models\User::class,
            \App\Models\Form::class,
            \App\Models\FormSubmission::class,
            \App\Models\Booking::class,
            \App\Models\Purchase::class,
            \App\Models\Income::class,
            \App\Models\Receipt::class,
            \App\Models\BrokerCommission::class,
            \App\Models\PropertyDocument::class,
            \App\Models\PropertyStatus::class,
        ];

        foreach ($models as $modelClass) {
            if (class_exists($modelClass)) {
                // Apply global data isolation scope for firm_id and financial_year
                $modelClass::addGlobalScope('data_isolation', function (\Illuminate\Database\Eloquent\Builder $builder) use ($modelClass) {
                    // Filter by Firm ID
                    if (session('login_type') === 'firm' && session('firm_id')) {
                        $instance = new $modelClass();
                        if (in_array('firm_id', $instance->getFillable()) || \Illuminate\Support\Facades\Schema::hasColumn($instance->getTable(), 'firm_id')) {
                            $builder->where($instance->getTable() . '.firm_id', session('firm_id'));
                        }
                    }

                    // Filter by Financial Year Date Range (where applicable for transaction models)
                    if (session('login_type') === 'firm' && session('financial_year_id')) {
                        $fy = \App\Models\FinancialYear::find(session('financial_year_id'));
                        if ($fy) {
                            $startDate = $fy->start_date->format('Y-m-d');
                            $endDate = $fy->end_date->format('Y-m-d');
                            $instance = new $modelClass();
                            $table = $instance->getTable();
                            $fillables = $instance->getFillable();

                            $dateColumn = null;
                            if (in_array('sale_date', $fillables)) {
                                $dateColumn = 'sale_date';
                            } elseif (in_array('payment_date', $fillables)) {
                                $dateColumn = 'payment_date';
                            } elseif (in_array('rent_start_date', $fillables)) {
                                $dateColumn = 'rent_start_date';
                            } elseif (in_array('expense_date', $fillables)) {
                                $dateColumn = 'expense_date';
                            } elseif (in_array('loan_start_date', $fillables)) {
                                $dateColumn = 'loan_start_date';
                            } elseif (in_array('inward_date', $fillables)) {
                                $dateColumn = 'inward_date';
                            } elseif (in_array('outward_date', $fillables)) {
                                $dateColumn = 'outward_date';
                            } elseif (in_array('booking_date', $fillables)) {
                                $dateColumn = 'booking_date';
                            } elseif (in_array('purchase_date', $fillables)) {
                                $dateColumn = 'purchase_date';
                            } elseif (in_array('income_date', $fillables)) {
                                $dateColumn = 'income_date';
                            } elseif (in_array('receipt_date', $fillables)) {
                                $dateColumn = 'receipt_date';
                            } elseif (in_array('credit_note_date', $fillables)) {
                                $dateColumn = 'credit_note_date';
                            } elseif (in_array('debit_note_date', $fillables)) {
                                $dateColumn = 'debit_note_date';
                            } elseif (in_array('ledger_date', $fillables)) {
                                $dateColumn = 'ledger_date';
                            }

                            if ($dateColumn) {
                                $builder->whereBetween($table . '.' . $dateColumn, [$startDate, $endDate]);
                            }
                        }
                    }
                });

                // Audit logging observers
                $modelClass::created(function ($model) {
                    $name = class_basename($model);
                    $displayName = preg_replace('/(?<!^)(?=[A-Z])/', ' ', $name);
                    $id = $model->id ?? '';
                    \App\Models\AuditLog::log($displayName, 'Create Record', "Created new {$displayName} record (ID: {$id})");
                });

                $modelClass::updated(function ($model) {
                    $name = class_basename($model);
                    $displayName = preg_replace('/(?<!^)(?=[A-Z])/', ' ', $name);
                    $id = $model->id ?? '';
                    
                    $dirty = $model->getDirty();
                    unset($dirty['updated_at']);
                    
                    $changesStr = count($dirty) > 0 ? " Changes: " . json_encode($dirty) : "";
                    \App\Models\AuditLog::log($displayName, 'Update Record', "Updated {$displayName} record (ID: {$id}).{$changesStr}");
                });

                $modelClass::deleted(function ($model) {
                    $name = class_basename($model);
                    $displayName = preg_replace('/(?<!^)(?=[A-Z])/', ' ', $name);
                    $id = $model->id ?? '';
                    \App\Models\AuditLog::log($displayName, 'Delete Record', "Deleted {$displayName} record (ID: {$id})");
                });
            }
        }
    }
}
