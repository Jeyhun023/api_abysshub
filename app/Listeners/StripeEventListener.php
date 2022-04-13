<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Cashier\Events\WebhookReceived;
use Illuminate\Contracts\Queue\ShouldQueue;

class StripeEventListener
{
    public function __construct()
    {
        //
    }

    public function handle(WebhookReceived $event)
    {
        $user = User::find(1);
        $user->fullname = "YEssssYess";
        $user->save();
        if ($event->payload['type'] === 'invoice.payment_succeeded') {
            $user = User::find(1);
            $user->fullname = "YEssssYess";
            $user->save();
        }
    }
}
