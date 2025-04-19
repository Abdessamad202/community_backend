<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test-dump', function () {
    dump('Hello Telescope!');
    return 'Check Telescope Dumps tab.';
});
// Broadcast::routes(['middleware' => ['auth:sanctum']]);
