<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * UI Routes
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
            '/users' ,
            array(
                'as' => 'report.users' ,
                'uses' => 'ReportController@users'
            )
        );
    }
);
