<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CalendarEventController;

Route::get('/calendar-events', [CalendarEventController::class, 'index']);

