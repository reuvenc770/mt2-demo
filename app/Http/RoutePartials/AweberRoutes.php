<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * AWebber has been turned off because we lost the accounts. Ken will get them back in August.
 */

/**
 * UI Routes
 */
/*
Route::get( '/tools/awebermapping' , [
    'as' => 'tools.awebermapping' ,
    'uses' => 'AWeberDeployMappingController@mapDeploys'
] );

Route::get( '/tools/aweberlists' , [
    'as' => 'tools.aweberlists' ,
    'uses' => 'AWeberListController@edit'
] );
*/

/**
 * API Routes
 */
/*
Route::group(
    [   
        'prefix' => 'api' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get('/tools/getunmappedreports', [
            'as' => 'api.tools.awebermapping.unmapped' ,
            'uses' => 'AWeberDeployMappingController@getOrphanReports'
        ] );

        Route::post('/tools/convertreport', [
            'as' => 'api.tools.awebermapping.convertreport' ,
            'uses' => 'AWeberDeployMappingController@convertReport'
        ] );

        Route::post('/tools/aweberlists/update', [
            'as' => 'api.tools.aweberlists.update' ,
            'uses' => 'AWeberListController@store'
        ] );

        Route::get('/tools/getaweberlists/{id}', [
            'as' => 'api.tools.aweberlists.getLists' ,
            'uses' => 'AWeberListController@getList'
        ] );
    }
);
*/
