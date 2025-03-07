<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;

class RoleDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = request()->input('page', 1);
        $per_page = request()->input('per_page', 5);
        $roles = Role::select('id', 'name', 'display_name', 'description')
            ->orderBy('id', 'asc')
            ->paginate($per_page, ['*'], 'page', $page);
        return Inertia::render('master/RoleData', [
            'roles' => $roles,
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
            'name' => 'required|string|max:255|unique:' . Role::class,
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return to_route('roles.index', ['page' => request('page', 1)]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:' . Role::class,
            'display_name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
        ]);

        return to_route('roles.index', ['page' => request('page', 1)]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return to_route('roles.index', ['page' => request('page', 1)]);
    }
}
