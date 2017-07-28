<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'isp' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'isp.list' ,
            'uses' => 'EmailDomainController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'isp.add' ,
            'uses' => 'EmailDomainController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'isp.edit' ,
            'uses' => 'EmailDomainController@edit'
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
            'api/isp',
            'EmailDomainController',
            [ 'except' => [ 'index' , 'create' , 'edit' ] ]
        );

        Route::resource(
            'api/isp' ,
            'IspController' ,
            [ 'only' => [ 'index' ] ]
        );
    }
);
