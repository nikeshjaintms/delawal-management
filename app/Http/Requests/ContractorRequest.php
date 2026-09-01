<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContractorRequest extends FormRequest
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
        return [
            'firm_ids'        => 'nullable|array',
            'firm_ids.*'      => 'nullable|integer|exists:firms,id',
            'firm_id'         => (auth()->user() && auth()->user()->isAdmin()) ? 'required|exists:firms,id' : 'nullable|exists:firms,id',
            'project_id'      => 'required|exists:projects,id',
            'contractor_name' => 'required|string|max:255',
            'mobile'          => 'nullable|string|max:20',
            'aadhar_no'       => 'nullable|string|max:30',
            'pan_no'          => 'nullable|string|max:30',
            'bank_name'       => 'nullable|string|max:100',
            'account_number'  => 'nullable|string|max:50',
            'ifsc_code'       => 'nullable|string|max:30',
            'branch_name'     => 'nullable|string|max:100',
            'address'         => 'nullable|string|max:1000',
            'status'          => 'required|in:active,inactive',
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id'      => 'Project',
            'contractor_name' => 'Contractor Name',
            'mobile'          => 'Mobile Number',
            'aadhar_no'       => 'Aadhar Card',
            'pan_no'          => 'PAN Card',
            'bank_name'       => 'Bank Name',
            'account_number'  => 'Account Number',
            'ifsc_code'       => 'IFSC Code',
            'branch_name'     => 'Branch Name',
            'address'         => 'Address',
            'status'          => 'Status',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->ajax() || $this->wantsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation errors occurred.',
                    'errors'  => $validator->errors()
                ], 422)
            );
        }
        parent::failedValidation($validator);
    }
}
