<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'registrar' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'registrar.list' ,
            'uses' => 'RegistrarController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'registrar.add' ,
            'uses' => 'RegistrarController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'registrar.edit' ,
            'uses' => 'RegistrarController@edit'
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
            'api/registrar',
            'RegistrarController',
            [ 'except' => ['create', 'edit']]
        );

        Route::get('/api/registrar/toggle/{id}', [
            'as' => 'api.registar.toggle',
            'uses' => 'RegistrarController@toggle'
        ]);
    }
);
