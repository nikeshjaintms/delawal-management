<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockOutwardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();

        if (isset($inputs['property_ids']) && is_array($inputs['property_ids'])) {
            $filtered = array_values(array_filter($inputs['property_ids']));
            $inputs['property_id'] = $filtered[0] ?? null;
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
        $rules = [
            'outward_date'   => 'required|date',
            'project_id'     => 'nullable|exists:projects,id',
            'property_id'    => 'nullable|exists:properties,id',
            'property_ids'   => 'nullable|array',
            'property_ids.*' => 'nullable|exists:properties,id',
            'contractor_id'  => 'nullable|exists:contractors,id',
            'remarks'        => 'nullable|string|max:1000',
        ];

        if ($this->has('items')) {
            $rules['stock_inward_number'] = 'required|string';
            $rules['items']               = 'required|array|min:1';
            $rules['items.*.material_id'] = 'required|exists:materials,id';
            $rules['items.*.qty_dispatch'] = 'required|numeric|min:0.001';
        } else {
            $rules['material_id'] = 'required|exists:materials,id';
            $rules['quantity']    = 'nullable|numeric|min:0';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'material_id'   => 'Material',
            'project_id'    => 'Project',
            'property_id'   => 'Unit / Plot',
            'contractor_id' => 'Contractor',
            'quantity'      => 'Quantity',
            'outward_date'  => 'Outward Date',
            'remarks'       => 'Remarks',
        ];
    }
}