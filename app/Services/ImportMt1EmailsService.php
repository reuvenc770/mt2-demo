<?php

namespace App\Services;
use App\Repositories\TempStoredEmailRepo;
use App\Services\API\Mt1DbApi;

class ImportMt1EmailsService
{

    private $tempEmailRepo;
    private $api;

    public function __construct(Mt1DbApi $api, TempStoredEmailRepo $tempEmailRepo) {
        $this->api = $api;
        $this->tempEmailRepo = $tempEmailRepo;
    }

    public function run() {
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
        }

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

}