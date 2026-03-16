<?php

namespace App\Http\Controllers\Web\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()?->hasPermission('super_admin')) {
                return redirect()->route('dashboard')->with('error', 'Unauthorized. Super Admin access required.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $roles = Role::with('permissions')->withCount('users')->orderBy('name')->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::orderBy('name')->get();

        return view('roles.create', compact('permissions'));
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $permissionIds = Permission::whereIn('uuid', $request->permissions)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update(['name' => $request->name]);
        $permissionIds = Permission::whereIn('uuid', $request->permissions ?? [])->pluck('id');
        $role->permissions()->sync($permissionIds);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'Super Admin role cannot be deleted.');
        }
        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a role that has members assigned.');
        }

        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
