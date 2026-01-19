<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('public.home'))->name('public.home');
Route::get('/chi-sono', fn() => view('public.bio'))->name('public.bio');
Route::get('/skills', fn() => view('public.skills'))->name('public.skills');
Route::get('/progetti', fn() => view('public.projects'))->name('public.projects');
Route::get('/preventivo', fn() => view('public.quote'))->name('public.quote');
Route::get('/contatti', fn() => view('public.contacts'))->name('public.contacts');
Route::get('/privacy-policy', fn() => view('public.privacy'))->name('public.privacy');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    Route::get('/hero', [SiteSettingsController::class, 'edit']);
Route::post('/hero', [SiteSettingsController::class, 'update']);

    Route::view('/bio', 'admin.bio');
    Route::view('/skills', 'admin.skills');
    Route::view('/contacts', 'admin.contacts');
    Route::view('/projects', 'admin.projects');
});
