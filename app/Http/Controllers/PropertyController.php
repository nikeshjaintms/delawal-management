<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyRequest;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use ZipArchive;

class PropertyController extends Controller
{
    // ----------------------------------------------------------------
    // INDEX
    // ----------------------------------------------------------------
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Property::with(['propertyType', 'firm', 'project']);

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
            $projectsQuery = \App\Models\Project::orderBy('project_name');
            if ($request->filled('firm_id')) {
                $projectsQuery->where('firm_id', $request->firm_id);
            }
            $projects = $projectsQuery->get();
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
            $projects = \App\Models\Project::where('firm_id', $firmId)->orderBy('project_name')->get();
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q
                    ->where('property_name', 'like', "%{$s}%")
                    ->orWhere('property_code', 'like', "%{$s}%")
                    ->orWhere('location', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%")
                    ->orWhere('status', 'like', "%{$s}%");
            });
        }

        $properties = $query->latest()->paginate(15)->withQueryString();

        return view('admin.properties.index', compact('properties', 'projects'));
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------
    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $propertyTypes = PropertyType::orderBy('name')->get();
            $projects = \App\Models\Project::orderBy('project_name')->get();
        } else {
            $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            })->orderBy('name')->get();
            $projects = \App\Models\Project::where('firm_id', $firmId)->orderBy('project_name')->get();
        }

        return view('admin.properties.create', compact('propertyTypes', 'projects'));
    }

    // ----------------------------------------------------------------
    // STORE
    // ----------------------------------------------------------------
    public function store(PropertyRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $mainImagePath = null;
        $documentPath = null;

        if ($request->hasFile('main_image')) {
            $mainImagePath = $request
                ->file('main_image')
                ->store('properties/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            $documentPath = $request
                ->file('document_file')
                ->store('properties/documents', 'public');
        }

        Property::create([
            'firm_id' => $firmId,
            'project_id' => $request->project_id,
            'property_type_id' => $request->property_type_id ?: null,
            'property_name' => $request->property_name,
            'property_code' => $request->property_code,
            'status' => $request->status,
            'location' => $request->location,
            'city' => $request->city,
            'address' => $request->address,
            'size' => $request->size,
            'size_unit' => $request->size_unit,
            'price' => $request->price ?: null,
            'unit_no' => $request->unit_no,
            'floor_no' => $request->floor_no,
            'facing' => $request->facing,
            'description' => $request->description,
            'main_image' => $mainImagePath,
            'document_file' => $documentPath,
        ]);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property added successfully.');
    }

    // ----------------------------------------------------------------
    // SHOW
    // ----------------------------------------------------------------
    public function show(Property $property)
    {
        $this->authorise($property);
        $property->load(['propertyType', 'documents' => fn($q) => $q->latest()]);

        return view('admin.properties.show', compact('property'));
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------
    public function edit(Property $property)
    {
        $this->authorise($property);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if ($isAdmin) {
            $propertyTypes = PropertyType::orderBy('name')->get();
            $projects = \App\Models\Project::where('firm_id', $property->firm_id)->orderBy('project_name')->get();
        } else {
            $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($property) {
                $q->where('firms.id', $property->firm_id);
            })->orderBy('name')->get();
            $projects = \App\Models\Project::where('firm_id', $property->firm_id)->orderBy('project_name')->get();
        }

        return view('admin.properties.edit', compact('property', 'propertyTypes', 'projects'));
    }

    // ----------------------------------------------------------------
    // UPDATE
    // ----------------------------------------------------------------
    public function update(PropertyRequest $request, Property $property)
    {
        $this->authorise($property);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $property->firm_id;

        $mainImagePath = $property->main_image;
        $documentPath = $property->document_file;

        if ($request->hasFile('main_image')) {
            if ($property->main_image) {
                Storage::disk('public')->delete($property->main_image);
            }
            $mainImagePath = $request
                ->file('main_image')
                ->store('properties/images', 'public');
        }

        if ($request->hasFile('document_file')) {
            if ($property->document_file) {
                Storage::disk('public')->delete($property->document_file);
            }
            $documentPath = $request
                ->file('document_file')
                ->store('properties/documents', 'public');
        }

        $property->update([
            'firm_id' => $firmId,
            'project_id' => $request->project_id,
            'property_type_id' => $request->property_type_id ?: null,
            'property_name' => $request->property_name,
            'property_code' => $request->property_code,
            'status' => $request->status,
            'location' => $request->location,
            'city' => $request->city,
            'address' => $request->address,
            'size' => $request->size,
            'size_unit' => $request->size_unit,
            'price' => $request->price ?: null,
            'unit_no' => $request->unit_no,
            'floor_no' => $request->floor_no,
            'facing' => $request->facing,
            'description' => $request->description,
            'main_image' => $mainImagePath,
            'document_file' => $documentPath,
        ]);

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property updated successfully.');
    }

    // ----------------------------------------------------------------
    // DESTROY
    // ----------------------------------------------------------------
    public function destroy(Property $property)
    {
        $this->authorise($property);

        if ($property->main_image) {
            Storage::disk('public')->delete($property->main_image);
        }
        if ($property->document_file) {
            Storage::disk('public')->delete($property->document_file);
        }

        $property->delete();

        return redirect()
            ->route('properties.index')
            ->with('success', 'Property deleted successfully.');
    }

    // ----------------------------------------------------------------
    // DOWNLOAD EXCEL TEMPLATE
    // ----------------------------------------------------------------
    public function downloadTemplate(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $projects = \App\Models\Project::with('firm')->orderBy('project_name')->get();
            $propertyTypes = PropertyType::orderBy('name')->get();
            $firms = \App\Models\Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $projects = \App\Models\Project::with('firm')->where('firm_id', $firmId)->orderBy('project_name')->get();
            $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($firmId) {
                $q->where('firms.id', $firmId);
            })->orderBy('name')->get();
            $firms = \App\Models\Firm::where('id', $firmId)->get();
        }

        $contextProject = null;
        if ($request->filled('project_id')) {
            $contextProject = \App\Models\Project::with('firm')->find($request->project_id);
        }

        $sampleFirm = $contextProject?->firm?->firm_name ?? ($firms->first()?->firm_name ?? 'Delawala Builders');
        $sampleProject = $contextProject?->project_name ?? ($projects->first()?->project_name ?? 'Delawala Residency');
        $sampleType = $propertyTypes->first()?->name ?? 'Plot';

        // If PhpSpreadsheet is installed, generate full multi-sheet XLSX template
        if (class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet') && class_exists('\\PhpOffice\\PhpSpreadsheet\\Writer\\Xlsx')) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Sheet 1: Template
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Properties Import Template');

            $headers = [
                'A1' => 'Firm Name',
                'B1' => 'Project Name*',
                'C1' => 'Property Type*',
                'D1' => 'Property Code*',
                'E1' => 'Property Name*',
                'F1' => 'Property Status*',
                'G1' => 'Location',
                'H1' => 'City',
                'I1' => 'Address',
                'J1' => 'Size',
                'K1' => 'Size Unit',
                'L1' => 'Price (INR)',
                'M1' => 'Unit No',
                'N1' => 'Floor No',
                'O1' => 'Facing',
                'P1' => 'Description',
                'Q1' => 'Image Filename',
            ];

            foreach ($headers as $cell => $text) {
                $sheet->setCellValue($cell, $text);
            }

            $sheet->setCellValue('A2', $sampleFirm);
            $sheet->setCellValue('B2', $sampleProject);
            $sheet->setCellValue('C2', $sampleType);
            $sheet->setCellValue('D2', 'DEL-PLOT-001');
            $sheet->setCellValue('E2', 'Plot No. 1');
            $sheet->setCellValue('F2', 'available');
            $sheet->setCellValue('G2', 'Zadeshwar Road');
            $sheet->setCellValue('H2', 'Bharuch');
            $sheet->setCellValue('I2', 'Plot No 1, Near NH8, Zadeshwar, Bharuch');
            $sheet->setCellValue('J2', '1200');
            $sheet->setCellValue('K2', 'sq.ft');
            $sheet->setCellValue('L2', '2500000');
            $sheet->setCellValue('M2', 'P-01');
            $sheet->setCellValue('N2', 'Ground');
            $sheet->setCellValue('O2', 'East');
            $sheet->setCellValue('P2', 'Prime location corner plot');
            $sheet->setCellValue('Q2', 'plot-001.jpg');

            // Style Header Row
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ];
            $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(28);
            $sheet->getRowDimension(2)->setRowHeight(22);

            foreach (range('A', 'Q') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Sheet 2: Reference & Instructions
            $guideSheet = $spreadsheet->createSheet();
            $guideSheet->setTitle('Instructions & Reference');

            $guideSheet->setCellValue('A1', 'Property Import Guidelines');
            $guideSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $instructions = [
                ['Column', 'Required', 'Allowed Values / Format', 'Description'],
                ['Firm Name', 'Admin Only', 'Text (e.g. ' . $sampleFirm . ')', 'Firm name. Optional for firm user.'],
                ['Project Name*', 'YES', 'Text (Must match active Project)', 'Project name or project code.'],
                ['Property Type*', 'YES', 'Text (Must match active Type)', 'Property type e.g. Flat, Plot, Shop.'],
                ['Property Code*', 'YES', 'Unique Text (e.g. DEL-PLOT-001)', 'Unique identifier per property.'],
                ['Property Name*', 'YES', 'Text (e.g. Plot No. 1)', 'Name/Title of the property.'],
                ['Property Status*', 'YES', 'available, booked, sold, rented', 'Current availability status.'],
                ['Location', 'NO', 'Text', 'Area or landmark location.'],
                ['City', 'NO', 'Text', 'City name.'],
                ['Address', 'NO', 'Text', 'Full street address.'],
                ['Size', 'NO', 'Number/Text (e.g. 1200)', 'Area size numeric value.'],
                ['Size Unit', 'NO', 'sq.ft, sq.yard, sq.meter, acre, bigha', 'Measurement unit for size.'],
                ['Price (INR)', 'NO', 'Number (e.g. 2500000)', 'Property price without currency symbols.'],
                ['Unit No', 'NO', 'Text (e.g. A-101)', 'Unit or flat number.'],
                ['Floor No', 'NO', 'Text (e.g. 1st Floor)', 'Floor number or level.'],
                ['Facing', 'NO', 'East, West, North, South, North-East...', 'Property orientation.'],
                ['Description', 'NO', 'Text', 'Notes or amenities description.'],
                ['Image Filename', 'NO', 'Image filename (e.g. plot-001.jpg)', 'Matching filename if images zip is uploaded.'],
            ];

            $r = 3;
            foreach ($instructions as $row) {
                $col = 'A';
                foreach ($row as $val) {
                    $guideSheet->setCellValue($col . $r, $val);
                    $col++;
                }
                if ($r === 3) {
                    $guideSheet->getStyle("A{$r}:D{$r}")->getFont()->setBold(true);
                    $guideSheet->getStyle("A{$r}:D{$r}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E2E8F0');
                }
                $r++;
            }

            $spreadsheet->setActiveSheetIndex(0);
            $filename = 'Property_Master_Import_Template.xlsx';

            return response()->streamDownload(function () use ($spreadsheet) {
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
        }

        // Fallback: Stream universal CSV Template compatible with Excel
        $csvHeaders = [
            'Firm Name', 'Project Name*', 'Property Type*', 'Property Code*', 'Property Name*',
            'Property Status*', 'Location', 'City', 'Address', 'Size', 'Size Unit',
            'Price (INR)', 'Unit No', 'Floor No', 'Facing', 'Description', 'Image Filename'
        ];
        $csvSample = [
            $sampleFirm, $sampleProject, $sampleType, 'DEL-PLOT-001', 'Plot No. 1',
            'available', 'Zadeshwar Road', 'Bharuch', 'Plot No 1, Near NH8, Bharuch',
            '1200', 'sq.ft', '2500000', 'P-01', 'Ground', 'East', 'Prime location corner plot', 'plot-001.jpg'
        ];

        return response()->streamDownload(function () use ($csvHeaders, $csvSample) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM so Excel opens it with proper encoding
            fputcsv($out, $csvHeaders);
            fputcsv($out, $csvSample);
            fclose($out);
        }, 'Property_Master_Import_Template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ----------------------------------------------------------------
    // VALIDATE EXCEL IMPORT
    // ----------------------------------------------------------------
    public function validateImport(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|max:10240',
            'image_archive' => 'nullable|file|mimes:zip|max:51200',
            'image_files.*' => 'nullable|file|image|max:10240',
        ]);

        $file = $request->file('excel_file');
        $batchId = (string) Str::uuid();

        $tempPath = storage_path("app/public/temp_import/{$batchId}");
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }

        // Save images into temp path if provided
        $uploadedImages = [];
        if ($request->hasFile('image_archive')) {
            $zipFile = $request->file('image_archive');
            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive();
                if ($zip->open($zipFile->getRealPath()) === true) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        $filename = basename($stat['name']);
                        if (!empty($filename) && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $filename)) {
                            $zip->extractTo($tempPath, $stat['name']);
                            $extractedFile = $tempPath . '/' . $stat['name'];
                            if (file_exists($extractedFile) && is_file($extractedFile)) {
                                copy($extractedFile, $tempPath . '/' . $filename);
                                $uploadedImages[strtolower($filename)] = $tempPath . '/' . $filename;
                            }
                        }
                    }
                    $zip->close();
                }
            }
        }

        if ($request->hasFile('image_files')) {
            foreach ($request->file('image_files') as $imgFile) {
                $originalName = $imgFile->getClientOriginalName();
                $targetFile = $tempPath . '/' . $originalName;
                $imgFile->move($tempPath, $originalName);
                $uploadedImages[strtolower($originalName)] = $targetFile;
            }
        }

        foreach (glob($tempPath . '/*') as $f) {
            if (is_file($f)) {
                $uploadedImages[strtolower(basename($f))] = $f;
            }
        }

        // Read Excel/CSV File using Universal Parser (PhpSpreadsheet or Native Fallback)
        try {
            $rows = $this->parseSpreadsheetData($file->getRealPath());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse Excel file. Error: ' . $e->getMessage(),
            ], 422);
        }

        if (empty($rows) || count($rows) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'The uploaded Excel file contains no data rows.',
            ], 422);
        }

        // Dynamically find the Header Row in the first 10 rows
        $dataStartRowIndex = 2;
        $headerRow = $rows[1] ?? [];
        $knownHeaderKeywords = ['code', 'name', 'project', 'type', 'category', 'status', 'city', 'firm', 'price', 'size'];

        foreach ($rows as $rowIndex => $rowCells) {
            if ($rowIndex > 10)
                break;

            $matchedKeywordCount = 0;
            foreach ($rowCells as $cellValue) {
                if (is_null($cellValue))
                    continue;
                $cleanVal = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $cellValue));
                foreach ($knownHeaderKeywords as $kw) {
                    if (str_contains($cleanVal, $kw)) {
                        $matchedKeywordCount++;
                        break;
                    }
                }
            }

            if ($matchedKeywordCount >= 2) {
                $headerRow = $rowCells;
                $dataStartRowIndex = $rowIndex + 1;
                break;
            }
        }

        $columnMap = [];
        $unmappedCols = [];

        // Exact match dictionary
        $exactDictionary = [
            'type' => ['propertytype', 'proptype', 'type', 'category', 'propertycategory', 'kind', 'projecttype'],
            'project' => ['projectname', 'projectcode', 'project'],
            'code' => ['propertycode', 'propcode', 'code', 'unitcode'],
            'name' => ['propertyname', 'propname', 'name', 'title'],
            'firm' => ['firmname', 'firm', 'company', 'companyname'],
            'status' => ['propertystatus', 'status', 'state'],
            'location' => ['location', 'loc'],
            'city' => ['city', 'town'],
            'address' => ['address', 'addr'],
            'size_unit' => ['sizeunit', 'unit'],
            'size' => ['size', 'area', 'sqft'],
            'price' => ['price', 'cost', 'amount', 'rate', 'value', 'priceinr'],
            'unit_no' => ['unitno', 'unitnumber', 'plotno', 'flatno'],
            'floor_no' => ['floorno', 'floor'],
            'facing' => ['facing', 'direction'],
            'description' => ['description', 'desc', 'details', 'notes', 'remarks', 'propertydescription'],
            'image' => ['imagefilename', 'image', 'mainimage', 'propertyimage', 'photo', 'img'],
        ];

        // Pass 1: Exact normalized dictionary match
        foreach ($headerRow as $colLetter => $rawHeader) {
            $cleanBom = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', (string) $rawHeader);
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanBom));

            if (empty($norm))
                continue;

            $matchedKey = null;
            foreach ($exactDictionary as $fieldKey => $validNorms) {
                if (in_array($norm, $validNorms, true)) {
                    $matchedKey = $fieldKey;
                    break;
                }
            }

            if ($matchedKey && !isset($columnMap[$matchedKey])) {
                $columnMap[$matchedKey] = $colLetter;
            } else {
                $unmappedCols[$colLetter] = $norm;
            }
        }

        // Pass 2: Substring fallback match for unmapped headers
        foreach ($unmappedCols as $colLetter => $norm) {
            if (!isset($columnMap['type']) && (str_contains($norm, 'type') || str_contains($norm, 'category'))) {
                $columnMap['type'] = $colLetter;
            } elseif (!isset($columnMap['project']) && str_contains($norm, 'project')) {
                $columnMap['project'] = $colLetter;
            } elseif (!isset($columnMap['code']) && str_contains($norm, 'code')) {
                $columnMap['code'] = $colLetter;
            } elseif (!isset($columnMap['status']) && str_contains($norm, 'status')) {
                $columnMap['status'] = $colLetter;
            } elseif (!isset($columnMap['firm']) && (str_contains($norm, 'firm') || str_contains($norm, 'company'))) {
                $columnMap['firm'] = $colLetter;
            } elseif (!isset($columnMap['name']) && str_contains($norm, 'name')) {
                $columnMap['name'] = $colLetter;
            } elseif (!isset($columnMap['image']) && (str_contains($norm, 'image') || str_contains($norm, 'photo'))) {
                $columnMap['image'] = $colLetter;
            }
        }

        // Verification maps
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $userFirmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $contextProjectId = $request->input('context_project_id');
        $contextProject = $contextProjectId ? \App\Models\Project::with('firm')->find($contextProjectId) : null;
        $contextFirmId = $request->input('context_firm_id') ?: ($contextProject?->firm_id);

        $firms = \App\Models\Firm::all();
        $firmsByName = $firms->pluck('id', 'firm_name')->mapWithKeys(fn($id, $name) => [strtolower(trim($name)) => $id]);

        $projects = \App\Models\Project::all();
        $projectsByKey = [];
        foreach ($projects as $p) {
            $projectsByKey[$p->firm_id][strtolower(trim($p->project_name))] = $p->id;
            if ($p->project_code) {
                $projectsByKey[$p->firm_id][strtolower(trim($p->project_code))] = $p->id;
            }
        }

        $propertyTypes = PropertyType::with('firms')->get();
        $typesByName = [];
        $typesByFirm = [];
        foreach ($propertyTypes as $pt) {
            $nameKey = strtolower(trim($pt->name));
            $typesByName[$nameKey] = $pt->id;
            if ($pt->relationLoaded('firms')) {
                foreach ($pt->firms as $f) {
                    $typesByFirm[$f->id][$nameKey] = $pt->id;
                }
            }
        }

        // Preload DB codes for existing property lookup
        $existingPropertiesByFirm = Property::select('id', 'firm_id', 'property_code')
            ->whereNotNull('property_code')
            ->get()
            ->groupBy('firm_id')
            ->map(function ($items) {
                $map = [];
                foreach ($items as $item) {
                    $map[strtolower(trim($item->property_code))] = $item->id;
                }
                return $map;
            })
            ->toArray();

        $validStatuses = ['available', 'booked', 'sold', 'rented'];
        $validSizeUnits = ['sq.ft', 'sq.yard', 'sq.meter', 'acre', 'bigha'];
        $validFacings = ['east', 'west', 'north', 'south', 'north-east', 'north-west', 'south-east', 'south-west'];

        $parsedRows = [];
        $batchCodes = [];  // Track duplicates within Excel file per firm
        $validCount = 0;
        $invalidCount = 0;
        $newCount = 0;
        $updateCount = 0;

        for ($r = $dataStartRowIndex; $r <= count($rows); $r++) {
            $rowData = $rows[$r];

            // Check if entire row is empty
            $isEmptyRow = true;
            foreach ($rowData as $val) {
                if (!is_null($val) && trim((string) $val) !== '') {
                    $isEmptyRow = false;
                    break;
                }
            }
            if ($isEmptyRow)
                continue;

            $errors = [];

            // Extract row fields
            $firmInput = isset($columnMap['firm']) ? trim((string) ($rowData[$columnMap['firm']] ?? '')) : '';
            $projectInput = isset($columnMap['project']) ? trim((string) ($rowData[$columnMap['project']] ?? '')) : '';
            $typeInput = isset($columnMap['type']) ? trim((string) ($rowData[$columnMap['type']] ?? '')) : '';
            $code = isset($columnMap['code']) ? trim((string) ($rowData[$columnMap['code']] ?? '')) : '';
            $name = isset($columnMap['name']) ? trim((string) ($rowData[$columnMap['name']] ?? '')) : '';
            $statusInput = isset($columnMap['status']) ? strtolower(trim((string) ($rowData[$columnMap['status']] ?? ''))) : 'available';
            $location = isset($columnMap['location']) ? trim((string) ($rowData[$columnMap['location']] ?? '')) : null;
            $city = isset($columnMap['city']) ? trim((string) ($rowData[$columnMap['city']] ?? '')) : null;
            $address = isset($columnMap['address']) ? trim((string) ($rowData[$columnMap['address']] ?? '')) : null;
            $size = isset($columnMap['size']) ? trim((string) ($rowData[$columnMap['size']] ?? '')) : null;
            $sizeUnitInput = isset($columnMap['size_unit']) ? strtolower(trim((string) ($rowData[$columnMap['size_unit']] ?? ''))) : null;
            $priceInput = isset($columnMap['price']) ? trim((string) ($rowData[$columnMap['price']] ?? '')) : null;
            $unitNo = isset($columnMap['unit_no']) ? trim((string) ($rowData[$columnMap['unit_no']] ?? '')) : null;
            $floorNo = isset($columnMap['floor_no']) ? trim((string) ($rowData[$columnMap['floor_no']] ?? '')) : null;
            $facingInput = isset($columnMap['facing']) ? trim((string) ($rowData[$columnMap['facing']] ?? '')) : null;
            $description = isset($columnMap['description']) ? trim((string) ($rowData[$columnMap['description']] ?? '')) : null;
            $imageFilename = isset($columnMap['image']) ? trim((string) ($rowData[$columnMap['image']] ?? '')) : null;

            // Firm Assignment with Context Support
            $targetFirmId = null;
            $targetFirmName = '';
            if ($isAdmin) {
                if (!empty($firmInput)) {
                    $firmKey = strtolower($firmInput);
                    if (isset($firmsByName[$firmKey])) {
                        $targetFirmId = $firmsByName[$firmKey];
                        $targetFirmName = $firmInput;
                    } elseif ($contextProject && (strtolower(trim($contextProject->firm?->firm_name ?? '')) === $firmKey || str_contains($firmKey, strtolower(trim($contextProject->firm?->firm_name ?? ''))))) {
                        $targetFirmId = $contextProject->firm_id;
                        $targetFirmName = $contextProject->firm?->firm_name ?? $firmInput;
                    } elseif ($contextFirmId) {
                        $targetFirmId = $contextFirmId;
                        $targetFirmName = $firms->firstWhere('id', $targetFirmId)?->firm_name ?? $firmInput;
                    } else {
                        $errors[] = "Invalid Firm '{$firmInput}'. Firm does not exist.";
                    }
                } else {
                    $targetFirmId = $contextFirmId ?: ($userFirmId ?: ($firms->first()?->id));
                    $targetFirmName = $firms->firstWhere('id', $targetFirmId)?->firm_name ?? 'Default Firm';
                }
            } else {
                $targetFirmId = $contextFirmId ?: $userFirmId;
                $targetFirmName = $firms->firstWhere('id', $targetFirmId)?->firm_name ?? '';
            }

            // Project Validation with Smart Fallback & Context Prioritization
            $projectId = null;
            $projectName = '';
            $projKey = strtolower(trim($projectInput));

            if ($contextProject && (empty($projectInput) || strtolower(trim($contextProject->project_name)) === $projKey || strtolower(trim($contextProject->project_code)) === $projKey)) {
                $projectId = $contextProject->id;
                $projectName = $contextProject->project_name;
                $targetFirmId = $contextProject->firm_id;
                $targetFirmName = $contextProject->firm?->firm_name ?? $targetFirmName;
            } elseif (empty($projectInput)) {
                $defaultProj = $contextProject ?: (\App\Models\Project::where('firm_id', $targetFirmId)->first() ?: $projects->first());
                if ($defaultProj) {
                    $projectId = $defaultProj->id;
                    $projectName = $defaultProj->project_name;
                } else {
                    $errors[] = 'Project Name is required.';
                }
            } else {
                if ($targetFirmId && isset($projectsByKey[$targetFirmId][$projKey])) {
                    $projectId = $projectsByKey[$targetFirmId][$projKey];
                    $projectName = $projectInput;
                } elseif ($contextProject && (strtolower(trim($contextProject->project_name)) === $projKey || strtolower(trim($contextProject->project_code)) === $projKey)) {
                    $projectId = $contextProject->id;
                    $projectName = $contextProject->project_name;
                } else {
                    $firmMatch = $projects->where('firm_id', $targetFirmId)->first(fn($p) => strtolower(trim($p->project_name)) === $projKey || strtolower(trim($p->project_code)) === $projKey);
                    if ($firmMatch) {
                        $projectId = $firmMatch->id;
                        $projectName = $firmMatch->project_name;
                    } else {
                        $globalMatch = $projects->first(fn($p) => strtolower(trim($p->project_name)) === $projKey || strtolower(trim($p->project_code)) === $projKey || str_contains(strtolower(trim($p->project_name)), $projKey));
                        if ($globalMatch) {
                            $projectId = $globalMatch->id;
                            $projectName = $globalMatch->project_name;
                            if (empty($firmInput)) {
                                $targetFirmId = $globalMatch->firm_id;
                                $targetFirmName = $globalMatch->firm?->firm_name ?? $targetFirmName;
                            }
                        } else {
                            $defaultProj = $contextProject ?: (\App\Models\Project::where('firm_id', $targetFirmId)->first() ?: $projects->first());
                            if ($defaultProj) {
                                $projectId = $defaultProj->id;
                                $projectName = $defaultProj->project_name;
                            } else {
                                $errors[] = "Invalid Project '{$projectInput}'. No matching project found.";
                            }
                        }
                    }
                }
            }

            // Property Type Validation (With 4-Step Smart Resilient Fallback)
            $propertyTypeId = null;
            $typeName = '';

            // Step A: If typeInput is empty, scan all unmapped cells in row for known PropertyType names
            if (empty($typeInput)) {
                foreach ($rowData as $colKey => $cellVal) {
                    $cellStr = trim((string) $cellVal);
                    if (empty($cellStr))
                        continue;
                    $lowerCellStr = strtolower($cellStr);
                    foreach ($propertyTypes as $pt) {
                        $ptLower = strtolower(trim($pt->name));
                        if ($lowerCellStr === $ptLower || str_contains($lowerCellStr, $ptLower)) {
                            $typeInput = $pt->name;
                            break 2;
                        }
                    }
                }
            }

            // Step B: If typeInput is STILL empty, infer from Property Name or Code (e.g. "Plot 1" => "Plot")
            if (empty($typeInput)) {
                $combinedText = strtolower($name . ' ' . $code);
                foreach ($propertyTypes as $pt) {
                    $ptLower = strtolower(trim($pt->name));
                    if (str_contains($combinedText, $ptLower) || ($ptLower === 'plot' && (str_contains($combinedText, 'plot') || str_starts_with(strtolower($code), 'p')))) {
                        $typeInput = $pt->name;
                        break;
                    }
                }
            }

            // Step C: If typeInput is STILL empty, default to "Plot" or first active PropertyType
            if (empty($typeInput)) {
                $defaultPt = $propertyTypes->first(fn($pt) => strtolower(trim($pt->name)) === 'plot') ?: $propertyTypes->first();
                if ($defaultPt) {
                    $typeInput = $defaultPt->name;
                }
            }

            // Step D: Perform exact/soft/firm lookup using $typeInput
            if (!empty($typeInput)) {
                $typeKey = strtolower(trim($typeInput));
                if ($targetFirmId && isset($typesByFirm[$targetFirmId][$typeKey])) {
                    $propertyTypeId = $typesByFirm[$targetFirmId][$typeKey];
                    $typeName = $typeInput;
                } elseif (isset($typesByName[$typeKey])) {
                    $propertyTypeId = $typesByName[$typeKey];
                    $typeName = $typeInput;
                } else {
                    $matchedPt = $propertyTypes->first(function ($pt) use ($typeKey) {
                        $ptNameLower = strtolower(trim($pt->name));
                        $singularType = rtrim($typeKey, 's');
                        $singularPt = rtrim($ptNameLower, 's');
                        return $ptNameLower === $typeKey || $singularPt === $singularType || str_contains($ptNameLower, $typeKey) || str_contains($typeKey, $ptNameLower);
                    });

                    if ($matchedPt) {
                        $propertyTypeId = $matchedPt->id;
                        $typeName = $matchedPt->name;
                    } else {
                        $plotPt = $propertyTypes->first(fn($pt) => strtolower(trim($pt->name)) === 'plot') ?: $propertyTypes->first();
                        if ($plotPt) {
                            $propertyTypeId = $plotPt->id;
                            $typeName = $plotPt->name;
                        } else {
                            $errors[] = "Invalid Property Type '{$typeInput}'. Type does not exist in master.";
                        }
                    }
                }
            } else {
                $errors[] = 'Property Type is required.';
            }

            // Property Code Validation & Action Determination (NEW vs UPDATE)
            $action = 'new';
            $existingPropertyId = null;

            if (empty($code)) {
                $errors[] = 'Property Code is required.';
            } else {
                $codeKey = strtolower($code);

                // Check DB for existing property code
                if ($targetFirmId && isset($existingPropertiesByFirm[$targetFirmId][$codeKey])) {
                    $action = 'update';
                    $existingPropertyId = $existingPropertiesByFirm[$targetFirmId][$codeKey];
                } else {
                    $action = 'new';
                }

                // Batch Duplicate Check (Same code repeated inside uploaded Excel file)
                $batchKey = ($targetFirmId ?: 0) . '_' . $codeKey;
                if (isset($batchCodes[$batchKey])) {
                    $errors[] = "Duplicate Property Code '{$code}' in Excel row {$batchCodes[$batchKey]}.";
                } else {
                    $batchCodes[$batchKey] = $r;
                }
            }

            // Property Name Validation
            if (empty($name)) {
                $errors[] = 'Property Name is required.';
            }

            // Status Validation
            $status = $statusInput ?: 'available';
            if (!in_array($status, $validStatuses)) {
                $errors[] = "Invalid Status '{$statusInput}'. Must be available, booked, sold, or rented.";
            }

            // Price Validation
            $price = null;
            if ($priceInput !== null && $priceInput !== '') {
                $cleanPrice = str_replace(',', '', $priceInput);
                if (!is_numeric($cleanPrice) || (float) $cleanPrice < 0) {
                    $errors[] = "Invalid Price '{$priceInput}'. Must be a valid positive number.";
                } else {
                    $price = (float) $cleanPrice;
                }
            }

            // Facing Validation
            $facing = null;
            if (!empty($facingInput)) {
                $facingLower = strtolower($facingInput);
                if (!in_array($facingLower, $validFacings)) {
                    $errors[] = "Invalid Facing direction '{$facingInput}'.";
                } else {
                    $facing = ucwords($facingLower);
                }
            }

            // Size Unit Validation
            $sizeUnit = null;
            if (!empty($sizeUnitInput)) {
                if (!in_array($sizeUnitInput, $validSizeUnits)) {
                    $errors[] = "Invalid Size Unit '{$sizeUnitInput}'. Allowed: sq.ft, sq.yard, sq.meter, acre, bigha.";
                } else {
                    $sizeUnit = $sizeUnitInput;
                }
            }

            // Image file mapping check
            $imageFound = false;
            if (!empty($imageFilename)) {
                $imgLower = strtolower(basename($imageFilename));
                if (isset($uploadedImages[$imgLower])) {
                    $imageFound = true;
                }
            }

            $isValid = empty($errors);
            if ($isValid) {
                $validCount++;
                if ($action === 'update') {
                    $updateCount++;
                } else {
                    $newCount++;
                }
            } else {
                $invalidCount++;
            }

            $parsedRows[] = [
                'row' => $r,
                'action' => $action,
                'existing_id' => $existingPropertyId,
                'firm_id' => $targetFirmId,
                'firm_name' => $targetFirmName,
                'project_id' => $projectId,
                'project_name' => $projectName ?: $projectInput,
                'property_type_id' => $propertyTypeId,
                'property_type_name' => $typeName ?: $typeInput,
                'property_code' => $code,
                'property_name' => $name,
                'status' => $status,
                'location' => $location,
                'city' => $city,
                'address' => $address,
                'size' => $size,
                'size_unit' => $sizeUnit,
                'price' => $price,
                'unit_no' => $unitNo,
                'floor_no' => $floorNo,
                'facing' => $facing,
                'description' => $description,
                'image_filename' => $imageFilename,
                'image_found' => $imageFound,
                'is_valid' => $isValid,
                'errors' => $errors,
            ];
        }

        // Save Batch Data to temp json file
        $batchData = [
            'batch_id' => $batchId,
            'total' => count($parsedRows),
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
            'new_count' => $newCount,
            'update_count' => $updateCount,
            'rows' => $parsedRows,
        ];

        file_put_contents("{$tempPath}/batch.json", json_encode($batchData, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'batch_id' => $batchId,
            'total_rows' => count($parsedRows),
            'valid_count' => $validCount,
            'invalid_count' => $invalidCount,
            'new_count' => $newCount,
            'update_count' => $updateCount,
            'preview' => $parsedRows,
        ]);
    }

    // ----------------------------------------------------------------
    // PROCESS EXCEL IMPORT
    // ----------------------------------------------------------------
    public function processImport(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|string',
        ]);

        $batchId = $request->batch_id;
        $tempPath = storage_path("app/public/temp_import/{$batchId}");
        $batchJsonFile = "{$tempPath}/batch.json";

        if (!file_exists($batchJsonFile)) {
            return response()->json([
                'success' => false,
                'message' => 'Import batch expired or not found. Please upload the Excel file again.',
            ], 422);
        }

        $batchData = json_decode(file_get_contents($batchJsonFile), true);
        $rows = $batchData['rows'] ?? [];

        $validRows = array_filter($rows, fn($r) => !empty($r['is_valid']));

        if (empty($validRows)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid properties to import.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $importedCount = 0;
            $createdCount = 0;
            $updatedCount = 0;

            foreach ($validRows as $row) {
                $mainImagePath = null;
                $hasNewImage = false;

                if (!empty($row['image_filename'])) {
                    $imgLower = strtolower(basename($row['image_filename']));
                    $sourceFile = "{$tempPath}/" . basename($row['image_filename']);
                    if (!file_exists($sourceFile)) {
                        foreach (glob("{$tempPath}/*") as $f) {
                            if (strtolower(basename($f)) === $imgLower) {
                                $sourceFile = $f;
                                break;
                            }
                        }
                    }
                    if (file_exists($sourceFile) && is_file($sourceFile)) {
                        $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);
                        $newFilename = 'properties/images/' . Str::slug($row['property_code']) . '-' . time() . '-' . Str::random(5) . '.' . $ext;
                        Storage::disk('public')->put($newFilename, file_get_contents($sourceFile));
                        $mainImagePath = $newFilename;
                        $hasNewImage = true;
                    }
                }

                $propertyData = [
                    'firm_id' => $row['firm_id'],
                    'project_id' => $row['project_id'],
                    'property_type_id' => $row['property_type_id'],
                    'property_code' => $row['property_code'],
                    'property_name' => $row['property_name'],
                    'status' => $row['status'],
                    'location' => $row['location'] ?: null,
                    'city' => $row['city'] ?: null,
                    'address' => $row['address'] ?: null,
                    'size' => $row['size'] ?: null,
                    'size_unit' => $row['size_unit'] ?: null,
                    'price' => $row['price'] ?: null,
                    'unit_no' => $row['unit_no'] ?: null,
                    'floor_no' => $row['floor_no'] ?: null,
                    'facing' => $row['facing'] ?: null,
                    'description' => $row['description'] ?: null,
                ];

                if ($row['action'] === 'update' && !empty($row['existing_id'])) {
                    $existingProperty = Property::find($row['existing_id']);
                    if ($existingProperty) {
                        $updateFields = [
                            'firm_id' => $row['firm_id'],
                            'project_id' => $row['project_id'],
                            'property_type_id' => $row['property_type_id'],
                            'property_code' => $row['property_code'],
                            'property_name' => $row['property_name'],
                            'status' => $row['status'],
                        ];

                        $optionalFields = [
                            'location', 'city', 'address', 'size', 'size_unit',
                            'price', 'unit_no', 'floor_no', 'facing', 'description'
                        ];

                        foreach ($optionalFields as $field) {
                            if (isset($row[$field]) && $row[$field] !== null && $row[$field] !== '') {
                                $updateFields[$field] = $row[$field];
                            } else {
                                $updateFields[$field] = $existingProperty->{$field};
                            }
                        }

                        if ($hasNewImage) {
                            $updateFields['main_image'] = $mainImagePath;
                        } else {
                            $updateFields['main_image'] = $existingProperty->main_image;
                        }

                        $existingProperty->update($updateFields);
                        $updatedCount++;
                    } else {
                        if ($hasNewImage) {
                            $propertyData['main_image'] = $mainImagePath;
                        }
                        Property::create($propertyData);
                        $createdCount++;
                    }
                } else {
                    if ($hasNewImage) {
                        $propertyData['main_image'] = $mainImagePath;
                    }
                    Property::create($propertyData);
                    $createdCount++;
                }

                $importedCount++;
            }

            DB::commit();

            // Cleanup temp directory
            if (file_exists($tempPath)) {
                array_map('unlink', glob("{$tempPath}/*"));
                @rmdir($tempPath);
            }

            $summaryMsg = "{$importedCount} properties processed successfully ({$createdCount} new, {$updatedCount} updated).";

            return response()->json([
                'success' => true,
                'message' => $summaryMsg,
                'imported_count' => $importedCount,
                'created_count' => $createdCount,
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred during import: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ----------------------------------------------------------------
    // BULK DELETE
    // ----------------------------------------------------------------
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:properties,id',
        ]);

        $ids = array_filter(array_map('intval', $request->ids));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one property to delete.',
            ], 422);
        }

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        DB::beginTransaction();
        try {
            $query = Property::whereIn('id', $ids);
            if (!$isAdmin && $firmId) {
                $query->where('firm_id', $firmId);
            }

            $properties = $query->get();

            if ($properties->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No valid properties found to delete or permission denied.',
                ], 403);
            }

            $deletedCount = 0;
            foreach ($properties as $property) {
                // Delete main_image from storage
                if ($property->main_image) {
                    Storage::disk('public')->delete($property->main_image);
                }
                // Delete document_file from storage
                if ($property->document_file) {
                    Storage::disk('public')->delete($property->document_file);
                }

                // Delete associated property documents if model/table exists
                if (class_exists(\App\Models\PropertyDocument::class)) {
                    $docs = \App\Models\PropertyDocument::where('property_id', $property->id)->get();
                    foreach ($docs as $doc) {
                        if ($doc->file_path) {
                            Storage::disk('public')->delete($doc->file_path);
                        }
                        $doc->delete();
                    }
                }

                $property->delete();
                $deletedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} " . ($deletedCount === 1 ? 'property' : 'properties') . ' deleted successfully.',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete selected properties. No records were deleted.',
            ], 500);
        }
    }

    // ----------------------------------------------------------------
    // SPREADSHEET PARSING HELPERS (PhpSpreadsheet + Native Fallback)
    // ----------------------------------------------------------------
    protected function parseSpreadsheetData(string $filePath): array
    {
        // 1. If PhpSpreadsheet is available, try it first
        if (class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray(null, true, true, true);
                if (!empty($data) && count($data) >= 1) {
                    return $data;
                }
            } catch (\Throwable $e) {
                // fallback to native
            }
        }

        // 2. Determine file type
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'csv' || $ext === 'txt') {
            return $this->parseCsvNatively($filePath);
        }

        // 3. Native XLSX parser via ZipArchive & SimpleXML
        return $this->parseXlsxNatively($filePath);
    }

    protected function parseXlsxNatively(string $filePath): array
    {
        if (!class_exists('ZipArchive')) {
            throw new \Exception('ZipArchive extension is required to read XLSX files. Please install or enable php-zip.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($filePath) !== true) {
            // Might be a CSV file renamed to XLSX or XLS
            try {
                return $this->parseCsvNatively($filePath);
            } catch (\Throwable $e) {
                throw new \Exception('Unable to open Excel file. Please ensure it is a valid .xlsx or .csv file.');
            }
        }

        // 1. Read Shared Strings (xl/sharedStrings.xml)
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Locate Worksheet XML
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'xl/worksheets/') && str_ends_with($name, '.xml')) {
                    $sheetXml = $zip->getFromName($name);
                    break;
                }
            }
        }
        $zip->close();

        if (!$sheetXml) {
            throw new \Exception('No worksheet data found in XLSX file.');
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            throw new \Exception('Could not parse worksheet XML data.');
        }

        $rows = [];
        $rowCounter = 1;
        foreach ($xml->sheetData->row as $row) {
            $rowNum = (int) ($row['r'] ?? $rowCounter);
            $rowData = [];
            foreach ($row->c as $cell) {
                $cellRef = (string) $cell['r'];
                $colLetter = preg_replace('/[0-9]/', '', $cellRef);
                $type = (string) ($cell['t'] ?? '');
                $val = isset($cell->v) ? (string) $cell->v : '';

                if ($type === 's' && isset($sharedStrings[(int) $val])) {
                    $val = $sharedStrings[(int) $val];
                } elseif ($type === 'inlineStr' && isset($cell->is->t)) {
                    $val = (string) $cell->is->t;
                }
                $rowData[$colLetter] = trim($val);
            }
            if (!empty($rowData)) {
                $rows[$rowNum] = $rowData;
            }
            $rowCounter++;
        }

        ksort($rows);
        $finalRows = [];
        $idx = 1;
        foreach ($rows as $r) {
            $finalRows[$idx] = $r;
            $idx++;
        }

        return $finalRows;
    }

    protected function parseCsvNatively(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('Unable to open CSV file.');
        }

        $rowIdx = 1;
        while (($data = fgetcsv($handle, 10000, ',')) !== false) {
            if (count($data) === 1 && str_contains($data[0], ';')) {
                $data = str_getcsv($data[0], ';');
            } elseif (count($data) === 1 && str_contains($data[0], "\t")) {
                $data = str_getcsv($data[0], "\t");
            }

            $letterRow = [];
            $colIndex = 0;
            foreach ($data as $cell) {
                $colLetter = $this->numToColLetter($colIndex);
                $cleanVal = preg_replace('/^\x{EF}\x{BB}\x{BF}/', '', (string) $cell);
                $letterRow[$colLetter] = trim($cleanVal);
                $colIndex++;
            }
            $rows[$rowIdx] = $letterRow;
            $rowIdx++;
        }
        fclose($handle);
        return $rows;
    }

    protected function numToColLetter(int $index): string
    {
        $letter = '';
        while ($index >= 0) {
            $letter = chr($index % 26 + 65) . $letter;
            $index = intdiv($index, 26) - 1;
        }
        return $letter;
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------
    private function authorise(Property $property): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($property->firm_id != $firmId) {
                abort(403);
            }
        }
    }
}
