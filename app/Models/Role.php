<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    //helper functions

    public function hasPermission($permis)
    {
        $bool = false;
        foreach ($this->permissions as $permission) {
            if ($permission->slug == $permis) {
                $bool = true;
            }
        }

        return $bool;
    }

    public function hasPermissions($permissions)
    {
        foreach ($permissions as $perm) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }
    }

    //scope functions
    public function scopeIsType($query, $type)
    {
        $query->where('type', $type);
    }

    public function scopeNotDeveloper($query)
    {
        $query->where('name', '!=', 'Developer');
    }

    public function scopeNotAdmin($query)
    {
        $query->whereNotIn('name', ['technician', 'admin']);
    }

    public function scopeNotMyself($query)
    {
        $query->where('id', '!=', auth()->user()->role_id);
    }

    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }
    }
}
