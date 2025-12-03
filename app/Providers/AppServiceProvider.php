<?php

namespace App\Providers;

use App\Models\Setting;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;
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
        // مشاركة إعدادات المتجر مع جميع الـ views
        view()->composer('*', function ($view) {
            $view->with([
                'store_logo' => Setting::get('store_logo'),
                'store_favicon' => Setting::get('store_favicon'),
                'store_name' => Setting::get('store_name', config('app.name')),
                'currency' => Setting::get('currency', 'ILS'),
                'currency_symbol' => Setting::get('currency_symbol', '₪'),
            ]);
        });
    }
}
