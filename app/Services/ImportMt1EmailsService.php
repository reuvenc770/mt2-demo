<?php

namespace App\Services;
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
    private $emailClientRepo;
    private $clientRepo;
    private $emailDomainRepo;

    public function __construct(
        Mt1DbApi $api, 
        TempStoredEmailRepo $tempEmailRepo, 
        EmailRepo $emailRepo, 
        EmailClientInstanceRepo $emailClientRepo,
        ClientRepo $clientRepo,
        EmailDomainRepo $emailDomainRepo) {

        $this->api = $api;
        $this->tempEmailRepo = $tempEmailRepo;
        $this->emailRepo = $emailRepo;
        $this->emailClientRepo = $emailClientRepo;
        $this->clientRepo = $clientRepo;
        $this->emailDomainRepo = $emailDomainRepo;
    }

    public function run() {

        // import new clients
        echo "importing new clients" . PHP_EOL;
        $lastLocalClientId = $this->clientRepo->getMaxClientId();
        $remoteMaxClient = $this->api->getMaxClientId();

        if ($remoteMaxClient > $lastLocalClientId) {
            echo "local max client id: $lastLocalClientId" . PHP_EOL;
            echo "remote max client id: $remoteMaxClient" . PHP_EOL;
            $newClients = $this->api->getNewClients($lastLocalClientId);

            foreach ($newClients as $row) {
                $client = $this->mapToClientTable($row);
                $this->clientRepo->insert($client);
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
            // insert into email_client_instances
            $clientId = $record['client_id'];

            if ($this->clientRepo->isActive($clientId)) {
                $emailRow = $this->mapToEmailTable($record);
                $this->emailRepo->insertCopy($emailRow);

                $emailClientRow = $this->mapToEmailClientTable($record);
                $this->emailClientRepo->insert($emailClientRow);
            }

        }
        // Delete records
        #$this->api->cleanTable();

    }

    private function mapToTempTable($row) {
        return [
            'email_id' => $row->email_user_id,
            'client_id' => $row->client_id,
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

    private function mapToEmailClientTable($row) {
        return [
            'email_id' => $row['email_id'],
            'client_id' => $row['client_id'],
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

    private function convertClientStatus($status) {
        return $status === 'A' ? 'Active' : 'Deleted';
    }

    private function mapToClientTable($row) {
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