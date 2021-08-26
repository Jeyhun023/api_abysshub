<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\VerificationController;
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

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 

Route::group(['middleware' => 'auth:api'], function ($router) {

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'password'], function () {
        Route::post('/create', [PasswordResetController::class, 'create']);
        Route::get('/{token}', [PasswordResetController::class, 'find']);
        Route::post('/reset', [PasswordResetController::class, 'reset']);
    });

    Route::group(['prefix' => 'email'], function () {
        Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
        Route::post('/resend', [VerificationController::class, 'resend']);
    }); 

});

Route::get('/content-check', [CheckController::class, 'contentCheck']); 