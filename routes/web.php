<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home');
Route::view('/admin-test', 'admin.dashboard');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    Route::view('/hero', 'admin.hero');
    Route::view('/bio', 'admin.bio');
    Route::view('/skills', 'admin.skills');
    Route::view('/contacts', 'admin.contacts');
    Route::view('/projects', 'admin.projects');
});

