<?php

use App\Http\Controllers\Admin\PerdoruesController;
use App\Http\Controllers\ArritjeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlerjeController;
use App\Http\Controllers\CompanyLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EdukimitController;
use App\Http\Controllers\FondetController;
use App\Http\Controllers\InvestimitController;
use App\Http\Controllers\IpoController;
use App\Http\Controllers\KompaniaController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\MarredhenieController;
use App\Http\Controllers\PersoniController;
use App\Http\Controllers\RaundiFinancimitController;
use App\Http\Controllers\VerifikuesController;
use App\Http\Controllers\ZyratController;
use Illuminate\Support\Facades\Route;

// ─── PUBLIC ──────────────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ─── AUTHENTICATED ────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // KPI (read-only, all roles)
    Route::get('/kpi', [KpiController::class, 'index'])->name('kpi.index');

    // ── End User Actions (Claim/Register Companies) ────────────────────────────
    Route::middleware('role:end_user')->group(function () {
        Route::get('/claim-company', [RegisterController::class, 'showClaimCompanyForm'])->name('claim-company.form');
        Route::post('/claim-company', [RegisterController::class, 'claimCompany'])->name('claim-company.post');
    });

    // ── Data entities ──────────────────────────────────────────────────────────
    Route::resource('kompanite', KompaniaController::class)
        ->parameters([
            'kompanite' => 'kompania',
        ]);
    Route::resource('personat', PersoniController::class)->parameters([
        'personat' => 'personi',
    ]);
    Route::resource('raundet', RaundiFinancimitController::class)->parameters([
        'raundet' => 'raundi',
    ]);
    Route::resource('investimet', InvestimitController::class)->parameters([
        'investimet' => 'investim',
    ]);
    Route::resource('fondet', FondetController::class)->parameters([
        'fondet' => 'fondi',
    ]);
    Route::resource('blerjet', BlerjeController::class)->parameters([
        'blerjet' => 'blerja',
    ]);
    Route::resource('ipos', IpoController::class)->parameters([
        'ipos' => 'ipo',
    ]);
    Route::resource('arritjet', ArritjeController::class)->parameters([
        'arritjet' => 'arritja',
    ]);
    Route::resource('zyrat', ZyratController::class)->parameters([
        'zyrat' => 'zyra',
    ]);
    Route::resource('marredheniet', MarredhenieController::class)->parameters([
        'marredheniet' => 'marredhenia',
    ]);

    Route::resource('edukimet', EdukimitController::class)->parameters([
        'edukimet' => 'edukimi',
    ]);

    // ── Verifikime ─────────────────────────────────────────────────────────────
    Route::middleware('role:verifikues,admin')
        ->prefix('verifikime')
        ->name('verifikime.')
        ->group(function () {
            Route::get('/', [VerifikuesController::class, 'index'])->name('queue');
            Route::post('/approve/{type}/{id}', [VerifikuesController::class, 'approve'])->name('approve');
            Route::post('/reject/{type}/{id}', [VerifikuesController::class, 'reject'])->name('reject');
            Route::post('/flag/{type}/{id}', [VerifikuesController::class, 'flag'])->name('flag');
        });

    // ── Admin ──────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::resource('perdoruesit', PerdoruesController::class);
        });

    // ── API Search Routes ──────────────────────────────────────────────────────
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/personat/search', [PersoniController::class, 'search'])->name('personat.search');
        Route::get('/raundet/search', [RaundiFinancimitController::class, 'search'])->name('raundet.search');
        Route::get('/kompanite/search-parent', [KompaniaController::class, 'searchParent'])
            ->name('kompanite-parent.search');

        Route::get('kompanite/search', [CompanyLookupController::class, 'search'])
            ->name('kompanite.search');
    });
});