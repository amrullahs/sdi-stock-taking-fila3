<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ShieldAutoGenerateProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the shield auto-generate command
        $this->commands([
            \App\Console\Commands\ShieldAutoGenerate::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register event listener for when new resources are created
        if ($this->app->runningInConsole()) {
            // Listen for artisan commands that create resources
            Event::listen('artisan.command.finished', function ($event, $data) {
                if (isset($data[0]) && str_contains($data[0], 'make:filament-resource')) {
                    $this->generatePermissions();
                }
            });
        }
    }
    
    private function generatePermissions(): void
    {
        try {
            Log::info('Shield: Auto-generating permissions for new resource');
            
            // Run our custom shield auto-generate command
            Artisan::call('shield:auto-generate');
            
            Log::info('Shield: Permissions auto-generated successfully');
            
        } catch (\Exception $e) {
            Log::error('Shield Auto Generate Error: ' . $e->getMessage());
        }
    }
}