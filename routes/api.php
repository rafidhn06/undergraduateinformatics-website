<?php

use App\Http\Controllers\Api\DatasetController;
use App\Http\Controllers\Api\FeedbackFormController;
use App\Http\Controllers\Api\FeedbackSubmissionController;
use App\Http\Controllers\Api\ImportantLinkController;
use App\Http\Controllers\Api\LinkSectionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReservationFormController;
use App\Http\Controllers\Api\ReservationSubmissionController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{slug}', [TagController::class, 'show']);

Route::get('/link-sections', [LinkSectionController::class, 'index']);
Route::get('/important-links', [ImportantLinkController::class, 'index']);
Route::get('/datasets', [DatasetController::class, 'index']);

Route::get('/feedback-form', [FeedbackFormController::class, 'show']);
Route::post('/feedback-submissions', [FeedbackSubmissionController::class, 'store'])->middleware('throttle:10,1');

Route::get('/reservation-form', [ReservationFormController::class, 'show']);
Route::get('/reservation-form/availability', [ReservationFormController::class, 'availability']);
Route::post('/reservation-submissions', [ReservationSubmissionController::class, 'store'])->middleware('throttle:10,1');
