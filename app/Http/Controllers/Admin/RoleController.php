<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users', 'permissions')->orderBy('sort_order')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:roles,slug|regex:/^[a-z0-9\-]+$/',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_system' => false,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('admin.roles.show', compact('role', 'permissions'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $role->load('permissions');
        $assignedIds = $role->permissions->pluck('id')->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'assignedIds'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|regex:/^[a-z0-9\-]+$/|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string|max:255',
            'sort_order' => 'integer',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Prevent changing slug of system roles
        if ($role->is_system && $role->getOriginal('slug') !== $data['slug']) {
            return back()->with('error', 'Cannot change slug of system roles.');
        }

        $role->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        if (!$role->is_system) {
            $role->permissions()->sync($data['permissions'] ?? []);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }
        if ($role->users()->exists()) {
            return back()->with('error', 'Cannot delete role with assigned users. Reassign users first.');
        }
        $role->permissions()->detach();
        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully.');
    }

    public function permissions()
    {
        $permissions = Permission::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $roles = Role::orderBy('sort_order')->get();
        return view('admin.roles.permissions', compact('permissions', 'roles'));
    }
}
