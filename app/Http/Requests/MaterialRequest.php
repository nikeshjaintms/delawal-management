<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaterialRequest extends FormRequest
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
        $user = auth()->user();
        $firmId = $user ? $user->firm_id : 0;

        $rules = [
            'project_id'           => 'nullable|exists:projects,id',
            'contractor_id'        => 'nullable|exists:contractors,id',
            'material_category_id' => 'nullable',
            'custom_category'      => 'nullable|string|max:100',
            'material_name'        => 'nullable|string|max:255',
            'specification'        => 'nullable|string|max:255',
            'unit'                 => 'required|string|max:50',
            'opening_stock'        => 'nullable|numeric|min:0',
            'unit_price'           => 'nullable|numeric|min:0',
            'total_price'          => 'nullable|numeric|min:0',
            'minimum_stock'        => 'nullable|numeric|min:0',
            'description'          => 'nullable|string|max:1000',
            'status'               => 'required|in:active,inactive',
        ];

        if ($user && $user->isAdmin()) {
            $rules['firm_ids'] = 'required|array|min:1';
            $rules['firm_ids.*'] = 'exists:firms,id';
        }

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
            'project_id'           => 'Project',
            'contractor_id'        => 'Contractor',
            'material_category_id' => 'Material Category',
            'material_name'        => 'Material Name',
            'specification'        => 'Specification / Size',
            'unit'                 => 'Unit of Measure',
            'description'          => 'Description',
            'status'               => 'Status',
            'firm_id'              => 'Firm',
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