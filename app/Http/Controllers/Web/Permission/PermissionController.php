<?php

namespace App\Http\Controllers\Web\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PermissionController extends Controller
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
        $permissions = Permission::with('roles')->orderBy('name')->get();

        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Permission::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission): View
    {
        return view('permissions.edit', compact('permission'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        if ($permission->name === 'super_admin') {
            return redirect()->back()->with('error', 'This permission cannot be deleted.');
        }
        if ($permission->roles()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a permission that is assigned to roles.');
        }

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
