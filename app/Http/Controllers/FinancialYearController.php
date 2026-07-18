<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialYearRequest;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class FinancialYearController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialYear::query();

        if ($request->filled('search')) {
            $query->where('year_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $years = $query->latest()->paginate(10)->withQueryString();

        return view('admin.firm-management.financial-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.firm-management.financial-years.create');
    }

    public function store(FinancialYearRequest $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $validated = $request->validated();
                $validated['is_active'] = $request->boolean('is_active');

                $year = FinancialYear::create($validated);

                // If marked active, deactivate all others
                if ($year->is_active) {
                    $year->activateExclusively();
                }

                return redirect()->route('financial-years.index')
                    ->with('success', 'Financial year created successfully!');
            } catch (Exception $e) {
                return back()->withInput()->with('error', 'Error creating financial year: ' . $e->getMessage());
            }
        });
    }

    public function show(FinancialYear $financialYear)
    {
        return view('admin.firm-management.financial-years.show', compact('financialYear'));
    }

    public function edit(FinancialYear $financialYear)
    {
        return view('admin.firm-management.financial-years.edit', compact('financialYear'));
    }

    public function update(FinancialYearRequest $request, FinancialYear $financialYear)
    {
        return DB::transaction(function () use ($request, $financialYear) {
            try {
                $validated = $request->validated();
                $validated['is_active'] = $request->boolean('is_active');

                $financialYear->update($validated);

                // If marked active, deactivate all others
                if ($financialYear->is_active) {
                    $financialYear->activateExclusively();
                }

                return redirect()->route('financial-years.index')
                    ->with('success', 'Financial year updated successfully!');
            } catch (Exception $e) {
                return back()->withInput()->with('error', 'Error updating financial year: ' . $e->getMessage());
            }
        });
    }

    public function destroy(FinancialYear $financialYear)
    {
        return DB::transaction(function () use ($financialYear) {
            try {
                if ($financialYear->is_active) {
                    return back()->with('error', 'Cannot delete the active financial year!');
                }

                $financialYear->delete();

                return redirect()->route('financial-years.index')
                    ->with('success', 'Financial year deleted successfully!');
            } catch (Exception $e) {
                return back()->with('error', 'Error deleting financial year: ' . $e->getMessage());
            }
        });
    }

    public function setActive(FinancialYear $financialYear)
    {
        return DB::transaction(function () use ($financialYear) {
            try {
                $financialYear->activateExclusively();
                return back()->with('success', "'{$financialYear->year_name}' is now the active financial year.");
            } catch (Exception $e) {
                return back()->with('error', 'Error setting active year: ' . $e->getMessage());
            }
        });
    }
}
