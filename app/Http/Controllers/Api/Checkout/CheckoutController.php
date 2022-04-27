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

    public function session(Request $request)
    {
        $stripeCustomer = $this->user->createOrGetStripeCustomer();
        \Stripe\Stripe::setApiKey(config('services.stripe'));

        $domain = 'http://localhost:3000';

        $checkout_session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price' => $request->priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $domain . 'subscription/status/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $domain . 'subscription/status/{CHECKOUT_SESSION_ID}',
            'automatic_tax' => [
                'enabled' => false,
            ],
            'customer' => $stripeCustomer->id
        ]);

        return $this->successResponse($checkout_session, null);
    }
    
    public function status(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe'));
        
        try {
            $session = \Stripe\Checkout\Session::retrieve($request->get('sessionId'));
        } catch(\Stripe\Exception\InvalidRequestException $es) {
            return $this->errorResponse(["failed" => [trans('messages.session_wrong')] ]);
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }

        return $this->successResponse($session, null);
    }
}
