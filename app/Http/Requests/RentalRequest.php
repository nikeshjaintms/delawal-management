<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RentalRequest extends FormRequest
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
        $firmId = auth()->check() ? auth()->user()->firm_id : 0;

        $rules = [
            'firm_id'            => 'required|exists:firms,id',
            'firm_ids'           => 'nullable|array',
            'firm_ids.*'         => 'exists:firms,id',
            'property_id'        => 'required|exists:properties,id',
            'tenant_id'          => 'nullable|exists:tenants,id',
            'agreement_no'       => 'nullable|string|max:100',
            'tenant_name'        => 'required|string|max:255',
            'tenant_mobile'      => 'required|string|max:15',
            'tenant_email'       => 'nullable|email|max:255',
            'rent_amount'        => 'required|numeric|min:0.01',
            'security_deposit'   => 'nullable|numeric|min:0',
            'maintenance_amount' => 'nullable|numeric|min:0',
            'rent_start_date'    => 'required|date',
            'rent_end_date'      => 'nullable|date|after_or_equal:rent_start_date',
            'handover_date'      => 'nullable|date',
            'rent_due_date'      => 'nullable|integer|min:1|max:31',
            'lock_in_period'     => 'nullable|integer|min:0',
            'notice_period'      => 'nullable|integer|min:0',
            'meter_reading'      => 'nullable|string|max:100',
            'escalation_percent' => 'nullable|numeric|min:0|max:100',
            'payment_status'     => 'required|in:pending,partial,paid',
            'rental_status'      => 'required|in:active,completed,cancelled',
            'remarks'            => 'nullable|string|max:1000',
            'agreement_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ];

        // Replace placeholders in unique rules dynamically
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
            'property_id'        => 'Property',
            'tenant_id'          => 'Tenant',
            'agreement_no'       => 'Agreement Number',
            'tenant_name'        => 'Tenant Name',
            'tenant_mobile'      => 'Tenant Mobile',
            'tenant_email'       => 'Tenant Email',
            'rent_amount'        => 'Rent Amount',
            'security_deposit'   => 'Security Deposit',
            'maintenance_amount' => 'Maintenance Amount',
            'rent_start_date'    => 'Rent Start Date',
            'rent_end_date'      => 'Rent End Date',
            'handover_date'      => 'Handover Date',
            'rent_due_date'      => 'Rent Due Day of Month',
            'lock_in_period'     => 'Lock-in Period',
            'notice_period'      => 'Notice Period',
            'meter_reading'      => 'Starting Meter Reading',
            'escalation_percent' => 'Annual Increment %',
            'payment_status'     => 'Payment Status',
            'rental_status'      => 'Rental Status',
            'remarks'            => 'Remarks',
            'agreement_document' => 'Agreement Document',
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