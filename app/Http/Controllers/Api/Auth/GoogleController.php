<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
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
        try {
            $googleUser = Socialite::driver('google')->stateless()->fields([
                'first_name',
                'last_name',
                'email'
            ])->user();
           
            $user = User::where('email', $googleUser->getEmail())
                ->where('type_id', '!=', '1')
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

            Auth::login($user);

            return $this->successResponse(new UserResource($user), trans('messages.register_success'));
        } catch (\Throwable $errors) {
            return $this->errorResponse(["failed" => [trans('messages.failed')] ]);
        }
    }
}