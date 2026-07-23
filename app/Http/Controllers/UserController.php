<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;

use App\Models\User;
use App\Models\Role;
use App\Models\Firm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->isAdmin();

        $query = User::with(['role', 'firm']);

        if (!$isAdmin) {
            $firmId = $user ? $user->firm_id : session('firm_id');
            $query->where('firm_id', $firmId);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile_number', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::all();
        $firms = Firm::all();

        return view('admin.users.index', compact('users', 'roles', 'firms'));
    }

    public function create()
    {
        $roles = Role::all();
        $firms = Firm::all();
        return view('admin.users.create', compact('roles', 'firms'));
    }

    public function store(UserRequest $request)
    {
        $role = Role::find($request->role_id);

        User::create([
            'firm_id'       => $request->firm_id,
            'role_id'       => $request->role_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'mobile_number' => $request->mobile_number,
            'password'      => Hash::make($request->password),
            'role'          => strtolower($role->name),
            'status'        => $request->status,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function toggleStatus(User $user)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();
        $authFirmId = $authUser ? $authUser->firm_id : session('firm_id');

        if (!$isAdmin && $user->firm_id != $authFirmId) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot change your own status.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->route('users.index')->with('success', 'User status updated successfully.');
    }

    public function show(User $user)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();
        $authFirmId = $authUser ? $authUser->firm_id : session('firm_id');

        if (!$isAdmin && $user->firm_id != $authFirmId) {
            abort(403);
        }
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();
        $authFirmId = $authUser ? $authUser->firm_id : session('firm_id');

        if (!$isAdmin && $user->firm_id != $authFirmId) {
            abort(403);
        }
        $roles = Role::all();
        $firms = Firm::all();
        return view('admin.users.edit', compact('user', 'roles', 'firms'));
    }

    public function update(UserRequest $request, User $user)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();
        $authFirmId = $authUser ? $authUser->firm_id : session('firm_id');

        if (!$isAdmin && $user->firm_id != $authFirmId) {
            abort(403);
        }

        $role = Role::find($request->role_id);

        $data = [
            'firm_id'       => $request->firm_id,
            'role_id'       => $request->role_id,
            'name'          => $request->name,
            'email'         => $request->email,
            'mobile_number' => $request->mobile_number,
            'role'          => strtolower($role->name),
            'status'        => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $authUser = Auth::user();
        $isAdmin = $authUser && $authUser->isAdmin();
        $authFirmId = $authUser ? $authUser->firm_id : session('firm_id');

        if (!$isAdmin && $user->firm_id != $authFirmId) {
            abort(403);
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
