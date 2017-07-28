<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'datacleanse' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'datacleanse.list' ,
            'uses' => 'DataCleanseController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'datacleanse.add' ,
            'uses' => 'DataCleanseController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'datacleanse.edit' ,
            'uses' => 'DataCleanseController@edit'
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
            'api/datacleanse' ,
            'DataCleanseController' ,
            [ 'only' => [ 'index' , 'store' ] ]
        );
    }
);
