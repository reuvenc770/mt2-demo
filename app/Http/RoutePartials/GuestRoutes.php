<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
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
