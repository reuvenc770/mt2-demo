<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'feed' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'feed.list' ,
            'uses' => 'FeedController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'feed.add' ,
            'uses' => 'FeedController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'feed.edit' ,
            'uses' => 'FeedController@edit'
        ] );

        Route::get( '/file/fieldorder/{id}' , [
            'as' => 'feed.file.fieldorder' ,
            'uses' => 'FeedController@viewFieldOrder'
        ] );
    }
);

/**
 * Realtime Record Post Route
 */
Route::any( '/api/post_data' , [
    'as' => 'api.feed.realtimerecords' ,
    'uses' => 'FeedApiController@ingest'
] );
