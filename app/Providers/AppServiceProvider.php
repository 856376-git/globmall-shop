<?php

namespace App\Providers;

use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register every permission slug as a Gate ability.
        // Usage in views: @can('product.edit') ... @endcan
        // Usage in code:  Gate::allows('product.edit')
        foreach (PermissionService::definitions() as $group => $perms) {
            foreach ($perms as $p) {
                Gate::define($p["slug"], function (User $user) use ($p) {
                    // Ensure roles relationship is loaded before hasPermission() reads it
                    $user->loadMissing('roles');
                    return $user->hasPermission($p["slug"]);
                });
            }
        }
    }
}
