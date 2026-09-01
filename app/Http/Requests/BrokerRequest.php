<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BrokerRequest extends FormRequest
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
            'firm_ids'              => 'nullable|array',
            'firm_ids.*'            => 'nullable|integer',
            'firm_id'               => 'nullable|integer',
            'project_id'            => 'nullable|integer|exists:projects,id',
            'name'                  => 'required|string|max:255',
            'mobile'                => 'required|string|min:10|max:15',
            'email'                 => 'nullable|email|max:255',
            'pan_no'                => 'nullable|string|max:20',
            'city'                  => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:1000',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'status'                => 'required|in:active,inactive',
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

    public function messages(): array
    {
        return [
            'name.required'                  => 'Broker name is required.',
            'mobile.required'                => 'Mobile number is required.',
            'mobile.min'                     => 'Mobile number must be at least 10 digits.',
            'commission_percentage.numeric'  => 'Commission percentage must be a valid number.',
            'commission_percentage.min'      => 'Commission percentage cannot be less than 0%.',
            'commission_percentage.max'      => 'Commission percentage cannot exceed 100%.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'                  => 'Broker Name',
            'mobile'                => 'Mobile Number',
            'email'                 => 'Email Address',
            'pan_no'                => 'PAN Number',
            'project_id'            => 'Project',
            'city'                  => 'City',
            'address'               => 'Address',
            'commission_percentage' => 'Commission Percentage',
            'status'                => 'Status',
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