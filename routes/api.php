<?php
//Auth
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\VerificationController;
//Forum
use App\Http\Controllers\Api\Forum\ThreadController;
use App\Http\Controllers\Api\Forum\AnswerController;
//Chat
use App\Http\Controllers\Api\Chat\ChatController;

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
    //Thread
    Route::get('/', [ThreadController::class, 'index']); 
    Route::get('/{thread}/{slug}', [ThreadController::class, 'show']); 
    Route::get('/{thread}/thread/getcomment', [ThreadController::class, 'getComment']);
    //Answer
    Route::get('/{answer}/answer/getcomment', [AnswerController::class, 'getComment']); 
    Route::get('/{thread}/answer/loadanswers', [AnswerController::class, 'loadAnswers']);
    
    Route::group(['middleware' => ['auth:api','verified']], function ($router) {
        //Thread
        Route::post('/create', [ThreadController::class, 'store']); 
        Route::put('/{thread}/thread/edit', [ThreadController::class, 'update']); 
        Route::delete('/{thread}/thread/delete', [ThreadController::class, 'delete']); 
        Route::post('/{thread}/thread/vote', [ThreadController::class, 'vote']); 
        Route::post('/{thread}/thread/unvote', [ThreadController::class, 'unvote']);
        Route::post('/{thread}/thread/comment', [ThreadController::class, 'comment']);  
        Route::put('/{comment}/thread/comment/edit', [ThreadController::class, 'commentUpdate']); 
        Route::delete('/{comment}/thread/comment/delete', [ThreadController::class, 'commentDelete']);
        //Answer
        Route::post('/{thread}/answer/submit', [AnswerController::class, 'store']); 
        Route::put('/{answer}/answer/edit', [AnswerController::class, 'update']); 
        Route::delete('/{answer}/answer/delete', [AnswerController::class, 'delete']); 
        Route::post('/{answer}/answer/vote', [AnswerController::class, 'vote']); 
        Route::post('/{answer}/answer/unvote', [AnswerController::class, 'unvote']); 
        Route::post('/{answer}/answer/comment', [AnswerController::class, 'comment']); 
        Route::put('/{comment}/answer/comment/edit', [AnswerController::class, 'commentUpdate']); 
        Route::delete('/{comment}/answer/comment/delete', [AnswerController::class, 'commentDelete']);
    });
});

//Chat
Route::group(['prefix' => 'chat', 'middleware' => ['auth:api','verified']], function ($router) {
    Route::get('/', [ChatController::class, 'index']); 
    Route::get('/{user}/check', [ChatController::class, 'check']); 
    Route::post('/{chat}/send', [ChatController::class, 'sendMessage']); 
    Route::get('/{chat}/{limit}/load', [ChatController::class, 'loadMessage']); 
});


