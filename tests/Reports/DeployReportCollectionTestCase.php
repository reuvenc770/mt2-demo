<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;

class DeployReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Reports\DeployReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }
}
