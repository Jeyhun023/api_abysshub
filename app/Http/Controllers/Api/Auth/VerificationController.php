<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Events\NewUserRegisteredEvent;
use App\Notifications\Auth\WelcomeMail;

class VerificationController extends Controller
{
    use ApiResponser;

    public function verify($id, Request $request)
    {
        if(!$request->hasValidSignature()){
            return $this->errorResponse(["token" => [trans('messages.token_wrong')] ]);
        }

        $user = User::findOrFail($id);
        
        if($user->hasVerifiedEmail()){
            return $this->errorResponse(["user" => [trans('messages.user_verified')] ]);
        }

        $user->markEmailasVerified();
        $user->notify((new WelcomeMail($user))->onQueue("medium"));

        return $this->successResponse(null, trans('messages.verified_success'));
    }

    public function resend()
    {
        $user = auth('api')->user();

        if($user->hasVerifiedEmail()){
            return $this->errorResponse(["user" => [trans('messages.user_verified')] ]);
        }

        event(new NewUserRegisteredEvent($user));
        
        return $this->successResponse(null, trans('messages.link_sent'));
    }
}
