<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\MT1Models\EmailList;
use App\Repositories\EmailRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\AttributionScheduleRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use App\Repositories\MT1Repositories\EmailListRepo;

class AttributionValidationService {

    private $emailRepo;
    private $efaRepo;
    private $mt1EmailRepo;
    private $emailFeedAssignmentRepo;
    private $recordDataRepo;
    private $truthRepo;
    private $scheduleRepo;
    const QUERY_LIMIT = 30000;
    
    public function __construct(EmailRepo $emailRepo, 
        EmailFeedAssignmentRepo $efaRepo,
        EmailListRepo $mt1EmailRepo,
        EmailAttributableFeedLatestDataRepo $recordDataRepo,
        AttributionRecordTruthRepo $truthRepo,
        ThirdPartyEmailStatusRepo $emailActionStatusRepo,
        AttributionScheduleRepo $scheduleRepo) {

        $this->emailRepo = $emailRepo;
        $this->efaRepo = $efaRepo;
        $this->mt1EmailRepo = $mt1EmailRepo;
        $this->recordDataRepo = $recordDataRepo;
        $this->truthRepo = $truthRepo;
        $this->scheduleRepo = $scheduleRepo;
    }

    public function process($currentId) {
        $maxId = $this->emailRepo->maxId();

        while ($currentId < $maxId) {
            $resource = $this->emailRepo->getEmailBatch($currentId, self::QUERY_LIMIT);

            foreach($resource->cursor() as $row) {
                if ($this->efaRepo->exists($row->id)) {
                    // Exists. Now we can check if attribution makes sense.
                    
                    /*
                        At this point, we could either
                        1. Run attribution for this record (going to be quite slow for all records ... )
                        2. Check to see whether the email feed instance exists and leave it at that.

                        - 1 is stricter, but even 2 might not indicate true attribution potential (until much later)
                        And what, seriously, can we do about it?
                    */
                }
                else {
                    // Doesn't exist. See if it's 3rd party and if we can get info from MT1
                    // We won't have this get-out-of-jail-free card later
                    $mt1Data = $this->mt1EmailRepo->getThirdPartyForAddress($row->email_address);
                    if ($mt1Data) {

                        // Insert into attribution table
                        $data = $this->mapEmailListToAttribution($mt1Obj);
                        $this->efaRepo->batchInsert($data);

                        // Now we need to make sure that corresponding rows exist elsewhere, and that they're lined up, data-wise

                        // Third party per-feed record data
                        $recordData = $this->mapEmailListToRecordData($mt1Data);
                        $this->recordDataRepo->batchInsert($recordData);

                        // attribution record truth table
                        $truthTableData = $this->mapEmailListToTruthTable($mt1Data);
                        $this->truthRepo->batchInsert($truthTableData);

                        // schedule
                        $expirationData = $this->mapEmailListToExpirationTable($mt1Data);
                        $this->scheduleRepo->batchInsert($expirationData);

                        // third party email status - use action type from mt1
                        $emailActionStatusData = $this->mapEmailListToActionStatus($mt1Data);
                        $this->emailActionStatusRepo->batchInsert($emailActionStatusData);
                    }
                    // An else would indicate non-third party. Should not have attribution.
                }

                $currentId = $row->id;
            }
        }

        $this->efaRepo->insertStored();
        $this->recordDataRepo->insertStored();
        $this->truthRepo->insertStored();
        $this->emailActionStatusRepo->insertStored();
        $this->scheduleRepo->insertStored();

    }

    private function mapEmailListToAttribution(EmailList $obj) {
        return [
            'email_id' =>  $mt1Data->email_id,
            'feed_id' => $mt1Data->feed_id,
            'subscribe_date' => $mt1Data->subscribe_date
        ];
    }

    private function mapEmailListToRecordData(EmailList $obj) {
        return [
            'email_id' => $obj->email_id,
            'feed_id' => $obj->feed_id,
            'subscribe_date' => $obj->subscribe_date,
            'capture_date' => $obj->capture_date,
            'attribution_status' => 'ATTR',
            'first_name' => $obj->first_name,
            'last_name' => $obj->last_name,
            'address' => $obj->address,
            'address2' => $obj->address2,
            'city' => $obj->city,
            'state' => $obj->state,
            'zip' => $obj->zip,
            'country' => $obj->country,
            'gender' => $obj->gender,
            'ip' => $obj->ip,
            'phone' => $obj->phone,
            'source_url' => $obj->source_url,
            'dob' => $obj->dob,
            'other_fields' => '{}'
        ];
    }

    private function mapEmailListToTruthTable(EmailList $obj) {
        $isRecentImport = Carbon::parse($obj->subscribe_date)->lte(Carbon::today()->subDays(15)) ? 0 : 1;
        $hasAction = 'None' === $obj->last_action_type ? 1 : 0;

        return [
            'email_id' => $obj->email_id,
            'recent_import' => $isRecentImport,
            'has_action' => $hasAction
        ];
    }

    private function mapEmailListToActionStatus(EmailList $obj) {
        return [
            'email_id' => $obj->email_id,
            'last_action_type' => $obj->last_action_type,
            'offer_id' => null, // Not provided 
            'datetime' => $obj->last_action_date,
            'esp_account_id' => null // same as above
        ];
    }

    private function mapEmailListToExpirationTable(EmailList $obj) {
        return [
            'email_id' => $obj->email_id,
            'trigger_date' => Carbon::parse($obj->subscribe_date)->addDays(15)->toDateString()
        ];
    }

    // We can also have a reverse job that goes through efa and checks the latest ones to see if they make sense
}