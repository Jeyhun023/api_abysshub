<?php

namespace App\Listeners;

use App\Events\UserVerifiedMailEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

class SendWelcomeMailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public $tries = 5;
    
    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'medium';

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserVerifiedMailEvent $event)
    {
        Mail::to($event->user->email)->send(new WelcomeMail($event->user));
    }
}
