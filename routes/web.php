<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BeachController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SelectionController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Site public
|--------------------------------------------------------------------------
| Le middleware « ffvb.sync » relance l'import des résultats FFVolley en
| arrière-plan (après l'envoi de la page) quand la dernière mise à jour est
| trop ancienne.
*/
Route::middleware('ffvb.sync')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::prefix('actualites')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/categorie/{category:slug}', [NewsController::class, 'category'])->name('category');
        Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
    });

    Route::prefix('competitions')->name('competitions.')->group(function () {
        Route::get('/', [CompetitionController::class, 'index'])->name('index');
        Route::get('/{season:slug}/{competition:slug}', [CompetitionController::class, 'show'])->name('show')->scopeBindings();
    });

    Route::prefix('matchs')->name('games.')->group(function () {
        Route::get('/', [GameController::class, 'index'])->name('index');
        Route::get('/resultats', [GameController::class, 'results'])->name('results');
        Route::get('/calendrier', [GameController::class, 'schedule'])->name('schedule');
        Route::get('/{game}', [GameController::class, 'show'])->name('show')->whereNumber('game');
    });

    Route::prefix('clubs')->name('clubs.')->group(function () {
        Route::get('/', [ClubController::class, 'index'])->name('index');
        Route::get('/{club:slug}', [ClubController::class, 'show'])->name('show');
    });

    Route::prefix('beach')->name('beach.')->group(function () {
        Route::get('/', [BeachController::class, 'index'])->name('index');
        Route::get('/calendrier', [BeachController::class, 'calendar'])->name('calendar');
        Route::get('/{event:slug}', [BeachController::class, 'show'])->name('show');
        Route::post('/{event:slug}/inscription', [BeachController::class, 'register'])
            ->name('register')->middleware('throttle:5,10');
    });

    Route::prefix('selections')->name('selections.')->group(function () {
        Route::get('/', [SelectionController::class, 'index'])->name('index');
        Route::get('/{team:slug}', [SelectionController::class, 'show'])->name('show');
    });

    Route::prefix('galerie')->name('gallery.')->group(function () {
        Route::get('/', [GalleryController::class, 'index'])->name('index');
        Route::get('/categorie/{category}', [GalleryController::class, 'category'])->name('category');
        Route::get('/photo/{gallery}', [GalleryController::class, 'show'])->name('show');
    });

    Route::get('/la-ligue', [LeagueController::class, 'index'])->name('league');
    Route::get('/mentions-legales', [LeagueController::class, 'legal'])->name('legal');

    Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store')->middleware('throttle:5,10');
});

Route::post('/newsletter', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe')->middleware('throttle:5,10');
Route::get('/newsletter/desinscription/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Authentification (pas d'inscription publique : les comptes sont créés par le super admin)
|--------------------------------------------------------------------------
*/
Auth::routes(['register' => false, 'verify' => false]);
Route::redirect('/home', '/admin');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
| admin : super admin + administrateurs de club (données limitées à leur club)
| super_admin : gestion globale du site
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('news', Admin\NewsController::class)->except('show');
    Route::resource('gallery', Admin\GalleryController::class)->except('show');
    Route::resource('clubs', Admin\ClubController::class)->except('show');
    Route::get('games', [Admin\GameController::class, 'index'])->name('games.index');

    Route::post('contacts/mark-read', [Admin\ContactController::class, 'markRead'])->name('contacts.markRead');
    Route::delete('contacts/read', [Admin\ContactController::class, 'destroyRead'])->name('contacts.destroyRead');
    Route::resource('contacts', Admin\ContactController::class)->only(['index', 'show', 'destroy']);

    Route::get('profil', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profil', [Admin\ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('super_admin')->group(function () {
        Route::resource('games', Admin\GameController::class)->except(['index', 'show']);

        Route::get('competitions', [Admin\CompetitionController::class, 'index'])->name('competitions.index');
        Route::put('competitions/{competition}', [Admin\CompetitionController::class, 'update'])->name('competitions.update');
        Route::post('sync', [Admin\SyncController::class, 'store'])->name('sync');

        Route::resource('users', Admin\UserController::class)->except('show');
        Route::resource('teams', Admin\TeamController::class)->except('show');
        Route::resource('teams.players', Admin\PlayerController::class)->except(['index', 'show'])->shallow();
        Route::resource('beach', Admin\BeachEventController::class)->parameters(['beach' => 'event'])->except('show');
        Route::get('beach/{event}/inscriptions', [Admin\BeachEventController::class, 'registrations'])->name('beach.registrations');
        Route::put('beach-inscriptions/{registration}', [Admin\BeachEventController::class, 'updateRegistration'])->name('beach.registrations.update');
        Route::resource('board', Admin\BoardMemberController::class)->parameters(['board' => 'member'])->except('show');
        Route::resource('documents', Admin\DocumentController::class)->except('show');
        Route::resource('partners', Admin\PartnerController::class)->except('show');
        Route::get('newsletter', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
        Route::get('newsletter/export', [Admin\NewsletterController::class, 'export'])->name('newsletter.export');

        Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
    });
});
