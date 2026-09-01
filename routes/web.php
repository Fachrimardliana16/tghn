<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [BillingController::class, 'index']);

// Rate limit: maksimal 15 request per menit per IP
Route::post('/check-billing', [BillingController::class, 'check'])
    ->middleware('throttle:15,1');

// Admin Login
Route::get('/admin/login', App\Livewire\Admin\Login::class)->name('admin.login');

// Admin Logout
Route::post('/admin/logout', function() {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('admin.login');
})->name('admin.logout');

// Admin Routes - Protected
Route::middleware(['admin'])->group(function () {
    Route::get('/admin', function() {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/admin/dashboard', App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');
});
