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

class SocialiteController extends Controller
{
    use ApiResponser;
    private $pac = "Abyss Personal Access Client";

    public function loginUrl($social)
    {
        if(!in_array($social, User::SOCIAL_TYPES)){
            abort(404);
        }
        return $this->successResponse([
            'url' => Socialite::driver($social)->stateless()->redirect()->getTargetUrl(),
        ], null);
    }

    public function loginCallback($social)
    {
        if(!in_array($social, User::SOCIAL_TYPES)){
            abort(404);
        }
        
        // try {
            $socialUser = Socialite::driver($social)->stateless()->user();
        // } catch (\Throwable $errors) {
        //     return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        // }
           
        $user = User::where('email', $socialUser->getEmail())
            ->where('socialite_type', '!=', array_search($social, User::SOCIAL_TYPES))
            ->exists();
        if ($user) {
            return $this->errorResponse(["failed" => [trans('auth.email.exist')]]);
        }

        $user = User::updateOrCreate([
            'socialite_id' => $socialUser->getId(),
            'socialite_type' => array_search($social, User::SOCIAL_TYPES)
        ], [
            'fullname' => $socialUser->getName(),
            //TODOLIST Check if username exist or not
            'username' => strtolower(str_replace(' ', '', $socialUser->getName())),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(40).'@'.$socialUser->getId()),
            'socialite_token' => $socialUser->token,
            'socialite_refresh_token' => $socialUser->refreshToken,
            'email_verified_at' => Carbon::now()
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
    }
}