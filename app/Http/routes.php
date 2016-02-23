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
    return redirect("/login");
});
Route::group( [ 'prefix' => 'espapi', 'middleware' => ['auth', 'pageLevel'] ] , function () {
    Route::get( '/' , array( 'as' => 'espapi.list' , 'uses' => 'EspApiController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'espapi.add' , 'uses' => 'EspApiController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'espapi.edit' , 'uses' => 'EspApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'pages', 'middleware' => ['auth', 'pageLevel'] ] , function () {
    Route::get( '/show-info' , array( 'as' => 'pages.showinfo' , 'uses' => 'ShowInfoController@index' ) );
} );

Route::group( [ 'prefix' => 'devtools', 'middleware' => ['auth','dev',] ] , function () {
    Route::get( '/jobs' , array( 'as' => 'devtools.jobs' , 'uses' => 'JobApiController@listALL' ) );
} );

Route::group( [ 'prefix' => 'user', 'middleware' => ['auth','admin', 'pageLevel'] ] , function () {
    Route::get( '/' , array( 'as' => 'user.list' , 'uses' => 'UserApiController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'user.add' , 'uses' => 'UserApiController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'user.edit' , 'uses' => 'UserApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'client', 'middleware' => ['auth', 'pageLevel'] ] , function () {
    Route::get( '/' , array( 'as' => 'client.list' , 'uses' => 'ClientController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'client.add' , 'uses' => 'ClientController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'client.edit' , 'uses' => 'ClientController@edit' ) );
} );

Route::group( [ 'prefix' => 'clientgroup', 'middleware' => ['auth', 'pageLevel'] ] , function () {
    Route::get( '/' , array( 'as' => 'clientgroup.list' , 'uses' => 'ClientGroupController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'clientgroup.add' , 'uses' => 'ClientGroupController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'clientgroup.edit' , 'uses' => 'ClientGroupController@edit' ) );
} );

Route::group( [ 'prefix' => 'role', 'middleware' => ['auth','admin', 'pageLevel'] ] , function () {
    Route::get( '/' , array( 'as' => 'role.list' , 'uses' => 'RoleApiController@listAll' ) );
    Route::get( '/create' , array( 'as' => 'role.add' , 'uses' => 'RoleApiController@create' ) );
    Route::get( '/edit/{id}' , array( 'as' => 'role.edit' , 'uses' => 'RoleApiController@edit' ) );
} );

Route::group( [ 'prefix' => 'api', 'middleware' => ['auth' , 'pageLevel'] ] , function () {
    Route::get('/pager/{type}', [ 'as' => 'pager', 'uses' =>'PagingController@paginate']);
    Route::resource( 'espapi' , 'EspApiController' , [ 'except' => [ 'create' , 'edit' ] ,'middleware' => ['auth']  ] );
    Route::resource( 'client' , 'ClientController' , [ 'except' => [ 'create' , 'edit' , 'pager' ] ,'middleware' => ['auth'] ] );
    Route::resource('user', 'UserApiController',  [ 'except' => [ 'create' , 'edit' ] ,'middleware' => ['auth','admin']] );
    Route::resource('role', 'RoleApiController',  [ 'except' => [ 'create' , 'edit' ] ,'middleware' => ['auth','admin']] );
    Route::resource('jobEntry', 'JobApiController',  [ 'only' => [ 'index' ] ,'middleware' => ['auth','dev']] );
    Route::resource( 'showinfo' , 'ShowInfoController' , [ 'only' => [ 'show' , 'store' ] , 'middleware' => [ 'auth' ] ] );

} );

//guest only
Route::group(['middleware' => ['guest']], function () {
    Route::get('login', ['as' => 'login', 'uses' => 'SessionsController@create']);
    Route::get('forgot_password', ['as' => 'forget.getemail', 'uses' => 'PasswordController@getEmail']);
    Route::post('forgot_password',['as' => 'forget.postemail', 'uses' => 'PasswordController@postEmail']);
    Route::get('reset_password/{token}', ['as' => 'password.reset', 'uses' => 'PasswordController@getReset']);
    Route::post('reset_password/{token}',['as' => 'password.store', 'uses' => 'PasswordController@postReset']);
});


Route::resource('sessions', 'SessionsController' , ['only' => ['create','store','destroy']]);
Route::get('home', ['as' => 'home', 'uses' => 'HomeController@home']);
Route::get('logout', ['as' => 'logout', 'uses' => 'SessionsController@destroy']);

Route::group( [ 'prefix' => 'api/mt1', 'middleware' => ['auth'] ] , function () {
    Route::resource('suppressionReason', 'MT1API\SuppressionReasonController',  [ 'only' => [ 'index' ]] );
    Route::resource('clientstatsgrouping', 'MT1API\ClientStatsGroupingController',  [ 'only' => [ 'index' ]] );
    //Route::resource( 'client' , 'MT1API\ClientApiController' , [ 'only' => [ ] ] ); Will be needed later
    Route::resource( 'clientgroup' , 'MT1API\ClientGroupApiController' , [ 'only' => [ 'index','show'] ] );
    Route::get( 'client/generatelinks/{id}' , array( 'as' => 'api.mt1.client.generatelinks' , 'uses' => 'ClientController@generatelinks' ) );
    Route::get( 'client/types' , array( 'as' => 'api.mt1.client.types' , 'uses' => 'MT1API\ClientApiController@types' ) );
});


Route::get('wizard/test', ['as' => 'logout', 'uses' => 'WizardController@test']);
Route::get('woo', ['as' => 'logout', 'uses' => 'WizardController@woo']);