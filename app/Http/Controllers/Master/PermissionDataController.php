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
        $search = request()->input('search', '');

        $permissions = Permission::select('id', 'name', 'display_name', 'description')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                            ->orWhere('display_name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate($per_page, ['*'], 'page', $page);

        return Inertia::render('master/PermissionData', [
            'permissions' => $permissions,
            'search' => $search,
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

        return to_route('permissions.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
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
            'name' => 'required|string|max:255|unique:' . Permission::class . ',name,' . $permission->id,
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return to_route('permissions.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return to_route('permissions.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
    }
}
