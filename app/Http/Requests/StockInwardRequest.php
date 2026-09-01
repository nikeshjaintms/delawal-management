<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockInwardRequest extends FormRequest
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
        $user = auth()->user();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $rules = [
            'inward_date'   => 'required|date',
            'contractor_id' => 'nullable|exists:contractors,id',
            'supplier_name' => 'nullable|string|max:255',
            'bill_no'       => 'nullable|string|max:255',
            'remarks'       => 'nullable|string|max:1000',
        ];

        if ($user && $user->isAdmin() && !$this->has('items')) {
            $rules['firm_id'] = 'required|exists:firms,id';
        }

        if ($this->has('items')) {
            $rules['purchase_order_id'] = 'required|exists:purchase_orders,id';
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.material_id'] = 'required|exists:materials,id';
            $rules['items.*.qty_received'] = 'required|numeric|min:0.001';
        } else {
            $rules['material_id'] = 'required|exists:materials,id';
            $rules['quantity'] = 'required|numeric|min:0.001';
            $rules['rate'] = 'nullable|numeric|min:0';
            $rules['project_id'] = 'nullable|exists:projects,id';
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
            'rate'        => 'Rate per Unit',
            'inward_date' => 'Inward Date',
            'supplier_name' => 'Supplier Name',
            'bill_no'     => 'Bill/Invoice No',
            'remarks'     => 'Remarks',
            'firm_id'     => 'Firm',
        ];
    }
}