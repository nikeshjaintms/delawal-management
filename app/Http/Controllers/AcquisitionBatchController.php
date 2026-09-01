<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcquisitionBatchRequest;
use App\Models\AcquisitionBatch;
use App\Models\PropertyMaster;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AcquisitionBatchController extends Controller
{
    public function store(AcquisitionBatchRequest $request)
    {
        $propertyMaster = PropertyMaster::findOrFail($request->property_master_id);
        $this->authoriseProperty($propertyMaster);

        $firmId = $propertyMaster->firm_id;

        // Ensure unique batch number if not provided
        $batchNumber = trim((string)$request->batch_number);
        if (empty($batchNumber)) {
            $count = AcquisitionBatch::where('property_master_id', $propertyMaster->id)->count() + 1;
            $batchNumber = 'BATCH-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            while (AcquisitionBatch::where('property_master_id', $propertyMaster->id)->where('batch_number', $batchNumber)->exists()) {
                $count++;
                $batchNumber = 'BATCH-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        }

        $documentPath = null;
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('acquisition-batches/documents', 'public');
        }

        $plotCount = (int) ($request->plot_count ?: $request->total_plots ?: 0);
        $purchaseRate = (float) $request->purchase_rate;
        $totalAmount = $request->filled('total_purchase_amount') && $request->total_purchase_amount > 0
            ? (float) $request->total_purchase_amount
            : ($purchaseRate * $plotCount);

        return DB::transaction(function () use ($request, $propertyMaster, $firmId, $batchNumber, $documentPath, $plotCount, $purchaseRate, $totalAmount) {
            // Concurrency guard: check if batch with same name already created in this property
            $existing = AcquisitionBatch::where('property_master_id', $propertyMaster->id)
                ->where('batch_name', trim($request->batch_name))
                ->first();

            if ($existing) {
                return redirect()->route('property-masters.show', $propertyMaster->id)
                    ->with('error', "Acquisition Batch '{$request->batch_name}' already exists for this property.");
            }

            $batch = AcquisitionBatch::create([
                'firm_id'               => $firmId,
                'property_master_id'    => $propertyMaster->id,
                'batch_name'            => trim($request->batch_name),
                'batch_number'          => $batchNumber,
                'purchase_date'         => $request->purchase_date,
                'purchase_rate'         => $purchaseRate,
                'rate_unit'             => $request->rate_unit ?: 'per_plot',
                'total_plots'           => $plotCount,
                'total_purchase_amount' => $totalAmount,
                'status'                => $request->status ?: 'active',
                'description'           => $request->description,
                'document_file'         => $documentPath,
                'created_by'            => auth()->id(),
                'updated_by'            => auth()->id(),
            ]);

            // Auto-generate plots if requested or plot_count > 0
            if ($plotCount > 0) {
                $prefix   = $request->plot_prefix ?: 'Plot ';
                $nextSeq  = $propertyMaster->getNextPlotSequenceNumber();
                $startNum = ($request->filled('start_number') && (int)$request->start_number > 0)
                    ? (int)$request->start_number
                    : $nextSeq;

                $size     = $request->plot_size ?: null;
                $sizeUnit = $request->plot_size_unit ?: 'sq.ft';
                $facing   = $request->plot_facing ?: null;
                $typeId   = $request->property_type_id ?: null;

                $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4));
                if (empty($propPrefix)) {
                    $propPrefix = 'PROP';
                }

                for ($i = 0; $i < $plotCount; $i++) {
                    $num = $startNum + $i;
                    $plotCode = 'P-' . $propPrefix . '-B' . $batch->id . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);

                    // Ensure unique property_code
                    if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                        $plotCode .= '-' . Str::random(3);
                    }

                    Property::create([
                        'firm_id'              => $firmId,
                        'property_master_id'   => $propertyMaster->id,
                        'acquisition_batch_id' => $batch->id,
                        'project_id'           => null, // unassigned until placed into a project
                        'property_type_id'     => $typeId,
                        'property_name'        => trim($prefix . $num),
                        'property_code'        => $plotCode,
                        'unit_no'              => (string) $num,
                        'size'                 => $size,
                        'size_unit'            => $sizeUnit,
                        'facing'               => $facing,
                        'location'             => $propertyMaster->location,
                        'city'                 => $propertyMaster->city,
                        'address'              => $propertyMaster->address,
                        'purchase_rate'        => $purchaseRate,
                        'purchase_date'        => $request->purchase_date,
                        'price'                => $purchaseRate, // default asking price to purchase rate
                        'status'               => 'available',
                        'description'          => 'Acquired under ' . $batch->batch_name . ' (' . $batch->batch_number . ') on ' . $request->purchase_date,
                    ]);
                }
            }

            return redirect()->route('property-masters.show', $propertyMaster->id)
                ->with('success', "Acquisition Batch '{$batch->batch_name}' and {$plotCount} plots created successfully.");
        });
    }

    public function show(AcquisitionBatch $acquisitionBatch)
    {
        $this->authoriseBatch($acquisitionBatch);
        $acquisitionBatch->load(['propertyMaster', 'plots.project', 'firm']);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'batch'   => $acquisitionBatch,
            ]);
        }

        return redirect()->route('property-masters.show', $acquisitionBatch->property_master_id);
    }

    public function update(Request $request, AcquisitionBatch $acquisitionBatch)
    {
        $this->authoriseBatch($acquisitionBatch);

        $validated = $request->validate([
            'batch_name'            => 'required|string|max:255',
            'batch_number'          => 'nullable|string|max:100',
            'purchase_date'         => 'required|date',
            'purchase_rate'         => 'required|numeric|min:0',
            'rate_unit'             => 'required|string|in:per_plot,per_sqft,per_sqyd',
            'total_purchase_amount' => 'nullable|numeric|min:0',
            'status'                => 'required|string|in:active,completed,archived',
            'description'           => 'nullable|string|max:2000',
            'document_file'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx|max:10240',
        ]);

        $documentPath = $acquisitionBatch->document_file;
        if ($request->hasFile('document_file')) {
            if ($acquisitionBatch->document_file) {
                Storage::disk('public')->delete($acquisitionBatch->document_file);
            }
            $documentPath = $request->file('document_file')->store('acquisition-batches/documents', 'public');
        }

        $acquisitionBatch->update([
            'batch_name'            => $validated['batch_name'],
            'batch_number'          => $validated['batch_number'] ?: $acquisitionBatch->batch_number,
            'purchase_date'         => $validated['purchase_date'],
            'purchase_rate'         => $validated['purchase_rate'],
            'rate_unit'             => $validated['rate_unit'],
            'total_purchase_amount' => $request->filled('total_purchase_amount') ? $validated['total_purchase_amount'] : $acquisitionBatch->total_purchase_amount,
            'status'                => $validated['status'],
            'description'           => $validated['description'] ?? null,
            'document_file'         => $documentPath,
            'updated_by'            => auth()->id(),
        ]);

        return redirect()->route('property-masters.show', $acquisitionBatch->property_master_id)
            ->with('success', "Acquisition Batch '{$acquisitionBatch->batch_name}' updated successfully.");
    }

    public function destroy(AcquisitionBatch $acquisitionBatch)
    {
        $this->authoriseBatch($acquisitionBatch);

        // Check if any plot in this batch has been booked or sold
        $bookedOrSold = $acquisitionBatch->plots()->whereIn('status', ['booked', 'sold'])->count();
        if ($bookedOrSold > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete this Acquisition Batch because {$bookedOrSold} plots are already booked or sold.");
        }

        $propertyMasterId = $acquisitionBatch->property_master_id;

        if ($acquisitionBatch->document_file) {
            Storage::disk('public')->delete($acquisitionBatch->document_file);
        }

        // Delete all plots attached to this batch (they are all unbooked/available)
        $acquisitionBatch->plots()->delete();
        $acquisitionBatch->delete();

        return redirect()->route('property-masters.show', $propertyMasterId)
            ->with('success', 'Acquisition Batch and associated unbooked plots deleted successfully.');
    }

    public function addPlots(Request $request, AcquisitionBatch $acquisitionBatch)
    {
        $this->authoriseBatch($acquisitionBatch);

        $request->validate([
            'plot_count'       => 'required|integer|min:1|max:500',
            'plot_prefix'      => 'nullable|string|max:50',
            'start_number'     => 'nullable|integer|min:1',
            'plot_size'        => 'nullable|string|max:50',
            'plot_size_unit'   => 'nullable|string|max:20',
            'plot_facing'      => 'nullable|string|max:50',
            'property_type_id' => 'nullable|exists:property_types,id',
            'purchase_rate'    => 'nullable|numeric|min:0',
        ]);

        $plotCount    = (int) $request->plot_count;
        $prefix       = $request->plot_prefix ?: 'Plot ';
        $propertyMaster = $acquisitionBatch->propertyMaster;
        $firmId       = $acquisitionBatch->firm_id;

        $nextSeq      = $propertyMaster->getNextPlotSequenceNumber();
        $startNum     = ($request->filled('start_number') && (int)$request->start_number > 0)
            ? (int)$request->start_number
            : $nextSeq;

        $size         = $request->plot_size ?: null;
        $sizeUnit     = $request->plot_size_unit ?: 'sq.ft';
        $facing       = $request->plot_facing ?: null;
        $typeId       = $request->property_type_id ?: null;
        $purchaseRate = $request->filled('purchase_rate') ? (float) $request->purchase_rate : (float) $acquisitionBatch->purchase_rate;

        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4));
        if (empty($propPrefix)) {
            $propPrefix = 'PROP';
        }

        DB::transaction(function () use ($acquisitionBatch, $propertyMaster, $firmId, $plotCount, $prefix, $startNum, $size, $sizeUnit, $facing, $typeId, $purchaseRate, $propPrefix) {
            for ($i = 0; $i < $plotCount; $i++) {
                $num = $startNum + $i;
                $plotCode = 'P-' . $propPrefix . '-B' . $acquisitionBatch->id . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);

                if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                Property::create([
                    'firm_id'              => $firmId,
                    'property_master_id'   => $propertyMaster->id,
                    'acquisition_batch_id' => $acquisitionBatch->id,
                    'project_id'           => null,
                    'property_type_id'     => $typeId,
                    'property_name'        => trim($prefix . $num),
                    'property_code'        => $plotCode,
                    'unit_no'              => (string) $num,
                    'size'                 => $size,
                    'size_unit'            => $sizeUnit,
                    'facing'               => $facing,
                    'location'             => $propertyMaster->location,
                    'city'                 => $propertyMaster->city,
                    'address'              => $propertyMaster->address,
                    'purchase_rate'        => $purchaseRate,
                    'purchase_date'        => $acquisitionBatch->purchase_date,
                    'price'                => $purchaseRate,
                    'status'               => 'available',
                    'description'          => 'Acquired under ' . $acquisitionBatch->batch_name . ' (' . $acquisitionBatch->batch_number . ')',
                ]);
            }

            // Update batch total plots count
            $newTotalPlots = $acquisitionBatch->plots()->count();
            $acquisitionBatch->update([
                'total_plots'           => $newTotalPlots,
                'total_purchase_amount' => $newTotalPlots * $acquisitionBatch->purchase_rate,
            ]);
        });

        return redirect()->route('property-masters.show', $acquisitionBatch->property_master_id)
            ->with('success', "{$plotCount} plots added to batch '{$acquisitionBatch->batch_name}' successfully.");
    }

    private function authoriseProperty(PropertyMaster $propertyMaster): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($propertyMaster->firm_id != $firmId) {
                abort(403, 'Unauthorized access to Property Master.');
            }
        }
    }

    private function authoriseBatch(AcquisitionBatch $batch): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($batch->firm_id != $firmId) {
                abort(403, 'Unauthorized access to Acquisition Batch.');
            }
        }
    }
}
