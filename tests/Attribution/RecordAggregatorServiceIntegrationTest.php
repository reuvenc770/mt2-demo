<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

use \Log;

class RecordAggregatorServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const TEST_DEPLOY_ID = 1;

    public $sut;

    public $testEmail1, $testEmail2, $testEmail3;

    public function setUp () {
        parent::setUp();

        $this->sut = \App\Factories\DataProcessingFactory::create( 'PopulateAttributionRecordReport' ); 
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_dailyRun () {
        $this->goodPath_dailyRun_testData();
        
        $this->sut->extract();
        $this->sut->load();

        foreach ( \App\Models\AttributionRecordReport::all() as $currentRow ) {
            switch ( $currentRow[ 'email_id' ] ) {
                
                case $this->testEmail1->id : #Verifying that tally is correct for first email
                    $this->assertEquals( 1 , $currentRow[ 'delivered' ] );
                    $this->assertEquals( 1 ,  $currentRow[ 'opened' ] );
                    $this->assertEquals( 1 , $currentRow[ 'unsubbed' ] );
                break;

                case $this->testEmail2->id : #Verifying that tally is correct for second email
                    $this->assertEquals( 1 , $currentRow[ 'bounced' ] );
                break;

                case $this->testEmail3->id : #Verifying that tally is correct for third email
                    $this->assertEquals( 1 , $currentRow[ 'delivered' ] );
                    $this->assertEquals( 1 , $currentRow[ 'opened' ] );
                    $this->assertEquals( 1 , $currentRow[ 'clicked' ] );
                    $this->assertEquals( 2 , $currentRow[ 'converted' ] );
                    $this->assertEquals( 4.258 , $currentRow[ 'revenue' ] );
                break;
            }
        }

        $this->assertEquals( 7 , \App\Models\EtlPickup::where( 'name' , 'PopulateAttributionRecordReport' )->pluck( 'stop_point' )->pop() );
    }

    public function goodPath_dailyRun_testData () {
        /**
         * ETL Pickup
         */

        factory( \App\Models\EtlPickup::class )->create( [ 'name' => 'PopulateAttributionRecordReport' , 'stop_point' => 1 ] );

        /**
         * Email Data
         */
        $this->testEmail1 = factory( \App\Models\Email::class )->create();
        $this->testEmail2 = factory( \App\Models\Email::class )->create();
        $this->testEmail3 = factory( \App\Models\Email::class )->create();

        /**
         * Suppression Data
         */
        factory( \App\Models\Suppression::class )->create( [
            'email_address' => $this->testEmail1->email_address ,
            'type_id' => 1 ,
            'esp_internal_id' => 10 ,
            'date' => Carbon::today()->toDateString()
        ] );

        factory( \App\Models\Suppression::class )->create( [
            'email_address' => $this->testEmail2->email_address ,
            'type_id' => 2 ,
            'esp_internal_id' => 10 ,
            'date' => Carbon::today()->toDateString()
        ] );

        /**
         * StandardReport Data for Mapping Deploy ID
         */
        factory( \App\Models\StandardReport::class )->create( [
            "esp_internal_id" => 10 ,
            "esp_account_id" => 1 ,
            "external_deploy_id" => self::TEST_DEPLOY_ID
        ] );

        /**
         * EmailAction Data 
         */
        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail1->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 4 ,
            'datetime' => Carbon::today()->addSeconds( 1 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail1->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 1 ,
            'datetime' => Carbon::today()->addSeconds( 2 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail3->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 4 ,
            'datetime' => Carbon::today()->addSeconds( 4 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail3->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 1 ,
            'datetime' => Carbon::today()->addSeconds( 5 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail3->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 2 ,
            'datetime' => Carbon::today()->addSeconds( 6 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail3->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 3 ,
            'datetime' => Carbon::today()->addSeconds( 7 )->toDateTimeString() ,
        ] );

        factory( \App\Models\EmailAction::class )->create( [
            'email_id' => $this->testEmail3->id ,
            'deploy_id' => self::TEST_DEPLOY_ID ,
            'action_id' => 3 ,
            'datetime' => Carbon::today()->addSeconds( 8 )->toDateTimeString() ,
        ] );

        /**
         * Conversion Data
         */
        factory( \App\Models\Cake\CakeConversion::class )->create( [
            'email_id' => $this->testEmail3->id ,
            's1' => 1 ,
            'price_received' => 3.00 ,
            'conversion_date' => Carbon::today()->addSeconds( 9 )->toDateTimeString() ,
        ] );

        factory( \App\Models\Cake\CakeConversion::class )->create( [
            'email_id' => $this->testEmail3->id ,
            's1' => 1 ,
            'price_received' => 1.258 ,
            'conversion_date' => Carbon::today()->addSeconds( 10 )->toDateTimeString() ,
        ] );
    }
}
