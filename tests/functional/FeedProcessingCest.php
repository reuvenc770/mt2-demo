<?php


class FeedProcessingCest
{
    public static $thirdParty = 3;

    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    public function testValidRecordsStoredSuccessfully ( FunctionalTester $I , \Helper\FeedProcessingHelper $H )
    {
        $I->am( 'Feed Processing Module' );
        $I->wantTo( 'successfully process raw email records' );

        $sut = $H->generateThirdPartyFeedProcessingService();
        $processingRecords = [];

        do {
            $processingRecords []= $H->createValidProcessingRecord();
        } while ( sizeof( $processingRecords ) < 100 );

        $sut->suppress( $processingRecords );
        $sut->process( $processingRecords );

        foreach ( $processingRecords as $record ) {
            $H->dontSeeValidationError( $record );

            $I->seeRecord( 'emails' , [ 'email_address' => $record->emailAddress ] );

            $I->dontSeeRecord( 'invalid_email_instances' , [ 'email_address' => $record->emailAddress ] );
        }
    }

    public function testCanadianRecordsWereRejected ( FunctionalTester $I , \Helper\FeedProcessingHelper $H ) {
        $I->am( 'Feed Processing Module' );
        $I->wantTo( 'see Canadian records fail to process.' );

        $sut = $H->generateThirdPartyFeedProcessingService();
        $processingRecords = [];
        $invalidRecords = [];

        do {
            $processingRecords []= $H->createValidProcessingRecord();
        } while ( sizeof( $processingRecords ) < 75 );

        do {
            $currentInvalidRecord = $H->createInvalidCanadianProcessingRecord();

            $invalidRecords []= $currentInvalidRecord;
            $processingRecords []= $currentInvalidRecord; 
        } while ( sizeof( $processingRecords ) < 100 );

        $sut->suppress( $processingRecords );
        $sut->process( $processingRecords );

        foreach ( $invalidRecords as $record ) {
            $H->seeCanadianValidationError( $record );

            $I->dontSeeRecord( 'emails' , [ 'email_address' => $record->emailAddress ] );

            $I->seeRecord( 'invalid_email_instances' , [
                'email_address' => $record->emailAddress ,
                'invalid_reason_id' => \App\Models\InvalidReason::CANADA
            ] );
        }
    }

    public function testInvalidTldEmailsWereRejected ( FunctionalTester $I , \Helper\FeedProcessingHelper $H ) {
        $I->am( 'Feed Processing Module' );
        $I->wantTo( 'see emails w/ invalid tld fail to process.' );

        $sut = $H->generateThirdPartyFeedProcessingService();
        $processingRecords = [];
        $invalidRecords = [];

        do {
            $processingRecords []= $H->createValidProcessingRecord();
        } while ( sizeof( $processingRecords ) < 75 );

        do {
            $currentInvalidRecord = $H->createInvalidTldEmailProcessingRecord();

            $invalidRecords []= $currentInvalidRecord;
            $processingRecords []= $currentInvalidRecord; 
        } while ( sizeof( $processingRecords ) < 100 );

        $sut->suppress( $processingRecords );
        $sut->process( $processingRecords );

        foreach ( $invalidRecords as $record ) {
            $H->seeTldEmailValidationError( $record );

            $I->dontSeeRecord( 'emails' , [ 'email_address' => $record->emailAddress ] );

            $I->seeRecord( 'invalid_email_instances' , [
                'email_address' => $record->emailAddress ,
                'invalid_reason_id' => \App\Models\InvalidReason::EMAIL
            ] );
        }
    }

    public function testInvalidBadAliasesEmailsWereRejected ( FunctionalTester $I , \Helper\FeedProcessingHelper $H ) {
        $I->am( 'Feed Processing Module' );
        $I->wantTo( 'see emails w/ bad aliases fail to process.' );

        $sut = $H->generateThirdPartyFeedProcessingService();
        $processingRecords = [];
        $invalidRecords = [];

        do {
            $processingRecords []= $H->createValidProcessingRecord();
        } while ( sizeof( $processingRecords ) < 75 );

        do {
            $currentInvalidRecord = $H->createBadAliasEmailProcessingRecord();

            $invalidRecords []= $currentInvalidRecord;
            $processingRecords []= $currentInvalidRecord; 
        } while ( sizeof( $processingRecords ) < 100 );

        $sut->suppress( $processingRecords );
        $sut->process( $processingRecords );

        foreach ( $invalidRecords as $record ) {
            $H->seeBadAliasEmailValidationError( $record );

            $I->dontSeeRecord( 'emails' , [ 'email_address' => $record->emailAddress ] );

            $I->seeRecord( 'invalid_email_instances' , [
                'email_address' => $record->emailAddress ,
                'invalid_reason_id' => \App\Models\InvalidReason::EMAIL
            ] );
        }
    }
}
