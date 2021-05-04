<?php

namespace BaseCode\Auth\Providers;

use BaseCode\Auth\Commands\Install;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class BaseAuthServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::before(function ($user, $ability) {
            return $user->isAdmin();
        });

        // Gate::define('access-user', function ($user, $userId) {
        //     return $user->getId() == $userId || $user->isAdmin();
        // });

        $this->commands([
            Install::class
        ]);

        $this->publishes([
            __DIR__.'/../../config/permission.php' => config_path('permission.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../Database/Migrations/' => database_path('migrations'),
        ], 'migrations');
    }
    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
