<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Spatie\Activitylog\Facades\LogActivity;


class LoginListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        LogActivity::useLog('login')
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_time' => now()->toDateTimeString(),
            ])
            ->log('User logged in');
    }
}
