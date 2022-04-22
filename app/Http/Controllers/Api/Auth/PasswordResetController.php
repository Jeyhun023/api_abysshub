<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\Auth\PasswordResetToken;
use App\Notifications\Auth\PasswordResetSuccess;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Http\Resources\Auth\UserResource;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    use ApiResponser;

    private $pac = "Abyss Personal Access Client";
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();
        
        try {
            //TODOLIST look if its exist in default 
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $request->email,
                'token' => Str::random(128)
            ]);

            $user->notify((new PasswordResetToken($passwordReset->token)));

            return $this->successResponse(null, trans('messages.sent_reset_link'));
        } catch (\Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();

        if(!$passwordReset){
            return $this->errorResponse(["token" => [trans('messages.token_wrong')] ]);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(180)->isPast()) {
            $passwordReset->delete();
            return $this->errorResponse(["token" => [trans('messages.token_expired')] ]);
        }
        
        return $this->successResponse($passwordReset);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(PasswordResetRequest $request)
    {
        $passwordReset = PasswordReset::where([
            'token' => $request->token
        ])->first();
        $user = User::where('email', $request->email)->first();

        if (!$passwordReset) {
            return $this->errorResponse(["token" => [trans('messages.token_wrong')] ]);
        }

        try {
            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            $user->notify((new PasswordResetSuccess($passwordReset))->onQueue("medium"));

            $tokenResult = $user->createToken($this->pac);
            $success['user'] = new UserResource($user);
            $success['access_token'] = $tokenResult->accessToken;
            $success['token_type'] = 'Bearer';
            $success['expires_at'] = Carbon::parse($tokenResult->token?->expires_at)->toDateTimeString();
 
            return $this->successResponse($success, trans('messages.password_changed'));
        } catch (\Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }

    }
}