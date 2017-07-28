<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'client' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'client.list' ,
            'uses' => 'ClientController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'client.add' ,
            'uses' => 'ClientController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'client.edit' ,
            'uses' => 'ClientController@edit'
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
            'api/client' ,
            'ClientController' ,
            [ 'only' => [ 'store' , 'update' , 'destroy' , 'show' ] ]
        );
    }
);
