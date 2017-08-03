<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

Route::group(
    [
        'prefix' => 'api/mt1' ,
        'middleware' => [ 'auth' ]
    ] ,
    function () {
        Route::get(
            'clientgroup/clients/{id}' ,
            [
                'as' => 'api.mt1.clientgroup.clients' ,
                'uses' => 'MT1API\ClientGroupApiController@clients'
            ]
        );

        Route::get(
            'client/generatelinks/{id}' ,
            [
                'as' => 'api.mt1.client.generatelinks' ,
                'uses' => 'FeedController@generatelinks'
            ]
        );

        Route::get(
            'client/types' ,
            [
                'as' => 'api.mt1.client.types' ,
                'uses' => 'MT1API\ClientApiController@types'
            ]
        );

        Route::get(
            'advertiser' ,
            [
                'as' => 'api.mt1.advertiser.get' ,
                'uses' => 'MT1API\AdvertiserController@index'
            ]
        );

        Route::get(
            'country' ,
            [
                'as' => 'api.mt1.country.get' ,
                'uses' => 'MT1API\CountryController@index'
            ]
        );

        Route::get(
            'offercategory' ,
            [
                'as' => 'api.mt1.offercategory.get' ,
                'uses' => 'MT1API\OfferCategoryController@index'
            ]
        );

        /**
         * RESTful Resources
         */
        Route::resource(
            'clientstatsgrouping' ,
            'MT1API\ClientStatsGroupingController' ,
            [ 'only' => [ 'index' ] ]
        );

        Route::resource(
            'clientgroup' ,
            'MT1API\ClientGroupApiController' ,
            [ 'only' => [ 'index' , 'show' ] ]
        );

        Route::resource(
            'uniqueprofiles' ,
            'MT1API\UniqueProfileApiController' ,
            [ 'only' => [ 'index' , 'show' ] ]
        );

        Route::resource(
            'esps',
            'MT1API\EspApiController',
            ['only' => ['index', 'show']]
        );
    }
);
