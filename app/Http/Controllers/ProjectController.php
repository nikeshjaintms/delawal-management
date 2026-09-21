<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\BrokerCommission;
use App\Models\ContractorPayment;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Project::with(['firm', 'propertyMasters', 'propertyMaster'])->withCount('properties');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_id', $request->property_id)
                  ->orWhereHas('propertyMasters', function ($sub) use ($request) {
                      $sub->where('property_masters.id', $request->property_id);
                  });
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('project_name', 'like', "%{$s}%")
                  ->orWhere('project_code', 'like', "%{$s}%")
                  ->orWhere('project_type', 'like', "%{$s}%")
                  ->orWhere('city',         'like', "%{$s}%")
                  ->orWhere('status',       'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->paginate(15)->withQueryString();
        
        $propertyMaster = null;
        if ($request->filled('property_id')) {
            $propertyMaster = PropertyMaster::find($request->property_id);
        }

        return view('admin.projects.index', compact('projects', 'propertyMaster'));
    }

    public function create(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $query = PropertyMaster::with([
            'plots' => function ($q) {
                $q->whereNull('project_id')->where('status', 'available');
            }
        ]);

        if ($isAdmin) {
            $properties = $query->orderBy('property_name')->get();
        } else {
            $properties = $query->where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        $selectedPropertyIds = (array) ($request->get('property_ids') ?: ($request->get('property_id') ? [$request->get('property_id')] : []));

        return view('admin.projects.create', compact('properties', 'selectedPropertyIds'));
    }

    public function store(ProjectRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $propertyMasterIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $propertyMasterIds = array_values(array_filter($propertyMasterIds));

        // Ensure property belongs to firm if specified
        if (!empty($propertyMasterIds)) {
            $prop = PropertyMaster::find($propertyMasterIds[0]);
            if ($prop) {
                $firmId = $prop->firm_id;
            }
        }

        $projectCode = $request->project_code;
        if (empty($projectCode)) {
            $latest = Project::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $projectCode = 'PRJ-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        $imagePath = null;
        if ($request->hasFile('project_image')) {
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        return DB::transaction(function () use ($request, $firmId, $projectCode, $imagePath, $propertyMasterIds) {
            $primaryPropertyId = !empty($propertyMasterIds) ? $propertyMasterIds[0] : null;

            $project = Project::create([
                'firm_id'       => $firmId,
                'property_id'   => $primaryPropertyId,
                'project_name'  => $request->project_name,
                'project_code'  => $projectCode,
                'project_type'  => $request->project_type,
                'address'       => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'country'       => $request->country,
                'pincode'       => $request->pincode,
                'description'   => $request->description,
                'status'        => $request->status,
                'project_image' => $imagePath,
                'created_by'    => auth()->id(),
                'updated_by'    => auth()->id(),
            ]);

            // Sync multiple Property Masters to pivot table
            if (!empty($propertyMasterIds)) {
                $project->syncPropertyMasters($propertyMasterIds);
            }

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project created successfully.');
        });
    }

    public function show(Project $project)
    {
        $this->authorise($project);
        Property::syncAllStatuses();
        $project->load([
            'propertyMasters',
            'propertyMaster',
            'firm',
            'properties.propertyType',
            'properties.propertyMaster',
            'properties.bookings.customer',
            'properties.bookingsList.customer',
            'properties.sales.customer',
            'contractors',
            'vendors',
            'expenses' => function ($q) {
                $q->with(['expenseCategory', 'purchaseOrder.vendor'])->orderBy('expense_date', 'desc')->orderBy('id', 'desc');
            },
            'purchaseOrders' => function ($q) {
                $q->with(['vendor', 'items'])->orderBy('po_date', 'desc')->orderBy('id', 'desc');
            },
        ]);

        $project->setRelation('properties', Property::naturalSort($project->properties));

        $projectExpenses = $project->expenses;
        $totalExpenses = (float) $projectExpenses->sum('amount');
        $poExpensesTotal = (float) $projectExpenses->whereNotNull('purchase_order_id')->sum('amount');
        $directExpensesTotal = (float) $projectExpenses->whereNull('purchase_order_id')->sum('amount');
        $approvedExpensesTotal = (float) $projectExpenses->where('approval_status', 'Approved')->sum('amount');
        $pendingExpensesTotal = (float) $projectExpenses->where('approval_status', 'Pending')->sum('amount');

        // Contractor Payments tied to this project
        $contractorPayments = ContractorPayment::with(['contractor', 'property', 'paymentMode'])
            ->where(function ($q) use ($project) {
                $q->where('project_id', $project->id)
                  ->orWhereHas('property', fn($p) => $p->where('project_id', $project->id))
                  ->orWhereHas('contractor', fn($c) => $c->where('project_id', $project->id)->orWhereHas('projects', fn($cp) => $cp->where('projects.id', $project->id)));
            })
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $contractorPaymentsTotal = (float) $contractorPayments->sum('amount');

        // Broker Commissions tied to this project
        $brokerCommissions = BrokerCommission::with(['broker', 'property', 'booking', 'customer'])
            ->where(function ($q) use ($project) {
                $q->whereHas('property', fn($p) => $p->where('project_id', $project->id))
                  ->orWhereHas('booking.property', fn($bp) => $bp->where('project_id', $project->id));
            })
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $brokerCommissionsTotal = (float) $brokerCommissions->sum('commission_amount');

        // Land Acquisition / Property Master Payments tied to this project
        $propertyMasterIds = $project->propertyMasters->pluck('id')->toArray();
        if ($project->property_id && !in_array($project->property_id, $propertyMasterIds)) {
            $propertyMasterIds[] = $project->property_id;
        }
        $landPayments = !empty($propertyMasterIds)
            ? PropertyMasterPayment::with(['propertyMaster', 'paymentMode'])
                ->whereIn('property_master_id', $propertyMasterIds)
                ->orderBy('payment_date', 'desc')
                ->orderBy('id', 'desc')
                ->get()
            : collect();
        $landPaymentsTotal = (float) $landPayments->sum('amount');

        $grandTotalProjectCost = $totalExpenses + $contractorPaymentsTotal + $brokerCommissionsTotal + $landPaymentsTotal;

        $projectPOs = $project->purchaseOrders;
        $poTotalAmount = (float) $projectPOs->sum('grand_total');

        $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($project) {
            $q->where('firms.id', $project->firm_id);
        })->orWhereDoesntHave('firms')->orderBy('name')->get();

        return view('admin.projects.show', compact(
            'project',
            'projectExpenses',
            'totalExpenses',
            'poExpensesTotal',
            'directExpensesTotal',
            'approvedExpensesTotal',
            'pendingExpensesTotal',
            'contractorPayments',
            'contractorPaymentsTotal',
            'brokerCommissions',
            'brokerCommissionsTotal',
            'landPayments',
            'landPaymentsTotal',
            'grandTotalProjectCost',
            'projectPOs',
            'poTotalAmount',
            'propertyTypes'
        ));
    }

    public function edit(Project $project)
    {
        $this->authorise($project);
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $project->firm_id;

        $query = PropertyMaster::with([
            'plots' => function ($q) use ($project) {
                $q->where(function ($sub) use ($project) {
                    $sub->whereNull('project_id')
                        ->orWhere('project_id', $project->id);
                });
            }
        ]);

        if ($isAdmin) {
            $properties = $query->orderBy('property_name')->get();
        } else {
            $properties = $query->where('firm_id', $firmId)->orderBy('property_name')->get();
        }

        $project->load(['properties', 'propertyMasters']);

        $selectedPropertyIds = $project->propertyMasters->pluck('id')->toArray();
        if (empty($selectedPropertyIds) && $project->property_id) {
            $selectedPropertyIds = [$project->property_id];
        }

        return view('admin.projects.edit', compact('project', 'properties', 'selectedPropertyIds'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorise($project);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = $isAdmin ? $request->firm_id : $project->firm_id;

        $propertyMasterIds = (array) ($request->property_ids ?: ($request->property_id ? [$request->property_id] : []));
        $propertyMasterIds = array_values(array_filter($propertyMasterIds));

        if (!empty($propertyMasterIds)) {
            $prop = PropertyMaster::find($propertyMasterIds[0]);
            if ($prop) {
                $firmId = $prop->firm_id;
            }
        }

        $projectCode = $request->project_code;
        if (empty($projectCode)) {
            $projectCode = $project->project_code;
        }

        $imagePath = $project->project_image;
        if ($request->hasFile('project_image')) {
            if ($project->project_image) {
                Storage::disk('public')->delete($project->project_image);
            }
            $imagePath = $request->file('project_image')->store('projects/images', 'public');
        }

        return DB::transaction(function () use ($request, $project, $firmId, $projectCode, $imagePath, $propertyMasterIds) {
            $primaryPropertyId = !empty($propertyMasterIds) ? $propertyMasterIds[0] : null;

            $project->update([
                'firm_id'       => $firmId,
                'property_id'   => $primaryPropertyId,
                'project_name'  => $request->project_name,
                'project_code'  => $projectCode,
                'project_type'  => $request->project_type,
                'address'       => $request->address,
                'city'          => $request->city,
                'state'         => $request->state,
                'country'       => $request->country,
                'pincode'       => $request->pincode,
                'description'   => $request->description,
                'status'        => $request->status,
                'project_image' => $imagePath,
                'updated_by'    => auth()->id(),
            ]);

            // Sync multiple property masters
            $project->syncPropertyMasters($propertyMasterIds);

            return redirect()->route('projects.show', $project->id)
                ->with('success', 'Project details updated successfully.');
        });
    }

    /**
     * AJAX endpoint: fetch plots from multiple Property Masters
     */
    public function getPropertiesAndPlots(Request $request)
    {
        $propertyIds = $request->get('property_ids');
        if (is_string($propertyIds)) {
            $propertyIds = explode(',', $propertyIds);
        }
        $propertyIds = array_values(array_filter((array) $propertyIds));

        $projectId = $request->get('project_id');

        if (empty($propertyIds)) {
            return response()->json([
                'success'    => true,
                'properties' => [],
            ]);
        }

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $query = PropertyMaster::whereIn('id', $propertyIds);
        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        }

        $propertyMasters = $query->with(['plots' => function ($q) use ($projectId) {
            $q->where(function ($sub) use ($projectId) {
                $sub->whereNull('project_id');
                if ($projectId) {
                    $sub->orWhere('project_id', $projectId);
                }
            });
        }])->get();

        $result = [];
        foreach ($propertyMasters as $pm) {
            $sortedPlots = Property::naturalSort($pm->plots);
            $result[] = [
                'id'            => $pm->id,
                'property_name' => $pm->property_name,
                'property_code' => $pm->property_code,
                'location'      => $pm->location,
                'city'          => $pm->city,
                'address'       => $pm->address,
                'state'         => $pm->state,
                'country'       => $pm->country,
                'pincode'       => $pm->pincode,
                'full_address'  => $pm->full_address,
                'purchase_rate' => $pm->purchase_rate,
                'plots'         => $sortedPlots->map(function ($plot) {
                    return [
                        'id'            => $plot->id,
                        'property_name' => $plot->property_name,
                        'property_code' => $plot->property_code,
                        'unit_no'       => $plot->unit_no,
                        'size'          => $plot->size,
                        'size_unit'     => $plot->size_unit,
                        'facing'        => $plot->facing,
                        'purchase_rate' => $plot->purchase_rate,
                        'price'         => $plot->price,
                        'status'        => $plot->status,
                        'project_id'    => $plot->project_id,
                    ];
                }),
            ];
        }

        return response()->json([
            'success'    => true,
            'properties' => $result,
        ]);
    }

    public function destroy(Project $project)
    {
        $this->authorise($project);

        // Check if any plots in this project are already booked or sold
        $bookedOrSold = $project->properties()->whereIn('status', ['booked', 'sold'])->count();
        if ($bookedOrSold > 0) {
            return redirect()->back()
                ->with('error', "Cannot delete Project because {$bookedOrSold} plot(s) are already booked or sold.");
        }

        if ($project->project_image) {
            Storage::disk('public')->delete($project->project_image);
        }

        $propertyId = $project->property_id;

        DB::transaction(function () use ($project) {
            // Unassign master plots back to available inventory
            $project->properties()
                ->whereNotNull('property_master_id')
                ->update(['project_id' => null]);

            // Delete any standalone plots created solely for this project
            $project->properties()->whereNull('property_master_id')->delete();

            // Detach pivot table
            $project->propertyMasters()->detach();

            // Delete contractors attached to this project
            $project->contractors()->delete();

            $project->delete();
        });

        if ($propertyId) {
            return redirect()->route('property-masters.show', $propertyId)
                ->with('success', 'Project deleted successfully and unbooked plots returned to available inventory.');
        }

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function exportPdf(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $query = Project::with(['firm', 'propertyMasters', 'propertyMaster', 'properties'])->withCount('properties');

        if ($isAdmin) {
            if ($request->filled('firm_id')) {
                $query->where('firm_id', $request->firm_id);
            }
        } else {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->filled('property_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('property_id', $request->property_id)
                  ->orWhereHas('propertyMasters', function ($sub) use ($request) {
                      $sub->where('property_masters.id', $request->property_id);
                  });
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('project_name', 'like', "%{$s}%")
                  ->orWhere('project_code', 'like', "%{$s}%")
                  ->orWhere('project_type', 'like', "%{$s}%")
                  ->orWhere('city',         'like', "%{$s}%")
                  ->orWhere('status',       'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->latest()->get();
        $totalProjects = $projects->count();
        $totalPlots = $projects->sum('properties_count');
        $totalValue = $projects->sum(function ($p) {
            return $p->properties->sum('price');
        });

        return view('admin.projects.pdf', compact('projects', 'totalProjects', 'totalPlots', 'totalValue'));
    }

    public function downloadPdf(Project $project)
    {
        $this->authorise($project);
        $project->load([
            'propertyMasters',
            'propertyMaster',
            'firm',
            'properties.propertyType',
            'properties.propertyMaster',
            'contractors',
            'expenses' => function ($q) {
                $q->with(['expenseCategory', 'purchaseOrder.vendor'])->orderBy('expense_date', 'desc');
            },
            'purchaseOrders.vendor',
        ]);

        $project->setRelation('properties', Property::naturalSort($project->properties));

        $totalPlots = $project->properties->count();
        $availablePlots = $project->properties->where('status', 'available')->count();
        $bookedPlots = $project->properties->where('status', 'booked')->count();
        $soldPlots = $project->properties->where('status', 'sold')->count();
        $totalValue = $project->properties->sum('price');
        $totalArea = $project->properties->sum('size');

        $projectExpenses = $project->expenses;
        $totalExpenses = (float) $projectExpenses->sum('amount');
        $poExpensesTotal = (float) $projectExpenses->whereNotNull('purchase_order_id')->sum('amount');
        $directExpensesTotal = (float) $projectExpenses->whereNull('purchase_order_id')->sum('amount');

        $contractorPayments = ContractorPayment::with(['contractor', 'property', 'paymentMode'])
            ->where(function ($q) use ($project) {
                $q->where('project_id', $project->id)
                  ->orWhereHas('property', fn($p) => $p->where('project_id', $project->id))
                  ->orWhereHas('contractor', fn($c) => $c->where('project_id', $project->id)->orWhereHas('projects', fn($cp) => $cp->where('projects.id', $project->id)));
            })
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        $contractorPaymentsTotal = (float) $contractorPayments->sum('amount');
        $grandTotalProjectCost = $totalExpenses + $contractorPaymentsTotal;

        return view('admin.projects.show-pdf', compact(
            'project', 'totalPlots', 'availablePlots', 'bookedPlots', 'soldPlots', 'totalValue', 'totalArea',
            'projectExpenses', 'totalExpenses', 'poExpensesTotal', 'directExpensesTotal',
            'contractorPayments', 'contractorPaymentsTotal', 'grandTotalProjectCost'
        ));
    }

    // ─────────────────────────────────────────────────────────────────
    // DIRECT PROJECT PLOT ACTIONS & SPREADSHEET IMPORT
    // ─────────────────────────────────────────────────────────────────

    /**
     * Download Excel template for Project plots import
     */
    public function downloadPlotsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Project Plots Template');

        $headers = [
            'A1' => 'Plot / Unit No *',
            'B1' => 'Plot Name *',
            'C1' => 'Size (Numeric)',
            'D1' => 'Size Unit (sq.ft / sq.yard)',
            'E1' => 'Facing Direction (East/West/North/South)',
            'F1' => 'Purchase / Cost Rate (₹)',
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
        $fileName = 'project_plots_import_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import plots directly from Excel for this Project
     */
    public function importPlots(Request $request, Project $project)
    {
        $this->authorise($project);

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        $res = $this->importPlotsFromSpreadsheet($request->file('excel_file'), $project);
        $count = $res['count'] ?? 0;

        if ($count === 0) {
            return redirect()->back()->with('error', 'No valid plots could be imported from the Excel file. Please ensure data rows are present.');
        }

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', "{$count} plots imported successfully into project '{$project->project_name}'.");
    }

    /**
     * Universal, resilient Plot Importer for Project from Spreadsheet
     */
    public function importPlotsFromSpreadsheet($uploadedFile, Project $project): array
    {
        $path = is_string($uploadedFile) ? $uploadedFile : $uploadedFile->getRealPath();
        $ext = is_string($uploadedFile) ? pathinfo($uploadedFile, PATHINFO_EXTENSION) : strtolower($uploadedFile->getClientOriginalExtension());
        $rows = [];

        if (in_array($ext, ['xlsx', 'xls']) && class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            try {
                $spreadsheet = IOFactory::load($path);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray(null, true, true, false);
            } catch (\Throwable $e) {
                $rows = [];
            }
        }

        if (empty($rows)) {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle, 5000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (empty($rows) || count($rows) < 1) {
            return ['count' => 0, 'plots' => []];
        }

        // 1. Locate header row dynamically within first 15 rows
        $headerRow = [];
        $headerRowIndex = 0;
        $dataStartRowIndex = 1;
        $knownHeaderKeywords = [
            'unit', 'plot', 'size', 'area', 'sqft', 'sqyd', 'facing', 'rate', 'price',
            'type', 'code', 'name', 'status', 'city', 'location', 'address', 'floor', 'amount'
        ];

        foreach ($rows as $rowIndex => $rowCells) {
            if ($rowIndex > 15)
                break;
            if (!is_array($rowCells))
                continue;

            $matchedCount = 0;
            foreach ($rowCells as $cell) {
                if (is_null($cell))
                    continue;
                $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string) $cell));
                foreach ($knownHeaderKeywords as $kw) {
                    if (str_contains($clean, $kw)) {
                        $matchedCount++;
                        break;
                    }
                }
            }

            if ($matchedCount >= 2) {
                $headerRow = $rowCells;
                $headerRowIndex = $rowIndex;
                $dataStartRowIndex = $rowIndex + 1;
                break;
            }
        }

        if (empty($headerRow) && !empty($rows)) {
            $headerRow = $rows[0] ?? [];
            $headerRowIndex = 0;
            $dataStartRowIndex = 1;
        }

        // 2. Build column mapping dictionary with exact normalized aliases
        $exactDict = [
            'unit_no' => ['unitno', 'unitnumber', 'plotno', 'unitnoplotno', 'unitnoplotno*', 'flatno', 'unit#', 'plot#', 'plotunitno', 'plotunitno*', 'plot/unitno*', 'plot/unitno'],
            'plot_name' => ['plotname', 'plotname*', 'propertyname', 'propertyname*', 'unitname', 'name', 'title'],
            'plot_code' => ['plotcode', 'plotcode*', 'propertycode', 'propertycode*', 'unitcode', 'code', 'propcode'],
            'size' => ['size', 'area', 'plotsize', 'plotarea', 'sizearea', 'size/area', 'sqft', 'areainsqft', 'areainsqyd', 'carpetarea', 'builtuparea', 'superarea', 'dimensionsize', 'dimension', 'sqyards', 'sqmeter', 'acre', 'bigha', 'plotareainsqft', 'sizenumeric', 'size(numeric)'],
            'size_unit' => ['sizeunit', 'measurementunit', 'areatype', 'areauom', 'uom', 'unittype', 'sizeunitsqftsqyard', 'sizeunit(sq.ft/sq.yard)'],
            'facing' => ['facing', 'direction', 'orientation', 'plotfacing', 'facingdirection', 'facingdirectioneastwestnorthsouth', 'facingdirection(east/west/north/south)'],
            'purchase_rate' => ['purchaserate', 'buyrate', 'originalpurchaserate', 'rate', 'costrate', 'batchrate', 'purchaserateperunit', 'purchaserateinr', 'purchaserate(₹)', 'purchaserate₹'],
            'price' => ['price', 'sellingprice', 'price(inr)', 'priceinr', 'askingprice', 'priceaskingprice', 'saleprice', 'amount', 'cost', 'value', 'expectedprice', 'sellingprice(₹)', 'sellingprice₹'],
            'status' => ['status', 'propertystatus', 'propertystatus*', 'state', 'statusavailablebookedsold', 'status(available/booked/sold)'],
            'property_type' => ['propertytype', 'propertytype*', 'proptype', 'type', 'category', 'kind', 'projecttype'],
            'floor_no' => ['floorno', 'floor', 'level'],
            'description' => ['description', 'descriptionnotes', 'notes', 'remarks', 'details', 'propertydescription'],
            'location' => ['location', 'loc', 'landmark'],
            'city' => ['city', 'town'],
            'address' => ['address', 'addr'],
        ];

        $columnMap = [];
        $unmappedCols = [];

        // Pass 1: Exact Match
        foreach ($headerRow as $colIdx => $rawHeader) {
            $cleanBom = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', (string) $rawHeader);
            $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanBom));
            if (empty($norm))
                continue;

            $matchedKey = null;
            foreach ($exactDict as $fieldKey => $validNorms) {
                if (in_array($norm, $validNorms, true)) {
                    $matchedKey = $fieldKey;
                    break;
                }
            }

            if ($matchedKey && !isset($columnMap[$matchedKey])) {
                $columnMap[$matchedKey] = $colIdx;
            } else {
                $unmappedCols[$colIdx] = $norm;
            }
        }

        // Pass 2: Substring Match with Strict Negative Exclusions
        foreach ($unmappedCols as $colIdx => $norm) {
            if (!isset($columnMap['size_unit']) && str_contains($norm, 'unit') && !str_contains($norm, 'no') && !str_contains($norm, 'num') && !str_contains($norm, 'plot')) {
                $columnMap['size_unit'] = $colIdx;
            } elseif (!isset($columnMap['unit_no']) && (str_contains($norm, 'unit') || str_contains($norm, 'plot')) && (str_contains($norm, 'no') || str_contains($norm, 'num') || str_contains($norm, '#'))) {
                $columnMap['unit_no'] = $colIdx;
            } elseif (!isset($columnMap['purchase_rate']) && (str_contains($norm, 'purchaserate') || str_contains($norm, 'buyrate') || (str_contains($norm, 'rate') && !str_contains($norm, 'selling')))) {
                $columnMap['purchase_rate'] = $colIdx;
            } elseif (!isset($columnMap['price']) && (str_contains($norm, 'price') || str_contains($norm, 'selling') || str_contains($norm, 'asking')) && !str_contains($norm, 'rate')) {
                $columnMap['price'] = $colIdx;
            } elseif (!isset($columnMap['size']) && (str_contains($norm, 'size') || str_contains($norm, 'area') || str_contains($norm, 'sqft') || str_contains($norm, 'sqyd'))) {
                $columnMap['size'] = $colIdx;
            } elseif (!isset($columnMap['facing']) && (str_contains($norm, 'facing') || str_contains($norm, 'direction') || str_contains($norm, 'orient'))) {
                $columnMap['facing'] = $colIdx;
            } elseif (!isset($columnMap['status']) && str_contains($norm, 'status')) {
                $columnMap['status'] = $colIdx;
            } elseif (!isset($columnMap['property_type']) && (str_contains($norm, 'propertytype') || str_contains($norm, 'category'))) {
                $columnMap['property_type'] = $colIdx;
            } elseif (!isset($columnMap['plot_name']) && str_contains($norm, 'name') && !str_contains($norm, 'firm') && !str_contains($norm, 'company') && !str_contains($norm, 'project')) {
                $columnMap['plot_name'] = $colIdx;
            } elseif (!isset($columnMap['plot_code']) && str_contains($norm, 'code') && !str_contains($norm, 'firm') && !str_contains($norm, 'company') && !str_contains($norm, 'project')) {
                $columnMap['plot_code'] = $colIdx;
            } elseif (!isset($columnMap['description']) && (str_contains($norm, 'desc') || str_contains($norm, 'note') || str_contains($norm, 'remark'))) {
                $columnMap['description'] = $colIdx;
            }
        }

        $projPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->project_name), 0, 4)) ?: 'PRJ';
        $firmId = $project->firm_id;
        $createdCount = 0;
        $createdPlots = [];

        // Pre-resolve PropertyType
        $defaultTypeObj = PropertyType::withoutGlobalScopes()->firstOrCreate(
            ['name' => 'Plot'],
            ['status' => 'active']
        );
        if ($firmId && method_exists($defaultTypeObj, 'firms')) {
            $defaultTypeObj->firms()->syncWithoutDetaching([$firmId]);
        }
        $defaultTypeId = $defaultTypeObj->id;

        $validFacingMap = [
            'east' => 'East',
            'west' => 'West',
            'north' => 'North',
            'south' => 'South',
            'northeast' => 'North-East',
            'north-east' => 'North-East',
            'northwest' => 'North-West',
            'north-west' => 'North-West',
            'southeast' => 'South-East',
            'south-east' => 'South-East',
            'southwest' => 'South-West',
            'south-west' => 'South-West',
        ];

        DB::transaction(function () use (
            $rows,
            $dataStartRowIndex,
            $columnMap,
            $project,
            $projPrefix,
            $firmId,
            $defaultTypeId,
            $validFacingMap,
            &$createdCount,
            &$createdPlots
        ) {
            $currentNextSeq = $project->getNextPlotSequenceNumber();

            for ($i = $dataStartRowIndex; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (!is_array($row) || empty(array_filter($row, fn($v) => !is_null($v) && trim((string) $v) !== ''))) {
                    continue; // Skip empty row
                }

                $getCol = function ($key) use ($row, $columnMap) {
                    if (isset($columnMap[$key]) && isset($row[$columnMap[$key]])) {
                        return trim((string) $row[$columnMap[$key]]);
                    }
                    return '';
                };

                $rawUnit = $getCol('unit_no');
                $rawName = $getCol('plot_name');
                $rawCode = $getCol('plot_code');
                $rawSize = $getCol('size');
                $rawUnitType = $getCol('size_unit');
                $rawFacing = $getCol('facing');
                $rawPurchaseRate = $getCol('purchase_rate');
                $rawPrice = $getCol('price');
                $rawStatus = $getCol('status');
                $rawType = $getCol('property_type');
                $rawDesc = $getCol('description');

                // 1. Determine Unit No & Plot Name
                $cleanUnit = $rawUnit !== '' ? $rawUnit : (string) $currentNextSeq++;
                $plotName = $rawName !== '' ? $rawName : ('Plot ' . $cleanUnit);

                // 2. Numeric Size
                $size = null;
                if ($rawSize !== '') {
                    $cleanedSize = preg_replace('/[^0-9.]/', '', $rawSize);
                    if (is_numeric($cleanedSize) && (float) $cleanedSize > 0) {
                        $size = round((float) $cleanedSize, 2);
                    }
                }

                // 3. Size Unit
                $sizeUnit = 'sq.ft';
                if (!empty($rawUnitType)) {
                    $normU = strtolower(preg_replace('/[^a-zA-Z]/', '', $rawUnitType));
                    if (str_contains($normU, 'yard') || str_contains($normU, 'yd') || str_contains($normU, 'var') || str_contains($normU, 'gaj')) {
                        $sizeUnit = 'sq.yard';
                    } elseif (str_contains($normU, 'meter') || str_contains($normU, 'mt') || str_contains($normU, 'sqm')) {
                        $sizeUnit = 'sq.meter';
                    } elseif (str_contains($normU, 'acre')) {
                        $sizeUnit = 'acre';
                    } elseif (str_contains($normU, 'bigha') || str_contains($normU, 'vigha')) {
                        $sizeUnit = 'bigha';
                    }
                }

                // 4. Facing
                $facing = null;
                if (!empty($rawFacing)) {
                    $normF = strtolower(preg_replace('/[^a-zA-Z]/', '', $rawFacing));
                    if (isset($validFacingMap[$normF])) {
                        $facing = $validFacingMap[$normF];
                    }
                }

                // 5. Rates & Prices
                $rateVal = 0.0;
                if ($rawPurchaseRate !== '') {
                    $rateVal = (float) preg_replace('/[^0-9.]/', '', $rawPurchaseRate);
                }

                $priceVal = 0.0;
                if ($rawPrice !== '') {
                    $priceVal = (float) preg_replace('/[^0-9.]/', '', $rawPrice);
                }
                if ($priceVal <= 0 && $rateVal > 0) {
                    $priceVal = $rateVal;
                }

                // 6. Status
                $status = 'available';
                if (!empty($rawStatus)) {
                    $normS = strtolower(trim($rawStatus));
                    if (in_array($normS, ['booked', 'book', 'reserved', 'reserve', 'hold'])) {
                        $status = 'booked';
                    } elseif (in_array($normS, ['sold', 'sale', 'close', 'closed'])) {
                        $status = 'sold';
                    } elseif (in_array($normS, ['rented', 'rent', 'lease', 'leased'])) {
                        $status = 'rented';
                    } elseif (in_array($normS, ['blocked', 'block', 'inactive', 'unavailable'])) {
                        $status = 'inactive';
                    }
                }

                // 7. Property Type ID
                $propertyTypeId = $defaultTypeId;
                if (!empty($rawType)) {
                    $ptObj = PropertyType::withoutGlobalScopes()->firstOrCreate(
                        ['name' => ucwords(trim($rawType))],
                        ['status' => 'active']
                    );
                    if ($firmId && method_exists($ptObj, 'firms')) {
                        $ptObj->firms()->syncWithoutDetaching([$firmId]);
                    }
                    $propertyTypeId = $ptObj->id;
                }

                // 8. Plot Code
                $plotCode = !empty($rawCode) ? $rawCode : ('P-' . $projPrefix . '-' . preg_replace('/[^A-Za-z0-9]/', '', $cleanUnit));
                if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                // 9. Description
                $description = $rawDesc !== '' ? $rawDesc : ('Imported via Excel under ' . $project->project_name);

                $plot = Property::create([
                    'firm_id' => $firmId,
                    'project_id' => $project->id,
                    'property_master_id' => $project->property_id ?: null,
                    'property_type_id' => $propertyTypeId,
                    'property_name' => $plotName,
                    'property_code' => $plotCode,
                    'unit_no' => (string) $cleanUnit,
                    'size' => $size,
                    'size_unit' => $sizeUnit,
                    'facing' => $facing,
                    'location' => $project->address,
                    'city' => $project->city,
                    'address' => $project->address,
                    'purchase_rate' => $rateVal,
                    'purchase_date' => date('Y-m-d'),
                    'price' => $priceVal,
                    'status' => $status,
                    'description' => $description,
                ]);

                $createdCount++;
                $createdPlots[] = $plot;
            }
        });

        return ['count' => $createdCount, 'plots' => $createdPlots];
    }

    /**
     * Add a single plot to Project
     */
    public function addSinglePlot(Request $request, Project $project)
    {
        $this->authorise($project);

        $request->validate([
            'property_name' => 'required|string|max:255',
            'unit_no' => 'nullable|string|max:50',
            'property_type_id' => 'nullable|exists:property_types,id',
            'size' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|max:50',
            'facing' => 'nullable|string|max:50',
            'purchase_rate' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,booked,sold,rented,inactive',
            'description' => 'nullable|string',
        ]);

        $projPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->project_name), 0, 4)) ?: 'PRJ';
        $unitNo = $request->unit_no ?: (string) $project->getNextPlotSequenceNumber();
        $plotCode = 'P-' . $projPrefix . '-' . str_pad($unitNo, 3, '0', STR_PAD_LEFT);

        if (Property::where('firm_id', $project->firm_id)->where('property_code', $plotCode)->exists()) {
            $plotCode .= '-' . Str::random(3);
        }

        $purchaseRate = $request->filled('purchase_rate') ? $request->purchase_rate : 0;
        $price = $request->filled('price') ? $request->price : $purchaseRate;

        // Resolve PropertyType ID
        $targetTypeId = $request->property_type_id;
        if (!$targetTypeId) {
            $defaultTypeObj = PropertyType::withoutGlobalScopes()->firstOrCreate(
                ['name' => 'Plot'],
                ['status' => 'active']
            );
            if ($project->firm_id && method_exists($defaultTypeObj, 'firms')) {
                $defaultTypeObj->firms()->syncWithoutDetaching([$project->firm_id]);
            }
            $targetTypeId = $defaultTypeObj->id;
        }

        // Validate Facing direction
        $facing = null;
        if ($request->filled('facing')) {
            $validFacings = ['East', 'West', 'North', 'South', 'North-East', 'North-West', 'South-East', 'South-West'];
            $matchFacing = collect($validFacings)->first(fn($f) => strcasecmp($f, trim($request->facing)) === 0);
            if ($matchFacing)
                $facing = $matchFacing;
        }

        $plot = Property::create([
            'firm_id' => $project->firm_id,
            'project_id' => $project->id,
            'property_master_id' => $project->property_id ?: null,
            'property_type_id' => $targetTypeId,
            'property_name' => $request->property_name,
            'property_code' => $plotCode,
            'unit_no' => $unitNo,
            'size' => $request->size ?: null,
            'size_unit' => $request->size_unit ?: 'sq.ft',
            'facing' => $facing,
            'location' => $project->address,
            'city' => $project->city,
            'address' => $project->address,
            'purchase_rate' => $purchaseRate,
            'purchase_date' => date('Y-m-d'),
            'price' => $price,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', "Plot '{$plot->property_name}' added successfully to project.");
    }

    /**
     * Bulk generate sequential plots for Project
     */
    public function bulkGeneratePlots(Request $request, Project $project)
    {
        $this->authorise($project);

        $request->validate([
            'total_plots' => 'nullable|integer|min:1|max:1000',
            'unit_numbers_list' => 'nullable|string|max:2000',
            'plot_prefix' => 'nullable|string|max:50',
            'start_number' => 'nullable|integer|min:1',
            'property_type_id' => 'nullable|exists:property_types,id',
            'size' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|max:50',
            'facing' => 'nullable|string|max:50',
            'purchase_rate' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $customList = $request->unit_numbers_list;
        $parsedUnits = PropertyMaster::parseUnitNumbersString($customList);
        $prefix = $request->plot_prefix !== null ? $request->plot_prefix : 'Plot ';
        $projPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $project->project_name), 0, 4)) ?: 'PRJ';

        $purchaseRate = $request->filled('purchase_rate') ? $request->purchase_rate : 0;
        $price = $request->filled('price') ? $request->price : $purchaseRate;
        $sizeUnit = $request->size_unit ?: 'sq.ft';

        // Resolve PropertyType ID
        $targetTypeId = $request->property_type_id;
        if (!$targetTypeId) {
            $defaultTypeObj = PropertyType::withoutGlobalScopes()->firstOrCreate(
                ['name' => 'Plot'],
                ['status' => 'active']
            );
            if ($project->firm_id && method_exists($defaultTypeObj, 'firms')) {
                $defaultTypeObj->firms()->syncWithoutDetaching([$project->firm_id]);
            }
            $targetTypeId = $defaultTypeObj->id;
        }

        // Validate Facing direction
        $facing = null;
        if ($request->filled('facing')) {
            $validFacings = ['East', 'West', 'North', 'South', 'North-East', 'North-West', 'South-East', 'South-West'];
            $matchFacing = collect($validFacings)->first(fn($f) => strcasecmp($f, trim($request->facing)) === 0);
            if ($matchFacing)
                $facing = $matchFacing;
        }

        $generatedUnits = [];

        DB::transaction(function () use ($project, $parsedUnits, $prefix, $projPrefix, $purchaseRate, $price, $sizeUnit, $targetTypeId, $facing, $request, &$generatedUnits) {
            if (!empty($parsedUnits)) {
                // Generate by parsed list (e.g. 1-10, 30, 35)
                foreach ($parsedUnits as $cleanUnit) {
                    $plotCode = 'P-' . $projPrefix . '-' . str_pad($cleanUnit, 3, '0', STR_PAD_LEFT);
                    if (Property::where('firm_id', $project->firm_id)->where('property_code', $plotCode)->exists()) {
                        $plotCode .= '-' . Str::random(3);
                    }

                    Property::create([
                        'firm_id' => $project->firm_id,
                        'project_id' => $project->id,
                        'property_master_id' => $project->property_id ?: null,
                        'property_type_id' => $targetTypeId,
                        'property_name' => trim($prefix . ' ' . $cleanUnit),
                        'property_code' => $plotCode,
                        'unit_no' => (string) $cleanUnit,
                        'size' => $request->size ?: null,
                        'size_unit' => $sizeUnit,
                        'facing' => $facing,
                        'location' => $project->address,
                        'city' => $project->city,
                        'address' => $project->address,
                        'purchase_rate' => $purchaseRate,
                        'purchase_date' => date('Y-m-d'),
                        'price' => $price,
                        'status' => 'available',
                        'description' => 'Bulk generated under project ' . $project->project_name,
                    ]);
                    $generatedUnits[] = $cleanUnit;
                }
            } else {
                // Generate sequential
                $count = (int) ($request->total_plots ?: 1);
                $startNum = $request->filled('start_number') ? (int) $request->start_number : $project->getNextPlotSequenceNumber();

                for ($i = 0; $i < $count; $i++) {
                    $num = $startNum + $i;
                    $plotCode = 'P-' . $projPrefix . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);

                    if (Property::where('firm_id', $project->firm_id)->where('property_code', $plotCode)->exists()) {
                        $plotCode .= '-' . Str::random(3);
                    }

                    Property::create([
                        'firm_id' => $project->firm_id,
                        'project_id' => $project->id,
                        'property_master_id' => $project->property_id ?: null,
                        'property_type_id' => $targetTypeId,
                        'property_name' => trim($prefix . ' ' . $num),
                        'property_code' => $plotCode,
                        'unit_no' => (string) $num,
                        'size' => $request->size ?: null,
                        'size_unit' => $sizeUnit,
                        'facing' => $facing,
                        'location' => $project->address,
                        'city' => $project->city,
                        'address' => $project->address,
                        'purchase_rate' => $purchaseRate,
                        'purchase_date' => date('Y-m-d'),
                        'price' => $price,
                        'status' => 'available',
                        'description' => 'Bulk generated under project ' . $project->project_name,
                    ]);
                    $generatedUnits[] = $num;
                }
            }
        });

        $genCount = count($generatedUnits);
        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', "{$genCount} plots/units generated successfully in project (" . implode(', ', array_slice($generatedUnits, 0, 10)) . ($genCount > 10 ? '...' : '') . ').');
    }

    /**
     * Quick update a plot from project show view
     */
    public function updatePlot(Request $request, Project $project, Property $property)
    {
        $this->authorise($project);

        if ($property->project_id != $project->id) {
            abort(404);
        }

        $request->validate([
            'property_name' => 'required|string|max:255',
            'property_code' => 'required|string|max:100',
            'size' => 'nullable|numeric|min:0',
            'size_unit' => 'nullable|string|max:50',
            'facing' => 'nullable|string|max:50',
            'purchase_rate' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,booked,sold,rented,inactive',
            'description' => 'nullable|string',
        ]);

        $property->update([
            'property_name' => $request->property_name,
            'property_code' => $request->property_code,
            'size' => $request->size ?: null,
            'size_unit' => $request->size_unit ?: 'sq.ft',
            'facing' => $request->facing ?: null,
            'purchase_rate' => $request->purchase_rate ?: 0,
            'price' => $request->price ?: 0,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', "Plot '{$property->property_name}' updated successfully.");
    }

    /**
     * Delete a plot from Project
     */
    public function destroyPlot(Project $project, Property $property)
    {
        $this->authorise($project);

        if ($property->project_id != $project->id) {
            abort(404);
        }

        if (in_array($property->status, ['booked', 'sold'])) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete plot '{$property->property_name}' because its status is '{$property->status}'.");
        }

        $plotName = $property->property_name;
        $property->delete();

        return redirect()
            ->route('projects.show', $project->id)
            ->with('success', "Plot '{$plotName}' deleted successfully.");
    }

    private function authorise(Project $project): void
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        if (!$isAdmin) {
            $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');
            if ($project->firm_id != $firmId) {
                abort(403);
            }
        }
    }
}

