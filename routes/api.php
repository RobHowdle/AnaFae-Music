<?php

use App\Http\Controllers\Api\ExternalReviewsController;
use App\Http\Controllers\Api\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/pages/{slug}', [PageController::class, 'show']);
Route::get('/reviews/lmm', [ExternalReviewsController::class, 'lastMinuteMusicians']);
