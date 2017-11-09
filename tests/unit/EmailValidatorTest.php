<?php


class EmailValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;
    protected $emailDomainMock;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->emailDomainMock = Mockery::spy( \App\Repositories\EmailDomainRepo::class );

        $this->sut = $laravel->app->make( \App\Services\Validators\EmailValidator::class , [ $this->emailDomainMock ] );
    }

    protected function _after()
    {
        unset( $this->emailDomainMock );
        unset( $this->sut );
    }

    public function testShouldHaveEmailDatapointKeys() {
        $this->assertEquals( [ 'emailAddress', 'newEmail', 'domainId', 'domainGroupId' ] , $this->sut->getRequiredData() );
    }

    public function testShouldNormalizeEmailAddress() {
        $this->sut->setData( [ 'emailAddress' => 'rumpelStiltSkin@gmail.com ' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();

        $this->assertEquals( 'rumpelstiltskin@gmail.com' , $this->sut->returnData()['emailAddress'] );
    }

    public function testShouldFailForEmailsOverFiftyCharacters() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email is invalid - length/' );

        $this->sut->setData( [ 'emailAddress' => 'rumpelstiltskinspinsgoldoutofstrawformyfirtbornbaby@gmail.com' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForIncorrectEmailFormat() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - incorrect format/' );

        $this->sut->setData( [ 'emailAddress' => 'rumpelstiltskin@gmail' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForInvalidEmailTld() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - suppressed TLD in domain/' );

        $this->sut->setData( [ 'emailAddress' => 'rumpelstiltskin@gmail.org' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForBadEmailAlias() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - banned alias/' );

        $this->sut->setData( [ 'emailAddress' => 'contact@rumpelstiltskin.com' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForSuppressedEmailDomain() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - suppressed domain/' );

        $this->emailDomainMock->shouldReceive( 'domainIsSuppressed' )->once()->andReturn( true );
        $this->sut->setData( [ 'emailAddress' => 'rumpelstiltskin@baddomain.com' , 'newEmail' => 0 , 'domainId' => 5 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForEmailAddresWithBannedWords() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - contains obscene language/' );

        $this->sut->setData( [ 'emailAddress' => 'seed@gmail.com' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }

    public function testShouldFailForInvalidEmailProviderRules() {
        $this->expectException( \App\Exceptions\ValidationException::class );
        $this->expectExceptionMessageRegExp( '/^Email address invalid - does not pass ISP validation/' );

        $mockRecord = new stdClass;
        $mockRecord->domain_group_name = 'gmail';
        $this->emailDomainMock->shouldReceive( 'getDomainAndClassInfo' )->once()->andReturn( $mockRecord );
        $this->sut->setData( [ 'emailAddress' => 'a@gmail.com' , 'newEmail' => 0 , 'domainId' => 1 , 'domainGroupId' => 1 ] );
        $this->sut->validate();
    }
}