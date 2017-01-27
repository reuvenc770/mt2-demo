<?php

namespace App\Services;

use App\Models\EmailFeedAction;
use App\Repositories\EmailFeedActionRepo;
use App\Repositories\EmailRepo;
use Log;

class EmailFeedActionService {
    private $repo;
    private $emailRepo;

    public function __construct(EmailFeedActionRepo $repo, EmailRepo $emailRepo) {
        $this->repo = $repo;
        $this->emailRepo = $emailRepo;
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
                $feedId = $this->emailRepo->getCurrentAttributedFeedId($action['email_id']);
                if ($feedId) {
                    $row = $this->mapToRow($action['email_id'], $feedId, $action['type']);
                    $this->repo->batchInsert($row);
                }
                else {
                    Log::emergency("Email id " . $action['email_id'] . ' does not have any attribution currently');
                }
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
            Log::emergency("EmailFeedAction case missed. Latest action is: " . $latestAction . ', current status is: ' . $currentStatus);
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