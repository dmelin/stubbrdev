<?php

use App\Http\Controllers\Dummy;
use App\Http\Controllers\RequestCacheController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiKeyController;


Route::get('/__token/request', [ApiKeyController::class, 'requestKey'])->middleware('throttle.token');;
Route::get('/__token/recover', [ApiKeyController::class, 'recoverKey'])->middleware('throttle.token');;
Route::get('/__token/verify', [ApiKeyController::class, 'verifyKey'])->middleware('throttle.token');;

Route::any('/__cache/clear', [RequestCacheController::class, 'clear'])
    ->middleware('adaptiveThrottle');

Route::any('/{any?}', [Dummy::class, 'hello'])
    ->middleware('adaptiveThrottle')
    ->where('any', '.*');
