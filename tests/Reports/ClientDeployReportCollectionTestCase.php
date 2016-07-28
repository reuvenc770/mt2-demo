<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;

class ClientDeployReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Reports\ClientDeployReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
