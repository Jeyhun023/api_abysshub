<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{

    use ApiResponser;

    /**
     * Create user
     *
     * @param  [string] phone
     * @param  [string] password
     * @return [string] message
     */
    private $pac = "Abyss Personal Access Client";

    public function register(RegisterRequest $request)
    {
        $user = new User([
            'email' => $request->email,
            'name' => $request->name,
            'password' => bcrypt($request->password),
        ]);
        $user->save();
        $tokenResult = $user->createToken($this->pac);
        $success['access_token'] = $tokenResult->accessToken;
        $success['token_type'] = 'Bearer';
        $success['expires_at'] = Carbon::parse($tokenResult->token?->expires_at)->toDateTimeString();
        return $this->sendResponse($success, 'User successfully created!', 201);
    }

    /**
     * Login user and create token
     *
     * @param  [string] phone
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return $this->sendError('Unauthorized', ['Crendentials do not match'], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken($this->pac);
        if ($request->remember_me) {
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
        }

        $success['access_token'] = $tokenResult->accessToken;
        $success['token_type'] = 'Bearer';
        $success['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
        return $this->sendResponse($success, 'User login successfully.');
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->sendResponse(null, 'Successfully logged out');

    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255|exists:users,email',
        ]);
        $user = User::where('email', $request->email)->first();
        try {
            $passwordReset = PasswordReset::updateOrCreate(
                [
                    'email' => $user->email,
                ],
                [
                    'email' => $user->email,
                    'token' => rand(100000, 999999),
                ]
            );
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );
            return $this->sendResponse(null, 'We have e-mailed your password reset link!', 201);
        } catch (\Exception $e) {
            return $this->sendError(null, $e->getMessage(), 500);
        }
    }

}
