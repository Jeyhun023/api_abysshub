<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\Auth\PasswordResetRequest;
use App\Notifications\Auth\PasswordResetSuccess;
use App\Traits\ApiResponser;
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
        $user = User::where('email', $request->email)->firstOrFail();
        try {
            $passwordReset = PasswordReset::updateOrCreate(
                [
                    'email' => $user->email,
                ],
                [
                    'email' => $user->email,
                    'token' => Str::random(128)
                ]
            );

            $user->notify((new PasswordResetRequest($passwordReset->token))->onQueue("high"));

            return $this->sendResponse(null, 'We have e-mailed your password reset link!', 201);
        } catch (\Exception $e) {
            return $this->sendError('Unknown Error', ["unknown" => ['Unknown error happened'] ], 500);
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
        if (!$passwordReset || Carbon::parse($passwordReset->updated_at)->addMinutes(180)->isPast()) {
            $passwordReset?->delete();
            return $this->sendError('The given data was invalid', ["token" => ['This password reset token is invalid'] ], 404);
        }
        return $this->sendResponse($passwordReset, 'Token is correct', 201);
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
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|exists:users,email',
            'password' => ['required', 'string', 'min:7', 'regex:/^[a-zA-Z0-9]*([a-zA-Z][0-9]|[0-9][a-zA-Z])[a-zA-Z0-9]*$/'],
            'token' => 'required|string',
        ]);
        $user = User::where('email', $request->email)->firstOrFail();
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $user->email],
        ])->first();

        if (!$passwordReset) {
            return $this->sendError('The given data was invalid', ["token" => ['This password reset token is invalid'] ], 404);
        }

        try {
            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            $user->notify((new PasswordResetSuccess($passwordReset))->onQueue("medium"));
            return $this->sendResponse($user, '');
        } catch (\Exception $e) {
            return $this->sendError('Unknown Error', ["unknown" => ['Unknown error happened'] ], 500);
        }

    }
}