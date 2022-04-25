<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\User;
Route::get('/', function () {
    return view('welcome');
});
//TODOLIST delete route
Route::get('/update', function () {
    return view('update-payment-method');
});
Route::get('phpmyinfo', function () {
    phpinfo(); 
})->name('phpmyinfo');
