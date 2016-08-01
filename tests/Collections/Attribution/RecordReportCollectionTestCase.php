<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Collections\Attribution;

use Tests\TestCase;

class RecordReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Collections\Attribution\RecordReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
