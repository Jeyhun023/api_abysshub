<?php

namespace App\Listeners;

use App\Events\NewUserRegisteredEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerificationMailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 2;

  

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'high';

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
