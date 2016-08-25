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

    const FEED_CLASS = \App\Models\Feed::class;
    const EMAIL_CLASS = \App\Models\Email::class;
    const ATTR_RECORD_REPORT_CLASS = \App\Models\AttributionRecordReport::class;
    const EMAIL_FEED_ASSIGN_CLASS = \App\Models\EmailFeedAssignment::class;
    const EMAIL_FEED_INSTANCE_CLASS = \App\Models\EmailFeedInstance::class;

    const TEST_DEPLOY_ID = 1;
    const TEST_OFFER_ID = 0;

    public $sut;

    public $testFeeds;

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
            switch ( $currentRow[ "feed_id" ] ) {
                case $this->testFeeds[ 0 ]->id :
                    $this->assertTrue( $currentRow[ "revenue" ] === 4.00 );
                    $this->assertTrue( $currentRow[ "mt2_uniques" ] === 2 );
                break;

                case $this->testFeeds[ 1 ]->id :
                    $this->assertTrue( $currentRow[ "revenue" ] === 6.00 );
                    $this->assertTrue( $currentRow[ "mt2_uniques" ] === 3 );
                break;

                case $this->testFeeds[ 2 ]->id :
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
        $this->testFeeds = [];
        for ( $index = 0 ; $index < 3 ; $index++ ) {
            $this->testFeeds[ $index ] = factory( self::FEED_CLASS )->create();
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
        $feedAssigns = [];
        for ( $index = 1 , $feedIndex = 0 ; $index <= count( $emails ) ; $index++ ) {
            $date = Carbon::today()->toDateString();
    
            if (
                ( $feedIndex === 0 && $index % 3 === 0 )
                || ( $feedIndex === 2 ) 
            ) {
                $date = Carbon::yesterday()->toDateString();
            }

            $feedAssigns[ $index ] =factory( self::EMAIL_FEED_ASSIGN_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "feed_id" => $this->testFeeds[ $feedIndex ]->id ,
                "capture_date" => $date 
            ] );

            factory( self::EMAIL_FEED_INSTANCE_CLASS )->create( [
                "email_id" => $emails[ $index ]->id ,
                "feed_id" => $this->testFeeds[ $feedIndex ]->id ,
                "capture_date" => $date 
            ] );

            if ( $index % 3 === 0 ) { $feedIndex++; }
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
