<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('public.home'))->name('public.home');
Route::get('/chi-sono', fn() => view('public.bio'))->name('public.bio');
Route::get('/skills', fn() => view('public.skills'))->name('public.skills');
Route::get('/progetti', fn() => view('public.projects'))->name('public.projects');
Route::get('/preventivo', fn() => view('public.quote'))->name('public.quote');
Route::get('/contatti', fn() => view('public.contacts'))->name('public.contacts');
Route::get('/privacy-policy', fn() => view('public.privacy'))->name('public.privacy');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN (tutto qui dentro /admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->group(function () {

    Route::view('/', 'admin.dashboard')->name('admin.dashboard');

    // HERO + LOGO
    Route::get('/hero', [SiteSettingsController::class, 'edit'])->name('admin.hero.edit');
    Route::post('/hero', [SiteSettingsController::class, 'update'])->name('admin.hero.update');

    // BIO
    Route::get('/bio', [SiteSettingsController::class, 'editBio'])->name('admin.bio.edit');
    Route::post('/bio', [SiteSettingsController::class, 'updateBio'])->name('admin.bio.update');

    // TinyMCE upload (UNA SOLA ROTTA)
    Route::post('/tinymce/upload', [SiteSettingsController::class, 'tinymceUpload'])
        ->name('admin.tinymce.upload');

    // SKILLS CRUD (queste sono quelle giuste)
    Route::get('/skills', [SkillController::class, 'index'])->name('admin.skills.index');
    Route::get('/skills/create', [SkillController::class, 'create'])->name('admin.skills.create');
    Route::post('/skills', [SkillController::class, 'store'])->name('admin.skills.store');
    Route::get('/skills/{skill}/edit', [SkillController::class, 'edit'])->name('admin.skills.edit');
    Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('admin.skills.update');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('admin.skills.destroy');

    // (placeholder pagine admin)
    Route::view('/contacts', 'admin.contacts')->name('admin.contacts');
    Route::view('/projects', 'admin.projects')->name('admin.projects');
});
