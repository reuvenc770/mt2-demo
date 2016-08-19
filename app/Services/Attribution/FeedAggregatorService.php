<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Repositories\Attribution\FeedReportRepo;
use App\Services\EmailClientAssignmentService;
use App\Services\EmailClientInstanceService;
use App\Exceptions\AggregatorServiceException;

class FeedAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $emailClientAssignmentService;
    protected $emailClientInstanceService;
    protected $feedRepo;

    public function __construct ( RecordReportService $recordReport , EmailClientAssignmentService $emailClientAssignmentService , EmailClientInstanceService $emailClientInstanceService , FeedReportRepo $feedRepo ) {
        $this->recordReport = $recordReport;
        $this->emailClientAssignmentService = $emailClientAssignmentService;
        $this->emailClientInstanceService = $emailClientInstanceService;
        $this->feedRepo = $feedRepo;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new AggregatorServiceException( 'RecordReportService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailClientAssignmentService ) ) {
            throw new AggregatorServiceException( 'EmailClientAssignmentService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailClientInstanceService ) ) {
            throw new AggregatorServiceException( 'EmailClientInstanceService needed. Please inject a service.' );
        }

        if ( !isset( $this->feedRepo ) ) {
            throw new AggregatorServiceException( 'FeedReportRepo needed. Please inject a repo.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();

        $this->flattenStruct( 1 );

        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->recordReport->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $date = $baseRecord->date;
        $clientId = $this->emailClientAssignmentService->getAssignedClient( $baseRecord->email_id );
        
        $this->createRowIfMissing( $date , $clientId );

        $currentRow = &$this->getCurrentRow( $date , $clientId );

        if ( is_null( $currentRow[ 'mt1_uniques' ] ) ) {
            $currentRow[ 'mt1_uniques' ] = (int)$this->emailClientInstanceService->getMt1UniqueCountForClientAndDate( $clientId , $date );
        }

        if ( is_null( $currentRow[ 'mt2_uniques' ] ) ) {
            $currentRow[ 'mt2_uniques' ] = (int)$this->emailClientInstanceService->getMt2UniqueCountForClientAndDate( $clientId , $date );
        }

        $currentRow[ "revenue" ] = (
            ( $currentRow[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) + ( $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER )
        ) / parent::WHOLE_NUMBER_MODIFIER;
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'client_id' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'mt1_uniques' ]}' ,
            '{$record[ 'mt2_uniques' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        $this->feedRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $clientId ) {
        if ( !isset( $this->recordStruct[ $date ][ $clientId ] ) ) {
            $this->recordStruct[ $date ][ $clientId ] = [
                "client_id" => $clientId ,
                "revenue" => 0 ,
                "mt1_uniques" => null ,
                "mt2_uniques" => null ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $clientId ) {
        return $this->recordStruct[ $date ][ $clientId ];
    }
}
