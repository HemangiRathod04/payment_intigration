<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetCSPHeaders;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [PaymentController::class, 'showForm'])->name('payment.form');
Route::post('/', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('/payment/success', function () {
    return view('payment-success');
})->name('payment.success');

Route::get('/payment/failure', function () {
    return view('payment-failure');
})->name('payment.failure');


