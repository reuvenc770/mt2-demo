<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution\FactoryTraits;

use Log;

trait AttributionAssignedRecordUpdaterIntegrationTest_Trait {
    public function setUpTestData () {
        $client = factory( \App\Models\Client::class )->create();

        Log::info( $client );
    }
}
