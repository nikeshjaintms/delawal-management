<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();

        // Resolve firm_ids array from super admin form-select
        if (isset($inputs['firm_ids']) && is_array($inputs['firm_ids']) && !empty($inputs['firm_ids'])) {
            $inputs['firm_id'] = (int) $inputs['firm_ids'][0];
        }

        if (empty($inputs['firm_id'])) {
            if (!empty($inputs['property_sale_id'])) {
                $sale = \App\Models\PropertySale::find($inputs['property_sale_id']);
                if ($sale && !empty($sale->firm_id)) {
                    $inputs['firm_id'] = (int) $sale->firm_id;
                }
            }
        }

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
            'firm_id'          => 'nullable|exists:firms,id',
            'firm_ids'         => 'nullable|array',
            'firm_ids.*'       => 'exists:firms,id',
            'property_sale_id' => 'required|exists:property_sales,id',
            'payment_date'     => 'required|date',
            'payment_amount'   => 'required|numeric|min:0.01',
            'payment_mode'     => 'required|string|max:255',
            'transaction_ref'  => 'nullable|string|max:255',
            'remarks'          => 'nullable|string|max:1000',
        ];

        // Replace placeholders in unique rules dynamically
        foreach ($rules as $field => $rule) {
            if (is_string($rule)) {
                $replaced = str_replace('{ID}', $id ?: 'NULL', $rule);
                $replaced = str_replace('{FIRM_ID}', $firmId, $replaced);
                $rules[$field] = $replaced;
            }
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'firm_id'          => 'Firm',
            'firm_ids'         => 'Firm(s)',
            'property_sale_id' => 'Property Booking / Sale',
            'payment_date'     => 'Payment Date',
            'payment_amount'   => 'New Payment Amount',
            'payment_mode'     => 'Payment Mode',
            'transaction_ref'  => 'Transaction ID / Cheque No',
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