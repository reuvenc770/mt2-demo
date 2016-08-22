<?php

namespace App\Services;

use App\Repositories\LinkRepo;

class LinkService {
    
    private $repo;

    public function __construct(LinkRepo $repo) {
        $this->repo = $repo;
    }

    public function checkLink($link) {
        throw new Exception('Not implemented yet');

        // returns 
    }

    public function addLink($url) {
        throw new Exception('Not implemented yet');
        // eventually this will insert (or not) the url into the `links` table and return the linkId
    }
}