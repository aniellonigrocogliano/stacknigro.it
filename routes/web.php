<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QuotePackageController;
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
use App\Http\Controllers\CaptchaSiteController;
use App\Http\Controllers\QuoteLevelController;
use App\Http\Controllers\QuoteOptionController;
use App\Http\Controllers\QuoteRuleController;
use App\Http\Controllers\AdminSuiteController;
use App\Http\Controllers\AdminLanguageController;
use App\Http\Controllers\AdminDownloadController;
use App\Http\Controllers\AdminDownloadVersionController;
use App\Http\Controllers\AdminDownloadAssetController;
use App\Http\Controllers\PublicDownloadController;
use App\Http\Controllers\QuoteAdminController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('public.home');
Route::get('/chi-sono', fn () => view('public.bio'))->name('public.bio');

Route::get('/progetti', [PublicProjectController::class, 'index'])->name('public.projects.index');
Route::get('/progetti/{project}', [PublicProjectController::class, 'show'])->name('public.projects.show');

Route::get('/preventivo', [PublicQuoteController::class, 'index'])->name('public.quotes');
Route::post('/preventivo/next', [PublicQuoteController::class, 'getNextStep'])->name('public.quotes.next');
// Il parametro {slug?} permette di catturare il nome del pacchetto nell'URL
Route::get('/preventivo/{slug?}', [PublicQuoteController::class, 'index'])->name('public.quotes.index');
Route::post('/preventivo', [PublicQuoteController::class, 'store'])->name('public.quotes.store');

Route::get('/cron/inbox-check', [\App\Http\Controllers\CronController::class, 'inboxCheck'])
    ->name('cron.inbox-check');

Route::get('/cron/captcha-rollup', [\App\Http\Controllers\CronController::class, 'captchaRollup'])
    ->name('cron.captcha-rollup');

Route::get('/contatti', [PublicContactController::class, 'create'])->name('public.contacts');
Route::post('/contatti', [PublicContactController::class, 'store'])->name('public.contacts.store');

Route::get('/privacy-policy', [PublicLegalPageController::class, 'privacyPolicy'])
    ->name('privacy.policy');

Route::get('/skills', [PublicSkillController::class, 'index'])->name('public.skills');

/*
|--------------------------------------------------------------------------
| PUBLIC DOWNLOAD CENTER
|--------------------------------------------------------------------------
*/
Route::get('/download', [PublicDownloadController::class, 'index'])->name('public.download.index');
Route::get('/download/{slug}', [PublicDownloadController::class, 'show'])->name('public.download.show');
Route::get('/download/file/{assetId}', [PublicDownloadController::class, 'file'])->name('public.download.file');

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
    Route::post('/hero', [SiteSettingsController::class, 'update'])->name('admin.hero.update');

    // BIO
    Route::get('/bio', [SiteSettingsController::class, 'editBio'])->name('admin.bio.edit');
    Route::post('/bio', [SiteSettingsController::class, 'updateBio'])->name('admin.bio.update');

    Route::post('/tinymce/upload', [SiteSettingsController::class, 'tinymceUpload'])
        ->name('admin.tinymce.upload');

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

    // ✅ REORDER PROGETTI
    Route::post('/projects/reorder', [ProjectController::class, 'reorder'])
        ->name('admin.projects.reorder');

    Route::delete('/projects/{project}/images/{image}', [ProjectController::class, 'destroyImage'])
        ->name('admin.project-images.destroy');

    Route::post('/projects/{project}/images/{image}/cover', [ProjectController::class, 'setCover'])
        ->name('admin.project-images.cover');

    Route::post('/projects/{project}/images/sort', [ProjectController::class, 'sortImages'])
        ->name('admin.project-images.sort');

/*
/*
|--------------------------------------------------------------------------
| PREVENTIVI (Gestione centralizzata a Tab)
|--------------------------------------------------------------------------
*/

// 1. Schermata Principale (quella con i 4 Tab)
// Usiamo il nome 'admin.quotes.index' per coerenza con il controller
// Definiamo la rotta con il nome nuovo, ma diamogli anche il vecchio nome come alias
Route::get('/quotes', [QuoteAdminController::class, 'index'])->name('admin.quotes.index');
Route::get('/quotes-old-alias', [QuoteAdminController::class, 'index'])->name('quotes.index'); // <-- AGGIUNGI QUESTA RIGA

// 2. LIVELLI
Route::put('/quotes/levels/{quoteLevel}', [QuoteLevelController::class, 'update'])->name('quotes.levels.update');

// 3. OPZIONI (CRUD Globale)
Route::post('/quotes/options', [QuoteOptionController::class, 'store'])->name('quotes.options.store');
Route::post('/quotes/options/attach', [QuoteOptionController::class, 'attach'])->name('quotes.options.attach');
Route::put('/quotes/options/{quoteOption}', [QuoteOptionController::class, 'update'])->name('quotes.options.update');
Route::delete('/quotes/options/{quoteOption}', [QuoteOptionController::class, 'destroy'])->name('quotes.options.destroy');

// 4. PIVOT (Logica specifica Livello <-> Opzione)
Route::put('/quotes/levels/{quoteLevel}/options/{quoteOption}', [QuoteLevelController::class, 'updatePivot'])->name('quotes.levels.options.update');
Route::delete('/quotes/levels/{quoteLevel}/options/{quoteOption}', [QuoteLevelController::class, 'detachOption'])->name('quotes.levels.options.detach');

// 5. REGOLE
Route::post('/quotes/rules', [QuoteRuleController::class, 'store'])->name('quotes.rules.store');
Route::delete('/quotes/rules/{quoteRule}', [QuoteRuleController::class, 'destroy'])->name('quotes.rules.destroy');

// 6. PACCHETTI (Sincronizzati con il nuovo Blade e Controller)
Route::get('/quotes/packages/create', [QuotePackageController::class, 'create'])->name('quotes.packages.create');
Route::post('/quotes/packages', [QuotePackageController::class, 'store'])->name('quotes.packages.store');
Route::get('/quotes/packages/{quotePackage}/edit', [QuotePackageController::class, 'edit'])->name('quotes.packages.edit');
Route::put('/quotes/packages/{quotePackage}', [QuotePackageController::class, 'update'])->name('quotes.packages.update');
Route::delete('/quotes/packages/{quotePackage}', [QuotePackageController::class, 'destroy'])->name('quotes.packages.destroy');


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
    Route::post('/contacts/reorder', [ContactController::class, 'reorder'])->name('contacts.reorder');

    /*
    |--------------------------------------------------------------------------
    | Pagina policy
    |--------------------------------------------------------------------------
    */
    Route::get('/legal', [LegalAdminController::class, 'edit'])->name('legal.edit');
    Route::put('/legal', [LegalAdminController::class, 'update'])->name('legal.update');

    /*
    |--------------------------------------------------------------------------
    | CAPTCHA Sites (CRUD)
    |--------------------------------------------------------------------------
    */
    Route::resource('captcha-sites', CaptchaSiteController::class)->except(['show']);

    // Statistiche sito
    Route::get('captcha-sites/{captcha_site}/stats', [CaptchaSiteController::class, 'stats'])
        ->name('captcha-sites.stats');

    // Azione extra: rigenera secret (mai mostrata)
    Route::post('captcha-sites/{captcha_site}/regenerate-secret', [CaptchaSiteController::class, 'regenerateSecret'])
        ->name('captcha-sites.regenerate-secret');

    // Mostra secret (solo su richiesta, flash in session)
    Route::post('captcha-sites/{captcha_site}/reveal-secret', [CaptchaSiteController::class, 'revealSecret'])
        ->name('captcha-sites.reveal-secret');

    // Scarica kit (php|laravel|node|serverless)
    Route::get('captcha-sites/{captcha_site}/download-kit/{type}', [CaptchaSiteController::class, 'downloadKit'])
        ->name('captcha-sites.download-kit');

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD CENTER (ADMIN)
    |--------------------------------------------------------------------------
    */

    // Suites
    Route::get('/download/suites', [AdminSuiteController::class, 'index'])->name('admin.download.suites.index');
    Route::get('/download/suites/create', [AdminSuiteController::class, 'create'])->name('admin.download.suites.create');
    Route::post('/download/suites', [AdminSuiteController::class, 'store'])->name('admin.download.suites.store');
    Route::get('/download/suites/{id}/edit', [AdminSuiteController::class, 'edit'])->name('admin.download.suites.edit');
    Route::put('/download/suites/{id}', [AdminSuiteController::class, 'update'])->name('admin.download.suites.update');
    Route::delete('/download/suites/{id}', [AdminSuiteController::class, 'destroy'])->name('admin.download.suites.destroy');
    Route::post('/download/suites/{id}/toggle', [AdminSuiteController::class, 'toggle'])->name('admin.download.suites.toggle');

    // Languages
    Route::get('/download/languages', [AdminLanguageController::class, 'index'])->name('admin.download.languages.index');
    Route::get('/download/languages/create', [AdminLanguageController::class, 'create'])->name('admin.download.languages.create');
    Route::post('/download/languages', [AdminLanguageController::class, 'store'])->name('admin.download.languages.store');
    Route::get('/download/languages/{id}/edit', [AdminLanguageController::class, 'edit'])->name('admin.download.languages.edit');
    Route::put('/download/languages/{id}', [AdminLanguageController::class, 'update'])->name('admin.download.languages.update');
    Route::delete('/download/languages/{id}', [AdminLanguageController::class, 'destroy'])->name('admin.download.languages.destroy');
    Route::post('/download/languages/{id}/toggle', [AdminLanguageController::class, 'toggle'])->name('admin.download.languages.toggle');

    // Downloads (Item)
    Route::get('/download/items', [AdminDownloadController::class, 'index'])->name('admin.download.items.index');
    Route::get('/download/items/create', [AdminDownloadController::class, 'create'])->name('admin.download.items.create');
    Route::post('/download/items', [AdminDownloadController::class, 'store'])->name('admin.download.items.store');
    Route::get('/download/items/{id}/edit', [AdminDownloadController::class, 'edit'])->name('admin.download.items.edit');
    Route::put('/download/items/{id}', [AdminDownloadController::class, 'update'])->name('admin.download.items.update');
    Route::delete('/download/items/{id}', [AdminDownloadController::class, 'destroy'])->name('admin.download.items.destroy');
    Route::post('/download/items/{id}/toggle', [AdminDownloadController::class, 'toggle'])->name('admin.download.items.toggle');

    // Versions (nested sotto download)
    Route::get('/download/items/{downloadId}/versions', [AdminDownloadVersionController::class, 'index'])->name('admin.download.versions.index');
    Route::get('/download/items/{downloadId}/versions/create', [AdminDownloadVersionController::class, 'create'])->name('admin.download.versions.create');
    Route::post('/download/items/{downloadId}/versions', [AdminDownloadVersionController::class, 'store'])->name('admin.download.versions.store');
    Route::get('/download/items/{downloadId}/versions/{id}/edit', [AdminDownloadVersionController::class, 'edit'])->name('admin.download.versions.edit');
    Route::put('/download/items/{downloadId}/versions/{id}', [AdminDownloadVersionController::class, 'update'])->name('admin.download.versions.update');
    Route::delete('/download/items/{downloadId}/versions/{id}', [AdminDownloadVersionController::class, 'destroy'])->name('admin.download.versions.destroy');
    Route::post('/download/items/{downloadId}/versions/{id}/toggle', [AdminDownloadVersionController::class, 'toggle'])->name('admin.download.versions.toggle');
    Route::post('/download/items/{downloadId}/versions/{id}/latest', [AdminDownloadVersionController::class, 'setLatest'])->name('admin.download.versions.latest');

    // Assets (nested sotto versione)
    Route::get('/download/items/{downloadId}/versions/{versionId}/assets', [AdminDownloadAssetController::class, 'index'])->name('admin.download.assets.index');
    Route::get('/download/items/{downloadId}/versions/{versionId}/assets/create', [AdminDownloadAssetController::class, 'create'])->name('admin.download.assets.create');
    Route::post('/download/items/{downloadId}/versions/{versionId}/assets', [AdminDownloadAssetController::class, 'store'])->name('admin.download.assets.store');
    Route::get('/download/items/{downloadId}/versions/{versionId}/assets/{id}/edit', [AdminDownloadAssetController::class, 'edit'])->name('admin.download.assets.edit');
    Route::put('/download/items/{downloadId}/versions/{versionId}/assets/{id}', [AdminDownloadAssetController::class, 'update'])->name('admin.download.assets.update');
    Route::delete('/download/items/{downloadId}/versions/{versionId}/assets/{id}', [AdminDownloadAssetController::class, 'destroy'])->name('admin.download.assets.destroy');
    Route::post('/download/items/{downloadId}/versions/{versionId}/assets/{id}/toggle', [AdminDownloadAssetController::class, 'toggle'])->name('admin.download.assets.toggle');

    // pulizia cache
    Route::post('/clear-cache', function () {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Cache pulita!');
    })->name('admin.clear-cache');
});
