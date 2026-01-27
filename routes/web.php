<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuoteAdminController;
use App\Http\Controllers\InboxAdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LegalAdminController;
use App\Http\Controllers\PublicLegalPageController;
use App\Http\Controllers\PublicSkillController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicContactController;
use App\Http\Controllers\PublicProjectController;
use App\Http\Controllers\PublicQuoteController;
/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/chi-sono', fn() => view('public.bio'))->name('public.bio');
Route::get('/progetti', [PublicProjectController::class, 'index'])->name('public.projects.index');
Route::get('/progetti/{project}', [PublicProjectController::class, 'show'])->name('public.projects.show');

Route::get('/preventivo', [PublicQuoteController::class, 'index'])->name('public.quotes');
Route::post('/preventivo', [PublicQuoteController::class, 'store'])->name('public.quotes.store');


Route::get('/contatti', [PublicContactController::class, 'create'])->name('public.contacts');
Route::post('/contatti', [PublicContactController::class, 'store'])->name('public.contacts.store');
Route::get('/privacy-policy', [PublicLegalPageController::class, 'privacyPolicy'])
    ->name('privacy.policy');
Route::get('/skills', [PublicSkillController::class, 'index'])->name('public.skills');

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
Route::delete('/projects/{project}/images/{image}', [ProjectController::class, 'destroyImage'])
  ->name('admin.project-images.destroy');

Route::post('/projects/{project}/images/{image}/cover', [ProjectController::class, 'setCover'])
  ->name('admin.project-images.cover');

Route::post('/projects/{project}/images/sort', [ProjectController::class, 'sortImages'])
  ->name('admin.project-images.sort');



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
Route::get('/inbox', [InboxAdminController::class, 'index'])->name('inbox.index');
    Route::get('/inbox/archive', [InboxAdminController::class, 'archive'])->name('inbox.archive');
    Route::get('/inbox/trash', [InboxAdminController::class, 'trash'])->name('inbox.trash');

    // show anche se nel cestino
    Route::get('/inbox/{conversation}', [InboxAdminController::class, 'show'])
        ->withTrashed()
        ->name('inbox.show');

    Route::patch('/inbox/{conversation}/read', [InboxAdminController::class, 'markRead'])->name('inbox.read');
    Route::patch('/inbox/{conversation}/unread', [InboxAdminController::class, 'markUnread'])->name('inbox.unread');

    Route::patch('/inbox/{conversation}/archive', [InboxAdminController::class, 'archiveOne'])->name('inbox.archiveOne');
    Route::patch('/inbox/{conversation}/unarchive', [InboxAdminController::class, 'unarchiveOne'])->name('inbox.unarchiveOne');

    Route::delete('/inbox/{conversation}', [InboxAdminController::class, 'moveToTrash'])->name('inbox.trashOne');

    Route::patch('/inbox/trash/{conversationId}/restore', [InboxAdminController::class, 'restore'])->name('inbox.restore');
    Route::delete('/inbox/trash/{conversationId}/force', [InboxAdminController::class, 'forceDelete'])->name('inbox.forceDelete');

    // 🔥 svuota cestino
    Route::delete('/inbox/trash/empty', [InboxAdminController::class, 'emptyTrash'])->name('inbox.trash.empty');

    Route::post('/inbox/{conversation}/reply', [InboxAdminController::class, 'reply'])->name('inbox.reply');

    /*
|--------------------------------------------------------------------------
| Contatti
|--------------------------------------------------------------------------
*/
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::put('/contacts/{contact}', [ContactController::class, 'update'])->name('contacts.update');
Route::delete('/contacts/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');

    /*
|--------------------------------------------------------------------------
| Pagina policy
|--------------------------------------------------------------------------
*/

Route::get('/legal', [LegalAdminController::class, 'edit'])->name('legal.edit');
Route::put('/legal', [LegalAdminController::class, 'update'])->name('legal.update');
});
