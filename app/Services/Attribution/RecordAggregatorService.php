<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;

use App\Repositories\Attribution\RecordReportRepo;
use App\Services\CakeConversionService;

class RecordAggregatorService extends AbstractReportAggregatorService {
    protected $recordRepo;
    protected $conversionService;

    public function __construct (
        RecordReportRepo $recordRepo ,
        CakeConversionService $conversionService
    ) {
        $this->recordRepo = $recordRepo;
        $this->conversionService = $conversionService;
    }

    public function setProcessMode ( $mode ) {
        $this->processMode = $mode;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        $this->buildRecords();
        $this->flattenStruct( 3 );
        $this->saveReport();
    }

    public function getBaseRecords () {
        $baseRecords = $this->conversionService->getByDate( $this->dateRange );

        return $baseRecords;
    }

    protected function processBaseRecord ( $baseRecord ) {
        $emailId = $baseRecord->email_id;
        $deployId = $baseRecord->deploy_id;
        $date = $baseRecord->date;

        $this->createRowIfMissing( $date , $emailId , $deployId );

        $currentRow = &$this->getCurrentRow( $date , $emailId , $deployId ); 

        $currentRow[ "revenue" ] = (
            ( $currentRow[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) + ( $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER )
        ) / parent::WHOLE_NUMBER_MODIFIER;
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'email_id' ]}' ,
            '{$record[ 'deploy_id' ]}' ,
            '{$record[ 'offer_id' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        $this->recordRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $emailId , $deployId , $offerId = null ) {
        if ( is_null( $offerId ) ) { $offerId = parent::STATIC_OFFER_ID_PLACEHOLDER; }

        if ( !isset( $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ] ) ) {
            $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ] = [
                "email_id" => $emailId ,
                "deploy_id" => $deployId ,
                "offer_id" => $offerId ,
                "revenue" => 0.00 ,
                "date" => $date 
            ];
        }
    }

    protected function &getCurrentRow ( $date , $emailId , $deployId , $offerId = null ) {
        if ( is_null( $offerId ) ) { $offerId = parent::STATIC_OFFER_ID_PLACEHOLDER; }

        return $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ];
    }
}
