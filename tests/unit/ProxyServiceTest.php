<?php


class ProxyServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $repoMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->repoMock = Mockery::spy( \App\Repositories\ProxyRepo::class );

        $this->sut = $laravel->app->make( \App\Services\ProxyService::class , [
            $this->repoMock
        ] );
    }

    protected function _after()
    {
        unset( $this->repoMock );
        unset( $this->sut );
    }


    public function testShouldKnowWhichEntityToGivePaginationCache () {
        $this->assertEquals( 'Proxy' , $this->sut->getType() );
    }

    public function testShouldDeleteIfCanBeDeleted () {
        $this->repoMock->shouldReceive( 'canBeDeleted' )->andReturn( true );

        $this->assertTrue( $this->sut->tryToDelete( 1 ) );

        $this->repoMock->shouldHaveReceived( 'delete' )->once();
    }
}
