<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $inputs = $this->all();
        // firm-select component submits firm_ids[] — extract first as firm_id
        if (empty($inputs['firm_id'])) {
            if (!empty($inputs['firm_ids']) && is_array($inputs['firm_ids'])) {
                $inputs['firm_id'] = $inputs['firm_ids'][0];
            } else {
                $inputs['firm_id'] = auth()->check() ? auth()->user()->firm_id : session('firm_id');
            }
        }

        // property-select component submits property_ids[] — extract first as property_id
        if (empty($inputs['property_id'])) {
            if (!empty($inputs['property_ids']) && is_array($inputs['property_ids'])) {
                $inputs['property_id'] = $inputs['property_ids'][0];
            }
        }

        // Handle Entire Property sale / booking scope
        if ((($inputs['sale_scope'] ?? $inputs['booking_scope'] ?? null) === 'entire') && !empty($inputs['property_master_id'])) {
            $pm = \App\Models\PropertyMaster::find($inputs['property_master_id']);
            if ($pm) {
                $entireProp = \App\Models\Property::firstOrCreate(
                    ['property_master_id' => $pm->id, 'unit_no' => null],
                    [
                        'firm_id'          => $pm->firm_id,
                        'property_name'    => $pm->property_name . ' (Entire Property)',
                        'property_code'    => $pm->property_code ? $pm->property_code . '-ENTIRE' : 'PROP-' . $pm->id . '-ENTIRE',
                        'location'         => $pm->location,
                        'city'             => $pm->city,
                        'address'          => $pm->address,
                        'size'             => $pm->total_area,
                        'size_unit'        => $pm->area_unit ?: 'sq.ft',
                        'price'            => $pm->purchase_price,
                        'purchase_rate'    => $pm->purchase_rate,
                        'purchase_date'    => $pm->purchase_date,
                        'status'           => 'available',
                        'description'      => 'Entire Property Master: ' . $pm->property_name,
                    ]
                );
                $inputs['property_id'] = $entireProp->id;
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
            'firm_id'           => 'required|exists:firms,id',
            'property_id'       => 'required|exists:properties,id',
            'customer_id'       => 'required|exists:customers,id',
            'broker_id'         => 'nullable|exists:brokers,id',
            'booking_type'      => 'required|in:booking,selling,buying',
            'booking_date'      => 'required|date',
            'total_amount'      => 'nullable|numeric|min:0',
            'discount_type'     => 'nullable|in:percentage,fixed',
            'discount_value'    => 'nullable|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
            'final_amount'      => 'nullable|numeric|min:0',
            'booking_amount'    => 'required|numeric|min:0',
            'remaining_amount'  => 'nullable|numeric|min:0',
            'payment_mode_id'   => 'nullable|exists:payment_modes,id',
            'payment_mode'      => 'nullable|string|max:100',
            'transaction_ref'   => 'nullable|string|max:255',
            'agreement_date'    => 'nullable|date',
            'status'            => 'required|in:pending,confirmed,cancelled',
            'payment_status'    => 'required|in:unpaid,partial,paid',
            'remarks'           => 'nullable|string|max:1000',
            'commission_type'   => 'nullable|in:percentage,fixed',
            'commission_value'  => 'nullable|numeric|min:0',
            'commission_amount' => 'nullable|numeric|min:0',
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
            'property_id'      => 'Property',
            'customer_id'      => 'Customer',
            'broker_id'        => 'Broker',
            'booking_date'     => 'Booking Date',
            'total_amount'     => 'Total Property Price',
            'discount_type'    => 'Discount Type',
            'discount_value'   => 'Discount Value',
            'discount_amount'  => 'Discount Amount',
            'final_amount'     => 'Final Net Amount',
            'booking_amount'   => 'Payment / Booking Amount',
            'remaining_amount' => 'Remaining Amount',
            'payment_mode_id'  => 'Payment Mode',
            'payment_mode'     => 'Payment Mode',
            'transaction_ref'  => 'Transaction Reference',
            'agreement_date'   => 'Agreement Date',
            'status'           => 'Booking Status',
            'payment_status'   => 'Payment Status',
            'remarks'          => 'Remarks',
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