<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'ispgroup' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'ispgroup.list' ,
            'uses' => 'DomainGroupController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'ispgroup.add' ,
            'uses' => 'DomainGroupController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'ispgroup.edit' ,
            'uses' => 'DomainGroupController@edit'
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
            'api/ispgroup',
            'DomainGroupController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);
