<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Events\NewUserRegisteredEvent;

class VerificationController extends Controller
{
    use ApiResponser;

    public function verify($id, Request $request)
    {
        if(!$request->hasValidSignature()){
            return $this->sendError(null, 'Email verification token is invalid.', 404);
        }

        $user = User::findOrFail($id);
        
        if($user->hasVerifiedEmail()){
            return $this->sendError(null, 'User has already verified.', 404);
        }

        $user->markEmailasVerified();
        return $this->sendResponse(null, 'User successfully verified!', 201);

    }

    public function resend()
    {
        $user = auth('api')->user();

        if($user->hasVerifiedEmail()){
            return $this->sendError(null, 'User has already verified.', 404);
        }
        
        event(new NewUserRegisteredEvent($user));
        
        return $this->sendResponse(null, 'Verification link has sent successfully', 201);
    }
}
