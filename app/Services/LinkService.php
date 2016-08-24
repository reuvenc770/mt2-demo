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

    public function getLinkId($url) {
        return $this->repo->getLinkId($url);
    }

}