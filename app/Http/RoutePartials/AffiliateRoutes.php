<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * API/UI Routes
 */
Route::group(
    [ 'middleware' => [ 'auth' , 'pageLevel' ] ] ,
    function () {
        Route::resource(
            'api/affiliates',
            'CakeAffiliateController',
            [ 'except' => ['index','show','create', 'edit']]
        );
    }
);
