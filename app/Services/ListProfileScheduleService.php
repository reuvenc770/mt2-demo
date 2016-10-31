<?php

namespace App\Services;

use App\Repositories\ListProfileScheduleRepo;

class ListProfileScheduleService {

    private $repo;

    public function __construct(ListProfileScheduleRepo $repo) {
        $this->repo = $repo;
    }

    public function updateSuccess($id) {
        $this->repo->updateSuccess($id);
    }
}