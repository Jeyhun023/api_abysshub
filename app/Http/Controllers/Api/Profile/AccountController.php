<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\User;
use App\Http\Requests\Api\Profile\AccountUpdateRequest;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    use ApiResponser;
    public $user;
 
    public function __construct()
    {
        $this->user = auth('api')->user();
    }
    
    public function update(AccountUpdateRequest $request)
    {
        $user= User::find($this->user->id);

        $user->name = $request->name;
        $user->fullname = $request->fullname;
        $user->description = $request->description;
        $user->image = $request->image;
        $user->skills = collect( $request->skills );
        $user->save();

        return new UserResource($user);
    }
}
