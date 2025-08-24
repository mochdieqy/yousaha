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
        // Use Bootstrap pagination
        \Illuminate\Pagination\Paginator::useBootstrap();
        
        // Share CurrencyHelper globally
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatRupiah($expression); ?>";
        });
        
        \Illuminate\Support\Facades\Blade::directive('currencyOnly', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::formatRupiahOnly($expression); ?>";
        });
    }
}
