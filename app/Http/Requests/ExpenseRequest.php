<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $inputs[$key] = trim($value);
            }
        }
        if (empty($inputs['approval_status'])) {
            $inputs['approval_status'] = 'Pending';
        }
        if (isset($inputs['is_tenant_recoverable'])) {
            $inputs['is_tenant_recoverable'] = filter_var($inputs['is_tenant_recoverable'], FILTER_VALIDATE_BOOLEAN);
        } else {
            $inputs['is_tenant_recoverable'] = false;
        }
        $this->replace($inputs);
    }

    public function rules(): array
    {
        $rules = [
            'firm_ids'              => 'nullable|array',
            'firm_ids.*'            => 'exists:firms,id',
            'firm_id'               => 'nullable|exists:firms,id',
            'project_id'            => 'nullable|exists:projects,id',
            'property_ids'          => 'nullable|array',
            'property_ids.*'        => 'exists:properties,id',
            'property_id'           => 'nullable|exists:properties,id',
            'rental_id'             => 'nullable|exists:rentals,id',
            'tenant_id'             => 'nullable|exists:tenants,id',
            'is_tenant_recoverable' => 'nullable|boolean',
            'recovery_amount'       => 'nullable|numeric|min:0',
            'recovery_status'       => 'nullable|string|in:Pending,Partially Recovered,Recovered,Not Applicable',
            'expense_date'          => 'required|date',
            'expense_category_id'   => 'nullable',
            'expense_category'      => 'nullable|string|max:255',
            'expense_subcategory'   => 'nullable|string|max:255',
            'expense_type'          => 'nullable|string|in:Property,Project,General,Office,Rental,Personal,Other',
            'expense_title'         => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:2000',
            'amount'                => 'required|numeric|min:0.01',
            'payment_mode'          => 'nullable|string|max:255',
            'reference_no'          => 'nullable|string|max:255',
            'payment_account'       => 'nullable|string|max:255',
            'vendor_id'             => 'nullable',
            'paid_to'               => 'nullable|string|max:255',
            'bill_no'               => 'nullable|string|max:255',
            'bill_file'             => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            'approval_status'       => 'nullable|in:Pending,Approved,Rejected',
            'remarks'               => 'nullable|string|max:2000',
            'notes'                 => 'nullable|string|max:2000',
        ];

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'firm_id'             => 'Firm',
            'firm_ids'            => 'Firm(s)',
            'project_id'          => 'Project',
            'property_ids'        => 'Property / Unit(s)',
            'property_id'         => 'Property / Unit',
            'expense_date'        => 'Expense Date',
            'expense_category_id' => 'Expense Category',
            'expense_category'    => 'Expense Category',
            'expense_type'        => 'Expense Type',
            'expense_title'       => 'Expense Title',
            'description'         => 'Description',
            'amount'              => 'Amount',
            'payment_mode'        => 'Payment Mode',
            'reference_no'        => 'Reference Number',
            'payment_account'     => 'Payment Account',
            'vendor_id'           => 'Vendor / Payee',
            'paid_to'             => 'Paid To',
            'bill_no'             => 'Bill / Invoice No',
            'bill_file'           => 'Attachment',
            'approval_status'     => 'Approval Status',
            'remarks'             => 'Remarks',
            'notes'               => 'Notes',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors' => $validator->errors()
                ], 422)
            );
        }
        parent::failedValidation($validator);
    }
}