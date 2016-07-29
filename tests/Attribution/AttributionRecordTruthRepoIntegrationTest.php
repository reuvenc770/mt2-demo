<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use Tests\Attribution\FactoryTraits\AttributionRecordTruthRepoIntegrationTest_Trait;

use \Illuminate\Foundation\Testing\DatabaseMigrations;

use \App\Repositories\AttributionRecordTruthRepo;

use \Log;

class AttributionRecordTruthRepoIntegrationTest extends TestCase {
    use DatabaseMigrations ,
        AttributionRecordTruthRepoIntegrationTest_Trait;

    public $repo;

    public function setUp () {
        parent::setUp();

        $this->repo = \App::make( \App\Repositories\AttributionRecordTruthRepo::class );
    }

    public function tearDown () {
        unset( $this->repo );

        parent::tearDown();
    }

    public function test_goodAssignedRecordRetrieval () {
        $this->setUpTestData_goodAssignedRecordRetrieval();

        $assignedRecords = $this->repo->getAssignedRecords();

        $this->assertEquals( count( $assignedRecords ) , 3 );
    }
}
