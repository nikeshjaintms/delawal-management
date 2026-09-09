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
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Acquisition Plots Template');

        $headers = [
            'Unit No / Plot No*', 'Plot Name*', 'Size / Area', 'Size Unit',
            'Facing', 'Property Type', 'Purchase Rate', 'Price (Asking Price)', 'Description / Notes'
        ];

        $sheet->fromArray([$headers], null, 'A1');

        $sampleData = [
            ['1', 'Plot 1', '1200', 'sq.ft', 'East', 'Plot', '1200.00', '1500000.00', 'Corner road facing plot'],
            ['2', 'Plot 2', '1200', 'sq.ft', 'West', 'Plot', '1200.00', '1450000.00', 'Standard layout plot'],
            ['3', 'Plot 3', '1500', 'sq.ft', 'North', 'Plot', '1200.00', '1800000.00', 'Garden facing premium plot'],
            ['4', 'Plot 4', '1800', 'sq.ft', 'East', 'Commercial', '1500.00', '2500000.00', 'Commercial main road plot'],
            ['5', 'Plot 5', '200', 'sq.yard', 'South', 'Plot', '10800.00', '2200000.00', 'Wide road plot'],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B']
            ],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ]);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Acquisition_Plots_Template.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

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

        $plotSource = $request->input('plot_source', $request->hasFile('excel_file') ? 'excel' : ($request->input('generate_plots', '1') == '0' ? 'none' : 'generator'));
        $plotCount = ($plotSource === 'none')
            ? 0
            : (int) ($request->plot_count ?: $request->total_plots ?: 0);

        $purchaseRate = (float) ($request->purchase_rate ?: 0);
        $totalSqft    = (float) ($request->total_sqft ?: 0);
        $plotSize     = (float) ($request->plot_size ?: 0);
        $rateUnit     = $request->rate_unit ?: 'per_plot';

        if ($request->filled('total_purchase_amount') && (float)$request->total_purchase_amount > 0) {
            $totalAmount = (float) $request->total_purchase_amount;
        } elseif ($rateUnit === 'per_sqft' && ($totalSqft > 0 || ($plotCount > 0 && $plotSize > 0))) {
            $calcSqft = $totalSqft > 0 ? $totalSqft : ($plotCount * $plotSize);
            $totalAmount = $purchaseRate * $calcSqft;
        } elseif ($rateUnit === 'per_sqyd' && ($totalSqft > 0 || ($plotCount > 0 && $plotSize > 0))) {
            $calcSqft = $totalSqft > 0 ? $totalSqft : ($plotCount * $plotSize);
            $totalAmount = $purchaseRate * ($calcSqft / 9);
        } else {
            $totalAmount = $purchaseRate * $plotCount;
        }

        return DB::transaction(function () use ($request, $propertyMaster, $firmId, $batchNumber, $documentPath, $plotSource, $plotCount, $purchaseRate, $totalAmount) {
            $batchName = trim((string)$request->batch_name);
            if (empty($batchName)) {
                $batchName = 'Batch ' . (AcquisitionBatch::where('property_master_id', $propertyMaster->id)->count() + 1);
            }

            // Ensure unique batch_name per property master
            $baseName = $batchName;
            $nameCount = 1;
            while (AcquisitionBatch::where('property_master_id', $propertyMaster->id)->where('batch_name', $batchName)->exists()) {
                $nameCount++;
                $batchName = $baseName . ' (' . $nameCount . ')';
            }

            $batch = AcquisitionBatch::create([
                'firm_id'               => $firmId,
                'property_master_id'    => $propertyMaster->id,
                'batch_name'            => $batchName,
                'batch_number'          => $batchNumber,
                'purchase_date'         => $request->purchase_date ?: date('Y-m-d'),
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

            $finalPlotCount = $plotCount;

            // 1. OPTION A: Import from Excel file
            if (($plotSource === 'excel' || $request->hasFile('excel_file')) && $request->file('excel_file')) {
                $importRes = $this->importPlotsFromExcel($request->file('excel_file'), $propertyMaster, $firmId, $batch->id, $purchaseRate, $batch->purchase_date);
                $finalPlotCount = $importRes['count'];
                $batch->update([
                    'total_plots'           => $finalPlotCount,
                    'total_purchase_amount' => $finalPlotCount * $purchaseRate,
                ]);
            }
            // 2. OPTION B: Direct Plot Generator
            elseif ($plotSource === 'generator' && $plotCount > 0) {
                $prefix   = $request->plot_prefix ?: 'Plot ';
                $nextSeq  = $propertyMaster->getNextPlotSequenceNumber();
                $startNum = ($request->filled('start_number') && (int)$request->start_number > 0)
                    ? (int)$request->start_number
                    : $nextSeq;

                $size     = $request->plot_size ?: null;
                $sizeUnit = $request->plot_size_unit ?: 'sq.ft';
                $facing   = $request->plot_facing ?: null;
                $typeId   = $request->property_type_id ?: null;

                $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';

                for ($i = 0; $i < $plotCount; $i++) {
                    $num = $startNum + $i;
                    $plotCode = 'P-' . $propPrefix . '-B' . $batch->id . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);

                    if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                        $plotCode .= '-' . Str::random(3);
                    }

                    Property::create([
                        'firm_id'              => $firmId,
                        'property_master_id'   => $propertyMaster->id,
                        'acquisition_batch_id' => $batch->id,
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
                        'purchase_date'        => $batch->purchase_date,
                        'price'                => $purchaseRate,
                        'status'               => 'available',
                        'description'          => 'Acquired under ' . $batch->batch_name . ' (' . $batch->batch_number . ') on ' . $batch->purchase_date,
                    ]);
                }
            }

            return redirect()->route('property-masters.show', $propertyMaster->id)
                ->with('success', "Acquisition Batch '{$batch->batch_name}' and {$finalPlotCount} plots created successfully.");
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
            'plot_source'      => 'nullable|string|in:generator,direct_generator,excel,excel_import,none,batch_only',
            'excel_file'       => 'nullable|file|mimes:xlsx,xls,csv,txt|max:10240',
            'plot_count'       => 'nullable|integer|min:1|max:500',
            'plot_prefix'      => 'nullable|string|max:50',
            'start_number'     => 'nullable|integer|min:1',
            'plot_size'        => 'nullable|max:50',
            'plot_size_unit'   => 'nullable|string|max:20',
            'plot_facing'      => 'nullable|string|max:50',
            'property_type_id' => 'nullable|exists:property_types,id',
            'purchase_rate'    => 'nullable|numeric|min:0',
        ]);

        $propertyMaster = $acquisitionBatch->propertyMaster;
        $firmId         = $acquisitionBatch->firm_id;
        $purchaseRate   = $request->filled('purchase_rate') ? (float) $request->purchase_rate : (float) $acquisitionBatch->purchase_rate;
        
        $rawPlotSource  = strtolower(trim((string)$request->input('plot_source', '')));
        if (in_array($rawPlotSource, ['excel', 'excel_import', 'import', 'upload']) || $request->hasFile('excel_file')) {
            $plotSource = 'excel';
        } else {
            $plotSource = 'generator';
        }

        // OPTION 1: Import plots from Excel
        if (($plotSource === 'excel' || $request->hasFile('excel_file')) && $request->file('excel_file')) {
            $importRes = $this->importPlotsFromExcel($request->file('excel_file'), $propertyMaster, $firmId, $acquisitionBatch->id, $purchaseRate, $acquisitionBatch->purchase_date);
            $addedCount = $importRes['count'];

            $newTotalPlots = $acquisitionBatch->plots()->count();
            $acquisitionBatch->update([
                'total_plots'           => $newTotalPlots,
                'total_purchase_amount' => $newTotalPlots * $acquisitionBatch->purchase_rate,
            ]);

            return redirect()->route('property-masters.show', $acquisitionBatch->property_master_id)
                ->with('success', "{$addedCount} plots imported from Excel into batch '{$acquisitionBatch->batch_name}' successfully.");
        }

        // OPTION 2: Direct Generator
        $plotCount    = (int) ($request->plot_count ?: 1);
        $prefix       = $request->plot_prefix ?: 'Plot ';

        $nextSeq      = $propertyMaster->getNextPlotSequenceNumber();
        $startNum     = ($request->filled('start_number') && (int)$request->start_number > 0)
            ? (int)$request->start_number
            : $nextSeq;

        $size         = $request->plot_size ?: null;
        $sizeUnit     = $request->plot_size_unit ?: 'sq.ft';
        $facing       = $request->plot_facing ?: null;
        $typeId       = $request->property_type_id ?: null;

        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';

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

    private function importPlotsFromExcel($uploadedFile, PropertyMaster $propertyMaster, $firmId, $batchId, $defaultPurchaseRate, $defaultPurchaseDate): array
    {
        $path = $uploadedFile->getRealPath();
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['xlsx', 'xls']) && class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);
            } catch (\Throwable $e) {
                $rows = [];
            }
        }

        if (empty($rows)) {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle, 3000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (empty($rows)) {
            return ['count' => 0, 'total_area' => 0];
        }

        // 1. Locate header row dynamically within first 10 rows
        $headerRow = [];
        $dataStartRowIndex = 0;
        $knownHeaderKeywords = ['unit', 'plot', 'size', 'area', 'sqft', 'sqyd', 'facing', 'rate', 'price', 'type', 'code', 'name', 'status', 'firm', 'desc', 'location', 'city', 'address'];

        foreach ($rows as $rowIndex => $rowCells) {
            if ($rowIndex > 10) break;
            if (!is_array($rowCells)) continue;

            $matchedCount = 0;
            foreach ($rowCells as $cell) {
                if (is_null($cell)) continue;
                $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$cell));
                foreach ($knownHeaderKeywords as $kw) {
                    if (str_contains($clean, $kw)) {
                        $matchedCount++;
                        break;
                    }
                }
            }

            if ($matchedCount >= 2) {
                $headerRow = $rowCells;
                $dataStartRowIndex = $rowIndex + 1;
                break;
            }
        }

        // If no explicit header row was detected, default to row 0 as headers if rows > 1
        if (empty($headerRow)) {
            $headerRow = $rows[0] ?? [];
            $dataStartRowIndex = 1;
        }

        // 2. Build column mapping dictionary with exhaustive synonyms
        $columnMap = [];
        $exactDict = [
            'firm'          => ['firm', 'firmname', 'company', 'companyname'],
            'plot_code'     => ['code', 'plotcode', 'plotcode*', 'propertycode', 'propertycode*', 'unitcode', 'propcode', 'plot#', 'unit#'],
            'plot_name'     => ['propertyname', 'propertyname*', 'plotname', 'plotname*', 'name', 'title', 'unitname'],
            'project'       => ['project', 'projectname', 'projectname*', 'projectcode', 'mastername', 'propertymaster'],
            'city'          => ['city', 'town'],
            'location'      => ['location', 'loc', 'landmark'],
            'address'       => ['address', 'addr'],
            'size'          => ['size', 'area', 'plotsize', 'plotarea', 'sizearea', 'size/area', 'sqft', 'areainsqft', 'areainsqyd', 'carpetarea', 'builtuparea', 'superarea', 'dimensionsize', 'dimension', 'sqyards', 'sqmeter', 'acre', 'bigha', 'plotareainsqft'],
            'size_unit'     => ['sizeunit', 'measurementunit', 'areatype', 'areauom', 'uom', 'unittype'],
            'price'         => ['price', 'price(inr)', 'priceinr', 'askingprice', 'priceaskingprice', 'sellingprice', 'saleprice', 'amount', 'cost', 'value'],
            'purchase_rate' => ['purchaserate', 'buyrate', 'originalpurchaserate', 'rate', 'costrate', 'batchrate', 'purchaserateperunit'],
            'status'        => ['status', 'propertystatus', 'propertystatus*', 'state'],
            'image'         => ['image', 'mainimage', 'propertyimage', 'photo', 'img', 'imagefilename'],
            'unit_no'       => ['unitno', 'unitnumber', 'plotno', 'unitnoplotno', 'unitnoplotno*', 'flatno'],
            'facing'        => ['facing', 'direction', 'orientation', 'plotfacing'],
            'type'          => ['propertytype', 'propertytype*', 'proptype', 'type', 'category', 'kind', 'projecttype'],
            'description'   => ['description', 'descriptionnotes', 'notes', 'remarks', 'details', 'propertydescription'],
        ];

        foreach ($headerRow as $colIdx => $rawHeader) {
            $cleanBom = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', (string)$rawHeader);
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanBom));
            if (empty($norm)) continue;

            foreach ($exactDict as $fieldKey => $validNorms) {
                if (!isset($columnMap[$fieldKey]) && in_array($norm, $validNorms, true)) {
                    $columnMap[$fieldKey] = $colIdx;
                    break;
                }
            }
        }

        // Substring fallback matching for unmapped critical fields
        foreach ($headerRow as $colIdx => $rawHeader) {
            $cleanBom = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', (string)$rawHeader);
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanBom));
            if (empty($norm)) continue;

            if (!isset($columnMap['project']) && str_contains($norm, 'project')) {
                $columnMap['project'] = $colIdx;
            } elseif (!isset($columnMap['size']) && (str_contains($norm, 'size') || str_contains($norm, 'area') || str_contains($norm, 'sqft'))) {
                $columnMap['size'] = $colIdx;
            } elseif (!isset($columnMap['size_unit']) && str_contains($norm, 'unit') && !str_contains($norm, 'unitno') && !str_contains($norm, 'unitnumber')) {
                $columnMap['size_unit'] = $colIdx;
            } elseif (!isset($columnMap['plot_code']) && str_contains($norm, 'code')) {
                $columnMap['plot_code'] = $colIdx;
            } elseif (!isset($columnMap['plot_name']) && str_contains($norm, 'name')) {
                $columnMap['plot_name'] = $colIdx;
            } elseif (!isset($columnMap['unit_no']) && (str_contains($norm, 'unit') || str_contains($norm, 'plot'))) {
                $columnMap['unit_no'] = $colIdx;
            } elseif (!isset($columnMap['facing']) && str_contains($norm, 'facing')) {
                $columnMap['facing'] = $colIdx;
            } elseif (!isset($columnMap['purchase_rate']) && str_contains($norm, 'rate')) {
                $columnMap['purchase_rate'] = $colIdx;
            } elseif (!isset($columnMap['price']) && (str_contains($norm, 'price') || str_contains($norm, 'amount'))) {
                $columnMap['price'] = $colIdx;
            } elseif (!isset($columnMap['image']) && (str_contains($norm, 'image') || str_contains($norm, 'photo') || str_contains($norm, 'img'))) {
                $columnMap['image'] = $colIdx;
            } elseif (!isset($columnMap['type']) && str_contains($norm, 'type')) {
                $columnMap['type'] = $colIdx;
            }
        }

        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';
        $propertyTypes = \App\Models\PropertyType::pluck('id', 'name')->toArray();

        $createdPlots = 0;
        $totalArea = 0;

        $totalRows = count($rows);
        for ($r = $dataStartRowIndex; $r < $totalRows; $r++) {
            $row = $rows[$r];
            if (!is_array($row) || empty(array_filter($row, fn($val) => $val !== null && trim((string)$val) !== ''))) {
                continue;
            }

            // 1. Code, Unit No & Plot Name
            $rawCode = isset($columnMap['plot_code']) && isset($row[$columnMap['plot_code']]) ? trim((string)$row[$columnMap['plot_code']]) : '';
            $rawName = isset($columnMap['plot_name']) && isset($row[$columnMap['plot_name']]) ? trim((string)$row[$columnMap['plot_name']]) : (isset($row[1]) ? trim((string)$row[1]) : '');
            $rawUnit = isset($columnMap['unit_no']) && isset($row[$columnMap['unit_no']]) ? trim((string)$row[$columnMap['unit_no']]) : (isset($row[0]) ? trim((string)$row[0]) : '');

            $unitNo = $rawUnit !== '' ? $rawUnit : ($rawCode !== '' ? $rawCode : (string)($propertyMaster->getNextPlotSequenceNumber() + $createdPlots));
            $plotName = $rawName !== '' ? $rawName : ($rawCode !== '' ? 'Plot ' . $rawCode : 'Plot ' . $unitNo);

            // 2. Project Resolution
            $projectInput = isset($columnMap['project']) && isset($row[$columnMap['project']]) ? trim((string)$row[$columnMap['project']]) : '';
            $projectId = null;
            if (!empty($projectInput)) {
                $matchedProject = \App\Models\Project::where('firm_id', $firmId)
                    ->where(function($q) use ($projectInput) {
                        $q->where('project_name', $projectInput)
                          ->orWhere('project_code', $projectInput);
                    })->first();
                if ($matchedProject) {
                    $projectId = $matchedProject->id;
                }
            }
            if (!$projectId) {
                $projectId = $propertyMaster->projects->first()?->id;
            }

            // 3. Size & Size Unit Parsing (Preserve exact string e.g. "1255 sq.ft Built Up")
            $rawSize = isset($columnMap['size']) && isset($row[$columnMap['size']]) ? trim((string)$row[$columnMap['size']]) : (isset($row[2]) ? trim((string)$row[2]) : '');
            $rawUnitStr = isset($columnMap['size_unit']) && isset($row[$columnMap['size_unit']]) ? trim((string)$row[$columnMap['size_unit']]) : (isset($row[3]) ? trim((string)$row[3]) : '');

            $rawSize = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $rawSize);
            $rawUnitStr = str_replace(["\xc2\xa0", '&nbsp;'], ' ', $rawUnitStr);

            // Anti-pollution validation: never let project name or firm name enter size or size_unit
            if (!empty($projectInput)) {
                if (strcasecmp($rawSize, $projectInput) === 0) {
                    $rawSize = '';
                }
                if (strcasecmp($rawUnitStr, $projectInput) === 0) {
                    $rawUnitStr = '';
                }
            }

            $size = null;
            $sizeUnit = null;

            if ($rawSize !== '') {
                // Exact string preservation
                $size = $rawSize;

                // If size already contains descriptive text/unit (e.g. "1255 sq.ft Built Up"), sizeUnit is kept null
                if (preg_match('/[a-zA-Z]/', $rawSize)) {
                    $sizeUnit = null;
                } elseif ($rawUnitStr !== '') {
                    $normUnit = strtolower(preg_replace('/[^a-zA-Z]/', '', $rawUnitStr));
                    if (in_array($normUnit, ['sqft', 'sqfeet', 'squarefeet', 'feet', 'ft'])) {
                        $sizeUnit = 'sq.ft';
                    } elseif (in_array($normUnit, ['sqyd', 'sqyard', 'sqyards', 'squareyard', 'squareyards', 'gaj', 'var'])) {
                        $sizeUnit = 'sq.yard';
                    } elseif (in_array($normUnit, ['sqmt', 'sqmeter', 'sqmeters', 'squaremeter', 'sqm', 'meter', 'mtr'])) {
                        $sizeUnit = 'sq.meter';
                    } elseif (in_array($normUnit, ['acre', 'acres'])) {
                        $sizeUnit = 'acre';
                    } elseif (in_array($normUnit, ['bigha', 'vigha', 'bighas'])) {
                        $sizeUnit = 'bigha';
                    } else {
                        $sizeUnit = trim($rawUnitStr);
                    }
                } else {
                    $sizeUnit = 'sq.ft';
                }
            }

            // 4. Facing Direction Validation
            $rawFacing = isset($columnMap['facing']) && isset($row[$columnMap['facing']]) ? trim((string)$row[$columnMap['facing']]) : (isset($row[4]) ? trim((string)$row[4]) : '');
            $facing = null;
            if ($rawFacing !== '') {
                $facingLower = strtolower(preg_replace('/[^a-zA-Z\-]/', '', str_replace(' ', '-', $rawFacing)));
                $validFacings = [
                    'east'       => 'East',
                    'west'       => 'West',
                    'north'      => 'North',
                    'south'      => 'South',
                    'north-east' => 'North-East',
                    'northeast'  => 'North-East',
                    'ne'         => 'North-East',
                    'north-west' => 'North-West',
                    'northwest'  => 'North-West',
                    'nw'         => 'North-West',
                    'south-east' => 'South-East',
                    'southeast'  => 'South-East',
                    'se'         => 'South-East',
                    'south-west' => 'South-West',
                    'southwest'  => 'South-West',
                    'sw'         => 'South-West',
                ];
                if (isset($validFacings[$facingLower])) {
                    $facing = $validFacings[$facingLower];
                }
            }

            // 5. Property Type
            $typeName = isset($columnMap['type']) && isset($row[$columnMap['type']]) ? trim((string)$row[$columnMap['type']]) : (isset($row[5]) ? trim((string)$row[5]) : null);
            $typeId = null;
            if ($typeName) {
                foreach ($propertyTypes as $name => $id) {
                    if (strcasecmp($name, $typeName) === 0 || str_contains(strtolower($typeName), strtolower($name))) {
                        $typeId = $id;
                        break;
                    }
                }
            }
            if (!$typeId) {
                foreach ($propertyTypes as $name => $id) {
                    if (strcasecmp($name, 'Plot') === 0) {
                        $typeId = $id;
                        break;
                    }
                }
                if (!$typeId && !empty($propertyTypes)) {
                    $typeId = reset($propertyTypes);
                }
            }

            // 6. Purchase Rate and Price
            $rawRate = isset($columnMap['purchase_rate']) && isset($row[$columnMap['purchase_rate']]) ? trim((string)$row[$columnMap['purchase_rate']]) : (isset($row[6]) ? trim((string)$row[6]) : '');
            $cleanedRate = preg_replace('/[^\d.]/', '', str_replace(',', '', $rawRate));
            $purchaseRate = ($cleanedRate !== '' && is_numeric($cleanedRate)) ? (float)$cleanedRate : $defaultPurchaseRate;

            $rawPrice = isset($columnMap['price']) && isset($row[$columnMap['price']]) ? trim((string)$row[$columnMap['price']]) : (isset($row[7]) ? trim((string)$row[7]) : '');
            $cleanedPrice = preg_replace('/[^\d.]/', '', str_replace(',', '', $rawPrice));
            $price = ($cleanedPrice !== '' && is_numeric($cleanedPrice)) ? (float)$cleanedPrice : $purchaseRate;

            // 7. Location, City, Address
            $rawLoc = isset($columnMap['location']) && isset($row[$columnMap['location']]) ? trim((string)$row[$columnMap['location']]) : '';
            $location = $rawLoc !== '' ? $rawLoc : $propertyMaster->location;

            $rawCity = isset($columnMap['city']) && isset($row[$columnMap['city']]) ? trim((string)$row[$columnMap['city']]) : '';
            $city = $rawCity !== '' ? $rawCity : $propertyMaster->city;

            $rawAddr = isset($columnMap['address']) && isset($row[$columnMap['address']]) ? trim((string)$row[$columnMap['address']]) : '';
            $address = $rawAddr !== '' ? $rawAddr : $propertyMaster->address;

            // 8. Status
            $rawStatus = isset($columnMap['status']) && isset($row[$columnMap['status']]) ? strtolower(trim((string)$row[$columnMap['status']])) : 'available';
            $status = in_array($rawStatus, ['available', 'booked', 'sold', 'rented']) ? $rawStatus : 'available';

            // 9. Image & Description
            $rawImage = isset($columnMap['image']) && isset($row[$columnMap['image']]) ? trim((string)$row[$columnMap['image']]) : '';
            $rawDesc = isset($columnMap['description']) && isset($row[$columnMap['description']]) ? trim((string)$row[$columnMap['description']]) : (isset($row[8]) ? trim((string)$row[8]) : '');
            $description = $rawDesc !== '' ? $rawDesc : ('Imported via Excel for Batch #' . $batchId);
            $mainImage = !empty($rawImage) ? $rawImage : null;

            // 10. Plot Code
            if (!empty($rawCode)) {
                $plotCode = $rawCode;
            } else {
                $plotCode = 'P-' . $propPrefix . '-B' . $batchId . '-' . preg_replace('/[^A-Za-z0-9]/', '', $unitNo);
            }
            if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                $plotCode .= '-' . Str::random(3);
            }

            Property::create([
                'firm_id'              => $firmId,
                'property_master_id'   => $propertyMaster->id,
                'acquisition_batch_id' => $batchId,
                'project_id'           => $projectId,
                'property_type_id'     => $typeId,
                'property_name'        => $plotName,
                'property_code'        => $plotCode,
                'unit_no'              => $unitNo,
                'size'                 => $size,
                'size_unit'            => $sizeUnit,
                'facing'               => $facing,
                'location'             => $location,
                'city'                 => $city,
                'address'              => $address,
                'purchase_rate'        => $purchaseRate,
                'purchase_date'        => $defaultPurchaseDate,
                'price'                => $price,
                'status'               => $status,
                'description'          => $description,
                'main_image'           => $mainImage,
            ]);

            $createdPlots++;
            $numericSize = (float) preg_replace('/[^\d.]/', '', str_replace(',', '', (string)$size));
            if ($numericSize > 0) {
                $isSqyd = (stripos((string)$size, 'sq.yd') !== false || stripos((string)$size, 'sqyd') !== false || ($sizeUnit && strtolower($sizeUnit) === 'sq.yard'));
                $totalArea += ($isSqyd ? $numericSize * 9 : $numericSize);
            }
        }

        return ['count' => $createdPlots, 'total_area' => $totalArea];
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
