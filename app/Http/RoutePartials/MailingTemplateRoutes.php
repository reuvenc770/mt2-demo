<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
 */
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
    }
);

/**
 * API Routes
 */
Route::group(
    [ 'middleware' => [ 'auth' , 'pageLevel' ] ] ,
    function () {
        Route::resource(
            'api/mailingtemplate',
            'MailingTemplateController',
            [ 'except' => ['create', 'edit']]
        );

        Route::get('/api/mailingtemplate/templates/{id}', [
            'as' => 'api.mailingtemplate.listbyesp',
            'uses' => 'EspApiAccountController@grabTemplatesByESP'
        ]);
    }
);
