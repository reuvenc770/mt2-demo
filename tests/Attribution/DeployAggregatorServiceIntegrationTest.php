<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

use \Log;

class DeployReportCollectionIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;

    const TEST_OFFER_ID = 0;

    public $sut;

    public $testDeploys;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\Attribution\DeployAggregatorService::class );
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
        $this->assertEquals( 3 , \App\Models\AttributionDeployReport::all()->count() );

        foreach ( $this->sut->getRecords() as $currentRow ) {
            switch ( $currentRow[ "deploy_id" ] ) {
                case $this->testDeploys[ 0 ] :
                    $this->assertTrue( $currentRow[ "delivered" ] === 3 );
                    $this->assertTrue( $currentRow[ "opened" ] === 6 );
                    $this->assertTrue( $currentRow[ "clicked" ] === 3 );
                    $this->assertTrue( $currentRow[ "converted" ] === 3 );
                    $this->assertTrue( $currentRow[ "revenue" ] === 6.00 );
                break;

                case $this->testDeploys[ 1 ] :
                    $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                    $this->assertTrue( $currentRow[ "opened" ] === 3 );
                    $this->assertTrue( $currentRow[ "clicked" ] === 1 );
                    $this->assertTrue( $currentRow[ "converted" ] === 1 );
                    $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                    $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                    $this->assertTrue( $currentRow[ "revenue" ] === 2.00 );
                break;

                case $this->testDeploys[ 2 ] :
                    $this->assertTrue( $currentRow[ "delivered" ] === 2 );
                    $this->assertTrue( $currentRow[ "opened" ] === 2 );
                    $this->assertTrue( $currentRow[ "bounced" ] === 1 );
                    $this->assertTrue( $currentRow[ "unsubbed" ] === 1 );
                break;
            }
        }

        unset( $recordReport );
    }

    public function goodPath_dailyRun_testData () {
        $this->testDeploys = [ 1 , 2 , 3 ];

        /**
         *  Email Data
         */
        $emails = [];
        for ( $index = 1 ; $index <= 9 ; $index++ ) {
            $emails[ $index ] = factory( self::EMAIL_CLASS )->create();
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

        for ( $index = 1 , $deployIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            factory( self::ATTR_RECORD_REPORT_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "deploy_id" => $this->testDeploys[ $deployIndex ] ,
                "offer_id" => self::TEST_OFFER_ID ,
                "date" => Carbon::today()->toDateString()
            ] + $recordRows[ $index - 1 ] );

            if ( $index % 3 === 0 ) { $deployIndex++; }
        }
    }
}
