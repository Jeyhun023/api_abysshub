<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Snipe\BanBuilder\CensorWords;
use App\Models\BadWord;

class AuthController extends Controller
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

        $censor = new CensorWords;
        $string = $censor->censorString($request->name);

        if ($string["isProfanity"]) {
            return $this->sendError('Profanity', ['Profanity detected'], 401);
        }

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
        $credentials = request()->only(['email', 'password']);
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

        $success['user'] = new UserResource($user);
        $success['access_token'] = $tokenResult->accessToken;
        $success['token_type'] = 'Bearer';
        $success['expires_at'] = Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
        return $this->sendResponse($success, 'User login successfully.');
    }

    public function getUser(Request $request)
    {
        $user = User::find($request->user()->id);
        return new UserResource($user);
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

}
