<?php

use App\Http\Controllers\CartController;
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
Route::group(['prefix' => 'cart'], function () {

    Route::post('/', [CartController::class, 'createCart']);
    Route::get('/', [CartController::class, 'getCarts']);
    Route::delete('/', [CartController::class, 'deleteCart']);

    Route::post('/add-product', [CartController::class, 'addProduct']);
    Route::delete('/remove-product', [CartController::class, 'removeProduct']);
});


