<?php
/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'espapi',
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'espapi.list' ,
            'uses' => 'EspApiAccountController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'espapi.add' ,
            'uses' => 'EspApiAccountController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'espapi.edit' ,
            'uses' => 'EspApiAccountController@edit'
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
            'api/espapi' ,
            'EspApiAccountController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [   
        'prefix' => 'api/espapi' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/espAccounts/{name}' , [
            'as' => 'api.espapi.GetAll' ,
            'uses' => 'EspApiAccountController@displayEspAccounts'
        ] );

        Route::get( '/generatecustomid' , [
            'as' => 'api.espapi.generatecustomid' ,
            'uses' => 'EspApiAccountController@generateCustomId'
        ] );

        Route::post( '/toggleStats/{id}' , [
            'as' => 'api.espapi.toggleStats' ,
            'uses' => 'EspApiAccountController@toggleStats'
        ] );

        Route::post( '/toggleSuppression/{id}' , [
            'as' => 'api.espapi.toggleSuppression' ,
            'uses' => 'EspApiAccountController@toggleSuppression'
        ] );

        Route::post( '/activate/{id}' , [
            'as' => 'api.espapi.activate' ,
            'uses' => 'EspApiAccountController@activate'
        ] );

        Route::post( '/deactivate/{id}' , [
            'as' => 'api.espapi.deactivate' ,
            'uses' => 'EspApiAccountController@deactivate'
        ] );

        Route::get( '/all' , [
            'as' => 'api.espapi.returnAll' ,
            'uses' => 'EspApiAccountController@returnAll'
        ] );

        Route::get( '/allactive' , [
            'as' => 'api.espapi.returnallactive' ,
            'uses' => 'EspApiAccountController@returnAllActive'
        ] );
    }
);
