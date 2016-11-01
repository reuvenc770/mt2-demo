<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class RerunAttributionAggregatorListenerIntegrationTest extends TestCase {
    use DatabaseMigrations;

    const TEST_MODEL_ID = 5;

    public $sut;

    public function setUp () {
        parent::setUp();

        \App\Repositories\Attribution\FeedReportRepo::generateTempTable( self::TEST_MODEL_ID );
        \App\Repositories\Attribution\ClientReportRepo::generateTempTable( self::TEST_MODEL_ID );

        $this->sut = \App::make( \App\Listeners\RerunAttributionAggregator::class );
    }

    public function tearDown() {
        unset( $this->sut );

        \App\Repositories\Attribution\FeedReportRepo::dropTempTable( self::TEST_MODEL_ID );
        \App\Repositories\Attribution\ClientReportRepo::dropTempTable( self::TEST_MODEL_ID );

        parent::tearDown();
    }

    public function test_goodRun_AttributionAggregatorJobGeneration () {
        $this->expectsJobs( \App\Jobs\AttributionAggregatorJob::class );

        $this->sut->handle( \App::make( \App\Events\AttributionCompleted::class , [ self::TEST_MODEL_ID ] ) );

        $this->assertEquals( \Carbon\Carbon::today()->day , \DB::table( 'job_entries' )->count() );
    }
}
