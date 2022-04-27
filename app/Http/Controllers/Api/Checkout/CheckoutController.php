<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    use ApiResponser;

    public function __construct()
    {
        $this->user = auth('api')->user();
    }

    public function index()
    {
        $plans = Plan::all();
        return $this->successResponse($plans, null);
    }

    public function session()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe'));

        $YOUR_DOMAIN = 'http://localhost:3002';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                'price' => 'price_1Kne6cJIjCailGvpK1w0HTin',
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $YOUR_DOMAIN . '?success=true',
            'cancel_url' => $YOUR_DOMAIN . '?canceled=true',
            'automatic_tax' => [
                'enabled' => false,
            ],
        ]);

        return $this->successResponse($checkout_session, null);
    }
}
