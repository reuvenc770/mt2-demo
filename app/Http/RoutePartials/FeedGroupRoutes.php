<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'feedgroup' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'feedgroup.list' ,
            'uses' => 'FeedGroupController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'feedgroup.add' ,
            'uses' => 'FeedGroupController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'feedgroup.edit' ,
            'uses' => 'FeedGroupController@edit'
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
            'api/feedgroup' ,
            'FeedGroupController' ,
            [ 'except' => [ 'index' , 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [
        'prefix' => 'api' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::resource(
            'feed' ,
            'FeedController' ,
            [ 'except' => [ 'create' , 'edit' , 'pager' ] ]
        );

        Route::get('/feed/updatepassword/{username}', [
            'as' => 'api.feed.updatepassword' ,
            'uses' => 'FeedController@resetPassword'
        ] );

        Route::put( '/feed/file/{id}' , [
            'as' => 'api.feed.file.savefieldorder' ,
            'uses' => 'FeedController@storeFieldOrder'
        ] );

        Route::put( '/feed/runreattribution/{id}' , [
            'as' => 'api.feed.reattribution.run' ,
            'uses' => 'FeedController@runReattribution'
        ] );

        Route::post( '/feed/createsuppression/{id}' , [
            'as' => 'api.feed.suppression.create' ,
            'uses' => 'FeedController@createSuppression'
        ] );

        Route::post( '/feed/searchsource' , [
            'as' => 'api.feed.searchsource' ,
            'uses' => 'FeedController@searchSource'
        ] );

        Route::get( '/feed/exportList' , [
            'as' => 'api.feed.exportlist' ,
            'uses' => 'FeedController@exportList'
        ] );
    }
);
