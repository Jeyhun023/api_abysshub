<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Str;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;

class AuthController extends Controller
{
    use ApiResponser;
    private $pac = "Abyss Personal Access Client";

    public function register(RegisterRequest $request)
    {
        try {
            $user = new User([
                'email' => $request->email,
                'username' => $request->username,
                'fullname' => $request->fullname,
                'password' => bcrypt($request->password),
            ]);
            $user->save();
            
            $shop = new Shop();
            $shop->user_id = $user->id;
            $shop->name = $user->fullname."'s shop";
            $shop->slug = Str::slug($shop->fullname);
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

    public function login(LoginRequest $request)
    {
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

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return $this->successResponse(null, trans('messages.logout_success'));
    }
}
