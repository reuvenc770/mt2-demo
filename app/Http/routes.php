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

Route::group(['middleware' => ['auth','admin']], function () {
    Route::get('register', 'RegistrationController@create');
    Route::post('register', ['as' => 'registration.store', 'uses' => 'RegistrationController@store']);
});

//guest only
Route::group(['middleware' => ['guest']], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
    Route::get('forgot_password', ['as' => 'forgetpassword.getemail', 'uses' => 'PasswordController@getEmail']);
    Route::post('forgot_password',['as' => 'forgetpassword.postemail', 'uses' => 'PasswordController@postEmail']);
    Route::get('reset_password/{token}', ['as' => 'password.reset', 'uses' => 'PasswordController@getReset']);
    Route::post('reset_password/{token}',['as' => 'password.store', 'uses' => 'PasswordController@postReset']);
});

//open routes
Route::resource('sessions', 'SessionsController' , ['only' => ['create','store','destroy']]);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@home']);
