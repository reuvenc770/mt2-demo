<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

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
