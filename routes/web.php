<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteAdminController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\DashboardController;

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
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN (PROTETTO)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->group(function () {

    // ✅ prima era Route::view('/', 'admin.dashboard') -> non passava $inboxUnread
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // SITE SETTINGS
    Route::get('/hero', [SiteSettingsController::class, 'edit'])->name('admin.hero.index');
    Route::put('/hero', [SiteSettingsController::class, 'update'])->name('admin.hero.update');

    //BIO
     Route::get('/bio', [SiteSettingsController::class, 'editBio'])->name('admin.bio.edit');
       Route::post('/bio', [SiteSettingsController::class, 'updateBio'])->name('admin.bio.update');
    Route::post('/tinymce/upload',
        [SiteSettingsController::class, 'tinymceUpload']
    )->name('admin.tinymce.upload');

    // SKILLS
Route::get('/skills', [SkillController::class, 'index'])->name('admin.skills.index');

Route::get('/skills/create', [SkillController::class, 'create'])->name('admin.skills.create');
Route::get('/skills/{skill}/edit', [SkillController::class, 'edit'])->name('admin.skills.edit');

Route::post('/skills', [SkillController::class, 'store'])->name('admin.skills.store');
Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('admin.skills.update');
Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('admin.skills.destroy');

    // PROJECTS
    Route::get('/projects', [ProjectController::class, 'index'])->name('admin.projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('admin.projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('admin.projects.store');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('admin.projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('admin.projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('admin.projects.destroy');

    /*
    |--------------------------------------------------------------------------
    | PREVENTIVI (pagina unica)
    |--------------------------------------------------------------------------
    */
    Route::get('/quotes', [QuoteAdminController::class, 'index'])->name('quotes.index');

    // LIVELLI (inline)
    Route::put('/quotes/levels/{level}', [QuoteAdminController::class, 'updateLevel'])->name('quotes.levels.update');

    // OPZIONI
    Route::post('/quotes/options', [QuoteAdminController::class, 'storeOption'])->name('quotes.options.store');
    Route::post('/quotes/options/attach', [QuoteAdminController::class, 'attachOption'])->name('quotes.options.attach');
    Route::put('/quotes/options/{option}', [QuoteAdminController::class, 'updateOption'])->name('quotes.options.update');
    Route::delete('/quotes/options/{option}', [QuoteAdminController::class, 'destroyOption'])->name('quotes.options.destroy');

    // PIVOT livello <-> opzione
    Route::put('/quotes/levels/{level}/options/{option}', [QuoteAdminController::class, 'updatePivot'])->name('quotes.levels.options.update');
    Route::delete('/quotes/levels/{level}/options/{option}', [QuoteAdminController::class, 'detachOption'])->name('quotes.levels.options.detach');

    // REGOLE
    Route::post('/quotes/rules', [QuoteAdminController::class, 'storeRule'])->name('quotes.rules.store');
    Route::delete('/quotes/rules/{quoteRule}', [QuoteAdminController::class, 'destroyRule'])->name('quotes.rules.destroy');

    /*
    |--------------------------------------------------------------------------
    | INBOX
    |--------------------------------------------------------------------------
    */
    Route::get('/inbox', [InboxController::class, 'index'])->name('admin.inbox.index');
    Route::get('/inbox/archive', [InboxController::class, 'archive'])->name('admin.inbox.archive');
    Route::get('/inbox/trash', [InboxController::class, 'trash'])->name('admin.inbox.trash');

    Route::get('/inbox/{conversation}', [InboxController::class, 'show'])->name('admin.inbox.show');

    Route::patch('/inbox/{conversation}/read', [InboxController::class, 'markRead'])->name('admin.inbox.read');
    Route::patch('/inbox/{conversation}/unread', [InboxController::class, 'markUnread'])->name('admin.inbox.unread');

    Route::patch('/inbox/{conversation}/archive', [InboxController::class, 'doArchive'])->name('admin.inbox.doArchive');
    Route::patch('/inbox/{conversation}/unarchive', [InboxController::class, 'unarchive'])->name('admin.inbox.unarchive');

    Route::delete('/inbox/{conversation}', [InboxController::class, 'destroy'])->name('admin.inbox.destroy');

    Route::post('/inbox/{conversation}/reply', [InboxController::class, 'reply'])->name('admin.inbox.reply');
});
