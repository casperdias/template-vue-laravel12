<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\Permission;

use Inertia\Inertia;
use Inertia\Response;

class RolePermissionController extends Controller
{
    public function index(Request $request, Role $role)
    {
        $search = $request->input('search');

        $permissions = Permission::where('name', 'like', "%{$search}%")
            ->orWhere('display_name', 'like', "%{$search}%")
            ->paginate(5);

        $permissions->getCollection()->transform(function ($permission) use ($role) {
            $permission->status = $role->hasPermissionTo($permission->name);
            return $permission;
        });

        return Inertia::render('master/RolePermission', [
            'role' => $role,
            'permissions' => $permissions,
            'search' => $search,
        ]);
    }

    public function store(Request $request, Role $role, Permission $permission)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        if ($request->status) {
            $role->givePermission($permission);
        } else {
            $role->revokePermission($permission);
        }

        return to_route('role.setting', [
            'role' => $role->id,
            'page' => $request->input('page', 1),
            'search' => $request->input('search', ''),
        ]);
    }
}
