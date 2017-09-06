<?php


class MailingTemplateServiceTest extends \Codeception\Test\Unit
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
        $this->repoMock = Mockery::spy( \App\Repositories\MailingTemplateRepo::class );

        $this->sut = $laravel->app->make( \App\Services\MailingTemplateService::class , [
            $this->repoMock
        ] );
    }

    protected function _after()
    {
        unset( $this->repoMock );
        unset( $this->sut );
    }

    public function testShouldKnowWhichEntityToGivePaginationCache () {
        $this->assertEquals( 'MailingTemplate' , $this->sut->getType() );
    }
}
