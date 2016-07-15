<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
use Tests\Attribution\FactoryTraits\AttributionAssignedRecordUpdaterE2ETest_Client_Trait;

use \Illuminate\Foundation\Testing\DatabaseMigrations;

use \App\Jobs\AttributionAssignedRecordUpdater;
use \App\Models\AttributionAssignedRecord;

class AttributionAssignedRecordUpdaterIntegrationTest extends TestCase {
    use DatabaseMigrations ,
        AttributionAssignedRecordUpdaterE2ETest_Client_Trait;

    public $updater;

    public function setUp () {
        parent::setUp();

        $this->setUpTestData();

        $this->updater = \App::make( \App\Jobs\AttributionAssignedRecordUpdater::class );
    }

    public function tearDown () {
        unset( $this->updater );

        parent::tearDown();
    }

    public function test_MainExecution_GoodPath () {
        $this->updater->handle();

        $eloquent = new AttributionAssignedRecord();
        $data = $eloquent->get()->toJson();

        #fill in expected data
        $expectedData = json_encode( [
           [ "client_id" => 1 ]
        ] );
        
        $this->assertJsonStringEqualsJsonString( $data , $expectedData );
    }
}
