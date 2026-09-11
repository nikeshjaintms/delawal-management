<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertyMasterRequest;
use App\Models\PropertyMaster;
use App\Models\PropertyMasterPayment;
use App\Models\PaymentMode;
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

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('property_name', 'like', "%{$s}%")
                  ->orWhere('property_code', 'like', "%{$s}%")
                  ->orWhere('property_type', 'like', "%{$s}%")
                  ->orWhere('location',      'like', "%{$s}%")
                  ->orWhere('city',          'like', "%{$s}%")
                  ->orWhere('status',        'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $propertyMasters = $query->latest()->paginate(15)->withQueryString();
        $propertyTypes   = PropertyMaster::PROPERTY_TYPES;

        return view('admin.property-masters.index', compact('propertyMasters', 'propertyTypes'));
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

        $vendors       = \App\Models\Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers       = \App\Models\Broker::where('status', 'active')->orderBy('name')->get();
        $propertyTypes = PropertyMaster::PROPERTY_TYPES;

        return view('admin.property-masters.create', compact('firms', 'vendors', 'brokers', 'propertyTypes'));
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

        // Broker and Commission calculation
        $brokerId = $request->broker_id ?: null;
        $brokerName = $request->broker_name;
        if ($brokerId && empty($brokerName)) {
            $bObj = \App\Models\Broker::find($brokerId);
            if ($bObj) $brokerName = $bObj->name;
        }
        $brokerCommType = $request->broker_commission_type ?: 'percentage';
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : null;
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : 0.00;
        if ($brokerCommType === 'percentage' && $brokerCommRate && $brokerCommAmount == 0 && $purchasePrice > 0) {
            $brokerCommAmount = round(($purchasePrice * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : 0.00;
        $brokerCommDue = max(0.00, $brokerCommAmount - $brokerCommPaid);
        $brokerCommStatus = $request->broker_commission_status;
        if (empty($brokerCommStatus)) {
            if ($brokerCommAmount > 0) {
                if ($brokerCommPaid >= $brokerCommAmount) {
                    $brokerCommStatus = 'paid';
                } elseif ($brokerCommPaid > 0) {
                    $brokerCommStatus = 'partial';
                } else {
                    $brokerCommStatus = 'unpaid';
                }
            } else {
                $brokerCommStatus = 'unpaid';
            }
        }

        $unitNumbersList = $request->unit_numbers_list;
        $parsedUnits     = PropertyMaster::parseUnitNumbersString($unitNumbersList);
        $totalUnitsCount = $request->filled('total_units_count') ? (int)$request->total_units_count : (!empty($parsedUnits) ? count($parsedUnits) : null);
        $unitPrefix      = $request->unit_prefix ?: 'Plot ';

        $propertyMaster = PropertyMaster::create([
            'firm_id'                        => $firmId,
            'property_name'                  => $request->property_name,
            'property_code'                  => $propertyCode,
            'property_type'                  => $request->property_type,
            'purchase_price'                 => $purchasePrice,
            'paid_amount'                    => $paidAmount,
            'due_amount'                     => $dueAmount,
            'purchase_date'                  => $request->purchase_date ?: date('Y-m-d'),
            'purchase_rate'                  => $request->purchase_rate ?: null,
            'total_area'                     => $request->total_area ?: null,
            'area_unit'                      => $request->area_unit ?: 'Sq.Ft',
            'total_units_count'              => $totalUnitsCount,
            'unit_numbers_list'              => $unitNumbersList,
            'unit_prefix'                    => $unitPrefix,
            'seller_name'                    => $request->seller_name,
            'vendor_id'                      => $request->vendor_id ?: null,
            'broker_id'                      => $brokerId,
            'broker_name'                    => $brokerName,
            'broker_commission_type'         => $brokerCommType,
            'broker_commission_rate'         => $brokerCommRate,
            'broker_commission_amount'       => $brokerCommAmount,
            'broker_commission_paid'         => $brokerCommPaid,
            'broker_commission_due'          => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode,
            'broker_commission_status'       => $brokerCommStatus,
            'broker_notes'                   => $request->broker_notes,
            'payment_mode'                   => $request->payment_mode,
            'payment_status'                 => $paymentStatus,
            'location'                       => $request->location,
            'address'                        => $request->address,
            'city'                           => $request->city,
            'state'                          => $request->state,
            'country'                        => $request->country,
            'pincode'                        => $request->pincode,
            'description'                    => $request->description,
            'status'                         => $request->status,
            'main_image'                     => $mainImagePath,
            'document_file'                  => $documentPath,
            'created_by'                     => auth()->id(),
            'updated_by'                     => auth()->id(),
        ]);

        // Auto-generate unit plots if requested or unit numbers provided
        if (($request->boolean('auto_generate_units') || $request->filled('unit_numbers_list')) && !empty($parsedUnits)) {
            $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';
            $unitCount = count($parsedUnits);
            $unitPrice = ($unitCount > 0 && $purchasePrice > 0) ? round($purchasePrice / $unitCount, 2) : ($propertyMaster->purchase_rate ?: 0);
            $unitArea = ($unitCount > 0 && $propertyMaster->total_area > 0) ? round($propertyMaster->total_area / $unitCount, 2) : null;

            foreach ($parsedUnits as $unitNo) {
                $cleanUnit = trim((string)$unitNo);
                $plotCode = 'P-' . $propPrefix . '-' . str_pad($cleanUnit, 3, '0', STR_PAD_LEFT);
                if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                Property::create([
                    'firm_id'            => $firmId,
                    'property_master_id' => $propertyMaster->id,
                    'property_name'      => trim($unitPrefix . ' ' . $cleanUnit),
                    'property_code'      => $plotCode,
                    'unit_no'            => $cleanUnit,
                    'size'               => $unitArea,
                    'size_unit'          => $propertyMaster->area_unit ?: 'sq.ft',
                    'location'           => $propertyMaster->location,
                    'city'               => $propertyMaster->city,
                    'address'            => $propertyMaster->address,
                    'purchase_rate'      => $propertyMaster->purchase_rate,
                    'purchase_date'      => $propertyMaster->purchase_date ?: date('Y-m-d'),
                    'price'              => $unitPrice,
                    'status'             => 'available',
                    'description'        => 'Auto-created unit #' . $cleanUnit . ' under ' . $propertyMaster->property_name,
                ]);
            }
        }

        if ($paidAmount > 0) {
            $paymentModeId = null;
            if ($request->payment_mode) {
                $pm = PaymentMode::where('name', $request->payment_mode)->first();
                if ($pm) $paymentModeId = $pm->id;
            }

            PropertyMasterPayment::create([
                'property_master_id' => $propertyMaster->id,
                'firm_id'            => $firmId,
                'payment_mode_id'    => $paymentModeId,
                'amount'             => $paidAmount,
                'payment_date'       => $propertyMaster->purchase_date ?: date('Y-m-d'),
                'payment_mode'       => $request->payment_mode ?: 'Cash',
                'reference_no'       => $request->reference_no ?? null,
                'bank_name'          => $request->bank_name ?? null,
                'remarks'            => $request->remarks ?: 'Initial Payment / Advance',
                'created_by'         => auth()->id(),
            ]);
        }

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master created successfully.');
    }

    public function show(PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);
        $propertyMaster->load([
            'firm',
            'vendor',
            'broker',
            'projects.properties',
            'plots' => fn($q) => $q->with(['project', 'propertyType']),
            'payments.creator',
            'payments.paymentMode'
        ]);

        $propertyTypes = PropertyType::whereHas('firms', function ($q) use ($propertyMaster) {
            $q->where('firms.id', $propertyMaster->firm_id);
        })->orWhereDoesntHave('firms')->orderBy('name')->get();

        $projects = Project::where('firm_id', $propertyMaster->firm_id)->orderBy('project_name')->get();
        $paymentModes = PaymentMode::where('status', 'active')->orderBy('name')->get();

        return view('admin.property-masters.show', compact('propertyMaster', 'propertyTypes', 'projects', 'paymentModes'));
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

        $vendors       = \App\Models\Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers       = \App\Models\Broker::where('status', 'active')->orderBy('name')->get();
        $propertyTypes = PropertyMaster::PROPERTY_TYPES;

        return view('admin.property-masters.edit', compact('propertyMaster', 'firms', 'vendors', 'brokers', 'propertyTypes'));
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

        // Broker and Commission calculation
        $brokerId = $request->filled('broker_id') ? $request->broker_id : ($request->has('broker_id') ? null : $propertyMaster->broker_id);
        $brokerName = $request->filled('broker_name') ? $request->broker_name : ($brokerId ? ($propertyMaster->broker_id == $brokerId ? $propertyMaster->broker_name : (\App\Models\Broker::find($brokerId)?->name ?? null)) : null);
        $brokerCommType = $request->filled('broker_commission_type') ? $request->broker_commission_type : ($propertyMaster->broker_commission_type ?? 'percentage');
        $brokerCommRate = $request->filled('broker_commission_rate') ? floatval($request->broker_commission_rate) : ($request->has('broker_commission_rate') ? null : $propertyMaster->broker_commission_rate);
        $brokerCommAmount = $request->filled('broker_commission_amount') ? floatval($request->broker_commission_amount) : ($propertyMaster->broker_commission_amount ?? 0.00);
        if ($brokerCommType === 'percentage' && $brokerCommRate && ($brokerCommAmount == 0 || $request->filled('broker_commission_rate')) && $purchasePrice > 0) {
            $brokerCommAmount = round(($purchasePrice * $brokerCommRate) / 100, 2);
        }
        $brokerCommPaid = $request->filled('broker_commission_paid') ? floatval($request->broker_commission_paid) : ($propertyMaster->broker_commission_paid ?? 0.00);
        $brokerCommDue = max(0.00, $brokerCommAmount - $brokerCommPaid);
        $brokerCommStatus = $request->broker_commission_status;
        if (empty($brokerCommStatus)) {
            if ($brokerCommAmount > 0) {
                if ($brokerCommPaid >= $brokerCommAmount) {
                    $brokerCommStatus = 'paid';
                } elseif ($brokerCommPaid > 0) {
                    $brokerCommStatus = 'partial';
                } else {
                    $brokerCommStatus = 'unpaid';
                }
            } else {
                $brokerCommStatus = 'unpaid';
            }
        }

        $unitNumbersList = $request->has('unit_numbers_list') ? $request->unit_numbers_list : $propertyMaster->unit_numbers_list;
        $parsedUnits     = PropertyMaster::parseUnitNumbersString($unitNumbersList);
        $totalUnitsCount = $request->filled('total_units_count') ? (int)$request->total_units_count : (!empty($parsedUnits) ? count($parsedUnits) : $propertyMaster->total_units_count);
        $unitPrefix      = $request->unit_prefix ?: ($propertyMaster->unit_prefix ?? 'Plot ');

        $propertyMaster->update([
            'firm_id'                        => $firmId,
            'property_name'                  => $request->property_name,
            'property_code'                  => $request->property_code ?: $propertyMaster->property_code,
            'property_type'                  => $request->property_type,
            'purchase_price'                 => $purchasePrice,
            'paid_amount'                    => $paidAmount,
            'due_amount'                     => $dueAmount,
            'purchase_date'                  => $request->purchase_date ?: $propertyMaster->purchase_date,
            'purchase_rate'                  => $request->purchase_rate ?: $propertyMaster->purchase_rate,
            'total_area'                     => $request->total_area ?: $propertyMaster->total_area,
            'area_unit'                      => $request->area_unit ?: ($propertyMaster->area_unit ?? 'Sq.Ft'),
            'total_units_count'              => $totalUnitsCount,
            'unit_numbers_list'              => $unitNumbersList,
            'unit_prefix'                    => $unitPrefix,
            'seller_name'                    => $request->seller_name ?: $propertyMaster->seller_name,
            'vendor_id'                      => $request->vendor_id ?: $propertyMaster->vendor_id,
            'broker_id'                      => $brokerId,
            'broker_name'                    => $brokerName,
            'broker_commission_type'         => $brokerCommType,
            'broker_commission_rate'         => $brokerCommRate,
            'broker_commission_amount'       => $brokerCommAmount,
            'broker_commission_paid'         => $brokerCommPaid,
            'broker_commission_due'          => $brokerCommDue,
            'broker_commission_payment_mode' => $request->broker_commission_payment_mode ?: $propertyMaster->broker_commission_payment_mode,
            'broker_commission_status'       => $brokerCommStatus,
            'broker_notes'                   => $request->broker_notes ?: $propertyMaster->broker_notes,
            'payment_mode'                   => $request->payment_mode ?: $propertyMaster->payment_mode,
            'payment_status'                 => $paymentStatus,
            'location'                       => $request->location,
            'address'                        => $request->address,
            'city'                           => $request->city,
            'state'                          => $request->state,
            'country'                        => $request->country,
            'pincode'                        => $request->pincode,
            'description'                    => $request->description,
            'status'                         => $request->status,
            'main_image'                     => $mainImagePath,
            'document_file'                  => $documentPath,
            'updated_by'                     => auth()->id(),
        ]);

        // Auto-generate missing unit plots if requested
        if ($request->boolean('auto_generate_units') && !empty($parsedUnits)) {
            $existingUnitNos = $propertyMaster->plots()->pluck('unit_no')->map(fn($u) => trim((string)$u))->toArray();
            $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';
            $unitCount = count($parsedUnits);
            $unitPrice = ($unitCount > 0 && $purchasePrice > 0) ? round($purchasePrice / $unitCount, 2) : ($propertyMaster->purchase_rate ?: 0);
            $unitArea = ($unitCount > 0 && $propertyMaster->total_area > 0) ? round($propertyMaster->total_area / $unitCount, 2) : null;

            foreach ($parsedUnits as $unitNo) {
                $cleanUnit = trim((string)$unitNo);
                if (in_array($cleanUnit, $existingUnitNos)) {
                    continue; // skip if already created
                }

                $plotCode = 'P-' . $propPrefix . '-' . str_pad($cleanUnit, 3, '0', STR_PAD_LEFT);
                if (Property::where('firm_id', $firmId)->where('property_code', $plotCode)->exists()) {
                    $plotCode .= '-' . Str::random(3);
                }

                Property::create([
                    'firm_id'            => $firmId,
                    'property_master_id' => $propertyMaster->id,
                    'property_name'      => trim($unitPrefix . ' ' . $cleanUnit),
                    'property_code'      => $plotCode,
                    'unit_no'            => $cleanUnit,
                    'size'               => $unitArea,
                    'size_unit'          => $propertyMaster->area_unit ?: 'sq.ft',
                    'location'           => $propertyMaster->location,
                    'city'               => $propertyMaster->city,
                    'address'            => $propertyMaster->address,
                    'purchase_rate'      => $propertyMaster->purchase_rate,
                    'purchase_date'      => $propertyMaster->purchase_date ?: date('Y-m-d'),
                    'price'              => $unitPrice,
                    'status'             => 'available',
                    'description'        => 'Auto-created unit #' . $cleanUnit . ' under ' . $propertyMaster->property_name,
                ]);
            }
        }

        if ($propertyMaster->payments()->count() === 0 && $paidAmount > 0) {
            $paymentModeId = null;
            if ($request->payment_mode) {
                $pm = PaymentMode::where('name', $request->payment_mode)->first();
                if ($pm) $paymentModeId = $pm->id;
            }

            PropertyMasterPayment::create([
                'property_master_id' => $propertyMaster->id,
                'firm_id'            => $firmId,
                'payment_mode_id'    => $paymentModeId,
                'amount'             => $paidAmount,
                'payment_date'       => $propertyMaster->purchase_date ?: date('Y-m-d'),
                'payment_mode'       => $request->payment_mode ?: 'Cash',
                'reference_no'       => $request->reference_no ?? null,
                'bank_name'          => $request->bank_name ?? null,
                'remarks'            => 'Initial Payment / Advance',
                'created_by'         => auth()->id(),
            ]);
        } elseif ($propertyMaster->payments()->count() > 0) {
            $propertyMaster->recalculatePaymentStatus();
        }

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Property Master updated successfully.');
    }

    public function storePayment(Request $request, PropertyMaster $propertyMaster)
    {
        $this->authorise($propertyMaster);

        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string|max:100',
            'reference_no' => 'nullable|string|max:150',
            'bank_name'    => 'nullable|string|max:150',
            'remarks'      => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $firmId = $propertyMaster->firm_id ?? ($user ? $user->firm_id : session('firm_id'));

        $paymentModeId = null;
        $pm = PaymentMode::where('name', $validated['payment_mode'])->first();
        if ($pm) $paymentModeId = $pm->id;

        PropertyMasterPayment::create([
            'property_master_id' => $propertyMaster->id,
            'firm_id'            => $firmId,
            'payment_mode_id'    => $paymentModeId,
            'amount'             => (float)$validated['amount'],
            'payment_date'       => $validated['payment_date'],
            'payment_mode'       => $validated['payment_mode'],
            'reference_no'       => $validated['reference_no'] ?? null,
            'bank_name'          => $validated['bank_name'] ?? null,
            'remarks'            => $validated['remarks'] ?? null,
            'created_by'         => $user ? $user->id : null,
        ]);

        $propertyMaster->recalculatePaymentStatus();

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Payment installment of ₹' . number_format($validated['amount'], 2) . ' recorded successfully.');
    }

    public function destroyPayment(PropertyMaster $propertyMaster, PropertyMasterPayment $payment)
    {
        $this->authorise($propertyMaster);

        if ($payment->property_master_id != $propertyMaster->id) {
            abort(404);
        }

        $amount = $payment->amount;
        $payment->delete();
        $propertyMaster->recalculatePaymentStatus();

        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', 'Payment record of ₹' . number_format($amount, 2) . ' removed successfully.');
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
            'total_plots'        => 'nullable|integer|min:1|max:1000',
            'unit_numbers_list'  => 'nullable|string|max:2000',
            'plot_prefix'        => 'nullable|string|max:50',
            'start_number'       => 'nullable|integer|min:1',
            'property_type_id'   => 'nullable|exists:property_types,id',
            'size'               => 'nullable|numeric|min:0',
            'size_unit'          => 'nullable|string|max:50',
            'facing'             => 'nullable|string|max:50',
            'purchase_rate'      => 'nullable|numeric|min:0',
            'price'              => 'nullable|numeric|min:0',
            'project_id'         => 'nullable|exists:projects,id',
        ]);

        $customList = $request->unit_numbers_list;
        $parsedUnits = PropertyMaster::parseUnitNumbersString($customList);
        $prefix = $request->plot_prefix !== null ? $request->plot_prefix : 'Plot ';
        $propPrefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $propertyMaster->property_name), 0, 4)) ?: 'PROP';

        $purchaseRate = $request->filled('purchase_rate') ? $request->purchase_rate : ($propertyMaster->purchase_rate ?: 0);
        $price = $request->filled('price') ? $request->price : $purchaseRate;
        $sizeUnit = $request->size_unit ?: 'sq.ft';

        $generatedUnits = [];

        DB::transaction(function () use ($propertyMaster, $parsedUnits, $prefix, $propPrefix, $purchaseRate, $price, $sizeUnit, $request, &$generatedUnits) {
            if (!empty($parsedUnits)) {
                // Generate by parsed list (e.g. 1-10, 30, 35)
                foreach ($parsedUnits as $cleanUnit) {
                    $plotCode = 'P-' . $propPrefix . '-' . str_pad($cleanUnit, 3, '0', STR_PAD_LEFT);
                    if (Property::where('firm_id', $propertyMaster->firm_id)->where('property_code', $plotCode)->exists()) {
                        $plotCode .= '-' . Str::random(3);
                    }

                    Property::create([
                        'firm_id'            => $propertyMaster->firm_id,
                        'property_master_id' => $propertyMaster->id,
                        'project_id'         => $request->project_id ?: null,
                        'property_type_id'   => $request->property_type_id ?: null,
                        'property_name'      => trim($prefix . ' ' . $cleanUnit),
                        'property_code'      => $plotCode,
                        'unit_no'            => (string) $cleanUnit,
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
                    $generatedUnits[] = $cleanUnit;
                }
            } else {
                // Generate sequential
                $count = (int) ($request->total_plots ?: 1);
                $startNum = $request->filled('start_number') ? (int) $request->start_number : $propertyMaster->getNextPlotSequenceNumber();

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
                        'property_name'      => trim($prefix . ' ' . $num),
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
                    $generatedUnits[] = $num;
                }
            }

            if ($request->project_id) {
                $project = Project::find($request->project_id);
                if ($project) {
                    $project->propertyMasters()->syncWithoutDetaching([$propertyMaster->id]);
                }
            }
        });

        $genCount = count($generatedUnits);
        return redirect()->route('property-masters.show', $propertyMaster->id)
            ->with('success', "{$genCount} plots/units generated successfully (" . implode(', ', array_slice($generatedUnits, 0, 10)) . ($genCount > 10 ? '...' : '') . ").");
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
            'broker',
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
