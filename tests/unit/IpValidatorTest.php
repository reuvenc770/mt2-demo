<?php


class IpValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $ipMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->ipMock = Mockery::spy( \App\Repositories\IpligenceDataRepo::class );

        $this->sut = $laravel->app->make( \App\Services\Validators\IpValidator::class , [
            $this->ipMock
        ] );
    }

    protected function _after()
    {
        unset( $this->ipMock );
        unset( $this->sut );
    }

    public function testShouldHaveIpDatapointKey () {
        $this->assertEquals( [ 'ip' ] , $this->sut->getRequiredData() );
    }

    public function testShouldDefaultIfNoIpPresent () {
        $this->sut->setData( [ 'ip' => '' ] );
        $this->sut->validate();

        $this->assertEquals( '10.1.2.3' , $this->sut->returnData()[ 'ip' ] );
    }

    public function testShouldFailForInvalidIp () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'ip' => '151651.1.1.1' ] );
        $this->sut->validate();
    }

    public function testShouldFailForCanadianIp () {
        $this->expectException( \App\Exceptions\ValidationException::class );
        
        $this->ipMock->shouldReceive( 'isFromCanada' )->once()->andReturn( true );
        $this->sut->setData( [ 'ip' => '104.160.220.131' ] );
        $this->sut->validate();
    }
}
