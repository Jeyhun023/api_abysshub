<?php

namespace App\Http\Controllers\Api\Checkout;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    use ApiResponser;

    public function index()
    {
        $plans = Plan::all();
        return $this->successResponse($plans, null);
    }
}
