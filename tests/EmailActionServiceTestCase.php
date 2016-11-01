<?php
/**
 *  @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \App\Services\EmailActionService;

class EmailActionServiceTestCase extends TestCase {
    public $mockRepo;
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->mockRepo = $this->getMockBuilder( \App\Repositories\EmailActionsRepo::class )
                                ->getMock();

        $this->sut = new EmailActionService( $this->mockRepo );
    }

    public function tearDown () {
        unset( $this->mockRepo );
        unset( $this->sut );

        parent::tearDown();
    }
}
