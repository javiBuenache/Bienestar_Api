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


Route::post('register', 'UserController@store');
Route::post('login','UserController@login');
Route::post('recuperate_password','UserController@recuperate_password');
Route::apiResource('applications', 'AppController');

Route::group(['middleware' => ['auth']], function () {
    Route::apiResource('users', 'UserController');
    Route::post('import_CSV', 'UserController@import_CSV');
}); 