<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution\FactoryTraits;

use Log;

trait AttributionAssignedRecordUpdaterE2ETest_Client_Trait {
    public function setUpTestData () {
        $client = factory( App\Models\Client::class )->create();

        Log::info( $client );
    }
}
