<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
Route::group(
    [
        'prefix' => 'deploy' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get('/', [
            'as' => 'deploy.list',
            'uses' => 'DeployController@listAll'
        ]);

        Route::get( '/preview/{deployId}' , [
            'as' => 'deploy.preview' ,
            'uses' => 'DeployController@previewDeploy'
        ] );


        Route::get( '/downloadhtml/{deployId}' , [
            'as' => 'deploy.downloadhtml' ,
            'uses' => 'DeployController@downloadHtml'
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
            'api/deploy',
            'DeployController',
            [ 'except' => [ 'index' , 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [
        'prefix' => 'api/deploy' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::post( '/copytofuture' , [
            'as' => 'api.deploy.copytofuture' ,
            'uses' => 'DeployController@copyToFuture'
        ] );

        Route::get( '/cakeaffiliates' , [
            'as' => 'api.deploy.cakeaffiliates' ,
            'uses' => 'DeployController@returnCakeAffiliates'
        ] );

        Route::post( '/validatedeploys' , [
            'as' => 'api.deploy.validateDeploys' ,
            'uses' => 'DeployController@validateMassUpload'
        ] );

        Route::post( '/massupload' , [
            'as' => 'api.deploy.massupload' ,
            'uses' => 'DeployController@massupload'
        ] );

        Route::get( '/check' , [
            'as' => 'api.deploy.checkProgress' ,
            'uses' => 'DeployController@checkProgress'
        ] );

        Route::post( '/package/create' , [
            'as' => 'api.deploy.deploypackages' ,
            'uses' => 'DeployController@deployPackages'
        ] );

        Route::get( '/exportcsv' , [
            'as' => 'api.deploy.exportcsv' ,
            'uses' => 'DeployController@exportCsv'
        ] );
    }
);
