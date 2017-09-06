<?php


class CountryAndStateValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->sut = $laravel->app->make( \App\Services\Validators\CountryAndStateValidator::class );
    }

    protected function _after()
    {
        unset( $this->sut );
    }

    public function testShouldHaveCountryAndStateDatapointKeys () {
        $this->assertEquals( ['state', 'country'] , $this->sut->getRequiredData() );
    }

    public function testShouldNormalizeUnitedStatesAlias () {
        $this->sut->setData( [ 'country' => 'unitedstates' , 'state' => 'NY' ] );
        $this->sut->validate();

        $this->assertEquals( 'US' , $this->sut->returnData()[ 'country' ] );
    }

    public function testShouldClearInvalidStates () {
        $this->sut->setData( [ 'country' => 'US' , 'state' => 'NYC' ] );
        $this->sut->validate();

        $this->assertEquals( '' , $this->sut->returnData()[ 'state' ] );
    }

    public function testShouldFailOnCanadaCountryDetection () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'country' => 'CA' , 'state' => 'TB' ] );
        $this->sut->validate();
    }
}
