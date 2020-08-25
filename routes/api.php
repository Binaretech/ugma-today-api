<?php

use Illuminate\Http\Request;
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

Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');

Route::post('passwordReset', 'AuthController@password_reset_email');
Route::post('resetPassword', 'AuthController@reset_password');

Route::middleware('auth:api')->group(function () {
    Route::put('user', 'UserController@update');
    Route::delete('user', 'UserController@destroy');
    Route::apiResource('user', 'UserController')->only('show');
});

Route::apiResource('cost', 'CostController')->only(['index', 'show']);


Route::prefix('admin')->middleware(['auth:api', 'scope:admin'])->group(function () {

    Route::apiResource('user', 'UserController')->except(['store', 'update', 'delete']);

    Route::post('ban/user/{user}', 'UserController@ban');

    Route::post('active/user/{user}', 'UserController@active');

    //------------------------------------------//
    //-----------------COSTS--------------------//
    //------------------------------------------//
    Route::get('cost', 'CostController@index_admin');
    Route::get('cost/{cost}', 'CostController@show_admin');
    Route::post('cost', 'CostController@store');
    Route::put('cost/{cost}', 'CostController@update');
    Route::delete('cost/{cost}', 'CostController@destroy');
});
