<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VolunteerController;
use App\Models\Listing;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('unauthorized', function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Unauthorized action'
    ], 403);
})->name('unauthorized');

Route::controller(AuthController::class)->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
        Route::get('user', 'getUser');
        Route::put('user/update', [UserController::class, 'update']);
    });

    Route::post('login', 'login');
    Route::post('register', 'register');
});

Route::get('volunteers', [VolunteerController::class, 'index']);

// Organizer Route Access
Route::controller(ListingController::class)->group(function () {
    Route::middleware('can:organizer')->group(function () {
        Route::get('requestListing', 'listingRequest');
        Route::post('listings', 'store');
        Route::put('listings/{listing}', 'update');
        Route::delete('listings/{listing}', 'destroy');
        Route::put('approve', 'approve');
        Route::put('decline', 'decline');
        Route::get('organizer/listings','myListings');
    });
    Route::get('listings', 'index');
    Route::get('listings/{listing}', 'show');
});
// Volunteer Route Access

Route::group([],function() {
    Route::controller(ListingController::class)->group(function () {
        Route::middleware('can:volunteer')->group(function () {
            Route::post('apply', 'apply');
        });

    });
    Route::controller(VolunteerController::class)->group(function () {
        Route::middleware('can:volunteer')->group(function () {
            Route::get('my-posits', 'myPosits');
        });
    });
});


