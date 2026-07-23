<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\StockInward;
use App\Models\StockOutward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockReportController extends Controller
{
    private function getReportData(Request $request)
    {
        $firmId = Auth::user()->firm_id;

        $query = Material::with('materialCategory')
            ->where('firm_id', $firmId);

        if ($request->search) {
            $query->where('material_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filter_category) {
            $query->where('material_category_id', $request->filter_category);
        }

        $materials = $query->orderBy('material_name')->get();

        // Attach inward/outward totals to each material
        $materials->each(function ($material) {
            $totalInward = StockInward::where('material_id', $material->id)
                ->sum('quantity');

            $totalOutward = StockOutward::where('material_id', $material->id)
                ->sum('quantity');

            $material->total_inward   = (float) $totalInward;
            $material->total_outward  = (float) $totalOutward;
            $material->computed_stock = (float) $material->opening_stock
                + (float) $totalInward
                - (float) $totalOutward;
        });

        return $materials;
    }

    public function index(Request $request)
    {
        $materials  = $this->getReportData($request);
        $categories = MaterialCategory::where('firm_id', Auth::user()->firm_id)
            ->where('status', 'active')
            ->orderBy('category_name')
            ->get();

        $lowStockCount = $materials->filter(function ($m) {
            return $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0;
        })->count();

        return view('admin.stock-report.index', compact('materials', 'categories', 'lowStockCount'));
    }

    public function exportPdf(Request $request)
    {
        $materials  = $this->getReportData($request);
        $categories = MaterialCategory::where('firm_id', Auth::user()->firm_id)
            ->orderBy('category_name')->get();

        return view('admin.stock-report.pdf', compact('materials'));
    }

    public function exportExcel(Request $request)
    {
        $materials = $this->getReportData($request);
        $filename = 'stock-report-' . date('Y-m-d') . '.csv';

        $totalStock = $materials->sum('computed_stock');
        $lowStockCount = $materials->filter(function ($m) {
            return $m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0;
        })->count();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($materials, $totalStock, $lowStockCount) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Report Header
            fputcsv($handle, ['Delawala Properties & Management - Current Stock Report']);
            fputcsv($handle, ['Generated on', date('d M Y, h:i A')]);
            fputcsv($handle, []); // Blank row

            // Summary Section
            fputcsv($handle, ['SUMMARY']);
            fputcsv($handle, ['Total Materials', $materials->count()]);
            fputcsv($handle, ['Total Current Stock', number_format($totalStock, 3)]);
            fputcsv($handle, ['Low Stock Items', $lowStockCount]);
            fputcsv($handle, []); // Blank row

            // Data Header
            fputcsv($handle, [
                'Material Name', 'Category', 'Unit',
                'Opening Stock', 'Total Inward', 'Total Outward',
                'Current Stock', 'Minimum Stock', 'Stock Status'
            ]);

            // Data Rows
            foreach ($materials as $m) {
                $status = ($m->computed_stock <= $m->minimum_stock && $m->minimum_stock > 0)
                    ? 'Low Stock' : 'Available';

                fputcsv($handle, [
                    $m->material_name,
                    $m->materialCategory->category_name ?? '-',
                    $m->unit ?? '-',
                    number_format($m->opening_stock, 3),
                    number_format($m->total_inward, 3),
                    number_format($m->total_outward, 3),
                    number_format($m->computed_stock, 3),
                    number_format($m->minimum_stock, 3),
                    $status,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
