<?php

namespace App\Listeners;

use App\Events\NewUserRegisteredEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVerificationMailListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(NewUserRegisteredEvent $event)
    {
        $event->user->sendEmailVerificationNotification();
    }
}
