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

    public function prepareForValidation()
    {
        $inputs = $this->all();
        foreach ($inputs as $key => $value) {
            if (is_string($value)) {
                $inputs[$key] = trim($value);
            }
        }

        if (!empty($inputs['property_master_id'])) {
            $baseName = !empty($inputs['batch_name']) ? trim($inputs['batch_name']) : ('Batch ' . (\App\Models\AcquisitionBatch::where('property_master_id', $inputs['property_master_id'])->count() + 1));
            
            $batchName = $baseName;
            $batchId = $this->route('acquisition_batch') ? ($this->route('acquisition_batch')->id ?? $this->route('acquisition_batch')) : null;
            
            $existing = \App\Models\AcquisitionBatch::where('property_master_id', $inputs['property_master_id'])
                ->where('batch_name', $batchName);
            if ($batchId) {
                $existing->where('id', '!=', $batchId);
            }
            
            if ($existing->exists()) {
                $count = \App\Models\AcquisitionBatch::where('property_master_id', $inputs['property_master_id'])->count() + 1;
                $batchName = $baseName . ' (' . $count . ')';
                while (\App\Models\AcquisitionBatch::where('property_master_id', $inputs['property_master_id'])->where('batch_name', $batchName)->exists()) {
                    $count++;
                    $batchName = $baseName . ' (' . $count . ')';
                }
            }
            $inputs['batch_name'] = $batchName;
        }

        if (isset($inputs['plot_source'])) {
            $src = strtolower(trim((string)$inputs['plot_source']));
            if (in_array($src, ['excel', 'excel_import', 'import', 'upload'])) {
                $inputs['plot_source'] = 'excel';
            } elseif (in_array($src, ['none', 'batch_only', 'no_plots'])) {
                $inputs['plot_source'] = 'none';
                $inputs['plot_count'] = 0;
            } else {
                $inputs['plot_source'] = 'generator';
            }
        }

        if (isset($inputs['generate_plots']) && ($inputs['generate_plots'] === '0' || $inputs['generate_plots'] === false || $inputs['generate_plots'] === 'false')) {
            $inputs['plot_count'] = 0;
            $inputs['plot_source'] = 'none';
        }

        if (!isset($inputs['plot_count']) || $inputs['plot_count'] === '') {
            $inputs['plot_count'] = isset($inputs['total_plots']) ? (int)$inputs['total_plots'] : 0;
        }

        if (empty($inputs['status'])) {
            $inputs['status'] = 'active';
        }

        if (empty($inputs['rate_unit'])) {
            $inputs['rate_unit'] = 'per_plot';
        }

        $this->replace($inputs);
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

            // Optional instant bulk plot generation / Excel import fields
            'plot_source'           => 'nullable|string|in:generator,direct_generator,excel,excel_import,none,batch_only',
            'excel_file'            => 'nullable|file|mimes:xlsx,xls,csv,txt|max:10240',
            'generate_plots'        => 'nullable',
            'plot_count'            => 'nullable|integer|min:0|max:1000',
            'plot_prefix'           => 'nullable|string|max:50',
            'start_number'          => 'nullable|integer|min:1',
            'plot_size'             => 'nullable|max:50',
            'plot_size_unit'        => 'nullable|string|max:20',
            'property_type_id'      => 'nullable|exists:property_types,id',
            'plot_facing'           => 'nullable|string|max:50',
        ];
    }
}
