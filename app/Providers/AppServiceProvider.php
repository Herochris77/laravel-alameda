<?php

namespace App\Providers;

use App\Http\Middleware\RolMiddleware;
use App\Http\Middleware\TesoreriaMiddleware;
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
        Route::aliasMiddleware('tesoreria', TesoreriaMiddleware::class);
        Route::aliasMiddleware('aviso', \App\Http\Middleware\AvisoAceptadoMiddleware::class);
    }
}
