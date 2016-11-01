<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \Illuminate\Foundation\Testing\DatabaseMigrations;

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
}
