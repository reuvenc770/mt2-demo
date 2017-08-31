<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeployRepoUnitTestCase extends TestCase
{
    protected $sut;
    protected $mockDeployModel;
    protected $mockOfferModel;

    public function setUp () {
        parent::setUp();
    }

    public function tearDown () {
        parent::tearDown();

        unset( $this->mockDeployModel );
        unset( $this->mockOfferModel );
        unset( $this->sut );
    }

    public function testInsert () {
        Mockery::mock('alias:Deploy');

        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'create' )->andReturn( factory( \App\Models\Deploy::class )->make() );

        $this->assertInstanceOf( \App\Models\Deploy::class , $this->sut->insert( [ "deploy_name" => 'testInsert' ] ) );
    }

    public function testGetModel () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'select' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->twice()->andReturn( $this->mockDeployModel );

        $this->assertInstanceOf( \App\Models\Deploy::class , $this->sut->getModel() );
    } 

    public function testDeployDetailGetter () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'selectRaw' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();

        #assert for return value once test databases are incorporated
        $this->sut->getDeployDetailsByIds( 1 );
    }

    public function testDeployPackageStatusUpdate () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'update' )->with( ['deployment_status' => 1 ] )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'getClassName' );

        $this->sut->deployPackages( 1 );
    }

    public function testDeploysForTodayGetter () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'whereRaw' )->with( "id > 2000000" )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();

        #assert for return value once test databases are incorporated
        $this->sut->getDeploysForToday( \Carbon\Carbon::now()->toDateString() );
    }

    protected function createSut () {
        $this->mockDeployModel = Mockery::mock( \App\Models\Deploy::class );
        $this->mockOfferModel = $this->createMock( \App\Models\Offer::class );

        $this->sut = $this->app->make( \App\Repositories\DeployRepo::class , [
            $this->mockDeployModel ,
            $this->mockOfferModel
        ] );
    }
}
