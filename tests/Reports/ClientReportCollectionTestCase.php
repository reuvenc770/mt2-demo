<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;

class ClientReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Reports\ClientReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
