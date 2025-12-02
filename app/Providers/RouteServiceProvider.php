<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        parent::boot();
        $this->map();
    }

    public function map()
    {
        $this->mapApiRoutes();
        $this->mapApiAdminRoutes();
        $this->mapApiBookingRoutes();
        $this->mapApiApartmentsRoutes();
    }

    protected function mapApiAdminRoutes()
    {
        Route::prefix('api') 
        ->middleware('api')
            ->group(base_path('routes/api_admin.php'));
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api') 
        ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    protected function mapApiBookingRoutes()
    {
        Route::prefix('api') 
        ->middleware('api')
            ->group(base_path('routes/api_booking.php'));
    }

    
    protected function mapApiApartmentsRoutes()
    {
        Route::prefix('api') 
        ->middleware('api')
            ->group(base_path('routes/api_apartments.php'));
    }
}