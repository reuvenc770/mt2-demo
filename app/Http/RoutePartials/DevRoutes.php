<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * API Routes
 */
Route::group(
    [   
        'prefix' => 'api' ,
        'middleware' => 'dev' 
    ] ,
    function () {
        Route::resource(
            'jobEntry' ,
            'JobApiController' ,
            [ 'only' => [ 'index' ] ]
        );
    }
);
