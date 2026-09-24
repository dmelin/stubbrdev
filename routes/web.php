<?php

use Illuminate\Support\Facades\Route;

// The front-end is a small single-page app with three pages.
Route::view('/', 'welcome');
Route::view('/builder', 'welcome');
Route::view('/docs', 'welcome');
