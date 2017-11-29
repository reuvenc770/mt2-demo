<?php


class SourceUrlValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $feedMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->feedMock = Mockery::spy( \App\Repositories\FeedRepo::class );
        $this->sut = $laravel->app->make( \App\Services\Validators\SourceUrlValidator::class , [
            $this->feedMock
        ] );
    }

    protected function _after()
    {
        unset( $this->feedMock );
        unset( $this->sut );
    }

    public function testShouldHaveSourceUrlDatapointKeys () {
        $this->assertEquals( [ 'sourceUrl' , 'feedId' ] , $this->sut->getRequiredData() );
    }

    public function testShouldDefaultIfFoundInDb () {
        $this->feedMock->shouldReceive( 'getSourceUrl' )->once()->andReturn( 'unittest.com' );

        $this->sut->setData( [ 'sourceUrl' => '' , 'feedId' => 9999999 ] );
        $this->sut->validate();

        $this->assertEquals( 'unittest.com' , $this->sut->returnData()[ 'sourceUrl' ] );
    }

    public function testShouldDefaultIfNoSourceUrlCanBeFoundInDb () {
        $this->feedMock->shouldReceive( 'getSourceUrl' )->once()->andReturn( null );

        $feedRecordMock = new stdClass;
        $feedRecordMock->name = 'feedtest';
        $this->feedMock->shouldReceive( 'fetch' )->once()->andReturn( $feedRecordMock );

        $this->sut->setData( [ 'sourceUrl' => '' , 'feedId' => 9999999 ] );
        $this->sut->validate();

        $this->assertEquals( 'na.feedtest.com' , $this->sut->returnData()[ 'sourceUrl' ] );
    }

    public function testShouldFailIfUrlContainsBetheboss () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'sourceUrl' => 'iwillbethebossnow.com' , 'feedId' => 9999999 ] );
        $this->sut->validate();
    }

    public function testShouldFailIfCanadianDomain () {
        $this->expectException( \App\Exceptions\ValidationException::class );

        $this->sut->setData( [ 'sourceUrl' => 'test.ca' , 'feedId' => 9999999 ] );
        $this->sut->validate();
    }
}
