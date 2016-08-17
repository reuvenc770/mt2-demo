<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Collections;

use Illuminate\Support\Collection;
use Carbon\Carbon;

class ThreeMonthReportCollection extends Collection {
    protected $clientReportRepo;
    protected $listOwnerReportRepo;
    protected $clientStatsGroupingService;
    protected $clientService;

    protected $recordCollector = [];
    protected $totalsCollector = [];
    protected $dates = [];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );

        $this->clientReportRepo = \App::make( \App\Repositories\Attribution\ClientReportRepo::class );
        $this->listOwnerReportRepo = \App::make( \App\Repositories\Attribution\ListOwnerReportRepo::class );
        $this->clientStatsGroupingService = \App::make( \App\Services\MT1Services\ClientStatsGroupingService::class );
        $this->clientService = \App::make( \App\Services\MT1Services\ClientService::class );

        $this->dates = [
            "two_months_ago" => Carbon::today()->subMonths( 2 )->toDateString() ,
            "last_month" => Carbon::today()->subMonths( 1 )->toDateString() ,
            "current_month" => Carbon::today()->toDateString()
        ];
    }
    
    public function load () {
        $listOwnerList = $this->listOwnerReportRepo->getListOwnersFromLastThreeMonths();

        foreach ( $listOwnerList as $listOwner ) {
            $this->recordCollector []= $this->getListOwnerRow( $listOwner->id );
            
            $clientFeeds = $this->clientService->getClientFeedsForListOwner( $listOwner->id );
            foreach ( $clientFeeds as $feedId ) {
                $this->recordCollector []= $this->getFeedRow( $feedId );
            }
        }

        parent::__construct( $this->recordCollector );
    }

    public function getRecordsAndTotals ( $options = [] ) {
        return [ "records" => $this , "totals" => $this->totalsCollector ];
    }

    protected function getListOwnerRow ( $listOwnerId ) {
        $currentListOwnerRow = [];

        $currentListOwnerRow[ 'client_stats_grouping_id' ] = $listOwnerId;
        foreach  ( $this->dates as $dateKey => $date ) {
            $listOwnerRecord = $this->listOwnerReportRepo->getAggregateForIdAndMonth( $listOwnerId , $date );    

            $currentListOwnerRow[ $dateKey ][ 'standard_revenue' ] = $listOwnerRecord->standard_revenue;
            $currentListOwnerRow[ $dateKey ][ 'cpm_revenue' ] = $listOwnerRecord->cpm_revenue;
            $currentListOwnerRow[ $dateKey ][ 'mt1_uniques' ] = $listOwnerRecord->mt1_uniques;
            $currentListOwnerRow[ $dateKey ][ 'mt2_uniques' ] = $listOwnerRecord->mt1_uniques;

            $this->updateTotals( $dateKey , $listOwnerRecord );
        }

        return $currentListOwnerRow;
    }

    protected function updateTotals ( $dateKey , $listOwnerRecord ) {
        if ( !isset( $this->totalsCollector[ $dateKey ] ) ) {
            $this->totalsCollector[ $dateKey ] = [
                'standard_revenue' => 0 ,
                'cpm_revenue' => 0 ,
                'mt1_uniques' => 0 ,
                'mt2_uniques' => 0
            ];
        }

        $this->totalsCollector[ $dateKey ][ 'standard_revenue' ] += $listOwnerRecord->standard_revenue;
        $this->totalsCollector[ $dateKey ][ 'cpm_revenue' ] += $listOwnerRecord->cpm_revenue;
        $this->totalsCollector[ $dateKey ][ 'mt1_uniques' ] += $listOwnerRecord->mt1_uniques;
        $this->totalsCollector[ $dateKey ][ 'mt2_uniques' ] += $listOwnerRecord->mt1_uniques;
    }

    protected function getFeedRow ( $feedId ) {
        $currentClientRow = [];

        $currentClientRow[ 'client_id' ] = $feedId;
        foreach ( $this->dates as $dateKey => $date ) {
            $feedRecord = $this->clientReportRepo->getAggregateForIdAndMonth( $feedId , $date );

            $currentClientRow[ $dateKey ][ 'standard_revenue' ] = $feedRecord->revenue;
            $currentClientRow[ $dateKey ][ 'mt1_uniques' ] = $feedRecord->mt1_uniques;
            $currentClientRow[ $dateKey ][ 'mt2_uniques' ] = $feedRecord->mt1_uniques;
        }

        return $currentClientRow;
    }

    #Need these methods since the controller calls this method
    public function &forPage ( $page , $perPage ) { return $this; }
    public function config () {}
    public function recordCount () { return 0; }
}
