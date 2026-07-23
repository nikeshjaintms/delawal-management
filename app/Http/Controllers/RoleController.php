<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->latest()->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(RoleRequest $request)
    {
        $validated = $request->validated();
        // Keep 'name' in sync with 'role_name'
        $validated['name'] = $validated['role_name'];

        Role::create($validated);

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function show(Role $role)
    {
        $role->loadCount('users');
        $role->load('permissions');

        $actions = ['view', 'add', 'edit', 'delete', 'print', 'export'];

        // Group all permissions by module for the matrix
        $permissions = Permission::orderBy('module_name')->orderBy('action')->get()
                                 ->groupBy('module_name');

        // The permission keys that this role actually has
        $assignedPermissions = $role->permissions()->pluck('permission_key')->toArray();

        return view('admin.roles.show', compact('role', 'permissions', 'assignedPermissions', 'actions'));
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $validated = $request->validated();
        // Keep 'name' in sync with 'role_name'
        $validated['name'] = $validated['role_name'];

        $role->update($validated);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting Admin role
        if ($role->id === 1) {
            return back()->with('error', 'Cannot delete Admin role!');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role with assigned users!');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

    public function toggleStatus(Role $role)
    {
        // Prevent deactivating Admin role
        if ($role->id === 1) {
            return back()->with('error', 'Cannot deactivate Admin role!');
        }

        $role->status = $role->status === 'active' ? 'inactive' : 'active';
        $role->save();

        return back()->with('success', 'Role status updated successfully!');
    }

    public function permissions(Role $role)
    {
        $actions = ['view', 'add', 'edit', 'delete', 'print', 'export'];

        // Group permissions by module
        $permissions = Permission::orderBy('module_name')->orderBy('action')->get()
                                 ->groupBy('module_name');

        // Get currently assigned permission IDs for this role
        $assignedPermissionIds = $role->permissions()->pluck('permissions.id')->toArray();

        return view('admin.roles.permissions', compact('role', 'permissions', 'assignedPermissionIds', 'actions'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validated();
        $permissionIds = $validated['permissions'] ?? [];

        // Sync permissions
        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.permissions', $role)->with('success', 'Permissions updated successfully!');
    }
}
