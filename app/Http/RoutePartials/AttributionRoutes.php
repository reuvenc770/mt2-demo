<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'attribution' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' ,
            [
                'as' => 'attribution.list' ,
                'uses' => 'AttributionController@listAll'
            ]
        );

        Route::get(
            '/create',
            array(
                'as' => 'attributionModel.add',
                'uses' => 'AttributionController@create'
            )
        );

        Route::get(
            '/edit/{modelId}',
            array(
                'as' => 'attributionModel.edit',
                'uses' => 'AttributionController@edit'
            )
        );

        Route::get(
            '/projection/',
            array(
                'as' => 'attributionProjection.show',
                'uses' => 'AttributionController@showProjection'
            )
        );
    }
);

/**
 * API Routes
 */

Route::group(
    [   
        'prefix' => 'api/attribution' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/model' , [
            'as' => 'api.attribution.model.index' ,
            'uses' => 'AttributionController@index'
        ] );

        Route::post( '/model' , [
            'as' => 'api.attribution.model.store' ,
            'uses' => 'AttributionController@store'
        ] );

        Route::put( '/model/{modelId}' , [
            'as' => 'api.attribution.model.update' ,
            'uses' => 'AttributionController@update'
        ] );

        Route::delete( '/model/{modelId}/{feedId}' , [
            'as' => 'api.attribution.model.destroy' ,
            'uses' => 'AttributionController@destroy'
        ] );

        Route::get( '/model/{modelId}' , [
            'as' => 'api.attribution.model.show' ,
            'uses' => 'AttributionController@show'
        ] );

        Route::get( '/model/{modelId}/levels' , [
            'as' => 'api.attribution.model.levels' ,
            'uses' => 'AttributionController@levels'
        ] );

        Route::get( '/model/{modelId}/feeds' , [
            'as' => 'api.attribution.model.clients' ,
            'uses' => 'AttributionController@getModelFeeds'
        ] );

        Route::post( '/model/copyLevels' , [
            'as' => 'api.attribution.model.copyLevels' ,
            'uses' => 'AttributionController@copyLevels'
        ] );

        Route::get( '/model/setlive/{modelId}' , [
            'as' => 'api.attribution.model.setlive' ,
            'uses' => 'AttributionController@setModelLive'
        ] );

        Route::post( '/model/run' , [
            'as' => 'api.attribution.run' ,
            'uses' => 'AttributionController@runAttribution'
        ] );

        Route::post( '/projection/report' , [
            'as' => 'api.attribution.projection.report' ,
            'uses' => 'AttributionController@getReportData'
        ] );

        Route::get( '/projection/chart/{modelId}' , [
            'as' => 'api.attribution.projection.chart' ,
            'uses' => 'AttributionController@getChartData'
        ] );

        Route::get( '/syncLevels' , [
            'as' => 'api.attribution.model.syncLevels' ,
            'uses' => 'AttributionController@syncLevelsWithMT1'
        ] );

        Route::post( '/quickReorder/{modelId}' , [
            'as' => 'api.attribution.quickReorder' ,
            'uses' => 'AttributionController@quickReorder'
        ] );
    }
);
