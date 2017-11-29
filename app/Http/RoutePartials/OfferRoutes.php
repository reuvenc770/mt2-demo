<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

/**
 * API Routes
 */
Route::group(
    [
        'prefix' => 'api/offer' ,
        'middleware' => [ 'auth' , 'pageLevel' ]
    ] ,
    function () {
        Route::get( '/daysearch' , [
            'as' => 'api.offer.search' ,
            'uses' => 'OfferController@typeAheadDaySearch'
        ] );

        Route::get( '/lpsearch' , [
            'as' => 'api.offer.search' ,
            'uses' => 'OfferController@typeAheadSearch'
        ] );
    }
);
