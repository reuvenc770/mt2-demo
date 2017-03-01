<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\FirstPartyRecordDataRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Services\Interfaces\IEtl;

class UserActionStatusService implements IEtl {
    
    private $actionsRepo;
    private $thirdPartyEmailStatusRepo;
    private $firstPartyUserRepo;
    private $resource;

    public function __construct(EmailActionsRepo $actionsRepo, ThirdPartyEmailStatusRepo $thirdPartyEmailStatusRepo, FirstPartyRecordDataRepo $firstPartyUserRepo) {
        $this->actionsRepo = $actionsRepo;
        $this->thirdPartyEmailStatusRepo = $thirdPartyEmailStatusRepo;
        $this->firstPartyUserRepo = $firstPartyUserRepo;
    }


    public function extract($lookback) {
        $this->resource = $this->actionsRepo->pullActionsForUserUpdate($lookback);
    }


    public function load() {
        // Can almost certainly assume that any converters would also be openers or clickers
        // So we can just use those two to remove someone from being `deliverable`

        // Large data set, so lazy loading required here
        foreach($this->resource->cursor() as $row) {
            if (3 === $row->party) {
                $this->thirdPartyEmailStatusRepo->batchInsert($row);
            }
            elseif (1 === $row->party) {
                $this->firstPartyUserRepo->updateActionData($row->email_id, $row->feed_id, $row->date);
            }
            else {
                throw new \Exception("Invalid party found for user update: {$row->party}");
            }
        }

        $this->thirdPartyEmailStatusRepo->insertStored();
        $this->firstPartyUserRepo->cleanUpActions();
    }
}