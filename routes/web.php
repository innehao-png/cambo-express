<?php

use App\Http\Controllers\Api\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/payment/result',  [CheckoutController::class, 'result']);
Route::get('/payment/cancel',  [CheckoutController::class, 'cancel']);
Route::get('/payment/success', [CheckoutController::class, 'success']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
