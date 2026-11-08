<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // register app services
    public function register(): void
    {
        //
    }

    // runs when the app starts
    public function boot(): void
    {
        // only admins pass this check
        Gate::define('admin', fn (User $user) => (bool) $user->is_admin);

        // style pagination links with Bootstrap
        Paginator::useBootstrapFive();
    }
}