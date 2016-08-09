<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Services;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class RetrieveTrackingDataJobIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Jobs\RetrieveTrackingDataJob(
            'Cake' ,
            \Carbon\Carbon::now()->subDay( 1 )->toDateString() ,
            \Carbon\Carbon::now()->toDateString() ,
            str_random( 16 ) ,
            \App\Jobs\RetrieveTrackingDataJob::PROCESS_TYPE_RECORD
        );
    }

    public function tearDown () {
        unset( $this->sut );
    }

    public function test_successfulRecordReportUpdate () {
        $this->sut->handle();

        $this->assertTrue( \App\Models\AttributionRecordReport::all()->count() > 0 );
    }
}
