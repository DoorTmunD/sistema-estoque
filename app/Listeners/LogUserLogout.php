<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        activity('auth')
            ->causedBy($event->user)
            ->log("Usuário {$event->user->name} realizou logout");
    }
}