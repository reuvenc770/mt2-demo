<?php

namespace App\Services;

class EmailRecordService {
    protected $repo;

    public function __construct ( EmailRecordRepo $repo ) {
        $this->repo = $repo;
    }
}
