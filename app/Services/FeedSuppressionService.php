<?php

namespace App\Services;

use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\SuppressionListSuppressionRepo;
use App\Repositories\SuppressionListRepo;

class FeedSuppressionService {

    private $assignmentRepo;
    private $suppressionRepo;
    private $listRepo;
    
    public function __construct(EmailFeedAssignmentRepo $assignmentRepo, SuppressionListSuppressionRepo $suppressionRepo, SuppressionListRepo $listRepo) {
        $this->assignmentRepo = $assignmentRepo;
        $this->suppressionRepo = $suppressionRepo;
        $this->listRepo = $listRepo;
    }

    public function processForFeedId($feedId) {
        /*
            This process takes records unique to a feed and places them in a new 
            so-called feed suppression list in suppression_list_suppressions. No
            reattribution happens - other records that _could_ be reattributed
            are still attributed to that feed.

            The reason for this particular behavior can be illustrated in the following case:
            Say the team receives notification that a particular feed should be shut down.
            They could reattribute the records and increase payout to other clients, but 
            instead can keep mailing the still-active re-attributable openers, clickers, and 
            converters for this feed (as they could be attributed elsewhere and won't cause
            anything to get flagged) without having to pay the alternate clients.
        */


        $records = $this->assignmentRepo->getFeedUniques($feedId);

        // Create new suppression list
        $listId = $this->listRepo->insert([
            'name' => "FeedSuppression-$feedId",
            'status' => 'A',
            'suppression_list_type' => 2 // Feed suppression
        ]);

        foreach ($records->cursor() as $record) {
            $this->suppressionRepo->addToSuppressionList($record->email_id, $listId);
        }
    }
}