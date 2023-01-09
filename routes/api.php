<?php

use App\Http\Controllers\ChannelSubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\FolderController;

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
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->makeVisible('email');
    });

    Route::post('/subscription', [ChannelSubscriptionController::class, 'subscribe']);
    Route::get('/subscription', [ChannelSubscriptionController::class, 'getOfUser']);
    Route::get('/items', [ItemsController::class, 'getItems']);
    Route::apiResource('folders', FolderController::class);
});

Route::post('register', [UserController::class, 'register']);
