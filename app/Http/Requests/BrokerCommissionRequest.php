<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrokerCommissionRequest extends FormRequest
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
            'firm_id' => 'required|exists:firms,id',
            'broker_id' => 'required|exists:brokers,id',
            'property_id' => 'required|exists:properties,id',
            'customer_id' => 'nullable|exists:customers,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'commission_type' => 'required|in:percentage,fixed',
            'commission_value' => 'required|numeric|min:0',
            'commission_amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,partial,paid',
            'payment_date' => 'nullable|date',
            'remarks' => 'nullable|string|max:1000',
            'status' => 'nullable|in:active,inactive',
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
            'firm_id' => 'Firm',
            'broker_id' => 'Broker',
            'property_id' => 'Property',
            'customer_id' => 'Customer',
            'booking_id' => 'Booking',
            'commission_type' => 'Commission Type',
            'commission_value' => 'Commission Value',
            'commission_amount' => 'Commission Amount',
            'payment_status' => 'Payment Status',
            'payment_date' => 'Payment Date',
            'remarks' => 'Remarks',
            'status' => 'Status',
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