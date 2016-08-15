<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Repositories\Attribution\ListOwnerReportRepo;
use App\Repositories\Attribution\ClientReportRepo;
use App\Services\MT1Services\ClientService;
use App\Exceptions\AggregatorServiceException;
use Carbon\Carbon;

class ListOwnerAggregatorService extends AbstractReportAggregatorService {
    protected $clientService;
    protected $clientReportRepo;
    protected $listOwnerRepo;

    public function __construct ( ClientService $clientService , ListOwnerReportRepo $listOwnerRepo , ClientReportRepo $clientReportRepo ) {
        $this->clientService = $clientService;
        $this->listOwnerRepo = $listOwnerRepo;
        $this->clientReportRepo = $clientReportRepo;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->clientService ) ) {
            throw new AggregatorServiceException( 'ClientService needed. Please inject a service.' );
        }

        if ( !isset( $this->listOwnerRepo ) ) {
            throw new AggregatorServiceException( 'ListOwnerReportRepo needed. Please inject a repo.' );
        }

        if ( !isset( $this->clientReportRepo ) ) {
            throw new AggregatorServiceException( 'ClientReportRepo needed. Please inject a repo.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();

        $this->flattenStruct( 1 );

        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->clientReportRepo->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $date = $baseRecord->date;
        $listOwnerId = $this->clientService->getAssignedListOwnerId( $baseRecord->client_id );
        
        $this->createRowIfMissing( $date , $listOwnerId );

        $currentRow = &$this->getCurrentRow( $date , $listOwnerId );

        $currentRow[ "standard_revenue" ] = (
            ( $currentRow[ "standard_revenue" ] * parent::WHOLE_NUMBER_MODIFIER ) + ( $baseRecord[ "revenue" ] * parent::WHOLE_NUMBER_MODIFIER )
        ) / parent::WHOLE_NUMBER_MODIFIER;

        #Need to hook in CPM revenue here

        $currentRow[ "mt1_uniques" ] += $baseRecord[ "mt1_uniques" ];
        $currentRow[ "mt2_uniques" ] += $baseRecord[ "mt2_uniques" ];
    }

    protected function formatRecordToSqlString ( $record ) {
        $date = Carbon::parse( $record[ 'date' ] )->startOfMonth()->toDateString();

        return "( 
            '{$record[ 'client_stats_grouping_id' ]}' ,
            '{$record[ 'standard_revenue' ]}' ,
            '{$record[ 'cpm_revenue' ]}' ,
            '{$record[ 'mt1_uniques' ]}' ,
            '{$record[ 'mt2_uniques' ]}' ,
            '{$date}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        $this->listOwnerRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $clientStatsGroupingId ) {
        $date = Carbon::parse( $date )->startOfMonth()->toDateString();

        if ( !isset( $this->recordStruct[ $date ][ $clientStatsGroupingId ] ) ) {
            $this->recordStruct[ $date ][ $clientStatsGroupingId ] = [
                "client_stats_grouping_id" => $clientStatsGroupingId ,
                "standard_revenue" => 0 ,
                "cpm_revenue" => 0 ,
                "mt1_uniques" => 0 ,
                "mt2_uniques" => 0 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $clientStatsGroupingId ) {
        $date = Carbon::parse( $date )->startOfMonth()->toDateString();

        return $this->recordStruct[ $date ][ $clientStatsGroupingId ];
    }
}
