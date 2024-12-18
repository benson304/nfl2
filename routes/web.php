<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\PlayerStatsController;
use App\Http\Livewire\Admin\BulkStatsUpdate;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StandingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Entry routes
    Route::get('/entries', [EntryController::class, 'index'])->name('entries.index');
    Route::get('/entries/create', [EntryController::class, 'create'])->name('entries.create');
    Route::post('/entries', [EntryController::class, 'store'])->name('entries.store');
    Route::get('/entries/{entry}/roster', [EntryController::class, 'roster'])->name('entries.roster');
    Route::post('/entries/{entry}/add-player', [EntryController::class, 'addPlayer'])->name('entries.add-player');
    Route::post('/entries/{entry}/revert-player', [EntryController::class, 'revertPlayer'])->name('entries.revert-player');
    Route::post('/entries/{entry}/transactions', [EntryController::class, 'processTransaction'])->name('entries.process-transaction');

    // Standings routes
    Route::get('/standings', [StandingsController::class, 'index'])->name('standings.index');
    Route::get('/standings/week/{week}', [StandingsController::class, 'weekly'])->name('standings.weekly');

    // Public entry routes
    Route::get('/public-entries', [EntryController::class, 'publicIndex'])->name('entries.public');
    Route::get('/public-entries/{entry}', [EntryController::class, 'publicRoster'])->name('entries.public.roster');
    Route::get('/entries/{entry}/public', [EntryController::class, 'publicRoster'])->name('entries.public.roster');

    // Transactions route
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

    // Player Management
    Route::resource('players', PlayerController::class, ['as' => 'admin']);

    // Game Management
    Route::resource('games', GameController::class, ['as' => 'admin']);

    // Team Management
    Route::resource('teams', TeamController::class, ['as' => 'admin']);

// Player Stats Management
    Route::resource('player-stats', PlayerStatsController::class, ['as' => 'admin'])
        ->parameters(['player-stats' => 'player_stat']);
    /*
    Route::get('player-stats', [PlayerStatsController::class, 'index'])->name('admin.player-stats.index');
    Route::get('player-stats/create', [PlayerStatsController::class, 'create'])->name('admin.player-stats.create');
    Route::post('player-stats', [PlayerStatsController::class, 'store'])->name('admin.player-stats.store');
    Route::get('player-stats/{game}', [PlayerStatsController::class, 'show'])->name('admin.player-stats.show');
    Route::get('player-stats/{game}/edit', [PlayerStatsController::class, 'edit'])->name('admin.player-stats.edit');
    Route::put('player-stats/{game}', [PlayerStatsController::class, 'update'])->name('admin.player-stats.update');
    Route::delete('player-stats/{stats}', [PlayerStatsController::class, 'destroy'])->name('admin.player-stats.destroy');
    */
    Route::get('/bulk-stats', [AdminController::class, 'bulkStats'])->name('admin.bulk-stats.index');
    Route::get('games/{game}/stats', [GameController::class, 'stats'])->name('admin.games.stats');
    Route::post('/games/stats/bulk-update', [GameController::class, 'bulkUpdate'])
    ->name('admin.games.stats.bulk-update');
    Route::post('/admin/bulk-stats/import', [AdminController::class, 'importStats'])
        ->name('admin.bulk-stats.import');
});
