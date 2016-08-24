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

<<<<<<< HEAD
    const CLIENT_CLASS = \App\Models\Feed::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;
    const EMAIL_CLIENT_ASSIGN_CLASS = \App\Models\EmailFeedAssignment::class;
=======
    const CLIENT_CLASS = \App\Models\Client::class;
    const ATTR_FEED_REPORT_CLASS = \App\Models\AttributionFeedReport::class;
>>>>>>> master

    const LISTOWNER_ID_1 = 11; #client 1, 2, 3, & 4 are assigned to this in dev
    const LISTOWNER_ID_2 = 12; #client 5 & 6 are assigned to this in dev
    const LISTOWNER_ID_3 = 13; #client 7, 8, & 9 are assigned to this in dev

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\Attribution\ClientAggregatorService::class );
    }

    public function tearDown() {
        unset( $this->sut );

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

    public function goodPath_dailyRun_testData () {
<<<<<<< HEAD
        /**
         * Feed Data
         */
=======
>>>>>>> master
        $this->testClients = [];
        for ( $index = 0 ; $index < 9 ; $index++ ) {
            $this->testClients[ $index ] = factory( self::CLIENT_CLASS )->create();
        }

<<<<<<< HEAD
        /**
         *  Email Data
         */
        $emails = [];
        for ( $index = 1 ; $index <= 9 ; $index++ ) {
            $emails[ $index ] = factory( self::EMAIL_CLASS )->create();
        }

        /**
         * EmailFeedAssignment Data
         */
        $clientAssigns = [];
        for ( $index = 1 , $clientIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            $clientAssigns[ $index ] =factory( self::EMAIL_CLIENT_ASSIGN_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "feed_id" => $this->testClients[ $clientIndex ]->id ,
                "capture_date" => Carbon::today()->toDateString()
            ] );

            if ( $index % 3 === 0 ) { $clientIndex++; }
        }

        /**
         * AttributionRecordReport Data
         */
        $recordRows = [
            [ "delivered" => 1 , "opened" => 2 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 2.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 2.00 ] ,
            [ "delivered" => 1 , "opened" => 3 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 2.00 ] ,
            [ "delivered" => 1 , "opened" => 2 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 2.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 0 , "opened" => 0 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 1 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 0 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 2 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 0 , "opened" => 0 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 1 , "revenue" => 0.00 ] ,
        ];

        for ( $index = 1 ; $index <= count( $emails ) ; $index++ ) {
            factory( self::ATTR_RECORD_REPORT_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "deploy_id" => self::TEST_DEPLOY_ID ,
                "offer_id" => self::TEST_OFFER_ID ,
=======
        $this->testClientRecords = [];
        foreach ( $this->testClients as $clientIndex => $client ) {
            $this->testClientRecords[ $clientIndex ] = factory( self::ATTR_FEED_REPORT_CLASS )->create( [
                "client_id" => $client->id ,
                "revenue" => mt_rand( 100 , 200 ) ,
                "mt1_uniques" => mt_rand( 1000 , 2000 ) ,
                "mt2_uniques" => mt_rand( 800 , 1500 ) , #mt2 uniques are lower since we imported only some email records
>>>>>>> master
                "date" => Carbon::today()->toDateString()
            ] );
        }
    }
}
