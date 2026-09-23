<?php

use App\Http\Controllers\Internal\DeployController;
use App\Http\Controllers\Web\Admin\AdminAuthController;
use App\Http\Controllers\Web\Admin\AdminHomeController;
use App\Http\Controllers\Web\Admin\DashboardDatasetController;
use App\Http\Controllers\Web\Admin\DatasetImportController;
use App\Http\Controllers\Web\Admin\FormLinkController;
use App\Http\Controllers\Web\Admin\ImportantLinkController;
use App\Http\Controllers\Web\Admin\PasswordRecoveryController;
use App\Http\Controllers\Web\Admin\PasswordResetController;
use App\Http\Controllers\Web\Admin\PostController;
use App\Http\Controllers\Web\Admin\ReservationScheduleController;
use App\Http\Controllers\Web\Admin\ImportantSectionController;
use App\Http\Controllers\Web\Admin\TagController;
use App\Http\Controllers\Web\FeedbackController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LinkController;
use App\Http\Controllers\Web\FallbackController;
use App\Http\Controllers\Web\PostController as WebPostController;
use App\Http\Controllers\Web\PostSearchRedirectController;
use App\Http\Controllers\Web\ReservationController;
use App\Http\Controllers\Web\TagController as WebTagController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tags', [WebTagController::class, 'index'])->name('tags.index');
Route::get('/tags/{slugOrId}', [WebTagController::class, 'show'])->name('tags.show');
Route::get('/posts', [WebPostController::class, 'index'])->name('posts.index');
Route::get('/posts/search', PostSearchRedirectController::class)->name('posts.search');
Route::get('/posts/{slugOrId}', [WebPostController::class, 'show'])->name('posts.show');
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::get('/reservation', [ReservationController::class, 'show'])->name('reservation.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'loginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('loginAttempt');
        Route::resource('password-resets', PasswordResetController::class)->only(['create', 'store', 'edit', 'update'])->parameters(['password-resets' => 'passwordReset']);
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', AdminHomeController::class)->name('home');

        Route::get('datasets', [DashboardDatasetController::class, 'index'])->name('datasets.index');
        Route::get('datasets/create', [DashboardDatasetController::class, 'create'])->name('datasets.create');
        Route::post('datasets', [DashboardDatasetController::class, 'store'])->name('datasets.store');
        Route::get('datasets/{dashboardDataset}/edit', [DashboardDatasetController::class, 'edit'])->name('datasets.edit');
        Route::match(['put', 'patch'], 'datasets/{dashboardDataset}', [DashboardDatasetController::class, 'update'])->name('datasets.update');
        Route::delete('datasets', [DashboardDatasetController::class, 'destroyAll'])->name('datasets.destroyAll');

        Route::get('dataset-imports/create', [DatasetImportController::class, 'create'])->name('dataset-imports.create');
        Route::post('dataset-imports', [DatasetImportController::class, 'store'])->name('dataset-imports.store');
        Route::get('dataset-imports/{datasetImport}', [DatasetImportController::class, 'show'])->name('dataset-imports.show');
        Route::match(['put', 'patch'], 'dataset-imports/{datasetImport}', [DatasetImportController::class, 'confirm'])->name('dataset-imports.confirm');
        Route::delete('dataset-imports/{datasetImport}', [DatasetImportController::class, 'destroy'])->name('dataset-imports.destroy');
        Route::get('reservations/{reservationSchedule}/document', [ReservationScheduleController::class, 'showDocument'])->name('reservations.document');
        Route::resource('reservations', ReservationScheduleController::class)->except(['show'])->parameters(['reservations' => 'reservationSchedule']);

        Route::get('sections/reorder', [ImportantSectionController::class, 'reorder'])->name('sections.reorder');
        Route::match(['put', 'patch'], 'sections/reorder', [ImportantSectionController::class, 'updateReorder'])->name('sections.reorder.update');
        Route::singleton('form-links', FormLinkController::class)->only(['show', 'update']);

        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('links', ImportantLinkController::class)->except(['show'])->parameters(['links' => 'importantLink']);
        Route::resource('tags', TagController::class)->except(['show']);
        Route::resource('sections', ImportantSectionController::class)->except(['show'])->parameters(['sections' => 'importantSection']);

        Route::singleton('password-recovery', PasswordRecoveryController::class)->only(['edit', 'update']);
    });
});

Route::post('/internal/deploy', [DeployController::class, 'run'])->middleware('throttle:5,1');

Route::fallback(FallbackController::class);
