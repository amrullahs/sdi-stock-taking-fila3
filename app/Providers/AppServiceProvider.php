<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Tables\Table;
use App\Models\LineSto;
use App\Models\LineStoDetail;
use App\Observers\LineStoObserver;
use App\Observers\LineStoDetailObserver;

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
        // Configure Filament Table to add record-row class for sticky header functionality
        Table::configureUsing(function (Table $table) {
            $table->recordClasses('record-row');
        });
        
        // Register observers
        LineSto::observe(LineStoObserver::class);
        LineStoDetail::observe(LineStoDetailObserver::class);
    }
}
