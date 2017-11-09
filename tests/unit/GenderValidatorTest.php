<?php


class GenderValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->nameGenderMock = Mockery::spy( \App\Repositories\NameGenderRepo::class );

        $this->sut = $laravel->app->make( \App\Services\Validators\GenderValidator::class , [
            $this->nameGenderMock
        ] );
    }

    protected function _after()
    {
        unset( $this->nameGenderMock );
        unset( $this->sut );
    }

    public function testShouldHaveGenderDatapointKeys () {
        $this->assertEquals( [ 'gender' , 'firstName' ] , $this->sut->getRequiredData() );
    }

    public function testShouldNormalizeMaleGenderValues () {
        $this->sut->setData( [ 'gender' => 'MALE' , 'firstName' => 'John' ] );
        $this->sut->validate();

        $this->assertEquals( 'M' , $this->sut->returnData()[ 'gender' ] );
    }

    public function testShouldNormalizeFemaleGenderValues () {
        $this->sut->setData( [ 'gender' => 'Frau' , 'firstName' => 'Jane' ] );
        $this->sut->validate();

        $this->assertEquals( 'F' , $this->sut->returnData()[ 'gender' ] );
    }

    public function testShouldAttemptToNormalizeGenderValuesUsingName () {
        $this->sut->setData( [ 'gender' => 'UNK' , 'firstName' => 'John' ] );
        $this->sut->validate();

        $this->nameGenderMock->shouldHaveReceived( 'getGender' )->with( 'John' )->once();
    } 
}
