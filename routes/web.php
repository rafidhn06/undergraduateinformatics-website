<?php

use App\Http\Controllers\Web\Admin\AdminController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\FormLinkController;
use App\Http\Controllers\Web\Admin\LinkController as AdminLinkController;
use App\Http\Controllers\Web\Admin\PostController;
use App\Http\Controllers\Web\Admin\ReservationScheduleController;
use App\Http\Controllers\Web\Admin\SectionController;
use App\Http\Controllers\Web\Admin\TagController;
use App\Http\Controllers\Web\FeedbackController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LinkController;
use App\Http\Controllers\Web\PostController as WebPostController;
use App\Http\Controllers\Web\ReservationController;
use App\Http\Controllers\Web\SearchController;
use App\Http\Controllers\Web\TagController as WebTagController;
use App\Models\DashboardDataset;
use App\Models\ReservationSchedule;
use App\Support\PageMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Internal\DeployController;
use Illuminate\Support\Facades\Schema;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/tags', [WebTagController::class, 'index'])->name('tags.index');
Route::get('/tags/{slug}', [WebTagController::class, 'show'])->name('tags.show');
Route::get('/posts/search', [SearchController::class, 'index'])->name('posts.search');
Route::get('/posts/{slug}', [WebPostController::class, 'show'])->name('posts.show');
Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
Route::get('/reservation', [ReservationController::class, 'show'])->name('reservation.show');

/*
 * Admin uses Laravel's regular web stack: session authentication, named routes,
 * Blade views, and standard form submissions.  The public site remains served
 * by its existing React/Vite entry point.
 */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminController::class, 'loginForm'])->name('login');
        Route::post('login', [AdminController::class, 'login'])->name('loginAttempt');
    });

    Route::get('forgot-password', [AdminController::class, 'forgotForm'])->name('forgotPassword');
    Route::post('submit-email-recovery', [AdminController::class, 'submitEmailRecovery'])->name('submitEmailRecovery');
    Route::get('question-form', [AdminController::class, 'questionForm'])->name('questionForm');
    Route::post('submit-answer-recovery', [AdminController::class, 'submitAnswerRecovery'])->name('submitAnswerRecovery');
    Route::get('password-recovery-form', [AdminController::class, 'passwordRecoveryForm'])->name('passwordRecoveryForm');
    Route::post('submit-password-recovery', [AdminController::class, 'submitPasswordRecovery'])->name('submitPasswordRecovery');

    Route::middleware('auth')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('dashboard', function () {
            $dashboardTablesReady = Schema::hasTable('dashboard_datasets')
                && Schema::hasTable('dashboard_dataset_items');

            $datasets = $dashboardTablesReady
                ? DashboardDataset::with(['items' => fn ($query) => $query->orderBy('sort_order')])
                    ->orderBy('id')
                    ->get()
                    ->map(fn (DashboardDataset $dataset) => [
                        'id' => $dataset->id,
                        'title' => $dataset->title,
                        'chart_type' => $dataset->chart_type,
                        'x_label' => $dataset->x_label,
                        'y_label' => $dataset->y_label,
                        'labels' => $dataset->items->pluck('label')->values(),
                        'values' => $dataset->items->pluck('value')->values(),
                    ])
                : collect();

            return view('AdminDashboard.index', [
                'datasets' => $datasets,
                'dashboardTablesReady' => $dashboardTablesReady,
            ]);
        })->name('dashboard');
        Route::get('dashboard/upload', [DashboardController::class, 'upload'])->name('dashboard.upload');
        Route::get('dashboard/create', [DashboardController::class, 'create'])->name('dashboard.create');
        Route::get('dashboard/{id}/edit', [DashboardController::class, 'edit'])->name('dashboard.edit');
        Route::post('dashboard/extract', [DashboardController::class, 'extract'])->name('dashboard.extract');
        Route::post('dashboard/save', [DashboardController::class, 'save'])->name('dashboard.save');
        Route::post('dashboard/store', [DashboardController::class, 'store'])->name('dashboard.store');
        Route::post('dashboard/{id}/update', [DashboardController::class, 'update'])->name('dashboard.update');
        Route::delete('dashboard/cleardata', [DashboardController::class, 'cleardata'])->name('dashboard.cleardata');
        Route::get('reservation', function (Request $request) {
            $reservationTableReady = Schema::hasTable('reservation_schedules');
            $reservationDetailsReady = $reservationTableReady
                && Schema::hasColumn('reservation_schedules', 'meeting_room');
            $search = trim((string) $request->get('search', ''));

            $reservations = collect();
            if ($reservationTableReady) {
                $query = ReservationSchedule::latest();
                if ($search !== '') {
                    $query->where(function ($inner) use ($search) {
                        $inner->where('requested_by', 'like', '%' . $search . '%')
                            ->orWhere('meeting_room', 'like', '%' . $search . '%')
                            ->orWhere('study_program', 'like', '%' . $search . '%')
                            ->orWhere('date', 'like', '%' . $search . '%')
                            ->orWhere('shift', 'like', '%' . $search . '%')
                            ->orWhere('agenda', 'like', '%' . $search . '%');
                    });
                }
                $reservations = $query->paginate(10)->withQueryString();
            }

            return view('AdminDashboard.reservation', [
                'reservationTableReady' => $reservationTableReady,
                'reservationDetailsReady' => $reservationDetailsReady,
                'reservations' => $reservations,
            ]);
        })->name('reservation');
        Route::get('reservation/create', [ReservationScheduleController::class, 'create'])->name('reservation.create');
        Route::get('reservation/{id}/edit', [ReservationScheduleController::class, 'edit'])->name('reservation.edit');
        Route::post('reservation/store', [ReservationScheduleController::class, 'store'])->name('reservation.store');
        Route::post('reservation/{id}/update', [ReservationScheduleController::class, 'update'])->name('reservation.update');
        Route::delete('reservation/{id}', [ReservationScheduleController::class, 'destroy'])->name('reservation.destroy');

        Route::get('form-link', [FormLinkController::class, 'show'])->name('form-link');
        Route::put('form-link/feedback', [FormLinkController::class, 'updateFeedback'])->name('form-link.feedback.update');
        Route::put('form-link/reservation', [FormLinkController::class, 'updateReservation'])->name('form-link.reservation.update');
        Route::put('form-link/{kind}/refresh', [FormLinkController::class, 'refresh'])->name('form-link.refresh');

        Route::get('logout', [AdminController::class, 'logout'])->name('logout');
        Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('links', AdminLinkController::class)->except(['show']);
        Route::resource('tags', TagController::class)->except(['show']);
        Route::resource('sections', SectionController::class)->except(['show']);

        Route::get('sections/change-order', [SectionController::class, 'changeOrder'])->name('sections.changeOrder');
        Route::post('sections/update-order', [SectionController::class, 'updateOrder'])->name('sections.updateOrder');

        Route::get('edit-password-recovery-questions', [AdminController::class, 'editPasswordRecoveryQuestion'])->name('editPasswordRecoveryQuestion');
        Route::post('update-password-recovery-questions', [AdminController::class, 'updatePasswordRecoveryQuestion'])->name('updatePasswordRecoveryQuestion');
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
