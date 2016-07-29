<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use DB;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;

class DeployAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;

    public function __construct ( RecordReportService $recordReport ) {
        $this->recordReport = $recordReport;
    }

    public function buildAndSaveReport ( array $dateRange = null ) {
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
        $deployId = $baseRecord->deploy_id;
        
        $this->createRowIfMissing( $date , $deployId );

        $currentRow = &$this->getCurrentRow( $date , $deployId );

        $currentRow[ "delivered" ] += $baseRecord[ "delivered" ];
        $currentRow[ "opened" ] += $baseRecord[ "opened" ];
        $currentRow[ "clicked" ] += $baseRecord[ "clicked" ];
        $currentRow[ "converted" ] += $baseRecord[ "converted" ];
        $currentRow[ "bounced" ] += $baseRecord[ "bounced" ];
        $currentRow[ "unsubbed" ] += $baseRecord[ "unsubbed" ];
        $currentRow[ "revenue" ] = ( $currentRow[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER + $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) / parent::WHOLE_NUMBER_MODIFIER;
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'deploy_id' ]}' ,
            '{$record[ 'delivered' ]}' ,
            '{$record[ 'opened' ]}' ,
            '{$record[ 'clicked' ]}' ,
            '{$record[ 'converted' ]}' ,
            '{$record[ 'bounced' ]}' ,
            '{$record[ 'unsubbed' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_deploy_reports ( deploy_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                deploy_id = deploy_id ,
                delivered = VALUES( delivered ) ,
                opened = VALUES( opened ) ,
                clicked = VALUES( clicked ) ,
                converted = VALUES( converted ) ,
                bounced = VALUES( bounced ) ,
                unsubbed = VALUES( unsubbed ) ,
                revenue = VALUES( revenue ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    protected function createRowIfMissing ( $date , $deployId ) {
        if ( !isset( $this->recordStruct[ $date ][ $deployId ] ) ) {
            $this->recordStruct[ $date ][ $deployId ] = [
                "deploy_id" => $deployId ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 0 ,
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => 0 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $deployId ) {
        return $this->recordStruct[ $date ][ $deployId ];
    }
}
