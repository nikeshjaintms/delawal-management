<?php

namespace App\Http\Controllers;

use App\Constants\ConstructionMaterials;
use App\Http\Requests\MaterialRequest;
use App\Models\Contractor;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $query = Material::with(['category', 'contractor.project', 'project']);

        if (!$isAdmin) {
            $query->where('firm_id', $firmId);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('material_name', 'like', '%'.$request->search.'%')
                  ->orWhere('specification', 'like', '%'.$request->search.'%')
                  ->orWhere('unit', 'like', '%'.$request->search.'%')
                  ->orWhereHas('category', function ($catq) use ($request) {
                      $catq->where('category_name', 'like', '%'.$request->search.'%');
                  })
                  ->orWhereHas('contractor', function ($cq) use ($request) {
                      $cq->where('contractor_name', 'like', '%'.$request->search.'%');
                  });
            });
        }

        if ($request->filled('material_category_id')) {
            $query->where('material_category_id', $request->material_category_id);
        }

        if ($request->filled('contractor_id')) {
            $query->where('contractor_id', $request->contractor_id);
        }

        $materials = $query->latest()->paginate(15)->withQueryString();

        $contractorsQuery = Contractor::with('project')->where('status', 'active')->orderBy('contractor_name');
        $categoriesQuery  = MaterialCategory::where('status', 'active')->orderBy('category_name');

        if (!$isAdmin) {
            $contractorsQuery->where('firm_id', $firmId);
            $categoriesQuery->where('firm_id', $firmId);
        }
        $contractors = $contractorsQuery->get();
        $categories  = $categoriesQuery->get();

        return view('admin.materials.index', compact('materials', 'contractors', 'categories'));
    }

    public function create()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        $contractorsQuery = Contractor::with('project')->where('status', 'active')->orderBy('contractor_name');
        $projectsQuery    = Project::orderBy('project_name');
        $categoriesQuery  = MaterialCategory::where('status', 'active')->orderBy('category_name');

        if (!$isAdmin) {
            $contractorsQuery->where('firm_id', $firmId);
            $projectsQuery->where('firm_id', $firmId);
            $categoriesQuery->where('firm_id', $firmId);
        }
        $contractors = $contractorsQuery->get();
        $projects    = $projectsQuery->get();
        $categories  = $categoriesQuery->get();
        $catalog     = ConstructionMaterials::CATALOG;

        return view('admin.materials.create', compact('contractors', 'projects', 'categories', 'catalog'));
    }

    public function store(MaterialRequest $request)
    {
        $user = Auth::user();
        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $opening = (float) ($request->opening_stock ?? 0);

        $projectId = $request->project_id;
        if (!$projectId && $request->contractor_id) {
            $contractor = Contractor::find($request->contractor_id);
            if ($contractor) {
                $projectId = $contractor->project_id;
            }
        }

        $primaryFirmId = reset($firmIds);
        $categoryId = $request->material_category_id ?: null;
        if ($request->filled('custom_category')) {
            $customName = trim($request->custom_category);
            if ($customName !== '') {
                $customCat = MaterialCategory::firstOrCreate(
                    ['firm_id' => $primaryFirmId, 'category_name' => $customName],
                    ['status' => 'active']
                );
                $categoryId = $customCat->id;
            }
        }

        $categoryName = '';
        if ($categoryId) {
            $cat = MaterialCategory::find($categoryId);
            if ($cat) $categoryName = $cat->category_name;
        } elseif ($request->filled('custom_category')) {
            $categoryName = trim($request->custom_category);
        }

        $spec = $request->specification ?: '';
        $matName = $request->material_name ?: trim($categoryName . ($spec ? ' ' . $spec : ''));
        if (empty($matName)) {
            $matName = 'Material';
        }

        $unitPrice  = (float) ($request->unit_price ?? 0);
        $totalPrice = (float) ($request->total_price ?? ($opening * $unitPrice));

        $material = Material::create([
            'firm_id'              => $primaryFirmId,
            'project_id'           => $projectId ?: null,
            'contractor_id'        => $request->contractor_id ?: null,
            'material_category_id' => $categoryId,
            'material_name'        => $matName,
            'specification'        => $spec ?: null,
            'unit'                 => $request->unit,
            'unit_price'           => $unitPrice,
            'total_price'          => $totalPrice,
            'opening_stock'        => $opening,
            'current_stock'        => $opening,
            'minimum_stock'        => $request->minimum_stock ?? 0,
            'status'               => $request->status,
        ]);

        $material->syncFirms($firmIds);

        return redirect()->route('materials.index')->with('success', 'Material added successfully.');
    }

    public function show(Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);
        $material->load(['category', 'contractor.project', 'project']);
        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);

        $contractorsQuery = Contractor::with('project')->where('status', 'active')->orderBy('contractor_name');
        $projectsQuery    = Project::orderBy('project_name');
        $categoriesQuery  = MaterialCategory::where('status', 'active')->orderBy('category_name');

        if (!$isAdmin) {
            $contractorsQuery->where('firm_id', $firmId);
            $projectsQuery->where('firm_id', $firmId);
            $categoriesQuery->where('firm_id', $firmId);
        }
        $contractors = $contractorsQuery->get();
        $projects    = $projectsQuery->get();
        $categories  = $categoriesQuery->get();
        $catalog     = ConstructionMaterials::CATALOG;

        return view('admin.materials.edit', compact('material', 'contractors', 'projects', 'categories', 'catalog'));
    }

    public function update(MaterialRequest $request, Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);

        $projectId = $request->project_id ?: $material->project_id;
        if ($request->contractor_id) {
            $contractor = Contractor::find($request->contractor_id);
            if ($contractor && $contractor->project_id) {
                $projectId = $contractor->project_id;
            }
        }

        $firmIds = $request->firm_ids;
        if (empty($firmIds)) {
            $firmIds = (array)($user ? $user->firm_id : session('firm_id'));
        }
        $primaryFirmId = reset($firmIds);

        $categoryId = $request->material_category_id ?: $material->material_category_id;
        if ($request->filled('custom_category')) {
            $customName = trim($request->custom_category);
            if ($customName !== '') {
                $customCat = MaterialCategory::firstOrCreate(
                    ['firm_id' => $primaryFirmId, 'category_name' => $customName],
                    ['status' => 'active']
                );
                $categoryId = $customCat->id;
            }
        }

        $categoryName = '';
        if ($categoryId) {
            $cat = MaterialCategory::find($categoryId);
            if ($cat) $categoryName = $cat->category_name;
        } elseif ($request->filled('custom_category')) {
            $categoryName = trim($request->custom_category);
        }

        $spec = $request->specification ?: $material->specification;
        $matName = $request->material_name ?: trim($categoryName . ($spec ? ' ' . $spec : ''));
        if (empty($matName)) {
            $matName = $material->material_name ?: 'Material';
        }

        $opening    = (float) ($request->opening_stock ?? $material->opening_stock);
        $unitPrice  = (float) ($request->unit_price ?? $material->unit_price);
        $totalPrice = (float) ($request->total_price ?? ($opening * $unitPrice));

        $material->update([
            'project_id'           => $projectId ?: null,
            'contractor_id'        => $request->contractor_id ?: null,
            'material_category_id' => $categoryId ?: null,
            'material_name'        => $matName,
            'specification'        => $spec ?: null,
            'unit'                 => $request->unit,
            'unit_price'           => $unitPrice,
            'total_price'          => $totalPrice,
            'opening_stock'        => $opening,
            'minimum_stock'        => $request->minimum_stock ?? $material->minimum_stock ?? 0,
            'status'               => $request->status,
        ]);
        $material->syncFirms($firmIds);

        return redirect()->route('materials.index')->with('success', 'Material updated successfully.');
    }

    public function destroy(Material $material)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();
        $firmId = $user ? $user->firm_id : session('firm_id');

        if (!$isAdmin && $material->firm_id != $firmId) abort(403);
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted successfully.');
    }
}
