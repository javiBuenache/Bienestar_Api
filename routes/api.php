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

Route::post('import_CSV', 'UsageController@import');


Route::group(['middleware' => ['auth']], function () {
    Route::apiResource('users', 'UserController');
    Route::apiResource('applications','AppController');
	Route::apiResource('restriction','RestrictionController@store');
    Route::apiResource('usage','UsageController');
    Route::get('show_apps','AppController@show');
    Route::get('show_locations', 'UsageController@show_locations');
    Route::get('show_data_user','UserController@show');
    Route::post('update_password','UserController@update');
    
}); 