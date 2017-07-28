<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'dba' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'dba.list' ,
            'uses' => 'DoingBusinessAsController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'dba.add' ,
            'uses' => 'DoingBusinessAsController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'dba.edit' ,
            'uses' => 'DoingBusinessAsController@edit'
        ] );
    }
);

/**
 * API Routes
 */
Route::group(
    [ 'middleware' => [ 'auth' , 'pageLevel' ] ] ,
    function () {
        Route::resource(
            'api/dba',
            'DoingBusinessAsController',
            [ 'except' => ['create', 'edit']]
        );

        Route::get( '/api/dba/toggle/{id}' , [
            'as' => 'api.dba.toggle',
            'uses' => 'DoingBusinessAsController@toggle'
        ] );
    }
);
