<?php


class CaptureDateValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->sut = $laravel->app->make( \App\Services\Validators\CaptureDateValidator::class );
    }

    protected function _after()
    {
        unset( $this->sut );
    }

    public function testShouldHaveCaptureDateDatapointKeys () {
        $this->assertEquals( [ 'captureDate' ] , $this->sut->getRequiredData() );
    }

    public function testShouldFailForInvalidDates () {
        $this->expectException( App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'captureDate' => '9999999-20-01' ] );
        $this->sut->validate();
    }

    public function testFutureCaptureDatesDefaultToToday () {
        $this->sut->setData( [ 'captureDate' => \Carbon\Carbon::today()->addMonth()->toDateString() ] );

        $this->sut->validate();

        $this->assertEquals(
            \Carbon\Carbon::today()->toDateString() ,
            $this->sut->returnData()[ 'captureDate' ]
        );
    }
}
