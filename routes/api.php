<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MpesaC2BController;

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

//do not use mpesa as the prefix for your routes
Route::post('/validation', [MpesaController::class, 'mpesaValidation']);
Route::post('/register/urls', [MpesaController::class, 'mpesaRegisterUrls']);
Route::post('/confirmation', [MpesaController::class, 'mpesaConfirmation']);
Route::post('/access/token', [MpesaController::class, 'generateAccessToken']);
Route::post('/stk/push', [MpesaController::class, 'stkPush']);

//mpesa c2b routes
Route::post('/c2b/accesstoken', [MpesaC2BController::class, 'generateAccessToken']);
Route::post('/c2b/register', [MpesaC2BController::class, 'registerURLS']);
Route::post('/c2b/simulate', [MpesaC2BController::class, 'simulateTransaction']);
Route::post('/c2b/confirmation', [MpesaC2BController::class, 'confirmTransaction']);
Route::post('/c2b/validation', [MpesaC2BController::class, 'validation']);
Route::post('/c2b/callback', [MpesaC2BController::class, 'callBack']);
Route::post('/c2b/timeout', [MpesaC2BController::class, 'timeout']);
Route::post('/c2b/result', [MpesaC2BController::class, 'result']);
