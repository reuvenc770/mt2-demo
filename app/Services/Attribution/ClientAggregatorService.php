<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Repositories\Attribution\ClientAggregatorRepo;
use App\Services\EmailClientAssignmentService;
use App\Exceptions\AggregatorServiceException;

class ClientAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $emailClientService;
    protected $clientRepo;

    public function __construct ( RecordReportService $recordReport , EmailClientAssignmentService $emailClientService , ClientAggregatorRepo $clientRepo ) {
        $this->recordReport = $recordReport;
        $this->emailClientService = $emailClientService;
        $this->clientRepo = $clientRepo;
    }

    public function buildAndSaveReport ( array $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new AggregatorServiceException( 'RecordReportService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailClientService ) ) {
            throw new AggregatorServiceException( 'EmailClientAssignmentService needed. Please inject a service.' );
        }

        if ( !isset( $this->clientRepo ) ) {
            throw new AggregatorServiceException( 'ClientAggregatorRepo needed. Please inject a repo.' );
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
        $clientId = $this->emailClientService->getAssignedClient( $baseRecord->email_id );
        
        $this->createRowIfMissing( $date , $clientId );

        $currentRow = &$this->getCurrentRow( $date , $clientId );

        $currentRow[ "delivered" ] += $baseRecord[ "delivered" ];
        $currentRow[ "opened" ] += $baseRecord[ "opened" ];
        $currentRow[ "clicked" ] += $baseRecord[ "clicked" ];
        $currentRow[ "converted" ] += $baseRecord[ "converted" ];
        $currentRow[ "bounced" ] += $baseRecord[ "bounced" ];
        $currentRow[ "unsubbed" ] += $baseRecord[ "unsubbed" ];
        $currentRow[ "revenue" ] = (
            ( $currentRow[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) + ( $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER )
        ) / parent::WHOLE_NUMBER_MODIFIER;
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'client_id' ]}' ,
            '{$record[ 'delivered' ]}' ,
            '{$record[ 'opened' ]}' ,
            '{$record[ 'clicked' ]}' ,
            '{$record[ 'converted' ]}' ,
            '{$record[ 'bounced' ]}' ,
            '{$record[ 'unsubbed' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'cost' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        $this->clientRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $clientId ) {
        if ( !isset( $this->recordStruct[ $date ][ $clientId ] ) ) {
            $this->recordStruct[ $date ][ $clientId ] = [
                "client_id" => $clientId ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 0 ,
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => 0 ,
                "cost" => 0.00 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $clientId ) {
        return $this->recordStruct[ $date ][ $clientId ];
    }
}
