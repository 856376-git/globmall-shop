<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'phone', 'role', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function recentlyViewed()
    {
        return $this->hasMany(RecentlyViewed::class);
    }

    public function points()
    {
        return $this->hasMany(UserPoints::class);
    }

    /**
     * Admin = has at least one backend role (super-admin/admin/operations/customer-service/viewer).
     * Backed by RBAC only; users.role field is no longer authoritative for admin identity.
     */
    public function isAdmin(): bool
    {
        $adminRoles = ['super-admin', 'admin', 'operations', 'customer-service', 'viewer'];
        return $this->hasRole($adminRoles);
    }

    public function isSeller(): bool
    {
        return $this->hasRole('seller');
    }

    // ===== RBAC =====

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->roles()->get()->flatMap(fn($r) => $r->permissions)->unique("id");
    }

    public function hasRole(string|array $roleSlug): bool
    {
        $slugs = is_array($roleSlug) ? $roleSlug : [$roleSlug];
        return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
    }

    public function hasPermission(string $permissionSlug): bool
    {
        // Super-admin bypasses all permission checks.
        if ($this->hasRole('super-admin')) {
            return true;
        }
        // All other roles (admin/operations/customer-service/viewer) use normal RBAC.
        return $this->roles()->get()->flatMap(fn($r) => $r->permissions)->pluck("slug")->contains($permissionSlug);
    }

    public function syncRoles(array $roleIds, ?int $actorId = null): void
    {
        // Reload roles so we have a fresh collection for diff
        $oldRoles = $this->roles()->get()->keyBy('id');
        $newRoleIds = collect($roleIds)->flip();

        $addedIds = $newRoleIds->diff($oldRoles->keys())->keys();
        $removedIds = $oldRoles->keys()->diff($newRoleIds)->keys();

        $actorId = $actorId ?? (auth()->check() ? auth()->id() : null);
        $ip = request()->ip();
        $ua = request()->userAgent();

        foreach ($addedIds as $rid) {
            $role = Role::find($rid);
            RolePermissionAuditLog::log(
                $actorId, $this->id, "role_assigned", "Role",
                $rid, $role?->name ?? "Role #$rid", $ip, $ua
            );
        }

        foreach ($removedIds as $rid) {
            $role = $oldRoles->get($rid);
            RolePermissionAuditLog::log(
                $actorId, $this->id, "role_removed", "Role",
                $rid, $role?->name ?? "Role #$rid", $ip, $ua
            );
        }

        $this->roles()->sync($roleIds);
    }

    public function assignRole(int $roleId, ?int $actorId = null): void
    {
        if (!$this->roles()->where('role_id', $roleId)->exists()) {
            $actorId = $actorId ?? (auth()->check() ? auth()->id() : null);
            $role = Role::find($roleId);
            RolePermissionAuditLog::log(
                $actorId, $this->id, "role_assigned", "Role",
                $roleId, $role?->name ?? "Role #$roleId",
                request()->ip(), request()->userAgent()
            );
            $this->roles()->attach($roleId);
        }
    }

    public function removeRole(int $roleId, ?int $actorId = null): void
    {
        $role = Role::find($roleId);
        $actorId = $actorId ?? (auth()->check() ? auth()->id() : null);
        RolePermissionAuditLog::log(
            $actorId, $this->id, "role_removed", "Role",
            $roleId, $role?->name ?? "Role #$roleId",
            request()->ip(), request()->userAgent()
        );
        $this->roles()->detach($roleId);
    }
}

