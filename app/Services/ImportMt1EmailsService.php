<?php

namespace App\Services;
use App\Events\NewRecords;
use App\Repositories\TempStoredEmailRepo;
use App\Services\API\Mt1DbApi;
use App\Repositories\EmailRepo;
use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\FeedRepo;
use App\Repositories\EmailDomainRepo;
use App\Repositories\AttributionLevelRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;
use App\Repositories\EmailIdHistoryRepo;
use App\Repositories\EmailFeedAssignmentRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use Carbon\Carbon;
use App\Models\EmailAttributableFeedLatestData;

class ImportMt1EmailsService
{

    private $tempEmailRepo;
    private $api;
    private $emailRepo;
    private $emailFeedRepo;
    private $feedRepo;
    private $emailDomainRepo;
    private $breakdownRepo;
    private $attributionLevelRepo;
    private $historyRepo;
    private $processingDate;
    private $formattedDate;
    private $emailFeedDataRepo;
    private $emailActionStatusRepo;

    private $emailIdCache = [];
    private $emailAddressCache = [];
    private $inBatchSwitches = [];

    public function __construct(
        Mt1DbApi $api, 
        TempStoredEmailRepo $tempEmailRepo, 
        EmailRepo $emailRepo, 
        EmailFeedInstanceRepo $emailFeedRepo,
        FeedRepo $feedRepo,
        EmailDomainRepo $emailDomainRepo,
        AttributionLevelRepo $attributionLevelRepo,
        FeedDateEmailBreakdownRepo $breakdownRepo,
        EmailIdHistoryRepo $historyRepo,
        EmailAttributableFeedLatestDataRepo $emailFeedDataRepo,
        ThirdPartyEmailStatusRepo $emailActionStatusRepo) {

        $this->api = $api;
        $this->tempEmailRepo = $tempEmailRepo;
        $this->emailRepo = $emailRepo;
        $this->emailFeedRepo = $emailFeedRepo;
        $this->feedRepo = $feedRepo;
        $this->emailDomainRepo = $emailDomainRepo;
        $this->attributionLevelRepo = $attributionLevelRepo;
        $this->breakdownRepo = $breakdownRepo;
        $this->historyRepo = $historyRepo;
        $this->emailFeedDataRepo = $emailFeedDataRepo;
        $this->emailActionStatusRepo = $emailActionStatusRepo;

        $this->processingDate = Carbon::today();
        $this->formattedDate = $this->processingDate->format('Y-m-d');
    }

    public function run($modulus) {
        $recordsToFlag = array();

        $now = time();
        echo "Beginning data pull" . PHP_EOL;
        $records = $this->api->getMt1EmailLogs($modulus);
        $finish = time();
        echo "Completed data pull. Beginning insert" . PHP_EOL;
        $total = $finish - $now;
        echo "total time: " . $total . PHP_EOL;

        $statuses = [];

        foreach ($records as $id => $record) {
            $record = $this->mapToTempTable($record);
            $this->tempEmailRepo->batchInsert($record);

            // insert into emails
            // insert into email_feed_instances
            $feedId = $record['feed_id'];

            // Note structure
            if (!isset($statuses[$feedId])) {
                $statuses[$feedId] = [
                    'fresh' => 0,
                    'non-fresh' => 0,
                    'suppressed' => 0,
                    'duplicate' => 0
                ];
            }

            // checks for active and 3rd party vs. 1st party
            if ($this->feedRepo->isActive($feedId)) {

                $emailAddress = $record['email_addr'];
                $importingEmailId = (int)$record['email_id'];
               
                // we need to know if this is new or not.
                // if it is new, insert it

                if (0 === $importingEmailId) {
                    $emailStatus = 'suppressed';
                }
                else {
                    $existsCheck = $this->emailRepo->getEmailId($emailAddress)->first();

                    if (null === $existsCheck) {
                        $this->emailActionStatusRepo->batchInsert($this->mapRecordToEmailStatus($record, 'None'));
                    }

                    if (isset($this->emailIdCache[$importingEmailId])) {
                        // email id is already a duplicate within this import. 
                        // Still want to update the email x feed info store 
                        $record['other_fields'] = '{}';
                        $record['attribution_status'] = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                        $this->emailFeedDataRepo->batchInsert($record);
                    }
                    elseif (null === $existsCheck && !isset($this->emailIdCache[$importingEmailId]) && !isset($this->emailAddressCache[$emailAddress])) {

                        // not inserted yet
                        // breaking encapsulation in order to improve performance
                        $emailStatus = 'fresh';
                    
                        // insert at this point
                        $emailRow = $this->mapToEmailTable($record);
                        $this->emailRepo->insertDelayedBatch($emailRow);
                        $this->emailIdCache[$importingEmailId] = 1;
                        $this->emailAddressCache[$emailAddress] = $importingEmailId;

                        // feed id already set
                        $record['other_fields'] = '{}';
                        $record['attribution_status'] = EmailAttributableFeedLatestData::ATTRIBUTED;
                        $this->emailFeedDataRepo->batchInsert($record);

                        $recordsToFlag[] = [
                            "email_id" => $importingEmailId, 
                            "feed_id" => $feedId, 
                            "datetime" => $record['capture_date'],
                            "capture_date" => $record['capture_date']
                        ];
                    }
                    elseif (null === $existsCheck && !isset($this->emailIdCache[$importingEmailId]) && isset($this->emailAddressCache[$emailAddress])) {
                        // this particular email address appears in this batch, but not under this email id

                        $firstEmailId = $this->emailAddressCache[$emailAddress];

                        // we need to pick a canonical email id. Let's stick with the last one for now 
                        // (would have to tell the email repo to forget that, which would be a mess)
                        $this->emailIdCache[$importingEmailId] = 1;
                        $emailStatus = 'duplicate'; // hard-coded because the check will fail otherwise

                        $record['other_fields'] = '{}';
                        $record['attribution_status'] = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                        $this->emailFeedDataRepo->batchInsert($record);

                        // but how do we deal with this? It won't exist in the db ... 
                        // and they can be in any order
                        $this->inBatchSwitches[] = ['old' => min($firstEmailId, $importingEmailId), 'new' => max($firstEmailId, $importingEmailId)];

                        if ($importingEmailId > $firstEmailId) {
                            // Switching the base here ... in order to handle future versions
                            $this->emailAddressCache[$emailAddress] = $importingEmailId;
                            $this->historyRepo->insertIntoHistory($firstEmailId, $importingEmailId); 
                        }
                    }
                    elseif ($existsCheck) {
                        $currentEmailId = (int)$existsCheck->id;
                        $attributionTruths = $this->emailRepo->getAttributionTruths($currentEmailId);
                        
                        if ($currentEmailId === $importingEmailId) {
                            // Everything is normal. Just importing another instance of this
                            $emailStatus = $this->getStatusForExistingEmail($importingEmailId, $feedId, $attributionTruths);
                        }
                        else {
                            // An email is being re-imported, but its email id differs due to MT1 ... logic
                            $this->historyRepo->insertIntoHistory($currentEmailId, $importingEmailId);
                            $emailStatus = $this->getStatusForExistingEmail($currentEmailId, $feedId, $attributionTruths);

                            // update emails table
                            $this->emailRepo->updateEmailId($currentEmailId, $importingEmailId);
                            $this->emailIdCache[$importingEmailId] = 1;
                            $record['email_id'] = $importingEmailId;
                        }

                        $emailActionStatus = $this->emailActionStatusRepo->getActionStatus($record['email_id']);
                        $attributedFeedId = (int)$this->emailRepo->getCurrentAttributedFeedId($record['email_id']);
                        $newStatus = EmailAttributableFeedLatestData::ATTRIBUTED;

                        if (!$emailActionStatus) {
                            $this->emailActionStatusRepo->batchInsert($this->mapRecordToEmailStatus($record, 'None'));
                        }

                        if ($attributionTruths->has_action && ($attributedFeedId === (int)$record['feed_id'])) {
                            // pass conditional
                        }
                        elseif ($attributionTruths->has_action && ($attributedFeedId !== (int)$record['feed_id'])) {
                            // set status to POR
                            $newStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_RESPONDER;
                        }
                        elseif (!$attributionTruths->has_action && 'fresh' === $emailStatus) {
                            // Need to change attribution and some types will change
                            $newStatus = EmailAttributableFeedLatestData::ATTRIBUTED;
                        }
                        elseif (!$attributionTruths->has_action && $attributionTruths->recent_import) {
                            // set status to POA
                            $newStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                        }
                        elseif (!$attributionTruths->has_action && !$attributionTruths->recent_import) {
                            // set status to POA
                            $newStatus = EmailAttributableFeedLatestData::PASSED_DUE_TO_ATTRIBUTION;
                        }
                        
                        $record['other_fields'] = '{}';
                        $record['attribution_status'] = $newStatus;
                        $this->emailFeedDataRepo->batchInsert($record);
                    }
                }

                $emailFeedRow = $this->mapToEmailFeedTable($record);
                $this->emailFeedRepo->batchInsert($emailFeedRow);
                $statuses[$feedId][$emailStatus]++;
            }

        }

        echo "done" . PHP_EOL;

        $this->breakdownRepo->massUpdateStatuses($statuses, $this->formattedDate);
        $this->emailRepo->insertStored(); // Clear out remaining inserts
        $this->emailFeedRepo->insertStored();
        $this->tempEmailRepo->insertStored();
        $this->emailFeedDataRepo->insertStored();

        // Need to handle in-batch switching between email ids
        $this->emailRepo->updateInBatchIdSwitches($this->inBatchSwitches);

        // Delete records
        if (sizeof($records) > 0) {
            $deletions = $this->api->cleanTable($modulus);
            echo "Read in " . sizeof($records) . " records, deleted " . $deletions . ', processing ' . count($recordsToFlag) . PHP_EOL; 
        }
        
        if (sizeof($recordsToFlag > 0)) {
            $time = Carbon::now()->toDateTimeString();
            \Event::fire(new NewRecords($recordsToFlag, $time));
        }
    }

    private function mapToTempTable($row) {
        if (preg_match('/\./', $row->ip)) {
            $ip = $row->ip;
        }
        else {
            $ip = long2ip($row->ip);
        }

        return [
            'email_id' => $row->email_user_id,
            'feed_id' => $row->client_id,  // these are stored under "client_id" in mt1
            'email_addr' => $row->email_addr,
            'status' => $row->status,
            'first_name' => $row->first_name,
            'last_name' => $row->last_name,
            'address' => $row->address,
            'address2' => $row->address2,
            'city' => $row->city,
            'state' => $row->state,
            'zip' => $row->zip,
            'country' => $row->country,
            'dob' => $row->dob,
            'gender' => $row->gender,
            'phone' => $row->phone,
            'mobile_phone' => $row->mobile_phone,
            'work_phone' => $row->work_phone,
            'capture_date' => $row->capture_date,
            'ip' => $ip,
            'source_url' => $row->source_url,
            'last_updated' => $row->lastUpdated
        ];
    }

    private function mapToEmailTable($row) {
        return [
            'id' => $row['email_id'],
            'email_address' => $row['email_addr'],
            'email_domain_id' => $this->emailDomainRepo->getIdForName($row['email_addr'])
        ];
    }

    private function mapToEmailFeedTable($row) {
        return [
            'email_id' => $row['email_id'],
            'feed_id' => $row['feed_id'],
            'subscribe_datetime' => $row['last_updated'], 
            'unsubscribe_datetime' => null, // null for now, at least
            'status' => $this->convertStatus($row['status']),
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'address' => $row['address'],
            'address2' => $row['address2'],
            'city' => $row['city'],
            'state' => $row['state'],
            'zip' => $row['zip'],
            'country' => $row['country'],
            'dob' => $row['dob'] === '0000-00-00' ? null : $row['dob'],
            'gender' => $row['gender'],
            'phone' => $row['phone'],
            'mobile_phone' => $row['mobile_phone'],
            'work_phone' => $row['work_phone'],
            'capture_date' => $row['capture_date'],
            'source_url' => $row['source_url'],
            'ip' => $row['ip'],
            'other_fields' => '{}'
        ];
    }

    private function mapRecordToEmailStatus($row, $status) {
        return [
            'email_id' => $row['email_id'],
            'action_type' => $status,
            'datetime' => null,
            'esp_account_id' => null,
            'offer_id' => null
        ];
    }

    private function convertStatus($status) {
        return $status === 'Active' ? 'A' : 'U';
    }

    /**
     *      RULES FOR EMAIL STATUS:
     *      Record processing first checks if an email is not suppressed. If not, it checks whether this email already exists in the database. 
     *       If not, the email is fresh. If it does already exist for an Orange client, pull the data from the email_list table. 
     *
     *       (1). If the email is Active (all records here are Active) and was imported > 90 days ago, check feed ids. If the importing feed id matches the currently-set feed id, 
     *           reject the record as a dupe. If they don't match, accept this as fresh.
     *       (2). Otherwise, if the email is Active, was imported > 10 days ago, has not had any actions (i.e. is "deliverable" and not "opener" or 
     *           "clicker"), and the importing feed has a higher attribution level than the currently-attributed feed, accept this as fresh.
     *       (3). Otherwise, if the email is Active and it's been less than 91 days: reject as duplicate if the feed ids match and reject as non-fresh 
     *           if they don't. Mark it as duplicate for "fresh." If the importing attribution level is greater than the current one, switch attribution. (this will be removed)
     *       (4). For all other cases, record as duplicate with the reason being duplicate (if the feed ids match) or non-fresh (if they don't)
     *      
     *      Returns one of 'fresh', 'non-fresh', 'duplicate'
     */

    private function getStatusForExistingEmail($emailId, $importingFeedId, $attributionTruths) {
        $currentFeedId = $this->emailRepo->getCurrentAttributedFeedId($emailId);

        // Catching an edge case where the email id does exist
        // but no attribution has been set
        // We might have an issue here where older imported data simply 
        // didn't have attribution set up, but this should slowly converge to the real numbers
        // as we go forward

        if (0 === $currentFeedId) {
            return 'fresh';
        }

        if (0 === $attributionTruths) {
            return 'fresh'; // We don't have attribution info for this one yet
        }

        $isRecentImport = $attributionTruths->recent_import;
        $hasActions = $attributionTruths->has_action;
        $currentAttributionLevel = $this->emailRepo->getCurrentAttributionLevel($emailId);
        $importingAttrLevel = $this->attributionLevelRepo->getLevel($importingFeedId);

        if (0 === $isRecentImport && 0 === $hasActions && $importingAttrLevel < $currentAttributionLevel) {
            return 'fresh';
        }
        else {
            return (($importingFeedId === $currentFeedId) ? 'duplicate' : 'non-fresh');
        }
    }
}
