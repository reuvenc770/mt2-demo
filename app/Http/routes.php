<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return View::make( 'layout.app' );
});
Route::get('test', 'TestStuff@index');
Route::get('update', 'HomeController@fuck');
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['auth','admin']], function () {
    Route::get('register', 'RegistrationController@create');
    Route::post('register', ['as' => 'registration.store', 'uses' => 'RegistrationController@store']);
});
//guest only
Route::group(['middleware' => ['guest']], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
});

//open routes
Route::resource('sessions', 'SessionsController' , ['only' => ['create','store','destroy']]);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@home']);