<?php

use App\Http\Controllers\Internal\DeployController;
use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\DashboardController;
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
use App\Http\Controllers\Web\PostController as WebPostController;
use App\Http\Controllers\Web\ReservationController;
use App\Http\Controllers\Web\TagController as WebTagController;
use App\Support\PageMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tags', [WebTagController::class, 'index'])->name('tags.index');
Route::get('/tags/{slug}', [WebTagController::class, 'show'])->name('tags.show');
Route::get('/posts', [WebPostController::class, 'index'])->name('posts.index');
Route::get('/posts/search', function (Request $request) {
    $query = array_filter([
        'q' => is_string($request->query('q')) ? $request->query('q') : null,
        'limit' => $request->query('per_page', $request->query('limit')),
        'page' => $request->query('page'),
    ]);

    return redirect('/posts?'.http_build_query($query), 301);
});
Route::get('/posts/{slug}', [WebPostController::class, 'show'])->name('posts.show');
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::get('/reservation', [ReservationController::class, 'show'])->name('reservation.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminController::class, 'loginForm'])->name('login');
        Route::post('login', [AdminController::class, 'login'])->name('loginAttempt');
        Route::resource('password-resets', PasswordResetController::class)->only(['create', 'store', 'edit', 'update'])->parameters(['password-resets' => 'passwordReset']);
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.datasets.index'));

        Route::get('datasets', [DashboardController::class, 'index'])->name('datasets.index');
        Route::get('datasets/create', [DashboardController::class, 'create'])->name('datasets.create');
        Route::post('datasets', [DashboardController::class, 'store'])->name('datasets.store');
        Route::get('datasets/{dashboardDataset}/edit', [DashboardController::class, 'edit'])->name('datasets.edit');
        Route::match(['put', 'patch'], 'datasets/{dashboardDataset}', [DashboardController::class, 'update'])->name('datasets.update');
        Route::delete('datasets', [DashboardController::class, 'destroyAll'])->name('datasets.destroyAll');

        Route::get('dataset-imports/create', [DatasetImportController::class, 'create'])->name('dataset-imports.create');
        Route::post('dataset-imports', [DatasetImportController::class, 'store'])->name('dataset-imports.store');
        Route::get('dataset-imports/{datasetImport}', [DatasetImportController::class, 'show'])->name('dataset-imports.show');
        Route::match(['put', 'patch'], 'dataset-imports/{datasetImport}', [DatasetImportController::class, 'update'])->name('dataset-imports.update');
        Route::delete('dataset-imports/{datasetImport}', [DatasetImportController::class, 'destroy'])->name('dataset-imports.destroy');
        Route::resource('reservations', ReservationScheduleController::class)->except(['show'])->parameters(['reservations' => 'reservationSchedule']);

        Route::get('sections/reorder', [ImportantSectionController::class, 'editOrder'])->name('sections.reorder');
        Route::match(['put', 'patch'], 'sections', [ImportantSectionController::class, 'updateAll'])->name('sections.updateAll');
        Route::singleton('form-links', FormLinkController::class)->only(['show', 'update']);

        Route::post('logout', [AdminController::class, 'logout'])->name('logout');
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('links', ImportantLinkController::class)->except(['show'])->parameters(['links' => 'importantLink']);
        Route::resource('tags', TagController::class)->except(['show']);
        Route::resource('sections', ImportantSectionController::class)->except(['show'])->parameters(['sections' => 'importantSection']);

        Route::singleton('password-recovery', PasswordRecoveryController::class)->only(['edit', 'update']);
    });
});

Route::post('/internal/deploy', [DeployController::class, 'run'])->middleware('throttle:5,1');

Route::fallback(function (Request $request) {
    if ($request->is('admin/*') || $request->is('api/*')) {
        abort(404);
    }

    return response()->view(
        'app',
        PageMeta::viewData($request, 'notFound', [], ['notFound' => true], null, null),
        404
    );
});
