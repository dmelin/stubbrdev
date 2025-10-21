<?php

use App\Http\Controllers\Dummy;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;


Route::post('/__token/request', [ApiKeyController::class, 'requestKey']);
Route::post('/__token/recover', [ApiKeyController::class, 'recoverKey']);
Route::post('/__token/verify', [ApiKeyController::class, 'verifyKey']);

Route::any('/{any?}', [Dummy::class, 'hello'])
    ->middleware('adaptiveThrottle')
    ->where('any', '.*');
