<?php
//Auth
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\VerificationController;
//Forum
use App\Http\Controllers\Api\Forum\ForumController;

use App\Http\Controllers\Api\CheckController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Auth

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 

Route::group(['prefix' => 'password'], function () {
    Route::post('/create', [PasswordResetController::class, 'create'])->middleware('throttle:2,1');
    Route::get('/check/{token}', [PasswordResetController::class, 'find']);
    Route::post('/reset', [PasswordResetController::class, 'reset']);
});

Route::group(['middleware' => 'auth:api'], function ($router) {

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'email'], function () {
        Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('/resend', [VerificationController::class, 'resend'])->middleware('throttle:2,1');
    }); 

    Route::group(['middleware' => 'verified'], function ($router) {
        Route::get('/content-check', [CheckController::class, 'contentCheck']); 
    });
});

//Forum
Route::group(['prefix' => 'forum'], function () {
    Route::get('/', [ForumController::class, 'index']); 
});
