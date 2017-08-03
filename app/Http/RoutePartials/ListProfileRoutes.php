<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'listprofile' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'listprofile.list' ,
            'uses' => 'ListProfileController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'listprofile.add' ,
            'uses' => 'ListProfileController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'listprofile.edit' ,
            'uses' => 'ListProfileController@edit'
        ] );

        Route::get( '/combine/edit/{id}' , [
            'as' => 'listprofile.combine.edit' ,
            'uses' => 'ListProfileController@editListCombine'
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
            'listprofile' ,
            'ListProfileController' ,
            [ 'except' => [ 'create' , 'edit' , 'copy' ] ]
        );
    }
);

Route::group(
    [   
        'prefix' => 'api/listprofile' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::post( '/copy' , [
            'as' => 'api.listprofile.copy' ,
            'uses' => 'ListProfileController@copy'
        ] );

        Route::get( '/isps/{id}' , [
            'as' => 'api.listprofile.isps' ,
            'uses' => 'ListProfileController@isps'
        ] );

        Route::get( '/sources/{id}' , [
            'as' => 'api.listprofile.sources' ,
            'uses' => 'ListProfileController@sources'
        ] );

        Route::get( '/seeds/{id}' , [
            'as' => 'api.listprofile.seeds' ,
            'uses' => 'ListProfileController@seeds'
        ] );

        Route::get( '/zips/{id}' , [
            'as' => 'api.listprofile.zips' ,
            'uses' => 'ListProfileController@zips'
        ] );

        Route::get( '/active' , [
            'as' => 'api.listprofile.active' ,
            'uses' => 'ListProfileController@listActive'
        ] );

        Route::get( '/listcombine' , [
            'as' => 'api.listprofile.combine' ,
            'uses' => 'ListProfileController@getCombines'
        ] );

        Route::get( '/listcombine/combineonly' , [
            'as' => 'api.listprofile.combinelist' ,
            'uses' => 'ListProfileController@getListCombinesOnly'
        ] );

        Route::get( '/listcombine/firstparty' , [
            'as' => 'api.listprofile.firstpartylist' ,
            'uses' => 'ListProfileController@getFirstPartyListCombines'
        ] );

        Route::post( '/listcombine/create' , [
            'as' => 'api.listprofile.combine.create' ,
            'uses' => 'ListProfileController@createListCombine',
        ] );

        Route::post( '/listcombine/export' , [
            'as' => 'api.listprofile.combine.export' ,
            'uses' => 'ListProfileController@exportListCombine',
        ] );

        Route::put( '/listcombine' , [
            'as' => 'api.listprofile.combine.update' ,
            'uses' => 'ListProfileController@updateListCombine'
        ] );
    }
);
