<?php


class AdvertiserInfoOfferMapStrategyTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $sut;

    const TYPE_ID_CPM = 1;
    const TYPE_ID_CPC = 2;
    const TYPE_ID_CPA = 3;
    const TYPE_ID_CPS = 4;
    const TYPE_ID_OTHER = 5;

    protected function _before()
    {
        $laravel = $this->getModule( 'Laravel5' );
        $this->sut = $laravel->app->make( \App\Services\MapStrategies\AdvertiserInfoOfferMapStrategy::class );
    }

    protected function _after()
    {
        unset( $this->sut );
    }

    public function testShouldRecognizeCpmOffers () {
        $this->assertEquals(
            self::TYPE_ID_CPM , 
            $this->sut->map( $this->getMockRecord( 'CPM' ) )[ 'offer_payout_type_id' ]
        );
    }

    public function testShouldRecognizeCpcOffers () {
        $this->assertEquals(
            self::TYPE_ID_CPC , 
            $this->sut->map( $this->getMockRecord( 'CPC' ) )[ 'offer_payout_type_id' ]
        );
    }

    public function testShouldRecognizeCpaOffers () {
        $this->assertEquals(
            self::TYPE_ID_CPA , 
            $this->sut->map( $this->getMockRecord( 'CPA' ) )[ 'offer_payout_type_id' ]
        );
    }

    public function testShouldRecognizeCpsOffers () {
        $this->assertEquals(
            self::TYPE_ID_CPS , 
            $this->sut->map( $this->getMockRecord( 'CPS' ) )[ 'offer_payout_type_id' ]
        );
    }

    public function testShouldSetDefaultForUnknownOfferType () {
        $this->assertEquals(
            self::TYPE_ID_OTHER , 
            $this->sut->map( $this->getMockRecord( 'WIZARDOZ' ) )[ 'offer_payout_type_id' ]
        );
    }

    protected function getMockRecord ( $offerType ) {
        return [
            'advertiser_id' => 9999,
            'advertiser_name' => 'TestAdvertiser',
            'company_id' => 9999,
            'status' => 'A',
            'date_approved' => \Carbon\Carbon::today()->toDateString(),
            'offer_type' => $offerType ,
            'unsub_link' => 'http://testunsub.com',
            'exclude_days' => 'NNNNNNN',
            'unsub_text' => 'Unsub me',
            'unsub_use' => 'Have no idea what this is for.'
        ]; 
    }
}
