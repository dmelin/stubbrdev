<?php

use App\Http\Controllers\DanielController;
use Illuminate\Support\Facades\Route;

Route::post('/daniel/chat', [DanielController::class, 'chat']);
Route::options('/daniel/chat', [DanielController::class, 'chat']);
