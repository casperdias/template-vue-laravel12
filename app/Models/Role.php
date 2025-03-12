<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'display_name', 'description'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasPermissionTo($permissionName)
    {
        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function givePermission(Permission $permission)
    {
        if (! $this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->attach($permission);
        }
    }

    public function revokePermission(Permission $permission)
    {
        if ($this->permissions()->where('permission_id', $permission->id)->exists()) {
            $this->permissions()->detach($permission);
        }
    }
}
