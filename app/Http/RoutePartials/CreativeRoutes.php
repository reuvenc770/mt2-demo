<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'creatives' ,
        'middleware' => [ 'auth'  ]
    ] ,
    function () {
        Route::get( '/preview/{offerId}' , [
            'as' => 'creatives.preview' ,
            'uses' => 'CreativeFromSubjectController@previewCreative'
        ] );
    }
);

/**
 * API Routes
 */
Route::group(
    [   
        'prefix' => 'api/cfs' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get('/creatives/{id}', [
            'as' => 'api.cfs.creatives',
            'uses' => 'CreativeFromSubjectController@getCreatives'
        ]);

        Route::get('/froms/{id}', [
            'as' => 'api.cfs.froms',
            'uses' => 'CreativeFromSubjectController@getFroms'
        ]);

        Route::get('/subjects/{id}', [
            'as' => 'api.cfs.subjects',
            'uses' => 'CreativeFromSubjectController@getSubjects'
        ]);
    }
);
