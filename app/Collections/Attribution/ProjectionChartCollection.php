<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections\Attribution;

use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Repositories\Attribution\ClientReportRepo;
use App\Services\MT1Services\ClientStatsGroupingService;

class ProjectionChartCollection extends Collection {
    protected $clientReport;
    protected $clientStatsGroupingService;

    protected $chartData = [
        "live" => [] , 
        "model" => []
    ];

    public function __construct (
        ClientReportRepo $clientReport ,
        ClientStatsGroupingService $clientStatsGroupingService
    ) {
        $this->clientReport = $clientReport;
        $this->clientStatsGroupingService = $clientStatsGroupingService;
    }

    public function getChartData ( $modelId ) {
        $clientList = $this->clientReport->getClientsForDateRange(
            Carbon::today()->startOfMonth()->toDateString() ,
            Carbon::today()->endOfMonth()->toDateString()
        );

        foreach ( $clientList as $client ) {
            $clientName = $this->clientStatsGroupingService->getListOwnerName( $client->id );

            if ( $clientName == null ) {
                $clientName = 'Untitled Client';
            } 

            $this->clientReport->switchToLiveTable();
            $clientLiveRecord = $this->clientReport->getAggregateForIdAndMonth( $client->id , Carbon::today()->startOfMonth()->toDateString() );

            $this->chartData[ 'live' ] []= [ $clientName , $clientLiveRecord->standard_revenue + $clientLiveRecord->cpm_revenue ];

            $this->clientReport->setModelId( $modelId );
            $clientModelRecord = $this->clientReport->getAggregateForIdAndMonth( $client->id , Carbon::today()->startOfMonth()->toDateString() );

            $this->chartData[ 'model' ] []= [ $clientName , $clientModelRecord->standard_revenue + $clientModelRecord->cpm_revenue ];
        }

        $this->chartData[ 'live' ] = collect( $this->chartData[ 'live' ] )->sortBy( 0 )->toArray();
        $this->chartData[ 'model' ] = collect( $this->chartData[ 'model' ] )->sortBy( 0 )->toArray();

        array_unshift( $this->chartData[ 'live' ] , [ 'Client Name' , 'Live Revenue' ] );
        array_unshift( $this->chartData[ 'model' ] , [ 'Client Name' , 'Model Revenue' ] );

        return response()->json( $this->chartData );
    }
}
