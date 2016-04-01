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

    public function run() {

        // set file for download
        $name = 'test.csv';
        $this->api->exportContentServerActions($name);

        // transfer file to local server
        $this->api->moveFile($name);

        // load into db
        $this->repo->loadInfile($name);
    }
}