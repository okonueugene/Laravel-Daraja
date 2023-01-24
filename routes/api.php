<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

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
Route::post('/deliverance/validation', [MpesaController::class, 'mpesaValidation']);
Route::post('/deliverance/register/urls', [MpesaController::class, 'mpesaRegisterUrls']);
Route::post('/deliverance/confirmation', [MpesaController::class, 'mpesaConfirmation']);
Route::post('/deliverance/access/token', [MpesaController::class, 'generateAccessToken']);


