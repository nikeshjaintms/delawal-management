<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerShareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'firm_id'            => 'required|exists:firms,id',
            'scope_type'         => 'required|in:firm,project,property_master,property',
            'project_id'         => 'required_if:scope_type,project|nullable|exists:projects,id',
            'property_master_id' => 'required_if:scope_type,property_master|nullable|exists:property_masters,id',
            'property_id'        => 'required_if:scope_type,property|nullable|exists:properties,id',
            'share_percentage'   => 'required|numeric|min:0.01|max:100',
            'effective_from'     => 'nullable|date',
            'status'             => 'nullable|in:active,inactive',
            'notes'              => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required_if'         => 'Please select a Project for Project-level profit sharing.',
            'property_master_id.required_if' => 'Please select a Property Master for Property-level profit sharing.',
            'property_id.required_if'        => 'Please select an Individual Plot/Unit.',
        ];
    }
}
