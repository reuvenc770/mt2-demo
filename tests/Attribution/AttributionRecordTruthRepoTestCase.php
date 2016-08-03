<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution;

use Tests\TestCase;

use \App\Repositories\AttributionRecordTruthRepo;

class AttributionRecordTruthRepoTestCase extends TestCase {
    public $eModel;
    public $repo;

    public function setUp () {
        parent::setUp();

        $this->eModel = $this->getMockBuilder( \App\Models\AttributionRecordTruth::class )
                            ->setMethods( [ 'getAssignedRecords' ] )
                            ->getMock();

        $this->repo = new AttributionRecordTruthRepo( $this->eModel ); 
    }

    public function tearDown () {
        unset( $this->eModel );
        unset( $this->repo );

        parent::tearDown();
    }

    public function test_stuff () {
        

        $this->assertTrue( true );
    }
} 
