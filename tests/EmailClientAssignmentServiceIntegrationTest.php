<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

class EmailClientAssignmentIntegrationTest extends TestCase {
    use DatabaseMigrations;

    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = \App::make( \App\Services\EmailClientAssignmentService::class );
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}