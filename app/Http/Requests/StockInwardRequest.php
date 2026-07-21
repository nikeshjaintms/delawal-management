<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StockInwardRequest extends FormRequest
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
            'material_id' => 'required|exists:materials,id',
            'property_id' => 'nullable|exists:properties,id',
            'quantity' => 'required|numeric|min:0.001',
            'rate' => 'nullable|numeric|min:0',
            'inward_date' => 'required|date',
            'supplier_name' => 'nullable|string|max:255',
            'bill_no' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:1000',
        ];

        if ($user && $user->isAdmin()) {
            $rules['firm_id'] = 'required|exists:firms,id';
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
            'material_id' => 'Material',
            'property_id' => 'Property',
            'quantity' => 'Quantity',
            'rate' => 'Rate per Unit',
            'inward_date' => 'Inward Date',
            'supplier_name' => 'Supplier Name',
            'bill_no' => 'Bill/Invoice No',
            'remarks' => 'Remarks',
            'firm_id' => 'Firm',
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