<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use App\Services\Attribution\AbstractReportAggregatorService;
use App\Repositories\Attribution\ClientReportRepo;
use App\Repositories\Attribution\FeedReportRepo;
use App\Services\MT1Services\ClientService;
use App\Exceptions\AggregatorServiceException;
use Carbon\Carbon;

class ClientAggregatorService extends AbstractReportAggregatorService {
    protected $clientService;
    protected $feedReportRepo;
    protected $clientRepo;

    public function __construct ( ClientService $clientService , ClientReportRepo $clientRepo , FeedReportRepo $feedReportRepo ) {
        $this->clientService = $clientService;
        $this->clientRepo = $clientRepo;
        $this->feedReportRepo = $feedReportRepo;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->clientService ) ) {
            throw new AggregatorServiceException( 'ClientService needed. Please inject a service.' );
        }

        if ( !isset( $this->clientRepo ) ) {
            throw new AggregatorServiceException( 'ClientReportRepo needed. Please inject a repo.' );
        }

        if ( !isset( $this->feedReportRepo ) ) {
            throw new AggregatorServiceException( 'FeedReportRepo needed. Please inject a repo.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();

        $this->flattenStruct( 1 );

        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->feedReportRepo->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $date = $baseRecord->date;
        $clientId = $this->clientService->getAssignedListOwnerId( $baseRecord->client_id );
        
        $this->createRowIfMissing( $date , $clientId );

        $currentRow = &$this->getCurrentRow( $date , $clientId );

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
        $this->clientRepo->runInsertQuery( $valuesSqlString );
    }

    protected function createRowIfMissing ( $date , $clientId ) {
        $date = Carbon::parse( $date )->startOfMonth()->toDateString();

        if ( !isset( $this->recordStruct[ $date ][ $clientId ] ) ) {
            $this->recordStruct[ $date ][ $clientId ] = [
                "client_stats_grouping_id" => $clientId ,
                "standard_revenue" => 0 ,
                "cpm_revenue" => 0 ,
                "mt1_uniques" => 0 ,
                "mt2_uniques" => 0 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $clientId ) {
        $date = Carbon::parse( $date )->startOfMonth()->toDateString();

        return $this->recordStruct[ $date ][ $clientId ];
    }
}
