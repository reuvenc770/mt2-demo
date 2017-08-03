<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'proxy' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'proxy.list' ,
            'uses' => 'ProxyController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'proxy.add' ,
            'uses' => 'ProxyController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'proxy.edit' ,
            'uses' => 'ProxyController@edit'
        ] );
    }
);

/**
 * API Routes
 */
Route::group(
    [
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::resource(
            'api/proxy',
            'ProxyController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [
        'prefix' => 'api/proxy' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get('/toggle/{id}', [
            'as' => 'api.proxy.toggle',
            'uses' => 'ProxyController@toggle'
        ]);

        Route::get('/active', [
            'as' => 'api.proxy.list',
            'uses' => 'ProxyController@listAllActive'
        ]);
    }
);
