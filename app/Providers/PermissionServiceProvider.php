<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\PermissionHelper;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Make permission helpers available globally
        $this->app->singleton('permissions', function () {
            return new PermissionHelper();
        });

        // Register Blade directives
        $this->registerBladeDirectives();
    }

    /**
     * Register custom Blade directives for permissions
     */
    private function registerBladeDirectives(): void
    {
        // @can directive
        Blade::directive('can', function ($expression) {
            return "<?php if(app('permissions')->userCan($expression)): ?>";
        });

        Blade::directive('endcan', function () {
            return "<?php endif; ?>";
        });

        // @canany directive
        Blade::directive('canany', function ($expression) {
            return "<?php if(app('permissions')->userCanAny($expression)): ?>";
        });

        Blade::directive('endcanany', function () {
            return "<?php endif; ?>";
        });

        // @canall directive
        Blade::directive('canall', function ($expression) {
            return "<?php if(app('permissions')->userCanAll($expression)): ?>";
        });

        Blade::directive('endcanall', function () {
            return "<?php endif; ?>";
        });

        // @role directive
        Blade::directive('role', function ($expression) {
            return "<?php if(app('permissions')->userHasRole($expression)): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        // @anyrole directive
        Blade::directive('anyrole', function ($expression) {
            return "<?php if(app('permissions')->userHasAnyRole($expression)): ?>";
        });

        Blade::directive('endanyrole', function () {
            return "<?php endif; ?>";
        });
    }
}
