<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AuditLog::query();

        // Filters
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('user_name')) {
            $query->where('user_name', $request->user_name);
        }

        if ($request->filled('module_name')) {
            $query->where('module_name', $request->module_name);
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        // Search (if general search is used or via filters)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('user_name', 'like', '%' . $search . '%')
                  ->orWhere('module_name', 'like', '%' . $search . '%')
                  ->orWhere('action_type', 'like', '%' . $search . '%')
                  ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Get filter options dynamically
        $users = AuditLog::select('user_name')
            ->whereNotNull('user_name')
            ->distinct()
            ->orderBy('user_name')
            ->pluck('user_name')
            ->toArray();

        $modules = AuditLog::select('module_name')
            ->distinct()
            ->orderBy('module_name')
            ->pluck('module_name')
            ->toArray();

        $actionTypes = [
            'Login',
            'Logout',
            'Create Record',
            'Update Record',
            'Delete Record',
            'Export PDF',
            'Export Excel',
            'Print',
            'Backup Generate'
        ];
        
        $dbActionTypes = AuditLog::select('action_type')
            ->distinct()
            ->pluck('action_type')
            ->toArray();
            
        $actionTypes = array_unique(array_merge($actionTypes, $dbActionTypes));
        sort($actionTypes);

        return view('admin.audit-logs.index', compact('logs', 'users', 'modules', 'actionTypes'));
    }

    /**
     * Async endpoint to track front-end print action.
     */
    public function track(Request $request)
    {
        $request->validate([
            'action_type' => 'required|string',
            'module_name' => 'required|string',
            'description' => 'required|string',
        ]);

        AuditLog::log($request->module_name, $request->action_type, $request->description);

        return response()->json(['status' => 'success']);
    }
}
