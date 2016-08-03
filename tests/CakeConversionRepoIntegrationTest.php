<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

use \App\Repositories\CakeConversionRepo;
use \Carbon\Carbon;

class CakeConversionRepoIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Repositories\CakeConversionRepo::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_goodPath_getByDateEmailDeploy () {
        $date = Carbon::today();

        $records = $this->sut->getByDate();

        $this->assertEquals( count( $records ) , 3 );

        $this->markTestSkipped(
            'This test has not been implemented yet.'
        );
    }
}
