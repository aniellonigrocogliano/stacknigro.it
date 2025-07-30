<?php

use Illuminate\Support\Facades\Route;
use App\Models\Visit;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HeroController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\BioController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FrontendController;


// Frontend - Registra le visite
Route::get('/', [FrontendController::class, 'index'])->name('homepage');
Route::view('/privacy','frontend.privacy')->name('privacy');
Route::post('/contact/send', [FrontendController::class, 'sendMessage'])->name('contact.send');
// Login Admin
Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/admin/login', [LoginController::class, 'login']);
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('logout');

// Area Admin Protetta
Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index']);

    // ✅ Rotte Hero
    Route::get('/admin/hero', [HeroController::class, 'index'])->name('admin.hero');
    Route::post('/admin/hero', [HeroController::class, 'update']);

    // ✅ Rotte Skills
    Route::get('/admin/skills', [SkillController::class, 'index'])->name('admin.skills.index');
    Route::post('/admin/skills', [SkillController::class, 'store'])->name('admin.skills.store');
    Route::delete('/admin/skills/{skill}', [SkillController::class, 'destroy'])->name('admin.skills.destroy');


    // ✅ Rotte Bio
    Route::get('/admin/bio', [BioController::class, 'index'])->name('admin.bio');
    Route::post('/admin/bio', [BioController::class, 'update']);

    // ✅ Rotte Gallery
    Route::get('/admin/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::post('/admin/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::delete('/admin/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

    // ✅ Rotte Messaggi
    Route::get('/admin/messages', [MessageController::class, 'index'])->name('admin.messages.index');
    Route::delete('/admin/messages/{id}', [MessageController::class, 'destroy'])->name('admin.messages.destroy');

    // ✅ Rotte Messaggi
    Route::get('/admin/contacts', [ContactController::class, 'index'])->name('admin.contacts');
    Route::post('/admin/contacts', [ContactController::class, 'update']);


});
