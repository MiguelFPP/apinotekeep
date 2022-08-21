<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\api\VerificationController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

/* verification email */
Route::get('email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

/* profile */
Route::controller(ProfileController::class)->middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/profile', 'show');
    Route::post('/profile', 'updateProfile');
    Route::post('/profile/password', 'changePassword');
});
