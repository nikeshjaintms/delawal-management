<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropertyMasterRequest extends FormRequest
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
        $firmId = $this->get('firm_id') ?: (auth()->check() && auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $rules = [
            'firm_id' => (auth()->user() && auth()->user()->isAdmin()) ? 'required|exists:firms,id' : 'nullable|exists:firms,id',
            'property_name' => 'required|string|max:255',
            'property_code' => 'nullable|string|max:100|unique:property_masters,property_code,{ID},id,firm_id,{FIRM_ID}',
            'property_type' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'purchase_price' => 'nullable|numeric|min:0',
            'paid_amount'    => 'nullable|numeric|min:0',
            'due_amount'     => 'nullable|numeric|min:0',
            'purchase_date'  => 'nullable|date',
            'purchase_rate'  => 'nullable|numeric|min:0',
            'total_area'     => 'nullable|numeric|min:0',
            'area_unit'      => 'nullable|string|max:50',
            'total_units_count'   => 'nullable|integer|min:0',
            'unit_numbers_list'   => 'nullable|string|max:2000',
            'unit_prefix'         => 'nullable|string|max:50',
            'auto_generate_units' => 'nullable|boolean',
            'seller_name'    => 'nullable|string|max:255',
            'vendor_id'      => 'nullable|exists:vendors,id',
            'broker_id'      => 'nullable|exists:brokers,id',
            'broker_name'    => 'nullable|string|max:255',
            'broker_commission_type' => 'nullable|in:percentage,fixed',
            'broker_commission_rate' => 'nullable|numeric|min:0',
            'broker_commission_amount' => 'nullable|numeric|min:0',
            'broker_commission_paid' => 'nullable|numeric|min:0',
            'broker_commission_due' => 'nullable|numeric|min:0',
            'broker_commission_payment_mode' => 'nullable|string|max:100',
            'broker_commission_status' => 'nullable|string|max:50',
            'broker_notes'   => 'nullable|string|max:2000',
            'payment_mode'   => 'nullable|string|max:100',
            'payment_status' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:2000',
            'main_image' => 'nullable|image|max:2048',
            'document_file' => 'nullable|file|max:10240',
        ];

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
            'firm_id' => 'Firm',
            'property_name' => 'Property Name',
            'property_code' => 'Property Code',
            'status' => 'Status',
            'location' => 'Location',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'pincode' => 'Pincode',
            'description' => 'Description',
            'main_image' => 'Main Image',
            'document_file' => 'Document File',
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
