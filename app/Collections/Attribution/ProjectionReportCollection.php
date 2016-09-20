<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections\Attribution;

use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Repositories\Attribution\ClientReportRepo;
use App\Repositories\Attribution\FeedReportRepo;
use App\Services\MT1Services\ClientService;
use App\Services\AttributionModelService;

class ProjectionReportCollection extends Collection {
    protected $clientReport;
    protected $feedReport;
    protected $clientService;
    protected $attrService;

    protected $reportRecords = [];

    public function __construct (
        ClientReportRepo $clientReport ,
        FeedReportRepo $feedReport ,
        ClientService $clientService ,
        AttributionModelService $attrService
    ) {
        $this->clientReport = $clientReport;
        $this->feedReport = $feedReport;
        $this->clientService = $clientService;
        $this->attrService = $attrService;
    }

    public function getReportData ( $modelId ) {
        $clientList = $this->clientReport->getClientsForDateRange(
            Carbon::today()->startOfMonth()->toDateString() ,
            Carbon::today()->endOfMonth()->toDateString()
        );

        foreach ( $clientList as $client ) {
            $this->reportRecords []= $this->getClientReportRow( $client->id , $modelId );
            
            $feedList = $this->clientService->getClientFeedsForListOwner( $client->id );

            foreach ( $feedList as $feedId ) {
                $this->reportRecords []= $this->getFeedReportRow( $feedId , $modelId );
            }
        }

        return response()->json( $this->reportRecords );
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
            "standard_revenue" => $clientRecord->standard_revenue ,
            "standard_revshare" => $clientRecord->standard_revenue * 0.15 ,
            "cpm_revenue" => $clientRecord->cpm_revenue ,
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
            "standard_revenue" => $feedRecord->revenue ,
            "standard_revshare" => $feedRecord->revenue * 0.15 ,
            "cpm_revenue" => 0 ,
            "cpm_revshare" => 0
        ];
    }
}
