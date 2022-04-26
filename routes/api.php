<?php
//Auth
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\SocialiteController;
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
use App\Http\Controllers\Api\Store\IterationController;
use App\Http\Controllers\Api\Store\StoreSearchController;
//Profile
use App\Http\Controllers\Api\Profile\InventoryController;
use App\Http\Controllers\Api\Profile\AccountController;
use App\Http\Controllers\Api\Profile\LibraryController;
//Other
use App\Http\Controllers\Api\Other\SkillController;
use App\Http\Controllers\Api\Other\TagController;
//Checkout
use App\Http\Controllers\Api\Checkout\CheckoutController;

use App\Http\Controllers\Api\CheckController;
use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
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

//TODOLIST Delete 2 lines above
Route::get('threads/search', [ThreadController::class, 'search']); 
Route::get('products/search', [ProductController::class, 'search']); 

//Auth
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 

Route::get('/auth/{social}/url', [SocialiteController::class, 'loginUrl']); 
Route::get('/auth/{social}/callback', [SocialiteController::class, 'loginCallback']); 

Route::get('/payment/plans', [CheckoutController::class, 'index']);

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
    Route::get('/search', [ForumSearchController::class, 'index']); 
    Route::get('/user/{query}', [ForumSearchController::class, 'user']);
    Route::get('/product/{query}', [ForumSearchController::class, 'product']);
    Route::get('/', [ThreadController::class, 'index']); 
    Route::get('/{thread}', [ThreadController::class, 'show']); 
    Route::get('/{thread}/thread/getcomment', [ThreadController::class, 'getComment']);
    //Answer
    Route::get('/{answer}/answer/getcomment', [AnswerController::class, 'getComment']); 
    Route::get('/{thread}/answer/loadanswers', [AnswerController::class, 'loadAnswers']);
    Route::get('/{thread}/answer/loadproducts', [AnswerController::class, 'loadProducts']);
    Route::get('/{thread}/{product}/answer/getanswers', [AnswerController::class, 'getAnswers']);
    
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

//Product
Route::group(['prefix' => 'store'], function () {
    Route::get('/search', [StoreSearchController::class, 'index']); 
    //TODOLIST add middleware if product not submitted only owner should see
    Route::get('/{product}', [ProductController::class, 'show']); 

    Route::group(['middleware' => ['auth:api','verified']], function ($router) {
        Route::post('/{product}/product/review', [ProductController::class, 'review']); 
        Route::post('/{product}/product/fullreview', [ProductController::class, 'fullReview']); 
        
        Route::post('/create', [ProductController::class, 'store']);
        Route::put('/{product}/product/edit', [ProductController::class, 'update']);
        Route::post('/{product}/product/images', [ProductController::class, 'imageUpload']);
        //TODOLIST Make it working iteration
        Route::post('/{product}/iterate', [IterationController::class, 'store']);
        Route::delete('/{product}/product/delete', [ProductController::class, 'delete']);
    });
});

//Profile
Route::group(['prefix' => 'profile', 'middleware' => ['auth:api','verified']], function () {
    //Account
    Route::post('/account/update', [AccountController::class, 'update']); 
    //Inventory
    Route::get('/inventory', [InventoryController::class, 'index']); 
    Route::post('/inventory/{product}/create', [InventoryController::class, 'store']); 
    Route::delete('/inventory/{product}/delete', [InventoryController::class, 'delete']);
    Route::get('/inventory/history', [InventoryController::class, 'history']); 
    //Library
    Route::get('/library/history', [LibraryController::class, 'history']); 
});


//Other
Route::group(['prefix' => 'other'], function () {
    Route::get('/tags/{tag}', [TagController::class, 'search']); 
    Route::get('/skills/{skill}', [SkillController::class, 'search']); 
});

//TODOLIST DELETE THESE STAFF
Route::post('/user/subscribe', function (Request $request) {
    $user = auth('api')->user();
    return $user->newSubscription(
        'default', 'price_1Kne6cJIjCailGvpK1w0HTin'
    )->create($user->pm_type);
//    return ->addPaymentMethod('pm_1Ko1AZJIjCailGvpSttx7nEU');
});
