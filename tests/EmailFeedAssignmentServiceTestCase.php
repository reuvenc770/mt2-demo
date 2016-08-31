<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \App\Services\EmailFeedAssignmentService;

class EmailFeedAssignmentServiceTestCase extends TestCase {
    public $mockRepo;
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->mockRepo = $this->getMockBuilder( \App\Repositories\EmailFeedAssignmentRepo::class )

        $this->sut = new EmailFeedAssignmentService( $this->mockRepo );
    }

    public function tearDown () {
        unset( $this->mockRepo );
        unset( $this->sut );

        parent::tearDown();
    }

}
