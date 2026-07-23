<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FirmRequest extends FormRequest
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

        $rules = [
            'firm_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:firms,email,' . ($id ?: 'NULL') . ',id',
            'mobile' => 'required|digits:10|regex:/^[0-9]{10}$/',
            'alternate_mobile' => 'nullable|digits:10|regex:/^[0-9]{10}$/',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:10',
            'gst_no' => 'nullable|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/i',
            'pan_number' => 'nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/i',
            'firm_logo' => 'nullable|image|max:2048',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:8|same:confirm_password';
            $rules['confirm_password'] = 'required';
        } else {
            $rules['password'] = 'nullable|string|min:8|same:confirm_password';
            $rules['confirm_password'] = 'nullable';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'mobile.required' => 'Mobile number is required.',
            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'mobile.regex' => 'Only numeric digits are allowed.',
            'alternate_mobile.digits' => 'Mobile number must be exactly 10 digits.',
            'alternate_mobile.regex' => 'Only numeric digits are allowed.',
        ];
    }

    public function attributes(): array
    {
        return [
            'firm_name' => 'Firm Name',
            'owner_name' => 'Owner Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'confirm_password' => 'Confirm Password',
            'mobile' => 'Mobile Number',
            'alternate_mobile' => 'Alternate Mobile Number',
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