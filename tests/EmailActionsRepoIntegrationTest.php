<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;

use \Illuminate\Foundation\Testing\DatabaseMigrations;

use \App\Repositories\EmailActionsRepo;
use \Carbon\Carbon;

class EmailActionsRepoIntegrationTest extends TestCase {
    use DatabaseMigrations

    public $repo;

    public function setUp () {
        parent::setUp();

        $this->repo = \App::make( \App\Repositories\EmailActionsRepo::class );
    }

    public function tearDown () {

        parent::tearDown();
    }
}
