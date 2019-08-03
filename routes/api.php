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

Route::middleware( 'auth:api' )->get( '/user', function ( Request $request ) {
    return $request->user();
} );

// 인증이 필요없는 API
Route::middleware( 'guest' )->group( function () {
    Route::post( 'signup', 'AuthController@signup' );
    Route::post( 'login', 'AuthController@login' );
} );

// 인증(로그인)이 필요한 API
Route::middleware( 'auth:api' )->group( function () {
    Route::get( 'logout', 'AuthController@logout' );
    Route::get( 'me', 'UserController@me' );
} );