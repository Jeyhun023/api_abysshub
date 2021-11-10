<?php
//Auth
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\VerificationController;
//Forum
use App\Http\Controllers\Api\Forum\ThreadController;
use App\Http\Controllers\Api\Forum\AnswerController;
use App\Http\Controllers\Api\Forum\ForumSearchController;
//Chat
use App\Http\Controllers\Api\Chat\ChatController;
//Store
use App\Http\Controllers\Api\Store\ProductController;

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

Route::get('/content-check', [CheckController::class, 'contentCheck']); 

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
});

//Forum
Route::group(['prefix' => 'forum'], function () {
    //Thread
    Route::get('/search/{query}', [ForumSearchController::class, 'index']); 
    Route::get('/user/{query}', [ForumSearchController::class, 'user']);
    Route::get('/', [ThreadController::class, 'index']); 
    Route::get('/{thread}/{slug}', [ThreadController::class, 'show']); 
    Route::get('/{thread}/thread/getcomment', [ThreadController::class, 'getComment']);
    //Answer
    Route::get('/{answer}/answer/getcomment', [AnswerController::class, 'getComment']); 
    Route::get('/{thread}/answer/loadanswers', [AnswerController::class, 'loadAnswers']);
    
    Route::group(['middleware' => ['auth:api','verified']], function ($router) {
        //Thread
        Route::post('/create', [ThreadController::class, 'store']); 
        Route::post('/{product}/product/discuss', [ThreadController::class, 'productDiscuss']);
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

//Product
Route::group(['prefix' => 'store'], function () {
    Route::get('/{product}/{slug}', [ProductController::class, 'show']); 

    Route::group(['middleware' => ['auth:api','verified']], function ($router) {
        Route::post('/{product}/product/review', [ProductController::class, 'review']); 
        Route::post('/{product}/product/fullreview', [ProductController::class, 'fullReview']); 
        
        Route::post('/create', [ProductController::class, 'store']);
        Route::put('/{product}/product/edit', [ProductController::class, 'update']); 
        Route::post('/{product}/product/plagiarism', [ProductController::class, 'plagiarismCheck']); 
        Route::post('/{product}/product/submit', [ProductController::class, 'submit']); 

        Route::post('/{product}/iterate', [ProductController::class, 'iterate']);
        Route::delete('/{product}/product/delete', [ProductController::class, 'delete']); 
    });
});
