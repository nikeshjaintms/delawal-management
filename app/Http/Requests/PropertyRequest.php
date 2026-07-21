<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PropertyRequest extends FormRequest
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
        $firmId = $this->get('firm_id') ?: (auth()->check() && auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $rules = [
            'firm_id' => (auth()->user() && auth()->user()->isAdmin()) ? 'required|exists:firms,id' : 'nullable|exists:firms,id',
            'property_name' => 'required|string|max:255',
            'property_type_id' => 'required|exists:property_types,id',
            'property_code' => 'required|string|max:100|unique:properties,property_code,{ID},id,firm_id,{FIRM_ID}',
            'status' => 'required|in:available,booked,sold,rented',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
            'size' => 'nullable|string|max:50',
            'size_unit' => 'nullable|string|max:30',
            'price' => 'nullable|numeric|min:0',
            'unit_no' => 'nullable|string|max:50',
            'floor_no' => 'nullable|string|max:50',
            'facing' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'main_image' => 'nullable',
            'document_file' => 'nullable',
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
            'property_name' => 'Property Name',
            'property_type_id' => 'Property Type',
            'property_code' => 'Property Code',
            'status' => 'Status',
            'location' => 'Location',
            'city' => 'City',
            'address' => 'Address',
            'size' => 'Size',
            'size_unit' => 'Size Unit',
            'price' => 'Price',
            'unit_no' => 'Unit Number',
            'floor_no' => 'Floor Number',
            'facing' => 'Facing',
            'description' => 'Description',
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