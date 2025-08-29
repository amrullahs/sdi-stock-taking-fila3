<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Spatie\Activitylog\Facades\LogActivity;

class LogoutListener
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
    public function handle(Logout $event): void
    {
        LogActivity::useLog('logout')
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'logout_time' => now()->toDateTimeString(),
            ])
            ->log('User logged out');
    }
}
