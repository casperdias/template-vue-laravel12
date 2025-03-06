<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;

class PermissionDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = request()->input('page', 1);
        $per_page = request()->input('per_page', 5);
        $permissions = Permission::select('id', 'name', 'display_name', 'description')
            ->orderBy('id', 'asc')
            ->paginate($per_page, ['*'], 'page', $page);
        return Inertia::render('master/PermissionData', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:' . Permission::class,
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return to_route('permissions.index', ['page' => request('page', 1)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:' . Permission::class,
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return to_route('permissions.index', ['page' => request('page', 1)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return to_route('permissions.index', ['page' => request('page', 1)]);
    }
}
