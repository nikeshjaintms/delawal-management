<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RentalPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();
        if (empty($inputs['firm_id'])) {
            $inputs['firm_id'] = auth()->check() ? auth()->user()->firm_id : session('firm_id');
        }
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
            'firm_id'        => 'required|exists:firms,id',
            'property_id'    => 'required|exists:properties,id',
            'payment_month'  => 'required|string|max:255',
            'payment_year'   => 'required|integer|min:2020',
            'rent_amount'    => 'required|numeric|min:0',
            'paid_amount'    => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
            'payment_mode'   => 'required|string|max:255',
            'remarks'        => 'nullable|string|max:1000',
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
            'property_id'   => 'Property',
            'payment_month' => 'Payment Month',
            'payment_year'  => 'Payment Year',
            'rent_amount'   => 'Rent Amount',
            'paid_amount'   => 'Paid Amount',
            'payment_date'  => 'Payment Date',
            'payment_mode'  => 'Payment Mode',
            'remarks'       => 'Remarks',
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