<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailDomain;
use App\Repositories\RepoInterfaces\Mt1Import;
use Carbon\Carbon;

class EmailDomainRepo implements Mt1Import {
    const LAST_ID_FIELD = 'LastSuppressedEmailDomainId';
    const DEFAULT_LOOKBACK = 7;

    protected $model;

    public function __construct ( EmailDomain $model ) {
        $this->model = $model;
    }

    public function pullForSync ( $lookback = null ) {
        $queryObj = $this->model->where( 'suppressed' , 1 );


        if ( $this->useLookback( $lookback ) ) {
            $currentLookback = $lookback?: self::DEFAULT_LOOKBACK;

            $dateRange = [
                Carbon::now()->subDays( $currentLookback )->toDateString() ,
                Carbon::now()->toDateString()
            ];

            $queryObj->whereBetween( 'dateSupp' , $dateRange );
        }

        return $queryObj;
    }

    protected function useLookback ( $lookback ) {
        return ( is_null( $lookback ) || ( is_numeric( $lookback ) && $lookback > 0 ) );
    }
}
