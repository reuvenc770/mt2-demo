<?php


class FeedProcessingServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $emailMock;
    protected $instanceMock;
    protected $emailDomainMock;
    protected $statsMock;
    protected $invalidMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->emailMock = Mockery::spy( \App\Repositories\EmailRepo::class );
        $this->instanceMock = Mockery::spy( \App\Repositories\EmailFeedInstanceRepo::class );
        $this->emailDomainMock = Mockery::spy( \App\Repositories\EmailDomainRepo::class );
        $this->statsMock = Mockery::spy( \App\Repositories\FeedDateEmailBreakdownRepo::class );
        $this->invalidMock = Mockery::spy( \App\Repositories\InvalidEmailInstanceRepo::class );

        $this->sut = $laravel->app->make( \App\Services\FeedProcessingService::class , [
            $this->emailMock ,
            $this->instanceMock ,
            $this->emailDomainMock ,
            $this->statsMock ,
            $this->invalidMock
        ] );
    }

    protected function _after()
    {
        unset( $this->emailMock );
        unset( $this->instanceMock );
        unset( $this->emailDomainMock );
        unset( $this->statsMock );
        unset( $this->invalidMock );
        unset( $this->sut );
    }

    public function testShouldChainRegisterValidators () {
        $validatorMock = Mockery::spy( \App\Services\Validators\AgeValidator::class );

        $this->assertSame( $this->sut , $this->sut->registerValidator( $validatorMock ) );
    }

    public function testShouldChainRegisterSuppressors () {
        $suppressorMock = Mockery::spy( \App\Services\GlobalSuppressionService::class );

        $this->assertSame( $this->sut , $this->sut->registerSuppression( $suppressorMock ) );
    }
}
