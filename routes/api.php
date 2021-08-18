<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\PasswordResetController;
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

Route::post('/register', [ApiAuthController::class, 'register']); //
Route::post('/login', [ApiAuthController::class, 'login']); //

Route::group(['prefix' => 'password'], function () {
    Route::post('/create', [PasswordResetController::class, 'create']);
    Route::get('/{token}', [PasswordResetController::class, 'find']);
    Route::post('/reset', [PasswordResetController::class, 'reset']);
}); //

Route::get('/content-check', [CheckController::class, 'contentCheck']); //