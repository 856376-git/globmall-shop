<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionService
{
    /**
     * All permissions grouped by module, used for seeding and UI display.
     */
    public static function definitions(): array
    {
        return [
            'dashboard' => [
                ['name' => 'View Dashboard', 'slug' => 'dashboard.view'],
            ],
            'products' => [
                ['name' => 'View Products', 'slug' => 'product.view'],
                ['name' => 'Create Product', 'slug' => 'product.create'],
                ['name' => 'Edit Product', 'slug' => 'product.edit'],
                ['name' => 'Delete Product', 'slug' => 'product.delete'],
            ],
            'categories' => [
                ['name' => 'View Categories', 'slug' => 'category.view'],
                ['name' => 'Create Category', 'slug' => 'category.create'],
                ['name' => 'Edit Category', 'slug' => 'category.edit'],
                ['name' => 'Delete Category', 'slug' => 'category.delete'],
            ],
            'brands' => [
                ['name' => 'View Brands', 'slug' => 'brand.view'],
                ['name' => 'Create Brand', 'slug' => 'brand.create'],
                ['name' => 'Edit Brand', 'slug' => 'brand.edit'],
                ['name' => 'Delete Brand', 'slug' => 'brand.delete'],
            ],
            'orders' => [
                ['name' => 'View Orders', 'slug' => 'order.view'],
                ['name' => 'Update Order Status', 'slug' => 'order.update'],
                ['name' => 'Delete Order', 'slug' => 'order.delete'],
                ['name' => 'Export Orders', 'slug' => 'order.export'],
            ],
            'coupons' => [
                ['name' => 'View Coupons', 'slug' => 'coupon.view'],
                ['name' => 'Create Coupon', 'slug' => 'coupon.create'],
                ['name' => 'Edit Coupon', 'slug' => 'coupon.edit'],
                ['name' => 'Delete Coupon', 'slug' => 'coupon.delete'],
            ],
            'users' => [
                ['name' => 'View Users', 'slug' => 'user.view'],
                ['name' => 'Edit User', 'slug' => 'user.edit'],
                ['name' => 'Delete User', 'slug' => 'user.delete'],
            ],
            'members' => [
                ['name' => 'View Members', 'slug' => 'member.view'],
                ['name' => 'Manage Points', 'slug' => 'member.points'],
            ],
            'reviews' => [
                ['name' => 'View Reviews', 'slug' => 'review.view'],
                ['name' => 'Approve/Reject Review', 'slug' => 'review.moderate'],
                ['name' => 'Delete Review', 'slug' => 'review.delete'],
            ],
            'refunds' => [
                ['name' => 'View Refunds', 'slug' => 'refund.view'],
                ['name' => 'Process Refund', 'slug' => 'refund.process'],
            ],
            'system' => [
                ['name' => 'Manage System Config', 'slug' => 'system.manage'],
                ['name' => 'Manage Roles & Permissions', 'slug' => 'role.manage'],
            ],
        ];
    }

    public static function defaultRoles(): array
    {
        return [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full access to all features',
                'is_system' => true,
                'sort_order' => 1,
                'permissions' => '*',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Manage products, orders, users, etc.',
                'is_system' => true,
                'sort_order' => 2,
                'permissions' => [
                    'dashboard.view',
                    'product.view', 'product.create', 'product.edit', 'product.delete',
                    'category.view', 'category.create', 'category.edit', 'category.delete',
                    'brand.view', 'brand.create', 'brand.edit', 'brand.delete',
                    'order.view', 'order.update', 'order.export',
                    'coupon.view', 'coupon.create', 'coupon.edit', 'coupon.delete',
                    'user.view', 'user.edit',
                    'member.view', 'member.points',
                    'review.view', 'review.moderate', 'review.delete',
                    'refund.view', 'refund.process',
                    'system.manage',
                ],
            ],
            [
                'name' => 'Operations',
                'slug' => 'operations',
                'description' => 'Manage products and orders',
                'is_system' => true,
                'sort_order' => 3,
                'permissions' => [
                    'dashboard.view',
                    'product.view', 'product.create', 'product.edit',
                    'category.view', 'category.create', 'category.edit',
                    'brand.view', 'brand.create', 'brand.edit',
                    'order.view', 'order.update',
                    'coupon.view', 'coupon.create', 'coupon.edit',
                    'review.view', 'review.moderate',
                ],
            ],
            [
                'name' => 'Customer Service',
                'slug' => 'customer-service',
                'description' => 'Handle orders, refunds and reviews',
                'is_system' => true,
                'sort_order' => 4,
                'permissions' => [
                    'dashboard.view',
                    'product.view',
                    'order.view', 'order.update',
                    'user.view',
                    'review.view',
                    'refund.view', 'refund.process',
                ],
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Read-only access',
                'is_system' => true,
                'sort_order' => 5,
                'permissions' => [
                    'dashboard.view',
                    'product.view',
                    'category.view',
                    'brand.view',
                    'order.view',
                    'coupon.view',
                    'user.view',
                    'member.view',
                    'review.view',
                    'refund.view',
                ],
            ],
        ];
    }

    public static function syncPermissions(): void
    {
        foreach (self::definitions() as $group => $perms) {
            foreach ($perms as $p) {
                Permission::updateOrCreate(
                    ['slug' => $p['slug']],
                    ['name' => $p['name'], 'group' => $group, 'description' => $p['description'] ?? null]
                );
            }
        }
    }

    public static function syncRoles(): void
    {
        self::syncPermissions();
        $allPerms = Permission::pluck('id')->toArray();

        foreach (self::defaultRoles() as $roleData) {
            $perms = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);

            if ($perms === '*') {
                $role->permissions()->sync($allPerms);
            } else {
                $ids = Permission::whereIn('slug', $perms)->pluck('id')->toArray();
                $role->permissions()->sync($ids);
            }
        }
    }
}
