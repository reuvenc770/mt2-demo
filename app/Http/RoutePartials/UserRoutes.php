<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'user' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'user.list' ,
            'uses' => 'UserApiController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'user.add' ,
            'uses' => 'UserApiController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'user.edit' ,
            'uses' => 'UserApiController@edit'
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
            'api/user',
            'UserApiController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);
