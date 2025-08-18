<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ShieldAutoGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shield:auto-generate {--force : Force regeneration of all permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate Shield permissions for new Filament resources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🛡️ Starting Shield auto-generation...');
        
        $resourcePath = app_path('Filament/Resources');
        
        if (!File::exists($resourcePath)) {
            $this->warn('No Filament resources directory found.');
            return 1;
        }

        // Get all resource files
        $resourceFiles = File::allFiles($resourcePath);
        $cacheFile = storage_path('framework/cache/shield_resources_cache.json');
        
        // Ensure cache directory exists
        $cacheDir = dirname($cacheFile);
        if (!File::exists($cacheDir)) {
            File::makeDirectory($cacheDir, 0755, true);
        }
        
        // Get previously cached resources
        $cachedResources = [];
        if (File::exists($cacheFile) && !$this->option('force')) {
            $cachedResources = json_decode(File::get($cacheFile), true) ?? [];
        }

        $currentResources = [];
        $validResources = [];
        
        foreach ($resourceFiles as $file) {
            if ($file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                $currentResources[] = $filePath;
                
                // Validate if resource has corresponding model
                if ($this->validateResourceModel($filePath)) {
                    $validResources[] = $filePath;
                }
            }
        }

        // Check if there are new resources or force flag is used
        $newResources = array_diff($currentResources, $cachedResources);
        
        if (!empty($newResources) || $this->option('force')) {
            if (empty($validResources)) {
                $this->warn('⚠️ No valid resources found (resources must have corresponding models).');
                return 0;
            }
            
            try {
                $this->info('📝 Generating permissions for resources...');
                
                // Use process to capture output and handle interactive prompts
                $process = new \Symfony\Component\Process\Process([
                    'php', 'artisan', 'shield:generate', '--all'
                ], base_path());
                
                $process->setInput("0\n"); // Auto-select first panel (admin)
                $process->setTimeout(120); // 2 minutes timeout
                $process->run();
                
                if ($process->isSuccessful()) {
                    $this->info('✅ Shield permissions generated successfully!');
                    
                    if (!empty($newResources)) {
                        $this->info('📋 New resources detected:');
                        foreach ($newResources as $resource) {
                            $this->line('  - ' . basename($resource));
                        }
                    }
                    
                    // Update cache
                    File::put($cacheFile, json_encode($currentResources));
                    
                    $this->info('🔄 Cache updated.');
                } else {
                    $this->error('❌ Failed to generate Shield permissions.');
                    $this->error('Error output: ' . $process->getErrorOutput());
                    return 1;
                }
                
            } catch (\Exception $e) {
                $this->error('❌ Error: ' . $e->getMessage());
                return 1;
            }
        } else {
            $this->info('✅ No new resources found. Permissions are up to date.');
        }
        
        return 0;
    }
    
    /**
     * Validate if resource has corresponding model
     */
    private function validateResourceModel(string $filePath): bool
    {
        try {
            $content = File::get($filePath);
            
            // Extract class name from file
            if (preg_match('/class\s+(\w+)\s+extends/', $content, $matches)) {
                $className = $matches[1];
                $namespace = 'App\\Filament\\Resources';
                $fullClassName = $namespace . '\\' . $className;
                
                // Check if class exists and has getModel method
                if (class_exists($fullClassName)) {
                    $reflection = new \ReflectionClass($fullClassName);
                    if ($reflection->hasMethod('getModel')) {
                        $modelClass = $fullClassName::getModel();
                        return class_exists($modelClass);
                    }
                }
            }
            
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
