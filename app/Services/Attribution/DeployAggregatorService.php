<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Services\Attribution\RecordReportService;
use App\Repositories\Attribution\DeployAggregatorRepo;

class DeployAggregatorService extends AbstractReportAggregatorService {
    protected $recordReport;
    protected $deployRepo;

    public function __construct ( RecordReportService $recordReport , DeployAggregatorRepo $deployRepo ) {
        $this->recordReport = $recordReport;
        $this->deployRepo = $deployRepo;
    }

    public function buildAndSaveReport ( array $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new AggregatorServiceException( 'RecordReportService needed. Please inject a service.' );                                                                                                        
        }

        if ( !isset( $this->deployRepo ) ) {
            throw new AggregatorServiceException( 'DeployAggregatorRepo needed. Please inject a service.' );                                                                                                        
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
        $this->deployRepo->runInsertQuery( $valuesSqlString );
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
