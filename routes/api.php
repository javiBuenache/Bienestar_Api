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
Route::post('import_CSV', 'AppController@import_CSV');

Route::group(['middleware' => ['auth']], function () {
    Route::apiResource('users', 'UserController');
    Route::get('show_data_user','UserController@show');
    Route::post('update_password','UserController@update');
    
}); 