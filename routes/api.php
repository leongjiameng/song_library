<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('api')->namespace('Auth')->prefix('auth')->group(function() {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::middleware(['jwt.auth', 'can:manage-songs'])->group(function() {
    Route::apiResource('songs', 'SongController')->only([
        'store',
        'update',
    ]);
    Route::apiResource('artists', 'ArtistController')->only([
        'store',
        'update',
    ]);


});

Route::middleware(['jwt.auth', 'can:view-songs'])->group(function() {

    Route::apiResource('songs', 'SongController')->only([
        'index',
        'show',
    ]);
    Route::apiResource('artists', 'ArtistController')->only([
        'index',
        'show',
    ]);


});

Route::middleware(['jwt.auth', 'can:manage-playlists'])->group(function() {

    Route::apiResource('playlists', 'PlaylistController');


});