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
    Route::get( '/' , array( 'as' => 'esp.index' , 'uses' => 'EspApiController@list' ) );

    Route::get( '/create' , array( 'as' => 'esp.create' , 'uses' => 'EspApiController@create' ) );

    Route::get( '/edit/{id}' , array( 'as' => 'esp.edit' , 'uses' => 'EspApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'api' ] , function () {
    Route::resource( 'esp' , 'EspApiController' , [ 'except' => [ 'create' , 'edit' ] ] );
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
