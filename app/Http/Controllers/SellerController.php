<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerRequest;
use App\Models\Firm;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    private function authorise(Seller $seller): void
    {
        $user = auth()->user();
        if (!$user || $user->isAdmin()) return;

        $firmId = $user->firm_id ?? session('firm_id');
        if ($seller->firm_id && $seller->firm_id != $firmId) {
            abort(403, 'Unauthorized access to Seller.');
        }
    }

    public function index(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $query = Seller::with(['firm', 'propertyMasters']);

        if (!$isAdmin) {
            $query->where(function ($q) use ($firmId) {
                $q->where('firm_id', $firmId)->orWhereNull('firm_id');
            });
        } elseif ($request->filled('firm_id')) {
            $query->where('firm_id', $request->firm_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('pan_no', 'like', "%{$s}%")
                  ->orWhere('aadhaar_no', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sellers = $query->latest()->paginate(15)->withQueryString();
        $firms   = $isAdmin ? Firm::where('status', 'active')->orderBy('firm_name')->get() : collect();

        // KPI aggregates
        $baseQuery = Seller::query();
        if (!$isAdmin) {
            $baseQuery->where(function ($q) use ($firmId) {
                $q->where('firm_id', $firmId)->orWhereNull('firm_id');
            });
        }
        $totalSellers = (clone $baseQuery)->count();
        $activeSellers = (clone $baseQuery)->where('status', 'active')->count();

        return view('admin.sellers.index', compact('sellers', 'firms', 'totalSellers', 'activeSellers'));
    }

    public function create()
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firms = Firm::where('id', $firmId)->get();
        }

        return view('admin.sellers.create', compact('firms'));
    }

    public function store(SellerRequest $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = $isAdmin ? ($request->firm_id ?: null) : (auth()->user() ? auth()->user()->firm_id : session('firm_id'));

        $seller = Seller::create(array_merge($request->validated(), [
            'firm_id' => $firmId,
        ]));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Seller created successfully.',
                'seller'  => $seller,
            ]);
        }

        return redirect()->route('sellers.index')->with('success', "Seller '{$seller->name}' created successfully.");
    }

    public function quickStore(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'phone'  => 'nullable|string|max:20',
            'city'   => 'nullable|string|max:100',
            'pan_no' => 'nullable|string|max:50',
        ]);

        $firmId = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $seller = Seller::create([
            'firm_id' => $firmId,
            'name'    => trim($request->name),
            'mobile'  => $request->mobile,
            'phone'   => $request->phone,
            'city'    => $request->city,
            'pan_no'  => $request->pan_no,
            'status'  => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Seller '{$seller->name}' created successfully.",
            'seller'  => [
                'id'     => $seller->id,
                'name'   => $seller->name,
                'mobile' => $seller->mobile ?: $seller->phone,
                'city'   => $seller->city,
            ],
        ]);
    }

    public function show(Seller $seller)
    {
        $this->authorise($seller);
        $seller->load(['firm', 'propertyMasters.plots', 'propertyMasters.payments', 'purchases']);

        return view('admin.sellers.show', compact('seller'));
    }

    public function edit(Seller $seller)
    {
        $this->authorise($seller);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        if ($isAdmin) {
            $firms = Firm::where('status', 'active')->orderBy('firm_name')->get();
        } else {
            $firms = Firm::where('id', $firmId)->get();
        }

        return view('admin.sellers.edit', compact('seller', 'firms'));
    }

    public function update(SellerRequest $request, Seller $seller)
    {
        $this->authorise($seller);

        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = $isAdmin ? ($request->firm_id ?: $seller->firm_id) : $seller->firm_id;

        $seller->update(array_merge($request->validated(), [
            'firm_id' => $firmId,
        ]));

        return redirect()->route('sellers.index')->with('success', "Seller '{$seller->name}' updated successfully.");
    }

    public function destroy(Seller $seller)
    {
        $this->authorise($seller);

        $linkedProperties = $seller->propertyMasters()->count();
        if ($linkedProperties > 0) {
            return redirect()->back()->with('error', "Cannot delete Seller because {$linkedProperties} Property Master acquisition(s) are linked to this Seller.");
        }

        $sellerName = $seller->name;
        $seller->delete();

        return redirect()->route('sellers.index')->with('success', "Seller '{$sellerName}' deleted successfully.");
    }

    public function exportPdf(Request $request)
    {
        $isAdmin = auth()->user() && auth()->user()->isAdmin();
        $firmId  = auth()->user() ? auth()->user()->firm_id : session('firm_id');

        $query = Seller::with(['firm', 'propertyMasters']);
        if (!$isAdmin) {
            $query->where(function ($q) use ($firmId) {
                $q->where('firm_id', $firmId)->orWhereNull('firm_id');
            });
        }
        $sellers = $query->orderBy('name')->get();

        return view('admin.sellers.pdf', compact('sellers'));
    }

    public function downloadPdf(Seller $seller)
    {
        $this->authorise($seller);
        $seller->load(['firm', 'propertyMasters.plots', 'propertyMasters.payments', 'purchases']);

        return view('admin.sellers.show-pdf', compact('seller'));
    }
}
