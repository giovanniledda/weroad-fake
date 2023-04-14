<?php

use App\Enums\Role;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\TravelController;
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

// A public (no auth) endpoint to get a list of paginated travels.
Route::resource('travels', TravelController::class)->only([
    'index',
]);

// A public (no auth) endpoint to get a list of paginated tours by the travel `slug`.
Route::get('/travels/{travel:slug}/tours', [TravelController::class, 'getTours'])->name('travels.getTours');

Route::middleware(['auth:sanctum'])->group(function () {
    // API route for logout user
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // A private (editor) endpoint to update a travel
    Route::middleware('role:'.Role::Editor->value)->group(function () {
        Route::resource('travels', TravelController::class)->only([
            'update',
        ]);
    });

    // A private (admin) endpoint to create new travels;
    Route::middleware('role:'.Role::Admin->value)->group(function () {
        Route::resource('travels', TravelController::class)->only([
            'store',
        ]);

        Route::post('/travels/{travel}/tour', [TravelController::class, 'createTour'])->name('travels.createTour');
    });
});
