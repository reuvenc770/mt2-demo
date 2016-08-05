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

    abstract public function buildAndSaveReport( $dateRange = null );

    abstract protected function getBaseRecords();

    abstract protected function processBaseRecord( $baseRecord );

    abstract protected function formatRecordToSqlString( $record );

    abstract protected function runInsertQuery( $valuesSqlString );

    public function setDateRange ( $dateRange = null ) {
        if ( is_null( $dateRange ) ) {
            $this->dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        } else {
            $this->dateRange = $dateRange;
        }
    }

    public function getRecords () {
        return $this->recordList;
    }

    public function count () {
        return count( $this->recordList );
    }

    protected function buildRecords () {
        foreach ( $this->getBaseRecords() as $current ) {
            $this->processBaseRecord( $current );
        }
    }

    protected function saveReport () {
        if ( $this->count() ) {
            $chunkedRows = $this->recordList->chunk( self::INSERT_CHUNK_AMOUNT );

            foreach ( $chunkedRows as $chunk ) {
                $rowStringList = [];

                foreach ( $chunk as $row ) {
                    $rowStringList []= $this->formatRecordToSqlString( $row ); 
                }

                $insertString = implode( ',' , $rowStringList );

                $this->runInsertQuery( $insertString );
            }
        }
    }

    protected function flattenStruct ( $flattenLevel = null ) {
        if ( is_null( $flattenLevel ) ) { $flattenLevel = self::DEFAULT_FLATTEN_LEVEL; }

        $this->recordList = collect( collect( $this->recordStruct )->flatten( $flattenLevel )->all() );
    }
}
