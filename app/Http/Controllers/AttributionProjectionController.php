<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Carbon\Carbon;
use App\Repositories\Attribution\ClientReportRepo;
use App\Repositories\Attribution\FeedReportRepo;
use App\Services\MT1Services\ClientStatsGroupingService;
use App\Services\MT1Services\ClientService;
use App\Services\AttributionModelService;

class AttributionProjectionController extends Controller
{
    protected $clientReport;
    protected $feedReport;
    protected $clientService;
    protected $feedService;
    protected $attrService;

    public function __construct (
        ClientReportRepo $clientReport ,
        FeedReportRepo $feedReport ,
        ClientStatsGroupingService $clientService ,
        ClientService $feedService ,
        AttributionModelService $attrService
    ) {
        $this->clientReport = $clientReport;
        $this->feedReport = $feedReport;
        $this->clientService = $clientService;
        $this->feedService = $feedService;
        $this->attrService = $attrService;
    }

    public function show($id)
    {
        return response()->view( 'pages.attribution.attribution-projection' );
    }

    public function getChartData ( $id ) {

    }

    public function getReportData ( $id ) {
        $reportRecords = [];

        $clientList = $this->clientReportRepo->getClientsForDateRange(
            Carbon::today()->startOfMonth()->toDateString() ,
            Carbon::today()->endOfMonth()->toDateString()
        );

        foreach ( $clientList as $client ) {
            $currentRow = [
                'client_stats_grouping_id' => $client->id ,
                'live' => [ "level" => $this->attrService->getLevel( $client->id ) ] ,
                'model' => [ "level" => $this->attrService->getLevel( $client->id , $id ) ]
            ];

            $clientRecord = $this->clientReport->getAggregateForIdAndMonth( $client->id , Carbon::today()->startOfMonth()->toDateString() );

            
        }
    }
}
