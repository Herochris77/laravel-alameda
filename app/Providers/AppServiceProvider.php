<?php

namespace App\Providers;

use App\Http\Middleware\RolMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('dompdf', function ($app) {
            return new \Barryvdh\DomPDF\Facade\Pdf;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::aliasMiddleware('rol', RolMiddleware::class);
    }
}
