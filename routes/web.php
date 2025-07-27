<?php

use App\Models\Visit;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// Frontend - Registra le visite
Route::get('/', function () {
    Visit::create([
        'ip_address' => request()->ip(),
        'user_agent' => request()->header('User-Agent'),
    ]);

    return view('frontend.index');
});

// Login Admin
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Area Admin Protetta
Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index']);
    // Qui potrai aggiungere altre rotte protette in futuro
});

