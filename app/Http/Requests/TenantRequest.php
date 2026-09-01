<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();
        if (empty($inputs['firm_id'])) {
            if (!empty($inputs['firm_ids']) && is_array($inputs['firm_ids'])) {
                $inputs['firm_id'] = $inputs['firm_ids'][0] ?? null;
            } elseif (!empty($inputs['firm_ids'])) {
                $inputs['firm_id'] = $inputs['firm_ids'];
            } else {
                $inputs['firm_id'] = auth()->check() ? auth()->user()->firm_id : session('firm_id');
            }
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

        return [
            'firm_id'         => 'required|exists:firms,id',
            'firm_ids'        => 'nullable|array',
            'firm_ids.*'      => 'exists:firms,id',
            'name'            => 'required|string|max:255',
            'mobile'          => 'required|digits:10|regex:/^[0-9]{10}$/',
            'email'           => 'nullable|email|max:255',
            'address'         => 'nullable|string|max:1000',
            'city'            => 'nullable|string|max:255',
            'identity_type'   => 'nullable|string|max:100',
            'identity_number' => 'nullable|string|max:100',
            'document_file'   => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
            'status'          => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile.regex' => 'Only numeric digits are allowed.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Tenant Name',
            'mobile' => 'Mobile Number',
            'email' => 'Email Address',
            'pan_no' => 'PAN Number',
            'address' => 'Address',
            'city' => 'City',
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