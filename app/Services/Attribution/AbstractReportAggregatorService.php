<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use Carbon\Carbon;

abstract class AbstractReportAggregatorService {
    const STATIC_OFFER_ID_PLACEHOLDER = 0;
    const WHOLE_NUMBER_MODIFIER = 100000;
    const INSERT_CHUNK_AMOUNT = 10000;
    const DEFAULT_FLATTEN_LEVEL = 1;

    protected $dateRange;
    protected $recordList = [];
    protected $recordStruct = [];

    abstract public function buildAndSaveReport( array $dateRange = null );

    abstract protected function buildRecords();

    abstract protected function saveReport();

    public function setDateRange ( $dateRange = null ) {
        if ( is_null( $dateRange ) && !isset( $this->dateRange ) ) {
            $this->dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        } elseif ( !is_null( $dateRange ) && isset( $this->dateRange ) ) {
            $this->dateRange = $dateRange;
        }
    }

    public function getRecords () {
        return $this->recordList;
    }

    public function count () {
        return count( $this->recordList );
    }

    protected function flattenStruct ( $flattenLevel = null ) {
        if ( is_null( $flattenLevel ) ) { $flattenLevel = self::DEFAULT_FLATTEN_LEVEL; }

        $this->recordList = collect( collect( $this->recordStruct )->flatten( $flattenLevel )->all() );
    }
}
