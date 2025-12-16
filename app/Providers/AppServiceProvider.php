<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// MODELS
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Service;

// OBSERVER
use App\Observers\ActivityObserver;

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
        User::observe(ActivityObserver::class);
        Client::observe(ActivityObserver::class);
        Invoice::observe(ActivityObserver::class);
        Service::observe(ActivityObserver::class);
    }
}
