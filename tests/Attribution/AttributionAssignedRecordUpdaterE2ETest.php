<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;
#use Tests\Attribution\FactoryTraits\AttributionAssignedRecordUpdaterE2ETest_Client_Trait;

use \Illuminate\Foundation\Testing\DatabaseMigrations;

#use \App\Jobs\AttributionAssignedRecordUpdater;
#use \App\Models\AttributionTransientRecord;

class AttributionAssignedRecordUpdaterE2ETest extends TestCase {
    use DatabaseMigrations; #,
        #AttributionAssignedRecordUpdaterE2ETest_Client_Trait;

    public $updater;

    public function setUp () {
        parent::setUp();

        $this->app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        #$this->setUpTestData();

        #$this->app->singleton('\Illuminate\Contracts\Console\Kernel' , '\Illuminate\Foundation\Console\Kernel' );

        #$this->updater = App::make( \App\Jobs\AttributionAssignedRecordUpdater::class )->bootstrap();
    }

    public function tearDown () {
        #unset( $this->updater );

        parent::tearDown();
    }

    public function test_MainExecution_GoodPath () {
        #$this->updater->handle();
        #
        #$data = new AttributionTransientRecord( 1 );
        #$expectedData = json_encode( [
        #   { "client_id" : 1 , }
        #] );
        #
        #$this->assertJsonStringEqualsJsonString();
    }
}
