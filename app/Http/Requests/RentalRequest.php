<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RentalRequest extends FormRequest
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
            'property_id'      => 'required|exists:properties,id',
            'tenant_name'      => 'required|string|max:255',
            'tenant_mobile'    => 'required|string|max:15',
            'tenant_email'     => 'nullable|email|max:255',
            'rent_amount'      => 'required|numeric|min:0.01',
            'security_deposit' => 'nullable|numeric|min:0',
            'rent_start_date'  => 'required|date',
            'rent_end_date'    => 'nullable|date|after_or_equal:rent_start_date',
            'rent_due_date'    => 'nullable|integer|min:1|max:31',
            'payment_status'   => 'required|in:pending,partial,paid',
            'rental_status'    => 'required|in:active,completed,cancelled',
            'remarks'          => 'nullable|string|max:1000',
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
            'property_id'      => 'Property',
            'tenant_name'      => 'Tenant Name',
            'tenant_mobile'    => 'Tenant Mobile',
            'tenant_email'     => 'Tenant Email',
            'rent_amount'      => 'Rent Amount',
            'security_deposit' => 'Security Deposit',
            'rent_start_date'  => 'Rent Start Date',
            'rent_end_date'    => 'Rent End Date',
            'rent_due_date'    => 'Rent Due Day of Month',
            'payment_status'   => 'Payment Status',
            'rental_status'    => 'Rental Status',
            'remarks'          => 'Remarks',
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