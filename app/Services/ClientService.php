<?php

namespace App\Services;

use App\Repositories\ClientRepo;

class ClientService
{
    private $repo;

    public function __construct( ClientRepo $clientRepo ) {
        $this->repo = $clientRepo;
    }

    public function get() {
        return $this->repo->get();
    }

}