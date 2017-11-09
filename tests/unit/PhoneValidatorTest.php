<?php


class PhoneValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->sut = $laravel->app->make( \App\Services\Validators\PhoneValidator::class );
    }

    protected function _after()
    {
        unset( $this->sut );
    }

    public function testShouldHavePhoneDatapointKeys () {
        $this->assertEquals( [ 'phone' ] , $this->sut->getRequiredData() );
    }

    public function testShouldStripNonNumericCharacters () {
        $this->sut->setData( [ 'phone' => '212-548-7888' ] );
        $this->sut->validate();

        $this->assertEquals( '2125487888' , $this->sut->returnData()[ 'phone' ] );
    }
}
