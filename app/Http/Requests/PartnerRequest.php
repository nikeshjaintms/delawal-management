<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'firm_id'          => 'nullable|exists:firms,id',
            'name'             => 'required|string|max:255',
            'mobile'           => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:150',
            'is_silent'        => 'nullable|boolean',
            'status'           => 'required|in:active,inactive',
            'notes'            => 'nullable|string|max:2000',
            // Optional initial share assignment on partner creation
            'initial_scope_type'         => 'nullable|in:firm,project,property_master,property',
            'initial_firm_id'            => 'nullable|exists:firms,id',
            'initial_project_id'         => 'nullable|exists:projects,id',
            'initial_property_master_id' => 'nullable|exists:property_masters,id',
            'initial_property_id'        => 'nullable|exists:properties,id',
            'initial_share_percentage'   => 'nullable|numeric|min:0.01|max:100',
            'initial_effective_from'     => 'nullable|date',
            'initial_share_notes'        => 'nullable|string|max:1000',
        ];
    }
}
