<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    CostController,
    UserController,
    PostController,
};

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('admin/login', [AuthController::class, 'login']);

Route::post('passwordReset', [AuthController::class, 'password_reset_email']);
Route::post('resetPassword', [AuthController::class, 'reset_password']);

Route::middleware('auth:api')->group(function () {
    Route::put('user', [UserController::class, 'update']);
    Route::delete('user', [UserController::class, 'destroy']);
    Route::apiResource('user', UserController::class)->only('show');
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::apiResource('cost', CostController::class)->only(['index', 'show']);

Route::get('post', [PostController::class, 'index_post']);
Route::get('news', [PostController::class, 'index_news']);


Route::prefix('admin')->middleware('scope:admin')->group(function () {

    Route::apiResource('user', UserController::class)->except(['store', 'update', 'delete', 'show']);

    Route::post('ban/user/{user}', [UserController::class, 'ban']);

    Route::post('active/user/{user}', [UserController::class, 'active']);

    //------------------------------------------//
    //-----------------COSTS--------------------//
    //------------------------------------------//
    Route::get('cost', [CostController::class, 'index']);
    Route::get('cost/{cost}', [CostController::class, 'show_admin']);
    Route::post('cost', [CostController::class, 'store']);
    Route::put('cost/{cost}', [CostController::class, 'update']);
    Route::delete('cost/{cost}', [CostController::class, 'destroy']);

    //------------------------------------------//
    //-----------------POSTS--------------------//
    //------------------------------------------//
    route::get('post', [PostController::class, 'index_admin']);
});

Route::apiResource('cost', CostController::class)->only(['index', 'show']);
