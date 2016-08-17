<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ThreeMonthReportTestDataSeeder extends Seeder
{
    const CLIENT_CLASS = \App\Models\Client::class;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testClients = []; 
        for ( $index = 0 ; $index < 9 ; $index++ ) { 
            $testClients[ $index ] = factory( self::CLIENT_CLASS )->create();
        }   

        $recordCount = 18; 
        $totalRecords = 0;
        $currentId = 11; 
        $dateType = 0;  

        $listOwnerFeedIndex = [ 11 => 0 , 12 => 4 , 13 => 6 ];
        $listOwnerFeedCount = [ 11 => 4 , 12 => 2 , 13 => 3 ];

        while ( $totalRecords < $recordCount ) { 
            $date = Carbon::today()->toDateString();

            if ( $dateType == 1 ) { 
                $date = Carbon::today()->subMonths( 1 )->toDateString();
            } elseif ( $dateType == 2 ) { 
                $date = Carbon::today()->subMonths( 2 )->toDateString();
            }   

            $currentListOwner = factory( \App\Models\AttributionListOwnerReport::class )->create( [
                "client_stats_grouping_id" => $currentId ,
                "standard_revenue" => mt_rand( 10.00 , 100.00 ) , 
                "cpm_revenue" => 0.00 , 
                "mt1_uniques" => mt_rand( 1000 , 10000 ) , 
                "mt2_uniques" => mt_rand( 1000 , 10000 ) , 
                "date" => $date 
            ] );

            $totalRecords++;

            if ( $totalRecords % 2 == 0 ) { 
                $clientCount = $listOwnerFeedCount[ $currentListOwner->client_stats_grouping_id ];
                $clientIndex = $listOwnerFeedIndex[ $currentListOwner->client_stats_grouping_id ];
                for ( $feedIndex = $clientIndex , $feedCount = 0 ; $feedCount < $clientCount ; $feedCount++, $feedIndex++  ) {
                    factory( \App\Models\AttributionClientReport::class )->create( [
                        "client_id" => $testClients[ $feedIndex ]->id ,
                        "revenue" => ( $currentListOwner->standard_revenue * 1000 / $clientCount ) / 1000,
                        "mt1_uniques" => $currentListOwner->mt1_uniques / $clientCount ,
                        "mt2_uniques" => $currentListOwner->mt2_uniques / $clientCount ,
                        "date" => $date
                    ] );
                }

                $dateType++;
            }

            if ( $totalRecords % 6 === 0 ) {
                $currentId++;
                $dateType = 0;
            }
        }
    }
}
