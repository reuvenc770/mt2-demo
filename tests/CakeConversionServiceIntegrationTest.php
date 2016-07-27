<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

class CakeConversionServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\CakeConversionService::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_retrieveTodaysConversions () {
        $this->goodPath_retrieveTodaysConversions_testData();

        $records = $this->sut->getByDate();

        $this->assertEquals( 2 , count( $records ) );
    }

    public function goodPath_retrieveTodaysConversions_testData () {
        factory( \App\Models\Cake\CakeConversion::class )->create( [
            "email_id" => 1 ,
            "s1" => 1 ,
            "price_received" => 3.00 ,
            "conversion_date" => Carbon::now()->toDateTimeString()
        ] );

        factory( \App\Models\Cake\CakeConversion::class )->create( [
            "email_id" => 1 ,
            "s1" => 1 ,
            "price_received" => 1.00 ,
            "conversion_date" => Carbon::now()->addMinutes( 2 )->toDateTimeString()
        ] );
    }
}
