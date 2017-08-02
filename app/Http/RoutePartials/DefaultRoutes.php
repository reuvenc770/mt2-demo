<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

Route::get( '/' , [
    'as' => 'root' ,
    'uses' => 'HomeController@redirect'
] );
