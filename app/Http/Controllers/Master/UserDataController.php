<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use App\Models\User;
use App\Models\Role;

class UserDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = request()->input('page', 1);
        $per_page = request()->input('per_page', 5);
        $search = request()->input('search', '');
        $users = User::with('role:id,display_name')
                ->select('id', 'name', 'email', 'created_at', 'email_verified_at', 'role_id')
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('id', 'asc')
                ->paginate($per_page, ['*'], 'page', $page);
        $roles = Role::all()->pluck('display_name', 'id');
        return Inertia::render('master/UserData', [
            'users' => $users,
            'roles' => $roles,
            'search' => $search
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'. User::class,
            'role_id' => 'required|exists:roles,id',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        event(new Registered($user));

        return to_route('users.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class.',email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update($request->only('name', 'email', 'role_id'));

        return to_route('users.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === 1) {
            return back()->withErrors(['id' => 'Super Admin cannot be deleted']);
        }

        $user->delete();

        return to_route('users.index', [
            'page' => request('page', 1),
            'search' => request('search', '')
        ]);
    }
}
