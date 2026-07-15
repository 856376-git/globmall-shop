<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Role;
use App\Notifications\RoleChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $users = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['orders', 'addresses', 'reviews']);
        $auditLogs = \App\Models\RolePermissionAuditLog::where('target_user_id', $user->id)
            ->with('actor')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        return view('admin.users.show', compact('user', 'auditLogs'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,banned',
        ]);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);
        return redirect()->route('admin.users.show', $user)->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete user with active orders.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    public function editRoles(User $user): View
    {
        $roles = Role::orderBy('sort_order')->get();
        $assignedIds = $user->roles->pluck('id')->toArray();
        return view('admin.users.roles', compact('user', 'roles', 'assignedIds'));
    }

    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $superAdmin = Role::where('slug', 'super-admin')->first();
        if ($superAdmin) {
            $stillHasSuper = collect($data['roles'] ?? [])->contains($superAdmin->id);
            $hadSuper = $user->roles()->where('roles.id', $superAdmin->id)->exists();
            if ($hadSuper && !$stillHasSuper) {
                $otherSuper = User::whereHas('roles', fn($q) => $q->where('roles.id', $superAdmin->id))
                    ->where('users.id', '!=', $user->id)->exists();
                if (!$otherSuper) {
                    return back()->with('error', 'Cannot remove the last super-admin user.');
                }
            }
        }

        // Capture old roles for diff
        $oldRoles = $user->roles()->get()->keyBy('id');
        $newRoleIds = collect($data['roles'] ?? []);

        $actor = auth()->user();
        $actorName = $actor ? $actor->name : 'System';

        $added = $newRoleIds->diff($oldRoles->keys())->values();
        $removed = $oldRoles->keys()->diff($newRoleIds)->values();

        // Sync via model (writes audit log)
        $user->syncRoles($data['roles'] ?? [], $actor?->id);

        // Notify user about each change
        $allChanged = $added->merge($removed)->unique();
        $rolesAll = Role::whereIn('id', $allChanged)->get()->keyBy('id');

        foreach ($added as $rid) {
            $role = $rolesAll->get($rid);
            if ($role) {
                $user->notify(new RoleChangedNotification('assigned', $role->name, $actorName));
            }
        }

        foreach ($removed as $rid) {
            $role = $rolesAll->get($rid);
            if ($role) {
                $user->notify(new RoleChangedNotification('removed', $role->name, $actorName));
            }
        }

        return redirect()->route('admin.users.show', $user)->with('success', 'User roles updated.');
    }
}

