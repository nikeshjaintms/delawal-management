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
            'status' => 'required|in:active,inactive',
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
