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

Route::group( [ 'prefix' => 'espapi', 'middleware' => ['auth'] ] , function () {
    Route::get( '/' , array( 'as' => 'esp.index' , 'uses' => 'EspApiController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'esp.create' , 'uses' => 'EspApiController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'esp.edit' , 'uses' => 'EspApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'user', 'middleware' => ['auth','admin'] ] , function () {
    Route::get( '/' , array( 'as' => 'user.index' , 'uses' => 'UserApiController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'user.create' , 'uses' => 'UserApiController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'user.edit' , 'uses' => 'UserApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'api' ] , function () {
    Route::resource( 'esp' , 'EspApiController' , [ 'except' => [ 'create' , 'edit' ] ,'middleware' => ['auth']  ] );
    Route::resource('user', 'UserApiController',  [ 'except' => [ 'create' , 'edit' ] ,'middleware' => ['auth','admin']] );
} );


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


Route::resource('sessions', 'SessionsController' , ['only' => ['create','store','destroy']]);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@home']);
