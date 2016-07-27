<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Reports;

use Tests\TestCase;

class RecordReportCollectionTestCase extends TestCase {
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->sut = new \App\Reports\RecordReportCollection();
    }

    public function tearDown () {
        unset( $this->sut );

        parent::tearDown();
    }

    /**
     * @expectedException App\Exceptions\RecordReportCollectionException
     */
    public function test_failPath_NoActionServicePresentForBuildingReport () {
        $mockConversion = $this->getMockBuilder( \App\Services\CakeConversionService::class )
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut->buildAndSaveReport( $mockConversion );
    }
}
