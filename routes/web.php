<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;

Route::get('/', [BillingController::class, 'index']);
Route::post('/check-billing', [BillingController::class, 'check']);
