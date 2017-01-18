<?php

namespace App\Services;

use App\Models\EmailFeedAction;
use App\Repositories\EmailFeedActionRepo;
use Log;

class EmailFeedActionService {
    private $repo;

    public function __construct(EmailFeedActionRepo $repo) {
        $this->repo = $repo;
    }

    public function bulkUpdate($data) {
        foreach($data as $action) {
            $currentStatus = $this->repo->getCurrentAttributedStatus($action['email_id']);
            if ($currentStatus) {
                $newStatus = $this->getNewStatus($action['type'], $currentStatus->status);

                $row = $this->mapToRow($action['email_id'], $currentStatus->feed_id, $newStatus);
                $this->repo->batchInsert($row);
            }
            else {
                Log::warning("No attributed status found for " . $action['email_id'] . '. Would have been: ' . $action['type']);
            }
            
        }

        $this->repo->insertStored();
    }

    private function getNewStatus($latestAction, $currentStatus) {
        if (EmailFeedAction::CONVERTER === $currentStatus) {
            return $currentStatus;
        }
        elseif (EmailFeedAction::CLICKER === $currentStatus) {
            return $currentStatus;
        }
        elseif ("converter" === $latestAction) {
            return EmailFeedAction::CONVERTER;
        }
        elseif ("clicker" === $latestAction) {
            if (EmailFeedAction::CONVERTER === $currentStatus) {
                return EmailFeedAction::CONVERTER;
            }
            else {
                return EmailFeedAction::CLICKER;
            }
        }
        elseif ("opener" === $latestAction) {
            if (EmailFeedAction::CONVERTER === $currentStatus || EmailFeedAction::CLICKER === $currentStatus) {
                return $currentStatus;
            }
            else {
                return EmailFeedAction::OPENER;
            }
        }
        else {
            // should have covered all cases
            Log::warning("EmailFeedAction case missed. Latest action is: " . $latestAction . ', current status is: ' . $currentStatus);
        }
    }

    private function mapToRow($emailId, $feedId, $status) {
        return [
            'email_id' => $emailId,
            'feed_id' => $feedId,
            'status' => $status
        ];
    }
}