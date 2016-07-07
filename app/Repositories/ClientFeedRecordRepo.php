<?php

namespace App\Repositories;

use App\Models\ClientFeedRecord;
use DB;

class ClientFeedRecordRepo {

    private $feedRecord;

    public function __construct ( ClientFeedRecord $feedRecord ) {
        $this->feedRecord = $feedRecord;
    }

    public function insert($row) {
        return $this->feedRecord->insert($row);
    }

}