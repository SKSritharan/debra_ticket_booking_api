<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/partners/register', [\App\Http\Controllers\API\V1\AuthController::class, 'registerPartner']);
Route::post('/login', [\App\Http\Controllers\API\V1\AuthController::class, 'login']);
Route::post('/register', [\App\Http\Controllers\API\V1\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    Route::controller(\App\Http\Controllers\API\V1\EventController::class)->prefix('events')->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::controller(\App\Http\Controllers\API\V1\TicketPurchaseController::class)->prefix('event')->group(function () {
        Route::post('/ticket/{id}', 'purchase');
        Route::get('/tickets', 'getPurchasedTickets');
    });

    Route::middleware('role:admin')->group(function () {
        Route::controller(\App\Http\Controllers\API\V1\EventController::class)->prefix('events')->group(function () {
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        Route::controller(\App\Http\Controllers\API\V1\TicketPurchaseController::class)->prefix('event')->group(function () {
            Route::get('/earning/event/{id}', 'earningsByEvent');
            Route::get('/earning/partner/{id}', 'earningsByPartner');
            Route::get('/earning', 'totalEarnings');
        });
    });

    Route::middleware('role:partner')->group(function () {
        Route::controller(\App\Http\Controllers\API\V1\EventController::class)->prefix('events')->group(function () {
            Route::post('/', 'store');
        });

        Route::controller(\App\Http\Controllers\API\V1\TicketPurchaseController::class)->prefix('event')->group(function () {
            Route::get('/partner/earning', 'getOwnEarnings');
        });
    });
});
