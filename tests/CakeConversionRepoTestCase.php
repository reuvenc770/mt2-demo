<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests;

use \App\Repositories\CakeConversionRepo;

class CakeConversionRepoTestCase extends TestCase {
    public $modelStub;
    public $sut;

    public function setUp () {
        parent::setUp();

        $this->modelStub = $this->createMock( \App\Models\Cake\CakeConversion::class );

        $this->sut = new CakeConversionRepo( $this->modelStub );
    }

    public function tearDown () {
        unset( $this->modelStub );
        unset( $this->sut );

        parent::tearDown();
    }
}
