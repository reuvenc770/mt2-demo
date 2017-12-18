<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\EmailDomain;
use App\Repositories\RepoInterfaces\Mt1Import;
use Carbon\Carbon;

class EmailDomainRepo implements Mt1Import {
    const DEFAULT_LOOKBACK = 7;

    protected $model;

    public function __construct ( EmailDomain $model ) {
        $this->model = $model;
    }

    public function pullForSync ( $lookback = null ) {
        if ( !$this->isValidLookback( $lookback ) ) {
            throw new \Exception( "EmailDomainRepo - lookback value '{$lookback}' invalid." );
        }

        $queryObj = $this->model->where( 'suppressed' , 1 );

        $currentLookback = $lookback?: self::DEFAULT_LOOKBACK;

        $dateRange = [
            Carbon::now()->subDays( $currentLookback )->toDateString() ,
            Carbon::now()->toDateString()
        ];

        $queryObj->whereBetween( 'dateSupp' , $dateRange );

        return $queryObj;
    }

    public function insertToMt1 ( $data ) {}

    protected function isValidLookback ( $lookback ) {
        return ( is_null( $lookback ) || ( is_numeric( $lookback ) && $lookback > 0 ) );
    }
}
