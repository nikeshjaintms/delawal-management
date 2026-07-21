<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PurchaseRequest extends FormRequest
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
            'firm_ids'         => 'nullable|array',
            'firm_ids.*'       => 'exists:firms,id',
            'firm_id'          => 'nullable|exists:firms,id',
            'vendor_id'       => 'nullable|exists:vendors,id',
            'item_name'       => 'required|string|max:255',
            'purchase_date'   => 'required|date',
            'purchase_amount' => 'required|numeric|min:0',
            'quantity'        => 'nullable|integer|min:1',
            'payment_mode'    => 'nullable|string|max:255',
            'payment_status'  => 'required|in:unpaid,partial,paid',
            'reference_no'    => 'nullable|string|max:255',
            'status'          => 'required|in:active,inactive',
            'remarks'         => 'nullable|string|max:1000',
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
            'firm_id'         => 'Firm',
            'vendor_id'       => 'Vendor',
            'item_name'       => 'Item Name',
            'purchase_date'   => 'Purchase Date',
            'purchase_amount' => 'Purchase Amount',
            'quantity'        => 'Quantity',
            'payment_mode'    => 'Payment Mode',
            'payment_status'  => 'Payment Status',
            'reference_no'    => 'Reference No',
            'status'          => 'Status',
            'remarks'         => 'Remarks',
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