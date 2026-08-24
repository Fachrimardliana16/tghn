<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;

Route::get('/', [BillingController::class, 'index']);

// Rate limit: maksimal 15 request per menit per IP
Route::post('/check-billing', [BillingController::class, 'check'])
    ->middleware('throttle:15,1');
