<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
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
 * API Routes
 */
Route::group(
    [ 'middleware' => [ 'auth' , 'pageLevel' ] ] ,
    function () {
        Route::resource(
            'api/role' ,
            'RoleApiController',
            [ 'except' => [ 'create' , 'edit' ] ]
        );
    }
);

Route::group(
    [   
        'prefix' => 'api/role' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/permissions/' , [
            'as' => 'api.role.permissions' ,
            'uses' => 'RoleApiController@permissions'
        ] );

        Route::get( '/permissionTree/{id}' , [
            'as' => 'api.role.permissions.tree' ,
            'uses' => 'RoleApiController@getPermissionTree'
        ] );
    }
);
