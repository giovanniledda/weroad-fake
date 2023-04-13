<?php

use App\Enums\Role;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\TravelController;
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

// API route for login user
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // TODO: refactor
//    Route::controller(TravelController::class)->group(function () {
//
//    });

    Route::middleware('role:'.Role::Editor->value)->group(function () {
        Route::resource('travels', TravelController::class)->only([
            'update'
        ]);
    });

    Route::middleware('role:'.Role::Admin->value)->group(function () {
        Route::resource('travels', TravelController::class)->only([
            'create', 'store', 'destroy'
        ]);
    });
});
