<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

class FeedAggregatorServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const CLIENT_CLASS = \App\Models\Client::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;
    const EMAIL_CLIENT_ASSIGN_CLASS = \App\Models\EmailClientAssignment::class;
    const EMAIL_CLIENT_INSTANCE_CLASS = \App\Models\EmailClientInstance::class;

    const TEST_DEPLOY_ID = 1;
    const TEST_OFFER_ID = 0;

    public $sut;

    public $testClients;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\Attribution\FeedAggregatorService::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_dailyRun () {
        $this->goodPath_dailyRun_testData();

        $this->sut->buildAndSaveReport();

        #Verifying that there is a record for each client.
        $this->assertEquals( 3 , $this->sut->count() );

        #Verifying that there is a record for each client in the DB
        $this->assertEquals( 3 , \App\Models\AttributionFeedReport::all()->count() );
        
        foreach ( $this->sut->getRecords() as $currentRow ) {
            switch ( $currentRow[ "client_id" ] ) {
                case $this->testClients[ 0 ]->id :
                    $this->assertTrue( $currentRow[ "revenue" ] === 4.00 );
                    $this->assertTrue( $currentRow[ "mt2_uniques" ] === 2 );
                break;

                case $this->testClients[ 1 ]->id :
                    $this->assertTrue( $currentRow[ "revenue" ] === 6.00 );
                    $this->assertTrue( $currentRow[ "mt2_uniques" ] === 3 );
                break;

                case $this->testClients[ 2 ]->id :
                    $this->assertTrue( $currentRow[ "revenue" ] === 0.00 );
                    $this->assertTrue( $currentRow[ "mt2_uniques" ] === 0 );
                break;
            }
        }
    }

    public function goodPath_dailyRun_testData () {
        /**
         * Client Data
         */
        $this->testClients = [];
        for ( $index = 0 ; $index < 3 ; $index++ ) {
            $this->testClients[ $index ] = factory( self::CLIENT_CLASS )->create();
        }

        /**
         *  Email Data
         */
        $emails = [];
        for ( $index = 1 ; $index <= 9 ; $index++ ) {
            $emails[ $index ] = factory( self::EMAIL_CLASS )->create();
        }

        /**
         * EmailClientAssignment Data
         */
        $clientAssigns = [];
        for ( $index = 1 , $clientIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            $date = Carbon::today()->toDateString();
    
            if (
                ( $clientIndex === 0 && $index % 3 === 0 )
                || ( $clientIndex === 2 ) 
            ) {
                $date = Carbon::yesterday()->toDateString();
            }

            $clientAssigns[ $index ] =factory( self::EMAIL_CLIENT_ASSIGN_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "client_id" => $this->testClients[ $clientIndex ]->id ,
                "capture_date" => $date 
            ] );

            factory( self::EMAIL_CLIENT_INSTANCE_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "client_id" => $this->testClients[ $clientIndex ]->id ,
                "capture_date" => $date 
            ] );

            if ( $index % 3 === 0 ) { $clientIndex++; }
        }

        /**
         * AttributionRecordReport Data
         */
        $recordRows = [
            [ "revenue" => 2.00 ] ,
            [ "revenue" => 2.00 ] ,
            [ "revenue" => 0.00 ] ,
            [ "revenue" => 2.00 ] ,
            [ "revenue" => 2.00 ] ,
            [ "revenue" => 2.00 ] ,
            [ "revenue" => 0.00 ] ,
            [ "revenue" => 0.00 ] ,
            [ "revenue" => 0.00 ] ,
        ];

        for ( $index = 1 ; $index <= count( $emails ) ; $index++ ) {
            factory( self::ATTR_RECORD_REPORT_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "deploy_id" => self::TEST_DEPLOY_ID ,
                "offer_id" => self::TEST_OFFER_ID ,
                "date" => Carbon::today()->toDateString()
            ] + $recordRows[ $index - 1 ] );
        }
    }
}
