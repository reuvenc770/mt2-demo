<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Collections\Attribution;

use Tests\TestCase;

class ClientReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Collections\Attribution\ClientReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
