<?php

namespace BaseCode\Common\Providers;

use BaseCode\Common\Commands\Install;
use Illuminate\Support\ServiceProvider;

class BaseCommonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            Install::class
        ]);

        $this->publishes([
            __DIR__.'/../Config/audit.php' => config_path('audit.php'),
        ], 'config');

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(base_path('BaseCode/Common/Helpers/*.php')) as $filename) {
            require_once $filename;
        }
    }
}
