<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\Auth\PasswordResetToken;
use App\Notifications\Auth\PasswordResetSuccess;
use App\Http\Requests\Api\Auth\PasswordResetRequest;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    use ApiResponser;
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
            $passwordReset = PasswordReset::updateOrCreate([
                'email' => $request->email,
                'token' => Str::random(128)
            ]);

            $user->notify((new PasswordResetToken($passwordReset->token))->onQueue("high"));

            return $this->successResponse(null, trans('messages.sent_reset_link'));
        } catch (\Exception $e) {
            return $this->errorResponse(["failed" => trans('messages.failed')]);
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
            ->firstOrFail();
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(180)->isPast()) {
            $passwordReset->delete();
            return $this->errorResponse(["token" => trans('messages.token_wrong')]);
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
            'token' => $request->token,
            'email'=> $request->email
        ])->first();
        $user = User::where('email', $request->email)->first();

        if (!$passwordReset) {
            return $this->errorResponse(["token" => trans('messages.token_wrong')]);
        }

        try {
            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            $user->notify((new PasswordResetSuccess($passwordReset))->onQueue("medium"));

            return $this->successResponse($user, trans('messages.password_changed'));
        } catch (\Exception $e) {
            return $this->errorResponse(["failed" => trans('messages.failed')]);
        }

    }
}