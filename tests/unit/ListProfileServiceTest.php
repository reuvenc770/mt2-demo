<?php


class ListProfileServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $repoMock;
    protected $lpQueryMock;
    protected $baseTableMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->repoMock = Mockery::spy( \App\Repositories\ListProfileRepo::class );
        $this->lpQueryMock = Mockery::spy( \App\Builders\ListProfileQueryBuilder::class );
        $this->baseTableMock = Mockery::spy( \App\Services\ListProfileBaseTableCreationService::class ); 

        $this->sut = $laravel->app->make( \App\Services\ListProfileService::class , [
            $this->repoMock ,
            $this->lpQueryMock ,
            $this->baseTableMock
        ] );
    }

    protected function _after()
    {
        unset( $this->repoMock );
        unset( $this->lpQueryMock );
        unset( $this->baseTableMock ); 
    }

    public function testShouldKnowWhichEntityToGivePaginationCache () {
        $this->assertEquals( 'ListProfile' , $this->sut->getType() );
    }

    public function testShouldDeleteIfCanBeDeleted () {
        $this->repoMock->shouldReceive( 'canBeDeleted' )->andReturn( true );

        $this->assertTrue( $this->sut->tryToDelete( 1 ) );

        $this->repoMock->shouldHaveReceived( 'delete' )->once();
    }
}
