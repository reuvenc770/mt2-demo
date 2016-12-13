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
Route::get( '/' , [
    'as' => 'root' ,
    'uses' => 'HomeController@redirect'
] );


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

    Route::any( '/api/post_data' , [
        'as' => 'api.feed.realtimerecords' ,
        'uses' => 'FeedApiController@ingest'
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
 *
 */
Route::group(
    [
        'prefix' => 'esp',
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'esp.list' ,
            'uses' => 'EspController@listAll'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'esp.edit' ,
            'uses' => 'EspController@edit'
        ] );

        Route::get( '/create' , [
            'as' => 'esp.add' ,
            'uses' => 'EspController@create'
        ] );
        Route::get( '/mapping/{id}' , [
            'as' => 'esp.mapping' ,
            'uses' => 'EspController@mappings'
        ] );
    }
);


/**
 * MT2 Tool Routes
 */
Route::group(
    [
        'prefix' => 'tools' ,
        'middleware' => [ 'auth','pageLevel' ]
    ] ,
    function () {

        Route::get( '/tools' , [
            'as' => 'tools.list' ,
            'uses' => 'HomeController@redirectTools'
        ] );

        Route::get( '/show-info' , [
            'as' => 'tools.recordlookup' ,
            'uses' => 'ShowInfoController@index'
        ] );
        Route::get( '/bulk-suppression' , [
            'as' => 'tools.bulksuppression' ,
            'uses' => 'BulkSuppressionController@index'
        ] );

        Route::get( '/appendeid' , [
            'as' => 'tools.appendeid' ,
            'uses' => 'AppendEidController@index'
        ] );

        Route::get( '/navigation' , [
            'as' => 'tools.navigation' ,
            'uses' => 'NavigationController@index'
        ] );

        Route::get( '/source-url-search' , [
            'as' => 'tools.sourceurlsearch' ,
            'uses' => 'SourceUrlSearchController@index'
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
        'middleware' => [ 'auth' , 'pageLevel' ]
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
        'middleware' => [ 'auth' , 'pageLevel' ]
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

        Route::get( '/preview/{deployId}' , [
            'as' => 'deploy.preview' ,
            'uses' => 'DeployController@previewDeploy'
        ] );


        Route::get( '/downloadhtml/{deployId}' , [
            'as' => 'deploy.downloadhtml' ,
            'uses' => 'DeployController@downloadHtml'
        ] );

    });

/**
 * User Routes
 */
Route::group(
    [
        'prefix' => 'user' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
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
 * Feed Routes
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
    }
);

/**
 * Feed Group Routes
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

        Route::get( '/combine/edit/{id}' , [
            'as' => 'listprofile.combine.edit' ,
            'uses' => 'ListProfileController@editListCombine'
        ] );

    }
);


/**
 * Role Routes
 */
Route::group(
    [
        'prefix' => 'role' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
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
        'middleware' => [ 'auth' , ]
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

Route::group(
    [
        'prefix' => 'creatives' ,
        'middleware' => [ 'auth'  ]
    ] ,
    function () {
        Route::get( '/preview/{offerId}' , [
            'as' => 'creatives.preview' ,
            'uses' => 'CreativeFromSubjectController@previewCreative'
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
 * Attribution Routes
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
            '/projection/{id}',
            array(
                'as' => 'attributionProjection.show',
                'uses' => 'AttributionController@showProjection'
            )
        );
    }
);

/**
 * Report Routes
 */
Route::group(
    [
        'prefix' => 'report' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get(
            '/' ,
            array(
                'as' => 'report.list' ,
                'uses' => 'ReportController@viewAmpReports'
            )
        );

        Route::get(
            '/export',
            array(
                'as' => 'report.export',
                'uses' => 'ReportController@export'
            )
        );

        Route::get(
            '/users' ,
            array(
                'as' => 'report.users' ,
                'uses' => 'ReportController@users'
            )
        );
    }
);

/**
 * ISP Group
 */
Route::group(
    [
        'prefix' => 'ispgroup' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'ispgroup.list' ,
            'uses' => 'DomainGroupController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'ispgroup.add' ,
            'uses' => 'DomainGroupController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'ispgroup.edit' ,
            'uses' => 'DomainGroupController@edit'
        ] );

    }
);

Route::group(
    [
        'prefix' => 'isp' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/' , [
            'as' => 'isp.list' ,
            'uses' => 'EmailDomainController@listAll'
        ] );

        Route::get( '/create' , [
            'as' => 'isp.add' ,
            'uses' => 'EmailDomainController@create'
        ] );

        Route::get( '/edit/{id}' , [
            'as' => 'isp.edit' ,
            'uses' => 'EmailDomainController@edit'
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

        Route::put('/dataexport/update', [
            'as' => 'dataexport.update',
            'middleware' => ['auth'],
            'uses' => 'DataExportController@message'
        ]);

        Route::get('/client/updatepassword/{username}', [
            'as' => 'api.client.updatepassword' ,
            'uses' => 'FeedController@resetClientPassword'
        ] );
        Route::get('/navigation/gettree', [
            'as' => 'api.tools.navigation.getTree' ,
            'uses' => 'NavigationController@returnCurrentNavigation'
        ] );
        Route::get('/navigation/orphans', [
            'as' => 'api.tools.navigation.getOrphans' ,
            'uses' => 'NavigationController@returnValidOrphanNavigation'
        ] );

        Route::post('/navigation', [
            'as' => 'api.tools.navigation.update' ,
            'uses' => 'NavigationController@update'
        ] );

        Route::get(
            'esp/mappings/{id}' ,
            [
                'as' => 'api.esp.mappings.get' ,
                'uses' => 'EspController@getMapping'
            ]
        );
        Route::put(
            'esp/mappings/{id}' ,
            [
                'as' => 'api.esp.mappings.update' ,
                'uses' => 'EspController@updateMappings'
            ]
        );

        Route::post(
            'esp/mappings/process' ,
            [
                'as' => 'api.esp.mappings.process' ,
                'uses' => 'EspController@processCSV'
            ]
        );

        Route::group(
            [ 'prefix' => 'deploy' ] ,
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
        Route::group(
            [ 'prefix' => 'proxy' ] ,
            function () {
                Route::get('/active', [
                    'as' => 'api.proxy.list',
                    'uses' => 'ProxyController@listAllActive'
                ]);
            }
        );

        /**
         * List Profile API Routes
         */
        Route::group(
            [ 'prefix' => 'listprofile' ] ,
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

        /**
         * Proxies additional routes
         */

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
         *  CFS API Routes
         */
        Route::group(
            ['prefix' => 'cfs'],
            function() {

                Route::get('/creatives/{id}', [
                    'as' => 'api.cfs.creatives',
                    'uses' => 'CreativeFromSubjectController@getCreatives'
                ]);

                Route::get('/froms/{id}', [
                    'as' => 'api.cfs.froms',
                    'uses' => 'CreativeFromSubjectController@getFroms'
                ]);

                Route::get('/subjects/{id}', [
                    'as' => 'api.cfs.subjects',
                    'uses' => 'CreativeFromSubjectController@getSubjects'
                ]);
            }
        );

        /**
         * Report API Routes
         */
        Route::group(
            ['prefix' => 'report'],
            function() {
                Route::get( '/' , [
                    'as' => 'api.report.getRecords' ,
                    'middleware' => 'auth' ,
                    'uses' => 'ReportController@getRecords'
                ] );
            }
        );


        /**
         *  Attribution API Routes
         */
        Route::group(
            [] ,
            function () {
                Route::get( '/attribution/model' , [
                    'as' => 'api.attribution.model.index' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@index'
                ] );

                Route::post( '/attribution/model' , [
                    'as' => 'api.attribution.model.store' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@store'
                ] );

                Route::put( '/attribution/model/{modelId}' , [
                    'as' => 'api.attribution.model.update' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@update'
                ] );

                Route::delete( '/attribution/model/{modelId}/{feedId}' , [
                    'as' => 'api.attribution.model.destroy' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@destroy'
                ] );

                Route::get( '/attribution/model/{modelId}' , [
                    'as' => 'api.attribution.model.show' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@show'
                ] );

                Route::get( '/attribution/model/{modelId}/levels' , [
                    'as' => 'api.attribution.model.levels' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@levels'
                ] );

                Route::get( '/attribution/model/{modelId}/feeds' , [
                    'as' => 'api.attribution.model.clients' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@getModelFeeds'
                ] );

                Route::post( '/attribution/model/copyLevels' , [
                    'as' => 'api.attribution.model.copyLevels' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@copyLevels'
                ] );

                Route::get( '/attribution/model/setlive/{modelId}' , [
                    'as' => 'api.attribution.model.setlive' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@setModelLive'
                ] );

                Route::post( '/attribution/model/run' , [
                    'as' => 'api.attribution.run' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@runAttribution'
                ] );

                Route::get( '/attribution/projection/report/{modelId}' , [
                    'as' => 'api.attribution.projection.report' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@getReportData'
                ] );

                Route::get( '/attribution/projection/chart/{modelId}' , [
                    'as' => 'api.attribution.projection.chart' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@getChartData'
                ] );

                Route::get( '/attribution/syncLevels' , [
                    'as' => 'api.attribution.model.syncLevels' ,
                    'middleware' => 'auth' ,
                    'uses' => 'AttributionController@syncLevelsWithMT1'
                ] );
            }
        );

        /**
         * ESP API Routes
         */
        Route::get( '/espapi/espAccounts/{name}' , [
            'as' => 'api.espapi.GetAll' ,
            'uses' => 'EspApiAccountController@displayEspAccounts'
        ] );

        /**
         * Feed API Routes
         */
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

        /**
         * API Resources
         */
        Route::get(
            'espapi/all' ,
            [
                'as' => 'api.espapi.returnAll' ,
                'uses' => 'EspApiAccountController@returnAll'
            ]
        );
        Route::get(
            'espapi/allactive' ,
            [
                'as' => 'api.espapi.returnallactive' ,
                'uses' => 'EspApiAccountController@returnAllActive'
            ]
        );
        Route::resource(
            'esp' ,
            'EspController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'espapi' ,
            'EspApiAccountController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'ymlp-campaign' ,
            'YmlpCampaignController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'feed' ,
            'FeedController' ,
            [ 'except' => [ 'create' , 'edit' , 'pager' ] ]
        );

        Route::resource(
            'client' ,
            'ClientController' ,
            [ 'only' => [ 'store' , 'update' , 'destroy' , 'show' ] ]
        );

        Route::resource(
            'feedgroup' ,
            'FeedGroupController' ,
            [ 'except' => [ 'create' , 'edit' ] ]
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
            'ispgroup',
            'DomainGroupController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );

        Route::resource(
            'isp',
            'EmailDomainController',
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
            'bulksuppression' ,
            'BulkSuppressionController' ,
            [ 'only' => [ 'store' ] ]
        );

        Route::resource(
            'suppressionReason' ,
            'SuppressionReasonController',
            [ 'only' => [ 'index' ] ]
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
        Route::get('/proxy/toggle/{id}', [
            'as' => 'api.proxy.toggle',
            'uses' => 'ProxyController@toggle'
        ]);

        Route::resource(
            'registrar',
            'RegistrarController',
            [ 'except' => ['create', 'edit']]
        );
        Route::get('/registrar/toggle/{id}', [
            'as' => 'api.registar.toggle',
            'uses' => 'RegistrarController@toggle'
        ]);

        Route::resource(
            'dba',
            'DoingBusinessAsController',
            [ 'except' => ['create', 'edit']]
        );

        Route::get('/dba/toggle/{id}', [
            'as' => 'api.dba.toggle',
            'uses' => 'DoingBusinessAsController@toggle'
        ]);

        Route::get('/mailingtemplate/templates/{id}', [
            'as' => 'api.mailingtemplate.listbyesp',
            'uses' => 'EspApiAccountController@grabTemplatesByESP'
        ]);
        Route::resource(
            'mailingtemplate',
            'MailingTemplateController',
            [ 'except' => ['create', 'edit']]
        );

        /**
         * Admin Level API Group
         */
        Route::group( [ 'middleware' => 'pageLevel' ] , function () {
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

        Route::post( '/appendeid/upload/' , [
            'as' => 'tools.appendeid.upload' ,
            'uses' => 'AppendEidController@manageUpload'
        ] );

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
