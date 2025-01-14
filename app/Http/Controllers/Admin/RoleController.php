<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $roles = Role::query()
            ->with(['permissions', 'users'])
            ->notDeveloper()
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->count(),
                'permissionIds' => $role->permissions->pluck('id'),
                'users' => $role->users
            ]);

        $permissions = Permission::get()->groupBy('type');

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'permis' => 'nullable',
            'permis.*' => 'numeric|exists:permissions,id'
        ]);

        $role = Role::create([
            'slug' => Str::slug($request->name),
            'name' => $request->name,
            'type' => 'Staff',
        ]);

        $role->permissions()->sync($request->permis);

        return redirect()->back()->with('success', 'Successfully Created.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string',
            'permis' => 'nullable',
            'permis.*' => 'numeric|exists:permissions,id'
        ]);

        $role->update([
            'slug' => Str::slug($request->name),
            'name' => $request->name,
            'type' => 'Staff',
        ]);

        $role->permissions()->sync($request->permis);

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot Delete. Role is linked with users']);
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
