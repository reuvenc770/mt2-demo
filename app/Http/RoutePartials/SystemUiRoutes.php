<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
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

        /**
         * Navigation Routes
         */
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


        /**
         * Notification Routes
         */
        Route::resource(
            'notifications',
            'ScheduledNotificationController',
            [ 'except' => ['index','show','create', 'edit']]
        );

        Route::get('/notifications/unscheduled', [
            'as' => 'api.notifications.unscheduled',
            'uses' => 'ScheduledNotificationController@getUnscheduledLogs'
        ]);

        Route::get('/notifications/emailtemplates', [
            'as' => 'api.notifications.emailtemplates',
            'uses' => 'ScheduledNotificationController@getEmailTemplates'
        ]);

        Route::get('/notifications/slacktemplates', [
            'as' => 'api.notifications.slacktemplates',
            'uses' => 'ScheduledNotificationController@getSlackTemplates'
        ]);

        Route::get('/notifications/contentkey', [
            'as' => 'api.notifications.contentkey',
            'uses' => 'ScheduledNotificationController@getContentKeys'
        ]);
    }
);
