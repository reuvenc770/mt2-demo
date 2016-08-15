<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace Tests\Attribution\FactoryTraits;

use Log;

trait AttributionRecordTruthRepoIntegrationTest_Trait {
    public function setUpTestData_goodAssignedRecordRetrieval () {
        $feed = factory( \App\Models\Feed::class )->create();


        $email1 = factory( \App\Models\Email::class )->create();

        $email1Assign = factory( \App\Models\EmailFeedAssignment::class )->create( [
            'feed_id' => $feed->id ,
            'email_id' => $email1->id
        ] );

        $email1Truth = factory( \App\Models\AttributionRecordTruth::class )->create( [
            'email_id' => $email1->id ,
            'recent_import' => 1 ,
            'has_action' => 0 ,
            'action_expired' => 0 ,
            'additional_imports' => 0
        ] );


        $email2 = factory( \App\Models\Email::class )->create();

        $email2Assign = factory( \App\Models\EmailFeedAssignment::class )->create( [
            'feed_id' => $feed->id ,
            'email_id' => $email2->id
        ] );

        $email2Truth = factory( \App\Models\AttributionRecordTruth::class )->create( [
            'email_id' => $email2->id ,
            'recent_import' => 0 ,
            'has_action' => 1 ,
            'action_expired' => 0 ,
            'additional_imports' => 0
        ] );


        $email3 = factory( \App\Models\Email::class )->create();

        $email3Assign = factory( \App\Models\EmailFeedAssignment::class )->create( [
            'feed_id' => $feed->id ,
            'email_id' => $email3->id
        ] );

        $email3Truth = factory( \App\Models\AttributionRecordTruth::class )->create( [
            'email_id' => $email3->id ,
            'recent_import' => 0 ,
            'has_action' => 1 ,
            'action_expired' => 0 ,
            'additional_imports' => 0
        ] );
    }
}
