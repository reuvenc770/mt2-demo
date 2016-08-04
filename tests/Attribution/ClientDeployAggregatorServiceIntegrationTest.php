<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

use \Log;

class ClientDeployAggregatorServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const CLIENT_CLASS = \App\Models\Client::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;
    const EMAIL_CLIENT_ASSIGN_CLASS = \App\Models\EmailClientAssignment::class;

    const TEST_OFFER_ID = 0;

    public $sut;

    public $testDeploys;
    public $testClients;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\Attribution\ClientDeployAggregatorService::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_dailyRun () {
        $this->goodPath_dailyRun_testData();

        $this->sut->buildAndSaveReport();

        #Verifying that there is a record for each client.
        $this->assertEquals( 9 , $this->sut->count() );

        #Verifying that there is a record for each client in the DB
        $this->assertEquals( 9 , \App\Models\AttributionClientDeployReport::all()->count() );

        foreach ( $this->sut->getRecords() as $currentRow ) {
            if ( $currentRow[ 'client_id' ] === 1 & $currentRow[ 'deploy_id' ] === 1 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "clicked" ] === 1 );
                $this->assertTrue( $currentRow[ "unsubbed" ] === 2 );
            } elseif ( $currentRow[ 'client_id' ] === 1 & $currentRow[ 'deploy_id' ] === 2 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                $this->assertTrue( $currentRow[ "unsubbed" ] === 2 );
            } elseif ( $currentRow[ 'client_id' ] === 1 & $currentRow[ 'deploy_id' ] === 3 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "clicked" ] === 2 );
                $this->assertTrue( $currentRow[ "converted" ] === 1 );
                $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                $this->assertTrue( $currentRow[ "revenue" ] === 1.50 );
            } elseif ( $currentRow[ 'client_id' ] === 2 & $currentRow[ 'deploy_id' ] === 1 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "clicked" ] === 1 );
                $this->assertTrue( $currentRow[ "unsubbed" ] === 2 );
            } elseif ( $currentRow[ 'client_id' ] === 2 & $currentRow[ 'deploy_id' ] === 2 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                $this->assertTrue( $currentRow[ "opened" ] === 2 );
                $this->assertTrue( $currentRow[ "clicked" ] === 1 );
                $this->assertTrue( $currentRow[ "converted" ] === 1 );
                $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                $this->assertTrue( $currentRow[ "revenue" ] === 0.50 );
            } elseif ( $currentRow[ 'client_id' ] === 2 & $currentRow[ 'deploy_id' ] === 3 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "clicked" ] === 2 );
            } elseif ( $currentRow[ 'client_id' ] === 3 & $currentRow[ 'deploy_id' ] === 1 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 4 );
                $this->assertTrue( $currentRow[ "clicked" ] === 5 );
                $this->assertTrue( $currentRow[ "converted" ] === 1 );
                $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                $this->assertTrue( $currentRow[ "revenue" ] === 1.00 );
            } elseif ( $currentRow[ 'client_id' ] === 3 & $currentRow[ 'deploy_id' ] === 2 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                $this->assertTrue( $currentRow[ "opened" ] === 3 );
                $this->assertTrue( $currentRow[ "clicked" ] === 3 );
                $this->assertTrue( $currentRow[ "converted" ] === 2 );
                $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                $this->assertTrue( $currentRow[ "revenue" ] === 1.00 );
            } elseif ( $currentRow[ 'client_id' ] === 3 & $currentRow[ 'deploy_id' ] === 3 ) {
                $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                $this->assertTrue( $currentRow[ "opened" ] === 5 );
                $this->assertTrue( $currentRow[ "clicked" ] === 6 );
                $this->assertTrue( $currentRow[ "converted" ] === 3 );
                $this->assertTrue( $currentRow[ "revenue" ] === 4.50 );
            }
        }
    }

    public function goodPath_dailyRun_testData () {
        $this->testDeploys = [ 1 , 2 , 3 ];
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
        for ( $index = 1 ; $index <= 27 ; $index++ ) {
            $emails[ $index ] = factory( self::EMAIL_CLASS )->create();
        }

        /**
         * EmailClientAssignment Data
         */
        $clientAssigns = [];
        for ( $index = 1 , $clientIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            $clientAssigns[ $index ] =factory( self::EMAIL_CLIENT_ASSIGN_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "client_id" => $this->testClients[ $clientIndex ]->id ,
                "capture_date" => Carbon::today()->toDateString()
            ] );

            if ( $index % 9 === 0 ) { $clientIndex++; }
        }

        /**
         * AttributionRecordReport Data
         */
        $recordRows = [
            #client 1 : deploy 1
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,

            #client 1 : deploy 2
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 0 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 1 , "revenue" => 0.00 ] ,

            #client 1 : deploy 3
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 1.50 ] ,

            #client 2 : deploy 1
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,

            #client 2 : deploy 2
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.50 ] ,
            [ "delivered" => 0 , "opened" => 0 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 1 , "revenue" => 0.00 ] ,

            #cleint 2 : deploy 3
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,

            #client 3 : deploy 1
            [ "delivered" => 1 , "opened" => 2 , "clicked" => 3 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 2 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 1.00 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 1 , "bounced" => 0 , "revenue" => 0.00 ] ,

            #client 3 : deploy 2
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.50 ] ,
            [ "delivered" => 1 , "opened" => 2 , "clicked" => 2 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 0.50 ] ,
            [ "delivered" => 0 , "opened" => 0 , "clicked" => 0 , "converted" => 0 , "unsubbed" => 0 , "bounced" => 1 , "revenue" => 0.00 ] ,

            #client 3 : deploy 3
            [ "delivered" => 1 , "opened" => 3 , "clicked" => 3 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 1.50 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 1 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 1.50 ] ,
            [ "delivered" => 1 , "opened" => 1 , "clicked" => 2 , "converted" => 1 , "unsubbed" => 0 , "bounced" => 0 , "revenue" => 1.50 ]
        ];

        for ( $index = 1 , $deployIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            factory( self::ATTR_RECORD_REPORT_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "deploy_id" => $this->testDeploys[ $deployIndex ] ,
                "offer_id" => self::TEST_OFFER_ID ,
                "date" => Carbon::today()->toDateString()
            ] + $recordRows[ $index - 1 ] );

            if ( $index % 3 === 0 ) { $deployIndex++; }
            if ( $index % 9 === 0 ) { $deployIndex = 0; }
        }
    }
}
