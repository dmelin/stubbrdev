<?php

use App\Http\Controllers\Dummy;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;


Route::get('/__token/request', [ApiKeyController::class, 'requestKey']);
Route::get('/__token/recover', [ApiKeyController::class, 'recoverKey']);
Route::get('/__token/verify', [ApiKeyController::class, 'verifyKey']);

Route::any('/{any?}', [Dummy::class, 'hello'])
    ->middleware('adaptiveThrottle')
    ->where('any', '.*');
