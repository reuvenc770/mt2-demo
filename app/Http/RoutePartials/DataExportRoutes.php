<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'dataexport',
        'middleware' => ['auth', 'pageLevel']
    ],
    function () {
        Route::get( '/' ,
            array(
                'as' => 'dataexport.list' ,
                'uses' => 'DataExportController@listActive'
            )
        );

        Route::get(
            '/create',
            array(
                'as' => 'dataexport.add',
                'uses' => 'DataExportController@create'
            )
        );

        Route::get(
            '/edit/{id}',
            array(
                'as' => 'dataexport.edit',
                'uses' => 'DataExportController@edit'
            )
        );
    }
);

/**
 * API Routes
 */
Route::group(
    [
        'prefix' => 'api' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::resource(
            'dataexport',
            'DataExportController',
            [   
                'except' => ['create', 'edit'],
                'middleware' => ['auth']
            ]
        );

        Route::put('/dataexport/update', [
            'as' => 'dataexport.update',
            'middleware' => ['auth'],
            'uses' => 'DataExportController@message'
        ]);
    }
);
