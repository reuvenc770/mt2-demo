<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'domain' ,
        'middleware' => [ 'auth'  , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'domain.list' ,
            'uses' => 'DomainController@listAll'
        ] );
        Route::get( '/listview' , [
            'as' => 'domain.listview' ,
            'uses' => 'DomainController@listView'
        ] );

        Route::get( '/search' , [
            'as' => 'domain.search' ,
            'uses' => 'DomainController@searchDomains'
        ] );

        Route::get( '/create' , [
            'as' => 'domain.add' ,
            'uses' => 'DomainController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'domain.edit' ,
            'uses' => 'DomainController@edit'
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
            'api/domain',
            'DomainController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [   
        'prefix' => 'api/domain' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/listDomains/{type}/{espAccountId}' , [
            'as' => 'api.domain.listDomains' ,
            'uses' => 'DomainController@getDomainsByTypeAndESP'
        ] );

        Route::get( '/listActiveDomains/{type}/{espAccountId}' , [
            'as' => 'api.domain.listDomains' ,
            'uses' => 'DomainController@getActiveDomainsByTypeAndESP'
        ] );
    }
);
