<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

class ClientAggregatorServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const FEED_CLASS = \App\Models\Feed::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_FEED_REPORT_CLASS = \App\Models\AttributionFeedReport::class;

    const LISTOWNER_ID_1 = 11; #client 1, 2, 3, & 4 are assigned to this in dev
    const LISTOWNER_ID_2 = 12; #client 5 & 6 are assigned to this in dev
    const LISTOWNER_ID_3 = 13; #client 7, 8, & 9 are assigned to this in dev

    const TEST_MODEL_ID = 5;

    public $sut;

    public function setUp () {
        parent::setUp();

        \App\Repositories\Attribution\FeedReportRepo::generateTempTable( self::TEST_MODEL_ID );
        \App\Repositories\Attribution\ClientReportRepo::generateTempTable( self::TEST_MODEL_ID );

        $this->sut = \App::make( \App\Services\Attribution\ClientAggregatorService::class );
    }

    public function tearDown() {
        unset( $this->sut );

        \App\Repositories\Attribution\FeedReportRepo::dropTempTable( self::TEST_MODEL_ID );
        \App\Repositories\Attribution\ClientReportRepo::dropTempTable( self::TEST_MODEL_ID );

        parent::tearDown();
    }

    public function test_goodPath_dailyRun  () {
        $this->goodPath_dailyRun_testData();

        $this->sut->buildAndSaveReport();

        #Verifying that there is a record for each list owner
        $this->assertEquals( 3 , $this->sut->count() );

        #Verifying that there is a record for each list owner in the Db
        $this->assertEquals( 3 , \App\Models\AttributionClientReport::all()->count() );

        foreach ( $this->sut->getRecords() as $currentRow ) {
            switch ( $currentRow[ 'client_stats_grouping_id' ] ) {
                case self::LISTOWNER_ID_1 :
                    $this->assertEquals(
                        $this->testClientRecords[ 0 ][ 'revenue' ] + $this->testClientRecords[ 1 ][ 'revenue' ] + $this->testClientRecords[ 2 ][ 'revenue' ] + $this->testClientRecords[ 3 ][ 'revenue' ] ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 0 ][ 'mt1_uniques' ] + $this->testClientRecords[ 1 ][ 'mt1_uniques' ] + $this->testClientRecords[ 2 ][ 'mt1_uniques' ] + $this->testClientRecords[ 3 ][ 'mt1_uniques' ] ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 0 ][ 'mt2_uniques' ] + $this->testClientRecords[ 1 ][ 'mt2_uniques' ] + $this->testClientRecords[ 2 ][ 'mt2_uniques' ] + $this->testClientRecords[ 3 ][ 'mt2_uniques' ] ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 

                case self::LISTOWNER_ID_2 :
                    $this->assertEquals(
                        $this->testClientRecords[ 4 ][ 'revenue' ] + $this->testClientRecords[ 5 ][ 'revenue' ] ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 4 ][ 'mt1_uniques' ] + $this->testClientRecords[ 5 ][ 'mt1_uniques' ] ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 4 ][ 'mt2_uniques' ] + $this->testClientRecords[ 5 ][ 'mt2_uniques' ] ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 

                case self::LISTOWNER_ID_3 :
                    $this->assertEquals(
                        $this->testClientRecords[ 6 ][ 'revenue' ] + $this->testClientRecords[ 7 ][ 'revenue' ] + $this->testClientRecords[ 8 ][ 'revenue' ] ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 6 ][ 'mt1_uniques' ] + $this->testClientRecords[ 7 ][ 'mt1_uniques' ] + $this->testClientRecords[ 8 ][ 'mt1_uniques' ] ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 6 ][ 'mt2_uniques' ] + $this->testClientRecords[ 7 ][ 'mt2_uniques' ] + $this->testClientRecords[ 8 ][ 'mt2_uniques' ] ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 
            }
        }
    }

    public function test_goodPath_modelRun () {
        $this->goodPath_dailyRun_testData( true );

        $this->sut->setModelId( self::TEST_MODEL_ID );

        $this->sut->buildAndSaveReport();

        #Verifying that there is a record for each list owner
        $this->assertEquals( 3 , $this->sut->count() );

        #Verifying that there is a record for each list owner in the Db
        $this->assertEquals( 3 , count( \DB::connection( 'attribution' )->table( \App\Models\AttributionClientReport::BASE_TABLE_NAME . self::TEST_MODEL_ID )->get() ) );

        foreach ( $this->sut->getRecords() as $currentRow ) {
            switch ( $currentRow[ 'client_stats_grouping_id' ] ) {
                case self::LISTOWNER_ID_1 :
                    $this->assertEquals(
                        $this->testClientRecords[ 0 ]->revenue + $this->testClientRecords[ 1 ]->revenue + $this->testClientRecords[ 2 ]->revenue + $this->testClientRecords[ 3 ]->revenue ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 0 ]->mt1_uniques + $this->testClientRecords[ 1 ]->mt1_uniques + $this->testClientRecords[ 2 ]->mt1_uniques + $this->testClientRecords[ 3 ]->mt1_uniques ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 0 ]->mt2_uniques + $this->testClientRecords[ 1 ]->mt2_uniques + $this->testClientRecords[ 2 ]->mt2_uniques + $this->testClientRecords[ 3 ]->mt2_uniques ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 

                case self::LISTOWNER_ID_2 :
                    $this->assertEquals(
                        $this->testClientRecords[ 4 ]->revenue + $this->testClientRecords[ 5 ]->revenue ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 4 ]->mt1_uniques + $this->testClientRecords[ 5 ]->mt1_uniques ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 4 ]->mt2_uniques + $this->testClientRecords[ 5 ]->mt2_uniques ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 

                case self::LISTOWNER_ID_3 :
                    $this->assertEquals(
                        $this->testClientRecords[ 6 ]->revenue + $this->testClientRecords[ 7 ]->revenue + $this->testClientRecords[ 8 ]->revenue ,  
                        $currentRow[ "standard_revenue" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 6 ]->mt1_uniques + $this->testClientRecords[ 7 ]->mt1_uniques + $this->testClientRecords[ 8 ]->mt1_uniques ,  
                        $currentRow[ "mt1_uniques" ]
                    );

                    $this->assertEquals(
                        $this->testClientRecords[ 6 ]->mt2_uniques + $this->testClientRecords[ 7 ]->mt2_uniques + $this->testClientRecords[ 8 ]->mt2_uniques ,  
                        $currentRow[ "mt2_uniques" ]
                    );
                break; 
            }
        }
    }

    public function goodPath_dailyRun_testData ( $modelRun = false ) {
        /**
         * Feed Data
         */

        $this->testFeeds = [];
        for ( $index = 0 ; $index < 9 ; $index++ ) {
            $this->testFeeds[ $index ] = factory( self::FEED_CLASS )->create();
        }

        $this->testClientRecords = [];

        if ( $modelRun ) {
            foreach ( $this->testFeeds as $clientIndex => $client ) {
                 $currentId = \DB::connection( 'attribution' )->table( \App\Models\AttributionFeedReport::BASE_TABLE_NAME . self::TEST_MODEL_ID )->insertGetId( [
                    "feed_id" => $client->id ,
                    "revenue" => mt_rand( 100 , 200 ) ,
                    "mt1_uniques" => mt_rand( 1000 , 2000 ) ,
                    "mt2_uniques" => mt_rand( 800 , 1500 ) , #mt2 uniques are lower since we imported only some email records
                    "date" => Carbon::today()->toDateString()
                ] );

                $this->testClientRecords[ $clientIndex ] = \DB::connection( 'attribution' )->table( \App\Models\AttributionFeedReport::BASE_TABLE_NAME . self::TEST_MODEL_ID )->find( $currentId );
            }
        } else {
            foreach ( $this->testFeeds as $clientIndex => $client ) {
                $this->testClientRecords[ $clientIndex ] = factory( self::ATTR_FEED_REPORT_CLASS )->create( [
                    "feed_id" => $client->id ,
                    "revenue" => mt_rand( 100 , 200 ) ,
                    "mt1_uniques" => mt_rand( 1000 , 2000 ) ,
                    "mt2_uniques" => mt_rand( 800 , 1500 ) , #mt2 uniques are lower since we imported only some email records
                    "date" => Carbon::today()->toDateString()
                ] );
            }
        }
    }
}
