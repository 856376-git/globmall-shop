<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Email pattern → role slug mapping.
     * Run after roles/permissions are synced.
     */
    private array $userRoleMap = [
        'admin@globmall.com' => 'super-admin',
        'ops@globmall.com'   => 'operations',
        'cs@globmall.com'    => 'customer-service',
        'viewer@globmall.com' => 'viewer',
        'demo@globmall.com'  => 'admin',        // the "admin" RBAC role (not super-admin)
    ];

    public function run(): void
    {
        // Sync all permissions and system roles
        PermissionService::syncRoles();

        // Auto-assign demo users to their roles
        foreach ($this->userRoleMap as $email => $roleSlug) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->command->warn("User $email not found — skipping role assignment.");
                continue;
            }

            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) {
                $this->command->warn("Role $roleSlug not found — skipping.");
                continue;
            }

            if (!$user->hasRole($roleSlug)) {
                $user->assignRole($role->id);
                $this->command->info("Assigned $roleSlug to $email.");
            } else {
                $this->command->line("User $email already has role $roleSlug.");
            }
        }
    }
}
