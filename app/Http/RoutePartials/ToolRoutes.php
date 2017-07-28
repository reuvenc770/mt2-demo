<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'tools' ,
        'middleware' => [ 'auth','pageLevel' ]
    ] ,
    function () {

        Route::get( '/tools' , [
            'as' => 'tools.list' ,
            'uses' => 'HomeController@redirectTools'
        ] );

        Route::get( '/show-info' , [
            'as' => 'tools.recordlookup' ,
            'uses' => 'ShowInfoController@index'
        ] );

        Route::get( '/notifications' , [
            'as' => 'tools.notifications' ,
            'uses' => 'ScheduledNotificationController@index'
        ] );

        Route::get( '/affiliates' , [
            'as' => 'tools.affiliates' ,
            'uses' => 'CakeAffiliateController@index'
        ] );

        Route::get( '/seed' , [
            'as' => 'tools.seed' ,
            'uses' => 'SeedEmailController@index'
        ] );

        Route::get( '/bulk-suppression' , [
            'as' => 'tools.bulksuppression' ,
            'uses' => 'BulkSuppressionController@index'
        ] );

        Route::get( '/appendeid' , [
            'as' => 'tools.appendeid' ,
            'uses' => 'AppendEidController@index'
        ] );

        Route::get( '/navigation' , [
            'as' => 'tools.navigation' ,
            'uses' => 'NavigationController@index'
        ] );

        Route::get( '/cacheclear' , [
            'as' => 'tools.cache' ,
            'uses' => 'CacheController@clearCacheTag'
        ] );

        Route::get( '/source-url-search' , [
            'as' => 'tools.sourceurlsearch' ,
            'uses' => 'SourceUrlSearchController@index'
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
            'api/tools/seed' ,
            'SeedEmailController' ,
            [ 'only' => [ 'store' , 'destroy' ] ]
        );

        Route::resource(
            'api/showinfo' ,
            'ShowInfoController' ,
            [ 'only' => [ 'show' , 'store' ] ]
        );

        Route::resource(
            'api/bulksuppression' ,
            'BulkSuppressionController' ,
            [ 'only' => [ 'store' ] ]
        );

        Route::resource(
            'api/suppressionReason' ,
            'SuppressionReasonController',
            [ 'only' => [ 'index' ] ]
        );
    }
);

Route::group(
    [   
        'prefix' => 'api/appendeid' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::post( '/upload/' , [
            'as' => 'tools.appendeid.upload' ,
            'uses' => 'AppendEidController@manageUpload'
        ] );
    }
);

Route::group(
    [   
        'prefix' => 'api/bulksuppression' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::post('/send', [
            'as' => 'bulksuppression.update',
            'middleware' => 'auth',
            'uses' => 'BulkSuppressionController@update'
        ]);

        Route::post('/transfer', [
            'as' => 'bulksuppression.transfer',
            'middleware' => 'auth',
            'uses' => 'BulkSuppressionController@store'
        ]);
    }
);
