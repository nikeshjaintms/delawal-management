<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreditNoteRequest extends FormRequest
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
            'credit_note_no'     => 'nullable|string|max:100',
            'credit_note_date'   => 'required|date',
            'customer_id'        => 'nullable|exists:customers,id',
            'related_invoice_no' => 'nullable|string|max:100',
            'status'             => 'required|in:Pending,Approved,Rejected',
            'reason'             => 'nullable|string|max:1000',
            'taxable_amount'     => 'required|numeric|min:0',
            'cgst_rate'          => 'nullable|numeric|min:0|max:100',
            'cgst_amount'        => 'nullable|numeric|min:0',
            'sgst_rate'          => 'nullable|numeric|min:0|max:100',
            'sgst_amount'        => 'nullable|numeric|min:0',
            'igst_rate'          => 'nullable|numeric|min:0|max:100',
            'igst_amount'        => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
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
            'credit_note_no'     => 'Credit Note Number',
            'credit_note_date'   => 'Credit Note Date',
            'customer_id'        => 'Customer',
            'related_invoice_no' => 'Related Invoice No',
            'status'             => 'Status',
            'reason'             => 'Reason',
            'taxable_amount'     => 'Taxable Amount',
            'cgst_rate'          => 'CGST Rate',
            'cgst_amount'        => 'CGST Amount',
            'sgst_rate'          => 'SGST Rate',
            'sgst_amount'        => 'SGST Amount',
            'igst_rate'          => 'IGST Rate',
            'igst_amount'        => 'IGST Amount',
            'notes'              => 'Notes',
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