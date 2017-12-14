<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class FeedProcessingHelper extends \Codeception\Module
{
    const THIRD_PARTY = 3;
    const BAD_ALIASES = \App\Services\Validators\EmailValidator::BAD_ALIASES;
    const INVALID_TLDS = \App\Services\Validators\EmailValidator::INVALID_TLDS; 

    /**
     * Helper Methods
     */
    public function generateThirdPartyFeedProcessingService () {
        return \App\Factories\FeedProcessingFactory::createService( self::THIRD_PARTY );
    }

    public function createRawRecord ( $overrideData = [] ) {
        return factory( \App\Models\RawFeedEmail::class )->create( $overrideData );
    }

    public function createValidRawRecord ( $overrideData = [] ) {
        $record = $this->createRawRecord( $overrideData );
        $validRecord = $record;

        if ( $this->isTldInvalid( $record->email_address ) ) {
            $record->delete();
            unset( $validRecord );
            
            $validRecord = $this->createValidRawRecord( $overrideData );
        }

        return $validRecord;
    }

    public function createProcessingRecord ( $overrideData = [] ) {
        $record = $this->createRawRecord( $overrideData );

        return $this->convertToProcessingRecord( $record );
    }

    public function createValidProcessingRecord ( $overrideData = [] ) {
        $record = $this->createValidRawRecord( $overrideData );
        return $this->convertToProcessingRecord( $record );
    }

    public function createInvalidCanadianProcessingRecord () {
        #we do not want it erroring out for another reason
        return $this->createValidProcessingRecord( [ 'country' => 'CA' ] );
    }

    public function createInvalidTldEmailProcessingRecord () {
        $rawRecord = $this->createRawRecord();

        if ( !$this->isTldInvalid( $rawRecord->email_address ) ) {
            $replaceRegex = '$1.' . self::INVALID_TLDS[ array_rand( self::INVALID_TLDS ) ];  
            $rawRecord->email_address = preg_replace( '/(.+)\.(\w+)$/' , $replaceRegex , $rawRecord->email_address );
            $rawRecord->save();
        }

        return $this->convertToProcessingRecord( $rawRecord );
    }

    public function createBadAliasEmailProcessingRecord () {
        $rawRecord = $this->createValidRawRecord();

        $replaceRegex = self::BAD_ALIASES[ array_rand( self::BAD_ALIASES ) ] . '@$2';  
        $rawRecord->email_address = preg_replace( '/^(.+)@(.+)$/' , $replaceRegex , $rawRecord->email_address );
        $rawRecord->save();

        return $this->convertToProcessingRecord( $rawRecord );
    }

    public function createSuppressedDomainEmailProcessingRecord () {
        $rawRecord = $this->createValidRawRecord();

        $fake = \Faker\Factory::create();

        $replaceRegex =  '$1@' . $fake->domain;  
        $rawRecord->email_address = preg_replace( '/^(.+)@(.+)$/' , $replaceRegex , $rawRecord->email_address );
        $rawRecord->save();

        return $this->convertToProcessingRecord( $rawRecord );
    }

    /**
     * Assertion Actions
     */
    public function dontSeeValidationError ( \App\DataModels\ProcessingRecord $record ) {
        try {
            $this->assertNull( $record->invalidReason );
        } catch ( \Exception $e ) {
            $this->fail( "I see a validation error for {$record->emailAddress}." );
        }

        codecept_debug( "I dont see a validation error for {$record->emailAddress}." );
    }

    public function seeCanadianValidationError ( \App\DataModels\ProcessingRecord $record ) {
        try {
            $this->assertEquals( preg_match( '/^Canada detected for country.+$/' , $record->invalidReason ) , 1 );
        } catch ( \Exception $e ) {
            $this->fail( "I dont see a Canada validation error for {$record->emailAddress}." );
        }

        codecept_debug( "I see a Cananda validation error for {$record->emailAddress}." );
    }

    public function seeTldEmailValidationError ( \App\DataModels\ProcessingRecord $record ) {
        try {
            $this->assertEquals( preg_match( '/^Email address invalid - suppressed TLD in domain.+$/' , $record->invalidReason ) , 1 );
        } catch ( \Exception $e ) {
            $this->fail( "I dont see a TLD email validation error for {$record->emailAddress}." );
        }

        codecept_debug( "I see a TLD email validation error for {$record->emailAddress}." );
    }

    public function seeBadAliasEmailValidationError ( \App\DataModels\ProcessingRecord $record ) {
        try {
            $this->assertEquals( preg_match( '/^Email address invalid - banned alias.+$/' , $record->invalidReason ) , 1 );
        } catch ( \Exception $e ) {
            $this->fail( "I dont see a bad alias email validation error for {$record->emailAddress}." );
        }

        codecept_debug( "I see a bad alias email validation error for {$record->emailAddress}." );
    }

    /**
     * Internal Utility Methods
     */
    private function isTldInvalid ( $email ) {
        $domain = $this->getDomain( $email );

        foreach(self::INVALID_TLDS as $tld) {
            if ( preg_match( "/\.{$tld}$/" , $domain ) ) {
                return true;
            }
        }

        $domainArray = explode('.', $domain );

        if ( sizeof( $domainArray ) > 2 ) { // Example, @site.org.uk
            $secondaryTld = $domainArray[1];

            foreach ( self::INVALID_TLDS as $tld ) {
                if ( $tld === $secondaryTld ) {
                    return true;
                }
            }
        }

        return false;
    }

    private function convertToProcessingRecord ( \App\Models\RawFeedEmail $record ) {
        return \App::make( \App\DataModels\ProcessingRecord::class , [ $record ] );
    }

    private function getDomain ( $email ) {
        $parts = explode( '@' , $email );
        return $parts[ 1 ];
    }
}
