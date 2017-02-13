<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections\Attribution;

use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Repositories\Attribution\ClientReportRepo;
use App\Repositories\Attribution\FeedReportRepo;
use App\Repositories\FeedRepo;
use App\Services\MT1Services\ClientService;
use App\Services\AttributionModelService;
use App\Services\MT1ApiService;
use App\Services\MT1Services\ClientStatsGroupingService;

class ProjectionReportCollection extends Collection {
    const CLIENT_API_ENDPOINT = 'clients_list';  

    protected $clientReport;
    protected $feedReport;
    protected $feedRepo;
    protected $clientService;
    protected $attrService;
    protected $listOwnerService;

    protected $reportRecords = [];

    protected $feedNameList = [];
    protected $clientNameList = [];

    public function __construct (
        ClientReportRepo $clientReport ,
        FeedReportRepo $feedReport ,
        FeedRepo $feedRepo ,
        ClientService $clientService ,
        AttributionModelService $attrService ,
        ClientStatsGroupingService $listOwnerService
    ) {
        $this->clientReport = $clientReport;
        $this->feedReport = $feedReport;
        $this->feedRepo = $feedRepo;
        $this->clientService = $clientService;
        $this->attrService = $attrService;
        $this->listOwnerService = $listOwnerService;
    }

    public function getReportData ( $modelId ) {
        $this->loadReportData( $modelId );

        return response()->json( $this->reportRecords );
    }

    public function getReportRowsHtml ( $modelId ) {
        $this->loadReportData( $modelId );
        $this->loadFeedNames();
        $this->loadClientNames();

        $tableRows = [];
        foreach ( $this->reportRecords as $currentRow ) {
            if (
                0 == $currentRow[ 'model' ][ 'standard_revenue' ]
                && 0 == $currentRow[ 'live' ][ 'standard_revenue' ]
                && 0 == $currentRow[ 'model' ][ 'cpm_revenue' ]
                && 0 == $currentRow[ 'live' ][ 'cpm_revenue' ]
            ) {
                continue;
            }
        
            $rowClass = '';
            $rowClientName = '';
            if ( isset( $currentRow[ 'client_stats_grouping_id' ] ) ) {
                $rowClass = 'class="mt2-total-row"';
                
                $clientName = 'Untitled Client';
                
                if ( isset( $this->clientNameList[ $currentRow[ 'client_stats_grouping_id' ] ] ) ) {
                    $clientName = $this->clientNameList[ $currentRow[ 'client_stats_grouping_id' ] ][ 'name' ];
                }
                
                $rowClientName = $clientName . ' (' . $currentRow[ 'client_stats_grouping_id' ] . ')';
            }

            $rowFeedName = '';
            if ( isset( $currentRow[ 'feed_id' ] ) ) {
                $feedName = 'Untitled Feed'; 
                
                if ( isset( $this->feedNameList[ $currentRow[ 'feed_id' ] ] ) ) {
                    $feedName = $this->feedNameList[ $currentRow[ 'feed_id' ] ][ 'name' ];
                }
                
                $rowFeedName = $feedName . ' (' . $currentRow[ 'feed_id' ] . ')';
            }

            $levelClass = '';
            $levelChanged = ( $currentRow[ 'model' ][ 'level' ] != $currentRow[ 'live' ][ 'level' ] );
            $levelIncreased = ( $currentRow[ 'model' ][ 'level' ] < $currentRow[ 'live' ][ 'level' ] );
            if ( $levelChanged && $levelIncreased ) {
                $levelClass = 'class="mt2-proj-increase-bg"';
            } elseif ( $levelChanged && !$levelIncreased  ) {
                $levelClass = 'class="mt2-proj-decrease-bg"';
            }

            $standardRevClass = '';
            $standardRevChanged = ( $currentRow[ 'model' ][ 'standard_revenue' ] != $currentRow[ 'live' ][ 'standard_revenue' ] );
            $standardRevIncreased = ( $currentRow[ 'model' ][ 'standard_revenue' ] > $currentRow[ 'live' ][ 'standard_revenue' ] );
            if ( $standardRevChanged && $standardRevIncreased ) {
                $standardRevClass = 'class="mt2-proj-increase-bg"';
            } elseif ( $standardRevChanged && !$standardRevIncreased ) {
                $standardRevClass = 'class="mt2-proj-decrease-bg"';
            }

            $standardRevShareClass = '';
            $standardRevShareChanged = ( $currentRow[ 'model' ][ 'standard_revshare' ] != $currentRow[ 'live' ][ 'standard_revshare' ] );
            $standardRevShareIncreased = ( $currentRow[ 'model' ][ 'standard_revshare' ] > $currentRow[ 'live' ][ 'standard_revshare' ] );
            if ( $standardRevShareChanged && $standardRevShareIncreased ) {
                $standardRevShareClass = 'class="mt2-proj-increase-bg"';
            } elseif ( $standardRevShareChanged && !$standardRevShareIncreased ) {
                $standardRevShareClass = 'class="mt2-proj-decrease-bg"';
            }

            $cpmRevClass = '';
            $cpmRevChanged = ( $currentRow[ 'model' ][ 'cpm_revenue' ] != $currentRow[ 'live' ][ 'cpm_revenue' ] );
            $cpmRevIncreased = ( $currentRow[ 'model' ][ 'cpm_revenue' ] > $currentRow[ 'live' ][ 'cpm_revenue' ] );
            if ( $cpmRevChanged && $cpmRevIncreased ) {
                $cpmRevClass = 'class="mt2-proj-increase-bg"';
            } elseif ( $cpmRevChanged && !$cpmRevIncreased ) {
                $cpmRevClass = 'class="mt2-proj-decrease-bg"';
            }

            $cpmRevShareClass = '';
            $cpmRevShareChanged = ( $currentRow[ 'model' ][ 'cpm_revshare' ] != $currentRow[ 'live' ][ 'cpm_revshare' ] );
            $cpmRevShareIncreased = ( $currentRow[ 'model' ][ 'cpm_revshare' ] > $currentRow[ 'live' ][ 'cpm_revshare' ] );
            if ( $cpmRevShareChanged && $cpmRevShareIncreased ) {
                $cpmRevShareClass = 'class="mt2-proj-increase-bg"';
            } elseif ( $cpmRevShareChanged && !$cpmRevShareIncreased ) {
                $cpmRevShareClass = 'class="mt2-proj-decrease-bg"';
            }

            $tableRows []= "<tr {$rowClass} md-row>" .
                "<td md-cell>{$rowClientName}</td>" .
                "<td md-cell>{$rowFeedName}</td>" .
                "<td md-cell>{$currentRow[ 'live' ][ 'level' ]}</td>" .
                "<td {$levelClass} md-cell>{$currentRow[ 'model' ][ 'level' ]}</td>" .
                "<td md-cell>\$" . round( $currentRow[ 'live' ][ 'standard_revenue' ] , 2 ) . "</td>" .
                "<td {$standardRevClass} md-cell>\$" . round( $currentRow[ 'model' ][ 'standard_revenue' ] , 2 ) . "</td>" .
                "<td md-cell>\$" . round( $currentRow[ 'live' ][ 'standard_revshare' ] , 2 ) . "</td>" .
                "<td {$standardRevShareClass} md-cell>\$" . round( $currentRow[ 'model' ][ 'standard_revshare' ] , 2 ) . "</td>" .
                "<td md-cell>\$" . round( $currentRow[ 'live' ][ 'cpm_revenue' ] , 2 ) . "</td>" .
                "<td {$cpmRevClass} md-cell>\$" . round( $currentRow[ 'model' ][ 'cpm_revenue' ] , 2 ) . "</td>" .
                "<td md-cell>\$" . round( $currentRow[ 'live' ][ 'cpm_revshare'] , 2 ) . "</td>" .
                "<td {$cpmRevShareClass} md-cell>\$" . round( $currentRow[ 'model' ][ 'cpm_revshare' ] , 2 ) . "</td>" .
                "</tr>";
        }

        return implode( "\n" , $tableRows );
    }

    protected function loadReportData ( $modelId ) {
        $clientList = $this->clientReport->getClientsForDateRange(
            Carbon::today()->subDays( 30 )->toDateString() ,
            Carbon::today()->endOfDay()->toDateString()
        );

        foreach ( $clientList as $client ) {
            $this->reportRecords []= $this->getClientReportRow( $client->id , $modelId );
            
            $feedList = $this->clientService->getClientFeedsForListOwner( $client->id );

            foreach ( $feedList as $feedId ) {
                $this->reportRecords []= $this->getFeedReportRow( $feedId , $modelId );
            }
        }
    } 

    protected function loadFeedNames () {
        $this->feedNameList = $this->feedRepo->getFeeds()->keyBy( 'id' )->toArray();
    }

    protected function loadClientNames () {
        $this->clientNameList = $this->listOwnerService->getListGroups()->keyBy( 'value' )->toArray();
    }

    protected function getClientReportRow ( $clientId , $modelId ) {
        $clientRow = [ 'client_stats_grouping_id' => $clientId ];

        $this->clientReport->switchToLiveTable();
        $clientRecord = $this->clientReport->getAggregateForIdAndMonth( $clientId , Carbon::today()->startOfMonth()->toDateString() );

        $clientRow[ 'live' ] = $this->formatClientRow( $clientRecord );

        $this->clientReport->setModelId( $modelId );
        $clientRecord = $this->clientReport->getAggregateForIdAndMonth( $clientId , Carbon::today()->startOfMonth()->toDateString() );

        $clientRow[ 'model' ] = $this->formatClientRow( $clientRecord );

        return $clientRow;
    }

    protected function formatClientRow ( $clientRecord ) {
        return [
            "level" => '' ,
            "standard_revenue" => (float)$clientRecord->standard_revenue ,
            "standard_revshare" => $clientRecord->standard_revenue * 0.15 ,
            "cpm_revenue" => (float)$clientRecord->cpm_revenue ,
            "cpm_revshare" => $clientRecord->cpm_revenue * 0.15
        ];
    }

    protected function getFeedReportRow ( $feedId , $modelId ) {
        $feedRow = [ 'feed_id' => $feedId ];

        $this->feedReport->switchToLiveTable();
        $feedRecord = $this->feedReport->getAggregateForIdAndMonth( $feedId , Carbon::today()->startOfMonth()->toDateString() );

        $feedRow[ 'live' ] = $this->formatFeedRow( $feedRecord , $feedId );

        $this->feedReport->setModelId( $modelId );
        $feedRecord = $this->feedReport->getAggregateForIdAndMonth( $feedId , Carbon::today()->startOfMonth()->toDateString() );

        $feedRow[ 'model' ] = $this->formatFeedRow( $feedRecord , $feedId , $modelId );

        return $feedRow;
    }

    protected function formatFeedRow ( $feedRecord , $feedId , $modelId = null ) {
        return [
            "level" => $this->attrService->getLevel( $feedId , $modelId ) ,
            "standard_revenue" => (float)$feedRecord->revenue ,
            "standard_revshare" => $feedRecord->revenue * 0.15 ,
            "cpm_revenue" => 0 ,
            "cpm_revshare" => 0
        ];
    }
}
