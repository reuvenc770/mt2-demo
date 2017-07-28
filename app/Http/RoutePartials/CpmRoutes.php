<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'cpm' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'cpm.list' ,
            'uses' => 'CpmPricingController@listAll'
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
            'api/cpm',
            'CpmPricingController',
            [ 'except' => [ 'index' , 'create', 'edit', 'show' , 'destroy']]
        );
    }
);
