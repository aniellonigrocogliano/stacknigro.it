<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteLevelController;
use App\Http\Controllers\QuoteOptionController;
use App\Http\Controllers\QuoteRuleController;

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
| AUTH (FUORI DA middleware auth)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN (PROTETTO)
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

    // TinyMCE upload
    Route::post('/tinymce/upload', [SiteSettingsController::class, 'tinymceUpload'])
        ->name('admin.tinymce.upload');

    // SKILLS CRUD
    Route::get('/skills', [SkillController::class, 'index'])->name('admin.skills.index');
    Route::get('/skills/create', [SkillController::class, 'create'])->name('admin.skills.create');
    Route::post('/skills', [SkillController::class, 'store'])->name('admin.skills.store');
    Route::get('/skills/{skill}/edit', [SkillController::class, 'edit'])->name('admin.skills.edit');
    Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('admin.skills.update');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('admin.skills.destroy');

    // CONTACTS
    Route::view('/contacts', 'admin.contacts')->name('admin.contacts');

    // PROJECTS CRUD
    Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

    // IMMAGINI PROGETTI
    Route::delete('/project-images/{image}', [ProjectController::class, 'deleteImage'])
        ->name('admin.project-images.destroy');
    Route::post('/projects/{project}/images/sort', [ProjectController::class, 'sortImages'])
  ->name('admin.project-images.sort');
    Route::post('/project-images/{image}/cover', [ProjectController::class, 'setCover'])
        ->name('admin.project-images.cover');

    // PREVENTIVATORE - CONFIG
Route::get('/quote-levels', [QuoteLevelController::class, 'index'])->name('admin.quote-levels.index');
Route::get('/quote-levels/create', [QuoteLevelController::class, 'create'])->name('admin.quote-levels.create');
Route::post('/quote-levels', [QuoteLevelController::class, 'store'])->name('admin.quote-levels.store');
Route::get('/quote-levels/{quoteLevel}/edit', [QuoteLevelController::class, 'edit'])->name('admin.quote-levels.edit');
Route::put('/quote-levels/{quoteLevel}', [QuoteLevelController::class, 'update'])->name('admin.quote-levels.update');
Route::delete('/quote-levels/{quoteLevel}', [QuoteLevelController::class, 'destroy'])->name('admin.quote-levels.destroy');

// sync opzioni livello (pivot)
Route::put('/quote-levels/{quoteLevel}/options', [QuoteLevelController::class, 'syncOptions'])
  ->name('admin.quote-levels.options.sync');

// OPZIONI
Route::get('/quote-options', [QuoteOptionController::class, 'index'])->name('admin.quote-options.index');
Route::get('/quote-options/create', [QuoteOptionController::class, 'create'])->name('admin.quote-options.create');
Route::post('/quote-options', [QuoteOptionController::class, 'store'])->name('admin.quote-options.store');
Route::get('/quote-options/{quoteOption}/edit', [QuoteOptionController::class, 'edit'])->name('admin.quote-options.edit');
Route::put('/quote-options/{quoteOption}', [QuoteOptionController::class, 'update'])->name('admin.quote-options.update');
Route::delete('/quote-options/{quoteOption}', [QuoteOptionController::class, 'destroy'])->name('admin.quote-options.destroy');

// REGOLE
Route::get('/quote-rules', [QuoteRuleController::class, 'index'])->name('admin.quote-rules.index');
Route::get('/quote-rules/create', [QuoteRuleController::class, 'create'])->name('admin.quote-rules.create');
Route::post('/quote-rules', [QuoteRuleController::class, 'store'])->name('admin.quote-rules.store');
Route::get('/quote-rules/{quoteRule}/edit', [QuoteRuleController::class, 'edit'])->name('admin.quote-rules.edit');
Route::put('/quote-rules/{quoteRule}', [QuoteRuleController::class, 'update'])->name('admin.quote-rules.update');
Route::delete('/quote-rules/{quoteRule}', [QuoteRuleController::class, 'destroy'])->name('admin.quote-rules.destroy');
});
