<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyMasterRequest;
use App\Models\PropertyMaster;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Firm;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PropertyMasterController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = PropertyMaster::with(['firm', 'projects'])->withCount(['projects', 'plots']);

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('location',      'like', "%{$s}%")
                  ->orWhere('city',          'like', "%{$s}%")
                  ->orWhere('status',        'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $propertyMasters = $query->latest()->paginate(15)->withQueryString();

        return view('admin.property-masters.index', compact('propertyMasters'));
    }

    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firms = Firm::where('id', $firmId)->get();
        }

        $vendors = \App\Models\Vendor::where('status', 'active')->orderBy('name')->get();

        return view('admin.property-masters.create', compact('firms', 'vendors'));
    }

    public function store(PropertyMasterRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $propertyCode = $request->property_code;
        if (empty($propertyCode)) {
            $latest = PropertyMaster::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $propertyCode = 'PROP-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        $mainImagePath = null;
        $documentPath  = null;

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('property-masters/images', 'public');
        }
        if ($request->hasFile('document_file')) {
            $documentPath = $request->file('document_file')->store('property-masters/documents', 'public');
        }

        $purchasePrice = $request->filled('purchase_price') ? floatval($request->purchase_price) : 0.00;
        $paidAmount = $request->filled('paid_amount') ? floatval($request->paid_amount) : ($request->filled('purchase_price') && $request->payment_status === 'paid' ? $purchasePrice : 0.00);
        $dueAmount = max(0.00, $purchasePrice - $paidAmount);

        $paymentStatus = $request->payment_status;
        if (empty($paymentStatus)) {
            if ($purchasePrice > 0) {
                if ($paidAmount >= $purchasePrice) {
                    $paymentStatus = 'paid';
                } elseif ($paidAmount > 0) {
                    $paymentStatus = 'partial';
                } else {
                    $paymentStatus = 'unpaid';
                }
            } else {
                $paymentStatus = 'paid';
            }
        }

        $propertyMaster = PropertyMaster::create([
            'firm_id'        => $firmId,
            'property_name'  => $request->property_name,
            'property_code'  => $propertyCode,
            'purchase_price' => $purchasePrice,
            'paid_amount'    => $paidAmount,
            'due_amount'     => $dueAmount,
            'purchase_date'  => $request->purchase_date ?: date('Y-m-d'),
            'purchase_rate'  => $request->purchase_rate ?: null,
            'total_area'     => $request->total_area ?: null,
            'area_unit'      => $request->area_unit ?: 'Sq.Ft',
            'seller_name'    => $request->seller_name,
            'vendor_id'      => $request->vendor_id ?: null,
            'payment_mode'   => $request->payment_mode,
            'payment_status' => $paymentStatus,
            'location'       => $request->location,
            'address'        => $request->address,
            'city'           => $request->city,
            'state'          => $request->state,
            'country'        => $request->country,
            'pincode'        => $request->pincode,
            'description'    => $request->description,
            'status'         => $request->status,
            'main_image'     => $mainImagePath,
            'document_file'  => $documentPath,
            'created_by'     => auth()->id(),
            'updated_by'     => auth()->id(),
        ]);

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master created successfully.');
    }

    public function show(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $propertyMaster->load([
            'firm',
            'vendor',
            'projects.properties',
            'plots' => fn($q) => $q->with(['project', 'propertyType'])
        ]);

        $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($propertyMaster) {
            $q->where('firms.id', $propertyMaster->firm_id);
        })->orWhereDoesntHave('firms')->orderBy('name')->get();

        $projects = Project::where('firm_id', $propertyMaster->firm_id)->orderBy('project_name')->get();

        return view('admin.property-masters.show', compact('propertyMaster', 'propertyTypes', 'projects'));
    }

    public function edit(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $isAdmin = auth()->user() && auth()->user()->isAdmin();

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $firms = Firm::where('id', $firmId)->get();
        }

        $vendors = \App\Models\Vendor::where('status', 'active')->orderBy('name')->get();

        return view('admin.property-masters.edit', compact('propertyMaster', 'firms', 'vendors'));
    }

    public function update(PropertyMasterRequest $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $propertyMaster->firm_id;

        $mainImagePath = $propertyMaster->main_image;
        $documentPath  = $propertyMaster->document_file;

        if ($request->hasFile('main_image')) {
            if ($propertyMaster->main_image) {
                Storage::disk('public')->delete($propertyMaster->main_image);
            }
            $mainImagePath = $request->file('main_image')->store('property-masters/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            if ($propertyMaster->document_file) {
                Storage::disk('public')->delete($propertyMaster->document_file);
            }
            $documentPath = $request->file('document_file')->store('property-masters/documents', 'public');
        }

        $purchasePrice = $request->purchase_price !== null ? floatval($request->purchase_price) : ($propertyMaster->purchase_price ?? 0.00);
        $paidAmount    = $request->paid_amount !== null ? floatval($request->paid_amount) : ($propertyMaster->paid_amount ?? 0.00);
        $dueAmount     = max(0.00, $purchasePrice - $paidAmount);

        $paymentStatus = $request->payment_status;
        if (empty($paymentStatus)) {
            if ($purchasePrice > 0) {
                if ($paidAmount >= $purchasePrice) {
                    $paymentStatus = 'paid';
                } elseif ($paidAmount > 0) {
                    $paymentStatus = 'partial';
                } else {
                    $paymentStatus = 'unpaid';
                }
            } else {
                $paymentStatus = 'paid';
            }
        }

        $propertyMaster->update([
            'firm_id'        => $firmId,
            'property_name'  => $request->property_name,
            'property_code'  => $request->property_code ?: $propertyMaster->property_code,
            'purchase_price' => $purchasePrice,
            'paid_amount'    => $paidAmount,
            'due_amount'     => $dueAmount,
            'purchase_date'  => $request->purchase_date ?: $propertyMaster->purchase_date,
            'purchase_rate'  => $request->purchase_rate ?: $propertyMaster->purchase_rate,
            'total_area'     => $request->total_area ?: $propertyMaster->total_area,
            'area_unit'      => $request->area_unit ?: ($propertyMaster->area_unit ?? 'Sq.Ft'),
            'seller_name'    => $request->seller_name ?: $propertyMaster->seller_name,
            'vendor_id'      => $request->vendor_id ?: $propertyMaster->vendor_id,
            'payment_mode'   => $request->payment_mode ?: $propertyMaster->payment_mode,
            'payment_status' => $paymentStatus,
            'location'       => $request->location,
            'address'        => $request->address,
            'city'           => $request->city,
            'state'          => $request->state,
            'country'        => $request->country,
            'pincode'        => $request->pincode,
            'description'    => $request->description,
            'status'         => $request->status,
            'main_image'     => $mainImagePath,
            'document_file'  => $documentPath,
            'updated_by'     => auth()->id(),
        ]);

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master updated successfully.');
    }

    public function destroy(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        // Check if any plots are booked or sold
        $bookedOrSold = $propertyMaster->plots()->whereIn('status', ['booked', 'sold'])->count();
        if ($bookedOrSold > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete Property Master because {$bookedOrSold} plot(s) are already booked or sold.");
        }

        if ($propertyMaster->main_image) {
            Storage::disk('public')->delete($propertyMaster->main_image);
        }
        if ($propertyMaster->document_file) {
            Storage::disk('public')->delete($propertyMaster->document_file);
        }

        DB::transaction(function () use ($propertyMaster) {
            $propertyMaster->plots()->delete();
            $propertyMaster->projects()->detach();
            $propertyMaster->delete();
        });

        return redirect()->route('property-masters.index')
            ->with('success', 'Property Master and all available plots deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────
    // DIRECT PLOT ACTIONS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Add a single plot to Property Master
     */
    public function addSinglePlot(Request $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $request->validate([
            'property_name'     => 'required|string|max:255',
            'unit_no'           => 'nullable|string|max:50',
            'property_type_id'  => 'nullable|exists:property_types,id',
            'size'              => 'nullable|numeric|min:0',
            'size_unit'         => 'nullable|string|max:50',
            'facing'            => 'nullable|string|max:50',
            'purchase_rate'     => 'nullable|numeric|min:0',
            'price'             => 'nullable|numeric|min:0',
            'status'            => 'required|in:available,booked,sold,reserved',
            'project_id'        => 'nullable|exists:projects,id',
            'description'       => 'nullable|string',
        ]);

        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';
        $unitNo = $request->unit_no ?: (string) $propertyMaster->getNextPlotSequenceNumber();
        $plotCode = 'P-' . $propPrefix . '-' . str_pad($unitNo, 3, '0', STR_PAD_LEFT);

        if (Property::where('firm_id', $propertyMaster->firm_id)->where('property_code', $plotCode)->exists()) {
            $plotCode .= '-' . Str::random(3);
        }

        $purchaseRate = $request->filled('purchase_rate') ? $request->purchase_rate : ($propertyMaster->purchase_rate ?: 0);
        $price = $request->filled('price') ? $request->price : $purchaseRate;

        $plot = Property::create([
            'firm_id'            => $propertyMaster->firm_id,
            'property_master_id' => $propertyMaster->id,
            'project_id'         => $request->project_id ?: null,
            'property_type_id'   => $request->property_type_id ?: null,
            'property_name'      => $request->property_name,
            'property_code'      => $plotCode,
            'unit_no'            => $unitNo,
            'size'               => $request->size ?: null,
            'size_unit'          => $request->size_unit ?: 'sq.ft',
            'facing'             => $request->facing ?: null,
            'location'           => $propertyMaster->location,
            'city'               => $propertyMaster->city,
            'address'            => $propertyMaster->address,
            'purchase_rate'      => $purchaseRate,
            'purchase_date'      => $propertyMaster->purchase_date ?: date('Y-m-d'),
            'price'              => $price,
            'status'             => $request->status,
            'description'        => $request->description,
        ]);

        // If a project is selected, ensure the project is also linked in pivot table
        if ($request->project_id) {
            $project = Project::find($request->project_id);
            if ($project) {
                $project->propertyMasters()->syncWithoutDetaching([$propertyMaster->id]);
            }
        }

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', "Plot '{$plot->property_name}' added successfully.");
    }

    /**
     * Bulk generate sequential plots for Property Master
     */
    public function bulkGeneratePlots(Request $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $request->validate([
            'total_plots'       => 'required|integer|min:1|max:1000',
            'plot_prefix'       => 'nullable|string|max:50',
            'start_number'      => 'nullable|integer|min:1',
            'property_type_id'  => 'nullable|exists:property_types,id',
            'size'              => 'nullable|numeric|min:0',
            'size_unit'         => 'nullable|string|max:50',
            'facing'            => 'nullable|string|max:50',
            'purchase_rate'     => 'nullable|numeric|min:0',
            'price'             => 'nullable|numeric|min:0',
            'project_id'        => 'nullable|exists:projects,id',
        ]);

        $count = (int) $request->total_plots;
        $prefix = $request->plot_prefix !== null ? $request->plot_prefix : 'Plot ';
        $startNum = $request->filled('start_number') ? (int) $request->start_number : $propertyMaster->getNextPlotSequenceNumber();
        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';

        $purchaseRate = $request->filled('purchase_rate') ? $request->purchase_rate : ($propertyMaster->purchase_rate ?: 0);
        $price = $request->filled('price') ? $request->price : $purchaseRate;
        $sizeUnit = $request->size_unit ?: 'sq.ft';

        DB::transaction(function () use ($propertyMaster, $count, $prefix, $startNum, $propPrefix, $purchaseRate, $price, $sizeUnit, $request) {
            for ($i = 0; $i < $count; $i++) {
                $num = $startNum + $i;
                $plotCode = 'P-' . $propPrefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);

                if (Property::where('firm_id', $propertyMaster->firm_id)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                Property::create([
                    'firm_id'            => $propertyMaster->firm_id,
                    'property_master_id' => $propertyMaster->id,
                    'project_id'         => $request->project_id ?: null,
                    'property_type_id'   => $request->property_type_id ?: null,
                    'property_name'      => trim($prefix . $num),
                    'property_code'      => $plotCode,
                    'unit_no'            => (string) $num,
                    'size'               => $request->size ?: null,
                    'size_unit'          => $sizeUnit,
                    'facing'             => $request->facing ?: null,
                    'location'           => $propertyMaster->location,
                    'city'               => $propertyMaster->city,
                    'address'            => $propertyMaster->address,
                    'purchase_rate'      => $purchaseRate,
                    'purchase_date'      => $propertyMaster->purchase_date ?: date('Y-m-d'),
                    'price'              => $price,
                    'status'             => 'available',
                    'description'        => 'Bulk generated under ' . $propertyMaster->property_name,
                ]);
            }

            if ($request->project_id) {
                $project = Project::find($request->project_id);
                if ($project) {
                    $project->propertyMasters()->syncWithoutDetaching([$propertyMaster->id]);
                }
            }
        });

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', "{$count} plots generated successfully (Unit #{$startNum} to #" . ($startNum + $count - 1) . ").");
    }

    /**
     * Import plots directly from Excel
     */
    public function importPlots(Request $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'project_id' => 'nullable|exists:projects,id',
        ]);

        $file = $request->file('excel_file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows) || count($rows) < 2) {
            return redirect()->back()->with('error', 'The uploaded Excel file is empty or missing data rows.');
        }

        // Detect header
        $headerRowIndex = 0;
        foreach ($rows as $idx => $r) {
            $joined = strtolower(implode(' ', array_filter($r, fn($v) => $v !== null)));
            if (str_contains($joined, 'plot') || str_contains($joined, 'unit') || str_contains($joined, 'size') || str_contains($joined, 'rate')) {
                $headerRowIndex = $idx;
                break;
            }
        }

        $headers = $rows[$headerRowIndex];
        $columnMap = [];
        foreach ($headers as $colIdx => $h) {
            $norm = strtolower(trim((string)$h));
            if (!isset($columnMap['unit_no']) && (str_contains($norm, 'unit') || str_contains($norm, 'plot no') || str_contains($norm, 'plot_no'))) {
                $columnMap['unit_no'] = $colIdx;
            } elseif (!isset($columnMap['plot_name']) && (str_contains($norm, 'name') || str_contains($norm, 'plot name') || str_contains($norm, 'title'))) {
                $columnMap['plot_name'] = $colIdx;
            } elseif (!isset($columnMap['plot_code']) && (str_contains($norm, 'code') || str_contains($norm, 'plot code'))) {
                $columnMap['plot_code'] = $colIdx;
            } elseif (!isset($columnMap['size']) && (str_contains($norm, 'size') || str_contains($norm, 'area'))) {
                $columnMap['size'] = $colIdx;
            } elseif (!isset($columnMap['size_unit']) && (str_contains($norm, 'unit') && !str_contains($norm, 'no'))) {
                $columnMap['size_unit'] = $colIdx;
            } elseif (!isset($columnMap['facing']) && str_contains($norm, 'facing')) {
                $columnMap['facing'] = $colIdx;
            } elseif (!isset($columnMap['purchase_rate']) && str_contains($norm, 'rate')) {
                $columnMap['purchase_rate'] = $colIdx;
            } elseif (!isset($columnMap['price']) && (str_contains($norm, 'price') || str_contains($norm, 'amount'))) {
                $columnMap['price'] = $colIdx;
            } elseif (!isset($columnMap['status']) && str_contains($norm, 'status')) {
                $columnMap['status'] = $colIdx;
            }
        }

        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';
        $propertyTypes = PropertyType::pluck('id', 'name')->toArray();
        $defaultRate = $propertyMaster->purchase_rate ?: 0;
        $createdPlots = 0;

        DB::transaction(function () use ($rows, $headerRowIndex, $columnMap, $propertyMaster, $propPrefix, $propertyTypes, $defaultRate, $request, &$createdPlots) {
            $totalRows = count($rows);
            for ($r = $headerRowIndex + 1; $r < $totalRows; $r++) {
                $row = $rows[$r];
                if (!is_array($row) || empty(array_filter($row, fn($val) => $val !== null && trim((string)$val) !== ''))) {
                    continue;
                }

                $rawUnit = isset($columnMap['unit_no']) && isset($row[$columnMap['unit_no']]) ? trim((string)$row[$columnMap['unit_no']]) : (isset($row[0]) ? trim((string)$row[0]) : '');
                $rawName = isset($columnMap['plot_name']) && isset($row[$columnMap['plot_name']]) ? trim((string)$row[$columnMap['plot_name']]) : (isset($row[1]) ? trim((string)$row[1]) : '');
                $rawCode = isset($columnMap['plot_code']) && isset($row[$columnMap['plot_code']]) ? trim((string)$row[$columnMap['plot_code']]) : '';

                $unitNo = $rawUnit !== '' ? $rawUnit : (string)($propertyMaster->getNextPlotSequenceNumber() + $createdPlots);
                $plotName = $rawName !== '' ? $rawName : ('Plot ' . $unitNo);

                $rawSize = isset($columnMap['size']) && isset($row[$columnMap['size']]) ? trim((string)$row[$columnMap['size']]) : (isset($row[2]) ? trim((string)$row[2]) : '');
                $rawUnitStr = isset($columnMap['size_unit']) && isset($row[$columnMap['size_unit']]) ? trim((string)$row[$columnMap['size_unit']]) : (isset($row[3]) ? trim((string)$row[3]) : 'sq.ft');

                $size = is_numeric(preg_replace('/[^\d.]/', '', $rawSize)) ? (float)preg_replace('/[^\d.]/', '', $rawSize) : null;
                $sizeUnit = !empty($rawUnitStr) ? $rawUnitStr : 'sq.ft';

                $facing = isset($columnMap['facing']) && isset($row[$columnMap['facing']]) ? trim((string)$row[$columnMap['facing']]) : (isset($row[4]) ? trim((string)$row[4]) : null);

                $rawRate = isset($columnMap['purchase_rate']) && isset($row[$columnMap['purchase_rate']]) ? trim((string)$row[$columnMap['purchase_rate']]) : (isset($row[5]) ? trim((string)$row[5]) : '');
                $rateVal = is_numeric(preg_replace('/[^\d.]/', '', $rawRate)) ? (float)preg_replace('/[^\d.]/', '', $rawRate) : $defaultRate;

                $rawPrice = isset($columnMap['price']) && isset($row[$columnMap['price']]) ? trim((string)$row[$columnMap['price']]) : (isset($row[6]) ? trim((string)$row[6]) : '');
                $priceVal = is_numeric(preg_replace('/[^\d.]/', '', $rawPrice)) ? (float)preg_replace('/[^\d.]/', '', $rawPrice) : $rateVal;

                $rawStatus = isset($columnMap['status']) && isset($row[$columnMap['status']]) ? strtolower(trim((string)$row[$columnMap['status']])) : 'available';
                $status = in_array($rawStatus, ['available', 'booked', 'sold', 'reserved']) ? $rawStatus : 'available';

                $plotCode = !empty($rawCode) ? $rawCode : ('P-' . $propPrefix . '-' . preg_replace('/[^A-Za-z0-9]/', '', $unitNo));
                if (Property::where('firm_id', $propertyMaster->firm_id)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                Property::create([
                    'firm_id'            => $propertyMaster->firm_id,
                    'property_master_id' => $propertyMaster->id,
                    'project_id'         => $request->project_id ?: null,
                    'property_type_id'   => reset($propertyTypes) ?: PropertyType::withoutGlobalScopes()->firstOrCreate(['name' => 'Plot'], ['status' => 'active'])->id,
                    'property_name'      => $plotName,
                    'property_code'      => $plotCode,
                    'unit_no'            => $unitNo,
                    'size'               => $size,
                    'size_unit'          => $sizeUnit,
                    'facing'             => $facing,
                    'location'           => $propertyMaster->location,
                    'city'               => $propertyMaster->city,
                    'address'            => $propertyMaster->address,
                    'purchase_rate'      => $rateVal,
                    'purchase_date'      => $propertyMaster->purchase_date ?: date('Y-m-d'),
                    'price'              => $priceVal,
                    'status'             => $status,
                    'description'        => 'Imported via Excel under ' . $propertyMaster->property_name,
                ]);

                $createdPlots++;
            }

            if ($request->project_id) {
                $project = Project::find($request->project_id);
                if ($project) {
                    $project->propertyMasters()->syncWithoutDetaching([$propertyMaster->id]);
                }
            }
        });

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', "{$createdPlots} plots imported successfully from Excel.");
    }

    /**
     * Download Excel template for plots import
     */
    public function downloadPlotTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Plots Import Template');

        $headers = [
            'A1' => 'Plot / Unit No *',
            'B1' => 'Plot Name *',
            'C1' => 'Size (Numeric)',
            'D1' => 'Size Unit (sq.ft / sq.yard)',
            'E1' => 'Facing Direction (East/West/North/South)',
            'F1' => 'Purchase Rate (₹)',
            'G1' => 'Selling Price (₹)',
            'H1' => 'Status (available/booked/sold)',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Sample Rows
        $sampleData = [
            ['1', 'Plot 1', '1200', 'sq.ft', 'East', '1500', '2200', 'available'],
            ['2', 'Plot 2', '1500', 'sq.ft', 'North', '1500', '2200', 'available'],
            ['3', 'Plot 3', '1800', 'sq.yard', 'West', '13500', '18000', 'available'],
        ];

        $rowIdx = 2;
        foreach ($sampleData as $r) {
            $colLetter = 'A';
            foreach ($r as $val) {
                $sheet->setCellValue($colLetter . $rowIdx, $val);
                $colLetter++;
            }
            $rowIdx++;
        }

        // Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'plots_import_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = PropertyMaster::with(['firm', 'projects', 'plots'])->withCount(['projects', 'plots']);

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('location',      'like', "%{$s}%")
                  ->orWhere('city',          'like', "%{$s}%")
                  ->orWhere('status',        'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $propertyMasters = $query->latest()->get();
        $totalPurchasePrice = $propertyMasters->sum('purchase_price');
        $totalPaid = $propertyMasters->sum('paid_amount');
        $totalDue = $propertyMasters->sum('due_amount');
        $totalPlots = $propertyMasters->sum('plots_count');

        return view('admin.property-masters.pdf', compact(
            'propertyMasters', 'totalPurchasePrice', 'totalPaid', 'totalDue', 'totalPlots'
        ));
    }

    public function downloadPdf(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $propertyMaster->load([
            'firm',
            'vendor',
            'plots.propertyType',
            'plots.project',
            'projects',
            'creator',
            'updater'
        ]);

        return view('admin.property-masters.show-pdf', compact('propertyMaster'));
    }

    private function authorise(PropertyMaster $propertyMaster)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($propertyMaster->firm_id != $firmId) {
                abort(403, 'Unauthorized access to Property Master.');
            }
        }
    }
}
