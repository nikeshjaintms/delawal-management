<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoanRequest extends FormRequest
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
        if (isset($inputs['firm_ids']) && is_array($inputs['firm_ids']) && !empty($inputs['firm_ids'])) {
            $inputs['firm_id'] = $inputs['firm_ids'][0];
        }
        $this->replace($inputs);
    }

    public function rules(): array
    {
        $id = null;
        if ($this->route()) {
            foreach ($this->route()->parameters() as $param) {
                if (is_object($param)) {
                    $id = $param->id;
                    break;
                } elseif (is_numeric($param)) {
                    $id = $param;
                    break;
                }
            }
        }
        $firmId = auth()->check() ? auth()->user()->firm_id : 0;

        $rules = [
            'firm_ids'         => 'nullable|array',
            'firm_ids.*'       => 'exists:firms,id',
            'firm_id'          => 'nullable|exists:firms,id',
            'loan_type'        => 'required|in:Business Loan,Personal Loan',
            
            // Business Loan Validation
            'bank_name'        => 'required_if:loan_type,Business Loan|nullable|string|max:255',
            'property_id'      => 'nullable|exists:properties,id',
            'customer_id'      => 'nullable|exists:customers,id',
            'loan_amount'      => 'required|numeric|min:0.01',
            'interest_rate'    => 'required_if:loan_type,Business Loan|nullable|numeric|min:0|max:100',
            'total_emi_months' => 'required_if:loan_type,Business Loan|nullable|integer|min:1',
            'emi_amount'       => 'required_if:loan_type,Business Loan|nullable|numeric|min:0',
            'loan_start_date'  => 'required|date',
            'loan_end_date'    => 'required_if:loan_type,Business Loan|nullable|date|after_or_equal:loan_start_date',
            'loan_status'      => 'required|in:Active,Completed,Closed,Cancelled',
            'remarks'          => 'nullable|string|max:1000',
            
            // Personal Loan Validation
            'person_name'      => 'required_if:loan_type,Personal Loan|nullable|string|max:255',
            'mobile_number'    => 'nullable|string|max:30',
            'relationship'     => 'nullable|string|max:100',
            'payment_mode_id'  => 'nullable|exists:payment_modes,id',
        ];

        // Replace placeholders in unique rules dynamically
        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                $replaced = str_replace('{ID}', $id ?: 'NULL', $rule);
                $replaced = str_replace('{FIRM_ID}', $firmId, $replaced);
                
                // Dynamic Password rule for users
                if ($field === 'password') {
                    if ($this->isMethod('post')) {
                        $replaced = 'required|string|min:6|same:confirm_password';
                    } else {
                        $replaced = 'nullable|string|min:6|same:confirm_password';
                    }
                }
                if ($field === 'confirm_password') {
                    if ($this->isMethod('post')) {
                        $replaced = 'required';
                    } else {
                        $replaced = 'nullable';
                    }
                }
                
                $rules[$field] = $replaced;
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'firm_id'          => 'Firm',
            'bank_name'        => 'Bank Name',
            'loan_type'        => 'Loan Type',
            'property_id'      => 'Property',
            'customer_id'      => 'Customer',
            'loan_amount'      => 'Loan Amount',
            'interest_rate'    => 'Interest Rate',
            'total_emi_months' => 'Total EMI Months',
            'emi_amount'       => 'EMI Amount',
            'loan_start_date'  => 'Loan Start Date',
            'loan_end_date'    => 'Loan End Date',
            'loan_status'      => 'Loan Status',
            'remarks'          => 'Remarks',
            'person_name'      => 'Person Name',
            'mobile_number'    => 'Mobile Number',
            'relationship'     => 'Relationship',
            'payment_mode_id'  => 'Payment Mode',
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