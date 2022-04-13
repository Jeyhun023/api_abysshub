<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\NewUserRegisteredEvent;
use App\Listeners\SendVerificationMailListener;
use App\Events\NewChatMessageEvent;
use App\Listeners\SendMessageNotificationListener;
use App\Events\NewSearchEvent;
use App\Listeners\NewSearchListener;
use App\Events\ThreadElasticEvent;
use App\Listeners\ThreadElasticListener;
use App\Events\StoreElasticEvent;
use App\Listeners\StoreElasticListener;
use Laravel\Cashier\Events\WebhookReceived;
use App\Listeners\StripeEventListener;

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
        ],
        NewSearchEvent::class => [
            NewSearchListener::class
        ],
        ThreadElasticEvent::class => [
            ThreadElasticListener::class
        ],
        StoreElasticEvent::class => [
            StoreElasticListener::class
        ],
        WebhookReceived::class => [
            StripeEventListener::class,
        ],
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
