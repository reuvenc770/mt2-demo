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
        Mockery::mock('alias:Deploy'); #Eloquent uses static methods so you need this so the calls resolve when mocking. This must run before mocking.

        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'create' )->andReturn( factory( \App\Models\Deploy::class )->make() );

        $this->assertInstanceOf( \App\Models\Deploy::class , $this->sut->insert( [ "deploy_name" => 'testInsert' ] ) );
    }

    public function testGetModel () {
        $this->createSut();

        #Here we are testing that the query structure hasn't changed. If this test fails for you, make sure it doesn't break the frontend index page and update the test for you changes.
        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'select' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->twice()->andReturn( $this->mockDeployModel );

        $this->assertInstanceOf( \App\Models\Deploy::class , $this->sut->getModel() );
    } 

    public function testDeployDetailGetter () {
        $this->createSut();

        #Here we are testing that the query structure hasn't changed. This is used for deploy packages so its important that this eloquent call works.
        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'selectRaw' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();

        #assert for return value once test databases are setup 
        $this->sut->getDeployDetailsByIds( 1 );
    }

    public function testDeployPackageStatusUpdate () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'update' )
            ->with( ['deployment_status' => 1 ] ) #this deployment status is important for alerting the operators that deploys are ready.
            ->once()
            ->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'getClassName' );

        $this->sut->deployPackages( 1 );
    }

    public function testDeploysForTodayGetter () {
        $this->createSut();

        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'whereRaw' )
            ->with( "id > 2000000" ) #we're starting here because this is the start of CMP IDs
            ->once()
            ->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();

        #assert for return value once test databases are setup 
        $this->sut->getDeploysForToday( \Carbon\Carbon::now()->toDateString() );
    }

    protected function createSut () {
        $this->mockDeployModel = Mockery::mock( \App\Models\Deploy::class );
        $this->mockOfferModel = Mockery::mock( \App\Models\Offer::class );

        $this->sut = $this->app->make( \App\Repositories\DeployRepo::class , [
            $this->mockDeployModel ,
            $this->mockOfferModel
        ] );
    }
}
