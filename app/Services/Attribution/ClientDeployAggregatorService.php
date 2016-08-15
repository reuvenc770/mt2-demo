<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Services\EmailFeedAssignmentService;
use App\Repositories\Attribution\ClientDeployReportRepo;
use App\Exceptions\AggregatorServiceException;

class ClientDeployAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $emailFeedService;
    protected $clientDeployRepo;

    public function __construct ( RecordReportService $recordReport , EmailFeedAssignmentService $emailFeedService , ClientDeployReportRepo $clientDeployRepo ) {
        $this->recordReport = $recordReport;
        $this->emailFeedService = $emailFeedService;
        $this->clientDeployRepo = $clientDeployRepo;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new AggregatorServiceException( 'RecordReportService needed. Please inject a service.' );
        }

        if ( !isset( $this->emailFeedService ) ) {
            throw new AggregatorServiceException( 'EmailFeedAssignmentService needed. Please inject a service.' );
        }

        if ( !isset( $this->clientDeployRepo ) ) {
            throw new AggregatorServiceException( 'ClientDeployReportRepo needed. Please inject a repo.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();

        $this->flattenStruct( 2 );

        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->recordReport->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $date = $baseRecord->date;
        $clientId = $this->emailFeedService->getAssignedClient( $baseRecord->email_id );
        $deployId = $baseRecord->deploy_id;
        
        $this->createRowIfMissing( $date , $clientId , $deployId );

        $currentRow = &$this->getCurrentRow( $date , $clientId , $deployId );

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
            '{$record[ 'deploy_id' ]}' ,
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
        $this->clientDeployRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $clientId , $deployId ) {
        if ( !isset( $this->recordStruct[ $date ][ $clientId ][ $deployId ] ) ) {
            $this->recordStruct[ $date ][ $clientId ][ $deployId ] = [
                "client_id" => $clientId ,
                "deploy_id" => $deployId ,
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

    protected function &getCurrentRow ( $date , $clientId , $deployId ) {
        return $this->recordStruct[ $date ][ $clientId ][ $deployId ];
    }
}
