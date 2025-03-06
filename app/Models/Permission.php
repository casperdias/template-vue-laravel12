<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = ['name', 'display_name', 'description'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($permission) {
            $permission->roles()->attach(1);
        });
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
    }
}
