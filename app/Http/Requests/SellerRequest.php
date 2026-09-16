<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firm_id'        => 'nullable|exists:firms,id',
            'name'           => 'required|string|max:255',
            'seller_type'    => 'nullable|string|in:individual,organization,joint_owner',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'mobile'         => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'pan_no'         => 'nullable|string|max:50',
            'aadhaar_no'     => 'nullable|string|max:50',
            'gst_no'         => 'nullable|string|max:50',
            'bank_name'      => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:100',
            'ifsc_code'      => 'nullable|string|max:50',
            'branch_name'    => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:1000',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
            'remarks'        => 'nullable|string|max:2000',
            'status'         => 'required|in:active,inactive',
        ];
    }
}
