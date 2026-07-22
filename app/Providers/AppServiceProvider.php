<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination (template uses Bootstrap)
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register auth event listeners for audit logging
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            [\App\Listeners\LogAuthEvents::class, 'handleLogin']
        );
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Logout::class,
            [\App\Listeners\LogAuthEvents::class, 'handleLogout']
        );
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Registered::class,
            [\App\Listeners\LogAuthEvents::class, 'handleRegistered']
        );
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\PasswordReset::class,
            [\App\Listeners\LogAuthEvents::class, 'handlePasswordReset']
        );
    }
}
