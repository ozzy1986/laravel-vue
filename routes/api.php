<?php

declare(strict_types=1);

use App\Http\Controllers\Api\FeedbackController;
use Illuminate\Support\Facades\Route;

Route::post('/feedbacks', [FeedbackController::class, 'store'])->name('api.feedbacks.store');
