<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    protected $fillable = [
        'firm_name', 'owner_name', 'email', 'password', 'mobile', 'alternate_mobile',
        'address', 'city', 'state', 'pincode',
        'gst_no', 'pan_number', 'firm_logo',
        'bank_name', 'account_number', 'ifsc_code', 'branch_name',
        'status',
    ];

    protected $hidden = ['password'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function financialYears()
    {
        return $this->hasMany(FinancialYear::class);
    }

    public function salesAgreements()
    {
        return $this->hasMany(PropertySale::class, 'firm_id');
    }

    public function propertySales()
    {
        return $this->hasMany(PropertySale::class, 'firm_id');
    }

    public function paymentCollections()
    {
        return $this->hasMany(Payment::class, 'firm_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'firm_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class, 'firm_id');
    }

    public function debitNotes()
    {
        return $this->hasMany(DebitNote::class, 'firm_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'firm_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'firm_id');
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'firm_id');
    }

    public function formSubmissions()
    {
        return $this->hasMany(FormSubmission::class, 'firm_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Form::class, 'firm_id');
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'firm_id');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class, 'firm_id');
    }

    public function rentalPayments()
    {
        return $this->hasMany(RentalPayment::class, 'firm_id');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class, 'firm_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'firm_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'firm_id');
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'firm_id');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'firm_id');
    }

    public function loanEmiSchedules()
    {
        return $this->hasMany(LoanEmiSchedule::class, 'firm_id');
    }

    public function brokerCommissions()
    {
        return $this->hasMany(BrokerCommission::class, 'firm_id');
    }

    // MorphToMany inverse relationships
    public function multiIncomes()          { return $this->morphedByMany(Income::class, 'firmable', 'firmables'); }
    public function multiExpenses()         { return $this->morphedByMany(Expense::class, 'firmable', 'firmables'); }
    public function multiPurchases()        { return $this->morphedByMany(Purchase::class, 'firmable', 'firmables'); }
    public function multiReceipts()         { return $this->morphedByMany(Receipt::class, 'firmable', 'firmables'); }
    public function multiLoans()            { return $this->morphedByMany(Loan::class, 'firmable', 'firmables'); }
    public function multiBrokerCommissions(){ return $this->morphedByMany(BrokerCommission::class, 'firmable', 'firmables'); }

    public function propertyTypes()
    {
        return $this->belongsToMany(PropertyType::class, 'property_type_firm', 'firm_id', 'property_type_id')->withTimestamps();
    }

    public function expenseCategories()
    {
        return $this->belongsToMany(ExpenseCategory::class, 'expense_category_firm', 'firm_id', 'expense_category_id')->withTimestamps();
    }

    public function paymentModes()
    {
        return $this->belongsToMany(PaymentMode::class, 'payment_mode_firm', 'firm_id', 'payment_mode_id')->withTimestamps();
    }

    public function invoiceSettings()
    {
        return $this->belongsToMany(InvoiceSetting::class, 'tax_gst_setting_firm', 'firm_id', 'invoice_setting_id')->withTimestamps();
    }
}
