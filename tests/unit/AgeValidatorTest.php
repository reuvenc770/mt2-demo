<?php


class AgeValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->sut = $laravel->app->make( \App\Services\Validators\AgeValidator::class );
    }

    protected function _after()
    {
        unset( $this->sut );
    }

    public function testShouldHaveAgeDatapointKeys () {
        $this->assertEquals( [ 'dob' ] , $this->sut->getRequiredData() );
    }

    public function testShouldFailForAgesUnderEighteen () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'dob' => '20170101' ] );
        $this->sut->validate();
    }

    public function testShouldFailForInvalidDobDates () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'dob' => '9999999-20-01' ] );
        $this->sut->validate();
    }
}
