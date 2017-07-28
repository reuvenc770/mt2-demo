<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'ymlp' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/ymlp-campaign' , [
            'as' => 'ymlpcampaign.list' ,
            'uses' => 'YmlpCampaignController@listAll'
        ] );

        Route::get( '/ymlp-campaign/edit/{id}' , [
            'as' => 'ymlpcampaign.edit' ,
            'uses' => 'YmlpCampaignController@edit'
        ] );

        Route::get( '/ymlp-campaign/create' , [
            'as' => 'ymlpcampaign.add' ,
            'uses' => 'YmlpCampaignController@create'
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
            'api/ymlp-campaign' ,
            'YmlpCampaignController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);
