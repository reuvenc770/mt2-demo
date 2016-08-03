<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use Tests\TestCase;
use \App\Services\CakeConversionService;
use \Carbon\Carbon;

class CakeConversionServiceTestCase extends TestCase {
    public $mockModel;
    public $mockRepo;
    public $sut; 

    public function setUp () {
        parent::setUp();

        $this->mockModel = $this->getMockBuilder( \App\Models\Cake\CakeConversion::class )
                                ->getMock();

        $this->mockRepo = $this->getMockBuilder( \App\Repositories\CakeConversionRepo::class )
                                ->setConstructorArgs( [ $this->mockModel ] )
                                ->setMethods( [ 'getByDate' ] )
                                ->getMock();

        $this->sut = new CakeConversionService( $this->mockRepo );    
    }

    public function tearDown () {
        unset( $this->mockModel );
        unset( $this->mockRepo );
        unset( $this->sut );

        parent::tearDown();
    }

    public function test_shouldCallRepoForData () {
        $this->mockRepo->expects( $this->once() )
                        ->method( 'getByDate' );

        $this->sut->getByDate();
    }
}
