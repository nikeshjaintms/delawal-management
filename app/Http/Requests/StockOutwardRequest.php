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
            'outward_date' => 'required|date',
            'remarks'      => 'nullable|string|max:1000',
        ];

        if ($this->has('items')) {
            $rules['stock_inward_number'] = 'required|string';
            $rules['property_id']         = 'required|exists:properties,id';
            $rules['items']               = 'required|array|min:1';
            $rules['items.*.material_id'] = 'required|exists:materials,id';
            $rules['items.*.qty_dispatch'] = 'required|numeric|min:0.001';
        } else {
            $rules['material_id'] = 'required|exists:materials,id';
            $rules['quantity']    = 'required|numeric|min:0.001';
            $rules['property_id'] = 'nullable|exists:properties,id';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'material_id' => 'Material',
            'quantity'    => 'Quantity',
            'outward_date'=> 'Outward Date',
            'remarks'     => 'Remarks',
        ];
    }
}