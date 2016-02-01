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

Route::group( [ 'prefix' => 'esp' ] , function () {
    Route::get( '/' , 'EspUiController@index' );
    Route::get( '/add' , 'EspUiController@add' );
} );

Route::group( [ 'prefix' => 'api' ] , function () {
    Route::resource( 'esp' , 'EspApiController' );
} );

Route::get('test', 'TestStuff@index');
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

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        return View::make( 'layout.app' );
    });
});
