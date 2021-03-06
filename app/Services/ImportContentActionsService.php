<?php

namespace App\Services;
use App\Services\API\Mt1DbApi;
use App\Repositories\ContentServerActionRepo;

class ImportContentActionsService {
    
    private $api;
    private $repo;

    public function __construct(Mt1DbApi $api, ContentServerActionRepo $repo) {
        $this->api = $api;
        $this->repo = $repo;
    }

    public function run($startDateTime) {

        // set file for download
        $name = 'csactions.csv';
        $this->api->exportContentServerActions($name, $startDateTime);

        // transfer file to local server
        $this->api->moveFile($name);

        // load into db
        $this->repo->loadInfile($name);
    }
}