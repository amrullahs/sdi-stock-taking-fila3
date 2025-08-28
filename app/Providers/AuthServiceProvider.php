<?php

namespace App\Providers;

use App\Models\LineSto;
use App\Models\LineStoDetail;
use App\Policies\LineStoPolicy;
use App\Policies\LineStoDetailPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        LineSto::class => LineStoPolicy::class,
        LineStoDetail::class => LineStoDetailPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}