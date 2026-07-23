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
            'firm_ids'             => 'nullable|array',
            'firm_ids.*'           => 'exists:firms,id',
            'firm_id'              => 'nullable|exists:firms,id',
            'expense_title'       => 'required|string|max:255',
            'expense_date'        => 'required|date',
            'expense_category_id' => 'nullable|exists:expense_categories,id',
            'property_id'         => 'nullable|exists:properties,id',
            'amount'              => 'required|numeric|min:0.01',
            'payment_mode'        => 'nullable|string|max:255',
            'paid_to'             => 'nullable|string|max:255',
            'bill_no'             => 'nullable|string|max:255',
            'bill_file'           => 'nullable|file|max:5120',
            'approval_status'     => 'required|in:Pending,Approved,Rejected',
            'remarks'             => 'nullable|string|max:1000',
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
            'firm_id'             => 'Firm',
            'expense_title'       => 'Expense Title',
            'expense_date'        => 'Expense Date',
            'expense_category_id' => 'Expense Category',
            'property_id'         => 'Property',
            'amount'              => 'Amount',
            'payment_mode'        => 'Payment Mode',
            'paid_to'             => 'Paid To',
            'bill_no'             => 'Bill / Invoice No',
            'bill_file'           => 'Bill File',
            'approval_status'     => 'Approval Status',
            'remarks'             => 'Remarks',
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