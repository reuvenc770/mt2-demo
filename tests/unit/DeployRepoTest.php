<?php

class DeployRepoTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $mockDeployModel;
    protected $mockOfferModel;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->mockDeployModel = Mockery::mock( \App\Models\Deploy::class );
        $this->mockOfferModel = Mockery::mock( \App\Models\Offer::class );

        $this->sut = $laravel->app->make( \App\Repositories\DeployRepo::class , [
            $this->mockDeployModel ,
            $this->mockOfferModel
        ] );
    }

    protected function _after()
    {
        unset( $this->mockDeployModel );
        unset( $this->mockOfferModel );
        unset( $this->mockOfferModel );
    }

    /**
     * Here we are testing that the query structure hasn't changed.
     *
     * If this test fails for you, make sure it doesn't break the frontend index page and update the test for your changes.
     */
    public function testGetModel () {
        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'select' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->twice()->andReturn( $this->mockDeployModel );

        $this->assertInstanceOf( \App\Models\Deploy::class , $this->sut->getModel() );
    } 

    /**
     * Here we are testing that the query structure hasn't changed.
     *
     * This is used for deploy packages so its important that this eloquent call works.
     *
     * TODO: Assert for return value once test databases are setup
     */
    public function testDeployDetailGetter () {
        $this->mockDeployModel->shouldReceive( 'leftJoin' )->times( 9 )->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'selectRaw' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();

        $this->sut->getDeployDetailsByIds( 1 );
    }

    /**
     * Here we are testing the deployment correct status is used when deploying packages.
     *
     * This is important for alerting the operators that deploys are ready.
     */
    public function testDeployPackageStatusUpdate () {
        $this->mockDeployModel->shouldReceive( 'wherein' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'getClassName' );
        $this->mockDeployModel->shouldReceive( 'update' )
            ->with( ['deployment_status' => 1 ] )
            ->once()
            ->andReturn( $this->mockDeployModel );

        $this->sut->deployPackages( 1 );
    }

    /**
     * Here we are testing that the query structure hasn't changed and is starting at the ID for CMP. 
     *
     * TODO: Assert for return value once test databases are setup
     */
    public function testDeploysForTodayGetter () {
        $this->mockDeployModel->shouldReceive( 'where' )->once()->andReturn( $this->mockDeployModel );
        $this->mockDeployModel->shouldReceive( 'get' )->once();
        $this->mockDeployModel->shouldReceive( 'whereRaw' )
            ->with( "id > 2000000" )
            ->once()
            ->andReturn( $this->mockDeployModel );

        $this->sut->getDeploysForToday( \Carbon\Carbon::now()->toDateString() );
    }
}
