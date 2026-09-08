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
        if (empty($inputs['item_name']) && !empty($inputs['property_name'])) {
            $inputs['item_name'] = $inputs['property_name'];
        }
        if (empty($inputs['purchase_date'])) {
            $inputs['purchase_date'] = date('Y-m-d');
        }
        if (!isset($inputs['purchase_amount']) || $inputs['purchase_amount'] === '') {
            $inputs['purchase_amount'] = 0;
        }
        if (empty($inputs['payment_status'])) {
            $inputs['payment_status'] = 'unpaid';
        }
        if (empty($inputs['status'])) {
            $inputs['status'] = 'active';
        }
        $this->replace($inputs);
    }

    public function rules(): array
    {
        return [
            'firm_ids'         => 'nullable|array',
            'firm_ids.*'       => 'exists:firms,id',
            'firm_id'          => 'nullable|exists:firms,id',
            'vendor_id'        => 'nullable|exists:vendors,id',
            'property_name'    => 'required|string|max:255',
            'property_type'    => 'nullable|string|max:100',
            'property_code'    => 'nullable|string|max:100',
            'location'         => 'nullable|string|max:255',
            'address'          => 'nullable|string|max:1000',
            'survey_no'        => 'nullable|string|max:100',
            'tp_no'            => 'nullable|string|max:100',
            'fp_no'            => 'nullable|string|max:100',
            'area'             => 'nullable|numeric|min:0',
            'area_unit'        => 'nullable|string|max:50',
            'item_name'        => 'nullable|string|max:255',
            'purchase_date'    => 'nullable|date',
            'purchase_amount'  => 'nullable|numeric|min:0',
            'quantity'         => 'nullable|numeric|min:0',
            'payment_mode'     => 'nullable|string|max:255',
            'payment_status'   => 'nullable|string|max:50',
            'reference_no'     => 'nullable|string|max:255',
            'status'           => 'nullable|string|max:50',
            'remarks'          => 'nullable|string|max:1000',
        ];
    }

    public function attributes(): array
    {
        return [
            'firm_id'         => 'Firm',
            'vendor_id'       => 'Vendor / Seller',
            'property_name'   => 'Property / Plot Name',
            'property_type'   => 'Property Type',
            'property_code'   => 'Property Number / Code',
            'location'        => 'Location',
            'address'         => 'Address',
            'survey_no'       => 'Survey No.',
            'tp_no'           => 'TP No.',
            'fp_no'           => 'FP No.',
            'area'            => 'Area',
            'area_unit'       => 'Area Unit',
            'item_name'       => 'Item / Property Name',
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