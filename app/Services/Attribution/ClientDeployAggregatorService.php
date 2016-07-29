<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use DB;
use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Services\EmailClientAssignmentService;

class ClientDeployAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $emailClientService;

    public function __construct ( RecordReportService $recordReport , EmailClientAssignmentService $emailClientService ) {
        $this->recordReport = $recordReport;
        $this->emailClientService = $emailClientService;
    }

    public function buildAndSaveReport ( array $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new ClientReportCollectionException( 'RecordReport Model needed. Please inject a model.' );
        }

        if ( !isset( $this->emailClientService ) ) {
            throw new ClientReportCollectionException( 'EmailClientAssignmentService needed. Please inject a service.' );
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
        $clientId = $this->emailClientService->getAssignedClient( $baseRecord->email_id );
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
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_client_deploy_reports ( client_id , deploy_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , cost , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                client_id = client_id ,
                deploy_id = deploy_id ,
                delivered = VALUES( delivered ) ,
                opened = VALUES( opened ) ,
                clicked = VALUES( clicked ) ,
                converted = VALUES( converted ) ,
                bounced = VALUES( bounced ) ,
                unsubbed = VALUES( unsubbed ) ,
                revenue = VALUES( revenue ) ,
                cost = VALUES( cost ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
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
