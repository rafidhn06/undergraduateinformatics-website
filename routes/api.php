<?php

use App\Http\Controllers\Api\DatasetController;
use App\Http\Controllers\Api\FeedbackFormController;
use App\Http\Controllers\Api\FeedbackSubmissionController;
use App\Http\Controllers\Api\ImportantLinkController;
use App\Http\Controllers\Api\ImportantSectionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReservationFormController;
use App\Http\Controllers\Api\ReservationSubmissionController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PostController::class, 'index'])->name('api.posts.index');
Route::get('/posts/{slugOrId}', [PostController::class, 'show'])->name('api.posts.show');

Route::get('/tags', [TagController::class, 'index'])->name('api.tags.index');
Route::get('/tags/{slugOrId}', [TagController::class, 'show'])->name('api.tags.show');

Route::get('/link-sections', [ImportantSectionController::class, 'index'])->name('api.link-sections.index');
Route::get('/important-links', [ImportantLinkController::class, 'index'])->name('api.important-links.index');
Route::get('/datasets', DatasetController::class)->name('api.datasets.index');

Route::get('/feedback-form', [FeedbackFormController::class, 'show'])->name('api.feedback-form.show');
Route::post('/feedback-submissions', [FeedbackSubmissionController::class, 'store'])->middleware('throttle:10,1')->name('api.feedback-submissions.store');

Route::get('/reservation-form', [ReservationFormController::class, 'show'])->name('api.reservation-form.show');
Route::get('/reservation-form/availability', [ReservationFormController::class, 'availability'])->name('api.reservation-form.availability');
Route::post('/reservation-submissions', [ReservationSubmissionController::class, 'store'])->middleware('throttle:10,1')->name('api.reservation-submissions.store');
