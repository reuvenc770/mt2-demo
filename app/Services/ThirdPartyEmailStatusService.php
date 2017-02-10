<?php

namespace App\Services;

use App\Models\ThirdPartyEmailStatus;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Repositories\EmailRepo;
use Log;

class ThirdPartyEmailStatusService {
    private $repo;
    private $emailRepo;

    public function __construct(ThirdPartyEmailStatusRepo $repo, EmailRepo $emailRepo) {
        $this->repo = $repo;
        $this->emailRepo = $emailRepo;
    }

    public function bulkUpdate($data) {
        foreach($data as $action) {
            $currentStatus = $this->repo->getActionStatus($action['email_id']);
            
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
        if (ThirdPartyEmailStatus::CONVERTER === $currentStatus) {
            return $currentStatus;
        }
        elseif (ThirdPartyEmailStatus::CLICKER === $currentStatus) {
            return $currentStatus;
        }
        elseif ("converter" === $latestAction) {
            return ThirdPartyEmailStatus::CONVERTER;
        }
        elseif ("clicker" === $latestAction) {
            if (ThirdPartyEmailStatus::CONVERTER === $currentStatus) {
                return ThirdPartyEmailStatus::CONVERTER;
            }
            else {
                return ThirdPartyEmailStatus::CLICKER;
            }
        }
        elseif ("opener" === $latestAction) {
            if (ThirdPartyEmailStatus::CONVERTER === $currentStatus || ThirdPartyEmailStatus::CLICKER === $currentStatus) {
                return $currentStatus;
            }
            else {
                return ThirdPartyEmailStatus::OPENER;
            }
        }
        else {
            // should have covered all cases
            Log::emergency("ThirdPartyEmailStatus case missed. Latest action is: " . $latestAction . ', current status is: ' . $currentStatus);
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