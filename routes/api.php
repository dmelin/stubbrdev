<?php

use App\Http\Controllers\Dummy;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;


Route::get('/__token/request', [ApiKeyController::class, 'requestKey'])->middleware('throttle.token');;
Route::get('/__token/recover', [ApiKeyController::class, 'recoverKey'])->middleware('throttle.token');;
Route::get('/__token/verify', [ApiKeyController::class, 'verifyKey'])->middleware('throttle.token');;

Route::any('/{any?}', [Dummy::class, 'hello'])
    ->middleware('adaptiveThrottle')
    ->where('any', '.*');
