<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Permission::get()->map(function ($permission) {
            Gate::define($permission->name, function ($user) use ($permission) {
                return $user->role->permissions->contains($permission);
            });
        });
    }
}
