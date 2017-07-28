<?php
/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'esp',
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'esp.list' ,
            'uses' => 'EspController@listAll'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'esp.edit' ,
            'uses' => 'EspController@edit'
        ] );

        Route::get( '/create' , [
            'as' => 'esp.add' ,
            'uses' => 'EspController@create'
        ] );
        Route::get( '/mapping/{id}' , [
            'as' => 'esp.mapping' ,
            'uses' => 'EspController@mappings'
        ] );
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
            'esp' ,
            'EspController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::get(
            'esp/mappings/{id}' ,
            [
                'as' => 'api.esp.mappings.get' ,
                'uses' => 'EspController@getMapping'
            ]
        );
        Route::put(
            'esp/mappings/{id}' ,
            [
                'as' => 'api.esp.mappings.update' ,
                'uses' => 'EspController@updateMappings'
            ]
        );

        Route::post(
            'esp/mappings/process' ,
            [
                'as' => 'api.esp.mappings.process' ,
                'uses' => 'EspController@processCSV'
            ]
        );
    }
);
