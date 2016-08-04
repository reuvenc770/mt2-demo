<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \App\Services\EmailClientAssignmentService;

class EmailClientAssignmentServiceTestCase extends TestCase {
    public $mockRepo;
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->mockRepo = $this->getMockBuilder( \App\Repositories\EmailClientAssignmentRepo::class )

        $this->sut = new EmailClientAssignmentService( $this->mockRepo );
    }

    public function tearDown () {
        unset( $this->mockRepo );
        unset( $this->sut );

        parent::tearDown();
    }

}
