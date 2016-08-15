<?php

namespace App\Services;
use App\Events\NewRecords;
use App\Repositories\TempStoredEmailRepo;
use App\Services\API\Mt1DbApi;
use App\Repositories\EmailRepo;
use App\Repositories\EmailClientInstanceRepo;
use App\Repositories\ClientRepo;
use App\Repositories\EmailDomainRepo;

class ImportMt1EmailsService
{

    private $tempEmailRepo;
    private $api;
    private $emailRepo;
    private $emailFeedRepo;
    private $clientRepo;
    private $emailDomainRepo;

    public function __construct(
        Mt1DbApi $api, 
        TempStoredEmailRepo $tempEmailRepo, 
        EmailRepo $emailRepo, 
        EmailFeedInstanceRepo $emailFeedRepo,
        ClientRepo $clientRepo,
        EmailDomainRepo $emailDomainRepo) {

        $this->api = $api;
        $this->tempEmailRepo = $tempEmailRepo;
        $this->emailRepo = $emailRepo;
        $this->emailFeedRepo = $emailFeedRepo;
        $this->clientRepo = $clientRepo;
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function run() {
        $recordsToFlag = array();
        // import new clients
        echo "importing new feeds" . PHP_EOL;
        $lastLocalFeedId = $this->clientRepo->getMaxFeedId();
        $remoteMaxFeed = $this->api->getMaxFeedId();

        if ($remoteMaxFeed > $lastLocalFeedId) {
            echo "local max feed id: $lastLocalFeedId" . PHP_EOL;
            echo "remote max feed id: $remoteMaxFeed" . PHP_EOL;
            $newFeeds = $this->api->getNewFeeds($lastLocalFeedId);

            foreach ($newFeeds as $row) {
                $feed = $this->mapToFeedTable($row);
                $this->clientRepo->insert($feed);
            }
        }
        else {
            echo "No new clients" . PHP_EOL;
        }

        // import emails

        $now = time();
        echo "Beginning data pull" . PHP_EOL;
        $records = $this->api->getMt1EmailLogs();
        $finish = time();
        echo "Completed data pull. Beginning insert" . PHP_EOL;
        $total = $finish - $now;
        echo "total time: " . $total . PHP_EOL;

        foreach ($records as $id => $record) {
            $record = $this->mapToTempTable($record);
            $this->tempEmailRepo->insert($record);

            // insert into emails
            // insert into email_feed_instances
            $feedId = $record['feed_id'];

            if ($this->clientRepo->isActive($feedId)) {
                $emailRow = $this->mapToEmailTable($record);
                $this->emailRepo->insertCopy($emailRow);
                if($record['email_id'] != 0 ) {
                    $recordsToFlag[] = ["email_id" => $record['email_id'], "feed_id" => $record['feed_id']];
                }
                //We do an upsert so there is no model actions.
                $emailClientRow = $this->mapToEmailFeedTable($record);
                $this->emailClientRepo->insert($emailClientRow);
            }

        }
        // Delete records
        if (sizeof($records) > 0) {
            $this->api->cleanTable();
        }
        \Event::fire(new NewRecords($recordsToFlag));
    }

    private function mapToTempTable($row) {
        return [
            'email_id' => $row->email_user_id,
            'feed_id' => $row->client_id,
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
            'ip' => $row->ip,
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
            'feed_id' => $row['client_id'],
            'subscribe_datetime' => 'NOW()', 
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
            'dob' => $row['dob'],
            'gender' => $row['gender'],
            'phone' => $row['phone'],
            'mobile_phone' => $row['mobile_phone'],
            'work_phone' => $row['work_phone'],
            'capture_date' => $row['capture_date'],
            'source_url' => $row['source_url'],
            'ip' => $row['ip']
        ];

    }

    private function convertStatus($status) {
        return $status === 'Active' ? 'A' : 'U';
    }

    private function convertFeedStatus($status) {
        return $status === 'A' ? 'Active' : 'Deleted';
    }

    private function mapToFeedTable($row) {
        return [
            'id' => $row->user_id,
            'name' => $row->username,
            'address' => $row->address,
            'address2' => $row->address2,
            'city' => $row->city,
            'state' => $row->state,
            'zip' => $row->zip,
            'phone' => $row->phone,
            'email_address' => $row->email_addr,
            'status' => $this->convertClientStatus($row->status),
            'source_url' => $row->clientRecordSourceURL,
            'created_at' => $row->create_datetime,
            'updated_at' => $row->overall_updated
        ];
    }
}