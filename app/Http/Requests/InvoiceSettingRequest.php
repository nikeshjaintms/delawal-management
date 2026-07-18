<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class InvoiceSettingRequest extends FormRequest
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
            'financial_year_id' => 'nullable|exists:financial_years,id',
            'status'            => 'required|in:active,inactive',
            'starting_number'   => 'required|integer|min:1',
            'current_number'    => 'required|integer|min:1',
            'sales_prefix'      => 'required|string|max:10',
            'purchase_prefix'   => 'required|string|max:10',
            'booking_prefix'    => 'required|string|max:10',
            'rental_prefix'     => 'required|string|max:10',
            'payment_prefix'    => 'required|string|max:10',
            'receipt_prefix'    => 'required|string|max:10',
            'expense_prefix'    => 'required|string|max:10',
            'income_prefix'     => 'required|string|max:10',
            'loan_prefix'       => 'required|string|max:10',
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
            'financial_year_id' => 'Financial Year',
            'status'            => 'Status',
            'starting_number'   => 'Starting Number',
            'current_number'    => 'Current Number',
            'sales_prefix'      => 'Sales Prefix',
            'purchase_prefix'   => 'Purchase Prefix',
            'booking_prefix'    => 'Booking Prefix',
            'rental_prefix'     => 'Rental Prefix',
            'payment_prefix'    => 'Payment Prefix',
            'receipt_prefix'    => 'Receipt Prefix',
            'expense_prefix'    => 'Expense Prefix',
            'income_prefix'     => 'Income Prefix',
            'loan_prefix'       => 'Loan Prefix',
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