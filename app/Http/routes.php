<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Default Routes
 */
Route::get('/', [ 'as' => 'root' , 'uses' => function () {
    return redirect("/login");
} ] );


/**
 * Guest Routes
 */
Route::group(
    [ 'middleware' => [ 'guest' ] ] ,
    function () {
        Route::get( 'login', [
            'as' => 'login' ,
            'uses' => 'SessionsController@create'
        ] );

        Route::get( 'forgot_password' , [
            'as' => 'forget.getemail' ,
            'uses' => 'PasswordController@getEmail'
        ] );

        Route::post( 'forgot_password' , [
            'as' => 'forget.postemail' ,
            'uses' => 'PasswordController@postEmail'
        ] );

        Route::get( 'reset_password/{token}' , [
            'as' => 'password.reset' ,
            'uses' => 'PasswordController@getReset'
        ] );

        Route::post( 'reset_password/{token}' , [
            'as' => 'password.store' ,
            'uses' => 'PasswordController@postReset'
        ] );
    }
);


/**
 * System/UI Routes
 */
Route::group( [] , function() {
    Route::resource(
        'sessions' ,
        'SessionsController' ,
        [ 'only' => [ 'create' , 'store' , 'destroy' ] ]
    );

    Route::get( 'home' , [
        'as' => 'home' ,
        'uses' => 'HomeController@home'
    ] );

    Route::get( 'logout' , [
        'as' => 'logout' ,
        'uses' => 'SessionsController@destroy'
    ] );

    Route::get( 'myprofile' , [
        'as' => 'myprofile' ,
        'uses' => 'UserApiController@myProfile'
    ] );
} );


/**
 * ESP API Account Routes
 */
Route::group(
    [
        'prefix' => 'espapi',
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'espapi.list' ,
            'uses' => 'EspApiController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'espapi.add' ,
            'uses' => 'EspApiController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'espapi.edit' ,
            'uses' => 'EspApiController@edit'
        ] );
    }
);


/**
 * MT2 Tool Routes
 */
Route::group(
    [
        'prefix' => 'tools' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/tools' , [ 'as' => 'tools.list' , 'uses' => function () { return redirect()->route( 'tools.recordlookup' ); } ] );

        Route::get( '/show-info' , [
            'as' => 'tools.recordlookup' ,
            'uses' => 'ShowInfoController@index'
        ] );
        Route::get( '/bulk-suppression' , [
            'as' => 'tools.bulksuppression' ,
            'uses' => 'BulkSuppressionController@index'
        ] );

    }
);


/**
* YMLP Manager Routes
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
 * Dev Tool Routes
 */
Route::group(
    [
        'prefix' => 'devtools' ,
        'middleware' => [ 'auth' , 'dev'  ]
    ] ,
    function () {
        Route::get( '/jobs' , [
            'as' => 'devtools.jobs' ,
            'uses' => 'JobApiController@listALL'
        ] );
    }
);

/** DBA Routes */
Route::group(
    [
        'prefix' => 'dba' ,
        'middleware' => [ 'auth' , 'admin' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'dba.list' ,
            'uses' => 'DoingBusinessAsController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'dba.add' ,
            'uses' => 'DoingBusinessAsController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'dba.edit' ,
            'uses' => 'DoingBusinessAsController@edit'
        ] );
    }
);
/** Proxy Routes */
Route::group(
    [
        'prefix' => 'proxy' ,
        'middleware' => [ 'auth' , 'admin' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'proxy.list' ,
            'uses' => 'ProxyController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'proxy.add' ,
            'uses' => 'ProxyController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'proxy.edit' ,
            'uses' => 'ProxyController@edit'
        ] );
    }
);
/** Registrar */
Route::group(
    [
        'prefix' => 'registrar' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'registrar.list' ,
            'uses' => 'RegistrarController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'registrar.add' ,
            'uses' => 'RegistrarController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'registrar.edit' ,
            'uses' => 'RegistrarController@edit'
        ] );
    }
);

/** Mailing Template */
Route::group(
    [
        'prefix' => 'mailingtemplate' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get('/', [
            'as' => 'mailingtemplate.list',
            'uses' => 'MailingTemplateController@listAll'
        ]);

        Route::get('/create', [
            'as' => 'mailingtemplate.add',
            'uses' => 'MailingTemplateController@create'
        ]);

        Route::get('/edit/{id}', [
            'as' => 'mailingtemplate.edit',
            'uses' => 'MailingTemplateController@edit'
        ]);

        Route::get('/preview/{id?}', [
            'as' => 'mailingtemplate.preview',
            'uses' => 'MailingTemplateController@preview'
        ]);
    });


/** Mailing Template */
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

    });

/**
 * User Routes
 */
Route::group(
    [
        'prefix' => 'user' ,
        'middleware' => [ 'auth' , 'admin' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'user.list' ,
            'uses' => 'UserApiController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'user.add' ,
            'uses' => 'UserApiController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'user.edit' ,
            'uses' => 'UserApiController@edit'
        ] );
    }
);


/**
 * Client Routes
 */
Route::group(
    [
        'prefix' => 'client' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'client.list' ,
            'uses' => 'ClientController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'client.add' ,
            'uses' => 'ClientController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'client.edit' ,
            'uses' => 'ClientController@edit'
        ] );

        Route::get( '/attribution' , [
            'as' => 'client.attribution' ,
            'uses' => 'AttributionController@listAll'
        ] );
    }
);


/**
 * Client Group Routes
 */
Route::group(
    [
        'prefix' => 'clientgroup' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'clientgroup.list' ,
            'uses' => 'ClientGroupController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'clientgroup.add' ,
            'uses' => 'ClientGroupController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'clientgroup.edit' ,
            'uses' => 'ClientGroupController@edit'
        ] );
    }
);


/**
 * Data Cleanse Routes
 */
Route::group(
    [
        'prefix' => 'datacleanse' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'datacleanse.list' ,
            'uses' => 'DataCleanseController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'datacleanse.add' ,
            'uses' => 'DataCleanseController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'datacleanse.edit' ,
            'uses' => 'DataCleanseController@edit'
        ] );
    }
);


/**
 * List Profile Routes
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

    }
);


/**
 * Role Routes
 */
Route::group(
    [
        'prefix' => 'role' ,
        'middleware' => [ 'auth' , 'admin' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'role.list' ,
            'uses' => 'RoleApiController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'role.add' ,
            'uses' => 'RoleApiController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'role.edit' ,
            'uses' => 'RoleApiController@edit'
        ] );
    }
);

/**
 * Domain Routes
 */
Route::group(
    [
        'prefix' => 'domain' ,
        'middleware' => [ 'auth' , 'admin' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'domain.list' ,
            'uses' => 'DomainController@listAll'
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
 *  Data Export Routes
 */

Route::group( 
    [ 
        'prefix' => 'dataexport', 
        'middleware' => ['auth', 'pageLevel'] 
    ],
    function () {
        Route::get( '/' , 
            array(
                'as' => 'dataexport.list' , 
                'uses' => 'DataExportController@listActive' 
            ) 
        );

        Route::get( 
            '/create', 
            array( 
                'as' => 'dataexport.add', 
                'uses' => 'DataExportController@create' 
            )
        );

        Route::get(
            '/edit/{id}',
            array( 
                'as' => 'dataexport.edit',
                'uses' => 'DataExportController@edit'
            )
        );
    }
);

/**
 * Attribution Model/Report Routes
 */

Route::group(
    [
        'prefix' => 'attr' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' ,
            [
                'as' => 'attr.model.list' ,
                'uses' => 'AttributionModelController@listAll'
            ]
        );

        Route::get( 
            '/create', 
            array( 
                'as' => 'attr.model.add', 
                'uses' => 'AttributionModelController@create' 
            )
        );

        Route::get( 
            '/edit/{modelId}', 
            array( 
                'as' => 'attr.model.edit', 
                'uses' => 'AttributionModelController@edit' 
            )
        );

        Route::get( 
            '/report', 
            array( 
                'as' => 'attr.report.view', 
                'uses' => 'AttributionReportController@view' 
            )
        );
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
        Route::get( '/pager/{type}' , [
            'as' => 'api.pager',
            'uses' =>'PagingController@paginate'
        ] );

        Route::put( '/profile/{id}' , [
            'as' => 'api.profile.update' ,
            'uses' => 'UserApiController@updateProfile'
        ] );

        Route::any('/attachment/upload', [
            'as' => 'api.attachment.upload' ,
            'uses' => 'AttachmentApiController@flow'
        ] );

        Route::get( '/client/attribution/list' , [
            'as' => 'api.client.attribution.list' ,
            'uses' => 'AttributionController@index'
        ] );

        Route::put('/dataexport/update', [ 
            'as' => 'dataexport.update', 
            'middleware' => ['auth'], 
            'uses' => 'DataExportController@message'
        ]);

        Route::get('/client/updatepassword/{username}', [
            'as' => 'api.client.updatepassword' ,
            'uses' => 'ClientController@resetClientPassword'
        ] );


        /**
         * Client Group API Routes
         */
        Route::group(
            [ 'prefix' => 'clientgroup' ] ,
            function () {
                Route::get( '/search' , [
                    'as' => 'api.clientgroup.search' ,
                    'uses' => 'ClientGroupController@paginateSearch'
                ] );

                Route::get( '/all' , [
                    'as' => 'api.clientgroup.all' ,
                    'uses' => 'ClientGroupController@index'
                ] );

                Route::get( '/copy/{id}' , [
                    'as' => 'api.clientgroup.copy' ,
                    'uses' => 'ClientGroupController@copy'
                ] );
            }
        );

        /**
         * List Profile API Routes
         */
        Route::group(
            [ 'prefix' => 'listprofile' ] ,
            function () {
                Route::get( '/copy' , [
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
            }
        );

        /**
         * Proxies additional routes
         */
        Route::group(
            [ 'prefix' => 'proxy' ] ,
            function () {
                Route::get( '/proxiesbytype/{type}' , [
                    'as' => 'api.proxy.listType' ,
                    'uses' => 'ProxyController@returnProxiesByType'
                ] );
            }
        );
        /**Domain Routes**/
        Route::group(
            [ 'prefix' => 'domain' ] ,
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
        Route::group(
            [ 'prefix' => 'deploy' ] ,
            function () {
                Route::get( '/cakeaffiliates' , [
                    'as' => 'api.deploy.cakeaffiliates' ,
                    'uses' => 'DeployController@returnCakeAffiliates'
                ] );
            }
        );

        /**
         * Offer Routes
         */
        Route::group(
            [ 'prefix' => 'offer' ] ,
            function () {
                Route::get( '/search' , [
                    'as' => 'api.offer.search' ,
                    'uses' => 'OfferController@typeAheadSearch'
                ] );
            }
        );


        /**
         *  Bulk Suppression API Routes
         */
        Route::group(
            ['prefix' => 'bulksuppression'],
            function() {

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

        /**
         *  Attribution API Routes
         */
        Route::group(
            [] ,
            function () {
                Route::get( '/attribution/report' , [
                    'as' => 'api.attribution.report' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionReportController@getRecords'
                ] );

                Route::get( '/attribution/model' , [
                    'as' => 'api.attribution.model.index' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@index'
                ] ); 

                Route::post( '/attribution/model' , [
                    'as' => 'api.attribution.model.store' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@store'
                ] ); 

                Route::put( '/attribution/model/{modelId}' , [
                    'as' => 'api.attribution.model.update' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@update'
                ] ); 

                Route::delete( '/attribution/model/{modelId}' , [
                    'as' => 'api.attribution.model.destroy' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@destroy'
                ] ); 

                Route::get( '/attribution/model/{modelId}' , [
                    'as' => 'api.attribution.model.show' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@show'
                ] ); 

                Route::get( '/attribution/model/{modelId}/levels' , [
                    'as' => 'api.attribution.model.levels' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@levels'
                ] );

                Route::get( '/attribution/model/{modelId}/clients' , [
                    'as' => 'api.attribution.model.clients' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@getModelClients'
                ] );

                Route::post( '/attribution/model/copyLevels' , [
                    'as' => 'api.attribution.model.copyLevels' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionModelController@copyLevels'
                ] );
            }
        );

        Route::get( '/espapi/espAccounts/{name}' , [
            'as' => 'api.espapi.GetAll' ,
            'uses' => 'EspApiController@displayEspAccounts'
        ] );
        /**
         * API Resources
         */
        Route::get(
            'espapi/all' ,
            [
                'as' => 'api.espapi.returnAll' ,
                'uses' => 'EspApiController@returnAll'
            ]
        );
        Route::resource(
            'espapi' ,
            'EspApiController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );


        Route::resource(
            'ymlp-campaign' ,
            'YmlpCampaignController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'client' ,
            'ClientController' ,
            [ 'except' => [ 'create' , 'edit' , 'pager' ] ]
        );

        Route::resource(
            'clientgroup' ,
            'ClientGroupController' ,
            [ 'except' => [ 'index' , 'create' , 'edit' , 'copy' ] ]
        );

        Route::resource(
            'user',
            'UserApiController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'deploy',
            'DeployController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );


        Route::resource(
            'domain',
            'DomainController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );
        
        Route::resource(
            'datacleanse' ,
            'DataCleanseController' ,
            [ 'only' => [ 'index' , 'store' ] ]
        );

        Route::resource(
            'listprofile' ,
            'ListProfileController' ,
            [ 'except' => [ 'create' , 'edit' , 'copy' ] ]
        );

        Route::resource(
            'showinfo' ,
            'ShowInfoController' ,
            [ 'only' => [ 'show' , 'store' ] ]
        );
        
        Route::resource(
            'attribution' ,
            'AttributionController' ,
            [ 'only' => [ 'store' ] ]
        );

        Route::resource(
            'bulksuppression' ,
            'BulkSuppressionController' ,
            [ 'only' => [ 'store' ] ]
        );

        Route::resource(
            'suppressionReason' ,
            'SuppressionReasonController',
            [ 'only' => [ 'index' ] ]
        );

        Route::post(
            'attribution/bulk' ,
            [
                'as' => 'api.attribution.bulk' ,
                'uses' => 'AttributionController@bulk'
            ]
	    );

	    Route::resource(
            'dataexport', 
            'DataExportController', 
            [
                'except' => ['create', 'edit'], 
                'middleware' =>['auth']
            ]
            );

        Route::resource(
            'isp' ,
            'IspController' ,
            [ 'only' => [ 'index' ] ]
        );

        Route::resource(
            'proxy',
            'ProxyController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'registrar',
            'RegistrarController',
            [ 'except' => ['create', 'edit']]
        );

        Route::resource(
            'dba',
            'DoingBusinessAsController',
            [ 'except' => ['create', 'edit']]
        );

        Route::get('/mailingtemplate/templates/{id}', [
            'as' => 'api.mailingtemplate.listbyesp',
            'uses' => 'EspApiController@grabTemplatesByESP'
        ]);
        Route::resource(
            'mailingtemplate',
            'MailingTemplateController',
            [ 'except' => ['create', 'edit']]
        );

        /**
         * Admin Level API Group
         */
        Route::group( [ 'middleware' => 'admin' ] , function () {
            Route::get( '/role/permissions/' , [
                'as' => 'api.role.permissions' ,
                'uses' => 'RoleApiController@permissions'
            ] );

            Route::get( '/role/permissionTree/{id}' , [
                'as' => 'api.role.permissions.tree' ,
                'uses' => 'RoleApiController@getPermissionTree'
            ] );

            Route::resource(
                'role' ,
                'RoleApiController',
                [ 'except' => [ 'create' , 'edit' ] ]
            );
        } );

        /**
         * Dev Level API Group
         */
        Route::group( [ 'middleware' => 'dev' ] , function () {
            Route::resource(
                'jobEntry' ,
                'JobApiController' ,
                [ 'only' => [ 'index' ] ]
            );
        } );


    }
);


/**
 * MT1 API Routes
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
                'uses' => 'ClientController@generatelinks'
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
         * MT1 API Resources
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


/**
 * Creation Wizard Routes
 *
 * Commenting out for now.
 */
/*
Route::group(
    [ 'prefix' => 'wizard' ] ,
    function () {
        Route::get( '/{type}' , [
            'as' => 'dfsddf' ,
            'uses' => 'WizardController@index'
        ] );

        Route::get( '/pager/{type}/{page}' , [
            'as' => 'gdfg',
            'uses' => 'WizardController@getPage'
        ] );
    }
);
 */
