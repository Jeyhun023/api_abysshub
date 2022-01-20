<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

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
        try {
            $user = new User([
                'email' => $request->email,
                'name' => $request->name,
                'password' => bcrypt($request->password),
            ]);
            $user->save();
            
            $shop = new Shop([
                'user_id' => $user->id,
                'name' => $user->name."'s shop",
                'slug' => Str::slug($user->name."'s shop")
            ]);
            $shop->save();

            $tokenResult = $user->createToken($this->pac);

            $success['user'] = new UserResource($user);
            $success['access_token'] = $tokenResult->accessToken;
            $success['token_type'] = 'Bearer';
            $success['expires_at'] = Carbon::parse($tokenResult->token?->expires_at)->toDateTimeString();
            
            return $this->successResponse($success, trans('messages.register_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
        
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
    public function login()
    {
        $user = User::find(1);
        $user->name = $request->input('SuppressionReason');
        
        $user->save();

        return response($request, 200);

        // LoginRequest $request
        try {
            $credentials = request()->only(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return $this->errorResponse(["password" => [trans('auth.password')] ]);
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
            
            return $this->successResponse($success, trans('messages.login_success'));
        } catch (Exception $e) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }

    public function getUser(Request $request)
    {
        $user = User::find($request->user()->id);

        return $this->successResponse(new UserResource($user));
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->successResponse(null, trans('messages.logout_success'));
    }

}
