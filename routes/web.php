<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/data.json', [MapController::class, 'getGeoJson']);
Route::get('/map', function () {
    return view('map-old');
});

//Route::get('/', [MapController::class, 'index']);
Route::get('/getFiles', [MapController::class, 'getFiles']);
Route::get('/getFile', [MapController::class, 'getFile']);
