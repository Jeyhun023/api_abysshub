<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Str;
use App\Traits\ApiResponser;
// use App\Models\SocialAccount;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Resources\Auth\UserResource;

class GoogleController extends Controller
{
    use ApiResponser;
    public function loginUrl()
    {
        return $this->successResponse([
            'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl(),
        ], null);
    }

    public function loginCallback()
    {
        // try {
            $googleUser = Socialite::driver('google')->stateless()->user();
           
            $user = User::where('email', $googleUser->getEmail())
                ->where('socialite_type', '!=', '1')
                ->exists();
            if ($user) {
                return $this->errorResponse(["failed" => [trans('auth.email.exist')]]);
            }

            $user = User::updateOrCreate([
                'socialite_id' => $googleUser->getId(),
                'socialite_type' => '1'
            ], [
                'fullname' => $googleUser->getName(),
                'username' => strtolower(str_replace(' ', '', $googleUser->getName())),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(40).'@'.$googleUser->getId()),
                'socialite_token' => $googleUser->token,
                'socialite_refresh_token' => $googleUser->refreshToken
            ]);

            $shop = new Shop();
            $shop->user_id = $user->id;
            $shop->name = $user->fullname."'s shop";
            $shop->slug = Str::slug($shop->name);
            $shop->save();

            $tokenResult = $user->createToken($this->pac);

            $success['user'] = new UserResource($user);
            $success['access_token'] = $tokenResult->accessToken;
            $success['token_type'] = 'Bearer';
            $success['expires_at'] = Carbon::parse($tokenResult->token?->expires_at)->toDateTimeString();
            
            return $this->successResponse($success, trans('messages.register_success'));
        // } catch (\Throwable $errors) {
        //     return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        // }
    }
}