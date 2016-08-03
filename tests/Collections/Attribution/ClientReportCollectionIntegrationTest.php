<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Collections\Attribution;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;
use \Carbon\Carbon;

class ClientReportCollectionIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public $testClients;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Collections\Attribution\ClientReportCollection::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
