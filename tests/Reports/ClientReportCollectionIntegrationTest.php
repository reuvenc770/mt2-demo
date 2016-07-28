<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

use \Log;

class ClientReportCollectionIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const CLIENT_CLASS = \App\Models\Client::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;
    const EMAIL_CLIENT_ASSIGN_CLASS = \App\Models\EmailClientAssignment::class;

    const TEST_DEPLOY_ID = 1;
    const TEST_OFFER_ID = 0;

    public $sut;

    public $testClients;

    public function setUp () {
        parent::setUp();

        $this->generateClientReportCollection();
    }

    public function generateClientReportCollection () {
        $this->sut = \App::make( \App\Reports\ClientReportCollection::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_dailyRun () {
        $this->goodPath_dailyRun_testData();

        $recordReport = \App::make( \App\Models\AttributionRecordReport::class );
        $emailClient = \App::make( \App\Services\EmailClientAssignmentService::class );

        $this->sut->injectRecordReportModel( $recordReport );
        $this->sut->injectEmailClientAssignmentService( $emailClient );

        $this->sut->buildAndSaveReport();

        #Verifying that there is a record for each client.
        $this->assertEquals( 3 , $this->sut->count() );

        #Verifying that there is a record for each client in the DB
        $this->assertEquals( 3 , \App\Models\AttributionClientReport::all()->count() );

        foreach ( $this->sut as $currentRow ) {
            switch ( $currentRow[ "client_id" ] ) {
                case $this->testClients[ 0 ]->id :
                    $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                    $this->assertTrue( $currentRow[ "opened" ] === 6 );
                    $this->assertTrue( $currentRow[ "clicked" ] === 3 );
                    $this->assertTrue( $currentRow[ "converted" ] === 3 );
                    $this->assertTrue( $currentRow[ "revenue" ] === 6.00 );
                break;

                case $this->testClients[ 1 ]->id :
                    $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                    $this->assertTrue( $currentRow[ "opened" ] === 3 );
                    $this->assertTrue( $currentRow[ "clicked" ] === 1 );
                    $this->assertTrue( $currentRow[ "converted" ] === 1 );
                    $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                    $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                    $this->assertTrue( $currentRow[ "revenue" ] === 2.00 );
                break;

                case $this->testClients[ 2 ]->id :
                    $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                    $this->assertTrue( $currentRow[ "opened" ] === 2 );
                    $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                    $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                break;
            }
        }

        unset( $recordReport );
        unset( $emailClient );
    }

    public function test_goodPath_loadTodaysItems () {
        /**
         * Get and save records to DB
         */
        $this->goodPath_dailyRun_testData();

        $recordReport = \App::make( \App\Models\AttributionRecordReport::class );
        $emailClient = \App::make( \App\Services\EmailClientAssignmentService::class );

        $this->sut->injectRecordReportModel( $recordReport );
        $this->sut->injectEmailClientAssignmentService( $emailClient );

        $this->sut->buildAndSaveReport();

        /**
         * Get new collection and load items for today
         */
        unset( $this->sut );
        $this->generateClientReportCollection();

        $this->sut->load();

        #Verifying that report has records laoded from DB
        $this->assertEquals( 3 , $this->sut->count() );
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
            $clientAssigns[ $index ] =factory( self::EMAIL_CLIENT_ASSIGN_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "client_id" => $this->testClients[ $clientIndex ]->id ,
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
                "date" => Carbon::today()->toDateString()
            ] + $recordRows[ $index - 1 ] );
        }
    }
}
