<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NewUserRegisteredEvent;
use App\Listeners\SendVerificationMailListener;
use App\Events\NewChatMessageEvent;
use App\Listeners\SendMessageNotificationListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewUserRegisteredEvent::class => [
            SendVerificationMailListener::class
        ],
        NewChatMessageEvent::class => [
            SendMessageNotificationListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
