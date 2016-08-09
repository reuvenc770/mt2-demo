<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Services;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class TrackingDataServiceIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App\Factories\APIFactory::createTrackingApiService(
            'Cake' ,
            \Carbon\Carbon::now()->subDays( 1 )->toDateString() ,
            \Carbon\Carbon::now()->toDateString()
        );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_successfulApiCall () {
        $records = $this->sut->retrieveApiStats( [ "recordstats" => 1 ] );

        $this->assertTrue( sizeof( $records ) > 0 );
    }
}
