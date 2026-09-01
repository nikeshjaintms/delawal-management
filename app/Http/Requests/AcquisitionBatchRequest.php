<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class AcquisitionBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $batchId = $this->route('acquisition_batch') ? ($this->route('acquisition_batch')->id ?? $this->route('acquisition_batch')) : null;
        $propertyMasterId = $this->property_master_id ?: ($this->route('acquisition_batch') ? $this->route('acquisition_batch')->property_master_id : null);

        return [
            'firm_id'               => $isAdmin ? 'nullable|exists:firms,id' : 'nullable',
            'property_master_id'    => 'required|exists:property_masters,id',
            'batch_name'            => [
                'required',
                'string',
                'max:255',
                Rule::unique('acquisition_batches', 'batch_name')
                    ->where('property_master_id', $propertyMasterId)
                    ->ignore($batchId),
            ],
            'batch_number'          => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('acquisition_batches', 'batch_number')
                    ->where('property_master_id', $propertyMasterId)
                    ->ignore($batchId),
            ],
            'purchase_date'         => 'required|date',
            'purchase_rate'         => 'required|numeric|min:0',
            'rate_unit'             => 'required|string|in:per_plot,per_sqft,per_sqyd',
            'total_plots'           => 'nullable|integer|min:0',
            'total_purchase_amount' => 'nullable|numeric|min:0',
            'status'                => 'required|string|in:active,completed,archived',
            'description'           => 'nullable|string|max:2000',
            'document_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',

            // Optional instant bulk plot generation fields
            'generate_plots'        => 'nullable|boolean',
            'plot_count'            => 'nullable|integer|min:1|max:1000',
            'plot_prefix'           => 'nullable|string|max:50',
            'start_number'          => 'nullable|integer|min:1',
            'plot_size'             => 'nullable|string|max:50',
            'plot_size_unit'        => 'nullable|string|max:20',
            'property_type_id'      => 'nullable|exists:property_types,id',
            'plot_facing'           => 'nullable|string|max:50',
        ];
    }
}
