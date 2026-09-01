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

        if (isset($inputs['firm_ids']) && is_array($inputs['firm_ids']) && !empty($inputs['firm_ids'])) {
            $inputs['firm_id'] = (int) $inputs['firm_ids'][0];
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
            'outward_date'  => 'required|date',
            'contractor_id' => 'nullable|exists:contractors,id',
            'remarks'       => 'nullable|string|max:1000',
        ];

        if ($this->has('items')) {
            $rules['stock_inward_number'] = 'required|string';
            $rules['project_id']         = 'nullable|exists:projects,id';
            $rules['property_id']        = 'nullable';
            $rules['items']               = 'required|array|min:1';
            $rules['items.*.material_id'] = 'required|exists:materials,id';
            $rules['items.*.qty_dispatch'] = 'required|numeric|min:0.001';
        } else {
            $rules['material_id'] = 'required|exists:materials,id';
            $rules['quantity']    = 'required|numeric|min:0.001';
            $rules['project_id']  = 'nullable|exists:projects,id';
            $rules['property_id'] = 'nullable';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'material_id' => 'Material',
            'project_id'  => 'Project',
            'quantity'    => 'Quantity',
            'outward_date'=> 'Outward Date',
            'remarks'     => 'Remarks',
        ];
    }
}